<template>
    <AdminLayout :page-title="track?.translated_name || track?.name || 'Track'">
        <Head :title="track?.translated_name || track?.name || 'Track'" />
        <div class="container-fluid px-3 px-md-4 px-lg-5 py-4">
            <div class="track-show-header">
                <nav aria-label="breadcrumb" class="track-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <Link :href="route('admin.tracks.index')" class="breadcrumb-link">
                                {{ t('tracks.tracks_management') || 'Tracks' }}
                            </Link>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ track?.translated_name || track?.name }}
                        </li>
                    </ol>
                </nav>
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div class="track-header-text">
                        <h1 class="track-title">{{ track?.translated_name || track?.name }}</h1>
                        <p class="track-description">{{ track?.translated_description || track?.description }}</p>
                        <small class="track-program-link">
                            {{ t('tracks.program') || 'Program' }}: 
                            <Link v-if="can('programs.view')" :href="route('admin.programs.show', track?.program?.slug || track?.program?.id)" class="program-link">
                                {{ track?.program?.translated_name || track?.program?.name }}
                            </Link>
                            <span v-else class="program-link">{{ track?.program?.translated_name || track?.program?.name }}</span>
                        </small>
                    </div>
                    <div class="btn-group">
                        <Link v-if="can('tracks.edit')" :href="route('admin.tracks.edit', track?.slug || track?.id)" class="btn btn-primary btn-lg">
                            <i class="bi bi-pencil me-2"></i>
                            {{ t('common.edit') || 'Edit' }}
                        </Link>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Statistics -->
                <div class="col-12 col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="bi bi-book text-primary fs-1"></i>
                            <h3 class="mt-3 mb-0">{{ track?.courses?.length || 0 }}</h3>
                            <p class="text-muted mb-0">{{ t('tracks.courses') || 'Courses' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="bi bi-check-circle text-success fs-1"></i>
                            <h3 class="mt-3 mb-0">{{ getPublishedCourses() }}</h3>
                            <p class="text-muted mb-0">{{ t('tracks.published_courses') || 'Published Courses' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="bi bi-people text-info fs-1"></i>
                            <h3 class="mt-3 mb-0">{{ getTotalStudents() }}</h3>
                            <p class="text-muted mb-0">{{ t('tracks.total_students') || 'Total Students' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="bi bi-graph-up-arrow text-warning fs-1"></i>
                            <h3 class="mt-3 mb-0">{{ progressStats?.overall_completion_percentage || 0 }}%</h3>
                            <p class="text-muted mb-0">{{ t('tracks.overall_completion') || 'Overall Completion' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Progress Statistics -->
                <div v-if="progressStats" class="col-12">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="bi bi-graph-up-arrow me-2"></i>
                                {{ t('tracks.progress_statistics') || 'Progress Statistics' }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Course Completion Stats -->
                            <div v-if="progressStats.course_completion_stats && progressStats.course_completion_stats.length > 0" class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3 cursor-pointer" @click="toggleCourseCompletion" style="cursor: pointer;">
                                    <h6 class="fw-bold mb-0">{{ t('tracks.course_completion') || 'Course Completion' }}</h6>
                                    <i class="bi" :class="isCourseCompletionCollapsed ? 'bi-chevron-down' : 'bi-chevron-up'" style="transition: transform 0.3s ease"></i>
                                </div>
                                <div v-show="!isCourseCompletionCollapsed" class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>{{ t('tracks.course') || 'Course' }}</th>
                                                <th>{{ t('tracks.total_students') || 'Total Students' }}</th>
                                                <th>{{ t('tracks.completed_students') || 'Completed Students' }}</th>
                                                <th>{{ t('tracks.completion_percentage') || 'Completion %' }}</th>
                                                <th>{{ t('tracks.progress') || 'Progress' }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="stat in progressStats.course_completion_stats" :key="stat.course_id">
                                                <td><strong>{{ stat.course_title }}</strong></td>
                                                <td>{{ stat.total_students }}</td>
                                                <td>{{ stat.completed_students }}</td>
                                                <td>{{ stat.completion_percentage }}%</td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar" :class="stat.completion_percentage >= 100 ? 'bg-success' : 'bg-info'" 
                                                             :style="{ width: stat.completion_percentage + '%' }">
                                                            {{ stat.completion_percentage }}%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Courses -->
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ t('tracks.courses') || 'Courses' }}</h5>
                            <button v-if="can('tracks.edit')" @click="showAddCourseModal = true" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>
                                {{ t('tracks.add_course') || 'Add Course' }}
                            </button>
                        </div>
                        <div class="card-body">
                            <div v-if="track?.courses && track.courses.length > 0" class="row g-3">
                                <div v-for="course in track.courses" :key="course.id" class="col-12 col-md-6 col-lg-4">
                                    <div class="card border h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0">{{ course.translated_title || course.title }}</h6>
                                                <span class="badge" :class="course.is_published ? 'bg-success' : 'bg-secondary'">
                                                    {{ course.is_published ? (t('common.published') || 'Published') : (t('common.draft') || 'Draft') }}
                                                </span>
                                            </div>
                                            <p class="card-text small text-muted mb-2">
                                                {{ (course.translated_description || course.description)?.substring(0, 80) }}...
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ course.duration_hours }} {{ t('common.hours') || 'Hours' }}
                                                </small>
                                                <small class="text-muted">
                                                    <i class="bi bi-people me-1"></i>
                                                    {{ course.students_count || 0 }} {{ t('tracks.students') || 'Students' }}
                                                </small>
                                            </div>
                                            <!-- Course completion progress -->
                                            <div v-if="getCourseCompletion(course.id)" class="mb-2">
                                                <small class="text-muted d-block mb-1">
                                                    {{ t('tracks.completion') || 'Completion' }}: {{ getCourseCompletion(course.id) }}%
                                                </small>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-success" :style="{ width: getCourseCompletion(course.id) + '%' }"></div>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <Link v-if="can('courses.view-all')" :href="route('admin.courses.show', course.slug || course.id)" class="btn btn-sm btn-outline-primary flex-grow-1">
                                                    {{ t('common.view') || 'View Course' }}
                                                </Link>
                                                <button v-if="can('tracks.edit')" @click="confirmRemoveCourse(course)" class="btn btn-sm btn-outline-danger" :title="t('tracks.remove_from_track') || 'Remove from Track'">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-4">
                                <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-3">{{ t('tracks.no_courses_in_track') || 'No courses in this track' }}</p>
                                <button v-if="can('tracks.edit')" @click="showAddCourseModal = true" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    {{ t('tracks.add_course') || 'Add Course' }}
                                </button>
                            </div>
                            
                            <!-- Student Progress in Track -->
                            <div v-if="progressStats && progressStats.student_progress && progressStats.student_progress.length > 0" class="mt-4 pt-4 border-top">
                                <div class="d-flex justify-content-between align-items-center mb-3 cursor-pointer" @click="toggleStudentProgress" style="cursor: pointer;">
                                    <h6 class="fw-bold mb-0">
                                        <i class="bi bi-people me-2"></i>
                                        {{ t('tracks.student_progress') || 'Student Progress in Track' }}
                                    </h6>
                                    <i class="bi" :class="isStudentProgressCollapsed ? 'bi-chevron-down' : 'bi-chevron-up'" style="transition: transform 0.3s ease"></i>
                                </div>
                                <div v-show="!isStudentProgressCollapsed" class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>{{ t('tracks.student') || 'Student' }}</th>
                                                <th>{{ t('tracks.completed_courses') || 'Completed Courses' }}</th>
                                                <th>{{ t('tracks.total_courses') || 'Total Courses' }}</th>
                                                <th>{{ t('tracks.progress') || 'Progress' }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="student in progressStats.student_progress" :key="student.student_id">
                                                <td><strong>{{ student.student_name }}</strong></td>
                                                <td>{{ student.completed_courses }}</td>
                                                <td>{{ student.total_courses }}</td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar" :class="student.progress_percentage >= 100 ? 'bg-success' : 'bg-primary'" 
                                                             :style="{ width: Math.min(student.progress_percentage, 100) + '%' }">
                                                            {{ student.progress_percentage }}%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Course Modal -->
            <div v-if="showAddCourseModal" class="add-course-modal" @click.self="showAddCourseModal = false">
                <div class="add-course-modal-dialog">
                    <div class="add-course-modal-content">
                        <div class="add-course-modal-header">
                            <h5 class="add-course-modal-title">
                                <i class="bi bi-plus-circle me-2"></i>
                                {{ t('tracks.add_course') || 'Add Course' }}
                            </h5>
                            <button type="button" class="add-course-modal-close" @click="showAddCourseModal = false" aria-label="Close">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <form @submit.prevent="addCourses">
                            <div class="add-course-modal-body">
                                <div v-if="availableCourses && availableCourses.length > 0">
                                    <label class="add-course-label">{{ t('tracks.select_courses') || 'Select courses to add' }}</label>
                                    <div class="add-course-list">
                                        <div v-for="course in availableCourses" :key="course.id" class="add-course-item">
                                            <input 
                                                class="add-course-checkbox" 
                                                type="checkbox" 
                                                :value="course.id" 
                                                :id="'course-' + course.id"
                                                v-model="selectedCourseIds"
                                            >
                                            <label class="add-course-item-label" :for="'course-' + course.id">
                                                <span class="add-course-item-title">{{ course.title }}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.course_ids" class="add-course-error">
                                        <i class="bi bi-exclamation-circle me-2"></i>
                                        {{ form.errors.course_ids }}
                                    </div>
                                    <div v-if="selectedCourseIds.length > 0" class="add-course-selected-count">
                                        {{ selectedCourseIds.length }} {{ t('tracks.selected') || 'selected' }}
                                    </div>
                                </div>
                                <div v-else class="add-course-empty">
                                    <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-3">{{ t('tracks.no_available_courses') || 'No available courses to add' }}</p>
                                </div>
                            </div>
                            <div class="add-course-modal-footer">
                                <button type="button" class="btn btn-secondary" @click="showAddCourseModal = false">
                                    {{ t('common.cancel') || 'Cancel' }}
                                </button>
                                <button type="submit" class="btn btn-primary" :disabled="form.processing || selectedCourseIds.length === 0">
                                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                    <i v-else class="bi bi-plus-circle me-2"></i>
                                    {{ t('tracks.add') || 'Add' }}
                                </button>
                            </div>
                        </form>
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
import { useAlert } from '@/composables/useAlert';
import { usePermissions } from '@/composables/usePermissions';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    track: Object,
    availableCourses: {
        type: Array,
        default: () => [],
    },
    progressStats: {
        type: Object,
        default: () => null,
    },
});

const { t } = useTranslation();
const { route } = useRoute();
const { showConfirm, showSuccess, showError } = useAlert();
const { can } = usePermissions();

const showAddCourseModal = ref(false);
const selectedCourseIds = ref([]);

const form = useForm({
    course_ids: [],
});

const addCourses = () => {
    if (selectedCourseIds.value.length === 0) {
        showError(
            t('tracks.select_at_least_one_course') || 'Please select at least one course',
            t('common.error') || 'Error'
        );
        return;
    }

    form.course_ids = selectedCourseIds.value;
    const trackIdentifier = props.track?.slug || props.track?.id;
    form.post(route('admin.tracks.add-courses', { track: trackIdentifier }), {
        preserveScroll: true,
        onSuccess: () => {
            showAddCourseModal.value = false;
            selectedCourseIds.value = [];
            form.reset();
            showSuccess(
                t('tracks.courses_added_successfully') || 'Courses added successfully',
                t('common.success') || 'Success'
            );
        },
        onError: () => {
            showError(
                t('tracks.add_courses_error') || 'Failed to add courses',
                t('common.error') || 'Error'
            );
        },
    });
};

const confirmRemoveCourse = async (course) => {
    const result = await showConfirm(
        t('tracks.confirm_remove_course') || `Are you sure you want to remove "${course.translated_title || course.title}" from this track?`,
        t('tracks.remove_course') || 'Remove Course'
    );

    if (result.isConfirmed) {
        router.delete(route('admin.tracks.remove-course', {
            track: props.track?.slug || props.track?.id,
            course: course.slug || course.id,
        }), {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess(
                    t('tracks.course_removed_successfully') || 'Course removed successfully',
                    t('common.success') || 'Success'
                );
            },
            onError: () => {
                showError(
                    t('tracks.remove_course_error') || 'Failed to remove course',
                    t('common.error') || 'Error'
                );
            },
        });
    }
};

const getPublishedCourses = () => {
    if (!props.track?.courses) return 0;
    return props.track.courses.filter(course => course.is_published).length;
};

const getTotalStudents = () => {
    // Use unique_students_count from track if available (calculated in backend)
    // This ensures we don't count the same student multiple times across different courses/batches
    if (props.track?.unique_students_count !== undefined) {
        return props.track.unique_students_count;
    }
    
    // Fallback: sum unique counts per course
    // Note: This may count students multiple times if they're enrolled in multiple courses
    // The backend should provide unique_students_count for accurate count
    if (!props.track?.courses) return 0;
    return props.track.courses.reduce((total, course) => {
        return total + (course.unique_students_count || course.students_count || 0);
    }, 0);
};

const getCourseCompletion = (courseId) => {
    if (!props.progressStats?.course_completion_stats) return null;
    const stat = props.progressStats.course_completion_stats.find(s => s.course_id === courseId);
    return stat ? stat.completion_percentage : null;
};

// Toggle states for sections
const isCourseCompletionCollapsed = ref(false);
const isStudentProgressCollapsed = ref(false);

const toggleCourseCompletion = () => {
    isCourseCompletionCollapsed.value = !isCourseCompletionCollapsed.value;
};

const toggleStudentProgress = () => {
    isStudentProgressCollapsed.value = !isStudentProgressCollapsed.value;
};
</script>

<style scoped>
/* Breadcrumb Styling */
.track-breadcrumb {
    margin-bottom: 1.5rem;
}

.track-breadcrumb .breadcrumb {
    background: transparent;
    padding: 0;
    margin: 0;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.track-breadcrumb .breadcrumb-item {
    display: flex;
    align-items: center;
    color: #6b7280;
}

.track-breadcrumb .breadcrumb-item + .breadcrumb-item::before {
    content: "/";
    padding: 0 0.75rem;
    color: #9ca3af;
    font-weight: 400;
    display: inline-block;
}

[dir="rtl"] .track-breadcrumb .breadcrumb-item + .breadcrumb-item::before {
    content: "/";
}

.track-breadcrumb .breadcrumb-link {
    color: #6b7280;
    text-decoration: none;
    transition: color 0.2s ease;
}

.track-breadcrumb .breadcrumb-link:hover {
    color: #111827;
}

.track-breadcrumb .breadcrumb-item.active {
    color: #111827;
    font-weight: 500;
}

/* Page Header */
.track-show-header {
    margin-bottom: 2.5rem;
}

.track-header-text {
    flex: 1;
    min-width: 0;
}

.track-title {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 0.75rem 0;
    line-height: 1.3;
}

@media (min-width: 768px) {
    .track-title {
        font-size: 2.25rem;
    }
}

.track-description {
    font-size: 1rem;
    color: #6b7280;
    margin: 0 0 0.75rem 0;
    line-height: 1.6;
}

.track-program-link {
    display: block;
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 0.5rem;
}

.program-link {
    color: #0d6efd;
    text-decoration: none;
    transition: color 0.2s ease;
    font-weight: 500;
}

.program-link:hover {
    color: #0a58ca;
    text-decoration: underline;
}

/* RTL Support */
[dir="rtl"] .track-header-text {
    text-align: right;
}

[dir="ltr"] .track-header-text {
    text-align: left;
}

/* Add Course Modal Styles */
.add-course-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.add-course-modal-dialog {
    width: 100%;
    max-width: 500px;
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.add-course-modal-content {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    max-height: 90vh;
}

.add-course-modal-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8f9fa;
}

.add-course-modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
    display: flex;
    align-items: center;
}

.add-course-modal-close {
    background: none;
    border: none;
    font-size: 1.25rem;
    color: #6b7280;
    cursor: pointer;
    padding: 0.25rem;
    line-height: 1;
    transition: color 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: 0.25rem;
}

.add-course-modal-close:hover {
    color: #111827;
    background-color: #e9ecef;
}

.add-course-modal-body {
    padding: 1.5rem;
    overflow-y: auto;
    flex: 1;
    max-height: calc(90vh - 140px);
}

.add-course-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 1rem;
    display: block;
}

.add-course-list {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 0.375rem;
    padding: 0.5rem;
    margin-bottom: 1rem;
}

.add-course-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    border-radius: 0.375rem;
    transition: background-color 0.2s ease;
    cursor: pointer;
}

.add-course-item:hover {
    background-color: #f8f9fa;
}

.add-course-checkbox {
    width: 1.25rem;
    height: 1.25rem;
    margin: 0;
    margin-inline-end: 0.75rem;
    cursor: pointer;
    accent-color: #0d6efd;
    flex-shrink: 0;
}

.add-course-item-label {
    flex: 1;
    margin: 0;
    cursor: pointer;
    display: flex;
    align-items: center;
    font-size: 0.9375rem;
    color: #111827;
}

.add-course-item-title {
    word-break: break-word;
    line-height: 1.5;
}

.add-course-selected-count {
    font-size: 0.875rem;
    color: #0d6efd;
    font-weight: 500;
    margin-top: 0.5rem;
    padding-top: 0.75rem;
    border-top: 1px solid #e5e7eb;
}

.add-course-error {
    padding: 0.75rem;
    background-color: #fee2e2;
    color: #991b1b;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    margin-top: 1rem;
    display: flex;
    align-items: center;
}

.add-course-empty {
    text-align: center;
    padding: 2rem 1rem;
}

.add-course-modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    background: #f8f9fa;
}

/* Scrollbar styling for course list */
.add-course-list::-webkit-scrollbar {
    width: 6px;
}

.add-course-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.add-course-list::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.add-course-list::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* RTL Support for Modal */
[dir="rtl"] .add-course-modal-header {
    flex-direction: row-reverse;
}

[dir="rtl"] .add-course-modal-footer {
    flex-direction: row-reverse;
}

[dir="rtl"] .add-course-item {
    flex-direction: row-reverse;
}

[dir="rtl"] .add-course-checkbox {
    margin-inline-end: 0;
    margin-inline-start: 0.75rem;
}
</style>

