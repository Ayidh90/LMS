<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var User $user */
        $user = Auth::user();

        // For admin routes, check both user->is_admin and role->is_admin
        if (in_array('admin', $roles)) {
            // Super admin can always access (no checks needed)
            if ($user->isSuperAdmin()) {
                return $next($request);
            }
            
            // Regular admin needs is_admin == 1
            // Check 1: User must have is_admin == 1
            if (!$user->is_admin || $user->is_admin == 0) {
                abort(403, __('messages.unauthorized'));
            }
            
            // Check 2: User must have a role with is_admin == 1
            $hasAdminRole = false;
            $userRoles = $user->roles()->get();
            
            foreach ($userRoles as $role) {
                if ($role->is_admin == 1) {
                    $hasAdminRole = true;
                    break;
                }
            }
            
            // Also check legacy role field if it's admin
            if (!$hasAdminRole && $user->role === 'admin') {
                $legacyRole = \Modules\Roles\Models\Role::where('slug', 'admin')
                    ->orWhere('name', 'admin')
                    ->first();
                if ($legacyRole && $legacyRole->is_admin == 1) {
                    $hasAdminRole = true;
                }
            }
            
            if (!$hasAdminRole) {
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

