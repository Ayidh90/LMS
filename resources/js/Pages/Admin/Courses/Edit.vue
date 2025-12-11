<template>
    <AdminLayout :page-title="t('admin.edit_course') || 'Edit Course'">
        <Head :title="t('admin.edit_course') || 'Edit Course'" />
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
                        <li class="breadcrumb-item active" aria-current="page">{{ t('admin.edit_course') || 'Edit Course' }}</li>
                    </ol>
                </nav>
                <h1 class="h2 fw-bold mb-2">{{ t('admin.edit_course') }}</h1>
                <p class="text-muted mb-0">{{ course.translated_title || course.title }}</p>
            </div>

            <!-- Quick Actions - Bootstrap Style -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <Link
                            :href="route('admin.courses.sections.index', course.slug || course.id)"
                            class="btn btn-outline-secondary btn-sm"
                        >
                            <i class="bi bi-layers me-2"></i>
                            {{ t('admin.manage_sections') || 'Manage Chapters' }}
                        </Link>
                        <Link
                            :href="route('admin.courses.lessons.index', course.slug || course.id)"
                            class="btn btn-outline-secondary btn-sm"
                        >
                            <i class="bi bi-book me-2"></i>
                            {{ t('admin.manage_lessons') || 'Manage Lessons' }}
                        </Link>
                        <Link
                            :href="route('admin.courses.batches.index', course.slug || course.id)"
                            class="btn btn-outline-secondary btn-sm"
                        >
                            <i class="bi bi-people me-2"></i>
                            {{ t('admin.manage_batches') || 'Manage Batches' }}
                        </Link>
                    </div>
                </div>
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
                                ></textarea>
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
                                >
                                    <option value="beginner">{{ t('courses.levels.beginner') }}</option>
                                    <option value="intermediate">{{ t('courses.levels.intermediate') }}</option>
                                    <option value="advanced">{{ t('courses.levels.advanced') }}</option>
                                </select>
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
                                    class="form-control form-control-lg"
                                />
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
                                    <div v-else-if="course.thumbnail_url" class="w-100">
                                        <img :src="course.thumbnail_url" class="img-fluid rounded" alt="Current thumbnail" />
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
                                />
                                <div class="form-text">{{ t('courses.thumbnail_hint') || 'Recommended: 1200x800px, max 2MB' }}</div>
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
                <div class="card-footer bg-light border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <button
                        v-if="!course.is_published"
                        type="button"
                        @click="confirmDelete"
                        class="btn btn-outline-danger"
                    >
                        <i class="bi bi-trash me-2"></i>
                        {{ t('common.delete') || 'Delete' }}
                    </button>
                    <div v-else></div>
                    <div class="d-flex gap-2">
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
                            <i v-else class="bi bi-check-circle me-2"></i>
                            {{ t('common.save_changes') || 'Save Changes' }}
                        </button>
                    </div>
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

const props = defineProps({
    course: Object,
});

const { t } = useTranslation();
const { route } = useRoute();
const { showSuccess, showError } = useAlert();

const form = useForm({
    title: props.course.title || '',
    title_ar: props.course.title_ar || '',
    description: props.course.description || '',
    description_ar: props.course.description_ar || '',
    level: props.course.level || 'beginner',
    duration_hours: props.course.duration_hours || 0,
    thumbnail: null,
    is_published: props.course.is_published || false,
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
    // Ensure title is trimmed and not empty
    if (!form.title || !form.title.trim()) {
        showError(
            t('courses.title_required') || 'The course title is required.',
            t('common.error') || 'Error'
        );
        return;
    }

    form.transform((data) => {
        // Trim string fields and ensure they're properly formatted
        const formData = { ...data };
        
        // Always trim and ensure title is present
        formData.title = (formData.title || '').trim();
        
        // Trim optional string fields
        formData.title_ar = formData.title_ar ? formData.title_ar.trim() : null;
        formData.description = formData.description ? formData.description.trim() : null;
        formData.description_ar = formData.description_ar ? formData.description_ar.trim() : null;
        
        // Only include thumbnail if it's a file, otherwise remove it
        if (!formData.thumbnail || !(formData.thumbnail instanceof File)) {
            delete formData.thumbnail;
        }
        
        // Ensure boolean values are properly set
        formData.is_published = Boolean(formData.is_published);
        
        // Ensure numeric values
        formData.duration_hours = parseInt(formData.duration_hours) || 0;
        
        return formData;
    }).put(route('admin.courses.update', props.course.slug || props.course.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            showSuccess(
                t('courses.updated_successfully') || 'Course updated successfully!',
                t('common.success') || 'Success'
            );
        },
        onError: (errors) => {
            if (errors.message) {
                showError(errors.message, t('common.error') || 'Error');
            }
        },
    });
};

const confirmDelete = () => {
    if (confirm(t('courses.confirm_delete') || 'Are you sure you want to delete this course?')) {
        router.delete(route('admin.courses.destroy', props.course.slug || props.course.id));
    }
};
</script>

