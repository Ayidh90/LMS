<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Course Permissions
            ['name' => 'view courses', 'slug' => 'courses.view', 'description' => 'Can view courses'],
            ['name' => 'view all courses', 'slug' => 'courses.view-all', 'description' => 'Can view all courses including unpublished'],
            ['name' => 'create courses', 'slug' => 'courses.create', 'description' => 'Can create new courses'],
            ['name' => 'edit courses', 'slug' => 'courses.edit', 'description' => 'Can edit courses'],
            ['name' => 'delete courses', 'slug' => 'courses.delete', 'description' => 'Can delete courses'],
            ['name' => 'publish courses', 'slug' => 'courses.publish', 'description' => 'Can publish/unpublish courses'],
            
            // Lesson Permissions
            ['name' => 'view lessons', 'slug' => 'lessons.view', 'description' => 'Can view lessons'],
            ['name' => 'create lessons', 'slug' => 'lessons.create', 'description' => 'Can create lessons'],
            ['name' => 'edit lessons', 'slug' => 'lessons.edit', 'description' => 'Can edit lessons'],
            ['name' => 'delete lessons', 'slug' => 'lessons.delete', 'description' => 'Can delete lessons'],
            
            // Section Permissions
            ['name' => 'view sections', 'slug' => 'sections.view', 'description' => 'Can view sections'],
            ['name' => 'create sections', 'slug' => 'sections.create', 'description' => 'Can create sections'],
            ['name' => 'edit sections', 'slug' => 'sections.edit', 'description' => 'Can edit sections'],
            ['name' => 'delete sections', 'slug' => 'sections.delete', 'description' => 'Can delete sections'],
            
            // Question Permissions
            ['name' => 'view questions', 'slug' => 'questions.view', 'description' => 'Can view questions'],
            ['name' => 'create questions', 'slug' => 'questions.create', 'description' => 'Can create questions'],
            ['name' => 'edit questions', 'slug' => 'questions.edit', 'description' => 'Can edit questions'],
            ['name' => 'delete questions', 'slug' => 'questions.delete', 'description' => 'Can delete questions'],
            
            // Batch Permissions
            ['name' => 'view batches', 'slug' => 'batches.view', 'description' => 'Can view batches'],
            ['name' => 'create batches', 'slug' => 'batches.create', 'description' => 'Can create batches'],
            ['name' => 'edit batches', 'slug' => 'batches.edit', 'description' => 'Can edit batches'],
            ['name' => 'delete batches', 'slug' => 'batches.delete', 'description' => 'Can delete batches'],
            ['name' => 'manage batches', 'slug' => 'batches.manage', 'description' => 'Can manage batches'],
            
            // Enrollment Permissions
            ['name' => 'view enrollments', 'slug' => 'enrollments.view', 'description' => 'Can view enrollments'],
            ['name' => 'create enrollments', 'slug' => 'enrollments.create', 'description' => 'Can create enrollments'],
            ['name' => 'edit enrollments', 'slug' => 'enrollments.edit', 'description' => 'Can edit enrollments'],
            ['name' => 'delete enrollments', 'slug' => 'enrollments.delete', 'description' => 'Can delete enrollments'],
            
            // User Permissions
            ['name' => 'view users', 'slug' => 'users.view', 'description' => 'Can view users'],
            ['name' => 'create users', 'slug' => 'users.create', 'description' => 'Can create users'],
            ['name' => 'edit users', 'slug' => 'users.edit', 'description' => 'Can edit users'],
            ['name' => 'delete users', 'slug' => 'users.delete', 'description' => 'Can delete users'],
            ['name' => 'manage users', 'slug' => 'users.manage', 'description' => 'Can manage all users'],
            
            // Role & Permission Permissions
            ['name' => 'view roles', 'slug' => 'roles.view', 'description' => 'Can view roles'],
            ['name' => 'create roles', 'slug' => 'roles.create', 'description' => 'Can create roles'],
            ['name' => 'edit roles', 'slug' => 'roles.edit', 'description' => 'Can edit roles'],
            ['name' => 'delete roles', 'slug' => 'roles.delete', 'description' => 'Can delete roles'],
            ['name' => 'manage roles', 'slug' => 'roles.manage', 'description' => 'Can manage roles'],
            ['name' => 'view permissions', 'slug' => 'permissions.view', 'description' => 'Can view permissions'],
            ['name' => 'manage permissions', 'slug' => 'permissions.manage', 'description' => 'Can manage permissions'],
            
            // Category Permissions
            ['name' => 'view categories', 'slug' => 'categories.view', 'description' => 'Can view categories'],
            ['name' => 'create categories', 'slug' => 'categories.create', 'description' => 'Can create categories'],
            ['name' => 'edit categories', 'slug' => 'categories.edit', 'description' => 'Can edit categories'],
            ['name' => 'delete categories', 'slug' => 'categories.delete', 'description' => 'Can delete categories'],
            
            // Settings Permissions
            ['name' => 'view settings', 'slug' => 'settings.view', 'description' => 'Can view settings'],
            ['name' => 'edit settings', 'slug' => 'settings.edit', 'description' => 'Can edit settings'],
            ['name' => 'manage settings', 'slug' => 'settings.manage', 'description' => 'Can manage settings'],
            
            // Dashboard Permissions
            ['name' => 'view admin dashboard', 'slug' => 'dashboard.admin', 'description' => 'Can view admin dashboard'],
            ['name' => 'view instructor dashboard', 'slug' => 'dashboard.instructor', 'description' => 'Can view instructor dashboard'],
            ['name' => 'view student dashboard', 'slug' => 'dashboard.student', 'description' => 'Can view student dashboard'],
            
            // Attendance Permissions
            ['name' => 'view attendance', 'slug' => 'attendance.view', 'description' => 'Can view attendance'],
            ['name' => 'mark attendance', 'slug' => 'attendance.mark', 'description' => 'Can mark attendance'],
            ['name' => 'manage attendance', 'slug' => 'attendance.manage', 'description' => 'Can manage attendance'],
            
            // FAQ Permissions
            ['name' => 'view faqs', 'slug' => 'faqs.view', 'description' => 'Can view FAQs'],
            ['name' => 'create faqs', 'slug' => 'faqs.create', 'description' => 'Can create FAQs'],
            ['name' => 'edit faqs', 'slug' => 'faqs.edit', 'description' => 'Can edit FAQs'],
            ['name' => 'delete faqs', 'slug' => 'faqs.delete', 'description' => 'Can delete FAQs'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                [
                    'name' => $permission['name'],
                    'slug' => $permission['slug'],
                    'description' => $permission['description'],
                    'guard_name' => 'web',
                ]
            );
        }

        $this->command->info('Permissions seeded successfully!');
    }
}
