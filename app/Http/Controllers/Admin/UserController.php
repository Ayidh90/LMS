<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Modules\Roles\Models\Role;
use Modules\Roles\Services\RoleService;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(
        private RoleService $roleService
    ) {}

    public function index()
    {
        $users = User::with('roles')
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        $roles = $this->roleService->getAllRoles();

        return Inertia::render('Admin/Users/Create', [
            'roles' => $roles ? $roles->toArray() : [],
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'national_id' => $validated['national_id'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? null,
            'is_admin' => $validated['is_admin'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $this->syncUserRoles($user, $validated);
        $this->handleSelectedRole($user);

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.users.created_successfully'));
    }

    public function edit(User $user)
    {
        $roles = $this->roleService->getAllRoles();
        $user->load('roles');

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'roles' => $roles ? $roles->toArray() : [],
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'national_id' => $validated['national_id'] ?? null,
            'role' => $validated['role'] ?? $user->role,
            'is_admin' => $validated['is_admin'] ?? $user->is_admin,
            'is_active' => $validated['is_active'] ?? $user->is_active,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        $this->syncUserRoles($user, $validated);
        $this->handleSelectedRole($user);

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.users.updated_successfully'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.users.deleted_successfully'));
    }

    /**
     * Sync user roles to model_has_roles table
     */
    private function syncUserRoles(User $user, array $validated): void
    {
        $roles = $this->getRolesFromRequest($validated);
        
        if (!empty($roles)) {
            $user->syncRoles($roles);
        }
    }

    /**
     * Get roles from request data
     */
    private function getRolesFromRequest(array $validated): array
    {
        // Multiple roles
        if (!empty($validated['role_ids']) && is_array($validated['role_ids'])) {
            return Role::whereIn('id', $validated['role_ids'])->get()->all();
        }

        // Single role by ID
        if (!empty($validated['role_id'])) {
            $role = $this->roleService->getRoleById($validated['role_id']);
            return $role ? [$role] : [];
        }

        // Fallback: find role by name or slug
        if (!empty($validated['role'])) {
            $role = Role::where('name', $validated['role'])
                ->orWhere('slug', $validated['role'])
                ->first();
            return $role ? [$role] : [];
        }

        return [];
    }

    /**
     * Handle selected_role based on user's roles
     */
    private function handleSelectedRole(User $user): void
    {
        $user->refresh();
        $userRoles = $user->roles()->get();
        $rolesCount = $user->countUserRoles($userRoles);

        if ($rolesCount === 0) {
            $user->update(['selected_role' => null]);
            return;
        }

        if ($rolesCount === 1) {
            $singleRole = $userRoles->first();
            $roleSlug = $singleRole->slug ?? $singleRole->name ?? $user->role;
            $user->update(['selected_role' => $roleSlug]);
            return;
        }

        // Multiple roles - validate selected_role
        $selectedRole = $user->selected_role;
        if (!$selectedRole) {
            return;
        }

        $roleExists = $userRoles->contains(function($role) use ($selectedRole) {
            return ($role->slug === $selectedRole || $role->name === $selectedRole);
        });

        // Also check legacy role field
        if (!$roleExists && $user->role === $selectedRole) {
            return;
        }

        // Clear invalid selected_role
        if (!$roleExists) {
            $user->update(['selected_role' => null]);
        }
    }
}

