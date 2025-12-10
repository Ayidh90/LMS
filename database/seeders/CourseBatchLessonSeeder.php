<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Batch;
use Modules\Courses\Models\Course;
use Modules\Courses\Models\Lesson;
use Modules\Courses\Models\Question;
use Modules\Courses\Models\Answer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CourseBatchLessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get instructors
        $instructor1 = User::updateOrCreate(
            ['email' => 'instructor1@lms.com'],
            [
                'name' => 'Ahmed Mohamed',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'is_admin' => false,
                'is_active' => true,
            ]
        );

        $instructor2 = User::updateOrCreate(
            ['email' => 'instructor2@lms.com'],
            [
                'name' => 'Sara Ali',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'is_admin' => false,
                'is_active' => true,
            ]
        );

        // Create courses
        $courses = [
            [
                'title' => 'Complete Web Development Course',
                'title_ar' => 'دورة تطوير الويب الكاملة',
                'description' => 'Learn HTML, CSS, JavaScript, React, Node.js, and more. Build real-world projects.',
                'description_ar' => 'تعلم HTML، CSS، JavaScript، React، Node.js والمزيد. أنشئ مشاريع حقيقية.',
                'level' => 'beginner',
                'price' => 199.99,
                'duration_hours' => 80,
                'is_published' => true,
                'batches' => [
                    [
                        'name' => 'Batch 2024 - Spring',
                        'name_ar' => 'دفعة 2024 - الربيع',
                        'description' => 'Spring 2024 batch for web development course',
                        'description_ar' => 'دفعة ربيع 2024 لدورة تطوير الويب',
                        'instructor_id' => $instructor1->id,
                        'start_date' => '2024-03-01',
                        'end_date' => '2024-06-30',
                        'max_students' => 50,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Batch 2024 - Fall',
                        'name_ar' => 'دفعة 2024 - الخريف',
                        'description' => 'Fall 2024 batch for web development course',
                        'description_ar' => 'دفعة خريف 2024 لدورة تطوير الويب',
                        'instructor_id' => $instructor2->id,
                        'start_date' => '2024-09-01',
                        'end_date' => '2024-12-31',
                        'max_students' => 50,
                        'is_active' => true,
                    ],
                ],
                'lessons' => [
                    [
                        'type' => 'text',
                        'title' => 'Introduction to Web Development',
                        'title_ar' => 'مقدمة في تطوير الويب',
                        'description' => 'Learn the basics of web development',
                        'description_ar' => 'تعلم أساسيات تطوير الويب',
                        'content' => 'Web development is the work involved in developing a website for the Internet or an intranet. Web development can range from developing a simple single static page of plain text to complex web applications, electronic businesses, and social network services.',
                        'content_ar' => 'تطوير الويب هو العمل المتضمن في تطوير موقع ويب للإنترنت أو الإنترانت. يمكن أن يتراوح تطوير الويب من تطوير صفحة ثابتة بسيطة من نص عادي إلى تطبيقات ويب معقدة وأعمال إلكترونية وخدمات شبكات اجتماعية.',
                        'order' => 1,
                        'duration_minutes' => 15,
                        'is_free' => true,
                    ],
                    [
                        'type' => 'youtube_video',
                        'title' => 'HTML Basics - Part 1',
                        'title_ar' => 'أساسيات HTML - الجزء الأول',
                        'description' => 'Learn HTML structure and tags',
                        'description_ar' => 'تعلم بنية HTML والعلامات',
                        'video_url' => 'https://www.youtube.com/watch?v=UB1O30fR-EE',
                        'order' => 2,
                        'duration_minutes' => 45,
                        'is_free' => true,
                    ],
                    [
                        'type' => 'youtube_video',
                        'title' => 'HTML Basics - Part 2',
                        'title_ar' => 'أساسيات HTML - الجزء الثاني',
                        'description' => 'Advanced HTML concepts and semantic elements',
                        'description_ar' => 'مفاهيم HTML المتقدمة والعناصر الدلالية',
                        'video_url' => 'https://www.youtube.com/watch?v=kUMe1FH4CHE',
                        'order' => 3,
                        'duration_minutes' => 50,
                        'is_free' => true,
                    ],
                    [
                        'type' => 'google_drive_video',
                        'title' => 'CSS Fundamentals',
                        'title_ar' => 'أساسيات CSS',
                        'description' => 'Learn CSS styling and layout',
                        'description_ar' => 'تعلم تنسيق CSS والتخطيط',
                        'video_url' => 'https://drive.google.com/file/d/1ABC123/view',
                        'order' => 4,
                        'duration_minutes' => 60,
                        'is_free' => false,
                    ],
                    [
                        'type' => 'youtube_video',
                        'title' => 'CSS Flexbox Tutorial',
                        'title_ar' => 'دليل CSS Flexbox',
                        'description' => 'Master CSS Flexbox for modern layouts',
                        'description_ar' => 'أتقن CSS Flexbox للتخطيطات الحديثة',
                        'video_url' => 'https://www.youtube.com/watch?v=JJSoEo8JSnc',
                        'order' => 5,
                        'duration_minutes' => 40,
                        'is_free' => false,
                    ],
                    [
                        'type' => 'image',
                        'title' => 'CSS Grid Layout Guide',
                        'title_ar' => 'دليل تخطيط CSS Grid',
                        'description' => 'Visual guide to CSS Grid',
                        'description_ar' => 'دليل مرئي لـ CSS Grid',
                        'content' => 'CSS Grid is a powerful layout system that allows you to create complex, responsive grid-based layouts with ease. This guide covers all the essential concepts.',
                        'content_ar' => 'CSS Grid هو نظام تخطيط قوي يتيح لك إنشاء تخطيطات معقدة وقابلة للاستجابة بسهولة. يغطي هذا الدليل جميع المفاهيم الأساسية.',
                        'video_url' => '/storage/images/css-grid-guide.png',
                        'order' => 6,
                        'duration_minutes' => 20,
                        'is_free' => true,
                    ],
                    [
                        'type' => 'video_file',
                        'title' => 'JavaScript Introduction',
                        'title_ar' => 'مقدمة JavaScript',
                        'description' => 'Learn JavaScript programming basics',
                        'description_ar' => 'تعلم أساسيات برمجة JavaScript',
                        'video_url' => '/storage/videos/javascript-intro.mp4',
                        'order' => 7,
                        'duration_minutes' => 90,
                        'is_free' => false,
                    ],
                    [
                        'type' => 'youtube_video',
                        'title' => 'JavaScript Variables and Data Types',
                        'title_ar' => 'متغيرات وأنواع البيانات في JavaScript',
                        'description' => 'Understanding variables, let, const, and data types',
                        'description_ar' => 'فهم المتغيرات و let و const وأنواع البيانات',
                        'video_url' => 'https://www.youtube.com/watch?v=W6NZfCO5SIk',
                        'order' => 8,
                        'duration_minutes' => 35,
                        'is_free' => false,
                    ],
                    [
                        'type' => 'youtube_video',
                        'title' => 'JavaScript Functions Explained',
                        'title_ar' => 'شرح دوال JavaScript',
                        'description' => 'Learn how to create and use functions in JavaScript',
                        'description_ar' => 'تعلم كيفية إنشاء واستخدام الدوال في JavaScript',
                        'video_url' => 'https://www.youtube.com/watch?v=N8ap4k_1QEQ',
                        'order' => 9,
                        'duration_minutes' => 55,
                        'is_free' => false,
                    ],
                    [
                        'type' => 'document_file',
                        'title' => 'JavaScript Cheat Sheet',
                        'title_ar' => 'ملخص JavaScript',
                        'description' => 'Quick reference guide for JavaScript',
                        'description_ar' => 'دليل مرجعي سريع لـ JavaScript',
                        'content' => 'Download the cheat sheet for quick reference. This comprehensive guide covers all JavaScript fundamentals including syntax, methods, and best practices.',
                        'content_ar' => 'قم بتنزيل الملخص للرجوع السريع. يغطي هذا الدليل الشامل جميع أساسيات JavaScript بما في ذلك الصياغة والطرق وأفضل الممارسات.',
                        'video_url' => '/storage/documents/javascript-cheatsheet.pdf',
                        'order' => 10,
                        'duration_minutes' => 10,
                        'is_free' => true,
                    ],
                    [
                        'type' => 'embed_frame',
                        'title' => 'Interactive Code Playground',
                        'title_ar' => 'ملعب الكود التفاعلي',
                        'description' => 'Practice coding in this interactive environment',
                        'description_ar' => 'تدرب على البرمجة في هذه البيئة التفاعلية',
                        'content' => '<iframe src="https://codepen.io/embed/example" width="100%" height="600"></iframe>',
                        'content_ar' => '<iframe src="https://codepen.io/embed/example" width="100%" height="600"></iframe>',
                        'order' => 11,
                        'duration_minutes' => 30,
                        'is_free' => true,
                    ],
                    [
                        'type' => 'youtube_video',
                        'title' => 'React Introduction',
                        'title_ar' => 'مقدمة React',
                        'description' => 'Get started with React framework',
                        'description_ar' => 'ابدأ مع إطار عمل React',
                        'video_url' => 'https://www.youtube.com/watch?v=SqcY0GlETPk',
                        'order' => 12,
                        'duration_minutes' => 120,
                        'is_free' => false,
                    ],
                    [
                        'type' => 'youtube_video',
                        'title' => 'React Components and Props',
                        'title_ar' => 'مكونات وخصائص React',
                        'description' => 'Learn React components and props system',
                        'description_ar' => 'تعلم نظام مكونات وخصائص React',
                        'video_url' => 'https://www.youtube.com/watch?v=DLX62G4lc44',
                        'order' => 13,
                        'duration_minutes' => 65,
                        'is_free' => false,
                    ],
                    [
                        'type' => 'text',
                        'title' => 'Node.js Backend Development',
                        'title_ar' => 'تطوير Backend بـ Node.js',
                        'description' => 'Introduction to server-side JavaScript',
                        'description_ar' => 'مقدمة في JavaScript من جانب الخادم',
                        'content' => 'Node.js is a JavaScript runtime built on Chrome\'s V8 JavaScript engine. It allows you to run JavaScript on the server side, enabling you to build scalable network applications.',
                        'content_ar' => 'Node.js هو وقت تشغيل JavaScript مبني على محرك V8 JavaScript من Chrome. يتيح لك تشغيل JavaScript من جانب الخادم، مما يتيح لك بناء تطبيقات شبكة قابلة للتوسع.',
                        'order' => 14,
                        'duration_minutes' => 25,
                        'is_free' => false,
                    ],
                ],
            ],
            [
                'title' => 'Mobile App Development with Flutter',
                'title_ar' => 'تطوير تطبيقات الموبايل باستخدام Flutter',
                'description' => 'Build cross-platform mobile apps with Flutter and Dart',
                'description_ar' => 'أنشئ تطبيقات موبايل متعددة المنصات باستخدام Flutter و Dart',
                'level' => 'intermediate',
                'price' => 249.99,
                'duration_hours' => 100,
                'is_published' => true,
                'batches' => [
                    [
                        'name' => 'Batch 2024 - Summer',
                        'name_ar' => 'دفعة 2024 - الصيف',
                        'description' => 'Summer 2024 batch for Flutter course',
                        'description_ar' => 'دفعة صيف 2024 لدورة Flutter',
                        'instructor_id' => $instructor1->id,
                        'start_date' => '2024-07-01',
                        'end_date' => '2024-09-30',
                        'max_students' => 30,
                        'is_active' => true,
                    ],
                ],
                'lessons' => [
                    [
                        'type' => 'text',
                        'title' => 'Flutter Introduction',
                        'title_ar' => 'مقدمة Flutter',
                        'description' => 'Introduction to Flutter framework',
                        'description_ar' => 'مقدمة في إطار عمل Flutter',
                        'content' => 'Flutter is Google\'s UI toolkit for building beautiful applications.',
                        'content_ar' => 'Flutter هو مجموعة أدوات واجهة المستخدم من Google لبناء تطبيقات جميلة.',
                        'order' => 1,
                        'duration_minutes' => 20,
                        'is_free' => true,
                    ],
                    [
                        'type' => 'youtube_video',
                        'title' => 'Dart Programming Basics',
                        'title_ar' => 'أساسيات برمجة Dart',
                        'description' => 'Learn Dart programming language',
                        'description_ar' => 'تعلم لغة برمجة Dart',
                        'video_url' => 'https://www.youtube.com/watch?v=example',
                        'order' => 2,
                        'duration_minutes' => 60,
                        'is_free' => false,
                    ],
                ],
            ],
            [
                'title' => 'Data Science with Python',
                'title_ar' => 'علوم البيانات باستخدام Python',
                'description' => 'Learn data analysis, machine learning, and visualization with Python',
                'description_ar' => 'تعلم تحليل البيانات والتعلم الآلي والتصور باستخدام Python',
                'level' => 'advanced',
                'price' => 299.99,
                'duration_hours' => 120,
                'is_published' => true,
                'batches' => [
                    [
                        'name' => 'Batch 2024 - Winter',
                        'name_ar' => 'دفعة 2024 - الشتاء',
                        'description' => 'Winter 2024 batch for Data Science course',
                        'description_ar' => 'دفعة شتاء 2024 لدورة علوم البيانات',
                        'instructor_id' => $instructor2->id,
                        'start_date' => '2024-01-01',
                        'end_date' => '2024-03-31',
                        'max_students' => 40,
                        'is_active' => true,
                    ],
                ],
                'lessons' => [
                    [
                        'type' => 'text',
                        'title' => 'Python Basics for Data Science',
                        'title_ar' => 'أساسيات Python لعلوم البيانات',
                        'description' => 'Learn Python fundamentals',
                        'description_ar' => 'تعلم أساسيات Python',
                        'content' => 'Python is a powerful programming language for data science. It provides extensive libraries and tools for data manipulation, analysis, and visualization.',
                        'content_ar' => 'Python هي لغة برمجة قوية لعلوم البيانات. توفر مكتبات وأدوات واسعة لمعالجة البيانات والتحليل والتصور.',
                        'order' => 1,
                        'duration_minutes' => 30,
                        'is_free' => true,
                    ],
                    [
                        'type' => 'youtube_video',
                        'title' => 'Python Data Types and Structures',
                        'title_ar' => 'أنواع البيانات والهياكل في Python',
                        'description' => 'Understanding Python data types',
                        'description_ar' => 'فهم أنواع البيانات في Python',
                        'video_url' => 'https://www.youtube.com/watch?v=khKv-8q7YmY',
                        'order' => 2,
                        'duration_minutes' => 45,
                        'is_free' => false,
                    ],
                    [
                        'type' => 'youtube_video',
                        'title' => 'Pandas DataFrames Tutorial',
                        'title_ar' => 'دليل DataFrames في Pandas',
                        'description' => 'Learn to work with Pandas DataFrames',
                        'description_ar' => 'تعلم العمل مع DataFrames في Pandas',
                        'video_url' => 'https://www.youtube.com/watch?v=vmEHCJofslg',
                        'order' => 3,
                        'duration_minutes' => 90,
                        'is_free' => false,
                    ],
                    [
                        'type' => 'youtube_video',
                        'title' => 'Data Visualization with Matplotlib',
                        'title_ar' => 'تصور البيانات باستخدام Matplotlib',
                        'description' => 'Create beautiful data visualizations',
                        'description_ar' => 'أنشئ تصورات بيانات جميلة',
                        'video_url' => 'https://www.youtube.com/watch?v=DAQNHzOcO5A',
                        'order' => 4,
                        'duration_minutes' => 70,
                        'is_free' => false,
                    ],
                    [
                        'type' => 'youtube_video',
                        'title' => 'Machine Learning with Scikit-learn',
                        'title_ar' => 'التعلم الآلي باستخدام Scikit-learn',
                        'description' => 'Introduction to machine learning',
                        'description_ar' => 'مقدمة في التعلم الآلي',
                        'video_url' => 'https://www.youtube.com/watch?v=pqNCD_5r0IU',
                        'order' => 5,
                        'duration_minutes' => 120,
                        'is_free' => false,
                    ],
                    [
                        'type' => 'image',
                        'title' => 'Data Science Workflow Diagram',
                        'title_ar' => 'مخطط سير عمل علوم البيانات',
                        'description' => 'Visual guide to data science workflow',
                        'description_ar' => 'دليل مرئي لسير عمل علوم البيانات',
                        'content' => 'This diagram illustrates the complete data science workflow from data collection to model deployment.',
                        'content_ar' => 'يُوضح هذا المخطط سير عمل علوم البيانات الكامل من جمع البيانات إلى نشر النموذج.',
                        'video_url' => '/storage/images/data-science-workflow.png',
                        'order' => 6,
                        'duration_minutes' => 20,
                        'is_free' => true,
                    ],
                    [
                        'type' => 'document_file',
                        'title' => 'Python Data Science Cheat Sheet',
                        'title_ar' => 'ملخص Python لعلوم البيانات',
                        'description' => 'Quick reference for Python data science',
                        'description_ar' => 'مرجع سريع لـ Python لعلوم البيانات',
                        'content' => 'Download this comprehensive cheat sheet covering Pandas, NumPy, Matplotlib, and Scikit-learn.',
                        'content_ar' => 'قم بتنزيل هذا الملخص الشامل الذي يغطي Pandas و NumPy و Matplotlib و Scikit-learn.',
                        'video_url' => '/storage/documents/python-datascience-cheatsheet.pdf',
                        'order' => 7,
                        'duration_minutes' => 10,
                        'is_free' => true,
                    ],
                    [
                        'type' => 'embed_frame',
                        'title' => 'Interactive Jupyter Notebook',
                        'title_ar' => 'دفتر Jupyter التفاعلي',
                        'description' => 'Practice data analysis in Jupyter',
                        'description_ar' => 'تدرب على تحليل البيانات في Jupyter',
                        'content' => '<iframe src="https://jupyter.org/try" width="100%" height="600"></iframe>',
                        'content_ar' => '<iframe src="https://jupyter.org/try" width="100%" height="600"></iframe>',
                        'order' => 8,
                        'duration_minutes' => 40,
                        'is_free' => true,
                    ],
                ],
            ],
        ];

        foreach ($courses as $courseData) {
            $batches = $courseData['batches'];
            $lessons = $courseData['lessons'];
            unset($courseData['batches'], $courseData['lessons']);

            $slug = Str::slug($courseData['title']);
            
            // Check if course already exists, if so update it, otherwise create new
            $course = Course::updateOrCreate(
                ['slug' => $slug],
                $courseData
            );

            // Create batches for this course (update or create)
            foreach ($batches as $batchData) {
                $batch = Batch::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'name' => $batchData['name'],
                    ],
                    $batchData
                );
            }

            // Create lessons for this course (update or create)
            foreach ($lessons as $lessonData) {
                Lesson::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'order' => $lessonData['order'],
                    ],
                    [
                        ...$lessonData,
                        'course_id' => $course->id,
                    ]
                );
            }
        }

        $this->command->info('Courses, batches, and lessons seeded successfully!');
    }
}
