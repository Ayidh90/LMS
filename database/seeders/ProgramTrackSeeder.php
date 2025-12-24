<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Track;
use Modules\Courses\Models\Course;
use Illuminate\Support\Str;

class ProgramTrackSeeder extends Seeder
{
    public function run(): void
    {
        // Create or get existing Programs
        $program1 = Program::firstOrCreate(
            ['slug' => 'web-development-program'],
            [
                'name' => 'Web Development Program',
                'name_ar' => 'برنامج تطوير الويب',
                'description' => 'Comprehensive web development program covering frontend and backend technologies',
                'description_ar' => 'برنامج شامل لتطوير الويب يغطي تقنيات الواجهة الأمامية والخلفية',
                'is_active' => true,
                'order' => 1,
            ]
        );

        $program2 = Program::firstOrCreate(
            ['slug' => 'data-science-program'],
            [
                'name' => 'Data Science Program',
                'name_ar' => 'برنامج علوم البيانات',
                'description' => 'Learn data analysis, machine learning, and AI technologies',
                'description_ar' => 'تعلم تحليل البيانات والتعلم الآلي وتقنيات الذكاء الاصطناعي',
                'is_active' => true,
                'order' => 2,
            ]
        );

        $program3 = Program::firstOrCreate(
            ['slug' => 'mobile-development-program'],
            [
                'name' => 'Mobile Development Program',
                'name_ar' => 'برنامج تطوير التطبيقات',
                'description' => 'Master mobile app development for iOS and Android',
                'description_ar' => 'إتقان تطوير تطبيقات الهاتف المحمول لنظامي iOS و Android',
                'is_active' => true,
                'order' => 3,
            ]
        );

        // Create or get existing Tracks for Program 1
        $track1 = Track::firstOrCreate(
            ['slug' => 'frontend-development-track'],
            [
                'program_id' => $program1->id,
                'name' => 'Frontend Development Track',
                'name_ar' => 'مسار تطوير الواجهة الأمامية',
                'description' => 'Learn HTML, CSS, JavaScript, React, and Vue.js',
                'description_ar' => 'تعلم HTML و CSS و JavaScript و React و Vue.js',
                'is_active' => true,
                'order' => 1,
            ]
        );

        $track2 = Track::firstOrCreate(
            ['slug' => 'backend-development-track'],
            [
                'program_id' => $program1->id,
                'name' => 'Backend Development Track',
                'name_ar' => 'مسار تطوير الخلفية',
                'description' => 'Master server-side development with PHP, Laravel, Node.js',
                'description_ar' => 'إتقان تطوير الخادم باستخدام PHP و Laravel و Node.js',
                'is_active' => true,
                'order' => 2,
            ]
        );

        // Create or get existing Tracks for Program 2
        $track3 = Track::firstOrCreate(
            ['slug' => 'data-analysis-track'],
            [
                'program_id' => $program2->id,
                'name' => 'Data Analysis Track',
                'name_ar' => 'مسار تحليل البيانات',
                'description' => 'Learn data analysis with Python, pandas, and visualization tools',
                'description_ar' => 'تعلم تحليل البيانات باستخدام Python و pandas وأدوات التصور',
                'is_active' => true,
                'order' => 1,
            ]
        );

        $track4 = Track::firstOrCreate(
            ['slug' => 'machine-learning-track'],
            [
                'program_id' => $program2->id,
                'name' => 'Machine Learning Track',
                'name_ar' => 'مسار التعلم الآلي',
                'description' => 'Deep dive into machine learning algorithms and AI',
                'description_ar' => 'تعمق في خوارزميات التعلم الآلي والذكاء الاصطناعي',
                'is_active' => true,
                'order' => 2,
            ]
        );

        // Create or get existing Tracks for Program 3
        $track5 = Track::firstOrCreate(
            ['slug' => 'ios-development-track'],
            [
                'program_id' => $program3->id,
                'name' => 'iOS Development Track',
                'name_ar' => 'مسار تطوير iOS',
                'description' => 'Build iOS apps with Swift and SwiftUI',
                'description_ar' => 'بناء تطبيقات iOS باستخدام Swift و SwiftUI',
                'is_active' => true,
                'order' => 1,
            ]
        );

        $track6 = Track::firstOrCreate(
            ['slug' => 'android-development-track'],
            [
                'program_id' => $program3->id,
                'name' => 'Android Development Track',
                'name_ar' => 'مسار تطوير Android',
                'description' => 'Create Android apps with Kotlin and Jetpack Compose',
                'description_ar' => 'إنشاء تطبيقات Android باستخدام Kotlin و Jetpack Compose',
                'is_active' => true,
                'order' => 2,
            ]
        );

        // Get existing courses that don't have a track assigned
        $existingCourses = Course::where(function($query) {
                $query->whereNull('track_id')
                      ->orWhere('track_id', 0);
            })
            ->get();

        if ($existingCourses->isEmpty()) {
            $this->command->warn('No existing courses found without tracks. Creating sample courses...');
            
            // Create sample courses if none exist
            $sampleCourses = [
                [
                    'track_id' => $track1->id,
                    'title' => 'HTML & CSS Fundamentals',
                    'title_ar' => 'أساسيات HTML و CSS',
                    'description' => 'Learn the basics of HTML and CSS for web development',
                    'description_ar' => 'تعلم أساسيات HTML و CSS لتطوير الويب',
                    'slug' => 'html-css-fundamentals',
                    'level' => 'beginner',
                    'course_type' => 'course',
                    'duration_hours' => 20,
                    'price' => 99.99,
                    'is_published' => true,
                ],
                [
                    'track_id' => $track1->id,
                    'title' => 'JavaScript Essentials',
                    'title_ar' => 'أساسيات JavaScript',
                    'description' => 'Master JavaScript programming fundamentals',
                    'description_ar' => 'إتقان أساسيات برمجة JavaScript',
                    'slug' => 'javascript-essentials',
                    'level' => 'beginner',
                    'course_type' => 'course',
                    'duration_hours' => 30,
                    'price' => 149.99,
                    'is_published' => true,
                ],
                [
                    'track_id' => $track2->id,
                    'title' => 'PHP Fundamentals',
                    'title_ar' => 'أساسيات PHP',
                    'description' => 'Learn PHP programming from scratch',
                    'description_ar' => 'تعلم برمجة PHP من الصفر',
                    'slug' => 'php-fundamentals',
                    'level' => 'beginner',
                    'course_type' => 'course',
                    'duration_hours' => 25,
                    'price' => 119.99,
                    'is_published' => true,
                ],
            ];

            foreach ($sampleCourses as $courseData) {
                Course::firstOrCreate(
                    ['slug' => $courseData['slug']],
                    $courseData
                );
            }
            
            $this->command->info('Sample courses created and assigned to tracks.');
        } else {
            // Distribute existing courses across tracks
            $tracks = [$track1, $track2, $track3, $track4, $track5, $track6];
            $trackIndex = 0;
            $coursesAssigned = 0;

            foreach ($existingCourses as $course) {
                // Assign course to current track
                $course->update([
                    'track_id' => $tracks[$trackIndex]->id,
                ]);
                
                $coursesAssigned++;
                
                // Move to next track (round-robin distribution)
                $trackIndex = ($trackIndex + 1) % count($tracks);
            }

            $this->command->info("Assigned {$coursesAssigned} existing courses to tracks.");
        }

        $this->command->info('Programs, Tracks, and Courses seeded successfully!');
    }
}

