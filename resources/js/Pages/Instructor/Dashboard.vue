<template>
    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50" :dir="direction">
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold text-gray-900">Instructor Dashboard</h1>
                        <Link
                            :href="route('courses.create')"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700"
                        >
                            Create Course
                        </Link>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="text-2xl font-bold text-indigo-600">{{ stats.total_courses }}</div>
                                <div class="text-sm font-medium text-gray-500 mt-1">Total Courses</div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="text-2xl font-bold text-green-600">{{ stats.published_courses }}</div>
                                <div class="text-sm font-medium text-gray-500 mt-1">Published</div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="text-2xl font-bold text-blue-600">{{ stats.total_students }}</div>
                                <div class="text-sm font-medium text-gray-500 mt-1">Total Students</div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="text-2xl font-bold text-purple-600">{{ stats.total_enrollments }}</div>
                                <div class="text-sm font-medium text-gray-500 mt-1">Enrollments</div>
                            </div>
                        </div>
                    </div>

                    <!-- Courses List -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">My Courses</h3>
                            <div class="space-y-4">
                                <div v-for="course in courses" :key="course.id" class="border-b pb-4 last:border-b-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-md font-medium text-gray-900">{{ course.title }}</h4>
                                            <p class="text-sm text-gray-500 mt-1">{{ course.description || 'No description' }}</p>
                                            <div class="flex items-center gap-4 mt-2">
                                                <span class="text-xs text-gray-400">{{ course.enrollments_count }} students</span>
                                                <span class="text-xs px-2 py-1 rounded"
                                                    :class="course.is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                                    {{ course.is_published ? 'Published' : 'Draft' }}
                                                </span>
                                            </div>
                                        </div>
                                        <Link
                                            :href="route('courses.edit', course.id)"
                                            class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"
                                        >
                                            Edit
                                        </Link>
                                    </div>
                                </div>
                                <div v-if="courses.length === 0" class="text-center py-8 text-gray-500">
                                    No courses yet.
                                    <Link
                                        :href="route('courses.create')"
                                        class="text-indigo-600 hover:text-indigo-800 ml-2"
                                    >
                                        Create your first course!
                                    </Link>
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
import { useRoute } from '@/composables/useRoute';
import { Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
    courses: Array,
});

const { direction } = useDirection();
const { route } = useRoute();
</script>

