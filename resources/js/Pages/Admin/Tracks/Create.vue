<template>
    <AdminLayout :page-title="t('tracks.create') || 'Create Track'">
        <Head :title="t('tracks.create') || 'Create Track'" />
        <div class="container-fluid px-3 px-md-4 px-lg-5 py-4">
            <div class="mb-4">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item">
                            <Link :href="route('admin.tracks.index')" class="text-decoration-none">
                                {{ t('tracks.tracks_management') || 'Tracks' }}
                            </Link>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ t('tracks.create') || 'Create Track' }}</li>
                    </ol>
                </nav>
                <h1 class="h2 fw-bold mb-2">{{ t('tracks.create') || 'Create Track' }}</h1>
            </div>

            <form @submit.prevent="submit" class="card shadow-sm border-0" enctype="multipart/form-data">
                <div class="card-body p-4 p-md-5">
                    <div class="row g-4">
                        <div class="col-12">
                            <label for="program_id" class="form-label fw-semibold">
                                {{ t('tracks.fields.program_id') || 'Program' }} <span class="text-danger">*</span>
                            </label>
                            <select
                                id="program_id"
                                v-model="form.program_id"
                                class="form-select form-select-lg"
                                :class="{ 'is-invalid': form.errors.program_id }"
                                required
                            >
                                <option value="">{{ t('common.select') || 'Select program' }}</option>
                                <option v-for="program in programs" :key="program.id" :value="program.id">
                                    {{ program.translated_name || program.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.program_id" class="invalid-feedback d-block">
                                {{ form.errors.program_id }}
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="name" class="form-label fw-semibold">
                                {{ t('tracks.fields.name') || 'Track Name' }} ({{ t('common.language_english') }}) <span class="text-danger">*</span>
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="form-control form-control-lg"
                                :class="{ 'is-invalid': form.errors.name }"
                                required
                            />
                            <div v-if="form.errors.name" class="invalid-feedback d-block">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="name_ar" class="form-label fw-semibold">
                                {{ t('tracks.fields.name_ar') || 'Track Name (Arabic)' }} ({{ t('common.language_arabic') }})
                            </label>
                            <input
                                id="name_ar"
                                v-model="form.name_ar"
                                type="text"
                                dir="rtl"
                                class="form-control form-control-lg"
                            />
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">
                                {{ t('common.description') || 'Description' }} ({{ t('common.language_english') }})
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                class="form-control"
                            ></textarea>
                        </div>

                        <div class="col-12">
                            <label for="description_ar" class="form-label fw-semibold">
                                {{ t('common.description') || 'Description' }} ({{ t('common.language_arabic') }})
                            </label>
                            <textarea
                                id="description_ar"
                                v-model="form.description_ar"
                                rows="4"
                                dir="rtl"
                                class="form-control"
                            ></textarea>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="order" class="form-label fw-semibold">
                                {{ t('common.order') || 'Order' }}
                            </label>
                            <input
                                id="order"
                                v-model="form.order"
                                type="number"
                                min="0"
                                class="form-control"
                            />
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-check form-switch mt-4">
                                <input
                                    id="is_active"
                                    v-model="form.is_active"
                                    class="form-check-input"
                                    type="checkbox"
                                />
                                <label class="form-check-label" for="is_active">
                                    {{ t('common.active') || 'Active' }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light d-flex justify-content-end gap-2 p-4">
                    <Link :href="route('admin.tracks.index')" class="btn btn-secondary">
                        {{ t('common.cancel') || 'Cancel' }}
                    </Link>
                    <button type="submit" :disabled="form.processing" class="btn btn-primary">
                        <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                        {{ form.processing ? (t('common.creating') || 'Creating...') : (t('common.create') || 'Create') }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    programs: Array,
    selectedProgramId: Number,
});

const { t } = useTranslation();
const { route } = useRoute();

const form = useForm({
    program_id: props.selectedProgramId || null,
    name: '',
    name_ar: '',
    description: '',
    description_ar: '',
    order: 0,
    is_active: true,
});

const submit = () => {
    form.post(route('admin.tracks.store'), {
        preserveScroll: true,
        onSuccess: () => {
            router.visit(route('admin.tracks.index'));
        },
    });
};
</script>

