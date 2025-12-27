<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
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
            'role' => $validated['role'], // Keep for backward compatibility
            'is_admin' => $validated['is_admin'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Assign roles using Spatie Permission (support multiple roles)
        if (!empty($validated['role_ids']) && is_array($validated['role_ids'])) {
            // Multiple roles - sync all roles
            $roles = \Modules\Roles\Models\Role::whereIn('id', $validated['role_ids'])->get();
            $user->syncRoles($roles);
        } elseif (!empty($validated['role_id'])) {
            // Single role - assign it (add to existing roles, not replace)
            $role = $this->roleService->getRoleById($validated['role_id']);
            $user->assignRole($role);
        } elseif (!empty($validated['role'])) {
            // Fallback: try to find role by name or slug
            $role = \Modules\Roles\Models\Role::where('name', $validated['role'])
                ->orWhere('slug', $validated['role'])
                ->first();
            if ($role) {
                $user->assignRole($role);
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.users.created_successfully'));
    }

    public function edit(User $user)
    {
        $roles = $this->roleService->getAllRoles();
        
        // Load user roles for display
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
            'national_id' => $validated['national_id'] ?? null, // Allow clearing national_id
            'role' => $validated['role'] ?? $user->role, // Keep for backward compatibility
            'is_admin' => $validated['is_admin'] ?? $user->is_admin,
            'is_active' => $validated['is_active'] ?? $user->is_active,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Update roles using Spatie Permission (support multiple roles)
        if (!empty($validated['role_ids']) && is_array($validated['role_ids'])) {
            // Multiple roles - sync all roles (replace existing)
            $roles = \Modules\Roles\Models\Role::whereIn('id', $validated['role_ids'])->get();
            $user->syncRoles($roles);
        } elseif (!empty($validated['role_id'])) {
            // Single role - sync to this role only (replace existing)
            $role = $this->roleService->getRoleById($validated['role_id']);
            $user->syncRoles([$role]);
        } elseif (!empty($validated['role'])) {
            // Fallback: try to find role by name or slug
            $role = \Modules\Roles\Models\Role::where('name', $validated['role'])
                ->orWhere('slug', $validated['role'])
                ->first();
            if ($role) {
                $user->syncRoles([$role]);
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.users.updated_successfully'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.users.deleted_successfully'));
    }
}

