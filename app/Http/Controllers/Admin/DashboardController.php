<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Modules\Courses\Models\Course;
use Modules\Roles\Models\Role;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {
        $this->middleware(function ($request, $next) {
            /** @var User $user */
            $user = Auth::user();
            
            // Super admin can always access (no checks needed)
            if ($user->isSuperAdmin()) {
                return $next($request);
            }
            
            // Regular admin needs is_admin == 1
            // Check 1: User must have is_admin == 1
            if (!$user || !$user->is_admin || $user->is_admin == 0) {
                abort(403, __('messages.forbidden'));
            }
            
            // Check 2: User must have a role with is_admin == 1
            $hasAdminRole = false;
            $userRoles = $user->roles()->get();
            
            foreach ($userRoles as $role) {
                if ($role->is_admin == 1) {
                    $hasAdminRole = true;
                    break;
                }
            }
            
            // Also check legacy role field if it's admin
            if (!$hasAdminRole && $user->role === 'admin') {
                $legacyRole = Role::where('slug', 'admin')
                    ->orWhere('name', 'admin')
                    ->first();
                if ($legacyRole && $legacyRole->is_admin == 1) {
                    $hasAdminRole = true;
                }
            }
            
            if (!$hasAdminRole) {
                abort(403, __('messages.forbidden'));
            }
            
            if (!$user->isAdmin() && !$user->hasPermission('dashboard.admin')) {
                abort(403, __('messages.forbidden'));
            }
            
            return $next($request);
        });
    }

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
            'total_programs' => $data['statistics']['programs']['total'] ?? 0,
            'active_programs' => $data['statistics']['programs']['active'] ?? 0,
            'total_tracks' => $data['statistics']['tracks']['total'] ?? 0,
            'active_tracks' => $data['statistics']['tracks']['active'] ?? 0,
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'statistics' => $data['statistics'],
            'enrollments_chart_data' => $data['enrollments_chart_data'],
            'courses_chart_data' => $data['courses_chart_data'],
            'users_chart_data' => $data['users_chart_data'],
            'lessons_chart_data' => $data['lessons_chart_data'],
            'batches_chart_data' => $data['batches_chart_data'],
            'completions_chart_data' => $data['completions_chart_data'],
            'today_enrollments_by_course' => $data['today_enrollments_by_course'],
            'recentUsers' => $data['recent_users'],
            'recentCourses' => $data['recent_courses'],
            'recentActivities' => $data['recent_activities'],
        ]);
    }
}

