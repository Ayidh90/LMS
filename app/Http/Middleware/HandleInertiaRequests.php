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
        
        // Get instructor permissions settings
        $settingsService = app(SettingsService::class);
        $instructorPermissions = $settingsService->getInstructorPermissions();
        
        return [
            ...parent::share($request),
            'direction' => $direction,
            'locale' => $locale,
            'isLocal' => app()->environment('local'),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'national_id' => $user->national_id,
                    'role' => $user->role,
                    'is_admin' => $user->is_admin ?? false,
                    'is_active' => $user->is_active ?? true,
                ] : null,
                'can' => $user?->getPermissionArray(),
                'roles' => $user ? array_values($user->getRoleNames()->toArray()) : [],
            ],
            'settings' => [
                'instructor_permissions' => $instructorPermissions,
            ],
        ];
    }
}

