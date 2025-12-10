<?php

namespace Database\Seeders;

use Modules\Roles\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin Role - Has all permissions
        $superAdmin = Role::updateOrCreate(
            ['slug' => 'super_admin'],
            [
                'name' => 'super_admin',
                'name_ar' => 'مدير النظام',
                'slug' => 'super_admin',
                'description' => 'Super Administrator with full system access',
                'description_ar' => 'مدير النظام مع وصول كامل للنظام',
                'guard_name' => 'web',
            ]
        );
        $superAdmin->syncPermissions(Permission::all());

        // Admin Role - Has most permissions except super admin specific ones
        $admin = Role::updateOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'admin',
                'name_ar' => 'مدير',
                'slug' => 'admin',
                'description' => 'Administrator with management access',
                'description_ar' => 'مدير مع وصول إداري',
                'guard_name' => 'web',
            ]
        );
        $admin->syncPermissions(Permission::all());

        // Instructor Role - Limited permissions
        $instructor = Role::updateOrCreate(
            ['slug' => 'instructor'],
            [
                'name' => 'instructor',
                'name_ar' => 'مدرب',
                'slug' => 'instructor',
                'description' => 'Instructor who can teach courses',
                'description_ar' => 'مدرب يمكنه تدريس الدورات',
                'guard_name' => 'web',
            ]
        );
        $instructor->syncPermissions(Permission::whereIn('slug', [
            'courses.view',
            'courses.view-all',
            'lessons.view',
            'lessons.create',
            'lessons.edit',
            'lessons.delete',
            'sections.view',
            'sections.create',
            'sections.edit',
            'sections.delete',
            'questions.view',
            'questions.create',
            'questions.edit',
            'questions.delete',
            'batches.view',
            'batches.edit',
            'enrollments.view',
            'attendance.view',
            'attendance.mark',
            'dashboard.instructor',
        ])->get());

        // Student Role - Very limited permissions
        $student = Role::updateOrCreate(
            ['slug' => 'student'],
            [
                'name' => 'student',
                'name_ar' => 'طالب',
                'slug' => 'student',
                'description' => 'Student who can enroll in courses',
                'description_ar' => 'طالب يمكنه التسجيل في الدورات',
                'guard_name' => 'web',
            ]
        );
        $student->syncPermissions(Permission::whereIn('slug', [
            'courses.view',
            'lessons.view',
            'sections.view',
            'questions.view',
            'batches.view',
            'enrollments.view',
            'dashboard.student',
        ])->get());

        $this->command->info('Roles seeded successfully!');
        $this->command->info('Super Admin: ' . $superAdmin->permissions()->count() . ' permissions');
        $this->command->info('Admin: ' . $admin->permissions()->count() . ' permissions');
        $this->command->info('Instructor: ' . $instructor->permissions()->count() . ' permissions');
        $this->command->info('Student: ' . $student->permissions()->count() . ' permissions');
    }
}

