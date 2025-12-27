<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Permission;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var User $user */
        $user = Auth::user();
        
        // Super admin has all permissions (always allowed)
        if ($user->isSuperAdmin()) {
            return $next($request);
        }
        
        // Regular admin has all permissions (must have both user->is_admin == 1 and role->is_admin == 1)
        if ($user->isAdmin() && $user->is_admin == 1) {
            // Check if user has a role with is_admin == 1
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
            
            if ($hasAdminRole) {
                return $next($request);
            }
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

