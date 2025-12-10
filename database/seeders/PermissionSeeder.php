<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'Create Courses', 'slug' => 'courses.create', 'description' => 'Can create new courses'],
            ['name' => 'Edit Courses', 'slug' => 'courses.edit', 'description' => 'Can edit courses'],
            ['name' => 'Delete Courses', 'slug' => 'courses.delete', 'description' => 'Can delete courses'],
            ['name' => 'Create Lessons', 'slug' => 'lessons.create', 'description' => 'Can create lessons'],
            ['name' => 'Edit Lessons', 'slug' => 'lessons.edit', 'description' => 'Can edit lessons'],
            ['name' => 'Delete Lessons', 'slug' => 'lessons.delete', 'description' => 'Can delete lessons'],
            ['name' => 'Manage Users', 'slug' => 'users.manage', 'description' => 'Can manage all users'],
            ['name' => 'View All Courses', 'slug' => 'courses.view-all', 'description' => 'Can view all courses'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        // Admin gets all permissions
        $adminPermissions = Permission::all();
        foreach ($adminPermissions as $permission) {
            RolePermission::updateOrCreate(
                [
                    'role' => 'admin',
                    'permission_id' => $permission->id,
                ],
                [
                    'role' => 'admin',
                    'permission_id' => $permission->id,
                ]
            );
        }

        // Instructor permissions
        $instructorPermissions = Permission::whereIn('slug', [
            'courses.create',
            'courses.edit',
            'courses.delete',
            'lessons.create',
            'lessons.edit',
            'lessons.delete',
        ])->get();

        foreach ($instructorPermissions as $permission) {
            RolePermission::updateOrCreate(
                [
                    'role' => 'instructor',
                    'permission_id' => $permission->id,
                ],
                [
                    'role' => 'instructor',
                    'permission_id' => $permission->id,
                ]
            );
        }

        // Student permissions (view only)
        $studentPermissions = Permission::whereIn('slug', [
            'courses.view-all',
        ])->get();

        foreach ($studentPermissions as $permission) {
            RolePermission::updateOrCreate(
                [
                    'role' => 'student',
                    'permission_id' => $permission->id,
                ],
                [
                    'role' => 'student',
                    'permission_id' => $permission->id,
                ]
            );
        }

        $this->command->info('Permissions seeded successfully!');
    }
}

