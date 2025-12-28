<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Http\Requests\Auth\PasswordResetEmailRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetController extends Controller
{
    /**
     * Show the forgot password form
     */
    public function showForgotPasswordForm(): Response
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    /**
     * Handle forgot password request
     * Verify email/national_id and redirect to reset password page
     */
    public function sendResetLink(PasswordResetEmailRequest $request)
    {
        $user = $this->findUserByEmailOrNationalId($request->email);

        if (!$user) {
            return back()->withErrors([
                'email' => __('We can\'t find a user with that email address or national ID.'),
            ])->onlyInput('email');
        }

        // Store email in session and redirect to reset password page
        Session::put('password_reset_email', $request->email);
        Session::put('password_reset_user_id', $user->id);

        return redirect()->route('password.reset', ['email' => $request->email]);
    }

    /**
     * Show the reset password form
     */
    public function showResetForm(Request $request): Response|RedirectResponse
    {
        $email = $request->query('email') ?? Session::get('password_reset_email');

        if (!$email) {
            return redirect()->route('password.forgot')
                ->withErrors(['email' => __('Please enter your email or national ID first.')]);
        }

        return Inertia::render('Auth/ResetPassword', [
            'email' => $email,
        ]);
    }

    /**
     * Handle password reset
     */
    public function reset(PasswordResetRequest $request)
    {
        $email = $request->email ?? Session::get('password_reset_email');
        
        if (!$email) {
            return redirect()->route('password.forgot')
                ->withErrors(['email' => __('Please enter your email or national ID first.')]);
        }

        $user = $this->findUserByEmailOrNationalId($email);

        if (!$user) {
            return back()->withErrors([
                'email' => __('We can\'t find a user with that email address or national ID.'),
            ])->onlyInput('email');
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Clear session
        Session::forget(['password_reset_email', 'password_reset_user_id']);

        // Auto-login the user
        Auth::login($user);
        $request->session()->regenerate();

        // Get selected_role from database, or use default role
        $selectedRole = $user->selected_role ?? $user->role;
        
        // If no selected_role in database, set it to default role
        if (!$user->selected_role) {
            $user->update(['selected_role' => $user->role]);
        }
        
        // Set in session as well for compatibility
        session(['selected_role' => $selectedRole]);

        // Redirect to dashboard
        return $this->redirectToDashboard($user, $request);
    }

    /**
     * Find user by email or national_id
     */
    private function findUserByEmailOrNationalId(string $email): ?User
    {
        if (Schema::hasColumn('users', 'national_id')) {
            return User::where(function ($query) use ($email) {
                $query->where('email', $email)
                    ->orWhere('national_id', $email);
            })->first();
        }

        return User::where('email', $email)->first();
    }

    /**
     * Redirect user to their role-specific dashboard
     */
    private function redirectToDashboard(User $user, Request $request)
    {
        $locale = $request->cookie('locale', 'ar');
        $direction = $request->cookie('direction', $locale === 'ar' ? 'rtl' : 'ltr');

        // Get available roles for selection first
        $user->refresh();
        $availableRoles = $user->getAvailableRolesForSelection();
        
        // If user has multiple roles, show role selection page
        if (count($availableRoles) > 1) {
            return redirect()->route('role-selection')
                ->cookie('locale', $locale, 60 * 24 * 365)
                ->cookie('direction', $direction, 60 * 24 * 365);
        }

        // Single role - use user's default role and save to database
        $selectedRole = $user->role;
        $user->update(['selected_role' => $selectedRole]);
        session(['selected_role' => $selectedRole]);
        
        $route = $this->getRouteForRole($selectedRole);
        return redirect()->route($route)
            ->cookie('locale', $locale, 60 * 24 * 365)
            ->cookie('direction', $direction, 60 * 24 * 365);
    }

    /**
     * Get route name for role
     */
    private function getRouteForRole(string $roleSlug): string
    {
        return match ($roleSlug) {
            'admin', 'super_admin' => 'admin.dashboard',
            'instructor' => 'instructor.dashboard',
            'student' => 'student.dashboard',
            default => 'profile.show',
        };
    }
}

