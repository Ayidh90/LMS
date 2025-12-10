<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'guard_name',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from name if not provided
        static::creating(function ($permission) {
            if (empty($permission->slug) && !empty($permission->name)) {
                $permission->slug = \Str::slug($permission->name);
            }
        });

        static::updating(function ($permission) {
            if (empty($permission->slug) && !empty($permission->name)) {
                $permission->slug = \Str::slug($permission->name);
            }
        });
    }

    /**
     * Legacy: Get roles that have this permission (old system)
     */
    public function legacyRoles()
    {
        return $this->belongsToMany(RolePermission::class, 'role_permissions', 'permission_id', 'role');
    }

    public static function hasPermission(string $role, string $permissionSlug): bool
    {
        return \DB::table('role_permissions')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('role_permissions.role', $role)
            ->where('permissions.slug', $permissionSlug)
            ->exists();
    }
}

