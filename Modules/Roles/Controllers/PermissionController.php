<?php

namespace Modules\Roles\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions
     */
    public function index()
    {
        $permissions = Permission::withCount('roleModels')->orderBy('name')->get();

        return Inertia::render('Admin/Permissions/Index', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a newly created permission
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:permissions,slug'],
            'description' => ['nullable', 'string'],
        ]);

        Permission::create($validated);

        return redirect()->route('admin.permissions.index')
            ->with('success', __('messages.permissions.created_successfully'));
    }

    /**
     * Update the specified permission
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:permissions,slug,' . $permission->id],
            'description' => ['nullable', 'string'],
        ]);

        $permission->update($validated);

        return redirect()->route('admin.permissions.index')
            ->with('success', __('messages.permissions.updated_successfully'));
    }

    /**
     * Remove the specified permission
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', __('messages.permissions.deleted_successfully'));
    }
}
