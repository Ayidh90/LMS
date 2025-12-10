<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Batch;
use App\Models\Enrollment;
// use App\Models\Category; // Removed - categories removed from system
use Modules\Courses\Models\Course;
use Modules\Courses\Models\Lesson;
use Modules\Courses\Models\Section;
use App\Services\ImageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CourseSeeder extends Seeder
{
    /**
     * Default course image path (should exist in storage/app/public/courses/)
     */
    private const DEFAULT_COURSE_IMAGE = 'courses/course.jpg';

    public function run(): void
    {
        $imageService = app(ImageService::class);
        
        // Ensure default course image exists or create placeholder
        $this->ensureDefaultCourseImage();
        
        // Get or create instructor
        $instructor = User::where('email', 'instructor@lms.com')->first();
        if (!$instructor) {
            $instructor = User::create([
                'name' => 'Instructor User',
                'email' => 'instructor@lms.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'is_active' => true,
            ]);
            // Assign instructor role using Spatie
            if (method_exists($instructor, 'assignRole')) {
                $instructor->assignRole('instructor');
            }
        }
        
        // Get or create student
        $student = User::where('email', 'student@lms.com')->first();
        if (!$student) {
            $student = User::create([
                'name' => 'Student User',
                'email' => 'student@lms.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'is_active' => true,
            ]);
            // Assign student role using Spatie
            if (method_exists($student, 'assignRole')) {
                $student->assignRole('student');
            }
        }

        // Categories removed from system - no longer needed
        // $categories = Category::all();
        // if ($categories->isEmpty()) {
        //     $this->command->warn('No categories found. Please run CategorySeeder first.');
        //     return;
        // }

        $courses = [
            [
                'title' => 'Complete Web Development Bootcamp',
                'title_ar' => 'دورة تطوير الويب الكاملة',
                'description' => 'Learn HTML, CSS, JavaScript, React, Node.js, and more. Build real-world projects and become a full-stack developer.',
                'description_ar' => 'تعلم HTML، CSS، JavaScript، React، Node.js والمزيد. أنشئ مشاريع حقيقية وأصبح مطور Full Stack.',
                'level' => 'beginner',
                'price' => 99.99,
                'duration_hours' => 60,
                'thumbnail' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800',
                'lessons' => [
                    ['title' => 'Introduction to HTML', 'title_ar' => 'مقدمة في HTML', 'order' => 1, 'duration_minutes' => 30, 'type' => 'youtube_video', 'video_url' => 'https://youtu.be/IPo6Ub_ks7s?si=uYkpp6TcIbhX1zlW'],
                    ['title' => 'HTML Structure and Elements', 'title_ar' => 'هيكل HTML والعناصر', 'order' => 2, 'duration_minutes' => 45, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=kUMe1FH4CHE'],
                    ['title' => 'CSS Fundamentals', 'title_ar' => 'أساسيات CSS', 'order' => 3, 'duration_minutes' => 45, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=1Rs2ND1ryYc'],
                    ['title' => 'CSS Layouts and Flexbox', 'title_ar' => 'تخطيطات CSS و Flexbox', 'order' => 4, 'duration_minutes' => 60, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=JJSoEo8JSnc'],
                    ['title' => 'JavaScript Basics', 'title_ar' => 'أساسيات JavaScript', 'order' => 5, 'duration_minutes' => 60, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=W6NZfCO5SIk'],
                    ['title' => 'JavaScript DOM Manipulation', 'title_ar' => 'التلاعب بـ DOM في JavaScript', 'order' => 6, 'duration_minutes' => 75, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=5fb2aPlgoys'],
                    ['title' => 'JavaScript Assignment', 'title_ar' => 'مهمة JavaScript', 'order' => 7, 'duration_minutes' => 90, 'type' => 'assignment'],
                    ['title' => 'React Introduction', 'title_ar' => 'مقدمة في React', 'order' => 8, 'duration_minutes' => 90, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=SqcY0GlETPk'],
                    ['title' => 'React Components and Props', 'title_ar' => 'مكونات React والخصائص', 'order' => 9, 'duration_minutes' => 105, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=DLX62G4lc44'],
                    ['title' => 'React Hooks and State', 'title_ar' => 'React Hooks والحالة', 'order' => 10, 'duration_minutes' => 120, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=O6P86uwfdR0'],
                    ['title' => 'React Quiz', 'title_ar' => 'اختبار React', 'order' => 11, 'duration_minutes' => 30, 'type' => 'test'],
                    ['title' => 'Node.js Backend', 'title_ar' => 'Node.js للخادم', 'order' => 12, 'duration_minutes' => 120, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=TlB_eWDSMt4'],
                    ['title' => 'Express.js Framework', 'title_ar' => 'إطار Express.js', 'order' => 13, 'duration_minutes' => 90, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=L72fhGm1tfE'],
                    ['title' => 'Building REST APIs', 'title_ar' => 'بناء REST APIs', 'order' => 14, 'duration_minutes' => 105, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=pKd0Rpw7O48'],
                    ['title' => 'Full Stack Project Assignment', 'title_ar' => 'مشروع Full Stack', 'order' => 15, 'duration_minutes' => 180, 'type' => 'assignment'],
                    ['title' => 'Course Final Test', 'title_ar' => 'الاختبار النهائي للدورة', 'order' => 16, 'duration_minutes' => 60, 'type' => 'test'],
                ],
            ],
            [
                'title' => 'Mobile App Development with Flutter',
                'title_ar' => 'تطوير تطبيقات الموبايل باستخدام Flutter',
                'description' => 'Master Flutter and Dart to build beautiful native mobile apps for iOS and Android.',
                'description_ar' => 'أتقن Flutter و Dart لبناء تطبيقات موبايل أصلية جميلة لـ iOS و Android.',
                'level' => 'intermediate',
                'price' => 149.99,
                'duration_hours' => 80,
                'thumbnail' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=800',
                'lessons' => [
                    ['title' => 'Flutter Setup', 'title_ar' => 'إعداد Flutter', 'order' => 1, 'duration_minutes' => 20, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=1gDhl4jeEzM'],
                    ['title' => 'Flutter Installation Guide', 'title_ar' => 'دليل تثبيت Flutter', 'order' => 2, 'duration_minutes' => 30, 'type' => 'document_file'],
                    ['title' => 'Dart Programming', 'title_ar' => 'برمجة Dart', 'order' => 3, 'duration_minutes' => 90, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=Ej_Pcr4uC2Q'],
                    ['title' => 'Dart Syntax and Concepts', 'title_ar' => 'صيغة Dart والمفاهيم', 'order' => 4, 'duration_minutes' => 75, 'type' => 'text'],
                    ['title' => 'Dart Practice Assignment', 'title_ar' => 'مهمة تدريبية في Dart', 'order' => 5, 'duration_minutes' => 60, 'type' => 'assignment'],
                    ['title' => 'Widgets and Layouts', 'title_ar' => 'Widgets والتخطيطات', 'order' => 6, 'duration_minutes' => 120, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=1ukSR1GRtMU'],
                    ['title' => 'Flutter UI Components', 'title_ar' => 'مكونات واجهة Flutter', 'order' => 7, 'duration_minutes' => 90, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=Gp2m8ZuXoPg'],
                    ['title' => 'State Management', 'title_ar' => 'إدارة الحالة', 'order' => 8, 'duration_minutes' => 150, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=shHnzK0s0qs'],
                    ['title' => 'Flutter Quiz', 'title_ar' => 'اختبار Flutter', 'order' => 9, 'duration_minutes' => 45, 'type' => 'test'],
                ],
            ],
            [
                'title' => 'Data Science with Python',
                'title_ar' => 'علوم البيانات باستخدام Python',
                'description' => 'Learn data analysis, machine learning, and data visualization with Python, Pandas, and Scikit-learn.',
                'description_ar' => 'تعلم تحليل البيانات، التعلم الآلي، وتصور البيانات باستخدام Python و Pandas و Scikit-learn.',
                'level' => 'intermediate',
                'price' => 129.99,
                'duration_hours' => 70,
                'thumbnail' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800',
                'lessons' => [
                    ['title' => 'Python Basics', 'title_ar' => 'أساسيات Python', 'order' => 1, 'duration_minutes' => 60, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=kqtD5dpn9C8'],
                    ['title' => 'Python Data Types', 'title_ar' => 'أنواع البيانات في Python', 'order' => 2, 'duration_minutes' => 45, 'type' => 'text'],
                    ['title' => 'Python Control Structures', 'title_ar' => 'هياكل التحكم في Python', 'order' => 3, 'duration_minutes' => 50, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=H2E1GQ2_0xQ'],
                    ['title' => 'Python Assignment', 'title_ar' => 'مهمة Python', 'order' => 4, 'duration_minutes' => 90, 'type' => 'assignment'],
                    ['title' => 'Pandas Data Analysis', 'title_ar' => 'تحليل البيانات بـ Pandas', 'order' => 5, 'duration_minutes' => 120, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=vmEHCJofslg'],
                    ['title' => 'Pandas DataFrames', 'title_ar' => 'DataFrames في Pandas', 'order' => 6, 'duration_minutes' => 90, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=zmdjNSmRXF4'],
                    ['title' => 'Data Visualization', 'title_ar' => 'تصور البيانات', 'order' => 7, 'duration_minutes' => 90, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=3m5qxZm_JqM'],
                    ['title' => 'Matplotlib and Seaborn', 'title_ar' => 'Matplotlib و Seaborn', 'order' => 8, 'duration_minutes' => 75, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=nZ76x13Nm8Q'],
                    ['title' => 'Visualization Assignment', 'title_ar' => 'مهمة التصور', 'order' => 9, 'duration_minutes' => 120, 'type' => 'assignment'],
                    ['title' => 'Machine Learning Basics', 'title_ar' => 'أساسيات التعلم الآلي', 'order' => 10, 'duration_minutes' => 150, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=aircAruvnKk'],
                    ['title' => 'Scikit-learn Tutorial', 'title_ar' => 'دليل Scikit-learn', 'order' => 11, 'duration_minutes' => 105, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=pqNCD_5J0Xg'],
                    ['title' => 'ML Project Assignment', 'title_ar' => 'مشروع التعلم الآلي', 'order' => 12, 'duration_minutes' => 180, 'type' => 'assignment'],
                    ['title' => 'Data Science Quiz', 'title_ar' => 'اختبار علوم البيانات', 'order' => 13, 'duration_minutes' => 60, 'type' => 'test'],
                ],
            ],
            [
                'title' => 'UI/UX Design Masterclass',
                'title_ar' => 'دورة UI/UX Design المتقدمة',
                'description' => 'Learn design principles, user research, wireframing, prototyping, and design tools like Figma.',
                'description_ar' => 'تعلم مبادئ التصميم، بحث المستخدم، wireframing، النماذج الأولية، وأدوات التصميم مثل Figma.',
                'level' => 'beginner',
                'price' => 89.99,
                'duration_hours' => 50,
                'thumbnail' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=800',
                'lessons' => [
                    ['title' => 'Design Principles', 'title_ar' => 'مبادئ التصميم', 'order' => 1, 'duration_minutes' => 45, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=ZbrzdMaumNk'],
                    ['title' => 'Color Theory and Typography', 'title_ar' => 'نظرية الألوان والطباعة', 'order' => 2, 'duration_minutes' => 60, 'type' => 'text'],
                    ['title' => 'User Research', 'title_ar' => 'بحث المستخدم', 'order' => 3, 'duration_minutes' => 60, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=QU9TdbbRxlU'],
                    ['title' => 'User Personas and Journey Maps', 'title_ar' => 'شخصيات المستخدم وخريطة الرحلة', 'order' => 4, 'duration_minutes' => 75, 'type' => 'document_file'],
                    ['title' => 'Wireframing', 'title_ar' => 'Wireframing', 'order' => 5, 'duration_minutes' => 90, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=qpH7-KFWZRI'],
                    ['title' => 'Prototyping Basics', 'title_ar' => 'أساسيات النماذج الأولية', 'order' => 6, 'duration_minutes' => 60, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=FTFaQWZBqQ8'],
                    ['title' => 'Design Assignment', 'title_ar' => 'مهمة التصميم', 'order' => 7, 'duration_minutes' => 120, 'type' => 'assignment'],
                    ['title' => 'Figma Basics', 'title_ar' => 'أساسيات Figma', 'order' => 8, 'duration_minutes' => 120, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=FTFaQWZBqQ8'],
                    ['title' => 'Advanced Figma Techniques', 'title_ar' => 'تقنيات Figma المتقدمة', 'order' => 9, 'duration_minutes' => 90, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=FTFaQWZBqQ8'],
                    ['title' => 'UI/UX Quiz', 'title_ar' => 'اختبار UI/UX', 'order' => 10, 'duration_minutes' => 45, 'type' => 'test'],
                ],
            ],
            [
                'title' => 'Digital Marketing Strategy',
                'title_ar' => 'استراتيجية التسويق الرقمي',
                'description' => 'Master SEO, social media marketing, content marketing, email marketing, and analytics.',
                'description_ar' => 'أتقن SEO، التسويق عبر وسائل التواصل الاجتماعي، تسويق المحتوى، التسويق عبر البريد الإلكتروني، والتحليلات.',
                'level' => 'beginner',
                'price' => 79.99,
                'duration_hours' => 40,
                'thumbnail' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800',
                'lessons' => [
                    ['title' => 'SEO Fundamentals', 'title_ar' => 'أساسيات SEO', 'order' => 1, 'duration_minutes' => 60, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=xsVTqzratPs'],
                    ['title' => 'Keyword Research', 'title_ar' => 'بحث الكلمات المفتاحية', 'order' => 2, 'duration_minutes' => 45, 'type' => 'text'],
                    ['title' => 'On-Page SEO', 'title_ar' => 'SEO على الصفحة', 'order' => 3, 'duration_minutes' => 50, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=GGR90iE6lqA'],
                    ['title' => 'SEO Assignment', 'title_ar' => 'مهمة SEO', 'order' => 4, 'duration_minutes' => 90, 'type' => 'assignment'],
                    ['title' => 'Social Media Marketing', 'title_ar' => 'التسويق عبر وسائل التواصل', 'order' => 5, 'duration_minutes' => 90, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=u9NE0jInb_c'],
                    ['title' => 'Facebook and Instagram Ads', 'title_ar' => 'إعلانات Facebook و Instagram', 'order' => 6, 'duration_minutes' => 75, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=GGR90iE6lqA'],
                    ['title' => 'Content Marketing', 'title_ar' => 'تسويق المحتوى', 'order' => 7, 'duration_minutes' => 75, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=GGR90iE6lqA'],
                    ['title' => 'Content Strategy Guide', 'title_ar' => 'دليل استراتيجية المحتوى', 'order' => 8, 'duration_minutes' => 60, 'type' => 'document_file'],
                    ['title' => 'Email Marketing', 'title_ar' => 'التسويق عبر البريد الإلكتروني', 'order' => 9, 'duration_minutes' => 60, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=GGR90iE6lqA'],
                    ['title' => 'Analytics and Tracking', 'title_ar' => 'التحليلات والتتبع', 'order' => 10, 'duration_minutes' => 60, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=GGR90iE6lqA'],
                    ['title' => 'Google Analytics Setup', 'title_ar' => 'إعداد Google Analytics', 'order' => 11, 'duration_minutes' => 45, 'type' => 'text'],
                    ['title' => 'Marketing Campaign Assignment', 'title_ar' => 'مهمة حملة تسويقية', 'order' => 12, 'duration_minutes' => 120, 'type' => 'assignment'],
                    ['title' => 'Digital Marketing Quiz', 'title_ar' => 'اختبار التسويق الرقمي', 'order' => 13, 'duration_minutes' => 45, 'type' => 'test'],
                ],
            ],
            [
                'title' => 'Business Management Essentials',
                'title_ar' => 'أساسيات إدارة الأعمال',
                'description' => 'Learn leadership, project management, financial planning, and business strategy.',
                'description_ar' => 'تعلم القيادة، إدارة المشاريع، التخطيط المالي، واستراتيجية الأعمال.',
                'level' => 'beginner',
                'price' => 69.99,
                'duration_hours' => 35,
                'thumbnail' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800',
                'lessons' => [
                    ['title' => 'Leadership Skills', 'title_ar' => 'مهارات القيادة', 'order' => 1, 'duration_minutes' => 45, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=UfEi0-G47pE'],
                    ['title' => 'Effective Communication', 'title_ar' => 'التواصل الفعال', 'order' => 2, 'duration_minutes' => 40, 'type' => 'text'],
                    ['title' => 'Team Building', 'title_ar' => 'بناء الفريق', 'order' => 3, 'duration_minutes' => 50, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=UfEi0-G47pE'],
                    ['title' => 'Leadership Assignment', 'title_ar' => 'مهمة القيادة', 'order' => 4, 'duration_minutes' => 90, 'type' => 'assignment'],
                    ['title' => 'Project Management', 'title_ar' => 'إدارة المشاريع', 'order' => 5, 'duration_minutes' => 90, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=UfEi0-G47pE'],
                    ['title' => 'Agile Methodology', 'title_ar' => 'منهجية Agile', 'order' => 6, 'duration_minutes' => 75, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=UfEi0-G47pE'],
                    ['title' => 'Project Management Tools', 'title_ar' => 'أدوات إدارة المشاريع', 'order' => 7, 'duration_minutes' => 60, 'type' => 'document_file'],
                    ['title' => 'PM Assignment', 'title_ar' => 'مهمة إدارة المشاريع', 'order' => 8, 'duration_minutes' => 120, 'type' => 'assignment'],
                    ['title' => 'Financial Planning', 'title_ar' => 'التخطيط المالي', 'order' => 9, 'duration_minutes' => 75, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=UfEi0-G47pE'],
                    ['title' => 'Budgeting and Forecasting', 'title_ar' => 'الميزانية والتنبؤ', 'order' => 10, 'duration_minutes' => 60, 'type' => 'text'],
                    ['title' => 'Business Strategy', 'title_ar' => 'استراتيجية الأعمال', 'order' => 11, 'duration_minutes' => 60, 'type' => 'youtube_video', 'video_url' => 'https://www.youtube.com/watch?v=UfEi0-G47pE'],
                    ['title' => 'SWOT Analysis', 'title_ar' => 'تحليل SWOT', 'order' => 12, 'duration_minutes' => 45, 'type' => 'document_file'],
                    ['title' => 'Business Plan Assignment', 'title_ar' => 'مهمة خطة الأعمال', 'order' => 13, 'duration_minutes' => 150, 'type' => 'assignment'],
                    ['title' => 'Business Management Quiz', 'title_ar' => 'اختبار إدارة الأعمال', 'order' => 14, 'duration_minutes' => 50, 'type' => 'test'],
                ],
            ],
        ];

        foreach ($courses as $courseData) {
            $lessons = $courseData['lessons'];
            $thumbnailUrl = $courseData['thumbnail'] ?? null;
            unset($courseData['lessons'], $courseData['thumbnail']);

            // Check if course already exists
            $slug = Str::slug($courseData['title']);
            $existingCourse = Course::where('slug', $slug)->first();
            
            // Always download thumbnail from URL if provided (force update)
            $thumbnail = null;
            if ($thumbnailUrl) {
                // Check if it's a URL
                if (filter_var($thumbnailUrl, FILTER_VALIDATE_URL)) {
                    // Generate filename based on slug
                    $filename = $slug . '.jpg';
                    // Pass old thumbnail path if course exists (for replacement)
                    $oldThumbnail = $existingCourse?->thumbnail;
                    
                    // Force download even if course already has thumbnail
                    $this->command->info("Downloading thumbnail for: {$courseData['title']} from URL...");
                    $thumbnail = $imageService->downloadFromUrl($thumbnailUrl, 'courses', $filename, $oldThumbnail);
                    
                    if ($thumbnail) {
                        $this->command->info("✓ Successfully downloaded thumbnail: {$filename}");
                    } else {
                        $this->command->error("✗ Failed to download thumbnail for: {$courseData['title']} from URL: {$thumbnailUrl}");
                        // Use default course image if download fails and no existing thumbnail
                        if ($existingCourse?->thumbnail) {
                            $thumbnail = $existingCourse->thumbnail;
                            $this->command->info("Keeping existing thumbnail for: {$courseData['title']}");
                        } else {
                            $thumbnail = self::DEFAULT_COURSE_IMAGE;
                            $this->command->info("Using default course image for: {$courseData['title']}");
                        }
                    }
                } else {
                    // Legacy: check if thumbnail exists in storage (for backward compatibility)
                    if (Storage::disk('public')->exists($thumbnailUrl)) {
                        $thumbnail = $thumbnailUrl;
                        $this->command->info("Found thumbnail in storage for: {$courseData['title']}");
                    } else {
                        $this->command->warn("Thumbnail not found in storage for: {$courseData['title']} (looking for: {$thumbnailUrl})");
                        // Use default course image if not found
                        $thumbnail = self::DEFAULT_COURSE_IMAGE;
                        $this->command->info("Using default course image for: {$courseData['title']}");
                    }
                }
            }
            
            // If no thumbnail was set, use default
            if (!$thumbnail) {
                $thumbnail = self::DEFAULT_COURSE_IMAGE;
            }

            // Ensure all translation fields are set
            $courseData['title_ar'] = $courseData['title_ar'] ?? null;
            $courseData['description_ar'] = $courseData['description_ar'] ?? null;

            if ($existingCourse) {
                // Force update existing course with thumbnail and translations
                $updateData = [];
                
                // Always force update thumbnail if we have a URL (even if course already has one)
                if ($thumbnailUrl && filter_var($thumbnailUrl, FILTER_VALIDATE_URL)) {
                    // Force update thumbnail - always set it (download was already attempted above)
                    // $thumbnail will contain the new downloaded image or the old one if download failed
                    if ($thumbnail !== null) {
                        $updateData['thumbnail'] = $thumbnail;
                        $this->command->info("Force updating thumbnail for existing course: {$courseData['title']} -> " . basename($thumbnail));
                    }
                } elseif ($thumbnail) {
                    // Use downloaded thumbnail (for non-URL cases)
                    $updateData['thumbnail'] = $thumbnail;
                }
                
                // Always update translations (allow overwriting)
                if (!empty($courseData['title_ar'])) {
                    $updateData['title_ar'] = $courseData['title_ar'];
                }
                if (!empty($courseData['description_ar'])) {
                    $updateData['description_ar'] = $courseData['description_ar'];
                }
                
                // Always update if we have thumbnail URL (force update) OR if we have other updates
                if (!empty($updateData)) {
                    $existingCourse->update($updateData);
                    $thumbnailInfo = isset($updateData['thumbnail']) ? basename($updateData['thumbnail']) : 'No change';
                    if ($thumbnailUrl && filter_var($thumbnailUrl, FILTER_VALIDATE_URL)) {
                        $this->command->info("✓ Force updated existing course: {$courseData['title']} with thumbnail: {$thumbnailInfo}");
                    } else {
                        $this->command->info("✓ Updated existing course: {$courseData['title']} with thumbnail: {$thumbnailInfo}");
                    }
                } else {
                    $this->command->info("Course already exists: {$courseData['title']}, skipping...");
                }
                $course = $existingCourse;
                
                // Delete existing sections and lessons to recreate with updated data
                $existingSectionsCount = $course->sections()->count();
                if ($existingSectionsCount > 0) {
                    $this->command->info("Course '{$courseData['title']}' has {$existingSectionsCount} existing section(s). Deleting to recreate with updated lessons...");
                    // Delete lessons first (due to foreign key constraint)
                    $course->lessons()->delete();
                    // Then delete sections
                    $course->sections()->delete();
                }
                
                // Create sections and lessons (always recreate if lessons are provided)
                if (!empty($lessons)) {
                    $this->command->info("Creating sections and lessons for course: {$courseData['title']}...");
                    $this->createSectionsAndLessons($course, $lessons, $courseData);
                }
                
                // For existing courses, ensure they have a batch
                $existingBatch = Batch::where('course_id', $course->id)->first();
                if (!$existingBatch) {
                    $this->createBatchForCourse($course, $instructor, $student);
                }
            } else {
                // Create new course with all translations
                // Note: instructor_id and category_id removed - instructors are assigned to batches, categories removed
                $course = Course::create([
                    'title' => $courseData['title'],
                    'title_ar' => $courseData['title_ar'],
                    'description' => $courseData['description'] ?? null,
                    'description_ar' => $courseData['description_ar'] ?? null,
                    'slug' => $slug,
                    // 'instructor_id' => $instructor->id, // Removed - instructors are assigned to batches now
                    // 'category_id' => removed - categories removed from system
                    'thumbnail' => $thumbnail,
                    'level' => $courseData['level'],
                    'price' => $courseData['price'],
                    'duration_hours' => $courseData['duration_hours'],
                    'is_published' => true,
                    'students_count' => rand(10, 500),
                ]);

                // Create sections and organize lessons
                $this->createSectionsAndLessons($course, $lessons, $courseData);
                
                // Create batch for this course with instructor
                $this->createBatchForCourse($course, $instructor, $student);

                $this->command->info("Created course: {$course->title} (EN) / {$course->title_ar} (AR)");
            }
        }

        $this->command->info('Courses seeded successfully!');
    }
    
    /**
     * Create a batch for a course and enroll student
     */
    private function createBatchForCourse(Course $course, User $instructor, User $student): void
    {
        $batchName = 'Batch ' . date('Y') . ' - ' . $course->title;
        $batchNameAr = 'دفعة ' . date('Y') . ' - ' . ($course->title_ar ?? $course->title);
        
        $batch = Batch::updateOrCreate(
            [
                'course_id' => $course->id,
                'name' => $batchName,
            ],
            [
                'course_id' => $course->id,
                'instructor_id' => $instructor->id,
                'name' => $batchName,
                'name_ar' => $batchNameAr,
                'description' => 'Batch for ' . $course->title,
                'description_ar' => 'دفعة لـ ' . ($course->title_ar ?? $course->title),
                'start_date' => now()->addDays(7),
                'end_date' => now()->addMonths(3),
                'max_students' => 50,
                'is_active' => true,
            ]
        );
        
        $this->command->info("  Created batch: {$batchName} with instructor: {$instructor->email}");
        
        // Enroll student in batch if not already enrolled
        $existingEnrollment = Enrollment::where('batch_id', $batch->id)
            ->where('student_id', $student->id)
            ->first();
            
        if (!$existingEnrollment) {
            Enrollment::create([
                'batch_id' => $batch->id,
                'student_id' => $student->id,
                'status' => 'enrolled',
                'progress' => 0,
                'enrolled_at' => now(),
            ]);
            $this->command->info("  Enrolled student: {$student->email} in batch: {$batchName}");
        } else {
            $this->command->info("  Student {$student->email} already enrolled in batch: {$batchName}");
        }
    }

    /**
     * Create sections and organize lessons for a course
     */
    private function createSectionsAndLessons(Course $course, array $lessons, array $courseData): void
    {
        if (empty($lessons)) {
            $this->command->warn("No lessons provided for course: {$course->title}");
            return;
        }

        // Group lessons into sections (2-3 lessons per section)
        $lessonsPerSection = 2;
        $sectionOrder = 1;
        $lessonOrderInSection = 1;
        $currentSection = null;

        foreach ($lessons as $index => $lessonData) {
            // Create a new section every $lessonsPerSection lessons
            if ($index % $lessonsPerSection === 0) {
                $sectionTitle = 'Section ' . $sectionOrder;
                $sectionTitleAr = 'القسم ' . $sectionOrder;
                
                // Check if section already exists with this title
                $existingSection = Section::where('course_id', $course->id)
                    ->where('title', $sectionTitle)
                    ->first();
                
                if (!$existingSection) {
                    $currentSection = Section::create([
                        'course_id' => $course->id,
                        'title' => $sectionTitle,
                        'title_ar' => $sectionTitleAr,
                        'description' => 'This section covers important topics in ' . $courseData['title'],
                        'description_ar' => 'يتناول هذا القسم مواضيع مهمة في ' . ($courseData['title_ar'] ?? $courseData['title']),
                        'order' => $sectionOrder,
                    ]);
                    $this->command->info("  Created section: {$sectionTitle}");
                } else {
                    $currentSection = $existingSection;
                    $this->command->info("  Section already exists: {$sectionTitle}, using existing section");
                }
                
                $sectionOrder++;
                $lessonOrderInSection = 1;
            }

            // Check if lesson already exists
            $existingLesson = Lesson::where('course_id', $course->id)
                ->where('title', $lessonData['title'])
                ->first();

            if (!$existingLesson) {
                // Create lesson and assign to current section
                $lessonAttributes = [
                    'course_id' => $course->id,
                    'section_id' => $currentSection->id,
                    'title' => $lessonData['title'],
                    'title_ar' => $lessonData['title_ar'] ?? null,
                    'description' => 'Learn ' . $lessonData['title'],
                    'description_ar' => 'تعلم ' . ($lessonData['title_ar'] ?? $lessonData['title']),
                    'content' => 'This lesson covers ' . $lessonData['title'] . ' in detail. You will learn the fundamentals and best practices.',
                    'content_ar' => 'يتناول هذا الدرس ' . ($lessonData['title_ar'] ?? $lessonData['title']) . ' بالتفصيل. ستتعلم الأساسيات وأفضل الممارسات.',
                    'order' => $lessonOrderInSection,
                    'duration_minutes' => $lessonData['duration_minutes'],
                    'is_free' => $lessonData['order'] === 1, // First lesson is free
                    'type' => $lessonData['type'] ?? 'text', // Default to 'text' if not specified
                ];
                
                // Add video_url if provided
                if (isset($lessonData['video_url']) && !empty($lessonData['video_url'])) {
                    $lessonAttributes['video_url'] = $lessonData['video_url'];
                }
                
                Lesson::create($lessonAttributes);
                $lessonType = $lessonData['type'] ?? 'text';
                $this->command->info("    Created lesson: {$lessonData['title']} (Type: {$lessonType})");
            } else {
                // Update existing lesson to assign to section if not already assigned
                if (!$existingLesson->section_id && $currentSection) {
                    $existingLesson->update(['section_id' => $currentSection->id]);
                    $this->command->info("    Updated lesson '{$lessonData['title']}' to assign to section");
                } else {
                    $this->command->info("    Lesson already exists: {$lessonData['title']}, skipping");
                }
            }
            
            $lessonOrderInSection++;
        }

        $sectionsCount = $course->sections()->count();
        $lessonsCount = $course->lessons()->count();
        $this->command->info("  Course '{$course->title}' now has {$sectionsCount} section(s) and {$lessonsCount} lesson(s)");
    }

    /**
     * Ensure default course image exists in storage
     */
    private function ensureDefaultCourseImage(): void
    {
        $defaultImagePath = self::DEFAULT_COURSE_IMAGE;
        
        // Check if default image already exists
        if (Storage::disk('public')->exists($defaultImagePath)) {
            $this->command->info("Default course image already exists: {$defaultImagePath}");
            return;
        }

        // Ensure courses directory exists
        if (!Storage::disk('public')->exists('courses')) {
            Storage::disk('public')->makeDirectory('courses');
        }

        // Try to download a default course image from Unsplash
        $defaultImageUrl = 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800'; // Generic course/education image
        $imageService = app(ImageService::class);
        
        $this->command->info("Creating default course image...");
        $downloaded = $imageService->downloadFromUrl($defaultImageUrl, 'courses', 'course.jpg');
        
        if ($downloaded) {
            $this->command->info("✓ Default course image created: {$downloaded}");
        } else {
            // If download fails, create a simple placeholder
            $this->command->warn("Failed to download default image, creating placeholder...");
            // Create a simple 1x1 transparent PNG as placeholder (you can replace this with actual image creation)
            $placeholder = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
            Storage::disk('public')->put($defaultImagePath, $placeholder);
            $this->command->info("Created placeholder image: {$defaultImagePath}");
        }
    }
}
