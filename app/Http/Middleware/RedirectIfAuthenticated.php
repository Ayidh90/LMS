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
                
                // Redirect based on user role to their respective dashboard
                return match(true) {
                    $user->isAdmin() => redirect()->route('admin.dashboard'),
                    $user->isInstructor() => redirect()->route('instructor.dashboard'),
                    $user->isStudent() => redirect()->route('student.dashboard'),
                    default => redirect()->route('profile.show'),
                };
            }
        }

        return $next($request);
    }
}

