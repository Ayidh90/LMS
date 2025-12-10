<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    public function store(LoginRequest $request)
    {

        // Try to find user by email or national_id
        // Check if national_id column exists first
        $hasNationalIdColumn = \Illuminate\Support\Facades\Schema::hasColumn('users', 'national_id');
        
        if ($hasNationalIdColumn) {
            $user = \App\Models\User::where('email', $request->email)
                ->orWhere('national_id', $request->email)
                ->first();
        } else {
            // Fallback to email only if national_id column doesn't exist yet
            $user = \App\Models\User::where('email', $request->email)->first();
        }

        if (!$user) {
            return back()->withErrors([
                'email' => __('messages.auth.credentials_invalid'),
            ])->onlyInput('email');
        }

        // Password is optional - only verify if provided
        if ($request->filled('password') && $user->password) {
            if (!Hash::check($request->password, $user->password)) {
                return back()->withErrors([
                    'email' => __('messages.auth.credentials_invalid'),
                ])->onlyInput('email');
            }
        }
        // If no password provided, allow login with email only

        // Check if user is active
        if (!$user->is_active) {
            return back()->withErrors([
                'email' => __('messages.auth.account_deactivated'),
            ])->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Preserve locale and direction cookies
        $locale = $request->cookie('locale', 'ar');
        $direction = $request->cookie('direction', $locale === 'ar' ? 'rtl' : 'ltr');

        // Redirect based on role and is_admin flag
        // Admins go to dashboard, students and instructors go to profile
        $redirect = match(true) {
            $user->isAdmin() => redirect()->route('admin.dashboard'),
            $user->isInstructor() => redirect()->route('profile.show'),
            $user->isStudent() => redirect()->route('profile.show'),
            default => redirect()->route('profile.show'),
        };

        return $redirect
            ->cookie('locale', $locale, 60 * 24 * 365)
            ->cookie('direction', $direction, 60 * 24 * 365);
    }

    public function destroy(Request $request)
    {
        // Preserve locale and direction cookies
        $locale = $request->cookie('locale', 'ar');
        $direction = $request->cookie('direction', $locale === 'ar' ? 'rtl' : 'ltr');

        // Logout the user
        Auth::guard('web')->logout();

        // Invalidate the session
        $request->session()->invalidate();
        
        // Regenerate CSRF token
        $request->session()->regenerateToken();

        // Redirect to welcome page with preserved locale/direction
        return redirect()->route('welcome')
            ->cookie('locale', $locale, 60 * 24 * 365)
            ->cookie('direction', $direction, 60 * 24 * 365);
    }
}

