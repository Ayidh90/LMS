<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Modules\Courses\Models\Course;
use Modules\Roles\Models\Role;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Permission;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    public function index()
    {
        $data = $this->dashboardService->getDashboardData();

        // Legacy stats for backward compatibility
        $stats = [
            'total_users' => $data['statistics']['users']['total'],
            'total_students' => $data['statistics']['users']['students'],
            'total_instructors' => $data['statistics']['users']['instructors'],
            'total_courses' => $data['statistics']['courses']['total'],
            'total_enrollments' => $data['statistics']['enrollments']['total'],
            'published_courses' => $data['statistics']['courses']['published'],
            'total_roles' => $data['statistics']['roles']['total'],
            'total_permissions' => $data['statistics']['permissions']['total'],
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'statistics' => $data['statistics'],
            'enrollments_chart_data' => $data['enrollments_chart_data'],
            'courses_chart_data' => $data['courses_chart_data'],
            'today_enrollments_by_course' => $data['today_enrollments_by_course'],
            'recentUsers' => $data['recent_users'],
            'recentCourses' => $data['recent_courses'],
            'recentActivities' => $data['recent_activities'],
        ]);
    }
}

