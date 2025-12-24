<template>
    <AdminLayout :page-title="program?.translated_name || program?.name || 'Program'">
        <Head :title="program?.translated_name || program?.name || 'Program'" />
        <div class="container-fluid px-3 px-md-4 px-lg-5 py-4">
            <div class="mb-4">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item">
                            <Link :href="route('admin.programs.index')" class="text-decoration-none">
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
                        <Link :href="route('admin.programs.edit', program?.slug || program?.id)" class="btn btn-primary">
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
                            <i class="bi bi-diagram-3 text-primary fs-1"></i>
                            <h3 class="mt-3 mb-0">{{ program?.tracks?.length || 0 }}</h3>
                            <p class="text-muted mb-0">{{ t('programs.tracks') || 'Tracks' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="bi bi-book text-success fs-1"></i>
                            <h3 class="mt-3 mb-0">{{ getTotalCourses() }}</h3>
                            <p class="text-muted mb-0">{{ t('programs.courses') || 'Courses' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="bi bi-check-circle text-info fs-1"></i>
                            <h3 class="mt-3 mb-0">{{ getActiveTracks() }}</h3>
                            <p class="text-muted mb-0">{{ t('programs.active_tracks') || 'Active Tracks' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tracks -->
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ t('programs.tracks') || 'Tracks' }}</h5>
                            <Link :href="route('admin.tracks.create', { program_id: program?.id })" class="btn btn-sm btn-primary">
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
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="bi bi-book me-1"></i>
                                                    {{ track.courses?.length || 0 }} {{ t('programs.courses') || 'Courses' }}
                                                </small>
                                                <Link :href="route('admin.tracks.show', track.slug || track.id)" class="btn btn-sm btn-outline-primary">
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
                                <Link :href="route('admin.tracks.create', { program_id: program?.id })" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    {{ t('tracks.create') || 'Create Track' }}
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
import { computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    program: Object,
});

const { t } = useTranslation();
const { route } = useRoute();

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
</script>

