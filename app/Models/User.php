<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;
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
    // public function courses() // Removed - instructors are assigned to batches now
    // {
    //     return $this->hasMany(\Modules\Courses\Models\Course::class, 'instructor_id');
    // }

    public function batches()
    {
        return $this->hasMany(Batch::class, 'instructor_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    // Get enrolled batches
    public function enrolledBatches()
    {
        return $this->belongsToMany(Batch::class, 'enrollments', 'student_id', 'batch_id')
            ->withPivot('status', 'progress', 'enrolled_at', 'completed_at')
            ->withTimestamps();
    }

    // Get enrolled courses through batches
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

    /**
     * Check if user has a specific role by slug
     */
    public function hasRoleBySlug(string $roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    /**
     * Check if user has any of the given roles by slug
     */
    public function hasAnyRoleBySlug(array $roleSlugs): bool
    {
        return $this->roles()->whereIn('slug', $roleSlugs)->exists();
    }

    /**
     * Get all permissions through roles
     */
    public function getPermissionsThroughRoles()
    {
        return Permission::whereHas('roles', function ($query) {
            $query->whereHas('models', function ($q) {
                $q->where('model_id', $this->id)
                  ->where('model_type', self::class);
            });
        })->get();
    }

    // Role checks - works with both Spatie roles and legacy role field
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin') || 
               $this->hasRoleBySlug('super_admin') || 
               $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || 
               $this->hasRoleBySlug('admin') || 
               $this->isSuperAdmin() ||
               ($this->role === 'admin' || $this->role === 'super_admin');
    }

    public function isInstructor(): bool
    {
        return $this->hasRole('instructor') || 
               $this->hasRoleBySlug('instructor') || 
               $this->role === 'instructor';
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student') || 
               $this->hasRoleBySlug('student') || 
               $this->role === 'student';
    }

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

    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return true;
        }

        // Check using Spatie's permission system - this automatically checks all roles
        $permission = Permission::where('slug', $permissionSlug)->first();
        if ($permission && $this->hasPermissionTo($permission)) {
            return true;
        }

        // Also check by name (Spatie uses name by default)
        if ($permission && $this->hasPermissionTo($permission->name)) {
            return true;
        }

        // Check permissions through all roles manually (for slug-based checks)
        $roles = $this->roles()->with('permissions')->get();
        foreach ($roles as $role) {
            foreach ($role->permissions as $rolePermission) {
                if ($rolePermission->slug === $permissionSlug || $rolePermission->name === $permissionSlug) {
                    return true;
                }
            }
        }

        // Check direct permissions assigned to user
        $directPermissions = $this->permissions;
        foreach ($directPermissions as $directPermission) {
            if ($directPermission->slug === $permissionSlug || $directPermission->name === $permissionSlug) {
                return true;
            }
        }

        // Fallback to old system for backward compatibility
        return Permission::hasPermission($this->role, $permissionSlug);
    }

    /**
     * Get all permissions as an associative array (slug => true)
     * Used for frontend permission checking
     * Concatenates permissions from ALL user roles
     */
    public function getPermissionArray(): array
    {
        $permissions = [];

        // Super admin always has all permissions - return true for any permission check
        if ($this->isSuperAdmin()) {
            // Get all permissions from database
            $allPermissions = Permission::pluck('slug')->toArray();
            foreach ($allPermissions as $slug) {
                $permissions[$slug] = true;
            }
            // Add a special flag to indicate super admin has all permissions
            $permissions['*'] = true;
            return $permissions;
        }

        // Regular admin also gets all permissions
        if ($this->isAdmin()) {
            // Get all permissions from database
            $allPermissions = Permission::pluck('slug')->toArray();
            foreach ($allPermissions as $slug) {
                $permissions[$slug] = true;
            }
            return $permissions;
        }

        // Get permissions through ALL Spatie roles (concatenate from all roles)
        // Method 1: Get permissions via Spatie's built-in method (automatically concatenates from all roles)
        try {
            $spatiePermissions = $this->getPermissionsViaRoles();
            foreach ($spatiePermissions as $permission) {
                $key = $permission->slug ?? $permission->name;
                if ($key) {
                    $permissions[$key] = true;
                }
            }
        } catch (\Exception $e) {
            // If getPermissionsViaRoles() doesn't exist, fall back to manual method
        }

        // Method 2: Manually get permissions from all roles (backup method)
        $roles = $this->roles()->with('permissions')->get();
        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
                // Use slug if available, otherwise use name
                $key = $permission->slug ?: $permission->name;
                if ($key) {
                    $permissions[$key] = true;
                }
            }
        }

        // Also get direct permissions assigned to user (Spatie supports direct user permissions)
        $directPermissions = $this->permissions;
        foreach ($directPermissions as $permission) {
            $key = $permission->slug ?: $permission->name;
            if ($key) {
                $permissions[$key] = true;
            }
        }

        // Fallback to old system for backward compatibility
        if ($this->role) {
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
        }

        return $permissions;
    }

    /**
     * Get all role names/slugs for this user
     * Used for frontend role checking
     */
    public function getRoleNames()
    {
        // Get Spatie roles by slug
        $roles = $this->roles()->pluck('slug')->toArray();
        
        // Also get role names from Spatie (using name field)
        $spatieRoleNames = $this->roles()->pluck('name')->toArray();
        $roles = array_unique(array_merge($roles, $spatieRoleNames));
        
        // Include legacy role field if it exists and is not already in the array
        if ($this->role && !in_array($this->role, $roles)) {
            $roles[] = $this->role;
        }
        
        return collect($roles);
    }

    /**
     * Get available roles for role selection
     * Returns roles that user can switch to
     * Includes admin roles if is_admin == 1
     * Always includes current role even if not in roles relationship
     */
    public function getAvailableRolesForSelection()
    {
        $roles = $this->roles()->get();
        $availableRoles = [];
        $addedSlugs = [];
        
        foreach ($roles as $role) {
            $slug = $role->slug ?? $role->name;
            // Super admin is always available (no checks needed)
            if ($slug === 'super_admin') {
                $availableRoles[] = [
                    'id' => $role->id,
                    'name' => $role->name,
                    'name_ar' => $role->name_ar ?? null,
                    'slug' => $slug,
                    'description' => $role->description ?? null,
                    'description_ar' => $role->description_ar ?? null,
                ];
                $addedSlugs[] = $slug;
            }
            // Regular admin needs both user->is_admin == 1 and role->is_admin == 1
            elseif ($slug === 'admin') {
                if ($this->is_admin == 1 && $role->is_admin == 1) {
                    $availableRoles[] = [
                        'id' => $role->id,
                        'name' => $role->name,
                        'name_ar' => $role->name_ar ?? null,
                        'slug' => $slug,
                        'description' => $role->description ?? null,
                        'description_ar' => $role->description_ar ?? null,
                    ];
                    $addedSlugs[] = $slug;
                }
            } else {
                // Include all non-admin roles
                $availableRoles[] = [
                    'id' => $role->id,
                    'name' => $role->name,
                    'name_ar' => $role->name_ar ?? null,
                    'slug' => $slug,
                    'description' => $role->description ?? null,
                    'description_ar' => $role->description_ar ?? null,
                ];
                $addedSlugs[] = $slug;
            }
        }
        
        // Add current role if it's not already in the list
        if ($this->role && !in_array($this->role, $addedSlugs)) {
            $currentRoleModel = \Modules\Roles\Models\Role::where('slug', $this->role)
                ->orWhere('name', $this->role)
                ->first();
            
            if ($currentRoleModel) {
                // Role exists in database, add it
                $slug = $currentRoleModel->slug ?? $currentRoleModel->name;
                if ($slug === 'super_admin') {
                    $availableRoles[] = [
                        'id' => $currentRoleModel->id,
                        'name' => $currentRoleModel->name,
                        'name_ar' => $currentRoleModel->name_ar ?? null,
                        'slug' => $slug,
                        'description' => $currentRoleModel->description ?? null,
                        'description_ar' => $currentRoleModel->description_ar ?? null,
                    ];
                } elseif ($slug === 'admin') {
                    if ($this->is_admin == 1 && $currentRoleModel->is_admin == 1) {
                        $availableRoles[] = [
                            'id' => $currentRoleModel->id,
                            'name' => $currentRoleModel->name,
                            'name_ar' => $currentRoleModel->name_ar ?? null,
                            'slug' => $slug,
                            'description' => $currentRoleModel->description ?? null,
                            'description_ar' => $currentRoleModel->description_ar ?? null,
                        ];
                    }
                } else {
                    $availableRoles[] = [
                        'id' => $currentRoleModel->id,
                        'name' => $currentRoleModel->name,
                        'name_ar' => $currentRoleModel->name_ar ?? null,
                        'slug' => $slug,
                        'description' => $currentRoleModel->description ?? null,
                        'description_ar' => $currentRoleModel->description_ar ?? null,
                    ];
                }
            } else {
                // Role doesn't exist in database, add it as a simple entry
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
        
        return $availableRoles;
    }

    /**
     * Get all user roles including admin roles
     */
    public function getAllRoles()
    {
        return $this->roles()->get();
    }

    /**
     * Check if user has multiple roles (more than one)
     */
    public function hasMultipleRoles(): bool
    {
        return $this->roles()->count() > 1 || 
               ($this->roles()->count() === 1 && $this->role && $this->role !== $this->roles()->first()?->slug);
    }
}

