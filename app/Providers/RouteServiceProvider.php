<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
        
        // Prevent Laravel from trying to resolve 'role' as a route model binding
        Route::bind('role', function ($value) {
            return $value; // Return as-is, don't try to resolve as model
        });
        
        // Configure route model binding for Course - try ID first, then slug
        Route::bind('course', function ($value) {
            try {
                // Try to find by ID first (if numeric)
                if (is_numeric($value)) {
                    $course = \Modules\Courses\Models\Course::find($value);
                    if ($course) {
                        return $course;
                    }
                }
                
                // Try to find by slug
                $course = \Modules\Courses\Models\Course::where('slug', $value)->first();
                
                if (!$course) {
                    // Last attempt: try ID again (in case value is string but represents ID)
                    $course = \Modules\Courses\Models\Course::find($value);
                }
                
                if (!$course) {
                    abort(404, 'Course not found');
                }
                
                return $course;
            } catch (\Exception $e) {
                \Log::error('Course route binding error: ' . $e->getMessage());
                abort(404, 'Course not found');
            }
        });
    }
}

