<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Modules\Roles\Services\RoleService;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
        private RoleService $roleService
    ) {
        // Allow stop-impersonating even when impersonating (bypass permission check)
        $this->middleware(function ($request, $next) {
            if ($request->routeIs('admin.users.stop-impersonating') && session()->has('impersonate_original_user_id')) {
                return $next($request);
            }
            return $next($request);
        })->only(['stopImpersonating']);
    }

    public function index()
    {
        $filters = [
            'search' => request('search'),
            'role' => request('role'),
            'sort_by' => request('sort_by', 'created_at'),
            'sort_order' => request('sort_order', 'desc'),
        ];

        $perPage = request('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50, 100]) ? $perPage : 15;

        $users = $this->userService->getPaginatedUsers($filters, $perPage);
        $roles = $this->roleService->getAllRoles();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'roles' => $roles ? $roles->toArray() : [],
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
        $user = $this->userService->createUser($request->validated());

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
        $this->userService->updateUser($user, $request->validated());

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.users.updated_successfully'));
    }

    public function destroy(User $user)
    {
        $this->userService->deleteUser($user);

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.users.deleted_successfully'));
    }

    /**
     * Impersonate a user
     */
    public function impersonate(User $user)
    {
        // Check if current user can impersonate
        /** @var User|null $currentUser */
        $currentUser = Auth::user();
        
        if (!$currentUser || (!$currentUser->isSuperAdmin() && !$currentUser->isAdmin())) {
            abort(403, __('messages.users.no_permission_to_impersonate'));
        }

        // Prevent impersonating yourself
        if ($currentUser->id === $user->id) {
            return back()->withErrors([
                'impersonate' => __('messages.users.cannot_impersonate_yourself'),
            ]);
        }

        // Store original user ID in session
        session([
            'impersonate_original_user_id' => $currentUser->id,
            'impersonate_started_at' => now(),
        ]);

        // Log in as the target user
        Auth::login($user);
        session()->regenerate();

        // Get available roles for the impersonated user
        $availableRoles = $user->getAvailableRolesForSelection();

        // If user has multiple roles, redirect to role selection
        if (count($availableRoles) > 1) {
            session()->flash('impersonating', true);
            return redirect()->route('role-selection');
        }

        // Single role - set selected_role and redirect to dashboard
        $roleSlug = $user->role ?? ($availableRoles[0]['slug'] ?? null);
        if ($roleSlug) {
            $user->update(['selected_role' => $roleSlug]);
            session(['selected_role' => $roleSlug]);
        }

        // Determine the correct dashboard route based on role
        $dashboardRoute = $this->userService->getDashboardRouteForRole($roleSlug);
        session()->flash('success', __('messages.users.impersonation_started', ['name' => $user->name]));
        return redirect()->route($dashboardRoute);
    }

    /**
     * Stop impersonating and return to original user
     */
    public function stopImpersonating()
    {
        // Check if user is actually impersonating
        $originalUserId = session('impersonate_original_user_id');

        if (!$originalUserId) {
            // If not impersonating, check if user has admin access
            /** @var User|null $currentUser */
            $currentUser = Auth::user();
            if (!$currentUser || (!$currentUser->isSuperAdmin() && !$currentUser->isAdmin())) {
                abort(403, __('messages.users.no_permission_to_impersonate'));
            }
            return redirect()->route('admin.users.index')->with('error', __('messages.users.no_impersonation_session'));
        }

        $originalUser = User::find($originalUserId);

        if (!$originalUser) {
            session()->forget(['impersonate_original_user_id', 'impersonate_started_at']);
            return redirect()->route('admin.users.index')->with('error', __('messages.users.original_user_not_found'));
        }

        // Log back in as original user
        Auth::login($originalUser);
        session()->regenerate();

        // Restore original user's selected role
        $originalUser->refresh();
        $availableRoles = $originalUser->getAvailableRolesForSelection();
        
        if (count($availableRoles) > 1) {
            $selectedRole = $originalUser->selected_role ?? $originalUser->role;
            session(['selected_role' => $selectedRole]);
        } else {
            $roleSlug = $originalUser->role ?? ($availableRoles[0]['slug'] ?? null);
            if ($roleSlug) {
                $originalUser->update(['selected_role' => $roleSlug]);
                session(['selected_role' => $roleSlug]);
            }
        }

        // Clear impersonation session
        session()->forget(['impersonate_original_user_id', 'impersonate_started_at']);

        session()->flash('success', __('messages.users.impersonation_stopped'));
        return redirect()->route('admin.users.index');
    }
}

