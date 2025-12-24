<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    public function store(LoginRequest $request)
    {
        $user = $this->findOrCreateUser($request);

        if (!$user) {
            return $this->invalidCredentialsResponse();
        }

        if (!$this->validatePassword($request, $user)) {
            return $this->invalidCredentialsResponse();
        }

        // Refresh user to get latest data after potential updates
        $user->refresh();

        if (!$user->is_active) {
            return back()->withErrors([
                'email' => __('messages.auth.account_deactivated'),
            ])->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return $this->redirectToDashboard($user, $request);
    }

    public function destroy(Request $request)
    {
        $locale = $request->cookie('locale', 'ar');
        $direction = $request->cookie('direction', $locale === 'ar' ? 'rtl' : 'ltr');

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome')
            ->cookie('locale', $locale, 60 * 24 * 365)
            ->cookie('direction', $direction, 60 * 24 * 365);
    }

    /**
     * Find user by email or national_id, or create if in development
     */
    private function findOrCreateUser(LoginRequest $request): ?User
    {
        $user = $this->findUserByEmailOrNationalId($request->email);

        if ($user) {
            return $user;
        }

        // Auto-create user in development environments
        if ($this->shouldAutoCreateUser($request->email)) {
            return $this->createStudentUser($request);
        }

        return null;
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
     * Check if user should be auto-created (development only)
     */
    private function shouldAutoCreateUser(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Allow auto-creation in local, testing, or when debug is enabled
        return app()->environment(['local', 'testing']) 
            || config('app.debug', false) === true;
    }

    /**
     * Create a new student user
     */
    private function createStudentUser(LoginRequest $request): ?User
    {
        try {
            $emailParts = explode('@', $request->email);
            $name = ucfirst(str_replace(['.', '_', '-'], ' ', $emailParts[0]));
            
            $user = User::create([
                'name' => $name,
                'email' => $request->email,
                'password' => $request->filled('password')
                    ? Hash::make($request->password)
                    : Hash::make('password'),
                'role' => 'student',
                'is_admin' => false,
                'is_active' => true,
            ]);

            if (method_exists($user, 'assignRole')) {
                try {
                    $user->assignRole('student');
                } catch (\Exception $e) {
                    // Role assignment failed, but user is created - continue
                }
            }

            return $user;
        } catch (\Exception $e) {
            // If creation fails (e.g., duplicate), try to find existing user
            return User::where('email', $request->email)->first();
        }
    }

    /**
     * Validate user password
     */
    private function validatePassword(LoginRequest $request, User $user): bool
    {
        // Password is optional - allow login without password
        if (!$request->filled('password')) {
            return true;
        }

        // If user has no password set, accept any password in development
        if (!$user->password) {
            if ($this->isDevelopment()) {
                // Set password from request in development
                $user->update(['password' => Hash::make($request->password)]);
            }
            return true;
        }

        // Verify password matches
        if (Hash::check($request->password, $user->password)) {
            return true;
        }

        // In development, allow 'password' as default and update it
        if ($this->isDevelopment() && $request->password === 'password') {
            $user->update(['password' => Hash::make('password')]);
            return true;
        }

        return false;
    }

    /**
     * Check if running in development environment
     */
    private function isDevelopment(): bool
    {
        return app()->environment(['local', 'testing']) || config('app.debug');
    }

    /**
     * Redirect user to their role-specific dashboard
     */
    private function redirectToDashboard(User $user, Request $request)
    {
        $locale = $request->cookie('locale', 'ar');
        $direction = $request->cookie('direction', $locale === 'ar' ? 'rtl' : 'ltr');

        $route = match (true) {
            $user->isAdmin() => 'admin.dashboard',
            $user->isInstructor() => 'instructor.dashboard',
            $user->isStudent() => 'student.dashboard',
            default => 'profile.show',
        };

        return redirect()->route($route)
            ->cookie('locale', $locale, 60 * 24 * 365)
            ->cookie('direction', $direction, 60 * 24 * 365);
    }

    /**
     * Return invalid credentials error response
     */
    private function invalidCredentialsResponse()
    {
        return back()->withErrors([
            'email' => __('messages.auth.credentials_invalid'),
        ])->onlyInput('email');
    }
}

