<?php

namespace Modules\Roles\Services;

use Modules\Roles\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
                'name_ar' => $data['name_ar'] ?? null,
                'slug' => $data['slug'] ?? Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'description_ar' => $data['description_ar'] ?? null,
                'guard_name' => $data['guard_name'] ?? 'web',
            ]);

            if (!empty($permissionIds)) {
                $role->syncPermissions($permissionIds);
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
                'name_ar' => $data['name_ar'] ?? $role->name_ar,
                'slug' => $data['slug'] ?? $role->slug,
                'description' => $data['description'] ?? $role->description,
                'description_ar' => $data['description_ar'] ?? $role->description_ar,
                'guard_name' => $data['guard_name'] ?? $role->guard_name,
            ]);

            if ($permissionIds !== null) {
                $role->syncPermissions($permissionIds);
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
            // Spatie handles cleanup automatically
            return $role->delete();
        });
    }

    /**
     * Assign role to user (using Spatie)
     */
    public function assignRoleToUser(User $user, Role $role): void
    {
        $user->assignRole($role);
    }

    /**
     * Remove role from user (using Spatie)
     */
    public function removeRoleFromUser(User $user, Role $role): void
    {
        $user->removeRole($role);
    }

    /**
     * Sync user roles (replace all roles using Spatie)
     */
    public function syncUserRoles(User $user, array $roleIds): void
    {
        $roles = Role::whereIn('id', $roleIds)->get();
        $user->syncRoles($roles);
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
        return $role->users()->paginate(15);
    }
}

