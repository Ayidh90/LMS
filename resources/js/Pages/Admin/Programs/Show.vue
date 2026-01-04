<template>
    <AdminLayout :page-title="program?.translated_name || program?.name || 'Program'">
        <Head :title="program?.translated_name || program?.name || 'Program'" />
        <div class="container-fluid px-3 px-md-4 px-lg-5 py-4">
            <div class="mb-4">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item">
                            <Link :href="route('admin.programs.index')" class="breadcrumb-link">
                                {{ t('programs.programs_management') || 'Programs' }}
                            </Link>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ program?.translated_name || program?.name }}
                        </li>
                    </ol>
                </nav>
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h1 class="h2 fw-bold mb-2">{{ program?.translated_name || program?.name }}</h1>
                        <p class="text-muted mb-0">{{ program?.translated_description || program?.description }}</p>
                    </div>
                    <div class="btn-group">
                        <Link v-if="can('programs.edit')" :href="route('admin.programs.edit', program?.slug || program?.id)" class="btn btn-primary">
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
                            <i class="bi bi-diagram-3 text-primary fs-1"></i>
                            <h3 class="mt-3 mb-0">{{ program?.tracks?.length || 0 }}</h3>
                            <p class="text-muted mb-0">{{ t('programs.tracks') || 'Tracks' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="bi bi-book text-success fs-1"></i>
                            <h3 class="mt-3 mb-0">{{ getTotalCourses() }}</h3>
                            <p class="text-muted mb-0">{{ t('programs.courses') || 'Courses' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="bi bi-check-circle text-info fs-1"></i>
                            <h3 class="mt-3 mb-0">{{ getActiveTracks() }}</h3>
                            <p class="text-muted mb-0">{{ t('programs.active_tracks') || 'Active Tracks' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="bi bi-graph-up-arrow text-warning fs-1"></i>
                            <h3 class="mt-3 mb-0">{{ progressStats?.overall_completion_percentage || 0 }}%</h3>
                            <p class="text-muted mb-0">{{ t('programs.overall_completion') || 'Overall Completion' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Track Completion Statistics -->
                <div v-if="progressStats && progressStats.track_completion_stats && progressStats.track_completion_stats.length > 0" class="col-12">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="bi bi-graph-up-arrow me-2"></i>
                                {{ t('programs.track_completion') || 'Track Completion' }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ t('programs.track') || 'Track' }}</th>
                                            <th>{{ t('programs.total_students') || 'Total Students' }}</th>
                                            <th>{{ t('programs.completed_students') || 'Completed Students' }}</th>
                                            <th>{{ t('programs.completion_percentage') || 'Completion %' }}</th>
                                            <th>{{ t('programs.progress') || 'Progress' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="stat in progressStats.track_completion_stats" :key="stat.track_id">
                                            <td><strong>{{ stat.track_name }}</strong></td>
                                            <td>{{ stat.total_students }}</td>
                                            <td>{{ stat.completed_students }}</td>
                                            <td>{{ stat.completion_percentage }}%</td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" :class="stat.completion_percentage >= 100 ? 'bg-success' : 'bg-info'" 
                                                         :style="{ width: Math.min(stat.completion_percentage, 100) + '%' }">
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

                <!-- Tracks -->
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ t('programs.tracks') || 'Tracks' }}</h5>
                            <Link v-if="can('tracks.create')" :href="`${route('admin.tracks.create')}?program_id=${program?.id}`" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>
                                {{ t('tracks.create') || 'Create Track' }}
                            </Link>
                        </div>
                        <div class="card-body">
                            <div v-if="program?.tracks && program.tracks.length > 0" class="row g-3">
                                <div v-for="track in program.tracks" :key="track.id" class="col-12 col-md-6 col-lg-4">
                                    <div class="card border h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0">{{ track.translated_name || track.name }}</h6>
                                                <span class="badge" :class="track.is_active ? 'bg-success' : 'bg-secondary'">
                                                    {{ track.is_active ? (t('common.active') || 'Active') : (t('common.inactive') || 'Inactive') }}
                                                </span>
                                            </div>
                                            <p class="card-text small text-muted mb-3">
                                                {{ (track.translated_description || track.description)?.substring(0, 80) }}...
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted">
                                                    <i class="bi bi-book me-1"></i>
                                                    {{ track.courses?.length || 0 }} {{ t('programs.courses') || 'Courses' }}
                                                </small>
                                            </div>
                                            <!-- Track completion progress -->
                                            <div v-if="getTrackCompletion(track.id)" class="mb-2">
                                                <small class="text-muted d-block mb-1">
                                                    {{ t('programs.completion') || 'Completion' }}: {{ getTrackCompletion(track.id) }}%
                                                </small>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-success" :style="{ width: getTrackCompletion(track.id) + '%' }"></div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <Link v-if="can('tracks.view')" :href="route('admin.tracks.show', track.slug || track.id)" class="btn btn-sm btn-outline-primary">
                                                    {{ t('common.view') || 'View' }}
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-4">
                                <i class="bi bi-diagram-3 text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-3">{{ t('programs.no_tracks') || 'No tracks in this program' }}</p>
                                <Link v-if="can('tracks.create')" :href="`${route('admin.tracks.create')}?program_id=${program?.id}`" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    {{ t('tracks.create') || 'Create Track' }}
                                </Link>
                            </div>
                            
                            <!-- Student Progress in Program -->
                            <div v-if="progressStats && progressStats.student_progress && progressStats.student_progress.length > 0" class="mt-4 pt-4 border-top">
                                <div class="d-flex justify-content-between align-items-center mb-3 cursor-pointer" @click="toggleStudentProgress" style="cursor: pointer;">
                                    <h6 class="fw-bold mb-0">
                                        <i class="bi bi-people me-2"></i>
                                        {{ t('programs.student_progress') || 'Student Progress in Program' }}
                                    </h6>
                                    <i class="bi" :class="isStudentProgressCollapsed ? 'bi-chevron-down' : 'bi-chevron-up'" style="transition: transform 0.3s ease"></i>
                                </div>
                                <div v-show="!isStudentProgressCollapsed" class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>{{ t('programs.student') || 'Student' }}</th>
                                                <th>{{ t('programs.completed_tracks') || 'Completed Tracks' }}</th>
                                                <th>{{ t('programs.total_tracks') || 'Total Tracks' }}</th>
                                                <th>{{ t('programs.progress') || 'Progress' }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="student in progressStats.student_progress" :key="student.student_id">
                                                <td><strong>{{ student.student_name }}</strong></td>
                                                <td>{{ student.completed_tracks }}</td>
                                                <td>{{ student.total_tracks }}</td>
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
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { usePermissions } from '@/composables/usePermissions';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    program: Object,
    progressStats: {
        type: Object,
        default: () => null,
    },
});

const { t } = useTranslation();
const { route } = useRoute();
const { can } = usePermissions();

const getTotalCourses = () => {
    if (!props.program?.tracks) return 0;
    return props.program.tracks.reduce((total, track) => {
        return total + (track.courses?.length || 0);
    }, 0);
};

const getActiveTracks = () => {
    if (!props.program?.tracks) return 0;
    return props.program.tracks.filter(track => track.is_active).length;
};

const getTrackCompletion = (trackId) => {
    if (!props.progressStats?.track_completion_stats) return null;
    const stat = props.progressStats.track_completion_stats.find(s => s.track_id === trackId);
    return stat ? stat.completion_percentage : null;
};

// Toggle state for student progress section
const isStudentProgressCollapsed = ref(false);

const toggleStudentProgress = () => {
    isStudentProgressCollapsed.value = !isStudentProgressCollapsed.value;
};
</script>

<style scoped>
.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 0;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
    color: #6b7280;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "/";
    padding: 0 0.75rem;
    color: #9ca3af;
    font-weight: 400;
}

[dir="rtl"] .breadcrumb-item + .breadcrumb-item::before {
    content: "/";
}

.breadcrumb-link {
    color: #6b7280;
    text-decoration: none;
    transition: color 0.2s ease;
}

.breadcrumb-link:hover {
    color: #111827;
}

.breadcrumb-item.active {
    color: #111827;
    font-weight: 500;
}
</style>

