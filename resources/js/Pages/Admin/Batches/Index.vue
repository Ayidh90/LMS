<template>
    <AdminLayout :page-title="t('admin.batches_management') || 'Batches Management'">
        <Head :title="t('admin.batches_management') || 'Batches Management'" />
        
        <div class="space-y-6 min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 pb-8">
            <!-- Page Header - Enhanced -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-2xl p-8 shadow-2xl relative overflow-hidden">
                <div class="absolute inset-0 bg-black/5"></div>
                <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-48 -mt-48 blur-3xl"></div>
                <div class="relative z-10 text-white flex-1">
                    <div class="flex items-center gap-2 text-sm text-blue-100 mb-3">
                        <Link :href="route('admin.courses.index')" class="hover:text-white transition-colors font-medium">{{ t('admin.courses_management') || 'Courses' }}</Link>
                        <span>›</span>
                        <Link :href="route('admin.courses.show', course.slug || course.id)" class="hover:text-white transition-colors font-medium">{{ course.title }}</Link>
                        <span>›</span>
                        <span class="text-white font-semibold">{{ t('admin.batches') || 'Batches' }}</span>
                    </div>
                    <h1 class="text-4xl font-bold mb-3">{{ t('admin.batches_management') || 'Batches Management' }}</h1>
                    <p class="text-blue-100 text-base">{{ t('admin.batches_description') || 'Manage course batches and student enrollments' }}</p>
                </div>
                <div class="relative z-10">
                    <Link :href="route('admin.courses.batches.create', course.slug || course.id)">
                        <Button 
                            :label="t('admin.create_batch') || 'Create Batch'" 
                            icon="pi pi-plus"
                            severity="secondary"
                            outlined
                            class="bg-white hover:bg-gray-50 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
                        />
                    </Link>
                </div>
            </div>

            <!-- Success Message -->
            <div v-if="page.props.flash?.success" class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
                <i class="pi pi-check-circle text-green-600"></i>
                <p class="text-green-800 font-medium">{{ page.props.flash.success }}</p>
            </div>

            <!-- Batches Grid -->
            <div v-if="batches && batches.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card 
                    v-for="batch in batches" 
                    :key="batch.id"
                    class="batch-card hover:shadow-xl transition-all duration-300 cursor-pointer"
                    @click="navigateToBatch(batch)"
                >
                    <template #content>
                        <div class="space-y-4">
                            <!-- Batch Header -->
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-xl font-bold text-gray-900 mb-2 truncate">{{ batch.name }}</h3>
                                    <p v-if="batch.description" class="text-sm text-gray-600 line-clamp-2">{{ batch.description }}</p>
                                </div>
                                <Tag 
                                    :value="batch.is_active ? t('common.active') : t('common.inactive')" 
                                    :severity="batch.is_active ? 'success' : 'secondary'"
                                    class="ml-2"
                                />
                            </div>

                            <!-- Batch Info -->
                            <div class="space-y-2 pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <i class="pi pi-user text-blue-600"></i>
                                    <span class="font-medium">{{ t('admin.instructor') || 'Instructor' }}:</span>
                                    <span class="text-gray-900">{{ batch.instructor?.name || '-' }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <i class="pi pi-users text-indigo-600"></i>
                                    <span class="font-medium">{{ t('admin.enrolled_students') || 'Enrolled' }}:</span>
                                    <span class="text-gray-900 font-semibold">{{ batch.enrollments_count || 0 }}</span>
                                </div>
                                <div v-if="batch.start_date" class="flex items-center gap-2 text-sm text-gray-600">
                                    <i class="pi pi-calendar text-purple-600"></i>
                                    <span class="font-medium">{{ t('common.start_date') || 'Start' }}:</span>
                                    <span class="text-gray-900">{{ formatDate(batch.start_date) }}</span>
                                </div>
                                <div v-if="batch.end_date" class="flex items-center gap-2 text-sm text-gray-600">
                                    <i class="pi pi-calendar-times text-orange-600"></i>
                                    <span class="font-medium">{{ t('common.end_date') || 'End' }}:</span>
                                    <span class="text-gray-900">{{ formatDate(batch.end_date) }}</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2 pt-4 border-t border-gray-100" @click.stop>
                                <Link 
                                    :href="route('admin.courses.batches.show', [course.slug || course.id, batch.id])"
                                    class="flex-1"
                                >
                                    <Button 
                                        :label="t('common.view') || 'View'" 
                                        icon="pi pi-eye"
                                        severity="secondary"
                                        outlined
                                        class="w-full"
                                    />
                                </Link>
                                <Link 
                                    :href="route('admin.courses.batches.edit', [course.slug || course.id, batch.id])"
                                    class="flex-1"
                                >
                                    <Button 
                                        :label="t('common.edit') || 'Edit'" 
                                        icon="pi pi-pencil"
                                        severity="info"
                                        outlined
                                        class="w-full"
                                    />
                                </Link>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>

            <!-- Empty State -->
            <Card v-else>
                <template #content>
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="pi pi-users text-white text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ t('admin.no_batches') || 'No batches yet' }}</h3>
                        <p class="text-gray-500 mb-6">{{ t('admin.no_batches_description') || 'Create your first batch to start enrolling students' }}</p>
                        <Link :href="route('admin.courses.batches.create', course.slug || course.id)">
                            <Button 
                                :label="t('admin.create_batch') || 'Create Batch'" 
                                icon="pi pi-plus"
                                class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700"
                            />
                        </Link>
                    </div>
                </template>
            </Card>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    batches: Array,
});

const { t } = useTranslation();
const { route } = useRoute();
const page = usePage();

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const navigateToBatch = (batch) => {
    router.visit(route('admin.courses.batches.show', [props.course.slug || props.course.id, batch.id]));
};
</script>

<style scoped>
:deep(.p-card) {
    border-radius: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(0, 0, 0, 0.06);
}

.batch-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15), 0 4px 8px rgba(0, 0, 0, 0.1);
    border-color: rgba(59, 130, 246, 0.3);
}

:deep(.p-button) {
    font-weight: 500;
    transition: all 0.3s ease;
}

:deep(.p-button:hover) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

:deep(.p-tag) {
    font-weight: 600;
    padding: 0.375rem 0.75rem;
}
</style>

