<?php

namespace App\Repositories;

use Modules\Courses\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use Modules\Roles\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    /**
     * Get dashboard statistics
     */
    public function getStatistics(): array
    {
        return [
            'courses' => [
                'total' => Course::count(),
                'published' => Course::where('is_published', true)->count(),
                'pending' => Course::where('is_published', false)->count(),
                'today' => Course::whereDate('created_at', today())->count(),
            ],
            'enrollments' => [
                'total' => Enrollment::count(),
                'today' => Enrollment::whereDate('created_at', today())->count(),
            ],
            'users' => [
                'total' => User::count(),
                'today' => User::whereDate('created_at', today())->count(),
                'students' => User::where('role', 'student')->count(),
                'instructors' => User::where('role', 'instructor')->count(),
            ],
            'roles' => [
                'total' => Role::count(),
            ],
            'permissions' => [
                'total' => Permission::count(),
            ],
        ];
    }

    /**
     * Get enrollments chart data for last 7 days
     */
    public function getEnrollmentsChartData(): array
    {
        $labels = [];
        $series = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('Y-m-d');
            $series[] = Enrollment::whereDate('created_at', $date->format('Y-m-d'))->count();
        }
        
        return [
            'labels' => $labels,
            'series' => $series,
        ];
    }

    /**
     * Get courses chart data for last 7 days
     */
    public function getCoursesChartData(): array
    {
        $labels = [];
        $series = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('Y-m-d');
            $series[] = Course::whereDate('created_at', $date->format('Y-m-d'))->count();
        }
        
        return [
            'labels' => $labels,
            'series' => $series,
        ];
    }

    /**
     * Get today's enrollments by course
     */
    public function getTodayEnrollmentsByCourse(): array
    {
        return Enrollment::whereDate('enrollments.created_at', today())
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->selectRaw('enrollments.course_id, courses.title as course_name, courses.title_ar as course_name_ar, count(*) as count')
            ->groupBy('enrollments.course_id', 'courses.title', 'courses.title_ar')
            ->get()
            ->map(function ($item) {
                $locale = app()->getLocale();
                return [
                    'course_id' => $item->course_id,
                    'course_name' => ($locale === 'ar' && $item->course_name_ar) ? $item->course_name_ar : $item->course_name,
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities(int $limit = 10): array
    {
        $activities = collect();
        
        // Recent user registrations
        $userActivities = User::select('id', 'name', 'email', 'created_at')
            ->selectRaw("'user_created' as type")
            ->latest('created_at')
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name . ' registered',
                    'type' => 'user_created',
                    'created_at' => $user->created_at,
                ];
            });

        // Recent course creations
        $courseActivities = Course::select('id', 'title', 'created_at')
            ->selectRaw("'course_created' as type")
            ->latest('created_at')
            ->take(5)
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'name' => 'Course "' . $course->title . '" created',
                    'type' => 'course_created',
                    'created_at' => $course->created_at,
                ];
            });

        // Recent enrollments
        $enrollmentActivities = Enrollment::with(['student', 'batch.course'])
            ->latest('created_at')
            ->take(5)
            ->get()
            ->map(function ($enrollment) {
                $courseTitle = $enrollment->batch?->course?->title ?? 'Course';
                return [
                    'id' => $enrollment->id,
                    'name' => ($enrollment->student->name ?? 'User') . ' enrolled in "' . $courseTitle . '"',
                    'type' => 'enrollment',
                    'created_at' => $enrollment->created_at,
                ];
            });

        return $userActivities
            ->merge($courseActivities)
            ->merge($enrollmentActivities)
            ->sortByDesc('created_at')
            ->take($limit)
            ->values()
            ->toArray();
    }

    /**
     * Get recent users
     */
    public function getRecentUsers(int $limit = 5): array
    {
        return User::latest()->take($limit)->get()->toArray();
    }

    /**
     * Get recent courses
     */
    public function getRecentCourses(int $limit = 5): array
    {
        return Course::latest()->take($limit)->get()->toArray(); // Removed instructor loading
    }
}

