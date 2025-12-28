<template>
    <AdminLayout :page-title="batch?.name || t('admin.batch_details') || 'Batch Details'">
        <Head :title="batch?.name || t('admin.batch_details') || 'Batch Details'" />
        
        <div class="space-y-6 min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/20 pb-8">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-2xl p-8 shadow-2xl relative overflow-hidden">
                <div class="absolute inset-0 bg-black/5"></div>
                <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-48 -mt-48 blur-3xl"></div>
                <div class="relative z-10 text-white flex-1">
                    <div class="flex items-center gap-2 text-sm text-blue-100 mb-3">
                        <Link :href="route('admin.courses.index')" class="hover:text-white transition-colors font-medium">{{ t('admin.courses_management') || 'Courses' }}</Link>
                        <span>›</span>
                        <Link :href="route('admin.courses.show', course.slug || course.id)" class="hover:text-white transition-colors font-medium">{{ course.title }}</Link>
                        <span>›</span>
                        <Link :href="route('admin.courses.batches.index', course.slug || course.id)" class="hover:text-white transition-colors font-medium">{{ t('admin.batches') || 'Batches' }}</Link>
                        <span>›</span>
                        <span class="text-white font-semibold">{{ batch?.name }}</span>
                    </div>
                    <h1 class="text-4xl font-bold mb-3">{{ batch?.name }}</h1>
                    <p class="text-blue-100 text-base">{{ batch?.description || t('admin.batch_details_description') || 'View batch details and manage students' }}</p>
                </div>
                <div class="relative z-10 flex items-center gap-3">
                    <button 
                        v-if="can('batches.edit')"
                        type="button"
                        @click="openBatchModal"
                        class="btn btn-light btn-lg shadow-lg"
                    >
                        <i class="bi bi-pencil me-2"></i>
                        {{ t('common.edit') || 'Edit' }}
                    </button>
                </div>
            </div>

            <!-- Success Message -->
            <div v-if="page.props.flash?.success" class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
                <i class="pi pi-check-circle text-green-600"></i>
                <p class="text-green-800 font-medium">{{ page.props.flash.success }}</p>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-4">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card stat-card stat-card-blue h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <h3 class="display-4 fw-bold text-primary mb-2">{{ (batch?.students?.length || 0) }}</h3>
                                    <p class="text-muted mb-0 small fw-semibold">{{ t('admin.enrolled_students') || 'Enrolled Students' }}</p>
                            </div>
                                <div class="bg-primary bg-gradient rounded-3 p-3 shadow">
                                    <i class="bi bi-people text-white fs-3"></i>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card stat-card stat-card-green h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <h3 class="display-4 fw-bold text-success mb-2">{{ batch?.max_students ? batch.max_students : '∞' }}</h3>
                                    <p class="text-muted mb-0 small fw-semibold">{{ t('admin.max_students') || 'Max Students' }}</p>
                            </div>
                                <div class="bg-success bg-gradient rounded-3 p-3 shadow">
                                    <i class="bi bi-people-fill text-white fs-3"></i>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card stat-card status-card stat-card-purple h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <div class="mb-3">
                                        <span 
                                            class="badge px-3 py-2 fs-6 fw-bold"
                                            :class="(batch?.is_active === true || batch?.is_active === 1) 
                                                ? 'bg-primary' 
                                                : 'bg-secondary'"
                                        >
                                            <i 
                                                class="bi me-2"
                                                :class="(batch?.is_active === true || batch?.is_active === 1) ? 'bi-check-circle' : 'bi-x-circle'"
                                            ></i>
                                            {{ (batch?.is_active === true || batch?.is_active === 1) ? t('common.active') : t('common.inactive') }}
                                        </span>
                                </div>
                                    <p class="text-muted mb-0 small fw-semibold">{{ t('common.status') || 'Status' }}</p>
                            </div>
                                <div 
                                    class="rounded-3 p-3 shadow"
                                    :class="(batch?.is_active === true || batch?.is_active === 1) 
                                        ? 'bg-primary bg-gradient' 
                                        : 'bg-secondary bg-gradient'"
                                >
                                    <i 
                                        class="bi text-white fs-3"
                                        :class="(batch?.is_active === true || batch?.is_active === 1) ? 'bi-check-circle' : 'bi-x-circle'"
                                    ></i>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card stat-card stat-card-orange h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <h5 class="fw-bold text-dark mb-0 text-truncate">{{ batch?.instructor?.name || '-' }}</h5>
                                        <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                            <i class="bi bi-people text-info"></i>
                                            <span class="badge bg-info text-white fw-bold">{{ (batch?.students?.length || 0) }}</span>
                            </div>
                            </div>
                                    <p class="text-muted mb-0 small fw-semibold">{{ t('admin.instructor') || 'Instructor' }}</p>
                        </div>
                                <div class="bg-warning bg-gradient rounded-3 p-3 shadow flex-shrink-0 ms-2">
                                    <i class="bi bi-person text-white fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Batch Details -->
                <div class="col-12 col-lg-8">
                    <div class="card info-card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h2 class="h5 fw-bold mb-0 text-primary">
                                <i class="bi bi-info-circle me-2"></i>
                                {{ t('admin.batch_information') || 'Batch Information' }}
                            </h2>
                            </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded border">
                                        <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ t('admin.batch_name') || 'Batch Name' }}</label>
                                        <p class="h6 fw-bold mb-0">{{ batch?.name }}</p>
                                </div>
                                </div>
                                <div v-if="batch?.description" class="col-12">
                                    <div class="p-3 bg-light rounded border">
                                        <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ t('admin.description') || 'Description' }}</label>
                                        <p class="mb-0">{{ batch?.description }}</p>
                                    </div>
                                    </div>
                                <div class="col-12 col-md-6" v-if="batch?.start_date">
                                    <div class="p-3 bg-success bg-opacity-10 rounded border border-success">
                                        <label class="form-label text-success small fw-bold text-uppercase mb-2 d-flex align-items-center gap-2">
                                            <i class="bi bi-calendar-event"></i>
                                            {{ t('common.start_date') || 'Start Date' }}
                                        </label>
                                        <p class="fw-semibold mb-0">{{ formatDate(batch.start_date) }}</p>
                                </div>
                            </div>
                                <div class="col-12 col-md-6" v-if="batch?.end_date">
                                    <div class="p-3 bg-danger bg-opacity-10 rounded border border-danger">
                                        <label class="form-label text-danger small fw-bold text-uppercase mb-2 d-flex align-items-center gap-2">
                                            <i class="bi bi-calendar-x"></i>
                                            {{ t('common.end_date') || 'End Date' }}
                                        </label>
                                        <p class="fw-semibold mb-0">{{ formatDate(batch.end_date) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Students List -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light d-flex align-items-center justify-content-between">
                                <div>
                                <h2 class="h5 fw-bold mb-1">{{ t('admin.enrolled_students') || 'Enrolled Students' }}</h2>
                                <p class="text-muted small mb-0">{{ t('admin.manage_students_description') || 'View and manage enrolled students' }}</p>
                                </div>
                            <button 
                                v-if="can('batches.add-students')"
                                type="button"
                                    @click="showAddStudentsModal = true"
                                class="btn btn-primary btn-sm"
                            >
                                <i class="bi bi-plus-circle me-2"></i>
                                {{ t('admin.add_students') || 'Add Students' }}
                            </button>
                            </div>
                        <div class="card-body">
                            <div v-if="batch?.students && batch.students.length > 0" class="list-group list-group-flush">
                                <div 
                                    v-for="student in batch.students" 
                                    :key="student.id"
                                    class="list-group-item d-flex align-items-center justify-content-between p-3 border rounded mb-2 student-item"
                                >
                                    <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
                                        <div class="bg-primary bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 48px; height: 48px; font-size: 1.2rem;">
                                            {{ student.name?.[0]?.toUpperCase() || 'S' }}
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <h6 class="fw-bold mb-1 text-truncate">{{ student.name }}</h6>
                                            <p class="text-muted small mb-1 text-truncate">{{ student.email }}</p>
                                            <small class="text-muted d-flex align-items-center gap-1">
                                                <i class="bi bi-calendar3"></i>
                                                {{ t('admin.enrolled_at') || 'Enrolled' }}: {{ formatDate(student.enrolled_at) }}
                                            </small>
                                        </div>
                                    </div>
                                    <button 
                                        v-if="can('batches.remove-students')"
                                        type="button"
                                        @click="removeStudent(student)"
                                        :title="t('admin.remove_student') || 'Remove Student'"
                                        class="btn btn-outline-danger btn-sm remove-student-btn"
                                    >
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div v-else class="text-center py-5">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi bi-people text-muted" style="font-size: 2.5rem;"></i>
                            </div>
                                <h5 class="fw-semibold mb-2">{{ t('admin.no_students_enrolled') || 'No students enrolled yet' }}</h5>
                                <p class="text-muted small mb-0">{{ t('admin.add_students_to_get_started') || 'Add students to get started' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-12 col-lg-4">
                    <!-- Quick Actions -->
                    <div class="card actions-card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h3 class="h6 fw-bold mb-0">
                                <i class="bi bi-lightning-charge text-primary me-2"></i>
                                {{ t('admin.quick_actions') || 'Quick Actions' }}
                            </h3>
                            </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button 
                                    v-if="can('batches.edit')"
                                    type="button"
                                    @click="openBatchModal"
                                    class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-start"
                                >
                                    <i class="bi bi-pencil me-2"></i>
                                    {{ t('common.edit') || 'Edit Batch' }}
                                </button>
                        <button
                            v-if="can('batches.add-students')"
                            type="button"
                                    @click="showAddStudentsModal = true"
                                    class="btn btn-outline-success w-100 d-flex align-items-center justify-content-start"
                        >
                                    <i class="bi bi-person-plus me-2"></i>
                                    {{ t('admin.add_students') || 'Add Students' }}
                        </button>
                                <Link 
                                    v-if="can('courses.view-all')"
                                    :href="route('admin.courses.show', course.slug || course.id)" 
                                    class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start"
                                >
                                    <i class="bi bi-arrow-left me-2"></i>
                                    {{ t('admin.back_to_course') || 'Back to Course' }}
                                </Link>
                                        </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>

        <!-- Add Students Modal -->
        <AddStudentsModal
            :show="showAddStudentsModal"
            :available-students="availableStudents"
            :processing="addingStudents"
            @close="closeAddStudentsModal"
            @submit="handleAddStudents"
                        />

        <!-- Batch Form Modal -->
        <BatchForm
            :show="showBatchModal"
            :batch="batch"
            :form-data="batchForm"
            :errors="batchForm.errors"
            :processing="batchForm.processing"
            :instructors="instructors"
            @close="closeBatchModal"
            @submit="submitBatch"
        />
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, usePage, router, useForm } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useModal } from '@/composables/useModal';
import { useAlert } from '@/composables/useAlert';
import { usePermissions } from '@/composables/usePermissions';
import { ref, watch } from 'vue';
import BatchForm from '@/Pages/Admin/Batches/Form.vue';
import AddStudentsModal from '@/Pages/Admin/Batches/AddStudentsModal.vue';

const props = defineProps({
    course: Object,
    batch: Object,
    availableStudents: Array,
    instructors: Array,
});

const { t } = useTranslation();
const { route } = useRoute();
const { setModalOpen } = useModal();
const { showSuccess, showError, showConfirm } = useAlert();
const { can } = usePermissions();
const page = usePage();

const showAddStudentsModal = ref(false);
const addingStudents = ref(false);

// Batch edit modal state
const showBatchModal = ref(false);
const batchForm = useForm({
    name: '',
    name_ar: '',
    description: '',
    description_ar: '',
    instructor_id: null,
    start_date: null,
    end_date: null,
    max_students: null,
    is_active: true,
});

// Update modal state when dialog is open/closed
watch(showAddStudentsModal, (isOpen) => {
    setModalOpen(isOpen);
}, { immediate: true });

watch(showBatchModal, (isOpen) => {
    setModalOpen(isOpen);
}, { immediate: true });

const formatDate = (dateString) => {
    if (!dateString) return '-';
    try {
        const date = new Date(dateString);
        const locale = page.props.locale || 'en';
        return date.toLocaleDateString(locale === 'ar' ? 'ar-SA' : 'en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    } catch (e) {
        return dateString;
    }
};

const openBatchModal = () => {
    if (!props.batch) return;
    
    // Load batch data into form - fetch full batch details for editing
    router.get(route('admin.courses.batches.edit', [
        props.course.slug || props.course.id,
        props.batch.id
    ]), {}, {
        preserveState: true,
        preserveScroll: true,
        only: ['batch'],
        onSuccess: (page) => {
            const fullBatch = page.props.batch || props.batch;
            batchForm.name = fullBatch.name || '';
            batchForm.name_ar = fullBatch.name_ar || '';
            batchForm.description = fullBatch.description || '';
            batchForm.description_ar = fullBatch.description_ar || '';
            batchForm.instructor_id = fullBatch.instructor_id || null;
            batchForm.start_date = fullBatch.start_date || null;
            batchForm.end_date = fullBatch.end_date || null;
            batchForm.max_students = fullBatch.max_students || null;
            batchForm.is_active = fullBatch.is_active !== undefined ? fullBatch.is_active : true;
            showBatchModal.value = true;
        },
    });
};

const closeBatchModal = () => {
    showBatchModal.value = false;
    batchForm.reset();
    batchForm.clearErrors();
};

const submitBatch = (formData) => {
    // Sync formData values into batchForm to ensure Arabic fields are included
    if (formData) {
        batchForm.name = formData.name || '';
        batchForm.name_ar = formData.name_ar || '';
        batchForm.description = formData.description || '';
        batchForm.description_ar = formData.description_ar || '';
        batchForm.instructor_id = formData.instructor_id || null;
        batchForm.start_date = formData.start_date || null;
        batchForm.end_date = formData.end_date || null;
        batchForm.max_students = formData.max_students || null;
        batchForm.is_active = formData.is_active !== undefined ? formData.is_active : true;
    }
    
    // Prepare form data - convert empty strings to null for nullable fields (including Arabic fields)
    ['name_ar', 'description', 'description_ar', 'start_date', 'end_date', 'max_students'].forEach(field => {
        if (batchForm[field] === '' || batchForm[field] === null || batchForm[field] === undefined) {
            batchForm[field] = null;
        }
    });
    
    if (!batchForm.instructor_id || batchForm.instructor_id === '') {
        batchForm.instructor_id = null;
    } else {
        batchForm.instructor_id = parseInt(batchForm.instructor_id);
    }
    
    if (batchForm.max_students && batchForm.max_students !== '') {
        batchForm.max_students = parseInt(batchForm.max_students) || null;
    }
    
    batchForm.is_active = batchForm.is_active === true || batchForm.is_active === 'true' || batchForm.is_active === 1;
    
    // Update batch
    batchForm.put(route('admin.courses.batches.update', [
        props.course.slug || props.course.id,
        props.batch?.id
    ]), {
        preserveScroll: true,
        onSuccess: () => {
            showSuccess(t('batches.updated_successfully') || 'Batch updated successfully!', t('common.success') || 'Success');
            closeBatchModal();
        },
        onError: (errors) => {
            if (errors.message) {
                showError(errors.message, t('common.error') || 'Error');
            }
        },
    });
};

const closeAddStudentsModal = () => {
    showAddStudentsModal.value = false;
};

const handleAddStudents = (studentIds) => {
    if (studentIds.length === 0) {
        showError(t('admin.select_at_least_one_student') || 'Please select at least one student', t('common.error') || 'Error');
        return;
    }
    
    addingStudents.value = true;
    
    router.post(
        route('admin.courses.batches.add-students', [props.course.slug || props.course.id, props.batch?.id]),
        { student_ids: studentIds },
        {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess(t('admin.students_added_successfully') || 'Students added successfully!', t('common.success') || 'Success');
                closeAddStudentsModal();
                addingStudents.value = false;
            },
            onError: (errors) => {
                addingStudents.value = false;
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
                }
            },
        }
    );
};

const removeStudent = async (student) => {
    const result = await showConfirm(
        t('admin.confirm_remove_student') || 'Are you sure you want to remove this student?',
        t('admin.remove_student') || 'Remove Student',
        t('admin.delete_warning') || 'This action cannot be undone.'
    );
    
    if (result.isConfirmed) {
        router.delete(
            route('admin.courses.batches.remove-student', [props.course.slug || props.course.id, props.batch?.id, student.id]),
            {
                preserveScroll: true,
                onSuccess: () => {
                    showSuccess(
                        t('admin.student_removed_successfully') || `${student.name} has been removed from this batch`,
                        t('common.success') || 'Success'
                    );
                },
                onError: (errors) => {
                    if (errors.message) {
                        showError(errors.message, t('common.error') || 'Error');
                    } else {
                        showError(
                            t('admin.failed_to_remove_student') || 'Failed to remove student. Please try again.',
                            t('common.error') || 'Error'
                        );
                    }
                },
            }
        );
    }
};
</script>

<style scoped>
/* Bootstrap Card Enhancements */
.card {
    transition: all 0.3s ease;
    border-radius: 0.75rem;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.card-header {
    background: linear-gradient(to bottom, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #dee2e6;
    border-radius: 0.75rem 0.75rem 0 0 !important;
}

.card-body {
    padding: 1.5rem;
}

/* Statistics Cards */
.stat-card {
    transition: all 0.3s ease;
    border-top: 3px solid transparent !important;
}

.stat-card-blue {
    border-top-color: var(--bs-primary) !important;
}

.stat-card-green {
    border-top-color: var(--bs-success) !important;
}

.stat-card-purple {
    border-top-color: #6f42c1 !important;
}

.stat-card-orange {
    border-top-color: var(--bs-warning) !important;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.student-item {
    transition: all 0.2s ease;
}

.student-item:hover {
    background-color: #f8f9fa !important;
    border-color: var(--bs-primary) !important;
}

/* Bootstrap Button Enhancements */
.btn {
    font-weight: 500;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn:active {
    transform: translateY(0);
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

/* Badge Styles */
.badge {
    font-weight: 600;
    padding: 0.5rem 0.75rem;
}

/* Dialog Styles - Enhanced for Add Students Modal */
:deep(.p-dialog-mask) {
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    animation: fadeIn 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

:deep(.p-dialog) {
    border-radius: 1.25rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(0, 0, 0, 0.05);
    border: none;
    background: white;
    animation: slideDown 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    max-width: 90vw;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-30px) scale(0.96);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

:deep(.p-dialog-header) {
    padding: 1.75rem 2rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    background: linear-gradient(to right, #f8fafc, #ffffff);
    border-radius: 1.25rem 1.25rem 0 0;
    position: relative;
}

:deep(.p-dialog-header::after) {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(0, 0, 0, 0.1), transparent);
}

:deep(.p-dialog-header .p-dialog-title) {
    font-size: 1.375rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
    letter-spacing: -0.02em;
}

:deep(.p-dialog-header .p-dialog-header-icon) {
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 0.625rem;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
}

:deep(.p-dialog-header .p-dialog-header-icon:hover) {
    background: rgba(0, 0, 0, 0.08);
    color: #1f2937;
    transform: rotate(90deg);
}

:deep(.p-dialog-content) {
    padding: 2rem;
    background: white;
    max-height: calc(100vh - 220px);
    overflow-y: auto;
}

/* Custom scrollbar for dialog content */
:deep(.p-dialog-content::-webkit-scrollbar) {
    width: 10px;
}

:deep(.p-dialog-content::-webkit-scrollbar-track) {
    background: #f1f5f9;
    border-radius: 5px;
    margin: 0.5rem 0;
}

:deep(.p-dialog-content::-webkit-scrollbar-thumb) {
    background: linear-gradient(to bottom, #cbd5e1, #94a3b8);
    border-radius: 5px;
    border: 2px solid #f1f5f9;
}

:deep(.p-dialog-content::-webkit-scrollbar-thumb:hover) {
    background: linear-gradient(to bottom, #94a3b8, #64748b);
}

:deep(.p-dialog-footer) {
    padding: 1.5rem 2rem;
    border-top: 1px solid rgba(0, 0, 0, 0.08);
    background: linear-gradient(to right, #ffffff, #f8fafc);
    border-radius: 0 0 1.25rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
}

:deep(.p-dialog-footer::before) {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(0, 0, 0, 0.1), transparent);
}


/* Student List Item Styles */
.student-item {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.student-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(to bottom, transparent, rgba(59, 130, 246, 0.5), transparent);
    border-radius: 0 4px 4px 0;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.student-item:hover::before {
    opacity: 1;
}

/* Empty State Styles */
.text-center.py-12 {
    background: linear-gradient(to bottom, #f8fafc, #ffffff);
    border-radius: 0.875rem;
    border: 2px dashed #e5e7eb;
    transition: all 0.3s ease;
}

.text-center.py-12:hover {
    border-color: #cbd5e1;
    background: linear-gradient(to bottom, #f1f5f9, #fafbfc);
}

.text-center.py-12 .w-16.h-16 {
    transition: transform 0.3s ease;
}

.text-center.py-12:hover .w-16.h-16 {
    transform: scale(1.1);
}

/* Student list container scrollbar */
div.max-h-96.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

div.max-h-96.overflow-y-auto::-webkit-scrollbar {
    width: 10px;
}

div.max-h-96.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 5px;
    margin: 0.5rem 0;
}

div.max-h-96.overflow-y-auto::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #cbd5e1, #94a3b8);
    border-radius: 5px;
    border: 2px solid #f1f5f9;
}

div.max-h-96.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #94a3b8, #64748b);
}

/* Info section in modal */
div.flex.items-center.justify-between.mb-4.p-3 {
    transition: all 0.3s ease;
    border-radius: 0.875rem;
}

div.flex.items-center.justify-between.mb-4.p-3:hover {
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
    transform: translateY(-1px);
}

/* Select all section */
div.flex.items-center.justify-between.bg-gray-50 {
    border-radius: 0.75rem;
    transition: all 0.2s ease;
}

div.flex.items-center.justify-between.bg-gray-50:hover {
    background: #f9fafb;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .stat-card:hover {
        transform: none;
}

    :deep(.p-dialog) {
        max-width: 95vw;
    }
}

@media (max-width: 768px) {
    :deep(.p-dialog) {
        width: 95% !important;
        margin: 1rem;
        max-width: calc(100vw - 2rem);
        border-radius: 1rem;
    }
    
    :deep(.p-dialog-header) {
        padding: 1.25rem 1.5rem;
}

    :deep(.p-dialog-content) {
        padding: 1.5rem;
    }
    
    :deep(.p-dialog-footer) {
        padding: 1rem 1.25rem;
}

    .modal-footer-wrapper {
        flex-direction: column;
        gap: 1rem;
    }
    
    .modal-footer-left,
    .modal-footer-right {
        width: 100%;
}

    .modal-footer-right {
        justify-content: center;
    }
    
    .modal-cancel-btn,
    .modal-submit-btn {
        width: 100% !important;
        justify-content: center !important;
}

    .selected-count-badge {
        width: 100%;
        text-align: center;
    }
    
    :deep(.p-button) {
        padding: 0.625rem 1.25rem;
        font-size: 0.8125rem;
}

    :deep(.p-button:hover:not(:disabled)) {
        transform: none;
}

.student-select-item:hover {
    transform: translateX(2px);
}

    .student-item:hover {
        transform: translateX(2px);
    }
    
    .add-students-dialog :deep(.p-dialog-header .p-dialog-title) {
        font-size: 1.25rem;
    }
    
    [dir="rtl"] .modal-footer-wrapper {
        flex-direction: column;
    }
}

@media (max-width: 640px) {
    :deep(.p-dialog-header),
    :deep(.p-dialog-content),
    :deep(.p-dialog-footer) {
        padding: 1rem;
    }
    
    :deep(.p-dialog-header .p-dialog-title) {
        font-size: 1.125rem;
}

    .student-select-item {
        padding: 0.875rem;
    }
    
    :deep(.p-button) {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
    }
}

/* RTL Support for Arabic */
[dir="rtl"] .add-students-dialog :deep(.p-dialog-header) {
    text-align: right;
}

[dir="rtl"] .add-students-dialog :deep(.p-dialog-content) {
    text-align: right;
}

[dir="rtl"] .add-students-dialog :deep(.p-dialog-footer) {
    flex-direction: row-reverse;
}

[dir="rtl"] .student-select-item {
    border-left: none;
    border-right: 4px solid transparent;
}

[dir="rtl"] .student-select-item::before {
    left: auto;
    right: 0;
}

[dir="rtl"] .student-select-item:hover {
    transform: translateX(-4px);
    border-right-color: #667eea;
}

[dir="rtl"] .student-select-item:has(input:checked) {
    transform: translateX(-2px);
    border-right-color: #667eea;
}

[dir="rtl"] :deep(.p-checkbox) {
    margin-right: 0;
    margin-left: 1rem;
}

[dir="rtl"] .student-item:hover {
    transform: translateX(-4px);
}

/* Search Input RTL Support */
.search-input {
    padding-left: 2.75rem;
    padding-right: 1rem;
}

[dir="rtl"] .search-input {
    padding-right: 2.75rem;
    padding-left: 1rem;
}

.search-icon {
    left: 0.75rem;
}

[dir="rtl"] .search-icon {
    left: auto;
    right: 0.75rem;
}

[dir="rtl"] :deep(.p-dialog-content input[type="text"]) {
    padding-right: 2.75rem;
    padding-left: 1rem;
}

[dir="rtl"] :deep(.p-dialog-content .pi-search) {
    left: auto;
    right: 0.75rem;
    }
    
[dir="rtl"] button[type="button"].text-sm:hover {
    transform: translateX(-2px);
}

[dir="rtl"] button[type="button"].text-sm:active {
    transform: translateX(0);
}

[dir="rtl"] .select-all-btn {
    order: 2;
}

[dir="rtl"] .select-all-btn + span {
    order: 1;
}

[dir="rtl"] .modal-info-header {
    flex-direction: row-reverse;
}

[dir="rtl"] .selected-badge {
    margin-left: 0;
    margin-right: auto;
}

/* RTL Modal Footer */
[dir="rtl"] .modal-footer-wrapper {
    flex-direction: row-reverse;
    }

[dir="rtl"] .modal-footer-right {
    flex-direction: row-reverse;
    justify-content: flex-start;
}

/* Ensure student list items styling */
.student-select-item .font-semibold {
    color: #1f2937;
    font-weight: 600;
    }

.student-select-item .text-sm.text-gray-500 {
    color: #6b7280;
    font-size: 0.875rem;
}

/* Remove Student Button - Bootstrap Style */
.remove-student-btn {
    opacity: 0.7;
    transition: all 0.2s ease;
}

.student-item:hover .remove-student-btn {
    opacity: 1;
}

.remove-student-btn:hover {
    transform: scale(1.1);
    opacity: 1 !important;
}

.remove-student-btn:active {
    transform: scale(0.95);
}

/* Status Card Styles */
.status-card {
    position: relative;
}

.status-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
}

.status-card .stat-card::before {
    background: linear-gradient(90deg, transparent, rgba(139, 92, 246, 0.3), transparent);
    }
    
/* RTL Remove Button */
[dir="rtl"] .remove-student-btn {
    margin-left: 0;
    margin-right: auto;
    }
    
/* Ensure delete button is visible in student items */
.student-item {
    position: relative;
    }
    
.student-item .remove-student-btn {
    opacity: 0.8;
    }
    
.student-item:hover .remove-student-btn {
    opacity: 1;
}
</style>

