<?php

namespace Modules\Roles\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Contracts\Role as RoleContract;

class Role extends SpatieRole
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'description',
        'description_ar',
        'guard_name',
        'is_admin',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from name if not provided
        static::creating(function ($role) {
            if (empty($role->slug) && !empty($role->name)) {
                $role->slug = \Illuminate\Support\Str::slug($role->name);
            }
        });

        static::updating(function ($role) {
            if (empty($role->slug) && !empty($role->name)) {
                $role->slug = \Illuminate\Support\Str::slug($role->name);
            }
        });
    }

    /**
     * Alias for users() for backward compatibility
     * Note: Spatie's Role model already has a users() method
     */
    public function models()
    {
        return $this->users();
    }

    /**
     * Check if role has a specific permission by slug
     */
    public function hasPermissionBySlug(string $permissionSlug): bool
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    /**
     * Assign permissions to role by IDs
     */
    public function assignPermissions(array $permissionIds): void
    {
        $this->syncPermissions($permissionIds);
    }

    /**
     * Revoke permissions from role by IDs
     */
    public function revokePermissions(array $permissionIds): void
    {
        $this->permissions()->detach($permissionIds);
    }
}
