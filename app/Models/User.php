<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use Modules\Roles\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'national_id',
        'password',
        'role',
        'is_admin',
        'selected_role',
        'avatar',
        'bio',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_admin' => 'boolean',
        ];
    }

    // Relationships
    public function batches()
    {
        return $this->hasMany(Batch::class, 'instructor_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function enrolledBatches()
    {
        return $this->belongsToMany(Batch::class, 'enrollments', 'student_id', 'batch_id')
            ->withPivot('status', 'progress', 'enrolled_at', 'completed_at')
            ->withTimestamps();
    }

    public function enrolledCourses()
    {
        return \Modules\Courses\Models\Course::whereHas('batches.enrollments', function($query) {
            $query->where('student_id', $this->id);
        })->distinct();
    }

    public function favorites()
    {
        return $this->hasMany(\Modules\Courses\Models\Favorite::class);
    }

    public function favoriteCourses()
    {
        return $this->belongsToMany(\Modules\Courses\Models\Course::class, 'favorites', 'user_id', 'course_id')
            ->withTimestamps();
    }

    public function hasFavorited(\Modules\Courses\Models\Course $course): bool
    {
        return $this->favorites()->where('course_id', $course->id)->exists();
    }

    // Role Methods
    public function hasRoleBySlug(string $roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    public function hasAnyRoleBySlug(array $roleSlugs): bool
    {
        return $this->roles()->whereIn('slug', $roleSlugs)->exists();
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin') 
            || $this->hasRoleBySlug('super_admin') 
            || $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin') 
            || $this->hasRoleBySlug('admin') 
            || $this->isSuperAdmin()
            || in_array($this->role, ['admin', 'super_admin']);
    }

    public function isInstructor(): bool
    {
        return $this->hasRole('instructor') 
            || $this->hasRoleBySlug('instructor') 
            || $this->role === 'instructor';
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student') 
            || $this->hasRoleBySlug('student') 
            || $this->role === 'student';
    }

    public function hasMultipleRoles(): bool
    {
        return $this->roles()->count() > 1 
            || ($this->roles()->count() === 1 && $this->role && $this->role !== $this->roles()->first()?->slug);
    }

    /**
     * Get the effective role for the user
     * - If user has one role: returns user->role
     * - If user has multiple roles: returns user->selected_role
     * 
     * @return string|null
     */
    public function getEffectiveRole(): ?string
    {
        $availableRoles = $this->getAvailableRolesForSelection();
        
        // If user has only one role, use that role
        if (count($availableRoles) === 1) {
            return $this->role ?? $availableRoles[0]['slug'] ?? null;
        }
        
        // If user has multiple roles, use selected_role
        if (count($availableRoles) > 1) {
            return $this->selected_role ?? $this->role;
        }
        
        // Fallback to user->role
        return $this->role;
    }

    // Permission Methods
    public function hasPermission(string $permissionSlug): bool
    {
        // Super admin always has all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        // For users with multiple roles, check permissions based on selected_role
        $userRoles = $this->roles()->get();
        $rolesCount = $this->countUserRoles($userRoles);
        
        // If user has multiple roles, check permissions only for selected_role
        if ($rolesCount > 1) {
            $selectedRoleSlug = $this->selected_role ?? session('selected_role');
            
            if ($selectedRoleSlug) {
                // Check if permission exists in selected role
                $selectedRole = $userRoles->first(function($role) use ($selectedRoleSlug) {
                    return ($role->slug === $selectedRoleSlug || $role->name === $selectedRoleSlug);
                });
                
                // Also check legacy role field
                if (!$selectedRole && $this->role === $selectedRoleSlug) {
                    $selectedRole = $this->findRoleBySlug($selectedRoleSlug);
                }
                
                if ($selectedRole) {
                    // Check if permission exists in selected role's permissions
                    $hasPermissionInRole = $selectedRole->permissions()
                        ->where('slug', $permissionSlug)
                        ->orWhere('name', $permissionSlug)
                        ->exists();
                    
                    if ($hasPermissionInRole) {
                        return true;
                    }
                    
                    // Also check direct permissions
                    return $this->hasDirectPermission($permissionSlug);
                }
            }
            
            // No valid selected_role - return false
            return false;
        }

        // Single role or no roles - use standard permission check
        // For admin users with single role, check if they have the permission
        if ($this->isAdmin() && $this->is_admin == 1) {
            // Check if user has a role with is_admin == 1
            $hasAdminRole = $userRoles->contains(function($role) {
                return $role->is_admin == 1;
            });
            
            // If user has admin role, check if permission exists in that role
            if ($hasAdminRole) {
                $adminRole = $userRoles->first(function($role) {
                    return $role->is_admin == 1;
                });
                
                if ($adminRole) {
                    $hasPermissionInRole = $adminRole->permissions()
                        ->where('slug', $permissionSlug)
                        ->orWhere('name', $permissionSlug)
                        ->exists();
                    
                    if ($hasPermissionInRole) {
                        return true;
                    }
                }
            }
        }

        $permission = Permission::where('slug', $permissionSlug)->first();
        
        if ($permission && ($this->hasPermissionTo($permission) || $this->hasPermissionTo($permission->name))) {
            return true;
        }

        if ($this->hasPermissionInRoles($permissionSlug)) {
            return true;
        }

        if ($this->hasDirectPermission($permissionSlug)) {
            return true;
        }

        return Permission::hasPermission($this->role, $permissionSlug);
    }

    /**
     * Get all permissions as an associative array (slug => true)
     * 
     * Logic:
     * - Super Admin: All permissions (only if super_admin role is selected)
     * - Single role: Permissions from that role only
     * - Multiple roles with selected_role: Permissions from selected_role only (even for admin roles)
     * - Multiple roles without selected_role: Empty permissions to force selection
     */
    public function getPermissionArray(): array
    {
        // Get roles from model relationship (Spatie roles)
        $userRoles = $this->roles()->with('permissions')->get();
        $rolesCount = $this->countUserRoles($userRoles);

        // Get selected_role from user table (database) - prioritize database over session
        $selectedRoleSlug = $this->selected_role ?? session('selected_role');

        // If user has multiple roles, they MUST have a selected_role
        // Return permissions ONLY for the selected_role (even if it's an admin role)
        if ($rolesCount > 1) {
            if ($selectedRoleSlug) {
                // Validate selected_role exists in user's roles
                $selectedRole = $userRoles->first(function($role) use ($selectedRoleSlug) {
                    return ($role->slug === $selectedRoleSlug || $role->name === $selectedRoleSlug);
                });
                
                // Also check legacy role field
                if (!$selectedRole && $this->role === $selectedRoleSlug) {
                    $selectedRole = $this->findRoleBySlug($selectedRoleSlug);
                }
                
                if ($selectedRole) {
                    // User has selected a role - return ONLY that role's permissions
                    // Even if the role has is_admin == 1, only return its assigned permissions
                    // Super admin is the only exception - if selected role is super_admin, return all permissions
                    if ($selectedRole->slug === 'super_admin' || $selectedRole->name === 'super_admin') {
                        return $this->getAllPermissionsArray();
                    }
                    
                    // For all other roles (including admin roles with is_admin == 1),
                    // return only the permissions assigned to that specific role
                    return $this->getSelectedRolePermissions($userRoles, $selectedRoleSlug);
                } else {
                    // Invalid selected_role - return empty to force selection
                    return [];
                }
            } else {
                // Multiple roles but no selected_role - return empty to force selection
                // This prevents showing merged permissions when a role should be selected
                return [];
            }
        }

        // Single role - return permissions for that role
        if ($rolesCount === 1) {
            $singleRole = $userRoles->first();
            
            // Super admin with single role gets all permissions
            if ($singleRole && ($singleRole->slug === 'super_admin' || $singleRole->name === 'super_admin')) {
                return $this->getAllPermissionsArray();
            }
            
            return $this->getSingleRolePermissions($userRoles);
        }

        // No roles - return empty permissions
        return [];
    }

    // Helper Methods
    private function getAllPermissionsArray(): array
    {
        $permissions = [];
        $allPermissions = Permission::pluck('slug')->toArray();
        
        foreach ($allPermissions as $slug) {
            $permissions[$slug] = true;
        }

        if ($this->isSuperAdmin()) {
            $permissions['*'] = true;
        }

        return $permissions;
    }

    public function countUserRoles($userRoles = null): int
    {
        if ($userRoles === null) {
            $userRoles = $this->roles()->get();
        }
        $count = $userRoles->count();
        
        if (!empty($this->role)) {
            $legacyRoleExists = $userRoles->contains(function($role) {
                return $role->slug === $this->role || $role->name === $this->role;
            });
            
            if (!$legacyRoleExists) {
                $count++;
            }
        }

        return $count;
    }

    private function getSingleRolePermissions($userRoles): array
    {
        $permissions = [];
        $singleRole = $userRoles->first();

        if (!$singleRole && !empty($this->role)) {
            $singleRole = $this->findRoleBySlug($this->role);
        }

        if ($singleRole) {
            $permissions = $this->extractPermissionsFromRole($singleRole);
        }

        return array_merge($permissions, $this->getDirectPermissionsArray());
    }

    private function getSelectedRolePermissions($userRoles, string $selectedRoleSlug): array
    {
        $permissions = [];
        $selectedRole = $userRoles->first(function($role) use ($selectedRoleSlug) {
            return $role->slug === $selectedRoleSlug || $role->name === $selectedRoleSlug;
        });

        if (!$selectedRole && $this->role === $selectedRoleSlug) {
            $selectedRole = $this->findRoleBySlug($selectedRoleSlug);
        }

        if ($selectedRole) {
            $permissions = $this->extractPermissionsFromRole($selectedRole);
        }

        return array_merge($permissions, $this->getDirectPermissionsArray());
    }

    private function getMergedRolePermissions($userRoles): array
    {
        $permissions = [];

        try {
            $spatiePermissions = $this->getPermissionsViaRoles();
            foreach ($spatiePermissions as $permission) {
                $key = $permission->slug ?? $permission->name;
                if ($key) {
                    $permissions[$key] = true;
                }
            }
        } catch (\Exception $e) {
            // Fallback to manual method
        }

        foreach ($userRoles as $role) {
            $rolePermissions = $this->extractPermissionsFromRole($role);
            $permissions = array_merge($permissions, $rolePermissions);
        }

        $permissions = array_merge($permissions, $this->getDirectPermissionsArray());

        if (!empty($this->role)) {
            $legacyPermissions = $this->getLegacyPermissions();
            $permissions = array_merge($permissions, $legacyPermissions);
        }

        return $permissions;
    }

    private function extractPermissionsFromRole($role): array
    {
        $permissions = [];

        foreach ($role->permissions as $permission) {
            $key = $permission->slug ?: $permission->name;
            if ($key) {
                $permissions[$key] = true;
            }
        }

        return $permissions;
    }

    private function getDirectPermissionsArray(): array
    {
        $permissions = [];

        foreach ($this->permissions as $permission) {
            $key = $permission->slug ?: $permission->name;
            if ($key) {
                $permissions[$key] = true;
            }
        }

        return $permissions;
    }

    private function findRoleBySlug(string $slug): ?Role
    {
        return Role::where('slug', $slug)
            ->orWhere('name', $slug)
            ->with('permissions')
            ->first();
    }

    private function getLegacyPermissions(): array
    {
        $permissions = [];
        $legacyPermissions = DB::table('role_permissions')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('role_permissions.role', $this->role)
            ->pluck('permissions.slug')
            ->toArray();

        foreach ($legacyPermissions as $slug) {
            if ($slug) {
                $permissions[$slug] = true;
            }
        }

        return $permissions;
    }

    private function hasPermissionInRoles(string $permissionSlug): bool
    {
        $roles = $this->roles()->with('permissions')->get();
        
        foreach ($roles as $role) {
            foreach ($role->permissions as $rolePermission) {
                if ($rolePermission->slug === $permissionSlug || $rolePermission->name === $permissionSlug) {
                    return true;
                }
            }
        }

        return false;
    }

    private function hasDirectPermission(string $permissionSlug): bool
    {
        foreach ($this->permissions as $directPermission) {
            if ($directPermission->slug === $permissionSlug || $directPermission->name === $permissionSlug) {
                return true;
            }
        }

        return false;
    }

    // Course Access
    public function canAccessCourse(\Modules\Courses\Models\Course $course): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->isInstructor() && $this->id === $course->instructor_id) {
            return true;
        }

        if ($this->isStudent()) {
            return $this->enrolledCourses()->where('courses.id', $course->id)->exists();
        }

        return false;
    }

    // Role Names and Selection
    public function getRoleNames()
    {
        $roles = $this->roles()->pluck('slug')->toArray();
        $roleNames = $this->roles()->pluck('name')->toArray();
        $roles = array_unique(array_merge($roles, $roleNames));

        if ($this->role && !in_array($this->role, $roles)) {
            $roles[] = $this->role;
        }

        return collect($roles);
    }

    public function getAvailableRolesForSelection(): array
    {
        $availableRoles = [];
        $addedSlugs = [];
        $roles = $this->roles()->get();

        foreach ($roles as $role) {
            $slug = $role->slug ?? $role->name;
            
            if ($this->shouldIncludeRole($role, $slug)) {
                $availableRoles[] = $this->formatRoleForSelection($role, $slug);
                $addedSlugs[] = $slug;
            }
        }

        if ($this->role && !in_array($this->role, $addedSlugs)) {
            $this->addLegacyRoleToSelection($availableRoles, $addedSlugs);
        }

        return $availableRoles;
    }

    private function shouldIncludeRole($role, string $slug): bool
    {
        if ($slug === 'super_admin') {
            return true;
        }

        // If role has is_admin == 1, user must also have is_admin == 1
        if ($role->is_admin == 1) {
            return $this->is_admin == 1;
        }

        return true;
    }

    private function formatRoleForSelection($role, string $slug): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'name_ar' => $role->name_ar ?? null,
            'slug' => $slug,
            'description' => $role->description ?? null,
            'description_ar' => $role->description_ar ?? null,
        ];
    }

    private function addLegacyRoleToSelection(array &$availableRoles, array &$addedSlugs): void
    {
        $currentRoleModel = Role::where('slug', $this->role)
            ->orWhere('name', $this->role)
            ->first();

        if ($currentRoleModel) {
            $slug = $currentRoleModel->slug ?? $currentRoleModel->name;
            
            if ($this->shouldIncludeLegacyRole($currentRoleModel, $slug)) {
                $availableRoles[] = $this->formatRoleForSelection($currentRoleModel, $slug);
                $addedSlugs[] = $slug;
            }
        } else {
            $availableRoles[] = [
                'id' => null,
                'name' => ucfirst($this->role),
                'name_ar' => null,
                'slug' => $this->role,
                'description' => null,
                'description_ar' => null,
            ];
        }
    }

    private function shouldIncludeLegacyRole($role, string $slug): bool
    {
        if ($slug === 'super_admin') {
            return true;
        }

        if ($slug === 'admin') {
            return $this->is_admin == 1 && $role->is_admin == 1;
        }

        return true;
    }

    public function getAllRoles()
    {
        return $this->roles()->get();
    }
}
