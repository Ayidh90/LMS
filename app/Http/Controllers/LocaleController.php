<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class LocaleController extends Controller
{
    public function setLocale(Request $request)
    {
        // Default to Arabic if no locale is provided
        $locale = $request->input('locale', 'ar');
        
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'ar'; // Fallback to Arabic
        }

        $direction = $locale === 'ar' ? 'rtl' : 'ltr';

        // Set locale in app
        app()->setLocale($locale);

        // Redirect back with cookies
        return redirect()->back()
            ->withCookie(cookie('locale', $locale, 60 * 24 * 365, '/', null, false, false))
            ->withCookie(cookie('direction', $direction, 60 * 24 * 365, '/', null, false, false));
    }
}

