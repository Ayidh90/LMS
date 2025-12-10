<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Get recent activities from various tables
        $activities = collect();

        // Recent user registrations
        $userActivities = DB::table('users')
            ->select('id', 'name', 'email', 'created_at', DB::raw("'user_created' as type"))
            ->latest('created_at')
            ->take(10)
            ->get();

        // Recent course creations
        $courseActivities = DB::table('courses')
            ->select('id', 'title as name', 'created_at', DB::raw("'course_created' as type"))
            ->latest('created_at')
            ->take(10)
            ->get();

        // Recent enrollments
        $enrollmentActivities = DB::table('enrollments')
            ->join('users', 'enrollments.student_id', '=', 'users.id')
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->select(
                'enrollments.id',
                DB::raw("CONCAT(users.name, ' enrolled in ', courses.title) as name"),
                'enrollments.created_at',
                DB::raw("'enrollment' as type")
            )
            ->latest('enrollments.created_at')
            ->take(10)
            ->get();

        // Merge and sort all activities
        $activities = $userActivities
            ->merge($courseActivities)
            ->merge($enrollmentActivities)
            ->sortByDesc('created_at')
            ->take(20)
            ->values();

        return Inertia::render('Admin/ActivityLogs/Index', [
            'activities' => $activities,
        ]);
    }
}

