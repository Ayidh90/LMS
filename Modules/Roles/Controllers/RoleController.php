<?php

namespace Modules\Roles\Controllers;

use App\Http\Controllers\Controller;
use Modules\Roles\Models\Role;
use Modules\Roles\Services\RoleService;
use Illuminate\Http\Request;
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
        $permissions = $this->roleService->getAllPermissions();

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        $permissions = $this->roleService->getAllPermissions();

        return Inertia::render('Admin/Roles/Create', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:roles,slug'],
            'description' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

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
        $permissions = $this->roleService->getAllPermissions();

        return Inertia::render('Admin/Roles/Show', [
            'role' => $role,
            'users' => $users,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit(Role $role)
    {
        $role = $this->roleService->getRoleById($role->id);
        $permissions = $this->roleService->getAllPermissions();

        return Inertia::render('Admin/Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:roles,slug,' . $role->id],
            'description' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = $this->roleService->updateRole(
            $role,
            $validated,
            $validated['permissions'] ?? []
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
