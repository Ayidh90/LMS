<template>
    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50" :dir="direction">
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <div class="mb-4">
                        <Link
                            :href="route('courses.show', course.slug || course.id)"
                            class="text-indigo-600 hover:text-indigo-800 text-sm"
                        >
                            ← Back to Course
                        </Link>
                    </div>

                    <div class="bg-white shadow rounded-lg p-6 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">{{ course.title }}</h1>
                                <p class="text-gray-600 mt-2">Lessons</p>
                            </div>
                            <div v-if="canEdit">
                                <Link
                                    :href="route('lessons.create', course.slug || course.id)"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700"
                                >
                                    Add Lesson
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow rounded-lg p-6">
                        <div v-if="lessons && lessons.length > 0" class="space-y-4">
                            <div
                                v-for="lesson in lessons"
                                :key="lesson.id"
                                class="border-b pb-4 last:border-b-0"
                            >
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">{{ lesson.title }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ lesson.description }}</p>
                                        <div class="flex items-center gap-4 mt-2">
                                            <span class="text-xs text-gray-500">Order: {{ lesson.order }}</span>
                                            <span class="text-xs text-gray-500">{{ lesson.duration_minutes }} minutes</span>
                                            <span v-if="lesson.is_free" class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded">Free</span>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <Link
                                            :href="route('lessons.show', [course.slug || course.id, lesson.id])"
                                            class="text-indigo-600 hover:text-indigo-800 text-sm"
                                        >
                                            View
                                        </Link>
                                        <Link
                                            v-if="canEdit"
                                            :href="route('lessons.edit', [course.slug || course.id, lesson.id])"
                                            class="text-indigo-600 hover:text-indigo-800 text-sm ml-2"
                                        >
                                            Edit
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            No lessons yet.
                            <Link
                                v-if="canEdit"
                                :href="route('lessons.create', course.id)"
                                class="text-indigo-600 hover:text-indigo-800 ml-2"
                            >
                                Add first lesson
                            </Link>
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

const props = defineProps({
    course: Object,
    lessons: Array,
    canEdit: Boolean,
});

const { direction } = useDirection();
const { route } = useRoute();
</script>

