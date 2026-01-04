<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // For Inertia requests, always redirect to login (don't return JSON)
        if ($request->header('X-Inertia')) {
            return route('login');
        }
        
        // For regular JSON API requests, return null to get JSON response
        return $request->expectsJson() ? null : route('login');
    }
}

