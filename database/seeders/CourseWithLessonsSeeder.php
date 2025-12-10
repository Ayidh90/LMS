<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Modules\Courses\Models\Course;
use Modules\Courses\Models\Lesson;
use Modules\Courses\Models\Question;
use Modules\Courses\Models\Answer;
use App\Services\ImageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CourseWithLessonsSeeder extends Seeder
{
    private const DEFAULT_COURSE_IMAGE = 'courses/course.jpg';

    public function run(): void
    {
        $imageService = app(ImageService::class);
        
        $this->ensureDefaultCourseImage();
        
        // Get or create instructor
        $instructor = User::updateOrCreate(
            ['email' => 'instructor@lms.com'],
            [
                'name' => 'Instructor User',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'is_admin' => false,
                'is_active' => true,
            ]
        );

        // Get categories
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Please run CategorySeeder first.');
            return;
        }

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
                'category' => 'Web Development',
                'lessons' => [
                    [
                        'type' => 'video',
                        'title' => 'Introduction to HTML',
                        'title_ar' => 'مقدمة في HTML',
                        'description' => 'Learn the basics of HTML structure and tags.',
                        'description_ar' => 'تعلم أساسيات بنية HTML والعلامات.',
                        'content' => 'HTML (HyperText Markup Language) is the standard markup language for creating web pages.',
                        'content_ar' => 'HTML (لغة ترميز النص التشعبي) هي لغة الترميز القياسية لإنشاء صفحات الويب.',
                        'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                        'order' => 1,
                        'duration_minutes' => 30,
                        'is_free' => true,
                        'questions' => [
                            [
                                'type' => 'multiple_choice',
                                'question' => 'What does HTML stand for?',
                                'question_ar' => 'ماذا تعني HTML؟',
                                'explanation' => 'HTML stands for HyperText Markup Language.',
                                'explanation_ar' => 'HTML تعني لغة ترميز النص التشعبي.',
                                'points' => 1,
                                'order' => 1,
                                'answers' => [
                                    ['answer' => 'HyperText Markup Language', 'answer_ar' => 'لغة ترميز النص التشعبي', 'is_correct' => true, 'order' => 1],
                                    ['answer' => 'High Tech Modern Language', 'answer_ar' => 'لغة تقنية عالية حديثة', 'is_correct' => false, 'order' => 2],
                                    ['answer' => 'Home Tool Markup Language', 'answer_ar' => 'لغة ترميز أداة المنزل', 'is_correct' => false, 'order' => 3],
                                    ['answer' => 'Hyperlink and Text Markup Language', 'answer_ar' => 'لغة ترميز الارتباط التشعبي والنص', 'is_correct' => false, 'order' => 4],
                                ],
                            ],
                            [
                                'type' => 'true_false',
                                'question' => 'HTML is a programming language.',
                                'question_ar' => 'HTML هي لغة برمجة.',
                                'explanation' => 'HTML is a markup language, not a programming language.',
                                'explanation_ar' => 'HTML هي لغة ترميز وليست لغة برمجة.',
                                'points' => 1,
                                'order' => 2,
                                'answers' => [
                                    ['answer' => 'True', 'answer_ar' => 'صحيح', 'is_correct' => false, 'order' => 1],
                                    ['answer' => 'False', 'answer_ar' => 'خطأ', 'is_correct' => true, 'order' => 2],
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'video',
                        'title' => 'CSS Fundamentals',
                        'title_ar' => 'أساسيات CSS',
                        'description' => 'Master CSS styling and layout techniques.',
                        'description_ar' => 'أتقن تقنيات التنسيق والتخطيط في CSS.',
                        'content' => 'CSS (Cascading Style Sheets) is used to style HTML elements.',
                        'content_ar' => 'CSS (أوراق الأنماط المتتالية) تُستخدم لتنسيق عناصر HTML.',
                        'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                        'order' => 2,
                        'duration_minutes' => 45,
                        'is_free' => false,
                        'questions' => [
                            [
                                'type' => 'multiple_choice',
                                'question' => 'What does CSS stand for?',
                                'question_ar' => 'ماذا تعني CSS؟',
                                'explanation' => 'CSS stands for Cascading Style Sheets.',
                                'explanation_ar' => 'CSS تعني أوراق الأنماط المتتالية.',
                                'points' => 1,
                                'order' => 1,
                                'answers' => [
                                    ['answer' => 'Cascading Style Sheets', 'answer_ar' => 'أوراق الأنماط المتتالية', 'is_correct' => true, 'order' => 1],
                                    ['answer' => 'Computer Style Sheets', 'answer_ar' => 'أوراق الأنماط الحاسوبية', 'is_correct' => false, 'order' => 2],
                                    ['answer' => 'Creative Style Sheets', 'answer_ar' => 'أوراق الأنماط الإبداعية', 'is_correct' => false, 'order' => 3],
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'quiz',
                        'title' => 'JavaScript Basics Quiz',
                        'title_ar' => 'اختبار أساسيات JavaScript',
                        'description' => 'Test your knowledge of JavaScript fundamentals.',
                        'description_ar' => 'اختبر معرفتك بأساسيات JavaScript.',
                        'content' => 'This quiz covers JavaScript basics including variables, functions, and control structures.',
                        'content_ar' => 'يغطي هذا الاختبار أساسيات JavaScript بما في ذلك المتغيرات والدوال وهياكل التحكم.',
                        'video_url' => null,
                        'order' => 3,
                        'duration_minutes' => 20,
                        'is_free' => false,
                        'questions' => [
                            [
                                'type' => 'multiple_choice',
                                'question' => 'Which keyword is used to declare a variable in JavaScript?',
                                'question_ar' => 'ما هي الكلمة المفتاحية المستخدمة لإعلان متغير في JavaScript؟',
                                'explanation' => 'let, const, and var can all be used to declare variables.',
                                'explanation_ar' => 'يمكن استخدام let و const و var جميعها لإعلان المتغيرات.',
                                'points' => 1,
                                'order' => 1,
                                'answers' => [
                                    ['answer' => 'var', 'answer_ar' => 'var', 'is_correct' => true, 'order' => 1],
                                    ['answer' => 'variable', 'answer_ar' => 'variable', 'is_correct' => false, 'order' => 2],
                                    ['answer' => 'v', 'answer_ar' => 'v', 'is_correct' => false, 'order' => 3],
                                    ['answer' => 'declare', 'answer_ar' => 'declare', 'is_correct' => false, 'order' => 4],
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'title' => 'React Introduction - Reading Material',
                        'title_ar' => 'مقدمة في React - مادة القراءة',
                        'description' => 'Read about React fundamentals and concepts.',
                        'description_ar' => 'اقرأ عن أساسيات ومفاهيم React.',
                        'content' => 'React is a JavaScript library for building user interfaces. It allows you to create reusable UI components.',
                        'content_ar' => 'React هي مكتبة JavaScript لبناء واجهات المستخدم. تتيح لك إنشاء مكونات واجهة مستخدم قابلة لإعادة الاستخدام.',
                        'video_url' => null,
                        'order' => 4,
                        'duration_minutes' => 15,
                        'is_free' => false,
                        'questions' => [],
                    ],
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
                'category' => 'Design',
                'lessons' => [
                    [
                        'type' => 'video',
                        'title' => 'Design Principles',
                        'title_ar' => 'مبادئ التصميم',
                        'description' => 'Learn the fundamental principles of good design.',
                        'description_ar' => 'تعلم المبادئ الأساسية للتصميم الجيد.',
                        'content' => 'Design principles include balance, contrast, emphasis, movement, pattern, rhythm, and unity.',
                        'content_ar' => 'تشمل مبادئ التصميم التوازن والتباين والتأكيد والحركة والنمط والإيقاع والوحدة.',
                        'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                        'order' => 1,
                        'duration_minutes' => 45,
                        'is_free' => true,
                        'questions' => [
                            [
                                'type' => 'multiple_choice',
                                'question' => 'What is the principle of design that refers to the visual weight of elements?',
                                'question_ar' => 'ما هو مبدأ التصميم الذي يشير إلى الوزن البصري للعناصر؟',
                                'explanation' => 'Balance refers to the visual weight of elements in a design.',
                                'explanation_ar' => 'يشير التوازن إلى الوزن البصري للعناصر في التصميم.',
                                'points' => 1,
                                'order' => 1,
                                'answers' => [
                                    ['answer' => 'Balance', 'answer_ar' => 'التوازن', 'is_correct' => true, 'order' => 1],
                                    ['answer' => 'Contrast', 'answer_ar' => 'التباين', 'is_correct' => false, 'order' => 2],
                                    ['answer' => 'Emphasis', 'answer_ar' => 'التأكيد', 'is_correct' => false, 'order' => 3],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($courses as $courseData) {
            $category = $categories->firstWhere('name', $courseData['category']) ?? $categories->first();
            $lessons = $courseData['lessons'];
            $thumbnailUrl = $courseData['thumbnail'] ?? null;
            unset($courseData['lessons'], $courseData['category'], $courseData['thumbnail']);

            $slug = Str::slug($courseData['title']);
            $existingCourse = Course::where('slug', $slug)->first();
            
            $thumbnail = null;
            if ($thumbnailUrl && filter_var($thumbnailUrl, FILTER_VALIDATE_URL)) {
                $filename = $slug . '.jpg';
                $oldThumbnail = $existingCourse?->thumbnail;
                $thumbnail = $imageService->downloadFromUrl($thumbnailUrl, 'courses', $filename, $oldThumbnail);
                if (!$thumbnail) {
                    $thumbnail = $existingCourse?->thumbnail ?? self::DEFAULT_COURSE_IMAGE;
                }
            } else {
                $thumbnail = self::DEFAULT_COURSE_IMAGE;
            }

            if ($existingCourse) {
                $course = $existingCourse;
                if ($thumbnail && $thumbnail !== $existingCourse->thumbnail) {
                    $existingCourse->update(['thumbnail' => $thumbnail]);
                }
                
                // Check if lessons already exist
                $existingLessonsCount = $course->lessons()->count();
                if ($existingLessonsCount > 0) {
                    $this->command->info("Course already exists: {$course->title} with {$existingLessonsCount} lessons, skipping...");
                    continue;
                }
                
                // Course exists but has no lessons, create them
                $this->command->info("Course exists but has no lessons: {$course->title}, creating lessons...");
            } else {
                $course = Course::create([
                    'title' => $courseData['title'],
                    'title_ar' => $courseData['title_ar'],
                    'description' => $courseData['description'] ?? null,
                    'description_ar' => $courseData['description_ar'] ?? null,
                    'slug' => $slug,
                    'instructor_id' => $instructor->id,
                    'category_id' => $category->id,
                    'thumbnail' => $thumbnail,
                    'level' => $courseData['level'],
                    'price' => $courseData['price'],
                    'duration_hours' => $courseData['duration_hours'],
                    'is_published' => true,
                    'students_count' => rand(10, 500),
                ]);

                $lessonsCount = 0;
                $questionsCount = 0;
                $answersCount = 0;

                foreach ($lessons as $lessonData) {
                    $questions = $lessonData['questions'] ?? [];
                    unset($lessonData['questions']);

                    $lesson = Lesson::create([
                        'course_id' => $course->id,
                        'type' => $lessonData['type'] ?? 'video',
                        'title' => $lessonData['title'],
                        'title_ar' => $lessonData['title_ar'] ?? null,
                        'description' => $lessonData['description'] ?? null,
                        'description_ar' => $lessonData['description_ar'] ?? null,
                        'content' => $lessonData['content'] ?? null,
                        'content_ar' => $lessonData['content_ar'] ?? null,
                        'video_url' => $lessonData['video_url'] ?? null,
                        'order' => $lessonData['order'],
                        'duration_minutes' => $lessonData['duration_minutes'],
                        'is_free' => $lessonData['is_free'] ?? false,
                    ]);
                    $lessonsCount++;

                    foreach ($questions as $questionData) {
                        $answers = $questionData['answers'] ?? [];
                        unset($questionData['answers']);

                        $question = Question::create([
                            'lesson_id' => $lesson->id,
                            'type' => $questionData['type'] ?? 'multiple_choice',
                            'question' => $questionData['question'],
                            'question_ar' => $questionData['question_ar'] ?? null,
                            'explanation' => $questionData['explanation'] ?? null,
                            'explanation_ar' => $questionData['explanation_ar'] ?? null,
                            'points' => $questionData['points'] ?? 1,
                            'order' => $questionData['order'],
                        ]);
                        $questionsCount++;

                        if (!empty($answers)) {
                            foreach ($answers as $answerData) {
                                Answer::create([
                                    'question_id' => $question->id,
                                    'answer' => $answerData['answer'],
                                    'answer_ar' => $answerData['answer_ar'] ?? null,
                                    'is_correct' => $answerData['is_correct'] ?? false,
                                    'order' => $answerData['order'],
                                ]);
                                $answersCount++;
                            }
                        }
                    }
                }

                $this->command->info("Created course: {$course->title} with {$lessonsCount} lessons, {$questionsCount} questions, and {$answersCount} answers");
            }
        }

        $this->command->info('Courses with lessons, questions, and answers seeded successfully!');
    }

    private function ensureDefaultCourseImage(): void
    {
        if (Storage::disk('public')->exists(self::DEFAULT_COURSE_IMAGE)) {
            return;
        }

        if (!Storage::disk('public')->exists('courses')) {
            Storage::disk('public')->makeDirectory('courses');
        }

        $imageService = app(ImageService::class);
        $defaultImageUrl = 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800';
        $imageService->downloadFromUrl($defaultImageUrl, 'courses', 'course.jpg');
    }
}
