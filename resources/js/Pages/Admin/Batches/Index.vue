<template>
    <AdminLayout :page-title="t('admin.batches_management') || 'Batches Management'">
        <Head :title="t('admin.batches_management') || 'Batches Management'" />
        
        <div class="container-fluid px-3 px-md-4 px-lg-5 py-4">
            <!-- Page Header - Bootstrap Style -->
            <div class="card border-0 shadow-lg mb-4 bg-gradient header-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4 p-md-5 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 w-100 h-100 opacity-10" style="background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);"></div>
                    <div class="row align-items-center position-relative">
                        <div class="col-12 col-md-8 mb-3 mb-md-0">
                            <nav aria-label="breadcrumb" class="mb-3">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <Link :href="route('admin.courses.index')" class="text-white text-decoration-none opacity-75 hover-opacity-100">{{ t('admin.courses_management') || 'Courses' }}</Link>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <span class="text-white opacity-50 mx-2">/</span>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <Link :href="route('admin.courses.show', course.slug || course.id)" class="text-white text-decoration-none opacity-75 hover-opacity-100">{{ course.title }}</Link>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <span class="text-white opacity-50 mx-2">/</span>
                                    </li>
                                    <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">{{ t('admin.batches') || 'Batches' }}</li>
                                </ol>
                            </nav>
                            <h1 class="h2 fw-bold text-white mb-2" style="font-size: 2rem; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">{{ t('admin.batches_management') || 'Batches Management' }}</h1>
                            <p class="text-white mb-0 opacity-90" style="font-size: 1rem;">{{ t('admin.batches_description') || 'Manage course batches and student enrollments' }}</p>
                        </div>
                        <div class="col-12 col-md-4 text-md-end">
                            <button 
                                type="button"
                                @click="openBatchModal(null)"
                                class="btn btn-light btn-lg shadow-sm fw-semibold create-batch-btn"
                                style="font-size: 1rem; padding: 0.75rem 1.5rem;"
                            >
                                <i class="bi bi-plus-circle me-2"></i>
                                {{ t('admin.create_batch') || 'Create Batch' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            <div v-if="page.props.flash?.success" class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <div>{{ page.props.flash.success }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <!-- Batches Grid - Bootstrap Style -->
            <div v-if="batches && batches.length > 0" class="row g-4">
                <div 
                    v-for="batch in batches" 
                    :key="batch.id"
                    class="col-12 col-sm-6 col-lg-4"
                >
                    <div 
                        class="card batch-card h-100 border-0 shadow-sm cursor-pointer"
                    @click="navigateToBatch(batch)"
                >
                        <div class="card-body p-4">
                            <!-- Batch Header -->
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="flex-grow-1 min-w-0 me-3">
                                    <h5 class="card-title fw-bold mb-2 text-dark" style="font-size: 1.1rem; line-height: 1.4;">{{ batch.name }}</h5>
                                    <p v-if="batch.description" class="text-muted small mb-0 line-clamp-2" style="font-size: 0.875rem;">{{ batch.description }}</p>
                                </div>
                                <span 
                                    class="badge flex-shrink-0 rounded-pill px-3 py-2"
                                    :class="batch.is_active ? 'bg-success' : 'bg-secondary'"
                                    style="font-size: 0.75rem; font-weight: 600;"
                                >
                                    <i 
                                        class="bi me-1"
                                        :class="batch.is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill'"
                                    ></i>
                                    {{ batch.is_active ? t('common.active') : t('common.inactive') }}
                                </span>
                            </div>

                            <!-- Batch Info -->
                            <div class="border-top pt-3 mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.875rem;">
                                        <i class="bi bi-person-fill text-primary" style="font-size: 1rem;"></i>
                                        <span class="fw-semibold">{{ t('admin.instructor') || 'Instructor' }}:</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                        <span class="text-dark fw-semibold" style="font-size: 0.875rem;">{{ batch.instructor?.name || '-' }}</span>
                                        <span class="badge bg-info text-white fw-bold d-flex align-items-center gap-1" style="font-size: 0.75rem; padding: 0.35rem 0.65rem;">
                                            <i class="bi bi-people-fill"></i>
                                            {{ batch.enrollments_count || 0 }}
                                        </span>
                                    </div>
                                </div>
                                <div v-if="batch.start_date" class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.875rem;">
                                        <i class="bi bi-calendar-event-fill text-success" style="font-size: 1rem;"></i>
                                        <span class="fw-semibold">{{ t('common.start_date') || 'Start' }}:</span>
                                    </div>
                                    <span class="text-dark fw-medium" style="font-size: 0.875rem;">{{ formatDate(batch.start_date) }}</span>
                                </div>
                                <div v-if="batch.end_date" class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.875rem;">
                                        <i class="bi bi-calendar-x-fill text-danger" style="font-size: 1rem;"></i>
                                        <span class="fw-semibold">{{ t('common.end_date') || 'End' }}:</span>
                                </div>
                                    <span class="text-dark fw-medium" style="font-size: 0.875rem;">{{ formatDate(batch.end_date) }}</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="d-flex gap-2 border-top pt-3" @click.stop>
                                <button 
                                    type="button"
                                    @click.stop="openBatchModal(batch)"
                                    class="btn btn-outline-primary btn-sm flex-fill fw-semibold"
                                    style="font-size: 0.875rem; padding: 0.5rem 1rem;"
                                >
                                    <i class="bi bi-pencil me-2"></i>
                                    {{ t('common.edit') || 'Edit' }}
                                </button>
                                <Link 
                                    :href="route('admin.courses.batches.show', [course.slug || course.id, batch.id])"
                                    class="btn btn-outline-secondary btn-sm flex-fill fw-semibold"
                                    style="font-size: 0.875rem; padding: 0.5rem 1rem;"
                                >
                                    <i class="bi bi-eye me-2"></i>
                                    {{ t('common.view') || 'View' }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="bg-primary bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                        <i class="bi bi-people text-white" style="font-size: 2.5rem;"></i>
                    </div>
                    <h3 class="h5 fw-semibold mb-2">{{ t('admin.no_batches') || 'No batches yet' }}</h3>
                    <p class="text-muted mb-4">{{ t('admin.no_batches_description') || 'Create your first batch to start enrolling students' }}</p>
                    <button 
                        type="button"
                        @click="openBatchModal(null)"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-plus-circle me-2"></i>
                        {{ t('admin.create_batch') || 'Create Batch' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Batch Form Modal -->
        <BatchForm
            :show="showBatchModal"
            :batch="editingBatch"
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
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useAlert } from '@/composables/useAlert';
import { router } from '@inertiajs/vue3';
import BatchForm from '@/Pages/Admin/Batches/Form.vue';

const props = defineProps({
    course: Object,
    batches: Array,
    instructors: Array,
    batch: Object, // For edit mode
});

const { t } = useTranslation();
const { route } = useRoute();
const { showSuccess, showError } = useAlert();
const page = usePage();

// Modal state
const showBatchModal = ref(false);
const editingBatch = ref(null);

// Batch form
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

const navigateToBatch = (batch) => {
    router.visit(route('admin.courses.batches.show', [props.course.slug || props.course.id, batch.id]));
};

const openBatchModal = (batch = null) => {
    editingBatch.value = batch;
    batchForm.reset();
    
    if (batch) {
        // Load batch data - fetch full batch details for editing
        router.get(route('admin.courses.batches.edit', [
            props.course.slug || props.course.id,
            batch.id
        ]), {}, {
            preserveState: true,
            preserveScroll: true,
            only: ['batch'],
            onSuccess: (page) => {
                const fullBatch = page.props.batch || batch;
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
    } else {
        // Creating new batch
        showBatchModal.value = true;
    }
};

const closeBatchModal = () => {
    showBatchModal.value = false;
    editingBatch.value = null;
    batchForm.reset();
    batchForm.clearErrors();
};

const submitBatch = (formData) => {
    // Prepare form data - convert empty strings to null for nullable fields
    ['name_ar', 'description', 'description_ar', 'start_date', 'end_date', 'max_students'].forEach(field => {
        if (batchForm[field] === '' || batchForm[field] === null) {
            batchForm[field] = null;
        }
    });
    
    // Ensure instructor_id is properly set
    if (!batchForm.instructor_id || batchForm.instructor_id === '') {
        batchForm.instructor_id = null;
    } else {
        batchForm.instructor_id = parseInt(batchForm.instructor_id);
    }
    
    // Ensure max_students is integer or null
    if (batchForm.max_students && batchForm.max_students !== '') {
        batchForm.max_students = parseInt(batchForm.max_students) || null;
    }
    
    // Ensure is_active is boolean
    batchForm.is_active = batchForm.is_active === true || batchForm.is_active === 'true' || batchForm.is_active === 1;
    
    if (editingBatch.value) {
        // Update existing batch
        batchForm.put(route('admin.courses.batches.update', [
            props.course.slug || props.course.id,
            editingBatch.value.id
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
    } else {
        // Create new batch
        batchForm.post(route('admin.courses.batches.store', [
            props.course.slug || props.course.id
        ]), {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess(t('batches.created_successfully') || 'Batch created successfully!', t('common.success') || 'Success');
                closeBatchModal();
            },
            onError: (errors) => {
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
                }
            },
        });
    }
};
</script>

<style scoped>
/* Header Card Styles */
.header-card {
    border-radius: 1rem !important;
    overflow: hidden;
}

.header-card .card-body {
    position: relative;
    z-index: 1;
}

.header-card .breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 1rem;
}

.header-card .breadcrumb-item + .breadcrumb-item::before {
    display: none;
}

.header-card .breadcrumb-item a {
    transition: opacity 0.2s ease;
}

.header-card .breadcrumb-item a:hover {
    opacity: 1 !important;
}

.create-batch-btn {
    transition: all 0.3s ease;
    border: 2px solid rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.95);
    color: #667eea;
    font-weight: 600;
}

.create-batch-btn:hover {
    background: #ffffff;
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    color: #764ba2;
}

.create-batch-btn:active {
    transform: translateY(0);
}

/* RTL Support for Header */
[dir="rtl"] .header-card .breadcrumb-item {
    padding-right: 0;
    padding-left: 0;
}

[dir="rtl"] .header-card .text-md-end {
    text-align: left !important;
}

[dir="rtl"] .header-card .col-md-8 {
    order: 2;
}

[dir="rtl"] .header-card .col-md-4 {
    order: 1;
    text-align: left !important;
}

/* Bootstrap Card Enhancements */
.card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 0.75rem;
    border: 1px solid rgba(0, 0, 0, 0.08);
}

.batch-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: #ffffff;
}

.batch-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.12) !important;
    border-color: rgba(13, 110, 253, 0.2) !important;
}

.card-body {
    padding: 1.5rem;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.cursor-pointer {
    cursor: pointer;
}

/* Badge Styles */
.badge {
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.badge.bg-success {
    background-color: #198754 !important;
    color: #ffffff;
}

.badge.bg-info {
    background-color: #0dcaf0 !important;
    color: #000000;
}

/* Button Styles */
.btn-outline-primary {
    border-color: #0d6efd;
    color: #0d6efd;
    transition: all 0.2s ease;
}

.btn-outline-primary:hover {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.25);
}

.btn-outline-secondary {
    border-color: #6c757d;
    color: #6c757d;
    transition: all 0.2s ease;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(108, 117, 125, 0.25);
}

.btn-sm {
    font-weight: 500;
    letter-spacing: 0.01em;
}

/* RTL Support */
[dir="rtl"] .batch-card .card-body {
    text-align: right;
}

[dir="rtl"] .badge {
    margin-left: 0;
    margin-right: 0.5rem;
}

[dir="rtl"] .me-2 {
    margin-right: 0 !important;
    margin-left: 0.5rem !important;
}

[dir="rtl"] .me-3 {
    margin-right: 0 !important;
    margin-left: 1rem !important;
}

[dir="rtl"] .ms-2 {
    margin-left: 0 !important;
    margin-right: 0.5rem !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .batch-card:hover {
        transform: translateY(-2px);
    }
    
    .card-body {
        padding: 1.25rem;
    }
}

@media (max-width: 576px) {
    .batch-card:hover {
        transform: none;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .btn-sm {
        font-size: 0.8125rem;
        padding: 0.4rem 0.75rem;
    }
}
</style>

