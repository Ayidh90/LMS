<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@lms.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'is_admin' => true,
                'is_active' => true,
            ]
        );

        // Create Admin
        User::updateOrCreate(
            ['email' => 'admin@lms.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_admin' => true,
                'is_active' => true,
            ]
        );

        // Create Instructor
        User::updateOrCreate(
            ['email' => 'instructor@lms.com'],
            [
                'name' => 'Instructor User',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'is_admin' => false,
                'is_active' => true,
            ]
        );

        // Create Student
        User::updateOrCreate(
            ['email' => 'student@lms.com'],
            [
                'name' => 'Student User',
                'password' => Hash::make('password'),
                'role' => 'student',
                'is_admin' => false,
                'is_active' => true,
            ]
        );

        $this->command->info('Default users created!');
        $this->command->info('Super Admin: superadmin@lms.com (no password)');
        $this->command->info('Admin: admin@lms.com / password');
        $this->command->info('Instructor: instructor@lms.com / password');
        $this->command->info('Student: student@lms.com / password');

        // Seed permissions first
        $this->call(PermissionSeeder::class);
        
        // Seed roles (after permissions)
        $this->call(RoleSeeder::class);
        
        // Assign roles to users
        $superAdminUser = User::where('email', 'superadmin@lms.com')->first();
        if ($superAdminUser) {
            $superAdminUser->assignRole('super_admin');
        }
        
        $adminUser = User::where('email', 'admin@lms.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('admin');
        }
        
        $instructorUser = User::where('email', 'instructor@lms.com')->first();
        if ($instructorUser) {
            $instructorUser->assignRole('instructor');
        }
        
        $studentUser = User::where('email', 'student@lms.com')->first();
        if ($studentUser) {
            $studentUser->assignRole('student');
        }
        
        // Seed categories and FAQs
        $this->call(FAQSeeder::class);
        
        // Seed courses (must be after categories)
        $this->call(CourseSeeder::class);
        
        // Seed courses with lessons, questions, and answers
        $this->call(CourseWithLessonsSeeder::class);
        
        // Seed programs, tracks, and courses
        $this->call(ProgramTrackSeeder::class);
    }
}

