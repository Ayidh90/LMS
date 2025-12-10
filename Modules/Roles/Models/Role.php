<?php

namespace Modules\Roles\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permission;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'guard_name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all models that have this role
     */
    public function models()
    {
        return $this->morphToMany(
            \App\Models\User::class,
            'model',
            'model_has_roles',
            'role_id',
            'model_id'
        )->withPivot('model_type');
    }

    /**
     * Get all permissions for this role
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'role_has_permissions',
            'role_id',
            'permission_id'
        )->withTimestamps();
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissions(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
    }

    /**
     * Revoke permissions from role
     */
    public function revokePermissions(array $permissionIds): void
    {
        $this->permissions()->detach($permissionIds);
    }
}
