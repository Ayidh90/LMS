<template>
  <AdminLayout :page-title="t('admin.dashboard')">
    <Head :title="t('admin.dashboard')" />

    <!-- Welcome Card - Enhanced -->
    <div class="welcome-card-enhanced bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-2xl shadow-2xl mb-8 border-0 overflow-hidden relative">
      <div class="absolute inset-0 bg-black/5"></div>
      <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-48 -mt-48 blur-3xl"></div>
      <div class="absolute bottom-0 left-0 w-96 h-96 bg-white/5 rounded-full -ml-48 -mb-48 blur-3xl"></div>
      <div class="relative p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div class="flex items-center gap-5 flex-1">
          <div class="relative">
            <div class="bg-white/20 backdrop-blur-lg text-white font-bold rounded-2xl flex items-center justify-center shadow-xl border-2 border-white/30" style="width: 72px; height: 72px">
              <i class="pi pi-user text-2xl"></i>
            </div>
            <span class="absolute bottom-0 right-0 translate-x-1/2 translate-y-1/2 p-1.5 border-3 border-white bg-emerald-400 rounded-full shadow-lg animate-pulse" style="width: 18px; height: 18px"></span>
          </div>
          <div>
            <div class="text-blue-100 text-sm mb-2 font-medium">{{ t('admin.welcome_back') || 'Welcome Back' }}</div>
            <div class="text-2xl font-bold text-white mb-1">{{ userName }}</div>
            <div class="text-blue-100 text-sm">{{ t('admin.dashboard_overview') || 'Here\'s what\'s happening with your platform today' }}</div>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <Link :href="safeRoute('welcome')" class="flex items-center gap-2 px-5 py-3 bg-white/10 backdrop-blur-md hover:bg-white/20 text-white rounded-xl text-sm font-semibold border border-white/20 transition-all duration-300 hover:shadow-xl hover:scale-105">
            <i class="pi pi-home text-base"></i>
            <span>{{ t('admin.main_website') || 'Main Website' }}</span>
          </Link>
          <span class="flex items-center justify-center gap-2 px-4 py-3 bg-emerald-500/20 backdrop-blur-md text-white rounded-xl text-sm font-semibold border border-emerald-300/30 shadow-lg">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
            </span>
            <span>{{ t('admin.online_now') || 'Online Now' }}</span>
          </span>
        </div>
      </div>
    </div>

    <!-- Statistics Cards - Only 4 Cards -->
    <div v-if="can('dashboard-view')" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
      <!-- Courses Card -->
      <Link :href="safeRoute('admin.courses.index')" class="stat-card stat-card-primary bg-white border border-gray-200 shadow-sm h-full rounded-xl overflow-hidden block">
        <div class="card-body relative overflow-hidden p-6">
          <div class="stat-card-pattern"></div>
          <div class="flex items-center justify-between mb-4">
            <div class="stat-icon-wrapper stat-icon-primary">
              <i class="pi pi-book text-2xl"></i>
            </div>
            <span class="badge-light-primary rounded-full px-3 py-2 text-xs font-semibold">{{ t('admin.total') }}</span>
          </div>
          <div class="stat-main-number mb-2">
            {{ props.stats?.total_courses || props.statistics?.courses?.total || 0 }}
          </div>
          <div class="stat-label mb-4">{{ t('admin.courses_count') }}</div>
          <div class="stat-footer flex gap-3">
            <div class="stat-footer-item flex-1">
              <div class="stat-footer-label">{{ t('admin.published') }}</div>
              <div class="stat-footer-value text-emerald-600">
                <i class="pi pi-check-circle mr-1"></i>
                {{ props.statistics?.courses?.published || props.stats?.published_courses || 0 }}
              </div>
            </div>
            <div class="stat-footer-item flex-1">
              <div class="stat-footer-label">{{ t('admin.pending') }}</div>
              <div class="stat-footer-value text-amber-600">
                <i class="pi pi-clock mr-1"></i>
                {{ props.statistics?.courses?.pending || 0 }}
              </div>
            </div>
          </div>
        </div>
      </Link>

      <!-- Enrollments Card -->
      <div class="stat-card stat-card-info bg-white border border-gray-200 shadow-sm h-full rounded-xl overflow-hidden cursor-default">
        <div class="card-body relative overflow-hidden p-6">
          <div class="stat-card-pattern"></div>
          <div class="flex items-center justify-between mb-4">
            <div class="stat-icon-wrapper stat-icon-info">
              <i class="pi pi-sign-in text-2xl"></i>
            </div>
            <span class="badge-light-info rounded-full px-3 py-2 text-xs font-semibold">{{ t('admin.total') }}</span>
          </div>
          <div class="stat-main-number mb-2">
            {{ props.statistics?.enrollments?.total || props.stats?.total_enrollments || 0 }}
          </div>
          <div class="stat-label mb-4">{{ t('admin.enrollments_count') }}</div>
          <div class="stat-footer flex gap-3">
            <div class="stat-footer-item flex-1">
              <div class="stat-footer-label">{{ t('admin.today') }}</div>
              <div class="stat-footer-value text-primary">
                <i class="pi pi-calendar-check mr-1"></i>
                {{ props.statistics?.enrollments?.today || 0 }}
              </div>
            </div>
            <div class="stat-footer-item flex-1">
              <div class="stat-footer-label">{{ t('admin.this_week') }}</div>
              <div class="stat-footer-value text-blue-600">
                <i class="pi pi-calendar mr-1"></i>
                {{ weeklyEnrollments }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Registered Users Card -->
      <Link :href="safeRoute('admin.users.index')" class="stat-card stat-card-success bg-white border border-gray-200 shadow-sm h-full rounded-xl overflow-hidden block">
        <div class="card-body relative overflow-hidden p-6">
          <div class="stat-card-pattern"></div>
          <div class="flex items-center justify-between mb-4">
            <div class="stat-icon-wrapper stat-icon-success">
              <i class="pi pi-users text-2xl"></i>
            </div>
            <span class="badge-light-success rounded-full px-3 py-2 text-xs font-semibold">{{ t('admin.total') }}</span>
          </div>
          <div class="stat-main-number mb-2">
            {{ props.statistics?.registered_users?.total || props.statistics?.users?.total || props.stats?.total_users || 0 }}
          </div>
          <div class="stat-label mb-4">{{ t('admin.registered_users_count') }}</div>
          <div class="stat-footer flex gap-3">
            <div class="stat-footer-item flex-1">
              <div class="stat-footer-label">{{ t('admin.registered_today') }}</div>
              <div class="stat-footer-value text-primary">
                <i class="pi pi-calendar-check mr-1"></i>
                {{ props.statistics?.registered_users?.today || props.statistics?.users?.today || 0 }}
              </div>
            </div>
            <div class="stat-footer-item flex-1">
              <div class="stat-footer-label">{{ t('admin.active') }}</div>
              <div class="stat-footer-value text-emerald-600">
                <i class="pi pi-check-circle mr-1"></i>
                {{ props.stats?.total_users || 0 }}
              </div>
            </div>
          </div>
        </div>
      </Link>

      <!-- Roles Card -->
      <Link :href="safeRoute('admin.roles.index')" class="stat-card stat-card-rose bg-white border border-gray-200 shadow-sm h-full rounded-xl overflow-hidden block">
        <div class="card-body relative overflow-hidden p-6">
          <div class="stat-card-pattern"></div>
          <div class="flex items-center justify-between mb-4">
            <div class="stat-icon-wrapper stat-icon-rose">
              <i class="pi pi-shield text-2xl"></i>
            </div>
            <span class="badge-light-rose rounded-full px-3 py-2 text-xs font-semibold">{{ t('admin.total') }}</span>
          </div>
          <div class="stat-main-number mb-2">
            {{ props.stats?.total_roles || 0 }}
          </div>
          <div class="stat-label mb-4">{{ t('admin.total_roles') }}</div>
          <div class="stat-footer flex gap-3">
            <div class="stat-footer-item flex-1">
              <div class="stat-footer-label">{{ t('admin.permissions') }}</div>
              <div class="stat-footer-value text-rose-600">
                <i class="pi pi-key mr-1"></i>
                {{ props.stats?.total_permissions || 0 }}
              </div>
            </div>
            <div class="stat-footer-item flex-1">
              <div class="stat-footer-label">{{ t('admin.active') }}</div>
              <div class="stat-footer-value text-rose-500">
                <i class="pi pi-check-circle mr-1"></i>
                {{ props.stats?.total_roles || 0 }}
              </div>
            </div>
          </div>
        </div>
      </Link>
    </div>

    <!-- Recent Courses Section -->
    <div class="recent-courses-section bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
      <div class="section-header border-b border-gray-200 bg-gradient-to-r from-gray-50 to-slate-50 py-4 px-6 flex items-center justify-between">
        <h4 class="section-title text-lg font-semibold text-gray-900 flex items-center gap-2">
          <i class="pi pi-book text-lg text-gray-700"></i>
          <span>{{ t('admin.recent_courses') }}</span>
        </h4>
        <Link :href="safeRoute('admin.courses.index')" class="view-all-link text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors flex items-center gap-1">
          <span>{{ t('common.view_all') }}</span>
          <span class="arrow-icon">→</span>
        </Link>
      </div>
      <div class="section-content p-6">
        <div v-if="recentCourses && recentCourses.length > 0" class="courses-list space-y-3">
          <div 
            v-for="course in recentCourses" 
            :key="course.id" 
            class="course-item flex items-center gap-4 p-4 border border-gray-100 rounded-xl transition-all duration-300 hover:shadow-md"
          >
            <div class="course-thumbnail w-16 h-16 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-md overflow-hidden">
              <span v-if="!course.thumbnail_url && !course.thumbnail">{{ (course.translated_title || course.title || 'C')?.[0]?.toUpperCase() || 'C' }}</span>
              <img v-else :src="course.thumbnail_url || course.thumbnail" :alt="course.translated_title || course.title" class="w-full h-full object-cover rounded-lg" @error="handleImageError($event)" />
            </div>
            <div class="course-info flex-1 min-w-0">
              <h5 class="course-title font-semibold text-gray-900 truncate mb-1">{{ course.translated_title || course.title }}</h5>
              <p class="course-meta text-sm text-gray-500 truncate">
                <span class="mr-2">{{ course.level ? t(`courses.levels.${course.level}`) : '' }}</span>
            
              </p>
            </div>
            <div class="course-actions flex items-center gap-3">
              <span 
                class="status-badge px-3 py-1.5 rounded-full text-xs font-semibold transition-all duration-200"
                :class="course.is_published ? 'status-published bg-emerald-50 text-emerald-700 border border-emerald-200' : 'status-draft bg-amber-50 text-amber-700 border border-amber-200'"
              >
                {{ getCourseStatus(course.is_published) }}
              </span>
            </div>
          </div>
        </div>
        <div v-else class="empty-state text-center py-12">
          <div class="empty-icon mb-4">
            <i class="pi pi-book text-4xl text-gray-300"></i>
          </div>
          <p class="empty-text text-gray-500 text-sm">{{ t('courses.no_courses') }}</p>
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
/* ============================================
   CSS Variables & Base Styles
   ============================================ */
* {
  font-family: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

:root {
  /* Primary Colors */
  --color-primary: #2563eb;
  --color-primary-light: #3b82f6;
  --color-primary-dark: #1d4ed8;
  
  /* Info Colors */
  --color-info: #4f46e5;
  --color-info-light: #6366f1;
  --color-info-dark: #4338ca;
  
  /* Success Colors */
  --color-success: #10b981;
  --color-success-light: #34d399;
  --color-success-dark: #059669;
  
  /* Warning Colors */
  --color-warning: #f59e0b;
  --color-warning-light: #fbbf24;
  --color-warning-dark: #d97706;
  
  /* Rose Colors */
  --color-rose: #f43f5e;
  --color-rose-light: #fb7185;
  --color-rose-dark: #e11d48;
  
  /* Gray Scale */
  --color-gray-900: #111827;
  --color-gray-800: #1f2937;
  --color-gray-700: #374151;
  --color-gray-600: #4b5563;
  --color-gray-500: #6b7280;
  --color-gray-400: #9ca3af;
  --color-gray-300: #d1d5db;
  --color-gray-200: #e5e7eb;
  --color-gray-100: #f3f4f6;
  --color-gray-50: #f9fafb;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  
  /* Transitions */
  --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
  --transition-base: 300ms cubic-bezier(0.4, 0, 0.2, 1);
  --transition-slow: 500ms cubic-bezier(0.4, 0, 0.2, 1);
}

/* ============================================
   Welcome Card Styles - Enhanced
   ============================================ */
.welcome-card-enhanced {
  position: relative;
  transition: all var(--transition-base);
  backdrop-filter: blur(10px);
  overflow: hidden;
}

.welcome-card {
  position: relative;
  transition: all var(--transition-base);
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #f1f5f9 100%);
  border: 1px solid rgba(0, 0, 0, 0.06);
  backdrop-filter: blur(10px);
  overflow: hidden;
}

.welcome-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--color-primary) 0%, var(--color-info) 50%, var(--color-success) 100%);
  opacity: 0;
  transition: opacity var(--transition-base);
}

.welcome-card::after {
  content: '';
  position: absolute;
  top: -50%;
  right: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(37, 99, 235, 0.03) 0%, transparent 70%);
  opacity: 0;
  transition: opacity var(--transition-slow);
  pointer-events: none;
}

.welcome-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
  border-color: rgba(0, 0, 0, 0.1);
}

.welcome-card:hover::before {
  opacity: 1;
}

.welcome-card:hover::after {
  opacity: 1;
}

/* ============================================
   Statistics Card Styles
   ============================================ */
.stat-card {
  border-radius: 1rem !important;
  transition: all var(--transition-slow);
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
  border: 1px solid rgba(0, 0, 0, 0.06);
  box-shadow: var(--shadow-sm);
  text-decoration: none;
  color: inherit;
  display: block;
  isolation: isolate;
}

.stat-card::after {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: 1rem;
  padding: 1px;
  background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.5), transparent);
  -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  -webkit-mask-composite: xor;
  mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  mask-composite: exclude;
  opacity: 0;
  transition: opacity var(--transition-base);
}

.stat-card:not(.cursor-default):hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: var(--shadow-2xl);
  border-color: rgba(0, 0, 0, 0.12);
}

.stat-card:not(.cursor-default):hover::after {
  opacity: 1;
}

.stat-card:not(.cursor-default):active {
  transform: translateY(-4px) scale(1.01);
}

/* Top accent bar */
.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  z-index: 2;
  border-radius: 1rem 1rem 0 0;
  transition: height var(--transition-base), opacity var(--transition-base);
  opacity: 0.9;
}

.stat-card:hover::before {
  height: 6px;
  opacity: 1;
}

.stat-card-primary::before {
  background: linear-gradient(90deg, var(--color-primary) 0%, var(--color-primary-light) 50%, var(--color-primary-dark) 100%);
}

.stat-card-info::before {
  background: linear-gradient(90deg, var(--color-info) 0%, var(--color-info-light) 50%, var(--color-info-dark) 100%);
}

.stat-card-success::before {
  background: linear-gradient(90deg, var(--color-success) 0%, var(--color-success-light) 50%, var(--color-success-dark) 100%);
}

.stat-card-rose::before {
  background: linear-gradient(90deg, var(--color-rose) 0%, var(--color-rose-light) 50%, var(--color-rose-dark) 100%);
}

/* Background pattern */
.stat-card-pattern {
  position: absolute;
  top: -60px;
  right: -60px;
  width: 200px;
  height: 200px;
  opacity: 0.03;
  background-image: radial-gradient(circle at 2px 2px, currentColor 1px, transparent 0);
  background-size: 24px 24px;
  z-index: 0;
  transition: opacity var(--transition-base), transform var(--transition-slow);
}

.stat-card:hover .stat-card-pattern {
  opacity: 0.06;
  transform: scale(1.1) rotate(5deg);
}

/* Icon wrapper */
.stat-icon-wrapper {
  width: 64px;
  height: 64px;
  border-radius: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all var(--transition-slow);
  position: relative;
  overflow: hidden;
  box-shadow: var(--shadow-md);
  z-index: 1;
}

.stat-icon-wrapper::before {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: 1rem;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.3), transparent);
  opacity: 0;
  transition: opacity var(--transition-base);
}

.stat-icon-wrapper::after {
  content: '';
  position: absolute;
  inset: -2px;
  border-radius: 1rem;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.5), transparent);
  opacity: 0;
  transition: opacity var(--transition-base);
  z-index: -1;
}

.stat-card:hover .stat-icon-wrapper::before,
.stat-card:hover .stat-icon-wrapper::after {
  opacity: 1;
}

.stat-icon-primary {
  background: linear-gradient(135deg, rgba(37, 99, 235, 0.15) 0%, rgba(37, 99, 235, 0.08) 100%);
  color: var(--color-primary);
}

.stat-icon-info {
  background: linear-gradient(135deg, rgba(79, 70, 229, 0.15) 0%, rgba(79, 70, 229, 0.08) 100%);
  color: var(--color-info);
}

.stat-icon-success {
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(16, 185, 129, 0.08) 100%);
  color: var(--color-success);
}

.stat-icon-rose {
  background: linear-gradient(135deg, rgba(244, 63, 94, 0.15) 0%, rgba(244, 63, 94, 0.08) 100%);
  color: var(--color-rose);
}

.stat-card:hover .stat-icon-wrapper {
  transform: scale(1.15) rotate(8deg);
  box-shadow: var(--shadow-xl);
}

/* Main number styling */
.stat-main-number {
  font-size: 2.75rem;
  font-weight: 800;
  line-height: 1;
  letter-spacing: -0.04em;
  font-family: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  background: linear-gradient(135deg, var(--color-gray-900) 0%, var(--color-gray-700) 50%, var(--color-gray-600) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  transition: all var(--transition-base);
  position: relative;
  z-index: 1;
}

.stat-card:hover .stat-main-number {
  transform: scale(1.05);
  background: linear-gradient(135deg, var(--color-gray-900) 0%, var(--color-gray-800) 50%, var(--color-gray-700) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Label styling */
.stat-label {
  font-size: 0.875rem;
  color: var(--color-gray-600);
  font-weight: 500;
  letter-spacing: 0.01em;
  transition: color var(--transition-base);
}

.stat-card:hover .stat-label {
  color: var(--color-gray-700);
}

/* Footer section */
.stat-footer {
  padding-top: 1.25rem;
  margin-top: 1.75rem;
  border-top: 2px solid rgba(0, 0, 0, 0.05);
  position: relative;
  background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.015) 100%);
  border-radius: 0.5rem;
  padding: 1.25rem 0.75rem;
  transition: all var(--transition-base);
}

.stat-card:hover .stat-footer {
  border-top-color: rgba(0, 0, 0, 0.08);
  background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.025) 100%);
}

.stat-footer-item {
  flex: 1;
  position: relative;
}

.stat-footer-item:not(:last-child)::after {
  content: '';
  position: absolute;
  right: 0;
  top: 0;
  bottom: 0;
  width: 1px;
  background: linear-gradient(180deg, transparent, rgba(0, 0, 0, 0.1), transparent);
  transition: opacity var(--transition-base);
}

.stat-card:hover .stat-footer-item:not(:last-child)::after {
  opacity: 0.5;
}

.stat-footer-label {
  font-size: 0.75rem;
  color: var(--color-gray-500);
  margin-bottom: 0.375rem;
  font-weight: 500;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  transition: color var(--transition-base);
}

.stat-card:hover .stat-footer-label {
  color: var(--color-gray-600);
}

.stat-footer-value {
  font-size: 1rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.375rem;
  color: var(--color-gray-800);
  transition: all var(--transition-base);
}

.stat-card:hover .stat-footer-value {
  transform: translateX(2px);
  color: var(--color-gray-900);
}

.stat-footer-value i {
  transition: transform var(--transition-base);
}

.stat-card:hover .stat-footer-value i {
  transform: scale(1.1);
}

/* ============================================
   Badge Styles
   ============================================ */
.badge-light-primary {
  background: rgba(37, 99, 235, 0.12) !important;
  color: var(--color-primary) !important;
  border: 1px solid rgba(37, 99, 235, 0.25) !important;
  font-weight: 600;
  transition: all var(--transition-base);
  backdrop-filter: blur(4px);
}

.stat-card:hover .badge-light-primary {
  background: rgba(37, 99, 235, 0.18) !important;
  border-color: rgba(37, 99, 235, 0.35) !important;
  transform: scale(1.05);
}

.badge-light-info {
  background: rgba(79, 70, 229, 0.12) !important;
  color: var(--color-info) !important;
  border: 1px solid rgba(79, 70, 229, 0.25) !important;
  font-weight: 600;
  transition: all var(--transition-base);
  backdrop-filter: blur(4px);
}

.stat-card:hover .badge-light-info {
  background: rgba(79, 70, 229, 0.18) !important;
  border-color: rgba(79, 70, 229, 0.35) !important;
  transform: scale(1.05);
}

.badge-light-success {
  background: rgba(16, 185, 129, 0.12) !important;
  color: var(--color-success) !important;
  border: 1px solid rgba(16, 185, 129, 0.25) !important;
  font-weight: 600;
  transition: all var(--transition-base);
  backdrop-filter: blur(4px);
}

.stat-card:hover .badge-light-success {
  background: rgba(16, 185, 129, 0.18) !important;
  border-color: rgba(16, 185, 129, 0.35) !important;
  transform: scale(1.05);
}

.badge-light-rose {
  background: rgba(244, 63, 94, 0.12) !important;
  color: var(--color-rose) !important;
  border: 1px solid rgba(244, 63, 94, 0.25) !important;
  font-weight: 600;
  transition: all var(--transition-base);
  backdrop-filter: blur(4px);
}

.stat-card:hover .badge-light-rose {
  background: rgba(244, 63, 94, 0.18) !important;
  border-color: rgba(244, 63, 94, 0.35) !important;
  transform: scale(1.05);
}

/* ============================================
   Utility Classes
   ============================================ */
.text-primary {
  color: var(--color-primary) !important;
  transition: color var(--transition-base);
}

.stat-card:hover .text-primary {
  color: var(--color-primary-dark) !important;
}

/* ============================================
   Recent Courses Section
   ============================================ */
.recent-courses-section {
  transition: all var(--transition-base);
  position: relative;
}

.recent-courses-section:hover {
  box-shadow: var(--shadow-md);
}

.section-header {
  position: relative;
  overflow: hidden;
}

.section-header::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, transparent, rgba(37, 99, 235, 0.3), transparent);
  opacity: 0;
  transition: opacity var(--transition-base);
}

.recent-courses-section:hover .section-header::after {
  opacity: 1;
}

.section-title {
  position: relative;
  z-index: 1;
}

.section-title i {
  transition: transform var(--transition-base);
}

.recent-courses-section:hover .section-title i {
  transform: scale(1.1) rotate(-5deg);
}

.view-all-link {
  position: relative;
  z-index: 1;
  padding: 0.375rem 0.75rem;
  border-radius: 0.5rem;
}

.view-all-link:hover {
  background: rgba(37, 99, 235, 0.05);
}

.view-all-link .arrow-icon {
  transition: transform var(--transition-base);
  display: inline-block;
}

.view-all-link:hover .arrow-icon {
  transform: translateX(4px);
}

.section-content {
  position: relative;
}

.courses-list {
  position: relative;
}

/* Course Item Styles */
.course-item {
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
  border: 1px solid rgba(0, 0, 0, 0.06);
}

.course-item::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 4px;
  background: linear-gradient(180deg, var(--color-primary), var(--color-info));
  transform: scaleY(0);
  transition: transform var(--transition-base);
  transform-origin: top;
  border-radius: 0 0.5rem 0.5rem 0;
}

.course-item:hover::before {
  transform: scaleY(1);
}

.course-item:hover {
  border-color: rgba(37, 99, 235, 0.2);
  background: linear-gradient(135deg, rgba(37, 99, 235, 0.02) 0%, rgba(79, 70, 229, 0.02) 50%, #ffffff 100%);
  transform: translateX(4px);
  box-shadow: var(--shadow-sm);
}

.course-thumbnail {
  position: relative;
  overflow: hidden;
  transition: all var(--transition-base);
  border: 2px solid rgba(255, 255, 255, 0.3);
}

.course-item:hover .course-thumbnail {
  transform: scale(1.05) rotate(2deg);
  box-shadow: var(--shadow-md);
}

.course-thumbnail img {
  transition: transform var(--transition-slow);
}

.course-item:hover .course-thumbnail img {
  transform: scale(1.1);
}

.course-info {
  position: relative;
}

.course-title {
  transition: color var(--transition-base);
  line-height: 1.4;
}

.course-item:hover .course-title {
  color: var(--color-primary);
}

.course-instructor {
  transition: color var(--transition-base);
}

.course-item:hover .course-instructor {
  color: var(--color-gray-600);
}

.course-actions {
  position: relative;
  z-index: 1;
}

.status-badge {
  position: relative;
  overflow: hidden;
  backdrop-filter: blur(4px);
  transition: all var(--transition-base);
  white-space: nowrap;
}

.status-badge::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.3), transparent);
  opacity: 0;
  transition: opacity var(--transition-base);
}

.status-badge:hover::before {
  opacity: 1;
}

.status-published {
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.12), rgba(16, 185, 129, 0.08)) !important;
}

.course-item:hover .status-published {
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.18), rgba(16, 185, 129, 0.12)) !important;
  transform: scale(1.05);
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
}

.status-draft {
  background: linear-gradient(135deg, rgba(245, 158, 11, 0.12), rgba(245, 158, 11, 0.08)) !important;
}

.course-item:hover .status-draft {
  background: linear-gradient(135deg, rgba(245, 158, 11, 0.18), rgba(245, 158, 11, 0.12)) !important;
  transform: scale(1.05);
  box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2);
}

.edit-button {
  position: relative;
  overflow: hidden;
}

.edit-button::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), transparent);
  opacity: 0;
  transition: opacity var(--transition-base);
  border-radius: 0.5rem;
}

.edit-button:hover::before {
  opacity: 1;
}

.edit-button svg {
  position: relative;
  z-index: 1;
  transition: transform var(--transition-base);
}

.edit-button:hover svg {
  transform: scale(1.1) rotate(5deg);
}

/* Empty State */
.empty-state {
  position: relative;
}

.empty-icon {
  transition: transform var(--transition-base);
}

.empty-state:hover .empty-icon {
  transform: scale(1.1) rotate(-5deg);
}

.empty-text {
  transition: color var(--transition-base);
}

.empty-state:hover .empty-text {
  color: var(--color-gray-600);
}

/* ============================================
   RTL Support
   ============================================ */
[dir="rtl"] .course-item::before {
  left: auto;
  right: 0;
  border-radius: 0.5rem 0 0 0.5rem;
}

[dir="rtl"] .course-item:hover {
  transform: translateX(-4px);
}

[dir="rtl"] .view-all-link .arrow-icon {
  transform: scaleX(-1);
}

[dir="rtl"] .view-all-link:hover .arrow-icon {
  transform: scaleX(-1) translateX(-4px);
}

[dir="rtl"] .stat-footer-item:not(:last-child)::after {
  right: auto;
  left: 0;
}

/* ============================================
   Responsive Adjustments
   ============================================ */
@media (max-width: 768px) {
  .stat-card:not(.cursor-default):hover {
    transform: translateY(-4px) scale(1.01);
  }
  
  .stat-main-number {
    font-size: 2.25rem;
  }
  
  .stat-icon-wrapper {
    width: 56px;
    height: 56px;
  }
  
  .course-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .course-actions {
    width: 100%;
    justify-content: space-between;
  }
  
  .welcome-card {
    padding: 1rem;
  }
}

/* ============================================
   Animation Keyframes
   ============================================ */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.stat-card {
  animation: fadeInUp 0.6s ease-out backwards;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }
</style>
