<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;

class RoleSelectionController extends Controller
{
    /**
     * Show role selection page
     */
    public function index()
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Get available roles for selection (includes admin if is_admin == 1)
        $availableRoles = $user->getAvailableRolesForSelection();
        
        // If user has only one role, redirect to appropriate dashboard
        if (count($availableRoles) <= 1) {
            return $this->redirectToRoleDashboard($user);
        }

        // Check if impersonating
        $impersonating = session()->has('impersonate_original_user_id');

        // If user has multiple roles (including admin), show role selection page
        return Inertia::render('Auth/RoleSelection', [
            'roles' => $availableRoles,
            'impersonating' => $impersonating,
        ]);
    }

    /**
     * Switch role - update selected_role and redirect to appropriate dashboard
     */
    public function switch(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // For GET request, get role_slug from query parameter
        $request->validate([
            'role_slug' => ['required', 'string'],
        ]);

        $roleSlug = $request->input('role_slug') ?? $request->query('role_slug');
        
        // Verify user has this role and get the role model
        $selectedRole = $user->roles()->where(function($query) use ($roleSlug) {
            $query->where('slug', $roleSlug)
                  ->orWhere('name', $roleSlug);
        })->first();
        
        // Also check legacy role field
        if (!$selectedRole && $user->role === $roleSlug) {
            $selectedRole = \Modules\Roles\Models\Role::where('slug', $roleSlug)
                ->orWhere('name', $roleSlug)
                ->first();
        }
        
        if (!$selectedRole) {
            return back()->withErrors([
                'role_slug' => __('Invalid role selected.'),
            ]);
        }

        // Check if role requires admin access (is_admin == 1)
        if ($selectedRole->is_admin == 1) {
            // User must have is_admin == 1 to access admin roles
            if ($user->is_admin != 1) {
                return back()->withErrors([
                    'role_slug' => __('You do not have permission to access admin dashboard.'),
                ]);
            }
        }

        // Update selected_role in database and session
        $user->update(['selected_role' => $roleSlug]);
        session(['selected_role' => $roleSlug]);
        
        // Refresh user to ensure latest data is loaded
        $user->refresh();
        
        // Verify selected_role is set correctly and user has this role
        if ($user->selected_role !== $roleSlug) {
            return back()->withErrors([
                'role_slug' => __('Failed to update selected role.'),
            ]);
        }
        
        // Ensure permissions are filtered based on selected_role
        // This is handled by HandleInertiaRequests middleware which calls getPermissionArray()
        // which checks selected_role and returns only that role's permissions
        
        // Redirect directly to appropriate dashboard based on selected role
        return $this->redirectToRoleDashboard($user, $roleSlug);
    }

    /**
     * Store selected role in session and redirect
     */
    public function store(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $request->validate([
            'role_slug' => ['required', 'string'],
        ]);

        $roleSlug = $request->role_slug;
        
        // Verify user has this role and get the role model
        $selectedRole = $user->roles()->where(function($query) use ($roleSlug) {
            $query->where('slug', $roleSlug)
                  ->orWhere('name', $roleSlug);
        })->first();
        
        // Also check legacy role field
        if (!$selectedRole && $user->role === $roleSlug) {
            $selectedRole = \Modules\Roles\Models\Role::where('slug', $roleSlug)
                ->orWhere('name', $roleSlug)
                ->first();
        }
        
        if (!$selectedRole) {
            return back()->withErrors([
                'role_slug' => __('Invalid role selected.'),
            ]);
        }

        // Check if role requires admin access (is_admin == 1)
        if ($selectedRole->is_admin == 1) {
            // User must have is_admin == 1 to access admin roles
            if ($user->is_admin != 1) {
                return back()->withErrors([
                    'role_slug' => __('You do not have permission to access admin dashboard.'),
                ]);
            }
        }

        // Update selected_role in database and session
        $user->update(['selected_role' => $roleSlug]);
        session(['selected_role' => $roleSlug]);
        
        // Refresh user to ensure latest data is loaded
        $user->refresh();
        
        // Verify selected_role is set correctly and user has this role
        if ($user->selected_role !== $roleSlug) {
            return back()->withErrors([
                'role_slug' => __('Failed to update selected role.'),
            ]);
        }
        
        // Ensure permissions are filtered based on selected_role
        // This is handled by HandleInertiaRequests middleware which calls getPermissionArray()
        // which checks selected_role and returns only that role's permissions

        return $this->redirectToRoleDashboard($user, $roleSlug);
    }

    /**
     * Redirect to role-specific dashboard
     */
    private function redirectToRoleDashboard(User $user, ?string $roleSlug = null)
    {
        $roleSlug = $roleSlug ?? session('selected_role') ?? $user->role;

        // Get the role model to check is_admin
        $role = $user->roles()->where(function($query) use ($roleSlug) {
            $query->where('slug', $roleSlug)
                  ->orWhere('name', $roleSlug);
        })->first();
        
        // Also check legacy role field
        if (!$role && $user->role === $roleSlug) {
            $role = \Modules\Roles\Models\Role::where('slug', $roleSlug)
                ->orWhere('name', $roleSlug)
                ->first();
        }

        // Determine route based on role type
        if ($role && $role->is_admin == 1) {
            // Any role with is_admin == 1 goes to admin dashboard
            $route = 'admin.dashboard';
        } else {
            // Check by slug for non-admin roles
            $route = match ($roleSlug) {
                'instructor' => 'instructor.dashboard',
                'student' => 'student.dashboard',
                default => 'profile.show',
            };
        }

        // Use Inertia::location() with full absolute URL to force GET request
        // This prevents Inertia from trying to resend POST request
        // Inertia::location() forces a full page reload with GET method
        $routeUrl = URL::route($route, [], true);
        
        // Ensure we have a full absolute URL (with protocol and domain)
        if (!str_starts_with($routeUrl, 'http://') && !str_starts_with($routeUrl, 'https://')) {
            $routeUrl = url($routeUrl);
        }
        
        // Return Inertia location response - this will force browser navigation
        return Inertia::location($routeUrl);
    }
}

