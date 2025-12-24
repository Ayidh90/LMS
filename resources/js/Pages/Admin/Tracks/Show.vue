<template>
    <AdminLayout :page-title="track?.translated_name || track?.name || 'Track'">
        <Head :title="track?.translated_name || track?.name || 'Track'" />
        <div class="container-fluid px-3 px-md-4 px-lg-5 py-4">
            <div class="mb-4">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item">
                            <Link :href="route('admin.tracks.index')" class="text-decoration-none">
                                {{ t('tracks.tracks_management') || 'Tracks' }}
                            </Link>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ track?.translated_name || track?.name }}
                        </li>
                    </ol>
                </nav>
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h1 class="h2 fw-bold mb-2">{{ track?.translated_name || track?.name }}</h1>
                        <p class="text-muted mb-0">{{ track?.translated_description || track?.description }}</p>
                        <small class="text-muted">
                            {{ t('tracks.program') || 'Program' }}: 
                            <Link :href="route('admin.programs.show', track?.program?.slug || track?.program?.id)" class="text-decoration-none">
                                {{ track?.program?.translated_name || track?.program?.name }}
                            </Link>
                        </small>
                    </div>
                    <div class="btn-group">
                        <Link :href="route('admin.tracks.edit', track?.slug || track?.id)" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>
                            {{ t('common.edit') || 'Edit' }}
                        </Link>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Statistics -->
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="bi bi-book text-primary fs-1"></i>
                            <h3 class="mt-3 mb-0">{{ track?.courses?.length || 0 }}</h3>
                            <p class="text-muted mb-0">{{ t('tracks.courses') || 'Courses' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="bi bi-check-circle text-success fs-1"></i>
                            <h3 class="mt-3 mb-0">{{ getPublishedCourses() }}</h3>
                            <p class="text-muted mb-0">{{ t('tracks.published_courses') || 'Published Courses' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="bi bi-people text-info fs-1"></i>
                            <h3 class="mt-3 mb-0">{{ getTotalStudents() }}</h3>
                            <p class="text-muted mb-0">{{ t('tracks.total_students') || 'Total Students' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Courses -->
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">{{ t('tracks.courses') || 'Courses' }}</h5>
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
                                            <Link :href="route('admin.courses.show', course.slug || course.id)" class="btn btn-sm btn-outline-primary w-100">
                                                {{ t('common.view') || 'View Course' }}
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-4">
                                <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-3">{{ t('tracks.no_courses_in_track') || 'No courses in this track' }}</p>
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
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    track: Object,
});

const { t } = useTranslation();
const { route } = useRoute();

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
</script>

