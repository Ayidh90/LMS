<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
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
        return Inertia::render('Admin/Users/Create');
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'national_id' => $validated['national_id'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_admin' => $validated['is_admin'] ?? in_array($validated['role'], ['admin', 'super_admin']),
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.users.created_successfully'));
    }

    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'national_id' => $validated['national_id'] ?? $user->national_id,
            'role' => $validated['role'],
            'is_admin' => $validated['is_admin'] ?? in_array($validated['role'], ['admin', 'super_admin']),
            'is_active' => $validated['is_active'] ?? $user->is_active,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

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

