<template>    <AdminLayout :page-title="t('courses.title') || 'Courses'">
        <Head :title="t('courses.title') || 'Courses'" />
        <div class="admin-courses-page" :dir="direction">
            <!-- Page Header -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div class="flex-grow-1">
                        <nav aria-label="breadcrumb" class="mb-2">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <Link :href="route('admin.dashboard')" class="text-decoration-none">
                                        {{ t('common.dashboard') || 'Dashboard' }}
                                    </Link>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{ t('courses.title') || 'Courses' }}
                                </li>
                            </ol>
                        </nav>
                        <h1 class="page-title mb-0">{{ t('courses.title') || 'Courses' }}</h1>
                        </div>
                    <div>
                        <Link :href="route('admin.courses.create')" class="btn btn-create">
                            <i class="bi bi-plus-circle"></i>
                            <span>{{ t('courses.create') || 'Create Course' }}</span>
                            </Link>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="filters-section">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="search-input-wrapper">
                            <i class="bi bi-search search-icon"></i>
                                <input
                                    v-model="search"
                                    type="text"
                                class="form-control search-input"
                                    :placeholder="t('common.search_placeholder') || 'Search courses...'"
                                    @input="debouncedSearch"
                                />
                            </div>
                        </div>
                    <div class="col-6 col-md-3 col-lg-2">
                            <select
                                v-model="statusFilter"
                            class="form-select filter-select"
                                @change="applyFilters"
                            >
                                <option value="">{{ t('courses.all_status') || 'All Status' }}</option>
                                <option value="published">{{ t('courses.status.published') || 'Published' }}</option>
                                <option value="draft">{{ t('courses.status.draft') || 'Draft' }}</option>
                            </select>
                        </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <select
                            v-model="levelFilter"
                            class="form-select filter-select"
                            @change="applyFilters"
                        >
                            <option value="">{{ t('courses.all_levels') || 'All Levels' }}</option>
                            <option value="beginner">{{ t('courses.levels.beginner') || 'Beginner' }}</option>
                            <option value="intermediate">{{ t('courses.levels.intermediate') || 'Intermediate' }}</option>
                            <option value="advanced">{{ t('courses.levels.advanced') || 'Advanced' }}</option>
                        </select>
                    </div>
                    <div class="col-12 col-lg-4 d-flex gap-2 justify-content-lg-end">
                        <button class="btn btn-filter-action" @click="refreshData" :title="t('common.refresh') || 'Refresh'">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                        <button class="btn btn-filter-action" @click="exportData" :title="t('common.export') || 'Export'">
                            <i class="bi bi-download"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Courses Table -->
            <div class="table-card">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>{{ t('courses.fields.title') || 'Title' }}</th>
                                <th class="text-center">{{ t('courses.fields.level') || 'Level' }}</th>
                                <th class="text-center">{{ t('courses.status.title') || 'Status' }}</th>
                                <th class="text-center">{{ t('courses.students') || 'Students' }}</th>
                                <th class="text-center" style="width: 140px;">{{ t('common.actions') || 'Actions' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(course, index) in courses.data" :key="course.id" class="course-row">
                                <td class="text-center row-number">
                                    {{ (courses.current_page - 1) * courses.per_page + index + 1 }}
                                </td>
                                    <td>
                                    <div class="course-info">
                                            <div class="course-thumbnail">
                                                <img 
                                                    v-if="course.thumbnail_url || course.thumbnail" 
                                                    :src="course.thumbnail_url || course.thumbnail" 
                                                    :alt="course.translated_title || course.title" 
                                                    class="thumbnail-image"
                                                    @error="handleImageError($event)"
                                                />
                                                <span v-else class="thumbnail-placeholder">
                                                {{ getInitial(course) }}
                                            </span>
                                        </div>
                                        <div class="course-details">
                                            <Link 
                                                :href="getCourseShowUrl(course)"
                                                class="course-title"
                                            >
                                                {{ course.translated_title || course.title }}
                                            </Link>
                                            <span class="course-duration">
                                                <i class="bi bi-clock"></i>
                                                {{ course.duration_hours || 0 }} {{ t('common.hours') || 'hours' }}
                                                </span>
                                            </div>
                                            </div>
                                    </td>
                                <td class="text-center">
                                    <span class="badge-level" :class="`badge-level-${course.level}`">
                                            {{ course.level ? (t(`courses.levels.${course.level}`) || course.level) : '-' }}
                                        </span>
                                    </td>
                                <td class="text-center">
                                    <span :class="course.is_published ? 'badge-status badge-published' : 'badge-status badge-draft'">
                                        {{ getStatusLabel(course.is_published) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="enrollment-count">
                                        <i class="bi bi-people"></i>
                                        {{ course.enrollments_count || 0 }}
                                    </span>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <Link 
                                            :href="getCourseShowUrl(course)" 
                                            class="btn-action btn-action-view"
                                            :title="t('common.view') || 'View'"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </Link>
                                        <Link 
                                            :href="getCourseEditUrl(course)" 
                                            class="btn-action btn-action-edit"
                                            :title="t('common.edit') || 'Edit'"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </Link>
                                        <button 
                                            v-if="!course.is_published"
                                            class="btn-action btn-action-delete"
                                            :title="t('common.delete') || 'Delete'"
                                            @click="confirmDelete(course)"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                                </tr>
                                <tr v-if="courses.data.length === 0">
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-book"></i>
                                        <h5>{{ t('courses.no_courses') || 'No courses found' }}</h5>
                                        <p>{{ t('courses.no_courses_description') || 'Get started by creating your first course' }}</p>
                                        <Link :href="route('admin.courses.create')" class="btn btn-create">
                                            <i class="bi bi-plus-circle"></i>
                                            <span>{{ t('courses.create') || 'Create Course' }}</span>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                <!-- Pagination -->
                <div v-if="courses.links && courses.links.length > 3" class="table-footer">
                    <div class="pagination-info">
                        {{ t('common.showing') || 'Showing' }} <strong>{{ courses.from }}</strong> 
                        {{ t('common.to') || 'to' }} <strong>{{ courses.to }}</strong> 
                        {{ t('common.of') || 'of' }} <strong>{{ courses.total }}</strong>
                            </div>
                            <nav>
                        <ul class="pagination mb-0">
                                    <template v-for="(link, index) in courses.links" :key="index">
                                        <li v-if="link.url" class="page-item" :class="{ active: link.active }">
                                    <Link 
                                                :href="getPaginationUrl(link.url)" 
                                                class="page-link" 
                                                preserveScroll
                                                preserveState
                                            >
                                                <span v-html="link.label"></span>
                                            </Link>
                                        </li>
                                        <li v-else class="page-item disabled">
                                            <span class="page-link" v-html="link.label"></span>
                                        </li>
                                    </template>
                                </ul>
                            </nav>
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
import { useAlert } from '@/composables/useAlert';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    courses: Object,
    filters: {
        type: Object,
        default: () => ({})
    }
});

const { t, locale } = useTranslation();
const { route } = useRoute();
const { showConfirm, showSuccess, showError } = useAlert();
const page = usePage();

const direction = computed(() => page.props.direction || 'ltr');
const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');
const levelFilter = ref(props.filters?.level || '');

// Helper function to get status translation
const getStatusLabel = (isPublished) => {
    return isPublished 
        ? (t('courses.status.published') || 'منشورة')
        : (t('courses.status.draft') || 'مؤرشفه');
};

// Helper function to get course show URL
const getCourseShowUrl = (course) => {
    if (!course) return '#';
    // Prioritize slug since Laravel route model binding uses slug as route key
    const identifier = course?.slug || course?.id;
    if (!identifier) {
        console.warn('Course missing both slug and id:', course);
        return '#';
    }
    try {
    const url = route('admin.courses.show', identifier);
        // Ensure we have a valid URL
        if (url && url !== '#' && url !== '/#') {
            return url;
        }
        // Fallback: construct URL manually if route helper fails
        return `/admin/courses/${identifier}`;
    } catch (error) {
        console.error('Error generating course show URL:', error);
        // Fallback: construct URL manually
        return `/admin/courses/${identifier}`;
    }
};

// Helper function to get course edit URL
const getCourseEditUrl = (course) => {
    if (!course) return '#';
    // Prioritize slug since Laravel route model binding uses slug as route key
    const identifier = course?.slug || course?.id;
    if (!identifier) return '#';
    try {
    const url = route('admin.courses.edit', identifier);
        if (url && url !== '#' && url !== '/#') {
            return url;
        }
        // Fallback: construct URL manually if route helper fails
        return `/admin/courses/${identifier}/edit`;
    } catch (error) {
        console.error('Error generating course edit URL:', error);
        // Fallback: construct URL manually
        return `/admin/courses/${identifier}/edit`;
    }
};

// Helper function to get course identifier for routes
const getCourseIdentifier = (course) => {
    if (!course) return null;
    return course?.slug || course?.id || null;
};

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
    if (levelFilter.value) params.level = levelFilter.value;
    
    router.get(route('admin.courses.index'), params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const refreshData = () => {
    router.reload({
        preserveScroll: true,
        only: ['courses']
    });
};

const exportData = () => {
    const params = new URLSearchParams();
    if (search.value) params.append('search', search.value);
    if (statusFilter.value) params.append('status', statusFilter.value);
    if (levelFilter.value) params.append('level', levelFilter.value);
    params.append('export', 'csv');
    
    const exportUrl = route('admin.courses.index') + '?' + params.toString();
    window.open(exportUrl, '_blank');
};

const getInitial = (course) => {
    const title = course.translated_title || course.title || 'C';
    return title.charAt(0).toUpperCase();
};

const handleImageError = (event) => {
    event.target.style.display = 'none';
    const parent = event.target.parentElement;
    if (parent && !parent.querySelector('.thumbnail-placeholder')) {
        const span = document.createElement('span');
        span.className = 'thumbnail-placeholder';
        span.textContent = 'C';
        parent.appendChild(span);
    }
};

const getPaginationUrl = (url) => {
    if (!url) return '#';
    
    try {
        const urlObj = new URL(url);
        // Preserve current filters in pagination URLs
        if (search.value) urlObj.searchParams.set('search', search.value);
        if (statusFilter.value) urlObj.searchParams.set('status', statusFilter.value);
        if (levelFilter.value) urlObj.searchParams.set('level', levelFilter.value);
        return urlObj.toString();
    } catch (e) {
        return url;
    }
};

const confirmDelete = async (course) => {
    const identifier = getCourseIdentifier(course);
    if (!identifier) {
        showError(
            t('courses.delete_error') || 'Failed to delete course',
            t('common.error') || 'Error'
        );
        return;
    }

    const result = await showConfirm(
        t('courses.confirm_delete') || 'Are you sure you want to delete this course? This action cannot be undone.',
        t('courses.delete_title') || 'Delete Course'
    );

    if (result.isConfirmed) {
        router.delete(route('admin.courses.destroy', identifier), {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess(
                    t('courses.deleted_successfully') || 'Course deleted successfully!',
                    t('common.success') || 'Success'
                );
            },
            onError: () => {
                showError(
                    t('courses.delete_error') || 'Failed to delete course',
                    t('common.error') || 'Error'
                );
            },
        });
    }
};
</script>

<style scoped>
/* Page Container */
.admin-courses-page {
    width: 100%;
    max-width: 100%;
    margin: 0;
    padding: 0;
}

/* Page Header */
.page-header {
    margin-bottom: 1.5rem;
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.breadcrumb-item {
    color: #6c757d;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    padding: 0 0.5rem;
    color: #adb5bd;
}

[dir="rtl"] .breadcrumb-item + .breadcrumb-item::before {
    content: "‹";
}

.breadcrumb-item a {
    color: #6c757d;
    text-decoration: none;
    transition: color 0.2s;
}

.breadcrumb-item a:hover {
    color: #0d6efd;
}

.breadcrumb-item.active {
    color: #212529;
    font-weight: 500;
}

.page-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #212529;
    margin: 0;
}

/* Create Button */
.btn-create {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: #ffffff;
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border: none;
    border-radius: 0.5rem;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
}

.btn-create:hover {
    color: #ffffff;
    background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
}

.btn-create i {
    font-size: 1rem;
}

/* Filters Section */
.filters-section {
    background: #ffffff;
    padding: 1.25rem;
    border-radius: 0.75rem;
    border: 1px solid #e9ecef;
    margin-bottom: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.search-input-wrapper {
    position: relative;
}

.search-icon {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    color: #adb5bd;
    font-size: 0.875rem;
    z-index: 1;
}

[dir="ltr"] .search-icon {
    left: 0.875rem;
}

[dir="rtl"] .search-icon {
    right: 0.875rem;
}

.search-input {
    height: 42px;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s;
    background-color: #ffffff;
}

[dir="ltr"] .search-input {
    padding-left: 2.5rem;
    padding-right: 0.75rem;
}

[dir="rtl"] .search-input {
    padding-right: 2.5rem;
    padding-left: 0.75rem;
}

.search-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    outline: none;
}

.search-input::placeholder {
    color: #adb5bd;
}

.filter-select {
    height: 42px;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    color: #495057;
    cursor: pointer;
    transition: all 0.2s;
    background-color: #ffffff;
    padding: 0.5rem 2rem 0.5rem 0.75rem;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
}

[dir="rtl"] .filter-select {
    padding: 0.5rem 0.75rem 0.5rem 2rem;
    background-position: left 0.75rem center;
}

.filter-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    outline: none;
}

.btn-filter-action {
    width: 42px;
    height: 42px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    background: #ffffff;
    color: #6c757d;
    transition: all 0.2s;
}

.btn-filter-action:hover {
    background: #0d6efd;
    border-color: #0d6efd;
    color: #ffffff;
}

.btn-filter-action i {
    font-size: 1rem;
}

/* Table Card */
.table-card {
    background: #ffffff;
    border-radius: 0.75rem;
    border: 1px solid #e9ecef;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.table {
    margin-bottom: 0;
}

.table thead {
    background: #f8f9fa;
}

.table thead th {
    border-bottom: 1px solid #e9ecef;
    font-weight: 600;
    font-size: 0.8125rem;
    color: #495057;
    padding: 0.875rem 1rem;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.table tbody tr {
    transition: background-color 0.15s;
    border-bottom: 1px solid #f1f3f5;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.table tbody tr:last-child {
    border-bottom: none;
}

.table td {
    padding: 1rem;
    vertical-align: middle;
    color: #212529;
    font-size: 0.875rem;
}

/* Row Number */
.row-number {
    color: #adb5bd;
    font-weight: 600;
    font-size: 0.8125rem;
}

/* Course Info */
.course-info {
    display: flex;
    align-items: center;
    gap: 0.875rem;
}

.course-thumbnail {
    width: 48px;
    height: 48px;
    border-radius: 0.5rem;
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
}

.thumbnail-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.thumbnail-placeholder {
    color: #ffffff;
    font-weight: 700;
    font-size: 1.125rem;
}

.course-details {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    min-width: 0;
}

.course-title {
    font-weight: 600;
    color: #212529;
    text-decoration: none;
    transition: color 0.2s;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 300px;
}

.course-title:hover {
    color: #0d6efd;
}

.course-duration {
    font-size: 0.75rem;
    color: #6c757d;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.course-duration i {
    font-size: 0.6875rem;
}

/* Badges */
.badge-level {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 0.375rem;
    background: #e7f3f8;
    color: #0c5460;
}

.badge-level-beginner {
    background: #d1e7dd;
    color: #0f5132;
}

.badge-level-intermediate {
    background: #fff3cd;
    color: #856404;
}

.badge-level-advanced {
    background: #f8d7da;
    color: #842029;
}

.badge-status {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 0.375rem;
}

.badge-published {
    background: #d1e7dd;
    color: #0f5132;
}

.badge-draft {
    background: #fff3cd;
    color: #856404;
}

/* Enrollment Count */
.enrollment-count {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    color: #495057;
    font-weight: 500;
    font-size: 0.875rem;
}

.enrollment-count i {
    color: #6c757d;
    font-size: 0.875rem;
}

/* Actions */
.actions-cell {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
}

.btn-action {
    width: 34px;
    height: 34px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.375rem;
    border: 1px solid;
    background: transparent;
    transition: all 0.2s;
    cursor: pointer;
    text-decoration: none;
}

.btn-action i {
    font-size: 0.8125rem;
}

.btn-action-view {
    border-color: #6c757d;
    color: #6c757d;
}

.btn-action-view:hover {
    background: #6c757d;
    color: #ffffff;
}

.btn-action-edit {
    border-color: #0d6efd;
    color: #0d6efd;
}

.btn-action-edit:hover {
    background: #0d6efd;
    color: #ffffff;
}

.btn-action-delete {
    border-color: #dc3545;
    color: #dc3545;
}

.btn-action-delete:hover {
    background: #dc3545;
    color: #ffffff;
}

/* Table Footer */
.table-footer {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

@media (min-width: 768px) {
    .table-footer {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
}

.pagination-info {
    font-size: 0.875rem;
    color: #6c757d;
}

.pagination {
    margin: 0;
    gap: 0.25rem;
}

.page-link {
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
    color: #495057;
    background: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

.page-link:hover {
    color: #0d6efd;
    background: #f8f9fa;
    border-color: #dee2e6;
}

.page-item.active .page-link {
    background: #0d6efd;
    border-color: #0d6efd;
    color: #ffffff;
}

.page-item.disabled .page-link {
    color: #adb5bd;
    background: #f8f9fa;
}

/* Empty State */
.empty-state {
    padding: 3rem 1rem;
}

.empty-state i {
    font-size: 4rem;
    color: #adb5bd;
    display: block;
    margin-bottom: 1rem;
}

.empty-state h5 {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #6c757d;
    margin-bottom: 1.5rem;
}

/* Responsive */
@media (max-width: 992px) {
    .page-title {
        font-size: 1.375rem;
    }
    
    .course-title {
        max-width: 200px;
    }
}

@media (max-width: 768px) {
    .filters-section {
        padding: 1rem;
    }
    
    .course-thumbnail {
        width: 40px;
        height: 40px;
    }
    
    .thumbnail-placeholder {
        font-size: 1rem;
    }
    
    .course-title {
        max-width: 150px;
        font-size: 0.8125rem;
    }
    
    .course-duration {
        font-size: 0.6875rem;
    }
    
    .table td,
    .table th {
        padding: 0.75rem 0.5rem;
        font-size: 0.8125rem;
    }
    
    .btn-action {
        width: 30px;
        height: 30px;
    }
    
    .btn-action i {
        font-size: 0.75rem;
    }
    
    .badge-level,
    .badge-status {
        padding: 0.25rem 0.5rem;
        font-size: 0.6875rem;
    }
}

@media (max-width: 576px) {
    .page-title {
        font-size: 1.25rem;
    }
    
    .btn-create {
        padding: 0.5rem 1rem;
        font-size: 0.8125rem;
    }
    
    .course-thumbnail {
        width: 36px;
        height: 36px;
    }
    
    .thumbnail-placeholder {
        font-size: 0.875rem;
    }
    
    .course-title {
        max-width: 120px;
    }
    
    .actions-cell {
        gap: 0.25rem;
    }
    
    .btn-action {
        width: 28px;
        height: 28px;
    }
    
    .empty-state i {
        font-size: 3rem;
    }
}

/* RTL Support */
[dir="rtl"] .course-info {
    flex-direction: row;
}

[dir="rtl"] .course-duration {
    flex-direction: row;
}

[dir="rtl"] .enrollment-count {
    flex-direction: row;
}

[dir="rtl"] .actions-cell {
    flex-direction: row;
}
</style>
