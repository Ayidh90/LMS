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
               $this->role === 'super_admin' || 
               ($this->is_admin && $this->role === 'admin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || 
               $this->hasRoleBySlug('admin') || 
               ($this->is_admin && ($this->role === 'admin' || $this->role === 'super_admin')) || 
               $this->role === 'super_admin';
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

        // Check using Spatie's permission system
        $permission = Permission::where('slug', $permissionSlug)->first();
        if ($permission && $this->hasPermissionTo($permission)) {
            return true;
        }

        // Check permissions through roles (custom method)
        foreach ($this->roles as $role) {
            if (method_exists($role, 'hasPermissionBySlug') && $role->hasPermissionBySlug($permissionSlug)) {
                return true;
            }
        }

        // Fallback to old system for backward compatibility
        return Permission::hasPermission($this->role, $permissionSlug);
    }

    /**
     * Get all permissions as an associative array (slug => true)
     * Used for frontend permission checking
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

        // Get permissions through Spatie roles
        $roles = $this->roles()->with('permissions')->get();
        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
                if ($permission->slug) {
                    $permissions[$permission->slug] = true;
                }
            }
        }

        // Also get direct permissions (Spatie supports direct user permissions)
        $directPermissions = $this->permissions;
        foreach ($directPermissions as $permission) {
            if ($permission->slug) {
                $permissions[$permission->slug] = true;
            }
        }

        // Fallback to old system for backward compatibility
        $legacyPermissions = DB::table('role_permissions')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('role_permissions.role', $this->role)
            ->pluck('permissions.slug')
            ->toArray();

        foreach ($legacyPermissions as $slug) {
            $permissions[$slug] = true;
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
}

