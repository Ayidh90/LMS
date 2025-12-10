<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    public function store(RegisterRequest $request)
    {
        $validated = $request->validated();
        // Note: 'role' is intentionally NOT validated - it's always set to 'student'

        // Auto-assign student role for new registrations - ignore any role from request
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'national_id' => $validated['national_id'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'student', // Always assign student role on registration - cannot be changed
            'is_admin' => false, // Never admin on registration
            'is_active' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on role and is_admin flag
        // Only admins get dashboard, students and instructors go to welcome
        return match(true) {
            $user->isAdmin() => redirect()->route('admin.dashboard'),
            default => redirect()->route('welcome'),
        };
    }
}

