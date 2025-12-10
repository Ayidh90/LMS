<template>
    <AdminLayout :page-title="course?.translated_title || course?.title || t('admin.course_details')">
        <Head :title="course?.translated_title || course?.title || t('admin.course_details')" />
        
        <div class="space-y-6" :dir="direction">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                        <Link :href="route('admin.courses.index')" class="hover:text-blue-600">{{ t('admin.courses_management') }}</Link>
                        <span>›</span>
                        <span class="text-gray-900">{{ course?.translated_title || course?.title }}</span>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ course?.translated_title || course?.title }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ t('admin.course_full_details') || 'View complete course information and statistics' }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.courses.edit', course?.slug || course?.id)"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {{ t('common.edit') }}
                    </Link>
                </div>
            </div>

            <!-- Success Message -->
            <div v-if="page.props.flash?.success" class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-green-800 font-medium">{{ page.props.flash.success }}</p>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 mb-1">{{ statistics?.total_students || 0 }}</div>
                    <div class="text-sm text-gray-500">{{ t('admin.total_students') || 'Total Students' }}</div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 mb-1">{{ statistics?.total_enrollments || 0 }}</div>
                    <div class="text-sm text-gray-500">{{ t('admin.total_enrollments') || 'Total Enrollments' }}</div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 mb-1">{{ statistics?.total_lessons || 0 }}</div>
                    <div class="text-sm text-gray-500">{{ t('admin.total_lessons') || 'Total Lessons' }}</div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 mb-1">{{ statistics?.total_batches || 0 }}</div>
                    <div class="text-sm text-gray-500">{{ t('admin.total_batches') || 'Total Batches' }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Course Image -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <img 
                            :src="course?.thumbnail_url || course?.thumbnail || '/images/default-course.avif'" 
                            :alt="course?.translated_title || course?.title"
                            class="w-full h-80 object-cover"
                            @error="handleImageError($event)"
                        />
                    </div>

                    <!-- Course Details -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ t('admin.course_details') || 'Course Details' }}</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">{{ t('courses.fields.title') || 'Title' }}</label>
                                <p class="mt-1 text-gray-900">{{ course?.translated_title || course?.title }}</p>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-500">{{ t('courses.fields.description') || 'Description' }}</label>
                                <p class="mt-1 text-gray-600 leading-relaxed">{{ course?.translated_description || course?.description || '-' }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-500">{{ t('courses.fields.level') || 'Level' }}</label>
                                    <p class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 capitalize">
                                            {{ t(`courses.levels.${course?.level}`) || course?.level }}
                                        </span>
                                    </p>
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-gray-500">{{ t('courses.fields.price') || 'Price' }}</label>
                                    <p class="mt-1 text-gray-900 font-semibold">
                                        {{ course?.price == 0 ? t('courses.free') : `${course?.price} ${t('common.currency') || 'SAR'}` }}
                                    </p>
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-gray-500">{{ t('courses.fields.duration_hours') || 'Duration' }}</label>
                                    <p class="mt-1 text-gray-900">{{ course?.duration_hours || 0 }} {{ t('common.hours') || 'hours' }}</p>
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-gray-500">{{ t('courses.status.title') || 'Status' }}</label>
                                    <p class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                            :class="course?.is_published ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'">
                                            {{ course?.is_published ? t('courses.status.published') : t('courses.status.draft') }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sections & Lessons -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-gray-900">{{ t('admin.course_content') || 'Course Content' }}</h2>
                            <Link
                                :href="route('admin.courses.sections.index', course?.slug || course?.id)"
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                            >
                                {{ t('admin.manage_sections') || 'Manage Sections' }}
                            </Link>
                        </div>

                        <div v-if="course?.sections && course.sections.length > 0" class="space-y-4">
                            <div v-for="section in course.sections" :key="section.id" class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-semibold text-gray-900">{{ section.translated_title || section.title }}</h3>
                                    <span class="text-sm text-gray-500">{{ section.lessons?.length || 0 }} {{ t('admin.lessons') || 'lessons' }}</span>
                                </div>
                                <p v-if="section.translated_description || section.description" class="text-sm text-gray-600 mb-3">
                                    {{ section.translated_description || section.description }}
                                </p>
                                <div v-if="section.lessons && section.lessons.length > 0" class="space-y-2">
                                    <div v-for="lesson in section.lessons" :key="lesson.id" class="flex items-center gap-2 text-sm text-gray-700 pl-4">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ lesson.translated_title || lesson.title }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            <p>{{ t('admin.no_sections') || 'No sections added yet' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ t('admin.quick_actions') || 'Quick Actions' }}</h3>
                        <div class="space-y-2">
                            <Link
                                :href="route('admin.courses.sections.create', course?.slug || course?.id)"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                            >
                                {{ t('admin.add_section') || 'Add Section' }}
                            </Link>
                            <Link
                                :href="route('admin.courses.lessons.create', course?.slug || course?.id)"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                            >
                                {{ t('admin.add_lesson') || 'Add Lesson' }}
                            </Link>
                            <Link
                                :href="route('admin.courses.batches.index', course?.slug || course?.id)"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                            >
                                {{ t('admin.manage_batches') || 'Manage Batches' }}
                            </Link>
                        </div>
                    </div>

                    <!-- Batches -->
                    <div v-if="course?.batches && course.batches.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ t('admin.batches') || 'Batches' }}</h3>
                        <div class="space-y-3">
                            <div v-for="batch in course.batches" :key="batch.id" class="border border-gray-200 rounded-lg p-3">
                                <div class="font-medium text-gray-900 mb-1">{{ batch.name }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ t('admin.instructor') || 'Instructor' }}: {{ batch.instructor?.name || '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { computed } from 'vue';

const props = defineProps({
    course: Object,
    statistics: Object,
});

const { t } = useTranslation();
const { route } = useRoute();
const page = usePage();

const direction = computed(() => page.props.direction || 'ltr');

const handleImageError = (event) => {
    event.target.src = '/images/default-course.avif';
};
</script>

