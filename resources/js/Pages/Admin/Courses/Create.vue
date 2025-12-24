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
            <form @submit.prevent="submit" class="card shadow-sm border-0" enctype="multipart/form-data">
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
                                <label for="title" class="form-label fw-semibold">
                                    {{ t('courses.fields.title') }} ({{ t('common.language_english') }}) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-translate text-primary"></i>
                                    </span>
                                    <input
                                        id="title"
                                        v-model="form.title"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.title }"
                                        :placeholder="t('courses.placeholders.title') || 'Enter course title'"
                                        required
                                    />
                                    <div v-if="form.errors.title" class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        {{ form.errors.title }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Title Arabic -->
                            <div class="col-12 col-md-6">
                                <label for="title_ar" class="form-label fw-semibold">
                                    {{ t('courses.fields.title') }} ({{ t('common.language_arabic') }})
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-translate text-success"></i>
                                    </span>
                                    <input
                                        id="title_ar"
                                        v-model="form.title_ar"
                                        type="text"
                                        dir="rtl"
                                        class="form-control"
                                        :placeholder="t('courses.placeholders.title') || 'أدخل عنوان الدورة'"
                                    />
                                </div>
                            </div>
                            
                            <!-- Description English -->
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">
                                    {{ t('courses.fields.description') }} ({{ t('common.language_english') }})
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light align-items-start pt-3">
                                        <i class="bi bi-card-text text-primary"></i>
                                    </span>
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="4"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.description }"
                                        :placeholder="t('courses.placeholders.description') || 'Enter course description'"
                                    ></textarea>
                                    <div v-if="form.errors.description" class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        {{ form.errors.description }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Description Arabic -->
                            <div class="col-12">
                                <label for="description_ar" class="form-label fw-semibold">
                                    {{ t('courses.fields.description') }} ({{ t('common.language_arabic') }})
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light align-items-start pt-3">
                                        <i class="bi bi-card-text text-success"></i>
                                    </span>
                                    <textarea
                                        id="description_ar"
                                        v-model="form.description_ar"
                                        rows="4"
                                        dir="rtl"
                                        class="form-control"
                                        :placeholder="t('courses.placeholders.description') || 'أدخل وصف الدورة'"
                                    ></textarea>
                                </div>
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
                            <!-- Track -->
                            <div class="col-12 col-md-6">
                                <label for="track_id" class="form-label fw-semibold">
                                    {{ t('courses.fields.track') || 'Track' }}
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-diagram-3 text-primary"></i>
                                    </span>
                                    <select
                                        id="track_id"
                                        v-model="form.track_id"
                                        class="form-select"
                                        :class="{ 'is-invalid': form.errors.track_id }"
                                    >
                                        <option :value="null">{{ t('common.select') || 'Select track' }}</option>
                                        <option v-for="track in tracks" :key="track.id" :value="track.id">
                                            {{ track.translated_name || track.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.track_id" class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        {{ form.errors.track_id }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Course Type -->
                            <div class="col-12 col-md-6">
                                <label for="course_type" class="form-label fw-semibold">
                                    {{ t('courses.fields.course_type') || 'Course Type' }} <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-book text-info"></i>
                                    </span>
                                    <select
                                        id="course_type"
                                        v-model="form.course_type"
                                        class="form-select"
                                        :class="{ 'is-invalid': form.errors.course_type }"
                                        required
                                    >
                                        <option value="course">{{ t('courses.types.course') || 'Course' }}</option>
                                        <option value="recurring">{{ t('courses.types.recurring') || 'Recurring' }}</option>
                                    </select>
                                    <div v-if="form.errors.course_type" class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        {{ form.errors.course_type }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Level -->
                            <div class="col-12 col-md-6">
                                <label for="level" class="form-label fw-semibold">
                                    {{ t('courses.fields.level') }} <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-bar-chart text-info"></i>
                                    </span>
                                    <select
                                        id="level"
                                        v-model="form.level"
                                        class="form-select"
                                        :class="{ 'is-invalid': form.errors.level }"
                                        required
                                    >
                                        <option value="">{{ t('common.select') || 'Select level' }}</option>
                                        <option value="beginner">{{ t('courses.levels.beginner') }}</option>
                                        <option value="intermediate">{{ t('courses.levels.intermediate') }}</option>
                                        <option value="advanced">{{ t('courses.levels.advanced') }}</option>
                                    </select>
                                    <div v-if="form.errors.level" class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        {{ form.errors.level }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Duration -->
                            <div class="col-12 col-md-6">
                                <label for="duration_hours" class="form-label fw-semibold">
                                    {{ t('courses.fields.duration') }} ({{ t('common.hours') }}) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-clock text-info"></i>
                                    </span>
                                    <input
                                        id="duration_hours"
                                        v-model="form.duration_hours"
                                        type="number"
                                        min="0"
                                        step="0.5"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.duration_hours }"
                                        placeholder="0"
                                        required
                                    />
                                    <span class="input-group-text bg-light">{{ t('common.hours') || 'Hours' }}</span>
                                    <div v-if="form.errors.duration_hours" class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        {{ form.errors.duration_hours }}
                                    </div>
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
                                <label class="form-label fw-semibold">{{ t('common.preview') || 'Preview' }}</label>
                                <div class="card border-2 border-dashed border-secondary-subtle">
                                    <div class="card-body p-3 text-center bg-light d-flex align-items-center justify-content-center" style="min-height: 200px;">
                                        <div v-if="thumbnailPreview" class="w-100 position-relative">
                                            <img :src="thumbnailPreview" class="img-fluid rounded shadow-sm" alt="Thumbnail Preview" />
                                            <button
                                                type="button"
                                                @click="removeThumbnail"
                                                class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 rounded-circle"
                                                style="width: 32px; height: 32px; padding: 0;"
                                                title="Remove image"
                                            >
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                        <div v-else class="text-muted">
                                            <i class="bi bi-image fs-1 d-block mb-2 opacity-50"></i>
                                            <small>{{ t('common.no_image') || 'No image selected' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Upload -->
                            <div class="col-12 col-md-8">
                                <label for="thumbnail" class="form-label fw-semibold">
                                    {{ t('common.choose_thumbnail') || 'Choose Thumbnail' }}
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-upload text-success"></i>
                                    </span>
                                    <input
                                        id="thumbnail"
                                        type="file"
                                        @change="handleThumbnail"
                                        accept="image/*"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.thumbnail }"
                                    />
                                    <div v-if="form.errors.thumbnail" class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        {{ form.errors.thumbnail }}
                                    </div>
                                </div>
                                <div class="form-text mt-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    {{ t('courses.thumbnail_hint') || 'Recommended: 1200x800px, max 2MB. Supported formats: JPG, PNG, GIF' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Publishing Section -->
                    <div class="mb-4 pt-5 border-top">
                        <div class="card bg-light border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-2 fw-semibold d-flex align-items-center gap-2">
                                            <i class="bi bi-globe text-primary"></i>
                                            {{ t('courses.publish_course') || 'Publish Course' }}
                                        </h5>
                                        <p class="text-muted small mb-0">
                                            <i class="bi bi-info-circle me-1"></i>
                                            {{ t('courses.publish_hint') || 'Make this course visible to students' }}
                                        </p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input 
                                            class="form-check-input" 
                                            type="checkbox" 
                                            id="publishSwitch"
                                            v-model="form.is_published"
                                            style="width: 3.5rem; height: 1.75rem; cursor: pointer;"
                                        />
                                        <label class="form-check-label fw-semibold ms-2" for="publishSwitch">
                                            <span v-if="form.is_published" class="text-success">{{ t('common.published') || 'Published' }}</span>
                                            <span v-else class="text-muted">{{ t('common.draft') || 'Draft' }}</span>
                                        </label>
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

const props = defineProps({
    tracks: {
        type: Array,
        default: () => [],
    },
    selectedTrackId: {
        type: Number,
        default: null,
    },
});

const { t } = useTranslation();
const { route } = useRoute();
const { showSuccess, showError } = useAlert();

const form = useForm({
    track_id: props.selectedTrackId || null,
    title: '',
    title_ar: '',
    description: '',
    description_ar: '',
    level: '',
    course_type: 'course',
    duration_hours: 0,
    thumbnail: null,
    is_published: false,
});

const thumbnailPreview = ref(null);

const handleThumbnail = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            showError('File size must be less than 2MB', t('common.error') || 'Error');
            event.target.value = '';
            return;
        }
        // Validate file type
        if (!file.type.match('image.*')) {
            showError('Please select a valid image file', t('common.error') || 'Error');
            event.target.value = '';
            return;
        }
        form.thumbnail = file;
        thumbnailPreview.value = URL.createObjectURL(file);
    }
};

const removeThumbnail = () => {
    form.thumbnail = null;
    thumbnailPreview.value = null;
    // Reset file input
    const fileInput = document.getElementById('thumbnail');
    if (fileInput) {
        fileInput.value = '';
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

