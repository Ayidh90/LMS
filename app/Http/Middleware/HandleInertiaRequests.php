<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Services\SettingsService;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }


    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // Get locale from cookie or default to Arabic
        $locale = $request->cookie('locale', 'ar');
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'ar';
        }
        
        // Set locale in app
        app()->setLocale($locale);
        
        // Get direction from cookie or default based on locale
        $direction = $request->cookie('direction');
        if (!$direction) {
            $direction = $locale === 'ar' ? 'rtl' : 'ltr';
        }
        
        $user = $request->user();
        
        // Ensure selected_role is properly set before getting permissions
        if ($user) {
            // Refresh to get latest selected_role from database
            $user->refresh();
            
            // Get user roles from model relationship (Spatie roles)
            $userRoles = $user->roles()->get();
            $rolesCount = $user->countUserRoles($userRoles);
            
            // Get selected_role from user table (database)
            $selectedRole = $user->selected_role ?? session('selected_role');
            
            // If user has only one role, automatically set it as selected_role
            if ($rolesCount === 1) {
                $singleRole = $userRoles->first();
                if ($singleRole) {
                    $singleRoleSlug = $singleRole->slug ?? $singleRole->name;
                } else if ($user->role) {
                    // Fallback to legacy role field
                    $singleRoleSlug = $user->role;
                } else {
                    $singleRoleSlug = null;
                }
                
                // If no selected_role is set, or if it doesn't match the single role, update it
                if ($singleRoleSlug && (!$selectedRole || $selectedRole !== $singleRoleSlug)) {
                    $selectedRole = $singleRoleSlug;
                    // Save to database (user table)
                    if (!$user->selected_role || $user->selected_role !== $singleRoleSlug) {
                        $user->update(['selected_role' => $singleRoleSlug]);
                    }
                    session(['selected_role' => $singleRoleSlug]);
                }
            }
            
            // If user has multiple roles, validate selected_role against actual roles
            if ($rolesCount > 1 && $selectedRole) {
                // Validate that selected_role exists in user's roles
                $roleExists = $userRoles->contains(function($role) use ($selectedRole) {
                    return ($role->slug === $selectedRole || $role->name === $selectedRole);
                });
                
                // Also check legacy role field
                if (!$roleExists && $user->role === $selectedRole) {
                    $roleExists = true;
                }
                
                // If selected_role doesn't match any of user's roles, clear it
                if (!$roleExists) {
                    $selectedRole = null;
                    $user->update(['selected_role' => null]);
                    session()->forget('selected_role');
                }
            }
            
            // Set selected_role on user model for getPermissionArray() to use
            if ($selectedRole) {
                $user->setAttribute('selected_role', $selectedRole);
            }
        }
        
        // Get settings service
        $settingsService = app(SettingsService::class);
        $instructorPermissions = $settingsService->getInstructorPermissions();
        $websiteSettings = $settingsService->getWebsiteSettings();
        
        // Get logo and favicon URLs using Settings model methods
        $logoUrl = $websiteSettings['logo_url'] ?? null;
        $faviconUrl = $websiteSettings['favicon_url'] ?? null;
        
        // Get selected role (from database or session or default to user's role)
        $selectedRole = null;
        if ($user) {
            $selectedRole = $user->selected_role ?? session('selected_role') ?? $user->role;
        }
        
        // Check if currently impersonating
        $isImpersonating = session()->has('impersonate_original_user_id');
        $originalUserId = session('impersonate_original_user_id');
        $originalUser = $isImpersonating && $originalUserId ? \App\Models\User::find($originalUserId) : null;

        return [
            ...parent::share($request),
            'direction' => $direction,
            'locale' => $locale,
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'national_id' => $user->national_id,
                    'role' => $user->role,
                    'is_admin' => $user->is_admin ?? false,
                    'is_active' => $user->is_active ?? true,
                    'selected_role' => $selectedRole,
                ] : null,
                'can' => $user ? $user->getPermissionArray() : [],
                'roles' => $user ? array_values($user->getRoleNames()->toArray()) : [],
                'availableRoles' => $user ? $user->getAvailableRolesForSelection() : [],
                'selectedRole' => $selectedRole,
                'impersonating' => $isImpersonating,
                'originalUser' => $originalUser ? [
                    'id' => $originalUser->id,
                    'name' => $originalUser->name,
                    'email' => $originalUser->email,
                ] : null,
            ],
            'settings' => [
                'instructor_permissions' => $instructorPermissions,
                'website' => [
                    'name' => $websiteSettings['name'],
                    'logo' => $logoUrl,
                    'favicon' => $faviconUrl,
                    'info' => $websiteSettings['info'],
                    'email' => $websiteSettings['email'],
                    'mobile' => $websiteSettings['mobile'],
                ],
            ],
        ];
    }
}
