<template>
    <AdminLayout :page-title="t('tracks.tracks_management') || 'Tracks'">
        <Head :title="t('tracks.tracks_management') || 'Tracks'" />
        <div class="container-fluid px-3 px-md-4 px-lg-5 py-4">
            <div class="tracks-index-header">
                <nav aria-label="breadcrumb" class="tracks-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <Link :href="route('admin.dashboard')" class="breadcrumb-link">
                                {{ t('common.dashboard') || 'Dashboard' }}
                            </Link>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ t('tracks.tracks_management') || 'Tracks' }}
                        </li>
                    </ol>
                </nav>
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div class="tracks-header-text">
                        <h1 class="tracks-title">{{ t('tracks.tracks_management') || 'Tracks' }}</h1>
                        <p class="tracks-subtitle">{{ t('tracks.tracks_description') || 'Manage learning tracks' }}</p>
                    </div>
                    <Link :href="route('admin.tracks.create')" class="btn btn-primary btn-lg tracks-create-btn">
                        <i class="bi bi-plus-circle me-2"></i>
                        {{ t('tracks.create') || 'Create Track' }}
                    </Link>
                </div>
            </div>

            <!-- Filters -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label">{{ t('tracks.filter_by_program') || 'Filter by Program' }}</label>
                            <select v-model="selectedProgram" @change="applyFilters" class="form-select">
                                <option value="">{{ t('common.all') || 'All Programs' }}</option>
                                <option v-for="program in programs" :key="program.id" :value="program.id">
                                    {{ program.translated_name || program.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tracks Grid -->
            <div v-if="tracks?.data && tracks.data.length > 0" class="row g-4">
                <div v-for="track in tracks.data" :key="track.id" class="col-12 col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 h-100 transition-hover">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-success bg-opacity-10 rounded p-3">
                                        <i class="bi bi-diagram-2 text-success fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1 fw-semibold">{{ track.translated_name || track.name }}</h5>
                                        <small class="text-muted d-block">{{ track.program?.translated_name || track.program?.name }}</small>
                                    </div>
                                </div>
                                <span class="badge align-self-start" :class="track.is_active ? 'bg-success' : 'bg-secondary'">
                                    {{ track.is_active ? (t('common.active') || 'Active') : (t('common.inactive') || 'Inactive') }}
                                </span>
                            </div>
                            <p v-if="track.translated_description || track.description" class="text-muted small mb-3 line-clamp-2">
                                {{ (track.translated_description || track.description)?.substring(0, 100) }}{{ (track.translated_description || track.description)?.length > 100 ? '...' : '' }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <small class="text-muted d-flex align-items-center">
                                    <i class="bi bi-book me-1"></i>
                                    {{ track.courses?.length || 0 }} {{ t('tracks.courses') || 'Courses' }}
                                </small>
                                <div class="btn-group btn-group-sm">
                                    <Link :href="route('admin.tracks.show', track.slug || track.id)" class="btn btn-outline-primary" :title="t('common.view') || 'View'">
                                        <i class="bi bi-eye"></i>
                                    </Link>
                                    <Link :href="route('admin.tracks.edit', track.slug || track.id)" class="btn btn-outline-secondary" :title="t('common.edit') || 'Edit'">
                                        <i class="bi bi-pencil"></i>
                                    </Link>
                                    <button 
                                        v-if="(track.courses?.length || 0) === 0"
                                        @click="confirmDelete(track)" 
                                        class="btn btn-outline-danger" 
                                        :title="t('common.delete') || 'Delete'"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                    <i class="bi bi-diagram-2 text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 mb-2">{{ t('tracks.no_tracks') || 'No tracks found' }}</h5>
                    <p class="text-muted mb-4">{{ t('tracks.no_tracks_description') || 'Get started by creating your first track' }}</p>
                    <Link :href="route('admin.tracks.create')" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        {{ t('tracks.create') || 'Create Track' }}
                    </Link>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="tracks?.links && tracks.links.length > 3" class="mt-4">
                <nav aria-label="Tracks pagination">
                    <ul class="pagination justify-content-center">
                        <li v-for="link in tracks.links" :key="link.label" class="page-item" :class="{ active: link.active, disabled: !link.url }">
                            <Link v-if="link.url" :href="link.url" class="page-link">
                                <span v-html="link.label"></span>
                            </Link>
                            <span v-else class="page-link" v-html="link.label"></span>
                        </li>
                    </ul>
                </nav>
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
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    tracks: Object,
    programs: Array,
    filters: Object,
});

const { t } = useTranslation();
const { route } = useRoute();
const { showConfirm, showSuccess, showError } = useAlert();

const selectedProgram = ref(props.filters?.program_id || '');

const applyFilters = () => {
    router.get(route('admin.tracks.index'), { program_id: selectedProgram.value || null }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const confirmDelete = async (track) => {
    const identifier = track.slug || track.id;
    if (!identifier) {
        showError(
            t('tracks.delete_error') || 'Failed to delete track',
            t('common.error') || 'Error'
        );
        return;
    }

    // Check if track has courses
    const coursesCount = track.courses?.length || 0;
    if (coursesCount > 0) {
        showError(
            t('tracks.cannot_delete_has_courses') || `Cannot delete track. This track contains ${coursesCount} course(s). Please remove all courses first.`,
            t('tracks.delete_error') || 'Cannot Delete Track'
        );
        return;
    }

    const result = await showConfirm(
        t('tracks.confirm_delete_track') || 'Are you sure you want to delete this track? This action cannot be undone.',
        t('tracks.delete_title') || 'Delete Track'
    );

    if (result.isConfirmed) {
        router.delete(route('admin.tracks.destroy', identifier), {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess(
                    t('tracks.deleted_successfully') || 'Track deleted successfully!',
                    t('common.success') || 'Success'
                );
            },
            onError: () => {
                showError(
                    t('tracks.delete_error') || 'Failed to delete track',
                    t('common.error') || 'Error'
                );
            },
        });
    }
};
</script>

<style scoped>
.transition-hover {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.transition-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.btn-group-sm .btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.card-title {
    word-break: break-word;
}

/* Breadcrumb Styling */
.tracks-breadcrumb {
    margin-bottom: 1.5rem;
}

.tracks-breadcrumb .breadcrumb {
    background: transparent;
    padding: 0;
    margin: 0;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tracks-breadcrumb .breadcrumb-item {
    display: flex;
    align-items: center;
    color: #6b7280;
}

.tracks-breadcrumb .breadcrumb-item + .breadcrumb-item::before {
    content: "/";
    padding: 0 0.75rem;
    color: #9ca3af;
    font-weight: 400;
    display: inline-block;
}

[dir="rtl"] .tracks-breadcrumb .breadcrumb-item + .breadcrumb-item::before {
    content: "/";
}

.tracks-breadcrumb .breadcrumb-link {
    color: #6b7280;
    text-decoration: none;
    transition: color 0.2s ease;
}

.tracks-breadcrumb .breadcrumb-link:hover {
    color: #111827;
}

.tracks-breadcrumb .breadcrumb-item.active {
    color: #111827;
    font-weight: 500;
}

/* Page Header */
.tracks-index-header {
    margin-bottom: 2rem;
}

.tracks-header-text {
    flex: 1;
    min-width: 0;
}

.tracks-title {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 0.5rem 0;
    line-height: 1.2;
}

@media (min-width: 768px) {
    .tracks-title {
        font-size: 2.25rem;
    }
}

.tracks-subtitle {
    font-size: 1rem;
    color: #6b7280;
    margin: 0;
    line-height: 1.5;
}

.tracks-create-btn {
    white-space: nowrap;
    flex-shrink: 0;
}

/* RTL Support */
[dir="rtl"] .tracks-header-text {
    text-align: right;
}

[dir="ltr"] .tracks-header-text {
    text-align: left;
}
</style>

