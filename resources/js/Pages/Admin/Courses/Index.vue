<template>
    <AdminLayout :page-title="t('admin.courses_management')">
        <div class="space-y-6 min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 pb-8">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-2xl p-6 shadow-xl">
                <div class="text-white">
                    <h1 class="text-3xl font-bold mb-2">{{ t('admin.courses_management') }}</h1>
                    <p class="text-blue-100 text-sm">{{ t('admin.courses_description') || 'Manage all courses in the system' }}</p>
                </div>
                <Link :href="route('admin.courses.create')">
                    <Button 
                        :label="t('admin.create_course')" 
                        icon="pi pi-plus"
                        severity="secondary"
                        outlined
                        class="bg-white"
                    />
                </Link>
            </div>

            <!-- Search and Filters -->
            <Card>
                <template #content>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <span class="p-input-icon-right w-full">
                                <i class="pi pi-search" />
                                <InputText
                                    v-model="search"
                                    :placeholder="t('common.search') || 'Search courses...'"
                                    class="w-full"
                                    @input="debouncedSearch"
                                />
                            </span>
                        </div>
                        <Dropdown
                            v-model="statusFilter"
                            :options="[
                                { label: t('courses.all_status') || 'All Status', value: '' },
                                { label: t('courses.status.published'), value: 'published' },
                                { label: t('courses.status.draft'), value: 'draft' }
                            ]"
                            optionLabel="label"
                            optionValue="value"
                            :placeholder="t('courses.all_status') || 'All Status'"
                            class="w-full sm:w-[200px]"
                            @change="applyFilters"
                        />
                    </div>
                </template>
            </Card>

            <!-- Courses Table -->
            <Card>
                <template #content>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-blue-50/30">
                            <tr>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    {{ t('courses.fields.title') }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    {{ t('courses.fields.level') }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    {{ t('courses.status.title') || 'Status' }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    {{ t('common.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr v-for="course in courses.data" :key="course.id" class="hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-indigo-50/30 transition-all duration-300 group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 via-indigo-600 to-purple-600 flex items-center justify-center text-white font-bold text-base flex-shrink-0 shadow-lg group-hover:shadow-xl group-hover:scale-110 transition-all duration-300">
                                            <img v-if="course.thumbnail_url || course.thumbnail" :src="course.thumbnail_url || course.thumbnail" :alt="course.translated_title || course.title" class="w-full h-full object-cover rounded-xl" />
                                            <span v-else>{{ (course.translated_title?.[0] || course.title?.[0] || 'C')?.toUpperCase() }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ course.translated_title || course.title }}</div>
                                            <div class="text-xs text-gray-500 mt-1">{{ course.level ? t(`courses.levels.${course.level}`) : '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <Tag :value="t('courses.levels.' + course.level) || course.level" severity="info" class="capitalize" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <Tag 
                                        :value="course.is_published ? t('courses.status.published') : t('courses.status.draft')" 
                                        :severity="course.is_published ? 'success' : 'warning'"
                                    />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <Link :href="route('admin.courses.show', course.slug || course.id)">
                                            <Button 
                                                icon="pi pi-eye"
                                                severity="secondary"
                                                text
                                                rounded
                                                :aria-label="t('common.view')"
                                            />
                                        </Link>
                                        <Link :href="route('admin.courses.edit', course.slug || course.id)">
                                            <Button 
                                                icon="pi pi-pencil"
                                                severity="info"
                                                text
                                                rounded
                                                :aria-label="t('common.edit')"
                                            />
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="courses.data.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center">
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
                </template>
            </Card>
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

<style scoped>
:deep(.p-card) {
    border-radius: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

:deep(.p-card:hover) {
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12), 0 4px 8px rgba(0, 0, 0, 0.08);
}

:deep(.p-button) {
    font-weight: 500;
    transition: all 0.3s ease;
}

:deep(.p-button:hover) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

:deep(.p-inputtext) {
    width: 100%;
}

:deep(.p-dropdown) {
    width: 100%;
}

:deep(.p-tag) {
    font-weight: 600;
    padding: 0.375rem 0.75rem;
}

:deep(.p-datatable) {
    border-radius: 0.75rem;
}

:deep(.p-datatable .p-datatable-thead > tr > th) {
    background: linear-gradient(135deg, rgba(249, 250, 251, 0.8) 0%, rgba(239, 246, 255, 0.5) 100%);
    border-bottom: 2px solid rgba(229, 231, 235, 0.8);
}

:deep(.p-datatable .p-datatable-tbody > tr:hover) {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(99, 102, 241, 0.05) 100%);
}
</style>

