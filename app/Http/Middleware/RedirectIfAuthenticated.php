<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Ensure user is an instance of User model
                if (!$user instanceof User) {
                    continue;
                }
                
                // Use same redirect logic as RedirectToDashboard middleware
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
                
                return match ($selectedRole) {
                    'admin', 'super_admin' => redirect()->route('admin.dashboard'),
                    'instructor' => redirect()->route('instructor.dashboard'),
                    'student' => redirect()->route('student.dashboard'),
                    default => redirect()->route('profile.show'),
                };
            }
        }

        return $next($request);
    }
}

