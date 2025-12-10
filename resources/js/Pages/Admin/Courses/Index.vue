<template>
    <AdminLayout :page-title="t('admin.courses_management')">
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ t('admin.courses_management') }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ t('admin.courses_description') || 'Manage all courses in the system' }}</p>
                </div>
                <Link
                    :href="route('admin.courses.create')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 font-medium transition-all duration-200 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ t('admin.create_course') }}
                </Link>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            v-model="search"
                            type="text"
                            :placeholder="t('common.search') || 'Search courses...'"
                            class="w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            @input="debouncedSearch"
                        />
                    </div>
                    <select
                        v-model="statusFilter"
                        @change="applyFilters"
                        class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white min-w-[150px]"
                    >
                        <option value="">{{ t('courses.all_status') || 'All Status' }}</option>
                        <option value="published">{{ t('courses.status.published') }}</option>
                        <option value="draft">{{ t('courses.status.draft') }}</option>
                    </select>
                </div>
            </div>

            <!-- Courses Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('courses.fields.title') }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('courses.fields.instructor') }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('courses.fields.level') }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('courses.fields.price') }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('courses.status.title') || 'Status' }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('common.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr v-for="course in courses.data" :key="course.id" class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                            {{ course.thumbnail ? '' : (course.translated_title?.[0] || course.title?.[0] || 'C') }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ course.translated_title || course.title }}</div>
                                            <div class="text-xs text-gray-500">{{ course.category?.translated_name || course.category?.name || '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600">{{ course.instructor?.name || t('courses.unknown_instructor') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 capitalize">
                                        {{ t('courses.levels.' + course.level) || course.level }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ course.price == 0 ? t('courses.free') : `$${course.price}` }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                        :class="course.is_published ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'">
                                        {{ course.is_published ? t('courses.status.published') : t('courses.status.draft') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <Link
                                            :href="route('courses.show', course.slug || course.id)"
                                            class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            :title="t('common.view')"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </Link>
                                        <Link
                                            :href="route('admin.courses.edit', course.slug || course.id)"
                                            class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                            :title="t('common.edit')"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="courses.data.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        <p class="text-gray-500 font-medium">{{ t('courses.no_courses') || 'No courses found' }}</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="courses.links && courses.links.length > 3" class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-gray-600">
                            {{ t('common.showing') }} <span class="font-medium">{{ courses.from }}</span> {{ t('common.to') }} <span class="font-medium">{{ courses.to }}</span> {{ t('common.of') }} <span class="font-medium">{{ courses.total }}</span>
                        </div>
                        <nav class="flex items-center gap-1">
                            <template v-for="(link, index) in courses.links" :key="index">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    :class="[
                                        'px-3 py-2 rounded-lg text-sm font-medium transition-all',
                                        link.active
                                            ? 'bg-blue-600 text-white shadow-sm'
                                            : 'text-gray-600 hover:bg-gray-100'
                                    ]"
                                >
                                    <span v-html="link.label"></span>
                                </Link>
                                <span
                                    v-else
                                    class="px-3 py-2 text-sm text-gray-400"
                                    v-html="link.label"
                                ></span>
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { Link, router } from '@inertiajs/vue3';

defineProps({
    courses: Object,
});

const { t } = useTranslation();
const { route } = useRoute();

const search = ref('');
const statusFilter = ref('');

let searchTimeout = null;

const debouncedSearch = () => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 300);
};

const applyFilters = () => {
    const params = {};
    if (search.value) params.search = search.value;
    if (statusFilter.value) params.status = statusFilter.value;
    
    router.get(route('admin.courses.index'), params, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

