<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Permission;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Admin has all permissions (must have is_admin flag set)
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check permissions through new role system first
        $hasPermission = $user->hasPermission($permission);
        
        // Fallback to old system
        if (!$hasPermission) {
            $hasPermission = Permission::hasPermission($user->role, $permission);
        }
        
        if (!$hasPermission) {
            abort(403, __('messages.forbidden'));
        }

        return $next($request);
    }
}

