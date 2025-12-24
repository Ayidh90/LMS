<template>
    <AuthenticatedLayout>
        <Head :title="t('instructor.dashboard') || 'Instructor Dashboard'" />
        <div class="instructor-dashboard" :dir="direction">
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <!-- Welcome Header -->
                    <div class="welcome-card mb-6">
                        <div class="welcome-content">
                            <div class="welcome-icon">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="welcome-text">
                                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">{{ t('instructor.dashboard') || 'Instructor Dashboard' }}</h1>
                                <p class="text-white text-opacity-90 text-sm">{{ t('instructor.welcome_message') || 'Welcome back! Manage your courses and students.' }}</p>
                            </div>
                            <div class="welcome-actions">
                        <Link
                            v-if="can('courses.create')"
                            :href="route('courses.create')"
                                    class="welcome-button"
                        >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                            {{ t('admin.create_course') || 'Create Course' }}
                        </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="stats-grid mb-8">
                        <div class="stat-card stat-card-indigo">
                            <div class="stat-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ stats.total_courses || 0 }}</div>
                                <div class="stat-label">{{ t('instructor.total_courses') || 'Total Courses' }}</div>
                            </div>
                        </div>

                        <div class="stat-card stat-card-green">
                            <div class="stat-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ stats.published_courses || 0 }}</div>
                                <div class="stat-label">{{ t('admin.published') || 'Published' }}</div>
                            </div>
                        </div>

                        <div class="stat-card stat-card-blue">
                            <div class="stat-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ stats.total_students || 0 }}</div>
                                <div class="stat-label">{{ t('admin.total_students') || 'Total Students' }}</div>
                            </div>
                        </div>

                        <div class="stat-card stat-card-purple">
                            <div class="stat-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ stats.total_enrollments || 0 }}</div>
                                <div class="stat-label">{{ t('instructor.enrollments') || 'Enrollments' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tracks & Courses -->
                    <div v-if="can('courses.view')" class="section-card">
                        <div class="section-header">
                            <h3 class="section-title">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                {{ t('instructor.my_courses') || 'My Courses' }}
                            </h3>
                        </div>
                        
                        <!-- Tabs Navigation -->
                        <div class="tabs-navigation">
                            <button
                                @click="activeView = 'tracks'"
                                :class="activeView === 'tracks' ? 'tab-active' : 'tab-inactive'"
                                class="tab-button"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                {{ t('admin.tracks') || 'Tracks' }}
                                <span v-if="tracks && tracks.length > 0" class="tab-badge">{{ tracks.length }}</span>
                            </button>
                            <button
                                @click="activeView = 'courses'"
                                :class="activeView === 'courses' ? 'tab-active' : 'tab-inactive'"
                                class="tab-button"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                {{ t('instructor.my_courses') || 'My Courses' }}
                                <span v-if="courses && courses.length > 0" class="tab-badge">{{ courses.length }}</span>
                            </button>
                        </div>

                        <div class="section-body">
                            <!-- Tracks View -->
                            <div v-if="activeView === 'tracks' && tracks && tracks.length > 0" class="tracks-list">
                            <div class="tracks-list">
                                <div v-for="track in tracks" :key="track.id" class="track-card-enhanced">
                                    <div class="track-header-enhanced">
                                        <div class="track-header-left">
                                            <div class="track-icon-wrapper">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                </svg>
                                            </div>
                                            <div class="track-title-wrapper">
                                                <h4 class="track-title-enhanced">{{ track.translated_name || track.name }}</h4>
                                                <p v-if="track.translated_description || track.description" class="track-description-enhanced">
                                                    {{ track.translated_description || track.description }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="track-header-right">
                                            <div class="track-courses-count-enhanced">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                                <div class="track-count-info">
                                                    <span class="track-count-number">{{ track.courses?.length || 0 }}</span>
                                                    <span class="track-count-label">{{ t('admin.courses') || 'Courses' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="track.courses && track.courses.length > 0" class="track-courses-enhanced">
                                        <div class="courses-grid-enhanced">
                                            <div v-for="course in track.courses" :key="course.id" class="course-card-enhanced">
                                                <div class="course-card-content-enhanced">
                                                    <div class="course-header-enhanced">
                                                        <h5 class="course-title-enhanced">{{ course.translated_title || course.title }}</h5>
                                                        <span class="status-badge-enhanced"
                                                            :class="course.is_published ? 'status-published-enhanced' : 'status-draft-enhanced'">
                                                            {{ course.is_published ? t('courses.status.published') : t('courses.status.draft') }}
                                                        </span>
                                                    </div>
                                                    <p v-if="course.translated_description || course.description" class="course-description-enhanced">
                                                        {{ course.translated_description || course.description }}
                                                    </p>
                                                    <div class="course-info-enhanced">
                                                        <div class="course-stat-enhanced">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                            </svg>
                                                            <span>{{ course.enrollments_count || 0 }} {{ t('instructor.students') || 'students' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="course-action-enhanced">
                                                        <Link
                                                            v-if="can('courses.edit')"
                                                            :href="route('courses.edit', course.id)"
                                                            class="course-button-enhanced course-button-secondary-enhanced"
                                                        >
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            {{ t('common.edit') || 'Edit' }}
                                                        </Link>
                                                        <Link
                                                            v-if="can('courses.view')"
                                                            :href="route('courses.show', course.slug || course.id)"
                                                            class="course-button-enhanced"
                                                        >
                                                            {{ t('courses.actions.view') || 'View Course' }}
                                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                            </svg>
                                                        </Link>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="empty-state-small-enhanced">
                                        <svg class="empty-icon-small" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        <p class="empty-text-small">{{ t('admin.no_courses') || 'No courses in this track' }}</p>
                                    </div>
                                </div>
                            </div>
                            </div>
                            
                            <!-- Empty State for Tracks -->
                            <div v-if="activeView === 'tracks' && (!tracks || tracks.length === 0)" class="empty-state col-span-full">
                                <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="empty-text">{{ t('admin.no_tracks') || 'No tracks available.' }}</p>
                            </div>

                            <!-- Courses View -->
                            <div v-if="activeView === 'courses' && courses && courses.length > 0" class="courses-grid">
                                <div v-for="course in courses" :key="course.id" class="course-card">
                                    <div class="course-card-content">
                                        <div class="course-header">
                                            <h4 class="course-title">{{ course.translated_title || course.title }}</h4>
                                            <span class="status-badge"
                                                :class="course.is_published ? 'status-published' : 'status-draft'">
                                                    {{ course.is_published ? t('courses.status.published') : t('courses.status.draft') }}
                                                </span>
                                        </div>
                                        <p v-if="course.translated_description || course.description" class="course-description">
                                            {{ course.translated_description || course.description || t('instructor.no_description') || 'No description' }}
                                        </p>
                                        <div class="course-info">
                                            <div class="course-stat">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                <span>{{ course.enrollments_count || 0 }} {{ t('instructor.students') || 'students' }}</span>
                                            </div>
                                        </div>
                                        <div class="course-action">
                                        <Link
                                            v-if="can('courses.edit')"
                                            :href="route('courses.edit', course.id)"
                                                class="course-button course-button-secondary"
                                        >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            {{ t('common.edit') || 'Edit' }}
                                        </Link>
                                            <Link
                                                v-if="can('courses.view')"
                                                :href="route('courses.show', course.slug || course.id)"
                                                class="course-button"
                                            >
                                                {{ t('courses.actions.view') || 'View Course' }}
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Empty State for Courses -->
                            <div v-if="activeView === 'courses' && (!courses || courses.length === 0)" class="empty-state col-span-full">
                                <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <p class="empty-text">{{ t('instructor.no_courses') || 'No courses yet.' }}</p>
                                    <Link
                                        v-if="can('courses.create')"
                                        :href="route('courses.create')"
                                    class="empty-action-button"
                                    >
                                        {{ t('instructor.create_first_course') || 'Create your first course!' }}
                                    </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useDirection } from '@/composables/useDirection';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { usePermissions } from '@/composables/usePermissions';
import { Head, Link, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const props = defineProps({
    stats: Object,
    courses: Array,
    tracks: Array,
    user: Object,
});

const { direction } = useDirection();
const { t } = useTranslation();
const { route } = useRoute();
const { can, isInstructor } = usePermissions();

// Active view state - default to tracks if available, otherwise courses
const activeView = ref('tracks');

// Check permissions on mount
onMounted(() => {
    if (!isInstructor.value && !can('dashboard.instructor')) {
        router.visit(route('login'));
    }
    
    // Set default view: tracks if available, otherwise courses
    if (!props.tracks || props.tracks.length === 0) {
        activeView.value = 'courses';
    }
});
</script>

<style scoped>
.instructor-dashboard {
    min-height: calc(100vh - 4rem);
    padding-bottom: 2rem;
}

/* Welcome Card */
.welcome-card {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
    position: relative;
    overflow: hidden;
}

.welcome-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    filter: blur(60px);
}

.welcome-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    position: relative;
    z-index: 1;
    flex-wrap: wrap;
}

.welcome-icon {
    width: 64px;
    height: 64px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.welcome-text {
    flex: 1;
    min-width: 0;
}

.welcome-text h1 {
    margin: 0;
}

.welcome-actions {
    flex-shrink: 0;
}

.welcome-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
}

.welcome-button:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.stat-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.stat-card-indigo:hover {
    border-color: #6366f1;
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
}

.stat-card-green:hover {
    border-color: #10b981;
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2);
}

.stat-card-blue:hover {
    border-color: #3b82f6;
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2);
}

.stat-card-purple:hover {
    border-color: #9333ea;
    box-shadow: 0 10px 25px rgba(147, 51, 234, 0.2);
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: white;
}

.stat-card-indigo .stat-icon {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
}

.stat-card-green .stat-icon {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.stat-card-blue .stat-icon {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.stat-card-purple .stat-icon {
    background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%);
}

.stat-content {
    flex: 1;
    min-width: 0;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1.2;
    color: #111827;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

/* Section Card */
.section-card {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.section-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

/* Tabs Navigation */
.tabs-navigation {
    display: flex;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.tab-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border: none;
    background: transparent;
    border-radius: 0.5rem;
    font-size: 0.9375rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #6b7280;
    position: relative;
}

.tab-button:hover {
    background: #f3f4f6;
    color: #374151;
}

.tab-active {
    color: #6366f1;
    background: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.tab-active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 2px;
    background: #6366f1;
    border-radius: 2px 2px 0 0;
}

.tab-badge {
    background: #e5e7eb;
    color: #6b7280;
    padding: 0.125rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.tab-active .tab-badge {
    background: #6366f1;
    color: white;
}

.section-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center;
    margin: 0;
}

.section-body {
    padding: 1.5rem;
}

/* Courses Grid */
.courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

.course-card {
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.5rem;
    transition: all 0.3s ease;
    background: white;
}

.course-card:hover {
    border-color: #6366f1;
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.15);
    transform: translateY(-2px);
}

.course-card-content {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.course-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.course-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    line-height: 1.4;
    flex: 1;
    margin: 0;
}

.course-description {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 1rem;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.course-info {
    margin-bottom: 1.25rem;
}

.course-stat {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.course-action {
    margin-top: auto;
    display: flex;
    gap: 0.75rem;
}

.course-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
    padding: 0.625rem 1.25rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
    flex: 1;
}

.course-button:hover {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.course-button-secondary {
    background: white;
    color: #6366f1;
    border: 1px solid #6366f1;
}

.course-button-secondary:hover {
    background: #f3f4f6;
    border-color: #4f46e5;
    color: #4f46e5;
}

.status-badge {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
    flex-shrink: 0;
}

.status-published {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.6875rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    box-shadow: 0 1px 2px rgba(5, 95, 70, 0.1);
}

.status-draft {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.6875rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    box-shadow: 0 1px 2px rgba(71, 85, 105, 0.1);
}

/* Tracks Enhanced */
.tracks-list {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.track-card-enhanced {
    border: 1px solid #e5e7eb;
    border-radius: 1rem;
    padding: 0;
    background: white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    overflow: hidden;
}

.track-card-enhanced:hover {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.track-header-enhanced {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 2px solid #e2e8f0;
}

.track-header-left {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    flex: 1;
}

.track-icon-wrapper {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.track-title-wrapper {
    flex: 1;
    min-width: 0;
}

.track-title-enhanced {
    font-size: 1.375rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 0.5rem 0;
    line-height: 1.3;
}

.track-description-enhanced {
    font-size: 0.875rem;
    color: #64748b;
    line-height: 1.6;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.track-header-right {
    flex-shrink: 0;
    margin-left: 1rem;
}

.track-courses-count-enhanced {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: white;
    padding: 0.75rem 1rem;
    border-radius: 0.75rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.track-courses-count-enhanced svg {
    color: #6366f1;
    flex-shrink: 0;
}

.track-count-info {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.track-count-number {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    line-height: 1;
}

.track-count-label {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.track-courses-enhanced {
    padding: 1.5rem;
    background: #fafbfc;
}

.courses-grid-enhanced {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.25rem;
}

.course-card-enhanced {
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1.25rem;
    transition: all 0.3s ease;
    background: white;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.course-card-enhanced:hover {
    border-color: #6366f1;
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.15);
    transform: translateY(-2px);
}

.course-card-content-enhanced {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.course-header-enhanced {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.course-title-enhanced {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
    line-height: 1.4;
    flex: 1;
    margin: 0;
}

.course-description-enhanced {
    font-size: 0.8125rem;
    color: #64748b;
    margin-bottom: 1rem;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.course-info-enhanced {
    margin-bottom: 1rem;
}

.course-stat-enhanced {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    color: #64748b;
    font-weight: 500;
}

.course-action-enhanced {
    margin-top: auto;
    display: flex;
    gap: 0.5rem;
}

.course-button-enhanced {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.375rem;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.8125rem;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
    flex: 1;
}

.course-button-enhanced:hover {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.course-button-secondary-enhanced {
    background: white;
    color: #6366f1;
    border: 1.5px solid #6366f1;
}

.course-button-secondary-enhanced:hover {
    background: #f8fafc;
    border-color: #4f46e5;
    color: #4f46e5;
}

.status-badge-enhanced {
    display: inline-block;
    padding: 0.25rem 0.625rem;
    border-radius: 0.375rem;
    font-size: 0.6875rem;
    font-weight: 600;
    flex-shrink: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-published-enhanced {
    background: #d1fae5;
    color: #065f46;
}

.status-draft-enhanced {
    background: #f1f5f9;
    color: #475569;
}

.empty-state-small-enhanced {
    text-align: center;
    padding: 2rem 1rem;
    color: #94a3b8;
}

.empty-icon-small {
    width: 48px;
    height: 48px;
    margin: 0 auto 0.75rem;
    color: #cbd5e1;
}

.empty-text-small {
    font-size: 0.875rem;
    margin: 0;
    color: #94a3b8;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6b7280;
}

.empty-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 1rem;
    color: #d1d5db;
}

.empty-text {
    font-size: 0.875rem;
    margin: 0 0 1rem;
}

.empty-action-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
}

.empty-action-button:hover {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
    .welcome-content {
        flex-direction: column;
        text-align: center;
    }

    .welcome-icon {
        width: 56px;
        height: 56px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .course-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .courses-grid {
        grid-template-columns: 1fr;
    }

    .stat-value {
        font-size: 1.5rem;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
    }

    .course-action {
        flex-direction: column;
    }

    .track-header-enhanced {
        flex-direction: column;
        gap: 1rem;
    }

    .track-header-right {
        margin-left: 0;
        width: 100%;
    }

    .track-courses-count-enhanced {
        width: 100%;
        justify-content: center;
    }

    .courses-grid-enhanced {
        grid-template-columns: 1fr;
    }

    .track-icon-wrapper {
        width: 40px;
        height: 40px;
    }

    .track-title-enhanced {
        font-size: 1.125rem;
    }
}

/* RTL Support */
[dir="rtl"] .welcome-content {
    direction: rtl;
}

[dir="rtl"] .section-title svg {
    margin-left: 0.5rem;
    margin-right: 0;
}

[dir="rtl"] .course-button svg {
    margin-left: 0;
    margin-right: 0.25rem;
    transform: scaleX(-1);
}

[dir="rtl"] .course-button-secondary svg {
    transform: none;
}
</style>

