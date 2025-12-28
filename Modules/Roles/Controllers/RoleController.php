<?php

namespace Modules\Roles\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Modules\Roles\Models\Role;
use Modules\Roles\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function __construct(
        private RoleService $roleService
    ) {}

    /**
     * Display a listing of roles
     */
    public function index()
    {
        $roles = $this->roleService->getAllRoles();
        $permissions = $this->roleService->getAllPermissions(Auth::user());

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles ? $roles->toArray() : [],
            'permissions' => $permissions ? $permissions->toArray() : [],
        ]);
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        $permissions = $this->roleService->getAllPermissions(Auth::user());

        return Inertia::render('Admin/Roles/Create', [
            'permissions' => $permissions ? $permissions->toArray() : [],
        ]);
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:roles,slug'],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'is_admin' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        // Validate that user can only assign permissions they have access to
        /** @var User|null $user */
        $user = Auth::user();
        if ($user && isset($validated['permissions']) && !empty($validated['permissions'])) {
            $allowedPermissions = $this->roleService->getAllPermissions($user)->pluck('id')->toArray();
            $requestedPermissions = $validated['permissions'];
            
            // Check if user has permissions.manage - if yes, they can assign any permission
            if (!$user->hasPermission('permissions.manage')) {
                // Filter out permissions the user doesn't have access to
                $validated['permissions'] = array_intersect($requestedPermissions, $allowedPermissions);
            }
        }

        $role = $this->roleService->createRole(
            $validated,
            $validated['permissions'] ?? []
        );

        return redirect()->route('admin.roles.index')
            ->with('success', __('messages.roles.created_successfully'));
    }

    /**
     * Display the specified role
     */
    public function show(Role $role)
    {
        $role = $this->roleService->getRoleById($role->id);
        $users = $this->roleService->getUsersWithRole($role);
        $permissions = $this->roleService->getAllPermissions(Auth::user());

        return Inertia::render('Admin/Roles/Show', [
            'role' => $role ? $role->toArray() : null,
            'users' => $users ?: [],
            'permissions' => $permissions ? $permissions->toArray() : [],
        ]);
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit(Role $role)
    {
        $role = $this->roleService->getRoleById($role->id);
        $permissions = $this->roleService->getAllPermissions(Auth::user());

        return Inertia::render('Admin/Roles/Edit', [
            'role' => $role ? $role->toArray() : null,
            'permissions' => $permissions ? $permissions->toArray() : [],
        ]);
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:roles,slug,' . $role->id],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'is_admin' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        // Validate that user can only assign permissions they have access to
        /** @var User|null $user */
        $user = Auth::user();
        if ($user && isset($validated['permissions']) && $validated['permissions'] !== null) {
            $allowedPermissions = $this->roleService->getAllPermissions($user)->pluck('id')->toArray();
            $requestedPermissions = $validated['permissions'];
            
            // Check if user has permissions.manage - if yes, they can assign any permission
            if (!$user->hasPermission('permissions.manage')) {
                // Filter out permissions the user doesn't have access to
                $validated['permissions'] = array_intersect($requestedPermissions, $allowedPermissions);
            }
        }

        $role = $this->roleService->updateRole(
            $role,
            $validated,
            $validated['permissions'] ?? null
        );

        return redirect()->route('admin.roles.index')
            ->with('success', __('messages.roles.updated_successfully'));
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role)
    {
        $this->roleService->deleteRole($role);

        return redirect()->route('admin.roles.index')
            ->with('success', __('messages.roles.deleted_successfully'));
    }
}
