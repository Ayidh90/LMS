<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class FixStudentUser extends Command
{
    protected $signature = 'user:fix-student {email}';
    protected $description = 'Create or fix a student user account';

    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if ($user) {
            $this->info("User found: {$user->name} ({$user->email})");
            
            // Update user to ensure it's a student and active
            $user->update([
                'role' => 'student',
                'is_admin' => false,
                'is_active' => true,
            ]);
            
            // Assign student role using Spatie if available
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('student');
            }
            
            $this->info("User updated successfully!");
            $this->info("Role: {$user->role}");
            $this->info("Active: " . ($user->is_active ? 'Yes' : 'No'));
        } else {
            $this->info("User not found. Creating new student user...");
            
            $user = User::create([
                'name' => 'Student User',
                'email' => $email,
                'password' => Hash::make('password'), // Default password
                'role' => 'student',
                'is_admin' => false,
                'is_active' => true,
            ]);
            
            // Assign student role using Spatie if available
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('student');
            }
            
            $this->info("User created successfully!");
            $this->info("Email: {$user->email}");
            $this->info("Password: password (default)");
        }
        
        return Command::SUCCESS;
    }
}

