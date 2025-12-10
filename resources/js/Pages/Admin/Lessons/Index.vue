<template>
    <AdminLayout :page-title="t('admin.lessons_management')">
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link :href="route('admin.courses.edit', course.slug || course.id)" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ t('admin.lessons_management') || 'Lessons' }}</h1>
                        <p class="text-sm text-gray-500">{{ course.title }}</p>
                    </div>
                </div>
                <Link
                    :href="route('admin.courses.lessons.create', course.slug || course.id)"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 font-medium transition-all shadow-lg shadow-blue-500/25"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ t('admin.add_lesson') || 'Add Lesson' }}
                </Link>
            </div>

            <!-- Filter by Section -->
            <div class="flex items-center gap-3">
                <select
                    v-model="selectedSection"
                    @change="filterBySection"
                    class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white min-w-[200px]"
                >
                    <option value="">{{ t('lessons.all_sections') || 'All Sections' }}</option>
                    <option v-for="section in sections" :key="section.id" :value="section.id">
                        {{ section.title }}
                    </option>
                </select>
            </div>

            <!-- Lessons Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">
                                    #
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('lessons.fields.title') || 'Title' }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('lessons.fields.type') || 'Type' }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('lessons.fields.section') || 'Section' }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('lessons.fields.duration') || 'Duration' }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('lessons.fields.questions') || 'Questions' }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('attendance.title') || 'Attendance' }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('common.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr v-for="lesson in filteredLessons" :key="lesson.id" class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-500 font-medium">
                                    {{ lesson.order }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div :class="getLessonTypeIcon(lesson.type).bg" class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5" :class="getLessonTypeIcon(lesson.type).text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getLessonTypeIcon(lesson.type).path" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ lesson.title }}</div>
                                            <div v-if="lesson.is_free" class="text-xs text-emerald-600 font-medium">{{ t('lessons.free') || 'Free' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 capitalize">
                                        {{ formatLessonType(lesson.type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ lesson.section?.title || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ lesson.duration_minutes ? `${lesson.duration_minutes} ${t('common.minutes') || 'min'}` : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <Link
                                        v-if="lesson.questions_count > 0"
                                        :href="route('admin.courses.lessons.questions.index', [course.slug || course.id, lesson.id])"
                                        class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700"
                                    >
                                        <span class="font-medium">{{ lesson.questions_count }}</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </Link>
                                    <span v-else class="text-sm text-gray-400">0</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <Link
                                        :href="route('admin.courses.lessons.show', [course.slug || course.id, lesson.id])"
                                        class="inline-flex items-center gap-1 text-sm text-emerald-600 hover:text-emerald-700"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                        {{ t('attendance.view') || 'View' }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <Link
                                            :href="route('admin.courses.lessons.show', [course.slug || course.id, lesson.id])"
                                            class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            :title="t('common.view')"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </Link>
                                        <Link
                                            :href="route('admin.courses.lessons.edit', [course.slug || course.id, lesson.id])"
                                            class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                            :title="t('common.edit')"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </Link>
                                        <Link
                                            :href="route('admin.courses.lessons.questions.index', [course.slug || course.id, lesson.id])"
                                            class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                            :title="t('admin.manage_questions') || 'Questions'"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </Link>
                                        <button
                                            @click="confirmDelete(lesson)"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            :title="t('common.delete')"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredLessons.length === 0">
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        <p class="text-gray-500 font-medium">{{ t('lessons.no_lessons') || 'No lessons found' }}</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    lessons: Array,
    sections: Array,
});

const { t } = useTranslation();
const { route } = useRoute();

const selectedSection = ref('');

const filteredLessons = computed(() => {
    if (!selectedSection.value) return props.lessons;
    return props.lessons.filter(l => l.section?.id === parseInt(selectedSection.value));
});

const filterBySection = () => {
    // Filter is handled by computed property
};

const getLessonTypeIcon = (type) => {
    const icons = {
        text: { bg: 'bg-blue-100', text: 'text-blue-600', path: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
        youtube_video: { bg: 'bg-red-100', text: 'text-red-600', path: 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
        google_drive_video: { bg: 'bg-yellow-100', text: 'text-yellow-600', path: 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z' },
        video_file: { bg: 'bg-purple-100', text: 'text-purple-600', path: 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z' },
        image: { bg: 'bg-emerald-100', text: 'text-emerald-600', path: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z' },
        document_file: { bg: 'bg-orange-100', text: 'text-orange-600', path: 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z' },
        embed_frame: { bg: 'bg-cyan-100', text: 'text-cyan-600', path: 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4' },
        assignment: { bg: 'bg-indigo-100', text: 'text-indigo-600', path: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' },
        test: { bg: 'bg-rose-100', text: 'text-rose-600', path: 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    };
    return icons[type] || icons.text;
};

const formatLessonType = (type) => {
    const types = {
        text: t('lessons.types.text') || 'Text',
        youtube_video: t('lessons.types.youtube') || 'YouTube',
        google_drive_video: t('lessons.types.drive') || 'Google Drive',
        video_file: t('lessons.types.video') || 'Video',
        image: t('lessons.types.image') || 'Image',
        document_file: t('lessons.types.document') || 'Document',
        embed_frame: t('lessons.types.embed') || 'Embed',
        assignment: t('lessons.types.assignment') || 'Assignment',
        test: t('lessons.types.test') || 'Test',
    };
    return types[type] || type;
};

const confirmDelete = (lesson) => {
    if (confirm(t('lessons.confirm_delete') || 'Are you sure you want to delete this lesson?')) {
        router.delete(route('admin.courses.lessons.destroy', [props.course.slug || props.course.id, lesson.id]));
    }
};
</script>

