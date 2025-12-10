<template>
    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50" :dir="direction">
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <h1 class="text-3xl font-bold text-gray-900 mb-6">Student Dashboard</h1>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="text-2xl font-bold text-indigo-600">{{ stats.enrolled_courses }}</div>
                                <div class="text-sm font-medium text-gray-500 mt-1">Enrolled Courses</div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="text-2xl font-bold text-green-600">{{ stats.completed_courses }}</div>
                                <div class="text-sm font-medium text-gray-500 mt-1">Completed</div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="text-2xl font-bold text-blue-600">{{ stats.in_progress }}</div>
                                <div class="text-sm font-medium text-gray-500 mt-1">In Progress</div>
                            </div>
                        </div>
                    </div>

                    <!-- My Courses -->
                    <div class="bg-white shadow rounded-lg mb-6">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">My Courses</h3>
                            <div class="space-y-4">
                                <div v-for="enrollment in enrollments" :key="enrollment.id" class="border-b pb-4 last:border-b-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-md font-medium text-gray-900">{{ enrollment.course?.title }}</h4>
                                            <p class="text-sm text-gray-500 mt-1">By {{ enrollment.course?.instructor?.name }}</p>
                                            <div class="mt-2">
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-indigo-600 h-2 rounded-full" :style="`width: ${enrollment.progress}%`"></div>
                                                </div>
                                                <span class="text-xs text-gray-500 mt-1">{{ enrollment.progress }}% Complete</span>
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded"
                                            :class="{
                                                'bg-green-100 text-green-800': enrollment.status === 'completed',
                                                'bg-blue-100 text-blue-800': enrollment.status === 'enrolled',
                                                'bg-gray-100 text-gray-800': enrollment.status === 'dropped'
                                            }">
                                            {{ enrollment.status }}
                                        </span>
                                    </div>
                                </div>
                                <div v-if="enrollments.length === 0" class="text-center py-8 text-gray-500">
                                    You haven't enrolled in any courses yet.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Available Courses -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Available Courses</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div v-for="course in availableCourses" :key="course.id" class="border rounded-lg p-4 hover:shadow-lg transition-shadow">
                                    <h4 class="text-md font-medium text-gray-900 mb-2">{{ course.translated_title || course.title }}</h4>
                                    <p class="text-sm text-gray-500 mb-2">{{ t('courses.fields.instructor') }}: {{ course.instructor?.name }}</p>
                                    <div class="flex justify-end items-center mt-4">
                                        <Link
                                            :href="route('courses.show', course.slug || course.id)"
                                            class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700"
                                        >
                                            {{ t('courses.actions.view') }}
                                        </Link>
                                    </div>
                                </div>
                                <div v-if="availableCourses.length === 0" class="col-span-full text-center py-8 text-gray-500">
                                    No courses available at the moment.
                                </div>
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
import { Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
    enrollments: Array,
    availableCourses: Array,
});

const { direction } = useDirection();
const { t } = useTranslation();
const { route } = useRoute();
</script>

