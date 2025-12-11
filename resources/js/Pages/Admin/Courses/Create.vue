<template>
    <AdminLayout :page-title="t('admin.create_course') || 'Create Course'">
        <Head :title="t('admin.create_course') || 'Create Course'" />
        <div class="container-fluid px-3 px-md-4 px-lg-5 py-4">
            <!-- Page Header - Bootstrap Style -->
            <div class="mb-4">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item">
                            <Link :href="route('admin.courses.index')" class="text-decoration-none">
                                {{ t('admin.courses_management') || 'Courses' }}
                            </Link>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ t('admin.create_course') || 'Create Course' }}</li>
                    </ol>
                </nav>
                <h1 class="h2 fw-bold mb-2">{{ t('admin.create_course') }}</h1>
                <p class="text-muted mb-0">{{ t('admin.create_course_description') || 'Fill in the details to create a new course' }}</p>
            </div>

            <!-- Form Card - Bootstrap Style -->
            <form @submit.prevent="submit" class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <!-- Basic Info Section -->
                    <div class="mb-5">
                        <h3 class="h5 fw-semibold mb-4 d-flex align-items-center gap-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary p-2 rounded">
                                <i class="bi bi-info-circle"></i>
                            </span>
                            {{ t('courses.basic_info') || 'Basic Information' }}
                        </h3>
                        
                        <div class="row g-4">
                            <!-- Title English -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">
                                    {{ t('courses.fields.title') }} ({{ t('common.language_english') }}) <span class="text-danger">*</span>
                                </label>
                                <input
                                    v-model="form.title"
                                    type="text"
                                    class="form-control form-control-lg"
                                    :class="{ 'is-invalid': form.errors.title }"
                                    :placeholder="t('courses.placeholders.title') || 'Enter course title'"
                                />
                                <div v-if="form.errors.title" class="invalid-feedback d-flex align-items-center gap-1">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ form.errors.title }}
                                </div>
                            </div>
                            
                            <!-- Title Arabic -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">
                                    {{ t('courses.fields.title') }} ({{ t('common.language_arabic') }})
                                </label>
                                <input
                                    v-model="form.title_ar"
                                    type="text"
                                    dir="rtl"
                                    class="form-control form-control-lg"
                                    :placeholder="t('courses.placeholders.title') || 'أدخل عنوان الدورة'"
                                />
                            </div>
                            
                            <!-- Description English -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    {{ t('courses.fields.description') }} ({{ t('common.language_english') }})
                                </label>
                                <textarea
                                    v-model="form.description"
                                    rows="4"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.description }"
                                    :placeholder="t('courses.placeholders.description') || 'Enter course description'"
                                ></textarea>
                                <div v-if="form.errors.description" class="invalid-feedback d-flex align-items-center gap-1">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ form.errors.description }}
                                </div>
                            </div>
                            
                            <!-- Description Arabic -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    {{ t('courses.fields.description') }} ({{ t('common.language_arabic') }})
                                </label>
                                <textarea
                                    v-model="form.description_ar"
                                    rows="4"
                                    dir="rtl"
                                    class="form-control"
                                    :placeholder="t('courses.placeholders.description') || 'أدخل وصف الدورة'"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Course Details Section -->
                    <div class="mb-5 pt-5 border-top">
                        <h3 class="h5 fw-semibold mb-4 d-flex align-items-center gap-2">
                            <span class="badge bg-info bg-opacity-10 text-info p-2 rounded">
                                <i class="bi bi-gear"></i>
                            </span>
                            {{ t('courses.details') || 'Course Details' }}
                        </h3>
                        
                        <div class="row g-4">
                            <!-- Level -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">
                                    {{ t('courses.fields.level') }} <span class="text-danger">*</span>
                                </label>
                                <select
                                    v-model="form.level"
                                    class="form-select form-select-lg"
                                    :class="{ 'is-invalid': form.errors.level }"
                                >
                                    <option value="">{{ t('common.select') || 'Select level' }}</option>
                                    <option value="beginner">{{ t('courses.levels.beginner') }}</option>
                                    <option value="intermediate">{{ t('courses.levels.intermediate') }}</option>
                                    <option value="advanced">{{ t('courses.levels.advanced') }}</option>
                                </select>
                                <div v-if="form.errors.level" class="invalid-feedback d-flex align-items-center gap-1">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ form.errors.level }}
                                </div>
                            </div>
                            
                            <!-- Duration -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">
                                    {{ t('courses.fields.duration') }} ({{ t('common.hours') }}) <span class="text-danger">*</span>
                                </label>
                                <input
                                    v-model="form.duration_hours"
                                    type="number"
                                    min="0"
                                    step="0.5"
                                    class="form-control form-control-lg"
                                    :class="{ 'is-invalid': form.errors.duration_hours }"
                                    placeholder="0"
                                />
                                <div v-if="form.errors.duration_hours" class="invalid-feedback d-flex align-items-center gap-1">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ form.errors.duration_hours }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thumbnail Section -->
                    <div class="mb-5 pt-5 border-top">
                        <h3 class="h5 fw-semibold mb-4 d-flex align-items-center gap-2">
                            <span class="badge bg-success bg-opacity-10 text-success p-2 rounded">
                                <i class="bi bi-image"></i>
                            </span>
                            {{ t('courses.thumbnail') || 'Course Thumbnail' }}
                        </h3>
                        
                        <div class="row g-4">
                            <!-- Preview -->
                            <div class="col-12 col-md-4">
                                <div class="border-2 border-dashed rounded p-3 text-center bg-light d-flex align-items-center justify-content-center" style="min-height: 200px;">
                                    <div v-if="thumbnailPreview" class="w-100">
                                        <img :src="thumbnailPreview" class="img-fluid rounded" alt="Preview" />
                                    </div>
                                    <div v-else class="text-muted">
                                        <i class="bi bi-image fs-1 d-block mb-2"></i>
                                        <small>{{ t('common.no_image') || 'No image' }}</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Upload -->
                            <div class="col-12 col-md-8">
                                <label class="form-label fw-semibold">{{ t('common.choose_thumbnail') || 'Choose Thumbnail' }}</label>
                                <input
                                    type="file"
                                    @change="handleThumbnail"
                                    accept="image/*"
                                    class="form-control form-control-lg"
                                    :class="{ 'is-invalid': form.errors.thumbnail }"
                                />
                                <div class="form-text">{{ t('courses.thumbnail_hint') || 'Recommended: 1200x800px, max 2MB' }}</div>
                                <div v-if="form.errors.thumbnail" class="invalid-feedback d-flex align-items-center gap-1">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ form.errors.thumbnail }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Publishing Section -->
                    <div class="mb-4 pt-5 border-top">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                    <div>
                                        <h5 class="mb-1 fw-semibold">{{ t('courses.publish_course') || 'Publish Course' }}</h5>
                                        <p class="text-muted small mb-0">{{ t('courses.publish_hint') || 'Make this course visible to students' }}</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input 
                                            class="form-check-input" 
                                            type="checkbox" 
                                            id="publishSwitch"
                                            v-model="form.is_published"
                                            style="width: 3rem; height: 1.5rem; cursor: pointer;"
                                        />
                                        <label class="form-check-label" for="publishSwitch"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions - Bootstrap Style -->
                <div class="card-footer bg-light border-top d-flex flex-column flex-md-row justify-content-end gap-3">
                    <Link
                        :href="route('admin.courses.index')"
                        class="btn btn-outline-secondary btn-lg"
                    >
                        {{ t('common.cancel') || 'Cancel' }}
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="btn btn-primary btn-lg"
                    >
                        <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        <i v-else class="bi bi-plus-circle me-2"></i>
                        {{ form.processing ? (t('common.creating') || 'Creating...') : (t('common.create') || 'Create Course') }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useAlert } from '@/composables/useAlert';
import { Head, Link, useForm, router } from '@inertiajs/vue3';


const { t } = useTranslation();
const { route } = useRoute();
const { showSuccess, showError } = useAlert();

const form = useForm({
    title: '',
    title_ar: '',
    description: '',
    description_ar: '',
    level: '',
    duration_hours: 0,
    thumbnail: null,
    is_published: false,
});

const thumbnailPreview = ref(null);

const handleThumbnail = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.thumbnail = file;
        thumbnailPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('admin.courses.store'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            showSuccess(
                t('courses.created_successfully') || 'Course created successfully!',
                t('common.success') || 'Success'
            );
            // Redirect to courses index after a short delay to show the alert
            setTimeout(() => {
                router.visit(route('admin.courses.index'));
            }, 1500);
        },
        onError: (errors) => {
            if (errors.message) {
                showError(errors.message, t('common.error') || 'Error');
            }
        },
    });
};
</script>

