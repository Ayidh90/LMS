<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectToDashboard
{
    /**
     * Handle an incoming request and redirect authenticated users to appropriate dashboard.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var User $user */
        $user = Auth::user();
        
        // Refresh to get latest data from database
        $user->refresh();
        
        // Get available roles for selection first
        $availableRoles = $user->getAvailableRolesForSelection();
        
        // If user has multiple roles, always show role selection page
        // Even if they have selected_role saved (to allow switching)
        if (count($availableRoles) > 1) {
            return redirect()->route('role-selection');
        }
        
        // Single role - use user's default role and save to database
        $selectedRole = $user->role;
        $user->update(['selected_role' => $selectedRole]);
        session(['selected_role' => $selectedRole]);
        
        return $this->redirectToRoleDashboard($selectedRole);
    }

    /**
     * Redirect to role-specific dashboard
     */
    private function redirectToRoleDashboard(string $roleSlug): Response
    {
        return match ($roleSlug) {
            'admin', 'super_admin' => redirect()->route('admin.dashboard'),
            'instructor' => redirect()->route('instructor.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => redirect()->route('profile.show'),
        };
    }
}
