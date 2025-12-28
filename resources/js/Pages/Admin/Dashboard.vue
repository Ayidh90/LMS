<template>
  <AdminLayout :page-title="t('admin.dashboard') || 'Dashboard'">
    <Head :title="t('admin.dashboard') || 'Dashboard'" />

    <div class="container-fluid px-3 px-md-4 px-lg-5 py-4">
      <!-- Welcome Card - Bootstrap Style -->
      <div class="card border-0 shadow-lg mb-4 header-gradient-card">
        <div class="card-body p-4 p-md-5">
          <div class="row align-items-center">
            <div class="col-12 col-md-8 mb-3 mb-md-0">
              <div class="d-flex align-items-center gap-4">
                <div class="position-relative">
                  <div class="bg-white bg-opacity-20 rounded-3 p-3 d-flex align-items-center justify-content-center shadow-lg border border-white border-opacity-30" style="width: 72px; height: 72px;">
                    <i class="bi bi-person-circle fs-1 text-white"></i>
                  </div>
                  <span class="position-absolute bottom-0 end-0 translate-middle p-1 border border-3 border-white bg-success rounded-circle shadow-lg" style="width: 18px; height: 18px; animation: pulse 2s infinite;"></span>
                </div>
                <div>
                  <div class="text-white-50 small mb-2 fw-semibold">{{ t('admin.welcome_back') || 'Welcome Back' }}</div>
                  <h2 class="h3 fw-bold text-white mb-1">{{ userName }}</h2>
                  <p class="text-white-50 small mb-0">{{ t('admin.dashboard_overview') || 'Here\'s what\'s happening with your platform today' }}</p>
                </div>
              </div>
            </div>
            <div class="col-12 col-md-4 text-md-end">
              <div class="d-flex flex-column flex-md-row gap-2 justify-content-md-end">
            
              
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics Grid - Modern Compact Style -->
      <div v-if="can('dashboard.admin')" class="mb-4">
        <div class="statistics-grid">
          <!-- Programs -->
          <Link v-if="can('programs.view')" :href="safeRoute('admin.programs.index')" class="stat-item stat-item-indigo text-decoration-none">
            <div class="stat-icon">
              <i class="bi bi-diagram-3"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ totalPrograms }}</div>
              <div class="stat-label">{{ t('admin.programs_count') || t('programs.programs_management') || 'Programs' }}</div>
              <div class="stat-details">
                <span v-if="activePrograms > 0" class="stat-badge stat-badge-success">
                  <i class="bi bi-check-circle"></i> {{ activePrograms }} {{ t('admin.active') || 'Active' }}
                </span>
                <span v-if="programsTracksCount > 0" class="stat-badge stat-badge-indigo">
                  <i class="bi bi-diagram-2"></i> {{ programsTracksCount }} {{ t('admin.tracks') || t('programs.tracks') || 'Tracks' }}
                </span>
              </div>
            </div>
          </Link>

          <!-- Tracks -->
          <Link v-if="can('tracks.view')" :href="safeRoute('admin.tracks.index')" class="stat-item stat-item-emerald text-decoration-none">
            <div class="stat-icon">
              <i class="bi bi-diagram-2"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ totalTracks }}</div>
              <div class="stat-label">{{ t('admin.tracks_count') || t('tracks.tracks_management') || 'Tracks' }}</div>
              <div class="stat-details">
                <span v-if="activeTracks > 0" class="stat-badge stat-badge-success">
                  <i class="bi bi-check-circle"></i> {{ activeTracks }} {{ t('admin.active') || 'Active' }}
                </span>
                <span v-if="tracksCoursesCount > 0" class="stat-badge stat-badge-emerald">
                  <i class="bi bi-book"></i> {{ tracksCoursesCount }} {{ t('admin.courses') || t('tracks.courses') || 'Courses' }}
                </span>
              </div>
            </div>
          </Link>

          <!-- Courses -->
          <Link v-if="can('courses.view-all')" :href="safeRoute('admin.courses.index')" class="stat-item stat-item-primary text-decoration-none">
            <div class="stat-icon">
              <i class="bi bi-book"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ props.stats?.total_courses || props.statistics?.courses?.total || 0 }}</div>
              <div class="stat-label">{{ t('admin.courses_count') || 'Courses' }}</div>
              <div class="stat-details">
                <span class="stat-badge stat-badge-success">
                  <i class="bi bi-check-circle"></i> {{ props.statistics?.courses?.published || props.stats?.published_courses || 0 }} {{ t('admin.published') || 'Published' }}
                </span>
                <span class="stat-badge stat-badge-warning">
                  <i class="bi bi-clock"></i> {{ props.statistics?.courses?.pending || 0 }} {{ t('admin.pending') || 'Pending' }}
                </span>
              </div>
            </div>
          </Link>

          <!-- Enrollments -->
          <div class="stat-item stat-item-info">
            <div class="stat-icon">
              <i class="bi bi-box-arrow-in-right"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ props.statistics?.enrollments?.total || props.stats?.total_enrollments || 0 }}</div>
              <div class="stat-label">{{ t('admin.enrollments_count') || 'Enrollments' }}</div>
              <div class="stat-details">
                <span class="stat-badge stat-badge-primary">
                  <i class="bi bi-calendar-check"></i> {{ props.statistics?.enrollments?.today || 0 }} {{ t('admin.today') || 'Today' }}
                </span>
                <span class="stat-badge stat-badge-info">
                  <i class="bi bi-calendar"></i> {{ weeklyEnrollments }} {{ t('admin.this_week') || 'This Week' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Users -->
          <Link v-if="can('users.view')" :href="safeRoute('admin.users.index')" class="stat-item stat-item-success text-decoration-none">
            <div class="stat-icon">
              <i class="bi bi-people"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ props.statistics?.registered_users?.total || props.statistics?.users?.total || props.stats?.total_users || 0 }}</div>
              <div class="stat-label">{{ t('admin.registered_users_count') || 'Registered Users' }}</div>
              <div class="stat-details">
                <span class="stat-badge stat-badge-primary">
                  <i class="bi bi-calendar-check"></i> {{ props.statistics?.registered_users?.today || props.statistics?.users?.today || 0 }} {{ t('admin.registered_today') || 'Today' }}
                </span>
                <span class="stat-badge stat-badge-success">
                  <i class="bi bi-check-circle"></i> {{ props.stats?.total_users || 0 }} {{ t('admin.active') || 'Active' }}
                </span>
              </div>
            </div>
          </Link>

          <!-- Roles -->
          <Link v-if="can('roles.view')" :href="safeRoute('admin.roles.index')" class="stat-item stat-item-danger text-decoration-none">
            <div class="stat-icon">
              <i class="bi bi-shield-check"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ props.stats?.total_roles || 0 }}</div>
              <div class="stat-label">{{ t('admin.total_roles') || 'Roles' }}</div>
              <div class="stat-details">
                <span class="stat-badge stat-badge-danger">
                  <i class="bi bi-key"></i> {{ props.stats?.total_permissions || 0 }} {{ t('admin.permissions') || 'Permissions' }}
                </span>
              </div>
            </div>
          </Link>

          <!-- Lessons -->
          <div v-if="(props.statistics?.lessons?.total || 0) > 0" class="stat-item stat-item-warning">
            <div class="stat-icon">
              <i class="bi bi-file-earmark-text"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ props.statistics?.lessons?.total || 0 }}</div>
              <div class="stat-label">{{ t('admin.lessons_count') || 'Lessons' }}</div>
              <div class="stat-details">
                <span v-if="(props.statistics?.lessons?.today || 0) > 0" class="stat-badge stat-badge-warning">
                  <i class="bi bi-calendar-check"></i> {{ props.statistics?.lessons?.today || 0 }} {{ t('admin.today') || 'Today' }}
                </span>
                <span v-if="(props.statistics?.lessons?.free || 0) > 0" class="stat-badge stat-badge-success">
                  <i class="bi bi-gift"></i> {{ props.statistics?.lessons?.free || 0 }} {{ t('admin.free') || 'Free' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Batches -->
          <div v-if="(props.statistics?.batches?.total || 0) > 0" class="stat-item stat-item-purple">
            <div class="stat-icon">
              <i class="bi bi-calendar-event"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ props.statistics?.batches?.total || 0 }}</div>
              <div class="stat-label">{{ t('admin.batches_count') || 'Batches' }}</div>
              <div class="stat-details">
                <span v-if="(props.statistics?.batches?.active || 0) > 0" class="stat-badge stat-badge-success">
                  <i class="bi bi-check-circle"></i> {{ props.statistics?.batches?.active || 0 }} {{ t('admin.active') || 'Active' }}
                </span>
                <span v-if="(props.statistics?.batches?.today || 0) > 0" class="stat-badge stat-badge-purple">
                  <i class="bi bi-calendar-check"></i> {{ props.statistics?.batches?.today || 0 }} {{ t('admin.today') || 'Today' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Categories -->
          <div v-if="(props.statistics?.categories?.total || 0) > 0" class="stat-item stat-item-secondary">
            <div class="stat-icon">
              <i class="bi bi-tags"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ props.statistics?.categories?.total || 0 }}</div>
              <div class="stat-label">{{ t('admin.categories_count') || 'Categories' }}</div>
              <div class="stat-details">
                <span v-if="(props.statistics?.categories?.active || 0) > 0" class="stat-badge stat-badge-success">
                  <i class="bi bi-check-circle"></i> {{ props.statistics?.categories?.active || 0 }} {{ t('admin.active') || 'Active' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Questions -->
          <div v-if="(props.statistics?.questions?.total || 0) > 0" class="stat-item stat-item-indigo">
            <div class="stat-icon">
              <i class="bi bi-question-circle"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ props.statistics?.questions?.total || 0 }}</div>
              <div class="stat-label">{{ t('admin.questions_count') || 'Questions' }}</div>
              <div class="stat-details">
                <span v-if="(props.statistics?.questions?.today || 0) > 0" class="stat-badge stat-badge-indigo">
                  <i class="bi bi-calendar-check"></i> {{ props.statistics?.questions?.today || 0 }} {{ t('admin.today') || 'Today' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Completions -->
          <div v-if="(props.statistics?.completions?.total || 0) > 0" class="stat-item stat-item-teal">
            <div class="stat-icon">
              <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ props.statistics?.completions?.total || 0 }}</div>
              <div class="stat-label">{{ t('admin.completions_count') || 'Completions' }}</div>
              <div class="stat-details">
                <span v-if="(props.statistics?.completions?.today || 0) > 0" class="stat-badge stat-badge-teal">
                  <i class="bi bi-calendar-check"></i> {{ props.statistics?.completions?.today || 0 }} {{ t('admin.today') || 'Today' }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div v-if="can('dashboard.admin')" class="row g-4 mb-4">
        <!-- Enrollments Chart -->
        <div v-if="hasChartData(props.enrollments_chart_data?.series)" class="col-12 col-lg-6">
          <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
              <h5 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-graph-up text-info"></i>
                {{ t('admin.enrollments_last_7_days') || 'Enrollments in the Last 7 Days' }}
              </h5>
            </div>
            <div class="card-body">
              <apexchart
                type="area"
                height="300"
                :options="enrollmentsChartOptions"
                :series="enrollmentsChartSeries"
              ></apexchart>
            </div>
          </div>
        </div>

        <!-- Courses Chart -->
        <div v-if="hasChartData(props.courses_chart_data?.series)" class="col-12 col-lg-6">
          <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
              <h5 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-graph-up-arrow text-primary"></i>
                {{ t('admin.courses_last_7_days') || 'Courses in the Last 7 Days' }}
              </h5>
            </div>
            <div class="card-body">
              <apexchart
                type="area"
                height="300"
                :options="coursesChartOptions"
                :series="coursesChartSeries"
              ></apexchart>
            </div>
          </div>
        </div>

        <!-- Users Chart -->
        <div v-if="hasChartData(props.users_chart_data?.series)" class="col-12 col-lg-6">
          <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
              <h5 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-people text-success"></i>
                {{ t('admin.users_last_7_days') || 'Users Registered in the Last 7 Days' }}
              </h5>
            </div>
            <div class="card-body">
              <apexchart
                type="line"
                height="300"
                :options="usersChartOptions"
                :series="usersChartSeries"
              ></apexchart>
            </div>
          </div>
        </div>

        <!-- Lessons Chart -->
        <div v-if="hasChartData(props.lessons_chart_data?.series)" class="col-12 col-lg-6">
          <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
              <h5 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-text text-warning"></i>
                {{ t('admin.lessons_last_7_days') || 'Lessons Created in the Last 7 Days' }}
              </h5>
            </div>
            <div class="card-body">
              <apexchart
                type="bar"
                height="300"
                :options="lessonsChartOptions"
                :series="lessonsChartSeries"
              ></apexchart>
            </div>
          </div>
        </div>

        <!-- Completions Chart -->
        <div v-if="hasChartData(props.completions_chart_data?.series)" class="col-12 col-lg-6">
          <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
              <h5 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill text-teal"></i>
                {{ t('admin.completions_last_7_days') || 'Lesson Completions in the Last 7 Days' }}
              </h5>
            </div>
            <div class="card-body">
              <apexchart
                type="line"
                height="300"
                :options="completionsChartOptions"
                :series="completionsChartSeries"
              ></apexchart>
            </div>
          </div>
        </div>

        <!-- Batches Chart -->
        <div v-if="hasChartData(props.batches_chart_data?.series)" class="col-12 col-lg-6">
          <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
              <h5 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-calendar-event text-purple"></i>
                {{ t('admin.batches_last_7_days') || 'Batches Created in the Last 7 Days' }}
              </h5>
            </div>
            <div class="card-body">
              <apexchart
                type="bar"
                height="300"
                :options="batchesChartOptions"
                :series="batchesChartSeries"
              ></apexchart>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Courses Section - Bootstrap Style -->
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
          <h4 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-book text-primary"></i>
            {{ t('admin.recent_courses') || 'Recent Courses' }}
          </h4>
          <Link v-if="can('courses.view-all')" :href="safeRoute('admin.courses.index')" class="btn btn-sm btn-outline-primary">
            {{ t('common.view_all') || 'View All' }}
            <i class="bi bi-arrow-right ms-1"></i>
          </Link>
        </div>
        <div class="card-body">
          <div v-if="recentCourses && recentCourses.length > 0" class="list-group list-group-flush">
            <div 
              v-for="course in recentCourses" 
              :key="course.id" 
              class="list-group-item border rounded mb-2 d-flex align-items-center gap-3"
            >
              <div class="course-thumbnail rounded d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0 shadow-sm overflow-hidden" style="width: 64px; height: 64px; background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);">
                <span v-if="!course.thumbnail_url && !course.thumbnail">{{ (course.translated_title || course.title || 'C')?.[0]?.toUpperCase() || 'C' }}</span>
                <img v-else :src="course.thumbnail_url || course.thumbnail" :alt="course.translated_title || course.title" class="w-100 h-100 object-fit-cover" @error="handleImageError($event)" />
              </div>
              <div class="flex-grow-1 min-w-0">
                <h5 class="fw-semibold mb-1 text-truncate">{{ course.translated_title || course.title }}</h5>
                <small class="text-muted">{{ course.level ? t(`courses.levels.${course.level}`) : '' }}</small>
              </div>
              <div>
                <span 
                  class="badge"
                  :class="course.is_published ? 'bg-success' : 'bg-warning'"
                >
                  {{ getCourseStatus(course.is_published) }}
                </span>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-5">
            <i class="bi bi-book fs-1 text-muted d-block mb-3"></i>
            <p class="text-muted mb-0">{{ t('courses.no_courses') || 'No courses found' }}</p>
          </div>
        </div>
      </div>
    </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useRoute } from '@/composables/useRoute';
import { useTranslation } from '@/composables/useTranslation';

const { t } = useTranslation();
const page = usePage();

// Check permission using page.props.auth.can (from HandleInertiaRequests)
const can = (permissionOrPermissions) => {
  // Safety check: ensure auth and can exist
  if (!page.props?.auth?.can) {
    return false;
  }
  try {
    // Safety check: ensure permissionOrPermissions is valid
    if (permissionOrPermissions === null || permissionOrPermissions === undefined) {
      return false;
    }
    let permissions = [].concat(permissionOrPermissions);
    let allPermissions = page.props.auth.can;
    // Safety check: ensure allPermissions is an object
    if (!allPermissions || typeof allPermissions !== 'object') {
      return false;
    }
    let hasPermission = false;
    permissions.forEach((item) => {
      if (item && allPermissions[item]) {
        hasPermission = true;
      }
    });
    return hasPermission;
  } catch (error) {
    console.error('Error in can method:', error);
    return false;
  }
};

const props = defineProps({
  statistics: {
    type: Object,
    default: () => ({}),
  },
  stats: {
    type: Object,
    default: () => ({}),
  },
  enrollments_chart_data: {
    type: Object,
    default: () => ({ labels: [], series: [] }),
  },
  courses_chart_data: {
    type: Object,
    default: () => ({ labels: [], series: [] }),
  },
  users_chart_data: {
    type: Object,
    default: () => ({ labels: [], series: [] }),
  },
  lessons_chart_data: {
    type: Object,
    default: () => ({ labels: [], series: [] }),
  },
  batches_chart_data: {
    type: Object,
    default: () => ({ labels: [], series: [] }),
  },
  completions_chart_data: {
    type: Object,
    default: () => ({ labels: [], series: [] }),
  },
  recentActivities: {
    type: Array,
    default: () => ([]),
  },
  recentUsers: {
    type: Array,
    default: () => ([]),
  },
  recentCourses: {
    type: Array,
    default: () => ([]),
  },
});

const { route } = useRoute();

const userName = computed(() => {
  return page.props?.auth?.user?.name || 'Admin';
});

const safeRoute = (routeName, routeParams = {}) => {
  if (typeof route !== 'function' || !routeName) {
    return '#';
  }
  try {
    const routeUrl = route(routeName, routeParams);
    if (!routeUrl || routeUrl === '#') {
      return '#';
    }
    return routeUrl;
  } catch (error) {
    return '#';
  }
};

const weeklyEnrollments = computed(() => {
  if (props.enrollments_chart_data?.series) {
    return props.enrollments_chart_data.series.reduce((sum, val) => sum + (val || 0), 0);
  }
  return 0;
});

// Programs count
const totalPrograms = computed(() => {
  return props.stats?.total_programs || 
         props.statistics?.programs?.total || 
         props.statistics?.programs_count ||
         0;
});

const activePrograms = computed(() => {
  return props.statistics?.programs?.active || 
         props.stats?.active_programs || 
         0;
});

const programsTracksCount = computed(() => {
  return props.statistics?.programs?.tracks || 
         props.statistics?.programs?.total_tracks ||
         0;
});

// Tracks count
const totalTracks = computed(() => {
  return props.stats?.total_tracks || 
         props.statistics?.tracks?.total || 
         props.statistics?.tracks_count ||
         0;
});

const activeTracks = computed(() => {
  return props.statistics?.tracks?.active || 
         props.stats?.active_tracks || 
         0;
});

const tracksCoursesCount = computed(() => {
  return props.statistics?.tracks?.courses || 
         props.statistics?.tracks?.total_courses ||
         0;
});

// Format chart labels for display
const formatChartLabels = (labels) => {
  if (!labels || !Array.isArray(labels)) return [];
  return labels.map(label => {
    const date = new Date(label);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
  });
};

// Check if chart has data (not all zeros)
const hasChartData = (series) => {
  if (!series || !Array.isArray(series)) return false;
  return series.some(value => value > 0);
};

// Chart options and series
const enrollmentsChartOptions = computed(() => ({
  chart: {
    type: 'area',
    toolbar: { show: false },
    zoom: { enabled: false },
  },
  dataLabels: { enabled: false },
  stroke: { curve: 'smooth', width: 2 },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.7,
      opacityTo: 0.3,
      stops: [0, 90, 100],
    },
  },
  xaxis: {
    categories: formatChartLabels(props.enrollments_chart_data?.labels || []),
  },
  colors: ['#0dcaf0'],
  tooltip: {
    y: {
      formatter: (val) => val + ' ' + (t('admin.enrollments') || 'enrollments'),
    },
  },
}));

const enrollmentsChartSeries = computed(() => [{
  name: t('admin.enrollments') || 'Enrollments',
  data: props.enrollments_chart_data?.series || [],
}]);

const coursesChartOptions = computed(() => ({
  chart: {
    type: 'area',
    toolbar: { show: false },
    zoom: { enabled: false },
  },
  dataLabels: { enabled: false },
  stroke: { curve: 'smooth', width: 2 },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.7,
      opacityTo: 0.3,
      stops: [0, 90, 100],
    },
  },
  xaxis: {
    categories: formatChartLabels(props.courses_chart_data?.labels || []),
  },
  colors: ['#3b82f6'],
  tooltip: {
    y: {
      formatter: (val) => val + ' ' + (t('admin.courses_count') || 'courses'),
    },
  },
}));

const coursesChartSeries = computed(() => [{
  name: t('admin.courses_count') || 'Courses',
  data: props.courses_chart_data?.series || [],
}]);

const usersChartOptions = computed(() => ({
  chart: {
    type: 'line',
    toolbar: { show: false },
    zoom: { enabled: false },
  },
  dataLabels: { enabled: false },
  stroke: { curve: 'smooth', width: 3 },
  markers: { size: 5 },
  xaxis: {
    categories: formatChartLabels(props.users_chart_data?.labels || []),
  },
  colors: ['#198754'],
  tooltip: {
    y: {
      formatter: (val) => val + ' ' + (t('admin.users') || 'users'),
    },
  },
}));

const usersChartSeries = computed(() => [{
  name: t('admin.users') || 'Users',
  data: props.users_chart_data?.series || [],
}]);

const lessonsChartOptions = computed(() => ({
  chart: {
    type: 'bar',
    toolbar: { show: false },
    zoom: { enabled: false },
  },
  dataLabels: { enabled: true },
  plotOptions: {
    bar: {
      borderRadius: 4,
      horizontal: false,
    },
  },
  xaxis: {
    categories: formatChartLabels(props.lessons_chart_data?.labels || []),
  },
  colors: ['#ffc107'],
  tooltip: {
    y: {
      formatter: (val) => val + ' ' + (t('admin.lessons_count') || 'lessons'),
    },
  },
}));

const lessonsChartSeries = computed(() => [{
  name: t('admin.lessons_count') || 'Lessons',
  data: props.lessons_chart_data?.series || [],
}]);

const completionsChartOptions = computed(() => ({
  chart: {
    type: 'line',
    toolbar: { show: false },
    zoom: { enabled: false },
  },
  dataLabels: { enabled: false },
  stroke: { curve: 'smooth', width: 3 },
  markers: { size: 5 },
  xaxis: {
    categories: formatChartLabels(props.completions_chart_data?.labels || []),
  },
  colors: ['#14b8a6'],
  tooltip: {
    y: {
      formatter: (val) => val + ' ' + (t('admin.completions_count') || 'completions'),
    },
  },
}));

const completionsChartSeries = computed(() => [{
  name: t('admin.completions_count') || 'Completions',
  data: props.completions_chart_data?.series || [],
}]);

const batchesChartOptions = computed(() => ({
  chart: {
    type: 'bar',
    toolbar: { show: false },
    zoom: { enabled: false },
  },
  dataLabels: { enabled: true },
  plotOptions: {
    bar: {
      borderRadius: 4,
      horizontal: false,
    },
  },
  xaxis: {
    categories: formatChartLabels(props.batches_chart_data?.labels || []),
  },
  colors: ['#9333ea'],
  tooltip: {
    y: {
      formatter: (val) => val + ' ' + (t('admin.batches_count') || 'batches'),
    },
  },
}));

const batchesChartSeries = computed(() => [{
  name: t('admin.batches_count') || 'Batches',
  data: props.batches_chart_data?.series || [],
}]);

// Safe translation helper with fallback
const safeTranslate = (key, fallback = '') => {
  const translated = t(key);
  return translated !== key ? translated : fallback;
};

// Course status translation helper
const getCourseStatus = (isPublished) => {
  if (isPublished) {
    return safeTranslate('courses.status.published', t('admin.published') || 'Published');
  }
  return safeTranslate('courses.status.draft', t('admin.pending') || 'Draft');
};

// Handle image error
const handleImageError = (event) => {
  event.target.style.display = 'none';
  const parent = event.target.parentElement;
  if (parent && !parent.querySelector('span')) {
    const courseTitle = event.target.alt || 'C';
    const firstLetter = courseTitle[0]?.toUpperCase() || 'C';
    const span = document.createElement('span');
    span.textContent = firstLetter;
    parent.appendChild(span);
  }
};
</script>

<style scoped>
/* Header Gradient Card */
.header-gradient-card {
  background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #8b5cf6 100%);
  position: relative;
  overflow: hidden;
}

.header-gradient-card::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 400px;
  height: 400px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  transform: translate(30%, -30%);
  filter: blur(60px);
}

.header-gradient-card .card-body {
  position: relative;
  z-index: 1;
}

/* Statistics Grid Layout */
.statistics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1rem;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.25rem;
  background: #fff;
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
  transition: all 0.3s ease;
  color: inherit;
}

.stat-item:hover {
  border-color: #3b82f6;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
  transform: translateY(-2px);
}

.stat-item-primary:hover {
  border-color: #3b82f6;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.stat-item-info:hover {
  border-color: #0dcaf0;
  box-shadow: 0 4px 12px rgba(13, 202, 240, 0.15);
}

.stat-item-success:hover {
  border-color: #198754;
  box-shadow: 0 4px 12px rgba(25, 135, 84, 0.15);
}

.stat-item-danger:hover {
  border-color: #dc3545;
  box-shadow: 0 4px 12px rgba(220, 53, 69, 0.15);
}

.stat-item-warning:hover {
  border-color: #ffc107;
  box-shadow: 0 4px 12px rgba(255, 193, 7, 0.15);
}

.stat-item-purple:hover {
  border-color: #9333ea;
  box-shadow: 0 4px 12px rgba(147, 51, 234, 0.15);
}

.stat-item-secondary:hover {
  border-color: #6c757d;
  box-shadow: 0 4px 12px rgba(108, 117, 125, 0.15);
}

.stat-item-indigo:hover {
  border-color: #6366f1;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
}

.stat-item-teal:hover {
  border-color: #14b8a6;
  box-shadow: 0 4px 12px rgba(20, 184, 166, 0.15);
}

.stat-item-emerald:hover {
  border-color: #10b981;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
}

.stat-icon {
  width: 56px;
  height: 56px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 0.75rem;
  font-size: 1.5rem;
  flex-shrink: 0;
}

.stat-item-primary .stat-icon {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: #fff;
}

.stat-item-info .stat-icon {
  background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%);
  color: #fff;
}

.stat-item-success .stat-icon {
  background: linear-gradient(135deg, #198754 0%, #157347 100%);
  color: #fff;
}

.stat-item-danger .stat-icon {
  background: linear-gradient(135deg, #dc3545 0%, #bb2d3b 100%);
  color: #fff;
}

.stat-item-warning .stat-icon {
  background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
  color: #fff;
}

.stat-item-purple .stat-icon {
  background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%);
  color: #fff;
}

.stat-item-secondary .stat-icon {
  background: linear-gradient(135deg, #6c757d 0%, #5c636a 100%);
  color: #fff;
}

.stat-item-indigo .stat-icon {
  background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
  color: #fff;
}

.stat-item-teal .stat-icon {
  background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
  color: #fff;
}

.stat-item-emerald .stat-icon {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: #fff;
}

.stat-content {
  flex: 1;
  min-width: 0;
}

.stat-value {
  font-size: 1.875rem;
  font-weight: 700;
  line-height: 1.2;
  color: #111827;
  margin-bottom: 0.25rem;
}

.stat-label {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.stat-details {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.stat-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.25rem 0.5rem;
  border-radius: 0.375rem;
  font-size: 0.75rem;
  font-weight: 500;
}

.stat-badge-success {
  background-color: #d1fae5;
  color: #065f46;
}

.stat-badge-warning {
  background-color: #fef3c7;
  color: #92400e;
}

.stat-badge-primary {
  background-color: #dbeafe;
  color: #1e40af;
}

.stat-badge-info {
  background-color: #cff4fc;
  color: #055160;
}

.stat-badge-danger {
  background-color: #fee2e2;
  color: #991b1b;
}

.stat-badge-purple {
  background-color: #f3e8ff;
  color: #6b21a8;
}

.stat-badge-indigo {
  background-color: #e0e7ff;
  color: #3730a3;
}

.stat-badge-teal {
  background-color: #ccfbf1;
  color: #134e4a;
}

.stat-badge-emerald {
  background-color: #d1fae5;
  color: #065f46;
}

@media (max-width: 768px) {
  .statistics-grid {
    grid-template-columns: 1fr;
  }
  
  .stat-item {
    padding: 1rem;
  }
  
  .stat-icon {
    width: 48px;
    height: 48px;
    font-size: 1.25rem;
  }
  
  .stat-value {
    font-size: 1.5rem;
  }
}

/* Course Thumbnail */
.course-thumbnail {
  transition: all 0.3s ease;
}

.list-group-item:hover .course-thumbnail {
  transform: scale(1.05);
}

/* List Group Item Hover */
.list-group-item {
  transition: all 0.2s ease;
}

.list-group-item:hover {
  background-color: #f8f9fa;
  border-color: #0d6efd !important;
}

/* Pulse Animation */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

</style>
