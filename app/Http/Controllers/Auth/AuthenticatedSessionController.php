<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Roles\Models\Role;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login form
     * Handles auto-login for role switching
     */
    public function create(Request $request): Response
    {
        if ($this->shouldAutoLogin($request)) {
            $result = $this->attemptAutoLogin($request);
            if ($result) {
                return $result;
            }
        }

        return Inertia::render('Auth/Login');
    }

    /**
     * Handle a login request
     */
    public function store(LoginRequest $request)
    {
        $user = $this->findOrCreateUser($request);

        if (!$user || !$this->validatePassword($request, $user)) {
            return $this->invalidCredentialsResponse();
        }

        $user->refresh();

        if (!$user->is_active) {
            return back()->withErrors([
                'email' => __('messages.auth.account_deactivated'),
            ])->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Get selected_role from database, or use default role
        $selectedRole = $user->selected_role ?? $user->role;
        
        // If no selected_role in database, set it to default role
        if (!$user->selected_role) {
            $user->update(['selected_role' => $user->role]);
        }
        
        // Set in session as well for compatibility
        session(['selected_role' => $selectedRole]);

        return $this->redirectToDashboard($user, $request);
    }

    public function destroy(Request $request)
    {
        $locale = $request->cookie('locale', 'ar');
        $direction = $request->cookie('direction', $locale === 'ar' ? 'rtl' : 'ltr');

        // Clear selected_role in database before logout
        /** @var User|null $user */
        $user = Auth::user();
        if ($user instanceof User) {
            $user->update(['selected_role' => null]);
        }

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
     * Check if auto-login should be attempted (role switching)
     */
    private function shouldAutoLogin(Request $request): bool
    {
        return $request->session()->has('switch_to_role') 
            && $request->session()->has('switch_user_email');
    }

    /**
     * Attempt auto-login for role switching
     */
    private function attemptAutoLogin(Request $request)
    {
        $switchToRole = $request->session()->get('switch_to_role');
        $switchUserEmail = $request->session()->get('switch_user_email');

        $user = User::where('email', $switchUserEmail)->first();

        if (!$user || !$user->is_active) {
            $this->clearSwitchSession($request);
            return null;
        }

        if (!$this->userHasRole($user, $switchToRole)) {
            $this->clearSwitchSession($request);
            return null;
        }

        if (!$this->validateAdminAccess($user, $switchToRole, $request)) {
            return Inertia::render('Auth/Login', [
                'errors' => ['email' => __('You do not have permission to access admin dashboard.')]
            ]);
        }

        $this->performAutoLogin($user, $switchToRole, $request);
        
        // After auto-login from role switching, redirect directly to dashboard
        // Don't show role selection page even if user has multiple roles
        [$locale, $direction] = $this->getLocaleAndDirection($request);
        $route = $this->getRouteForRole($switchToRole);
        
        // Use Inertia::location() with full URL to force GET request
        // This prevents Inertia from trying to resend POST request
        $routeUrl = URL::route($route);
        
        return Inertia::location($routeUrl);
    }

    /**
     * Check if user has the specified role
     */
    private function userHasRole(User $user, string $roleSlug): bool
    {
        return $user->hasRoleBySlug($roleSlug) || $user->role === $roleSlug;
    }

    /**
     * Validate admin access for role switching
     */
    private function validateAdminAccess(User $user, string $roleSlug, Request $request): bool
    {
        if (!in_array($roleSlug, ['admin', 'super_admin'])) {
            return true; // Non-admin roles don't need validation
        }

        if ($roleSlug === 'super_admin') {
            return true; // Super admin always allowed
        }

        // Regular admin needs both user->is_admin == 1 and role->is_admin == 1
        if ($user->is_admin != 1) {
            $this->clearSwitchSession($request);
            return false;
        }

        $role = $this->findAdminRole($user);
        if (!$role || $role->is_admin != 1) {
            $this->clearSwitchSession($request);
            return false;
        }

        return true;
    }

    /**
     * Find admin role for user
     */
    private function findAdminRole(User $user): ?Role
    {
        $role = $user->roles()
            ->where(function($query) {
                $query->where('slug', 'admin')->orWhere('name', 'admin');
            })
            ->first();

        if (!$role && $user->role === 'admin') {
            $role = Role::where('slug', 'admin')
                ->orWhere('name', 'admin')
                ->first();
        }

        return $role;
    }

    /**
     * Perform auto-login and set session data
     */
    private function performAutoLogin(User $user, string $roleSlug, Request $request): void
    {
        Auth::login($user, true);
        $request->session()->regenerate();
        
        // Update selected_role in database
        $user->update(['selected_role' => $roleSlug]);
        
        // Set in session as well
        session(['selected_role' => $roleSlug]);
        $this->clearSwitchSession($request);
    }

    /**
     * Clear role switching session data
     */
    private function clearSwitchSession(Request $request): void
    {
        $request->session()->forget(['switch_to_role', 'switch_user_email']);
    }

    /**
     * Get locale and direction from cookies
     */
    private function getLocaleAndDirection(Request $request): array
    {
        $locale = $request->cookie('locale', 'ar');
        $direction = $request->cookie('direction', $locale === 'ar' ? 'rtl' : 'ltr');
        
        return [$locale, $direction];
    }

    /**
     * Add locale and direction cookies to response
     */
    private function withLocaleCookies($response, string $locale, string $direction)
    {
        return $response
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

    /**
     * Get route name for user based on their role
     */
    private function getRouteForUser(User $user): string
    {
        return match (true) {
            $user->isSuperAdmin() => 'admin.dashboard',
            $user->isAdmin() && $user->is_admin == 1 => 'admin.dashboard',
            $user->isInstructor() => 'instructor.dashboard',
            $user->isStudent() => 'student.dashboard',
            default => 'profile.show',
        };
    }

    /**
     * Redirect user to their role-specific dashboard
     * If user has multiple roles, show role selection page
     * Otherwise, go directly to dashboard based on selected/default role
     */
    private function redirectToDashboard(User $user, Request $request)
    {
        [$locale, $direction] = $this->getLocaleAndDirection($request);

        // Get available roles for selection first
        $user->refresh(); // Refresh to get latest data
        $availableRoles = $user->getAvailableRolesForSelection();
        
        // If user has multiple roles, always show role selection page
        // Even if they have selected_role saved (to allow switching)
        if (count($availableRoles) > 1) {
            return $this->withLocaleCookies(
                redirect()->route('role-selection'),
                $locale,
                $direction
            );
        }

        // Single role - use user's default role and save to database
        $selectedRole = $user->role;
        $user->update(['selected_role' => $selectedRole]);
        session(['selected_role' => $selectedRole]);
        
        $route = $this->getRouteForRole($selectedRole);
        return $this->withLocaleCookies(
            redirect()->route($route),
            $locale,
            $direction
        );
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

