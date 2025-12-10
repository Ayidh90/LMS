<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Get roles that have this permission (new system)
     */
    public function roleModels()
    {
        return $this->belongsToMany(
            \Modules\Roles\Models\Role::class,
            'role_has_permissions',
            'permission_id',
            'role_id'
        )->withTimestamps();
    }

    /**
     * Alias for roleModels for easier access
     */
    public function rolesWithPermission()
    {
        return $this->roleModels();
    }

    /**
     * Legacy: Get roles that have this permission (old system)
     */
    public function roles()
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

