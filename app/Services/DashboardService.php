<?php

namespace App\Services;

use App\Repositories\DashboardRepository;

class DashboardService
{
    public function __construct(
        private DashboardRepository $repository
    ) {}

    /**
     * Get all dashboard data
     */
    public function getDashboardData(): array
    {
        return [
            'statistics' => $this->repository->getStatistics(),
            'enrollments_chart_data' => $this->repository->getEnrollmentsChartData(),
            'courses_chart_data' => $this->repository->getCoursesChartData(),
            'today_enrollments_by_course' => $this->repository->getTodayEnrollmentsByCourse(),
            'recent_activities' => $this->repository->getRecentActivities(10),
            'recent_users' => $this->repository->getRecentUsers(5),
            'recent_courses' => $this->repository->getRecentCourses(5),
        ];
    }
}

