<template>
    <AdminLayout :page-title="t('admin.courses_management')">
        <div class="space-y-6 min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 pb-8">
            <!-- Page Header - Enhanced -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-2xl p-8 shadow-2xl relative overflow-hidden">
                <div class="absolute inset-0 bg-black/5"></div>
                <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-48 -mt-48 blur-3xl"></div>
                <div class="relative z-10 text-white flex-1">
                    <h1 class="text-4xl font-bold mb-3">{{ t('admin.courses_management') || 'Courses Management' }}</h1>
                    <p class="text-blue-100 text-base">{{ t('admin.courses_description') || 'Manage all courses in the system' }}</p>
                </div>
                <div class="relative z-10">
                    <Link :href="route('admin.courses.create')">
                        <Button 
                            :label="t('admin.create_course') || 'Create Course'" 
                            icon="pi pi-plus"
                            severity="secondary"
                            outlined
                            class="bg-white hover:bg-gray-50 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
                        />
                    </Link>
                </div>
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
                                    :placeholder="t('common.search_placeholder') || 'Search courses...'"
                                    class="w-full"
                                    @input="debouncedSearch"
                                />
                            </span>
                        </div>
                        <Dropdown
                            v-model="statusFilter"
                            :options="[
                                { label: t('courses.all_status') || 'All Status', value: '' },
                                { label: t('courses.status.published') || 'Published', value: 'published' },
                                { label: t('courses.status.draft') || 'Draft', value: 'draft' }
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
            <Card class="courses-table-card">
                <template #content>
                <div class="overflow-x-auto">
                    <table class="min-w-full courses-table">
                        <thead>
                            <tr>
                                <th class="table-header">
                                    {{ t('courses.fields.title') || 'Title' }}
                                </th>
                                <th class="table-header">
                                    {{ t('courses.fields.level') || 'Level' }}
                                </th>
                                <th class="table-header">
                                    {{ t('courses.status.title') || 'Status' }}
                                </th>
                                <th class="table-header">
                                    {{ t('common.actions') || 'Actions' }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="course in courses.data" 
                                :key="course.id" 
                                class="course-row"
                            >
                                <td class="course-title-cell">
                                    <Link 
                                        :href="route('admin.courses.show', course.slug || course.id)"
                                        class="flex items-center gap-4 group-link"
                                    >
                                        <div class="course-thumbnail">
                                            <img 
                                                v-if="course.thumbnail_url || course.thumbnail" 
                                                :src="course.thumbnail_url || course.thumbnail" 
                                                :alt="course.translated_title || course.title" 
                                                class="thumbnail-image"
                                                @error="handleImageError($event)"
                                            />
                                            <span v-else class="thumbnail-placeholder">
                                                {{ (course.translated_title?.[0] || course.title?.[0] || 'C')?.toUpperCase() }}
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="course-name">{{ course.translated_title || course.title }}</div>
                                            <div class="course-level-text">{{ course.level ? (t(`courses.levels.${course.level}`) || course.level) : '' }}</div>
                                        </div>
                                    </Link>
                                </td>
                                <td class="table-cell">
                                    <Tag 
                                        :value="course.level ? (t(`courses.levels.${course.level}`) || course.level) : '-'" 
                                        severity="info" 
                                        class="level-tag" 
                                    />
                                </td>
                                <td class="table-cell">
                                    <Tag 
                                        :value="course.is_published ? (t('courses.status.published') || 'Published') : (t('courses.status.draft') || 'Draft')" 
                                        :severity="course.is_published ? 'success' : 'warning'"
                                        class="status-tag"
                                    />
                                </td>
                                <td class="table-cell">
                                    <div class="action-buttons">
                                        <Link 
                                            :href="route('admin.courses.show', course.slug || course.id)" 
                                            class="action-button"
                                        >
                                            <Button 
                                                icon="pi pi-eye"
                                                severity="secondary"
                                                text
                                                rounded
                                                :aria-label="t('common.view') || 'View'"
                                                class="action-btn"
                                            />
                                        </Link>
                                        <Link 
                                            :href="route('admin.courses.edit', course.slug || course.id)" 
                                            class="action-button"
                                        >
                                            <Button 
                                                icon="pi pi-pencil"
                                                severity="info"
                                                text
                                                rounded
                                                :aria-label="t('common.edit') || 'Edit'"
                                                class="action-btn"
                                            />
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="courses.data.length === 0" class="empty-row">
                                <td colspan="4" class="empty-state-cell">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="pi pi-book text-5xl text-gray-300"></i>
                                        </div>
                                        <h3 class="empty-title">{{ t('courses.no_courses') || 'No courses found' }}</h3>
                                        <p class="empty-description">{{ t('courses.no_courses_description') || 'Get started by creating your first course' }}</p>
                                        <Link :href="route('admin.courses.create')">
                                            <Button 
                                                :label="t('admin.create_course') || 'Create Course'" 
                                                icon="pi pi-plus"
                                                class="empty-action-btn"
                                            />
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="courses.links && courses.links.length > 3" class="pagination-wrapper">
                    <div class="pagination-content">
                        <div class="pagination-info">
                            {{ t('common.showing') }} <span class="font-semibold text-gray-900">{{ courses.from }}</span> 
                            {{ t('common.to') }} <span class="font-semibold text-gray-900">{{ courses.to }}</span> 
                            {{ t('common.of') }} <span class="font-semibold text-gray-900">{{ courses.total }}</span>
                        </div>
                        <nav class="pagination-nav">
                            <template v-for="(link, index) in courses.links" :key="index">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    :class="[
                                        'pagination-link',
                                        link.active ? 'pagination-link-active' : 'pagination-link-inactive'
                                    ]"
                                >
                                    <span v-html="link.label"></span>
                                </Link>
                                <span
                                    v-else
                                    class="pagination-link-disabled"
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

const handleImageError = (event) => {
    event.target.style.display = 'none';
    const parent = event.target.parentElement;
    if (parent && !parent.querySelector('.thumbnail-placeholder')) {
        const courseTitle = event.target.alt || 'C';
        const firstLetter = courseTitle[0]?.toUpperCase() || 'C';
        const span = document.createElement('span');
        span.className = 'thumbnail-placeholder';
        span.textContent = firstLetter;
        parent.appendChild(span);
    }
};
</script>

<style scoped>
/* PrimeVue Card Styles - Following v3 Patterns */
:deep(.p-card) {
    border-radius: 12px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    border: 1px solid var(--p-border-color);
    background: var(--p-surface-card);
}

:deep(.p-card:hover) {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

:deep(.p-card-body) {
    padding: 1.5rem;
}

:deep(.p-card-content) {
    padding: 0;
}

/* PrimeVue Input Styles - Following v3 Patterns */
:deep(.p-inputtext) {
    width: 100%;
    border-radius: 6px;
    transition: all 0.2s ease;
}

:deep(.p-inputtext:focus) {
    border-color: var(--p-primary-color);
    box-shadow: 0 0 0 0.2rem var(--p-focus-ring);
}

:deep(.p-input-icon-right) {
    width: 100%;
}

:deep(.p-input-icon-right > i) {
    color: var(--p-text-muted-color);
    right: 0.75rem;
}

/* PrimeVue Dropdown Styles */
:deep(.p-dropdown) {
    width: 100%;
    border-radius: 6px;
    transition: all 0.2s ease;
}

:deep(.p-dropdown:not(.p-disabled):hover) {
    border-color: var(--p-primary-color);
}

:deep(.p-dropdown:not(.p-disabled).p-focus) {
    border-color: var(--p-primary-color);
    box-shadow: 0 0 0 0.2rem var(--p-focus-ring);
}

/* Table Styles - PrimeVue v3 Compatible */
.courses-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: var(--p-surface-card);
}

.table-header {
    padding: 1rem 1.5rem;
    text-align: right;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--p-text-color);
    background: var(--p-surface-50);
    border-bottom: 1px solid var(--p-border-color);
    position: sticky;
    top: 0;
    z-index: 10;
}

.table-header:first-child {
    border-top-right-radius: 12px;
}

.table-header:last-child {
    border-top-left-radius: 12px;
}

/* Table Row Styles - PrimeVue v3 Compatible */
.course-row {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    border-bottom: 1px solid var(--p-border-color);
    background: var(--p-surface-card);
}

.course-row:hover {
    background: var(--p-surface-hover);
    cursor: pointer;
}

.course-row:last-child {
    border-bottom: none;
}

/* Table Cell Styles */
.table-cell {
    padding: 1.25rem 1.5rem;
    vertical-align: middle;
    white-space: nowrap;
}

.course-title-cell {
    padding: 1.25rem 1.5rem;
    vertical-align: middle;
}

/* Course Thumbnail */
.course-thumbnail {
    width: 4rem;
    height: 4rem;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--p-primary-color) 0%, var(--p-primary-600) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.course-row:hover .course-thumbnail {
    transform: scale(1.05);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.thumbnail-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 0.75rem;
    transition: transform 0.3s ease;
}

.course-row:hover .thumbnail-image {
    transform: scale(1.1);
}

.thumbnail-placeholder {
    color: white;
    font-weight: 700;
    font-size: 1.25rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Course Info */
.group-link {
    text-decoration: none;
    color: inherit;
}

.course-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--p-text-color);
    margin-bottom: 0.25rem;
    transition: color 0.2s ease;
    line-height: 1.4;
}

.course-row:hover .course-name {
    color: var(--p-primary-color);
}

.course-level-text {
    font-size: 0.75rem;
    color: var(--p-text-muted-color);
    font-weight: 500;
}

/* PrimeVue Tag Styles */
:deep(.p-tag) {
    font-weight: 600;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

:deep(.level-tag),
:deep(.status-tag) {
    font-weight: 600;
}

/* PrimeVue Button Styles */
.action-buttons {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.action-button {
    display: inline-flex;
    transition: transform 0.2s ease;
}

.action-button:hover {
    transform: scale(1.1);
}

:deep(.p-button) {
    transition: all 0.2s ease;
    border-radius: 6px;
}

:deep(.p-button.p-button-text:hover) {
    background: var(--p-button-text-hover-bg);
}

:deep(.p-button.p-button-rounded) {
    border-radius: 50%;
}

/* Empty State */
.empty-row {
    border: none;
}

.empty-state-cell {
    padding: 4rem 1.5rem;
    text-align: center;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.empty-icon {
    margin-bottom: 1.5rem;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

.empty-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--p-text-color);
    margin-bottom: 0.5rem;
}

.empty-description {
    font-size: 0.875rem;
    color: var(--p-text-muted-color);
    margin-bottom: 1.5rem;
}

.empty-action-btn {
    transition: all 0.2s ease;
}

:deep(.empty-action-btn.p-button) {
    background: var(--p-primary-color);
    border-color: var(--p-primary-color);
}

:deep(.empty-action-btn.p-button:hover) {
    background: var(--p-primary-600);
    border-color: var(--p-primary-600);
    transform: translateY(-1px);
}

/* Pagination - PrimeVue v3 Compatible */
.pagination-wrapper {
    padding: 1.5rem;
    border-top: 1px solid var(--p-border-color);
    background: var(--p-surface-50);
}

.pagination-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

@media (min-width: 640px) {
    .pagination-content {
        flex-direction: row;
    }
}

.pagination-info {
    font-size: 0.875rem;
    color: var(--p-text-muted-color);
}

.pagination-nav {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.pagination-link {
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2.5rem;
}

.pagination-link-inactive {
    color: var(--p-text-muted-color);
    background: transparent;
}

.pagination-link-inactive:hover {
    background: var(--p-surface-hover);
    color: var(--p-text-color);
}

.pagination-link-active {
    background: var(--p-primary-color);
    color: var(--p-primary-contrast-color);
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.pagination-link-disabled {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    color: var(--p-disabled-color);
    cursor: not-allowed;
}

/* Responsive */
@media (max-width: 768px) {
    .table-header,
    .table-cell,
    .course-title-cell {
        padding: 1rem;
    }
    
    .course-thumbnail {
        width: 3rem;
        height: 3rem;
    }
    
    .course-name {
        font-size: 0.875rem;
    }
    
    .pagination-content {
        flex-direction: column;
        align-items: stretch;
    }
    
    .pagination-nav {
        justify-content: center;
        flex-wrap: wrap;
    }
}
</style>

