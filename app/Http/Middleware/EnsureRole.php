<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Super admin has access to everything
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // For admin routes, check both role and is_admin flag
        if (in_array('admin', $roles)) {
            if (!$user->isAdmin()) {
                abort(403, __('messages.unauthorized'));
            }
            return $next($request);
        }

        // Check if user has the required role (check both old system and new system)
        $hasRole = false;
        
        // Check new role system (polymorphic)
        foreach ($roles as $roleSlug) {
            if ($user->hasRole($roleSlug)) {
                $hasRole = true;
                break;
            }
        }
        
        // Fallback to old system
        if (!$hasRole && in_array($user->role, $roles)) {
            $hasRole = true;
        }
        
        if (!$hasRole) {
            abort(403, __('messages.unauthorized'));
        }

        return $next($request);
    }
}

