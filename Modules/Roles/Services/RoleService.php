<?php

namespace Modules\Roles\Services;

use Modules\Roles\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RoleService
{
    /**
     * Get all roles with permissions count
     */
    public function getAllRoles()
    {
        return Role::withCount('permissions')->latest()->get();
    }

    /**
     * Get role by ID with permissions
     */
    public function getRoleById(int $id)
    {
        return Role::with('permissions')->findOrFail($id);
    }

    /**
     * Create a new role
     */
    public function createRole(array $data, array $permissionIds = []): Role
    {
        return DB::transaction(function () use ($data, $permissionIds) {
            $role = Role::create([
                'name' => $data['name'],
                'slug' => $data['slug'] ?? \Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'guard_name' => $data['guard_name'] ?? 'web',
            ]);

            if (!empty($permissionIds)) {
                $role->assignPermissions($permissionIds);
            }

            return $role->load('permissions');
        });
    }

    /**
     * Update a role
     */
    public function updateRole(Role $role, array $data, array $permissionIds = null): Role
    {
        return DB::transaction(function () use ($role, $data, $permissionIds) {
            $role->update([
                'name' => $data['name'] ?? $role->name,
                'slug' => $data['slug'] ?? $role->slug,
                'description' => $data['description'] ?? $role->description,
                'guard_name' => $data['guard_name'] ?? $role->guard_name,
            ]);

            if ($permissionIds !== null) {
                $role->assignPermissions($permissionIds);
            }

            return $role->load('permissions');
        });
    }

    /**
     * Delete a role
     */
    public function deleteRole(Role $role): bool
    {
        return DB::transaction(function () use ($role) {
            // Detach all permissions
            $role->permissions()->detach();
            
            // Detach all models (users)
            $role->models()->detach();
            
            return $role->delete();
        });
    }

    /**
     * Assign role to user
     */
    public function assignRoleToUser(User $user, Role $role): void
    {
        if (!$user->roles()->where('roles.id', $role->id)->exists()) {
            $user->roles()->attach($role->id, ['model_type' => User::class]);
        }
    }

    /**
     * Remove role from user
     */
    public function removeRoleFromUser(User $user, Role $role): void
    {
        $user->roles()->detach($role->id);
    }

    /**
     * Sync user roles (replace all roles)
     */
    public function syncUserRoles(User $user, array $roleIds): void
    {
        $user->roles()->sync($roleIds);
    }

    /**
     * Get all permissions
     */
    public function getAllPermissions()
    {
        return Permission::orderBy('name')->get();
    }

    /**
     * Get users with a specific role
     */
    public function getUsersWithRole(Role $role)
    {
        return $role->models()->paginate(15);
    }
}

