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

        // If user has multiple roles (including admin), show role selection page
        return Inertia::render('Auth/RoleSelection', [
            'roles' => $availableRoles,
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
        
        // Verify user has this role
        if (!$user->hasRoleBySlug($roleSlug) && $user->role !== $roleSlug) {
            return back()->withErrors([
                'role_slug' => __('Invalid role selected.'),
            ]);
        }

        // If user is trying to select admin role
        if ($roleSlug === 'super_admin') {
            // Super admin can always access (no checks needed)
            if (!$user->hasRoleBySlug('super_admin') && $user->role !== 'super_admin') {
                return back()->withErrors([
                    'role_slug' => __('Invalid role selected.'),
                ]);
            }
        } elseif ($roleSlug === 'admin') {
            // Regular admin needs both user->is_admin == 1 and role->is_admin == 1
            if ($user->is_admin != 1) {
                return back()->withErrors([
                    'role_slug' => __('You do not have permission to access admin dashboard.'),
                ]);
            }
            
            $selectedRole = $user->roles()->where(function($query) {
                $query->where('slug', 'admin')
                      ->orWhere('name', 'admin');
            })->first();
            
            if (!$selectedRole && $user->role === 'admin') {
                $selectedRole = \Modules\Roles\Models\Role::where('slug', 'admin')
                    ->orWhere('name', 'admin')
                    ->first();
            }
            
            if (!$selectedRole || $selectedRole->is_admin != 1) {
                return back()->withErrors([
                    'role_slug' => __('This role does not have admin access.'),
                ]);
            }
        }

        // Update selected_role in database (set to null first, then set new role)
        $user->update(['selected_role' => null]);
        $user->refresh();
        $user->update(['selected_role' => $roleSlug]);
        
        // Store selected role in session
        session(['selected_role' => $roleSlug]);
        
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
        
        // Verify user has this role
        if (!$user->hasRoleBySlug($roleSlug) && $user->role !== $roleSlug) {
            return back()->withErrors([
                'role_slug' => __('Invalid role selected.'),
            ]);
        }

        // If user is trying to select admin role
        if ($roleSlug === 'super_admin') {
            // Super admin can always access (no checks needed)
            // Just verify user has this role
            if (!$user->hasRoleBySlug('super_admin') && $user->role !== 'super_admin') {
                return back()->withErrors([
                    'role_slug' => __('Invalid role selected.'),
                ]);
            }
        } elseif ($roleSlug === 'admin') {
            // Regular admin needs both user->is_admin == 1 and role->is_admin == 1
            // Check user->is_admin
            if ($user->is_admin != 1) {
                return back()->withErrors([
                    'role_slug' => __('You do not have permission to access admin dashboard.'),
                ]);
            }
            
            // Check role->is_admin
            $selectedRole = $user->roles()->where(function($query) {
                $query->where('slug', 'admin')
                      ->orWhere('name', 'admin');
            })->first();
            
            // Also check legacy role field
            if (!$selectedRole && $user->role === 'admin') {
                $selectedRole = \Modules\Roles\Models\Role::where('slug', 'admin')
                    ->orWhere('name', 'admin')
                    ->first();
            }
            
            if (!$selectedRole || $selectedRole->is_admin != 1) {
                return back()->withErrors([
                    'role_slug' => __('This role does not have admin access.'),
                ]);
            }
        }

        // Update selected_role in database (set to null first, then set new role)
        $user->update(['selected_role' => null]);
        $user->refresh();
        $user->update(['selected_role' => $roleSlug]);
        
        // Store selected role in session
        session(['selected_role' => $roleSlug]);

        return $this->redirectToRoleDashboard($user, $roleSlug);
    }

    /**
     * Redirect to role-specific dashboard
     */
    private function redirectToRoleDashboard(User $user, ?string $roleSlug = null)
    {
        $roleSlug = $roleSlug ?? session('selected_role') ?? $user->role;

        $route = match ($roleSlug) {
            'admin', 'super_admin' => 'admin.dashboard',
            'instructor' => 'instructor.dashboard',
            'student' => 'student.dashboard',
            default => 'profile.show',
        };

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

