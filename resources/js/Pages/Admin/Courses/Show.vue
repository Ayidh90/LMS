<template>
    <AdminLayout :page-title="course?.translated_title || course?.title || t('admin.course_details') || 'Course Details'">
        <Head :title="course?.translated_title || course?.title || t('admin.course_details') || 'Course Details'" />
        
        <div class="admin-course-show-page container-fluid px-3 px-md-4 px-lg-5 py-4" :dir="direction">
            <!-- Page Header - Bootstrap Style -->
            <div class="card border-0 shadow-lg mb-4 header-gradient-card">
                <div class="card-body p-3 p-md-4 p-lg-5">
                    <!-- Breadcrumb Navigation -->
                    <nav aria-label="breadcrumb" class="breadcrumb-wrapper mb-3 mb-md-4">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <Link 
                                    :href="route('admin.courses.index')" 
                                    class="text-white text-decoration-none d-inline-flex align-items-center breadcrumb-link"
                                >
                                    <i 
                                        class="bi" 
                                        :class="[
                                            direction === 'rtl' ? 'bi-arrow-right' : 'bi-arrow-left',
                                            direction === 'rtl' ? 'ms-1' : 'me-1'
                                        ]"
                                    ></i>
                                    <span>{{ t('admin.courses_management') || 'Courses Management' }}</span>
                                </Link>
                            </li>
                            <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">
                                {{ course?.translated_title || course?.title }}
                            </li>
                        </ol>
                    </nav>
                    
                    <!-- Page Header -->
                    <div class="row align-items-start align-items-lg-center g-4">
                        <div class="col-12 col-lg-8 order-1 order-lg-2">
                            <div class="header-content">
                                <h1 class="header-title">
                                    <i 
                                        class="bi bi-book header-title-icon" 
                                        :class="direction === 'rtl' ? 'ms-3' : 'me-3'"
                                    ></i>
                                    <span class="header-title-text">{{ course?.translated_title || course?.title }}</span>
                                </h1>
                                <p class="header-subtitle">
                                    <i class="bi bi-info-circle me-2"></i>
                                    {{ t('admin.course_full_details') || 'View complete course information and statistics' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 order-2 order-lg-1">
                            <div class="header-action-wrapper d-flex justify-content-center justify-content-lg-start" :class="direction === 'rtl' ? 'flex-row-reverse' : ''">
                                <Link 
                                    v-if="courseIdentifier"
                                    :href="route('admin.courses.edit', courseIdentifier)" 
                                    class="edit-course-btn"
                                >
                                    <i 
                                        class="bi bi-pencil-fill edit-btn-icon" 
                                        :class="direction === 'rtl' ? 'ms-2' : 'me-2'"
                                    ></i>
                                    <span class="edit-btn-text">{{ t('admin.edit_course') || t('common.edit') || 'Edit Course' }}</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Message - Bootstrap Style -->
            <div v-if="page.props.flash?.success" class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ page.props.flash.success }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <!-- Statistics Cards - Bootstrap Style -->
            <div class="row g-3 g-md-4 mb-4">
                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                    <div class="card border-0 shadow-sm h-100 stat-card-hover">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-primary bg-gradient rounded p-3">
                                    <i class="bi bi-people fs-4 text-white"></i>
                                </div>
                            </div>
                            <h3 class="h2 fw-bold text-primary mb-1">{{ statistics?.total_students || 0 }}</h3>
                            <small class="text-muted fw-semibold d-block">{{ t('admin.total_students') || 'Total Students' }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                    <div class="card border-0 shadow-sm h-100 stat-card-hover">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-success bg-gradient rounded p-3">
                                    <i class="bi bi-check-circle fs-4 text-white"></i>
                                </div>
                            </div>
                            <h3 class="h2 fw-bold text-success mb-1">{{ statistics?.total_enrollments || 0 }}</h3>
                            <small class="text-muted fw-semibold d-block">{{ t('admin.total_enrollments') || 'Total Enrollments' }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                    <div class="card border-0 shadow-sm h-100 stat-card-hover">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-info bg-gradient rounded p-3">
                                    <i class="bi bi-book fs-4 text-white"></i>
                                </div>
                            </div>
                            <h3 class="h2 fw-bold text-info mb-1">{{ statistics?.total_lessons || 0 }}</h3>
                            <small class="text-muted fw-semibold d-block">{{ t('admin.total_lessons') || 'Total Lessons' }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                    <div class="card border-0 shadow-sm h-100 stat-card-hover">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-warning bg-gradient rounded p-3">
                                    <i class="bi bi-collection fs-4 text-white"></i>
                                </div>
                            </div>
                            <h3 class="h2 fw-bold text-warning mb-1">{{ statistics?.total_batches || 0 }}</h3>
                            <small class="text-muted fw-semibold d-block">{{ t('admin.total_batches') || 'Total Batches' }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                    <div class="card border-0 shadow-sm h-100 stat-card-hover">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-danger bg-gradient rounded p-3">
                                    <i class="bi bi-question-circle fs-4 text-white"></i>
                                </div>
                            </div>
                            <h3 class="h2 fw-bold text-danger mb-1">{{ statistics?.total_questions || 0 }}</h3>
                            <small class="text-muted fw-semibold d-block">{{ t('admin.total_questions') || 'Total Questions' }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Main Content -->
                <div class="col-12 col-lg-8">
                    <!-- Course Image -->
                    <div class="card shadow-sm mb-4 course-image-card">
                        <div class="card-body p-0">
                            <img 
                                :src="course?.thumbnail_url || course?.thumbnail || '/images/default-course.avif'" 
                                :alt="course?.translated_title || course?.title"
                                class="img-fluid w-100 course-thumbnail-image"
                                @error="handleImageError($event)"
                            />
                        </div>
                    </div>

                    <!-- Course Details - Bootstrap Style -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h3 class="h5 fw-bold mb-0">{{ t('admin.course_details') || 'Course Details' }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-semibold">{{ t('courses.fields.title') || 'Title' }} ({{ t('common.language_english') || 'English' }})</label>
                                    <p class="mb-0 fw-semibold">{{ course?.title || '-' }}</p>
                                </div>

                                <div v-if="course?.title_ar" class="col-12">
                                    <label class="form-label text-muted small fw-semibold">{{ t('courses.fields.title') || 'Title' }} ({{ t('common.language_arabic') || 'Arabic' }})</label>
                                    <p class="mb-0 fw-semibold" dir="rtl">{{ course?.title_ar }}</p>
                                </div>
                                
                                <div v-if="course?.translated_title && course?.translated_title !== course?.title" class="col-12">
                                    <label class="form-label text-muted small fw-semibold">{{ t('courses.fields.title') || 'Title' }} ({{ t('common.current_language') || 'Current Language' }})</label>
                                    <p class="mb-0 fw-semibold" :dir="direction">{{ course?.translated_title }}</p>
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-muted small fw-semibold">{{ t('courses.fields.description') || 'Description' }} ({{ t('common.language_english') || 'English' }})</label>
                                    <p class="mb-0 text-muted">{{ course?.description || '-' }}</p>
                                </div>

                                <div v-if="course?.description_ar" class="col-12">
                                    <label class="form-label text-muted small fw-semibold">{{ t('courses.fields.description') || 'Description' }} ({{ t('common.language_arabic') || 'Arabic' }})</label>
                                    <p class="mb-0 text-muted" dir="rtl">{{ course?.description_ar }}</p>
                                </div>
                                
                                <div v-if="course?.translated_description && course?.translated_description !== course?.description" class="col-12">
                                    <label class="form-label text-muted small fw-semibold">{{ t('courses.fields.description') || 'Description' }} ({{ t('common.current_language') || 'Current Language' }})</label>
                                    <p class="mb-0 text-muted" :dir="direction">{{ course?.translated_description }}</p>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label text-muted small fw-semibold">{{ t('courses.fields.level') || 'Level' }}</label>
                                    <p class="mb-0">
                                        <span class="badge bg-info">{{ t(`courses.levels.${course?.level}`) || course?.level }}</span>
                                    </p>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label text-muted small fw-semibold">{{ t('courses.fields.duration_hours') || 'Duration' }}</label>
                                    <p class="mb-0 fw-semibold">{{ course?.duration_hours || 0 }} {{ t('common.hours') || 'hours' }}</p>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label text-muted small fw-semibold">{{ t('courses.status.title') || 'Status' }}</label>
                                    <p class="mb-0">
                                        <span :class="course?.is_published ? 'badge bg-success' : 'badge bg-warning'">
                                            {{ course?.is_published ? t('courses.status.published') : t('courses.status.draft') }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sections & Lessons - Bootstrap Style -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h3 class="h5 fw-bold mb-1 d-flex align-items-center gap-2">
                                    <i class="bi bi-book text-primary"></i>
                                    {{ t('admin.course_content') || 'Course Content' }}
                                </h3>
                                <p class="text-muted small mb-0">{{ t('admin.course_content_description') || 'Manage sections, lessons, and questions' }}</p>
                            </div>
                            <Link v-if="courseIdentifier" :href="route('admin.courses.sections.index', courseIdentifier)" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-gear me-1"></i>
                                {{ t('admin.manage_sections') || 'Manage Chapters' }}
                            </Link>
                        </div>
                        <div class="card-body">
                            <div v-if="course?.sections && course.sections.length > 0">
                                <div class="accordion" id="sectionsAccordion">
                                    <div v-for="(section, index) in course.sections" :key="section.id" class="accordion-item mb-3">
                                        <h2 class="accordion-header" :id="`heading-${section.id}`">
                                            <button 
                                                class="accordion-button" 
                                                :class="{ collapsed: index !== 0 }"
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                :data-bs-target="`#section-${section.id}`" 
                                                :aria-expanded="index === 0 ? 'true' : 'false'"
                                                :aria-controls="`section-${section.id}`"
                                            >
                                                <span class="badge bg-info me-2">{{ section.lessons?.length || 0 }}</span>
                                                {{ section.translated_title || section.title }}
                                            </button>
                                        </h2>
                                        <div 
                                            :id="`section-${section.id}`" 
                                            class="accordion-collapse collapse" 
                                            :class="{ show: index === 0 }" 
                                            :aria-labelledby="`heading-${section.id}`"
                                            data-bs-parent="#sectionsAccordion"
                                        >
                                            <div class="accordion-body">
                                                <p v-if="section.translated_description || section.description" class="text-muted mb-3">{{ section.translated_description || section.description }}</p>
                                                <div v-if="section.lessons && section.lessons.length > 0" class="list-group list-group-flush">
                                                    <div v-for="lesson in section.lessons" :key="lesson.id" class="list-group-item border rounded mb-2">
                                                        <div class="d-flex align-items-center gap-3 flex-wrap">
                                                            <div class="bg-primary bg-gradient rounded p-2 flex-shrink-0">
                                                                <i class="bi bi-play-circle text-white"></i>
                                                            </div>
                                                            <div class="flex-grow-1 min-w-0">
                                                                <strong class="d-block">{{ lesson.translated_title || lesson.title }}</strong>
                                                                <small v-if="lesson.translated_description || lesson.description" class="text-muted d-block mt-1">
                                                                    {{ (lesson.translated_description || lesson.description).substring(0, 100) }}{{ (lesson.translated_description || lesson.description).length > 100 ? '...' : '' }}
                                                                </small>
                                                            </div>
                                                            <div class="d-flex gap-2 align-items-center flex-wrap">
                                                                <Link v-if="lesson.id && courseIdentifier" :href="route('admin.courses.lessons.questions.create', [courseIdentifier, lesson.id])" class="btn btn-sm btn-success" :title="t('admin.add_question') || 'Add Question'">
                                                                    <i class="bi bi-plus-circle me-1"></i>
                                                                    <span class="d-none d-sm-inline">{{ t('admin.add_question') || 'Add Question' }}</span>
                                                                </Link>
                                                                <Link v-if="lesson.id && lesson.questions && lesson.questions.length > 0 && courseIdentifier" :href="route('admin.courses.lessons.questions.index', [courseIdentifier, lesson.id])" class="btn btn-sm btn-info" :title="t('admin.view_questions') || 'View Questions'">
                                                                    <i class="bi bi-eye me-1"></i>
                                                                    <span class="d-none d-sm-inline">{{ t('admin.view_questions') || 'View Questions' }}</span>
                                                                </Link>
                                                                <span v-if="lesson.questions && lesson.questions.length > 0" class="badge bg-info align-self-center">
                                                                    <i class="bi bi-question-circle me-1"></i>
                                                                    {{ lesson.questions.length }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div v-if="lesson.questions && lesson.questions.length > 0" class="mt-3 pt-3 border-top">
                                                            <small class="text-muted fw-semibold d-block mb-2">
                                                                <i class="bi bi-list-ul me-1"></i>
                                                                {{ t('lessons.questions') || 'Questions' }}:
                                                            </small>
                                                            <div v-for="question in lesson.questions" :key="question.id" class="d-flex align-items-start gap-2 mb-2">
                                                                <i class="bi bi-circle-fill text-primary flex-shrink-0 mt-1" style="font-size: 0.5rem;"></i>
                                                                <small class="text-muted flex-grow-1">{{ question.translated_question || question.question }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div v-else class="text-center py-3 text-muted">
                                                    <i class="bi bi-book me-2"></i>
                                                    {{ t('lessons.no_lessons') || 'No lessons in this section' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                <p class="text-muted mb-3">{{ t('admin.no_sections') || 'No sections added yet' }}</p>
                                <Link v-if="courseIdentifier" :href="route('admin.courses.sections.create', courseIdentifier)" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    {{ t('admin.add_section') || 'Add Section' }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-12 col-lg-4">
                    <!-- Quick Actions - Bootstrap Style -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h3 class="h6 fw-bold mb-1 d-flex align-items-center gap-2">
                                <i class="bi bi-lightning-charge text-primary"></i>
                                {{ t('admin.quick_actions') || 'Quick Actions' }}
                            </h3>
                            <p class="text-muted small mb-0">{{ t('admin.manage_course_content_description') || 'Manage course content and structure' }}</p>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <Link v-if="courseIdentifier" :href="route('admin.courses.sections.create', courseIdentifier)" class="btn btn-outline-info">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    {{ t('admin.add_section') || 'Add Section' }}
                                </Link>
                                <Link v-if="courseIdentifier" :href="route('admin.courses.sections.index', courseIdentifier)" class="btn btn-outline-warning">
                                    <i class="bi bi-list me-2"></i>
                                    {{ t('admin.manage_sections') || 'Manage Chapters' }}
                                </Link>
                                <Link v-if="courseIdentifier" :href="route('admin.courses.lessons.create', courseIdentifier)" class="btn btn-outline-secondary">
                                    <i class="bi bi-book me-2"></i>
                                    {{ t('admin.add_lesson') || 'Add Lesson' }}
                                </Link>
                                <Link v-if="courseIdentifier && course?.sections && course.sections.length > 0 && course.sections.some(s => s.lessons && s.lessons.length > 0)" :href="route('admin.courses.lessons.index', courseIdentifier)" class="btn btn-outline-warning">
                                    <i class="bi bi-list me-2"></i>
                                    {{ t('admin.manage_lessons') || 'Manage Lessons' }}
                                </Link>
                                <Link v-if="courseIdentifier" :href="route('admin.courses.batches.index', courseIdentifier)" class="btn btn-outline-primary">
                                    <i class="bi bi-people me-2"></i>
                                    {{ t('admin.manage_batches') || 'Manage Batches' }}
                                </Link>
                                <Link v-if="courseIdentifier" :href="route('admin.courses.batches.create', courseIdentifier)" class="btn btn-outline-success">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    {{ t('admin.create_batch') || 'Create Batch' }}
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Batches - Bootstrap Style -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h3 class="h6 fw-bold mb-1 d-flex align-items-center gap-2">
                                    <i class="bi bi-people text-success"></i>
                                    {{ t('admin.batches') || 'Batches' }}
                                </h3>
                                <p class="text-muted small mb-0">{{ t('admin.batches_description') || 'Manage course batches and student enrollments' }}</p>
                            </div>
                            <div class="d-flex gap-2">
                                <Link v-if="courseIdentifier" :href="route('admin.courses.batches.index', courseIdentifier)" class="btn btn-sm btn-outline-primary" :title="t('admin.manage_batches') || 'Manage Batches'">
                                    <i class="bi bi-list me-1"></i>
                                    <span class="d-none d-sm-inline">{{ t('admin.manage_batches') || 'Manage' }}</span>
                                </Link>
                                <Link v-if="courseIdentifier" :href="route('admin.courses.batches.create', courseIdentifier)" class="btn btn-sm btn-success" :title="t('admin.create_batch') || 'Create Batch'">
                                    <i class="bi bi-plus me-1"></i>
                                    <span class="d-none d-sm-inline">{{ t('admin.create_batch') || 'Create' }}</span>
                            </Link>
                            </div>
                        </div>
                        <div class="card-body">
                            <div v-if="courseIdentifier && course?.batches && course.batches.length > 0" class="list-group list-group-flush">
                                <Link 
                                    v-for="batch in course.batches" 
                                    :key="batch.id" 
                                    :href="route('admin.courses.batches.show', [courseIdentifier, batch.id])"
                                    class="list-group-item list-group-item-action border rounded mb-2"
                                >
                                    <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                                        <div class="flex-grow-1">
                                            <strong class="d-block mb-1">{{ batch.name }}</strong>
                                    <div class="text-muted small">
                                        <i class="bi bi-person me-1"></i>
                                        {{ t('admin.instructor') || 'Instructor' }}: <strong>{{ batch.instructor?.name || '-' }}</strong>
                                    </div>
                                        </div>
                                        <span :class="batch.is_active ? 'badge bg-success' : 'badge bg-secondary'">
                                            <i :class="batch.is_active ? 'bi bi-check-circle' : 'bi bi-x-circle'" class="me-1"></i>
                                            {{ batch.is_active ? (t('admin.active') || 'Active') : (t('admin.inactive') || 'Inactive') }}
                                        </span>
                                    </div>
                                    <div v-if="batch.enrollments_count !== undefined" class="text-muted small mt-2 d-flex align-items-center gap-2">
                                        <span>
                                        <i class="bi bi-people me-1"></i>
                                        {{ batch.enrollments_count }} {{ t('admin.students') || 'students' }}
                                        </span>
                                        <span v-if="batch.start_date" class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            {{ new Date(batch.start_date).toLocaleDateString() }}
                                        </span>
                                    </div>
                                </Link>
                            </div>
                            <div v-else class="text-center py-5">
                                <i class="bi bi-people fs-1 text-muted d-block mb-3"></i>
                                <p class="text-muted mb-4">{{ t('admin.no_batches') || 'No batches yet' }}</p>
                                <Link v-if="courseIdentifier" :href="route('admin.courses.batches.create', courseIdentifier)" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    {{ t('admin.create_batch') || 'Create Batch' }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { computed, onMounted, nextTick } from 'vue';

const props = defineProps({
    course: Object,
    statistics: Object,
});

const { t } = useTranslation();
const { route } = useRoute();
const page = usePage();

const direction = computed(() => page.props.direction || 'ltr');
const courseIdentifier = computed(() => props.course?.slug || props.course?.id || null);

const handleImageError = (event) => {
    event.target.src = '/images/default-course.avif';
};

// Initialize Bootstrap accordion and other components
onMounted(() => {
    nextTick(() => {
        // Initialize Bootstrap accordion
        if (window.bootstrap && window.bootstrap.Collapse) {
            // Get all accordion buttons
            const accordionButtons = document.querySelectorAll('.accordion-button[data-bs-toggle="collapse"]');
            
            accordionButtons.forEach((button) => {
                const targetSelector = button.getAttribute('data-bs-target');
                if (targetSelector) {
                    const targetElement = document.querySelector(targetSelector);
                    if (targetElement) {
                        // Initialize collapse instance
                        const collapseInstance = new window.bootstrap.Collapse(targetElement, {
                            toggle: false
                        });
                        
                        // Handle button click
                        button.addEventListener('click', (e) => {
                            e.preventDefault();
                            collapseInstance.toggle();
                        });
                    }
                }
            });
        }

        // Initialize Bootstrap alerts dismiss functionality
        if (window.bootstrap && window.bootstrap.Alert) {
            const alertElements = document.querySelectorAll('.alert.alert-dismissible');
            alertElements.forEach((alert) => {
                const closeButtons = alert.querySelectorAll('.btn-close[data-bs-dismiss="alert"]');
                closeButtons.forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const bsAlert = new window.bootstrap.Alert(alert);
                        bsAlert.close();
                    });
                });
            });
        }
    });
});
</script>

<style scoped>
/* Color Variables */
:root {
    --primary-color: #0d6efd;
    --primary-dark: #0b5ed7;
    --primary-darker: #0a58ca;
    --primary-light: #3d8bfd;
    --success-color: #198754;
    --success-light: #20c997;
    --info-color: #0dcaf0;
    --info-dark: #0aa2c0;
    --warning-color: #ffc107;
    --warning-dark: #ffb300;
    --danger-color: #dc3545;
    --danger-light: #e4606d;
    --white: #ffffff;
    --gray-50: #f8f9fa;
    --gray-100: #f1f3f5;
    --gray-200: #e9ecef;
    --gray-300: #dee2e6;
    --gray-400: #ced4da;
    --gray-500: #adb5bd;
    --gray-600: #6c757d;
    --gray-700: #495057;
    --gray-800: #343a40;
    --gray-900: #212529;
    --text-primary: #212529;
    --text-secondary: #6c757d;
    --text-muted: #6c757d;
    --border-color: #e9ecef;
    --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    --shadow-md: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    --shadow-xl: 0 1rem 2rem rgba(0, 0, 0, 0.2);
}

/* Page Container */
.admin-course-show-page {
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
    padding: 0;
    background: transparent;
    min-height: calc(100vh - 80px);
    position: relative;
    border-radius: 0;
    overflow-x: hidden;
}

.admin-course-show-page .container-fluid {
    max-width: 100%;
    padding-left: 1rem;
    padding-right: 1rem;
}

@media (min-width: 576px) {
    .admin-course-show-page .container-fluid {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
}

@media (min-width: 768px) {
    .admin-course-show-page .container-fluid {
        padding-left: 2rem;
        padding-right: 2rem;
    }
}

@media (min-width: 992px) {
    .admin-course-show-page .container-fluid {
        padding-left: 3rem;
        padding-right: 3rem;
    }
}

.admin-course-show-page::before {
    display: none;
}

.admin-course-show-page > * {
    position: relative;
    z-index: 1;
}

/* Header Gradient Card */
.header-gradient-card {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 30%, #0a58ca 65%, #084298 100%);
    background-image: 
        linear-gradient(135deg, #0d6efd 0%, #0b5ed7 30%, #0a58ca 65%, #084298 100%),
        radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.2) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.15) 0%, transparent 50%);
    position: relative;
    overflow: hidden;
    border-radius: 1.25rem;
    box-shadow: 
        0 0.75rem 2rem rgba(13, 110, 253, 0.45), 
        0 0.375rem 0.75rem rgba(13, 110, 253, 0.3), 
        inset 0 1px 0 rgba(255, 255, 255, 0.15),
        inset 0 -1px 0 rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    border: none;
}

.breadcrumb-wrapper {
    background-color: rgba(255, 255, 255, 0.15);
    border-radius: 0.75rem;
    padding: 0.75rem 1.25rem;
    border: 1px solid rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(8px);
}

.header-gradient-card::before {
    content: '';
    position: absolute;
    top: -40%;
    right: -5%;
    width: 700px;
    height: 700px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0.15) 30%, rgba(255, 255, 255, 0.05) 60%, transparent 80%);
    border-radius: 50%;
    transform: translate(0, 0);
    filter: blur(140px);
    pointer-events: none;
    opacity: 1;
    animation: pulse-glow 4s ease-in-out infinite;
}

.header-gradient-card::after {
    content: '';
    position: absolute;
    bottom: -25%;
    left: -5%;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 40%, rgba(255, 255, 255, 0.03) 70%, transparent 90%);
    border-radius: 50%;
    transform: translate(0, 0);
    filter: blur(120px);
    pointer-events: none;
    opacity: 0.9;
    animation: pulse-glow 5s ease-in-out infinite reverse;
}

.header-gradient-card .card-body {
    background: transparent !important;
    position: relative;
    z-index: 2;
}

@keyframes pulse-glow {
    0%, 100% {
        opacity: 0.85;
        transform: scale(1);
    }
    50% {
        opacity: 1;
        transform: scale(1.15);
    }
}

[dir="rtl"] .header-gradient-card::before {
    right: auto;
    left: 0;
    transform: translate(-30%, -30%);
}

.header-gradient-card .card-body {
    position: relative;
    z-index: 1;
}

/* Header Content */
.header-content {
    max-width: 100%;
    padding-right: 0;
}

[dir="rtl"] .header-content {
    padding-left: 0;
}

.header-title {
    font-size: clamp(1.5rem, 4vw, 2.75rem);
    font-weight: 800;
    color: var(--white);
    line-height: 1.2;
    margin-bottom: 1.25rem;
    margin-top: 0;
    display: flex;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 1rem;
    position: relative;
    z-index: 2;
}

.header-title-icon {
    font-size: clamp(1.75rem, 3vw, 2.5rem);
    color: var(--white);
    flex-shrink: 0;
    margin-top: 0.125rem;
    filter: drop-shadow(0 3px 8px rgba(0, 0, 0, 0.2));
    opacity: 1;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
}

.header-title-text {
    flex: 1;
    min-width: 0;
    word-wrap: break-word;
    word-break: break-word;
    overflow-wrap: break-word;
    line-height: 1.3;
    text-shadow: 0 3px 8px rgba(0, 0, 0, 0.2), 0 1px 3px rgba(0, 0, 0, 0.15);
    color: var(--white);
    font-weight: 800;
}

.header-subtitle {
    color: rgba(255, 255, 255, 0.98);
    font-size: clamp(0.9375rem, 2vw, 1.1875rem);
    line-height: 1.7;
    margin-bottom: 0;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.625rem 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
    position: relative;
    z-index: 2;
}

.header-subtitle i {
    font-size: 1.125rem;
    opacity: 1;
    flex-shrink: 0;
    color: rgba(255, 255, 255, 0.95);
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
}

/* Header Action Wrapper */
.header-action-wrapper {
    align-items: center;
    height: 100%;
    min-height: 70px;
    width: 100%;
    padding-bottom: 0.5rem;
    justify-content: flex-start;
    gap: 0.75rem;
}

@media (min-width: 992px) {
    .header-action-wrapper {
        width: auto;
        min-height: 80px;
        padding-bottom: 0;
    }
}

@media (max-width: 991px) {
    .header-action-wrapper {
        justify-content: center !important;
        margin-top: 0;
        margin-bottom: 1rem;
        min-height: auto;
        padding-bottom: 0;
    }
}

/* Breadcrumb Styling */
.header-gradient-card .breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 0;
    flex-wrap: wrap;
}

.header-gradient-card .breadcrumb-item {
    color: rgba(255, 255, 255, 0.88);
    font-size: 0.9375rem;
}

.header-gradient-card .breadcrumb-item.active {
    color: var(--white);
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.header-gradient-card .breadcrumb-link {
    color: rgba(255, 255, 255, 0.92);
    text-decoration: none;
    transition: all 0.2s ease;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    margin: -0.25rem -0.5rem;
}

.header-gradient-card .breadcrumb-link:hover {
    color: var(--white);
    background: rgba(255, 255, 255, 0.15);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.header-gradient-card .breadcrumb-link i {
    font-size: 0.875rem;
    transition: transform 0.2s ease;
}

.header-gradient-card .breadcrumb-link:hover i {
    transform: translateX(-2px);
}

[dir="rtl"] .header-gradient-card .breadcrumb-link:hover i {
    transform: translateX(2px);
}

.header-gradient-card .breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    padding: 0 0.5rem;
    color: rgba(255, 255, 255, 0.6);
    font-size: 1.25rem;
    font-weight: 300;
    line-height: 1;
}

[dir="rtl"] .header-gradient-card .breadcrumb-item + .breadcrumb-item::before {
    content: "‹";
}

/* Edit Course Button */
.edit-course-btn {
    background: var(--white) !important;
    border: 2.5px solid rgba(255, 255, 255, 0.8) !important;
    color: var(--primary-color) !important;
    font-weight: 700;
    font-size: 1.0625rem;
    padding: 1.125rem 2.5rem;
    border-radius: 1rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 0.5rem 1.25rem rgba(0, 0, 0, 0.2), 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.9);
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
    min-width: 200px;
    position: relative;
    overflow: hidden;
    letter-spacing: 0.03em;
    backdrop-filter: blur(12px);
    text-shadow: none;
}

.edit-course-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(13, 110, 253, 0.15), transparent);
    transition: left 0.7s ease;
    z-index: 0;
}

.edit-course-btn:hover::before {
    left: 100%;
}

.edit-course-btn::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(13, 110, 253, 0.1);
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
    z-index: 0;
}

.edit-course-btn:hover::after {
    width: 300px;
    height: 300px;
}

.edit-course-btn:hover {
    background: var(--white) !important;
    color: var(--primary-dark) !important;
    border-color: rgba(255, 255, 255, 1) !important;
    transform: translateY(-4px) scale(1.03);
    box-shadow: 0 0.75rem 2rem rgba(0, 0, 0, 0.3), 0 0.5rem 1rem rgba(0, 0, 0, 0.2), 0 0 0 3px rgba(255, 255, 255, 0.3), inset 0 1px 0 rgba(255, 255, 255, 1);
}

.edit-course-btn:active {
    transform: translateY(-2px) scale(1.01);
    box-shadow: 0 0.375rem 1rem rgba(0, 0, 0, 0.2);
}

.edit-course-btn:focus {
    outline: none;
    box-shadow: 0 0.5rem 1.25rem rgba(0, 0, 0, 0.2), 0 0 0 5px rgba(255, 255, 255, 0.7);
    border-color: rgba(255, 255, 255, 1) !important;
}

.edit-btn-icon {
    font-size: 1.25rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    font-weight: 700;
    position: relative;
    z-index: 1;
    color: var(--primary-color);
}

.edit-course-btn:hover .edit-btn-icon {
    transform: rotate(-20deg) scale(1.2);
    color: var(--primary-dark);
}

.edit-btn-text {
    font-weight: 700;
    letter-spacing: 0.03em;
    line-height: 1.5;
    font-size: 1.0625rem;
    position: relative;
    z-index: 1;
}

/* Responsive Button Width */
.edit-course-btn {
    width: 100%;
}

@media (min-width: 992px) {
    .edit-course-btn {
        width: auto;
        min-width: 200px;
    }
}

/* Responsive Adjustments for Header */
@media (max-width: 991px) {
    .header-gradient-card .card-body {
        padding: 1.5rem !important;
    }
    
    .header-content {
        padding-right: 0;
        padding-left: 0;
    }
    
    .header-title {
        font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

    .header-title-icon {
        font-size: 1.5rem;
    }
    
    .header-subtitle {
    font-size: 0.9375rem;
}

    .edit-course-btn {
        width: 100%;
        justify-content: center;
        min-width: auto;
        padding: 0.875rem 1.75rem;
        font-size: 0.9375rem;
    }
    
    .header-action-wrapper {
        margin-bottom: 1.5rem;
    }
}

@media (max-width: 767px) {
    .header-gradient-card {
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
    }
    
    .header-gradient-card .card-body {
        padding: 1.25rem !important;
    }
    
    .header-title {
        font-size: 1.375rem;
        margin-bottom: 0.75rem;
        gap: 0.5rem;
    }
    
    .header-title-icon {
        font-size: 1.25rem;
        margin-top: 0.125rem;
    }
    
    .header-subtitle {
        font-size: 0.875rem;
        line-height: 1.5;
        padding: 0.375rem 0;
    }
    
    .header-subtitle i {
        font-size: 0.875rem;
    }
    
    .header-content {
        padding-right: 0;
        padding-left: 0;
    }
    
    .edit-course-btn {
        padding: 0.75rem 1.5rem;
        font-size: 0.9375rem;
        min-width: auto;
    }
    
    .edit-btn-icon {
        font-size: 1rem;
    }
    
    .header-gradient-card .breadcrumb-item {
        font-size: 0.875rem;
    }
    
    .edit-course-btn {
    padding: 0.625rem 1.25rem;
        font-size: 0.9375rem;
        min-width: auto;
    }
    
    .edit-course-btn i {
        font-size: 1rem;
    }
}

@media (max-width: 575px) {
    .header-gradient-card .card-body {
        padding: 1rem !important;
    }
    
    .header-title {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }
    
    .header-title-icon {
        font-size: 1.125rem;
    }
    
    .header-subtitle {
        font-size: 0.8125rem;
        line-height: 1.5;
    }
    
    .header-content {
        padding-right: 0;
        padding-left: 0;
    }

    .header-gradient-card .breadcrumb-item {
        font-size: 0.8125rem;
    }
    
    .header-gradient-card .breadcrumb-item + .breadcrumb-item::before {
        font-size: 1rem;
        padding: 0 0.375rem;
    }
    
    .edit-course-btn {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        min-width: auto;
        border-width: 1.5px !important;
    }
    
    .edit-course-btn i {
        font-size: 0.9375rem;
    }
    
    .edit-course-btn span {
        font-size: 0.875rem;
    }
}

/* Alert Styling */
.alert {
    border-radius: 0.5rem;
    border: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-bottom: 1.5rem;
}

/* Statistics Cards */
.stat-card-hover {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 0.75rem;
    border: 1px solid var(--border-color);
    overflow: hidden;
    background: var(--white);
    position: relative;
    cursor: default;
}

.stat-card-hover::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, rgba(13, 110, 253, 0.3), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stat-card-hover:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-lg) !important;
    border-color: var(--primary-color);
}

.stat-card-hover:hover::before {
    opacity: 1;
}

.stat-card-hover .card-body {
    padding: 1.25rem;
}

.stat-card-hover .bg-gradient {
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 56px;
    height: 56px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

.stat-card-hover:hover .bg-gradient {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.stat-card-hover h3 {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    line-height: 1.2;
    transition: all 0.3s ease;
}

.stat-card-hover:hover h3 {
    transform: scale(1.05);
}

.stat-card-hover small {
    font-size: 0.8125rem;
    color: var(--text-muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    line-height: 1.4;
}

/* Card Styling */
.card {
    border-radius: 0.75rem;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
    margin-bottom: 1.5rem;
    overflow: hidden;
    background: var(--white);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: var(--shadow-md);
    border-color: var(--gray-300);
}

.card-header {
    background: linear-gradient(to bottom, var(--white) 0%, var(--gray-50) 100%);
    border-bottom: 1px solid var(--border-color);
    padding: 1.5rem 1.75rem;
    font-weight: 600;
    position: relative;
}

.card-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--primary-color), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.card:hover .card-header::after {
    opacity: 0.4;
}

.card-header.bg-light {
    background: linear-gradient(to bottom, var(--gray-50) 0%, var(--white) 100%) !important;
}

.card-header h3,
.card-header h5,
.card-header h6 {
    margin: 0;
    font-weight: 600;
    color: var(--text-primary);
}

.card-header p {
    margin: 0.25rem 0 0 0;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.card-body {
    padding: 1.75rem;
    background: var(--white);
}

/* Course Image */
.course-image-card {
    overflow: hidden;
    border-radius: 0.75rem;
    position: relative;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.course-image-card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.course-thumbnail-image {
    width: 100%;
    height: auto;
    max-height: 400px;
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
}

.course-image-card:hover .course-thumbnail-image {
    transform: scale(1.02);
}

@media (max-width: 768px) {
    .course-thumbnail-image {
        max-height: 300px;
    }
}

@media (max-width: 576px) {
    .course-thumbnail-image {
        max-height: 250px;
    }
}

/* Form Labels */
.form-label {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.card-body p {
    font-size: 0.9375rem;
    line-height: 1.6;
    color: var(--gray-700);
}

.card-body p.fw-semibold {
    font-weight: 600;
    color: var(--text-primary);
}

/* Badge Styling */
.badge {
    padding: 0.5rem 0.75rem;
    font-size: 0.8125rem;
    font-weight: 500;
    border-radius: 0.375rem;
    box-shadow: var(--shadow-sm);
    transition: all 0.2s ease;
}

.badge:hover {
    transform: scale(1.05);
    box-shadow: var(--shadow-md);
}

.badge.bg-info {
    background-color: var(--info-color) !important;
    color: var(--white);
}

.badge.bg-success {
    background-color: var(--success-color) !important;
    color: var(--white);
}

.badge.bg-warning {
    background-color: var(--warning-color) !important;
    color: var(--gray-900);
}

.badge.bg-danger {
    background-color: var(--danger-color) !important;
    color: var(--white);
}

.badge.bg-primary {
    background-color: var(--primary-color) !important;
    color: var(--white);
}

.badge.bg-secondary {
    background-color: var(--gray-600) !important;
    color: var(--white);
}

/* Accordion Enhancements */
.accordion {
    --bs-accordion-border-radius: 0.5rem;
    --bs-accordion-border-color: #e9ecef;
}

.accordion-item {
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    margin-bottom: 0.75rem;
    overflow: visible;
    background: #ffffff;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    position: relative;
}
.accordion-collapse,
.accordion-body {
    overflow: visible;
}


.accordion-item:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-color: #0d6efd;
}

.accordion-item:last-child {
    margin-bottom: 0;
}

.accordion-button {
    font-weight: 600;
    font-size: 0.9375rem;
    padding: 1rem 1.25rem;
    background: #ffffff;
    color: #212529;
    border: none;
    transition: all 0.2s ease;
}

.accordion-button:not(.collapsed) {
    background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
    color: #0d6efd;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.1);
    font-weight: 700;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: transparent;
}

.accordion-button .badge {
    margin-right: 0.75rem;
    font-size: 0.75rem;
}

[dir="rtl"] .accordion-button .badge {
    margin-right: 0;
    margin-left: 0.75rem;
}

.accordion-body {
    padding: 1.25rem;
    background: var(--white);
}

/* List Group Enhancements */
.list-group {
    border-radius: 0.5rem;
}

.list-group-item {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid var(--border-color);
    padding: 1.25rem;
    background: var(--white);
    position: relative;
}

.list-group-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, var(--primary-color), var(--primary-dark));
    transform: scaleY(0);
    transform-origin: bottom;
    transition: transform 0.3s ease;
}

.list-group-item:hover {
    transform: none;
    border-color: var(--primary-color);
    background: linear-gradient(to right, var(--gray-50) 0%, var(--white) 100%);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.18);
    padding-left: 1.5rem;
}

[dir="rtl"] .list-group-item:hover {
    padding-left: 1.25rem;
    padding-right: 1.5rem;
}

.list-group-item:hover::before {
    transform: scaleY(1);
}

[dir="rtl"] .list-group-item::before {
    left: auto;
    right: 0;
}

.list-group-item-action {
    cursor: pointer;
    text-decoration: none;
    color: inherit;
}

.list-group-item-action:hover {
    color: inherit;
}

.list-group-item strong {
    font-size: 0.9375rem;
    color: var(--text-primary);
    display: block;
    margin-bottom: 0.5rem;
}

.list-group-item .text-muted {
    font-size: 0.8125rem;
    color: var(--text-muted);
}

.list-group-item .badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.625rem;
}

/* Lesson Items */
.list-group-item .bg-primary {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.375rem;
}

.list-group-item .flex-grow-1 strong {
    font-size: 0.9375rem;
    color: var(--text-primary);
    margin: 0;
}

.list-group-item .d-flex.gap-2 {
    gap: 0.5rem;
}

.list-group-item .btn-sm {
    padding: 0.375rem 0.625rem;
    font-size: 0.8125rem;
    border-radius: 0.375rem;
    border: 1px solid;
    transition: all 0.2s ease;
}

.list-group-item .btn-sm:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

/* Question Items */
.list-group-item .border-top {
    border-top: 1px solid var(--border-color) !important;
    padding-top: 0.75rem;
    margin-top: 0.75rem;
}

.list-group-item .bi-circle-fill {
    flex-shrink: 0;
    margin-top: 0.375rem;
}

.list-group-item small {
    font-size: 0.8125rem;
    line-height: 1.5;
}

/* Quick Actions */
.d-grid.gap-2 {
    gap: 0.75rem;
}

.btn {
    border-radius: 0.5rem;
    font-weight: 500;
    padding: 0.625rem 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-width: 1px;
    font-size: 0.875rem;
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn:hover::before {
    width: 300px;
    height: 300px;
}

.btn-outline-info,
.btn-outline-warning,
.btn-outline-secondary,
.btn-outline-primary,
.btn-outline-success {
    transition: all 0.2s ease;
}

.btn-outline-info:hover,
.btn-outline-warning:hover,
.btn-outline-secondary:hover,
.btn-outline-primary:hover,
.btn-outline-success:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    border-width: 2px;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
    border-radius: 0.375rem;
}

/* Empty States */
.text-center.py-5 {
    padding: 3rem 2rem;
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
    border-radius: 0.75rem;
    border: 2px dashed var(--gray-300);
    margin: 1rem 0;
    transition: all 0.3s ease;
}

.text-center.py-5:hover {
    border-color: var(--primary-color);
    background: linear-gradient(135deg, var(--white) 0%, var(--gray-50) 100%);
}

.text-center.py-5 i {
    font-size: 3.5rem;
    color: var(--gray-500);
    display: block;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.text-center.py-5:hover i {
    color: var(--primary-color);
    transform: scale(1.05);
}

.text-center.py-5 p {
    color: var(--text-muted);
    margin-bottom: 1rem;
    font-size: 0.9375rem;
    font-weight: 500;
}

.text-center.py-3 {
    padding: 2rem 1rem;
    background: var(--gray-50);
    border-radius: 0.5rem;
}

/* Responsive Styles */
@media (max-width: 1200px) {
    .stat-card-hover h3 {
        font-size: 1.5rem;
    }
}

@media (max-width: 992px) {
    .header-gradient-card h1 {
        font-size: 1.75rem;
    }
    
    .stat-card-hover .card-body {
        padding: 1rem;
    }
    
    .stat-card-hover h3 {
        font-size: 1.5rem;
    }
    
    .stat-card-hover .bg-gradient {
        width: 48px;
        height: 48px;
        padding: 0.75rem !important;
    }
    
    .stat-card-hover .fs-4 {
        font-size: 1rem !important;
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    .col-lg-4 {
        margin-top: 1rem;
    }
}

@media (max-width: 768px) {
    .header-gradient-card .card-body {
        padding: 1.5rem !important;
    }
    
    .header-gradient-card h1 {
        font-size: 1.5rem;
    }
    
    .header-gradient-card h1 .bi {
        font-size: 1.25rem;
    }
    
    .header-gradient-card p {
        font-size: 0.875rem;
    }
    
    .card-header {
        padding: 1rem 1.25rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .stat-card-hover h3 {
        font-size: 1.25rem;
    }
    
    .stat-card-hover small {
        font-size: 0.75rem;
    }
    
    .stat-card-hover .bg-gradient {
        width: 40px;
        height: 40px;
        padding: 0.625rem !important;
    }
    
    .accordion-button {
        padding: 0.875rem 1rem;
        font-size: 0.875rem;
    }
    
    .accordion-body {
        padding: 1rem;
    }
    
    .list-group-item {
        padding: 0.875rem;
    }
    
    .btn-lg {
        padding: 0.5rem 1rem;
        font-size: 1rem;
    }
    
    .flex-wrap {
        flex-wrap: wrap !important;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
    
    .header-gradient-card .card-body {
        padding: 1.25rem !important;
    }
    
    .header-gradient-card h1 {
        font-size: 1.25rem;
    }
    
    .header-gradient-card h1 .bi {
        font-size: 1rem;
        margin-right: 0.5rem !important;
    }
    
    [dir="rtl"] .header-gradient-card h1 .bi {
        margin-left: 0.5rem !important;
        margin-right: 0 !important;
    }
    
    .row.g-3,
    .row.g-4 {
        --bs-gutter-y: 0.75rem;
        --bs-gutter-x: 0.75rem;
    }
    
    .card-body {
        padding: 0.875rem;
    }
    
    .card-header {
        padding: 0.875rem 1rem;
    }
    
    .stat-card-hover .card-body {
        padding: 0.75rem;
    }
    
    .stat-card-hover h3 {
        font-size: 1rem;
    }
    
    .stat-card-hover small {
        font-size: 0.7rem;
    }
    
    .stat-card-hover .bg-gradient {
        width: 32px;
        height: 32px;
        padding: 0.5rem !important;
    }
    
    .stat-card-hover .bg-gradient i {
        font-size: 0.875rem !important;
    }
    
    .accordion-button {
        padding: 0.75rem 0.875rem;
        font-size: 0.8125rem;
    }
    
    .accordion-button .badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .accordion-body {
        padding: 0.875rem;
    }
    
    .list-group-item {
        padding: 0.75rem;
    }
    
    .text-center.py-5 {
        padding: 2rem 1rem;
    }
    
    .text-center.py-5 i {
        font-size: 2.5rem;
    }
    
    .text-center.py-3 {
        padding: 1.5rem 0.75rem;
    }
    
    .gap-2 {
        gap: 0.375rem !important;
    }
    
    .gap-3 {
        gap: 0.75rem !important;
    }
    
    .btn {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }
    
    .btn i {
        font-size: 0.875rem;
    }
}

/* RTL Support */
/* RTL Support */
[dir="rtl"] .header-gradient-card .text-md-end {
    text-align: left !important;
}

[dir="rtl"] .me-1 {
    margin-right: 0 !important;
    margin-left: 0.25rem !important;
}

[dir="rtl"] .me-2 {
    margin-right: 0 !important;
    margin-left: 0.5rem !important;
}

[dir="rtl"] .ms-1 {
    margin-left: 0 !important;
    margin-right: 0.25rem !important;
}

[dir="rtl"] .ms-2 {
    margin-left: 0 !important;
    margin-right: 0.5rem !important;
}

[dir="rtl"] .breadcrumb-item + .breadcrumb-item::before {
    content: "‹";
    padding: 0 0.5rem;
}

[dir="rtl"] .text-start {
    text-align: right !important;
}

[dir="rtl"] .text-end {
    text-align: left !important;
}

[dir="rtl"] .accordion-button::after {
    margin-left: 0;
    margin-right: auto;
}

/* Bootstrap Overrides for Better Integration */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
    text-decoration: none;
}

.btn i {
    flex-shrink: 0;
}

.btn-sm {
    font-size: 0.875rem;
    line-height: 1.5;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    white-space: nowrap;
}

/* Accordion Improvements */
.accordion-button {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
}

.accordion-button .badge {
    flex-shrink: 0;
    font-size: 0.875rem;
    padding: 0.35rem 0.65rem;
}

.accordion-button:not(.collapsed) {
    background: linear-gradient(to bottom, rgba(13, 110, 253, 0.08) 0%, var(--white) 100%);
    color: var(--primary-color);
    font-weight: 600;
    box-shadow: inset 0 -1px 0 rgba(13, 110, 253, 0.15);
}

.accordion-item {
    border: 1px solid rgba(0, 0, 0, 0.125);
    margin-bottom: 0.5rem;
    border-radius: 0.375rem !important;
    overflow: hidden;
}

.accordion-item:last-child {
    margin-bottom: 0;
}

.accordion-body {
    padding: 1.5rem;
}

/* Card Header Improvements */
.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    background-color: #f8f9fa;
}

.card-header h3,
.card-header h5,
.card-header h6 {
    margin-bottom: 0;
}

/* Improved Responsive Utilities */
@media (max-width: 576px) {
    .d-none.d-sm-inline {
        display: none !important;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.8125rem;
    }
    
    .accordion-button {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
    }
}

/* Better List Group Item Styling */
.list-group-item .min-w-0 {
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
}

.list-group-item {
    background-color: #fff;
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

/* Flex Utilities */
.flex-shrink-0 {
    flex-shrink: 0 !important;
}

.flex-grow-1 {
    flex-grow: 1 !important;
}

.gap-2 {
    gap: 0.5rem !important;
}

.gap-3 {
    gap: 1rem !important;
}

/* Better Button Alignment */
.d-grid .btn {
    justify-content: flex-start;
    padding-left: 1rem;
    padding-right: 1rem;
}

[dir="rtl"] .d-grid .btn {
    justify-content: flex-start;
}

/* Card Body Improvements */
.card-body {
    background: #ffffff;
}

.card {
    background: #ffffff;
}

/* Better Shadow for Cards */
.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.shadow-lg {
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
}

/* Improved Text Muted */
.text-muted {
    color: #6c757d !important;
}

/* Better Small Text */
small.text-muted {
    font-size: 0.875rem;
    line-height: 1.5;
}

/* Improved Badge Styling */
.badge {
    font-weight: 500;
    letter-spacing: 0.025em;
}

.badge.bg-info {
    background-color: #0dcaf0 !important;
    color: #000;
}

.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
}

.badge.bg-primary {
    background-color: #0d6efd !important;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
}

/* Breadcrumb Improvements */
.breadcrumb {
    margin-bottom: 0;
    background: transparent;
    padding: 0;
    display: flex;
    flex-wrap: wrap;
    list-style: none;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
}

.breadcrumb-item a {
    text-decoration: none;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
}

.breadcrumb-item a:hover {
    text-decoration: underline;
    opacity: 0.9;
}

.breadcrumb-item.active {
    font-weight: 500;
}

/* Icon Improvements */
.bi {
    vertical-align: middle;
}

/* Better Hover Effects */
.card:hover {
    transition: all 0.3s ease;
}

.list-group-item-action {
    cursor: pointer;
    transition: all 0.2s ease;
}

.list-group-item-action:hover {
    background-color: var(--gray-50);
    transform: translateX(3px);
}

[dir="rtl"] .list-group-item-action:hover {
    transform: translateX(-3px);
}

/* Better Button Styling */
.btn-outline-info:hover {
    color: #000;
}

.btn-outline-warning:hover {
    color: #000;
}

/* Ensure all buttons are clickable and properly styled */
.btn,
.btn-sm,
.btn-outline-primary,
.btn-outline-info,
.btn-outline-warning,
.btn-outline-secondary,
.btn-outline-success,
.btn-success,
.btn-info,
.btn-primary {
    cursor: pointer;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    pointer-events: auto;
    position: relative;
    z-index: 1;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.btn:disabled,
.btn.disabled {
    cursor: not-allowed;
    opacity: 0.65;
    pointer-events: none;
}

/* Ensure Link components styled as buttons work properly */
a.btn,
a.btn-sm {
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

a.btn:hover,
a.btn-sm:hover {
    text-decoration: none;
    transform: translateY(-1px);
}

a.btn:active,
a.btn-sm:active {
    transform: translateY(0);
}

/* Button hover effects */
.btn-outline-primary:hover {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.btn-outline-info:hover {
    background-color: var(--info-color);
    border-color: var(--info-color);
    color: #000;
}

.btn-outline-warning:hover {
    background-color: var(--warning-color);
    border-color: var(--warning-color);
    color: #000;
}

.btn-outline-secondary:hover {
    background-color: var(--gray-600);
    border-color: var(--gray-600);
    color: white;
}

.btn-outline-success:hover {
    background-color: var(--success-color);
    border-color: var(--success-color);
    color: white;
}

.btn-success:hover {
    background-color: #157347;
    border-color: #146c43;
}

.btn-info:hover {
    background-color: var(--info-dark);
    border-color: var(--info-dark);
}

.btn-primary:hover {
    background-color: var(--primary-dark);
    border-color: var(--primary-dark);
}

/* Accordion button improvements */
.accordion-button {
    cursor: pointer;
    user-select: none;
}

.accordion-button:not(.collapsed)::after {
    transform: rotate(-180deg);
}

[dir="rtl"] .accordion-button:not(.collapsed)::after {
    transform: rotate(180deg);
}

/* Responsive Image */
.course-thumbnail-image {
    object-fit: cover;
    width: 100%;
    height: auto;
}

/* Improved Spacing */
.mb-1 {
    margin-bottom: 0.25rem !important;
}

.mb-2 {
    margin-bottom: 0.5rem !important;
}

.mb-3 {
    margin-bottom: 1rem !important;
}

.mb-4 {
    margin-bottom: 1.5rem !important;
}

/* Better Alignment */
.align-items-center {
    align-items: center !important;
}

.align-items-start {
    align-items: flex-start !important;
}

.justify-content-between {
    justify-content: space-between !important;
}

.justify-content-start {
    justify-content: flex-start !important;
}
</style>

