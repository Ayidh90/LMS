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
            ['name' => 'view courses', 'name_ar' => 'عرض الدورات', 'slug' => 'courses.view', 'description' => 'Can view courses', 'description_ar' => 'يمكن عرض الدورات'],
            ['name' => 'view all courses', 'name_ar' => 'عرض جميع الدورات', 'slug' => 'courses.view-all', 'description' => 'Can view all courses including unpublished', 'description_ar' => 'يمكن عرض جميع الدورات بما في ذلك غير المنشورة'],
            ['name' => 'create courses', 'name_ar' => 'إنشاء الدورات', 'slug' => 'courses.create', 'description' => 'Can create new courses', 'description_ar' => 'يمكن إنشاء دورات جديدة'],
            ['name' => 'edit courses', 'name_ar' => 'تعديل الدورات', 'slug' => 'courses.edit', 'description' => 'Can edit courses', 'description_ar' => 'يمكن تعديل الدورات'],
            ['name' => 'delete courses', 'name_ar' => 'حذف الدورات', 'slug' => 'courses.delete', 'description' => 'Can delete courses', 'description_ar' => 'يمكن حذف الدورات'],
            ['name' => 'publish courses', 'name_ar' => 'نشر الدورات', 'slug' => 'courses.publish', 'description' => 'Can publish/unpublish courses', 'description_ar' => 'يمكن نشر/إلغاء نشر الدورات'],
            
            // Lesson Permissions
            ['name' => 'view lessons', 'name_ar' => 'عرض الدروس', 'slug' => 'lessons.view', 'description' => 'Can view lessons', 'description_ar' => 'يمكن عرض الدروس'],
            ['name' => 'create lessons', 'name_ar' => 'إنشاء الدروس', 'slug' => 'lessons.create', 'description' => 'Can create lessons', 'description_ar' => 'يمكن إنشاء الدروس'],
            ['name' => 'edit lessons', 'name_ar' => 'تعديل الدروس', 'slug' => 'lessons.edit', 'description' => 'Can edit lessons', 'description_ar' => 'يمكن تعديل الدروس'],
            ['name' => 'delete lessons', 'name_ar' => 'حذف الدروس', 'slug' => 'lessons.delete', 'description' => 'Can delete lessons', 'description_ar' => 'يمكن حذف الدروس'],
            
            // Section Permissions
            ['name' => 'view sections', 'name_ar' => 'عرض الأقسام', 'slug' => 'sections.view', 'description' => 'Can view sections', 'description_ar' => 'يمكن عرض الأقسام'],
            ['name' => 'create sections', 'name_ar' => 'إنشاء الأقسام', 'slug' => 'sections.create', 'description' => 'Can create sections', 'description_ar' => 'يمكن إنشاء الأقسام'],
            ['name' => 'edit sections', 'name_ar' => 'تعديل الأقسام', 'slug' => 'sections.edit', 'description' => 'Can edit sections', 'description_ar' => 'يمكن تعديل الأقسام'],
            ['name' => 'delete sections', 'name_ar' => 'حذف الأقسام', 'slug' => 'sections.delete', 'description' => 'Can delete sections', 'description_ar' => 'يمكن حذف الأقسام'],
            
            // Question Permissions
            ['name' => 'view questions', 'name_ar' => 'عرض الأسئلة', 'slug' => 'questions.view', 'description' => 'Can view questions', 'description_ar' => 'يمكن عرض الأسئلة'],
            ['name' => 'create questions', 'name_ar' => 'إنشاء الأسئلة', 'slug' => 'questions.create', 'description' => 'Can create questions', 'description_ar' => 'يمكن إنشاء الأسئلة'],
            ['name' => 'edit questions', 'name_ar' => 'تعديل الأسئلة', 'slug' => 'questions.edit', 'description' => 'Can edit questions', 'description_ar' => 'يمكن تعديل الأسئلة'],
            ['name' => 'delete questions', 'name_ar' => 'حذف الأسئلة', 'slug' => 'questions.delete', 'description' => 'Can delete questions', 'description_ar' => 'يمكن حذف الأسئلة'],
            
            // Batch Permissions
            ['name' => 'view batches', 'name_ar' => 'عرض الدفعات', 'slug' => 'batches.view', 'description' => 'Can view batches', 'description_ar' => 'يمكن عرض الدفعات'],
            ['name' => 'create batches', 'name_ar' => 'إنشاء الدفعات', 'slug' => 'batches.create', 'description' => 'Can create batches', 'description_ar' => 'يمكن إنشاء الدفعات'],
            ['name' => 'edit batches', 'name_ar' => 'تعديل الدفعات', 'slug' => 'batches.edit', 'description' => 'Can edit batches', 'description_ar' => 'يمكن تعديل الدفعات'],
            ['name' => 'delete batches', 'name_ar' => 'حذف الدفعات', 'slug' => 'batches.delete', 'description' => 'Can delete batches', 'description_ar' => 'يمكن حذف الدفعات'],
            ['name' => 'add students to batches', 'name_ar' => 'إضافة طلاب للدفعات', 'slug' => 'batches.add-students', 'description' => 'Can add students to batches', 'description_ar' => 'يمكن إضافة طلاب للدفعات'],
            ['name' => 'remove students from batches', 'name_ar' => 'إزالة طلاب من الدفعات', 'slug' => 'batches.remove-students', 'description' => 'Can remove students from batches', 'description_ar' => 'يمكن إزالة طلاب من الدفعات'],
            ['name' => 'manage batches', 'name_ar' => 'إدارة الدفعات', 'slug' => 'batches.manage', 'description' => 'Can manage batches', 'description_ar' => 'يمكن إدارة الدفعات'],
            
            // Enrollment Permissions
            ['name' => 'view enrollments', 'name_ar' => 'عرض التسجيلات', 'slug' => 'enrollments.view', 'description' => 'Can view enrollments', 'description_ar' => 'يمكن عرض التسجيلات'],
            ['name' => 'create enrollments', 'name_ar' => 'إنشاء التسجيلات', 'slug' => 'enrollments.create', 'description' => 'Can create enrollments', 'description_ar' => 'يمكن إنشاء التسجيلات'],
            ['name' => 'edit enrollments', 'name_ar' => 'تعديل التسجيلات', 'slug' => 'enrollments.edit', 'description' => 'Can edit enrollments', 'description_ar' => 'يمكن تعديل التسجيلات'],
            ['name' => 'delete enrollments', 'name_ar' => 'حذف التسجيلات', 'slug' => 'enrollments.delete', 'description' => 'Can delete enrollments', 'description_ar' => 'يمكن حذف التسجيلات'],
            
            // User Permissions
            ['name' => 'view users', 'name_ar' => 'عرض المستخدمين', 'slug' => 'users.view', 'description' => 'Can view users', 'description_ar' => 'يمكن عرض المستخدمين'],
            ['name' => 'create users', 'name_ar' => 'إنشاء المستخدمين', 'slug' => 'users.create', 'description' => 'Can create users', 'description_ar' => 'يمكن إنشاء المستخدمين'],
            ['name' => 'edit users', 'name_ar' => 'تعديل المستخدمين', 'slug' => 'users.edit', 'description' => 'Can edit users', 'description_ar' => 'يمكن تعديل المستخدمين'],
            ['name' => 'delete users', 'name_ar' => 'حذف المستخدمين', 'slug' => 'users.delete', 'description' => 'Can delete users', 'description_ar' => 'يمكن حذف المستخدمين'],
            ['name' => 'manage users', 'name_ar' => 'إدارة المستخدمين', 'slug' => 'users.manage', 'description' => 'Can manage all users', 'description_ar' => 'يمكن إدارة جميع المستخدمين'],
            
            // Role & Permission Permissions
            ['name' => 'view roles', 'name_ar' => 'عرض الأدوار', 'slug' => 'roles.view', 'description' => 'Can view roles', 'description_ar' => 'يمكن عرض الأدوار'],
            ['name' => 'create roles', 'name_ar' => 'إنشاء الأدوار', 'slug' => 'roles.create', 'description' => 'Can create roles', 'description_ar' => 'يمكن إنشاء الأدوار'],
            ['name' => 'edit roles', 'name_ar' => 'تعديل الأدوار', 'slug' => 'roles.edit', 'description' => 'Can edit roles', 'description_ar' => 'يمكن تعديل الأدوار'],
            ['name' => 'delete roles', 'name_ar' => 'حذف الأدوار', 'slug' => 'roles.delete', 'description' => 'Can delete roles', 'description_ar' => 'يمكن حذف الأدوار'],
            ['name' => 'manage roles', 'name_ar' => 'إدارة الأدوار', 'slug' => 'roles.manage', 'description' => 'Can manage roles', 'description_ar' => 'يمكن إدارة الأدوار'],
            ['name' => 'view permissions', 'name_ar' => 'عرض الصلاحيات', 'slug' => 'permissions.view', 'description' => 'Can view permissions', 'description_ar' => 'يمكن عرض الصلاحيات'],
            ['name' => 'manage permissions', 'name_ar' => 'إدارة الصلاحيات', 'slug' => 'permissions.manage', 'description' => 'Can manage permissions', 'description_ar' => 'يمكن إدارة الصلاحيات'],
            
            // Settings Permissions
            ['name' => 'view settings', 'name_ar' => 'عرض الإعدادات', 'slug' => 'settings.view', 'description' => 'Can view settings', 'description_ar' => 'يمكن عرض الإعدادات'],
            ['name' => 'edit settings', 'name_ar' => 'تعديل الإعدادات', 'slug' => 'settings.edit', 'description' => 'Can edit settings', 'description_ar' => 'يمكن تعديل الإعدادات'],
            ['name' => 'manage settings', 'name_ar' => 'إدارة الإعدادات', 'slug' => 'settings.manage', 'description' => 'Can manage settings', 'description_ar' => 'يمكن إدارة الإعدادات'],
            
            // Dashboard Permissions
            ['name' => 'view admin dashboard', 'name_ar' => 'عرض لوحة تحكم المدير', 'slug' => 'dashboard.admin', 'description' => 'Can view admin dashboard', 'description_ar' => 'يمكن عرض لوحة تحكم المدير'],
            ['name' => 'view instructor dashboard', 'name_ar' => 'عرض لوحة تحكم المدرب', 'slug' => 'dashboard.instructor', 'description' => 'Can view instructor dashboard', 'description_ar' => 'يمكن عرض لوحة تحكم المدرب'],
            ['name' => 'view student dashboard', 'name_ar' => 'عرض لوحة تحكم الطالب', 'slug' => 'dashboard.student', 'description' => 'Can view student dashboard', 'description_ar' => 'يمكن عرض لوحة تحكم الطالب'],
            
            // Attendance Permissions
            ['name' => 'view attendance', 'name_ar' => 'عرض الحضور', 'slug' => 'attendance.view', 'description' => 'Can view attendance', 'description_ar' => 'يمكن عرض الحضور'],
            ['name' => 'mark attendance', 'name_ar' => 'تسجيل الحضور', 'slug' => 'attendance.mark', 'description' => 'Can mark attendance', 'description_ar' => 'يمكن تسجيل الحضور'],
            ['name' => 'manage attendance', 'name_ar' => 'إدارة الحضور', 'slug' => 'attendance.manage', 'description' => 'Can manage attendance', 'description_ar' => 'يمكن إدارة الحضور'],
            
            // Program Permissions
            ['name' => 'view programs', 'name_ar' => 'عرض البرامج', 'slug' => 'programs.view', 'description' => 'Can view programs', 'description_ar' => 'يمكن عرض البرامج'],
            ['name' => 'create programs', 'name_ar' => 'إنشاء البرامج', 'slug' => 'programs.create', 'description' => 'Can create programs', 'description_ar' => 'يمكن إنشاء البرامج'],
            ['name' => 'edit programs', 'name_ar' => 'تعديل البرامج', 'slug' => 'programs.edit', 'description' => 'Can edit programs', 'description_ar' => 'يمكن تعديل البرامج'],
            ['name' => 'delete programs', 'name_ar' => 'حذف البرامج', 'slug' => 'programs.delete', 'description' => 'Can delete programs', 'description_ar' => 'يمكن حذف البرامج'],
            ['name' => 'manage programs', 'name_ar' => 'إدارة البرامج', 'slug' => 'programs.manage', 'description' => 'Can manage programs', 'description_ar' => 'يمكن إدارة البرامج'],
            
            // Track Permissions
            ['name' => 'view tracks', 'name_ar' => 'عرض المسارات', 'slug' => 'tracks.view', 'description' => 'Can view tracks', 'description_ar' => 'يمكن عرض المسارات'],
            ['name' => 'create tracks', 'name_ar' => 'إنشاء المسارات', 'slug' => 'tracks.create', 'description' => 'Can create tracks', 'description_ar' => 'يمكن إنشاء المسارات'],
            ['name' => 'edit tracks', 'name_ar' => 'تعديل المسارات', 'slug' => 'tracks.edit', 'description' => 'Can edit tracks', 'description_ar' => 'يمكن تعديل المسارات'],
            ['name' => 'delete tracks', 'name_ar' => 'حذف المسارات', 'slug' => 'tracks.delete', 'description' => 'Can delete tracks', 'description_ar' => 'يمكن حذف المسارات'],
            ['name' => 'manage tracks', 'name_ar' => 'إدارة المسارات', 'slug' => 'tracks.manage', 'description' => 'Can manage tracks', 'description_ar' => 'يمكن إدارة المسارات'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                [
                    'name' => $permission['name'],
                    'name_ar' => $permission['name_ar'] ?? null,
                    'slug' => $permission['slug'],
                    'description' => $permission['description'],
                    'description_ar' => $permission['description_ar'] ?? null,
                    'guard_name' => 'web',
                ]
            );
        }

        $this->command->info('Permissions seeded successfully!');
    }
}
