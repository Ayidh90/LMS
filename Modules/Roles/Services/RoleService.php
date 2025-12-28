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
                'is_admin' => $data['is_admin'] ?? false,
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
                'is_admin' => $data['is_admin'] ?? $role->is_admin,
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
     * Get all permissions (filtered by user's access)
     * If user has permissions.manage, return all permissions
     * Otherwise, return only permissions the user has
     * Excludes categories and faqs permissions
     */
    public function getAllPermissions(?User $user = null)
    {
        // If no user provided, get current authenticated user
        if (!$user) {
            $user = auth()->user();
        }

        // Base query - exclude categories and faqs
        $query = Permission::where(function ($q) {
            $q->where('slug', 'not like', 'categories.%')
              ->where('slug', 'not like', 'faqs.%');
        });

        // If user has permissions.manage, they can see all permissions
        if ($user && $user->hasPermission('permissions.manage')) {
            return $query->orderBy('name')->get();
        }

        // Otherwise, only return permissions the user actually has
        if ($user) {
            $userPermissionIds = collect();
            
            // Get permissions from all user roles
            $roles = $user->roles()->with('permissions')->get();
            foreach ($roles as $role) {
                $userPermissionIds = $userPermissionIds->merge($role->permissions->pluck('id'));
            }
            
            // Also get direct permissions assigned to user
            $directPermissions = $user->permissions()->pluck('id');
            $userPermissionIds = $userPermissionIds->merge($directPermissions);
            
            // Return unique permissions (excluding categories and faqs)
            return $query->whereIn('id', $userPermissionIds->unique())
                ->orderBy('name')
                ->get();
        }

        // If no user, return empty collection
        return collect();
    }

    /**
     * Get users with a specific role
     */
    public function getUsersWithRole(Role $role)
    {
        // Use Spatie's role scope to get users with this role
        return User::role($role->name)->get()->toArray();
    }
}

