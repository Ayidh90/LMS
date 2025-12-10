<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
     * Get all roles for this user (polymorphic)
     */
    public function roles()
    {
        return $this->morphToMany(
            \Modules\Roles\Models\Role::class,
            'model',
            'model_has_roles',
            'model_id',
            'role_id'
        )->withPivot('model_type')->withTimestamps();
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roleSlugs): bool
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

    // Role checks
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin' || ($this->is_admin && $this->role === 'admin');
    }

    public function isAdmin(): bool
    {
        // User must have is_admin flag set to true AND role must be admin or super_admin
        return ($this->is_admin && ($this->role === 'admin' || $this->role === 'super_admin')) || $this->role === 'super_admin';
    }

    public function isInstructor(): bool
    {
        return $this->role === 'instructor';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
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

        // Check permissions through roles
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permissionSlug)) {
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

        // Get permissions through roles (new system)
        $roles = $this->roles()->with('permissions')->get();
        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
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
        $roles = $this->roles()->pluck('slug')->toArray();
        
        // Include legacy role field if it exists and is not already in the array
        if ($this->role && !in_array($this->role, $roles)) {
            $roles[] = $this->role;
        }
        
        return collect($roles);
    }
}

