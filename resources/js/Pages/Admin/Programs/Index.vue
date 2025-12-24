<template>
    <AdminLayout :page-title="t('programs.programs_management') || 'Programs'">
        <Head :title="t('programs.programs_management') || 'Programs'" />
        <div class="container-fluid px-3 px-md-4 px-lg-5 py-4">
            <!-- Page Header -->
            <div class="mb-4">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item">
                            <Link :href="route('admin.dashboard')" class="text-decoration-none">
                                {{ t('common.dashboard') || 'Dashboard' }}
                            </Link>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ t('programs.programs_management') || 'Programs' }}
                        </li>
                    </ol>
                </nav>
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h1 class="h2 fw-bold mb-2">{{ t('programs.programs_management') || 'Programs' }}</h1>
                        <p class="text-muted mb-0">{{ t('programs.programs_description') || 'Manage training programs' }}</p>
                    </div>
                    <Link :href="route('admin.programs.create')" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i>
                        {{ t('programs.create_program') || 'Create Program' }}
                    </Link>
                </div>
            </div>

            <!-- Programs Grid -->
            <div v-if="programs?.data && programs.data.length > 0" class="row g-4">
                <div v-for="program in programs.data" :key="program.id" class="col-12 col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 h-100 transition-hover">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary bg-opacity-10 rounded p-3">
                                        <i class="bi bi-diagram-3 text-primary fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1 fw-semibold">{{ program.translated_name || program.name }}</h5>
                                        <small class="text-muted d-block">{{ program.slug }}</small>
                                    </div>
                                </div>
                                <span class="badge align-self-start" :class="program.is_active ? 'bg-success' : 'bg-secondary'">
                                    {{ program.is_active ? (t('common.active') || 'Active') : (t('common.inactive') || 'Inactive') }}
                                </span>
                            </div>
                            <p v-if="program.translated_description || program.description" class="text-muted small mb-3 line-clamp-2">
                                {{ (program.translated_description || program.description)?.substring(0, 100) }}{{ (program.translated_description || program.description)?.length > 100 ? '...' : '' }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <small class="text-muted d-flex align-items-center">
                                    <i class="bi bi-diagram-3 me-1"></i>
                                    {{ program.tracks?.length || 0 }} {{ t('programs.tracks') || 'Tracks' }}
                                </small>
                                <div class="btn-group btn-group-sm">
                                    <Link :href="route('admin.programs.show', program.slug || program.id)" class="btn btn-outline-primary" :title="t('common.view') || 'View'">
                                        <i class="bi bi-eye"></i>
                                    </Link>
                                    <Link :href="route('admin.programs.edit', program.slug || program.id)" class="btn btn-outline-secondary" :title="t('common.edit') || 'Edit'">
                                        <i class="bi bi-pencil"></i>
                                    </Link>
                                    <button 
                                        v-if="(program.tracks?.length || 0) === 0"
                                        @click="confirmDelete(program)" 
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
                    <i class="bi bi-diagram-3 text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 mb-2">{{ t('programs.no_programs') || 'No programs found' }}</h5>
                    <p class="text-muted mb-4">{{ t('programs.no_programs_description') || 'Get started by creating your first program' }}</p>
                    <Link :href="route('admin.programs.create')" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        {{ t('programs.create_program') || 'Create Program' }}
                    </Link>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="programs?.links && programs.links.length > 3" class="mt-4">
                <nav aria-label="Programs pagination">
                    <ul class="pagination justify-content-center">
                        <li v-for="link in programs.links" :key="link.label" class="page-item" :class="{ active: link.active, disabled: !link.url }">
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
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useAlert } from '@/composables/useAlert';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    programs: Object,
});

const { t } = useTranslation();
const { route } = useRoute();
const { showConfirm, showSuccess, showError } = useAlert();

const confirmDelete = async (program) => {
    const identifier = program.slug || program.id;
    if (!identifier) {
        showError(
            t('programs.delete_error') || 'Failed to delete program',
            t('common.error') || 'Error'
        );
        return;
    }

    // Check if program has tracks
    const tracksCount = program.tracks?.length || 0;
    if (tracksCount > 0) {
        showError(
            t('programs.cannot_delete_has_tracks') || `Cannot delete program. This program contains ${tracksCount} track(s). Please remove all tracks first.`,
            t('programs.delete_error') || 'Cannot Delete Program'
        );
        return;
    }

    const result = await showConfirm(
        t('programs.confirm_delete_program') || 'Are you sure you want to delete this program? This action cannot be undone.',
        t('programs.delete_title') || 'Delete Program'
    );

    if (result.isConfirmed) {
        router.delete(route('admin.programs.destroy', identifier), {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess(
                    t('programs.deleted_successfully') || 'Program deleted successfully!',
                    t('common.success') || 'Success'
                );
            },
            onError: () => {
                showError(
                    t('programs.delete_error') || 'Failed to delete program',
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

.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    padding: 0 0.5rem;
    color: #adb5bd;
}

[dir="rtl"] .breadcrumb-item + .breadcrumb-item::before {
    content: "‹";
}

.breadcrumb-item a {
    color: #6c757d;
    text-decoration: none;
    transition: color 0.2s;
}

.breadcrumb-item a:hover {
    color: #0d6efd;
}

.breadcrumb-item.active {
    color: #212529;
    font-weight: 500;
}
</style>

