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

      <!-- Statistics Cards - Bootstrap Style -->
      <div v-if="can('dashboard-view')" class="row g-4 mb-4">
        <!-- Courses Card -->
        <div class="col-12 col-md-6 col-lg-3">
          <Link :href="safeRoute('admin.courses.index')" class="card border-0 shadow-sm h-100 stat-card-hover text-decoration-none text-dark">
            <div class="card-body position-relative">
              <div class="border-top border-primary border-4 rounded-top"></div>
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="bg-primary bg-opacity-10 rounded p-3">
                  <i class="bi bi-book fs-3 text-primary"></i>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary">{{ t('admin.total') || 'Total' }}</span>
              </div>
              <h3 class="h2 fw-bold mb-2">{{ props.stats?.total_courses || props.statistics?.courses?.total || 0 }}</h3>
              <p class="text-muted small mb-3">{{ t('admin.courses_count') || 'Courses' }}</p>
              <div class="border-top pt-3">
                <div class="row g-2">
                  <div class="col-6">
                    <small class="text-muted d-block">{{ t('admin.published') || 'Published' }}</small>
                    <strong class="text-success">
                      <i class="bi bi-check-circle me-1"></i>
                      {{ props.statistics?.courses?.published || props.stats?.published_courses || 0 }}
                    </strong>
                  </div>
                  <div class="col-6">
                    <small class="text-muted d-block">{{ t('admin.pending') || 'Pending' }}</small>
                    <strong class="text-warning">
                      <i class="bi bi-clock me-1"></i>
                      {{ props.statistics?.courses?.pending || 0 }}
                    </strong>
                  </div>
                </div>
              </div>
            </div>
          </Link>
        </div>

        <!-- Enrollments Card -->
        <div class="col-12 col-md-6 col-lg-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body position-relative">
              <div class="border-top border-info border-4 rounded-top"></div>
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="bg-info bg-opacity-10 rounded p-3">
                  <i class="bi bi-box-arrow-in-right fs-3 text-info"></i>
                </div>
                <span class="badge bg-info bg-opacity-10 text-info">{{ t('admin.total') || 'Total' }}</span>
              </div>
              <h3 class="h2 fw-bold mb-2">{{ props.statistics?.enrollments?.total || props.stats?.total_enrollments || 0 }}</h3>
              <p class="text-muted small mb-3">{{ t('admin.enrollments_count') || 'Enrollments' }}</p>
              <div class="border-top pt-3">
                <div class="row g-2">
                  <div class="col-6">
                    <small class="text-muted d-block">{{ t('admin.today') || 'Today' }}</small>
                    <strong class="text-primary">
                      <i class="bi bi-calendar-check me-1"></i>
                      {{ props.statistics?.enrollments?.today || 0 }}
                    </strong>
                  </div>
                  <div class="col-6">
                    <small class="text-muted d-block">{{ t('admin.this_week') || 'This Week' }}</small>
                    <strong class="text-info">
                      <i class="bi bi-calendar me-1"></i>
                      {{ weeklyEnrollments }}
                    </strong>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Registered Users Card -->
        <div class="col-12 col-md-6 col-lg-3">
          <Link :href="safeRoute('admin.users.index')" class="card border-0 shadow-sm h-100 stat-card-hover text-decoration-none text-dark">
            <div class="card-body position-relative">
              <div class="border-top border-success border-4 rounded-top"></div>
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="bg-success bg-opacity-10 rounded p-3">
                  <i class="bi bi-people fs-3 text-success"></i>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success">{{ t('admin.total') || 'Total' }}</span>
              </div>
              <h3 class="h2 fw-bold mb-2">{{ props.statistics?.registered_users?.total || props.statistics?.users?.total || props.stats?.total_users || 0 }}</h3>
              <p class="text-muted small mb-3">{{ t('admin.registered_users_count') || 'Registered Users' }}</p>
              <div class="border-top pt-3">
                <div class="row g-2">
                  <div class="col-6">
                    <small class="text-muted d-block">{{ t('admin.registered_today') || 'Today' }}</small>
                    <strong class="text-primary">
                      <i class="bi bi-calendar-check me-1"></i>
                      {{ props.statistics?.registered_users?.today || props.statistics?.users?.today || 0 }}
                    </strong>
                  </div>
                  <div class="col-6">
                    <small class="text-muted d-block">{{ t('admin.active') || 'Active' }}</small>
                    <strong class="text-success">
                      <i class="bi bi-check-circle me-1"></i>
                      {{ props.stats?.total_users || 0 }}
                    </strong>
                  </div>
                </div>
              </div>
            </div>
          </Link>
        </div>

        <!-- Roles Card -->
        <div class="col-12 col-md-6 col-lg-3">
          <Link :href="safeRoute('admin.roles.index')" class="card border-0 shadow-sm h-100 stat-card-hover text-decoration-none text-dark">
            <div class="card-body position-relative">
              <div class="border-top border-danger border-4 rounded-top"></div>
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="bg-danger bg-opacity-10 rounded p-3">
                  <i class="bi bi-shield-check fs-3 text-danger"></i>
                </div>
                <span class="badge bg-danger bg-opacity-10 text-danger">{{ t('admin.total') || 'Total' }}</span>
              </div>
              <h3 class="h2 fw-bold mb-2">{{ props.stats?.total_roles || 0 }}</h3>
              <p class="text-muted small mb-3">{{ t('admin.total_roles') || 'Roles' }}</p>
              <div class="border-top pt-3">
                <div class="row g-2">
                  <div class="col-6">
                    <small class="text-muted d-block">{{ t('admin.permissions') || 'Permissions' }}</small>
                    <strong class="text-danger">
                      <i class="bi bi-key me-1"></i>
                      {{ props.stats?.total_permissions || 0 }}
                    </strong>
                  </div>
                  <div class="col-6">
                    <small class="text-muted d-block">{{ t('admin.active') || 'Active' }}</small>
                    <strong class="text-danger">
                      <i class="bi bi-check-circle me-1"></i>
                      {{ props.stats?.total_roles || 0 }}
                    </strong>
                  </div>
                </div>
              </div>
            </div>
          </Link>
        </div>
      </div>

      <!-- Recent Courses Section - Bootstrap Style -->
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
          <h4 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-book text-primary"></i>
            {{ t('admin.recent_courses') || 'Recent Courses' }}
          </h4>
          <Link :href="safeRoute('admin.courses.index')" class="btn btn-sm btn-outline-primary">
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
import { usePermissions } from '@/composables/usePermissions';

const { t } = useTranslation();
const { can } = usePermissions();

const props = defineProps({
  statistics: {
    type: Object,
    default: () => ({}),
  },
  stats: {
    type: Object,
    default: () => ({}),
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

const page = usePage();
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
  if (props.statistics?.enrollments_chart_data?.series) {
    return props.statistics.enrollments_chart_data.series.reduce((sum, val) => sum + (val || 0), 0);
  }
  return 0;
});

// Using can from usePermissions composable

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

/* Statistics Cards Hover Effect */
.stat-card-hover {
  transition: all 0.3s ease;
}

.stat-card-hover:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
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
