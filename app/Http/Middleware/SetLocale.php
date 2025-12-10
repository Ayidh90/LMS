<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Default to Arabic if no locale cookie is set
        $locale = $request->cookie('locale');
        
        if (!$locale || !in_array($locale, ['en', 'ar'])) {
            $locale = 'ar'; // Default to Arabic
        }
        
        // Set locale in application
        app()->setLocale($locale);
        
        // Set locale in session for validation messages
        $request->session()->put('locale', $locale);

        return $next($request);
    }
}

