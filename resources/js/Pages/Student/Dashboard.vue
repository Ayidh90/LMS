<template>
    <AuthenticatedLayout>
        <Head :title="t('student.dashboard') || 'Student Dashboard'" />
        <div class="student-dashboard" :dir="direction">
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <!-- Welcome Header -->
                    <div class="welcome-card mb-6">
                        <div class="welcome-content">
                            <div class="welcome-icon">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div class="welcome-text">
                                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">{{ t('student.dashboard') || 'Student Dashboard' }}</h1>
                                <p class="text-white text-opacity-90 text-sm">{{ t('student.welcome_message') || 'Welcome back! Continue your learning journey.' }}</p>
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
                                <div class="stat-value">{{ stats.enrolled_courses }}</div>
                                <div class="stat-label">{{ t('student.enrolled_courses') || 'Enrolled Courses' }}</div>
                            </div>
                        </div>

                        <div class="stat-card stat-card-green">
                            <div class="stat-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ stats.completed_courses }}</div>
                                <div class="stat-label">{{ t('student.completed') || 'Completed' }}</div>
                            </div>
                        </div>

                        <div class="stat-card stat-card-blue">
                            <div class="stat-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ stats.in_progress }}</div>
                                <div class="stat-label">{{ t('student.in_progress') || 'In Progress' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- My Courses & Tracks -->
                    <div v-if="can('courses.view')" class="section-card">
                        <div class="section-header">
                            <h3 class="section-title">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                {{ t('student.my_courses') || 'My Courses' }}
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
                                {{ t('student.my_courses') || 'My Courses' }}
                                <span v-if="enrollments && enrollments.length > 0" class="tab-badge">{{ enrollments.length }}</span>
                            </button>
                        </div>

                        <div class="section-body">
                            <!-- Tracks with Courses View -->
                            <div v-if="activeView === 'tracks' && tracks && tracks.length > 0" class="tracks-container">
                                <div v-for="track in tracks" :key="'track-' + track.id" class="track-card-udemy">
                                    <div class="track-header-udemy">
                                        <div class="track-header-content">
                                            <div class="track-icon-udemy">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                </svg>
                                            </div>
                                            <div class="track-info-udemy">
                                                <h4 class="track-title-udemy">{{ track.translated_name || track.name }}</h4>
                                                <p v-if="track.translated_description || track.description" class="track-description-udemy">
                                                    {{ track.translated_description || track.description }}
                                                </p>
                                            </div>
                                        </div>
                                    
                                    </div>
                                    
                               

                                    <div v-if="track.courses && track.courses.length > 0" class="track-courses-container">
                                        <div class="track-courses-header-udemy">
                                            <span class="track-courses-count">{{ track.courses_count || 0 }} {{ t('admin.courses') || 'Courses' }}</span>
                                        </div>
                                        <div class="track-courses-grid-udemy">
                                            <Link
                                                v-for="course in track.courses"
                                                :key="course.id"
                                                :href="route('courses.show', course.slug || course.id)"
                                                class="course-item-udemy course-item-link"
                                            >
                                                <div class="course-item-content">
                                                    <div class="course-item-header">
                                                        <h5 class="course-item-title">{{ course.translated_title || course.title }}</h5>
                                                        <span class="course-item-progress-badge">{{ course.progress || 0 }}%</span>
                                                    </div>
                                                    <div v-if="course.course_type" class="course-item-type">
                                                        <span class="course-type-badge">{{ t(`courses.types.${course.course_type}`) || course.course_type }}</span>
                                                    </div>
                                                    <div class="course-item-progress-bar-wrapper">
                                                        <div class="course-item-progress-bar">
                                                            <div class="course-item-progress-fill" :style="`width: ${course.progress || 0}%`"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </Link>
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

                            <!-- Courses Only View -->
                            <div v-if="activeView === 'courses' && enrollments && enrollments.length > 0" class="courses-container">
                                <div v-for="enrollment in enrollments" :key="'course-' + enrollment.id" class="course-card-udemy">
                                    <div class="course-card-header-udemy">
                                        <div class="course-card-icon-udemy">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                        <div class="course-card-info-udemy">
                                            <h4 class="course-card-title-udemy">{{ enrollment.batch?.course?.translated_title || enrollment.batch?.course?.title || enrollment.course?.translated_title || enrollment.course?.title }}</h4>
                                            <div v-if="enrollment.batch?.course?.course_type || enrollment.course?.course_type" class="course-card-type-udemy">
                                                <span class="course-type-badge">{{ t(`courses.types.${enrollment.batch?.course?.course_type || enrollment.course?.course_type}`) || (enrollment.batch?.course?.course_type || enrollment.course?.course_type) }}</span>
                                            </div>
                                            <div class="course-card-meta">
                                                <div class="course-card-instructor-udemy">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    <span>{{ enrollment.batch?.instructor?.name || enrollment.course?.instructor?.name || '-' }}</span>
                                                </div>
                                                <span class="course-card-status"
                                                    :class="{
                                                        'status-completed': enrollment.status === 'completed',
                                                        'status-enrolled': enrollment.status === 'enrolled',
                                                        'status-dropped': enrollment.status === 'dropped'
                                                    }">
                                                    {{ t(`courses.status.${enrollment.status}`) || enrollment.status }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="course-card-progress-section-udemy">
                                        <div class="course-card-progress-header-udemy">
                                            <span class="course-card-progress-label">{{ t('student.progress') || 'Progress' }}</span>
                                            <span class="course-card-progress-value">{{ enrollment.progress || 0 }}%</span>
                                        </div>
                                        <div class="course-card-progress-bar-udemy">
                                            <div class="course-card-progress-fill-udemy" :style="`width: ${enrollment.progress || 0}%`"></div>
                                        </div>
                                    </div>
                                    <div class="course-card-action-udemy">
                                        <Link
                                            v-if="can('courses.view')"
                                            :href="route('courses.show', enrollment.batch?.course?.slug || enrollment.batch?.course?.id || enrollment.course?.slug || enrollment.course?.id)"
                                            class="course-card-button-udemy"
                                        >
                                            {{ t('courses.actions.continue') || 'Continue Learning' }}
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            <!-- Empty State for Courses -->
                            <div v-if="activeView === 'courses' && (!enrollments || enrollments.length === 0)" class="empty-state col-span-full">
                                <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <p class="empty-text">{{ t('student.no_enrollments') || 'You haven\'t enrolled in any courses yet.' }}</p>
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
    enrollments: Array,
    tracks: Array,
});

const { direction } = useDirection();
const { t } = useTranslation();
const { route } = useRoute();
const { can, isStudent } = usePermissions();

const activeView = ref('tracks');

// Set default view based on available data
onMounted(() => {
    if (!isStudent.value && !can('dashboard.student')) {
        router.visit(route('login'));
    }
    
    // Set default view: tracks if available, otherwise courses
    if (!props.tracks || props.tracks.length === 0) {
        activeView.value = 'courses';
    }
});
</script>

<style scoped>
.student-dashboard {
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
    gap: 1.5rem;
    position: relative;
    z-index: 1;
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

.welcome-text h1 {
    margin: 0;
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

/* Course Progress Section */
.course-progress-section {
    margin: 1.25rem 0;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 0.5rem;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.progress-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
}

.progress-percentage {
    font-size: 1rem;
    font-weight: 700;
    color: #6366f1;
}

.progress-bar-large {
    width: 100%;
    height: 12px;
    background: #e5e7eb;
    border-radius: 9999px;
    overflow: hidden;
    position: relative;
}

.progress-fill-large {
    height: 100%;
    background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 100%);
    border-radius: 9999px;
    transition: width 0.5s ease;
    position: relative;
}

.progress-fill-large::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}

.status-badge {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-completed {
    background: #d1fae5;
    color: #065f46;
}

.status-enrolled {
    background: #dbeafe;
    color: #1e40af;
}

.status-dropped {
    background: #f3f4f6;
    color: #374151;
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

/* Tracks Container - Udemy Style */
.tracks-container {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    margin-bottom: 2rem;
}

.track-card-udemy {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.track-card-udemy:hover {
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
    transform: translateY(-4px);
    border-color: #6366f1;
}

.track-header-udemy {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 2rem;
}

.track-header-content {
    display: flex;
    align-items: flex-start;
    gap: 1.25rem;
    flex: 1;
}

.track-icon-udemy {
    width: 56px;
    height: 56px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.track-info-udemy {
    flex: 1;
    min-width: 0;
}

.track-title-udemy {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    margin: 0 0 0.5rem 0;
    line-height: 1.3;
}

.track-description-udemy {
    font-size: 0.9375rem;
    color: rgba(255, 255, 255, 0.9);
    line-height: 1.6;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.track-progress-overview {
    text-align: center;
    flex-shrink: 0;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    padding: 1rem 1.5rem;
    border-radius: 0.75rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.track-progress-percentage-large {
    font-size: 2rem;
    font-weight: 700;
    color: white;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.track-progress-label-small {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.9);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
}

.track-progress-bar-container {
    padding: 1.5rem 2rem;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}

.track-progress-bar-udemy {
    width: 100%;
    height: 16px;
    background: #e5e7eb;
    border-radius: 9999px;
    overflow: hidden;
    position: relative;
}

.track-progress-fill-udemy {
    height: 100%;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    border-radius: 9999px;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
}

.track-progress-fill-udemy::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    animation: shimmer 2s infinite;
}

.track-courses-container {
    padding: 2rem;
}

.track-courses-header-udemy {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e5e7eb;
}

.track-courses-count {
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.track-courses-grid-udemy {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
}

.course-item-udemy {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.25rem;
    transition: all 0.3s ease;
}

.course-item-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

.course-item-link:hover {
    text-decoration: none;
    color: inherit;
}

.course-item-udemy:hover,
.course-item-link:hover .course-item-udemy {
    background: white;
    border-color: #6366f1;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
    transform: translateY(-2px);
    cursor: pointer;
}

.course-item-content {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.course-item-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
}

.course-item-title {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
    flex: 1;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.course-item-progress-badge {
    font-size: 0.875rem;
    font-weight: 700;
    color: #6366f1;
    background: #eef2ff;
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    flex-shrink: 0;
}

.course-item-progress-bar-wrapper {
    width: 100%;
}

.course-item-progress-bar {
    width: 100%;
    height: 8px;
    background: #e5e7eb;
    border-radius: 9999px;
    overflow: hidden;
}

.course-item-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    border-radius: 9999px;
    transition: width 0.5s ease;
}

.course-item-type {
    margin-top: 0.5rem;
}

.course-type-badge {
    display: inline-block;
    padding: 0.25rem 0.625rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.course-card-type-udemy {
    margin-bottom: 0.5rem;
}

/* Standalone Courses - Udemy Style */
.courses-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
}

.course-card-udemy {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 1rem;
    padding: 1.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: column;
}

.course-card-udemy:hover {
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
    transform: translateY(-4px);
    border-color: #6366f1;
}

.course-card-header-udemy {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.25rem;
}

.course-card-icon-udemy {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.course-card-info-udemy {
    flex: 1;
    min-width: 0;
}

.course-card-title-udemy {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin: 0 0 0.75rem 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.course-card-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.course-card-instructor-udemy {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.course-card-status {
    display: inline-block;
    padding: 0.25rem 0.625rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    width: fit-content;
}

.course-card-status.status-completed {
    background: #d1fae5;
    color: #065f46;
}

.course-card-status.status-enrolled {
    background: #dbeafe;
    color: #1e40af;
}

.course-card-status.status-dropped {
    background: #f3f4f6;
    color: #374151;
}

.course-card-progress-section-udemy {
    margin: 1.25rem 0;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 0.75rem;
}

.course-card-progress-header-udemy {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.course-card-progress-label {
    font-size: 0.8125rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.course-card-progress-value {
    font-size: 1rem;
    font-weight: 700;
    color: #6366f1;
}

.course-card-progress-bar-udemy {
    width: 100%;
    height: 12px;
    background: #e5e7eb;
    border-radius: 9999px;
    overflow: hidden;
}

.course-card-progress-fill-udemy {
    height: 100%;
    background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 100%);
    border-radius: 9999px;
    transition: width 0.5s ease;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
}

.course-card-action-udemy {
    margin-top: auto;
    padding-top: 1.25rem;
    border-top: 1px solid #e5e7eb;
}

.course-card-button-udemy {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    font-size: 0.9375rem;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.course-card-button-udemy:hover {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
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
    margin: 0;
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

    .track-courses-grid-udemy {
        grid-template-columns: 1fr;
    }

    .courses-container {
        grid-template-columns: 1fr;
    }

    .track-header-udemy {
        flex-direction: column;
        gap: 1.5rem;
    }

    .track-progress-overview {
        width: 100%;
    }

    .stat-value {
        font-size: 1.5rem;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
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

[dir="rtl"] .course-instructor svg {
    margin-left: 0.25rem;
    margin-right: 0;
}

[dir="rtl"] .course-header {
    direction: rtl;
}

[dir="rtl"] .progress-header {
    direction: rtl;
}

[dir="rtl"] .course-button svg {
    margin-left: 0;
    margin-right: 0.25rem;
    transform: scaleX(-1);
}
</style>

