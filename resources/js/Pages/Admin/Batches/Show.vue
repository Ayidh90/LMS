<template>
    <AdminLayout :page-title="batch?.name || t('admin.batch_details') || 'Batch Details'">
        <Head :title="batch?.name || t('admin.batch_details') || 'Batch Details'" />
        
        <div class="space-y-6 min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 pb-8">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-2xl p-8 shadow-2xl relative overflow-hidden">
                <div class="absolute inset-0 bg-black/5"></div>
                <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-48 -mt-48 blur-3xl"></div>
                <div class="relative z-10 text-white flex-1">
                    <div class="flex items-center gap-2 text-sm text-blue-100 mb-3">
                        <Link :href="route('admin.courses.index')" class="hover:text-white transition-colors font-medium">{{ t('admin.courses_management') || 'Courses' }}</Link>
                        <span>›</span>
                        <Link :href="route('admin.courses.show', course.slug || course.id)" class="hover:text-white transition-colors font-medium">{{ course.title }}</Link>
                        <span>›</span>
                        <Link :href="route('admin.courses.batches.index', course.slug || course.id)" class="hover:text-white transition-colors font-medium">{{ t('admin.batches') || 'Batches' }}</Link>
                        <span>›</span>
                        <span class="text-white font-semibold">{{ batch?.name }}</span>
                    </div>
                    <h1 class="text-4xl font-bold mb-3">{{ batch?.name }}</h1>
                    <p class="text-blue-100 text-base">{{ batch?.description || t('admin.batch_details_description') || 'View batch details and manage students' }}</p>
                </div>
                <div class="relative z-10 flex items-center gap-3">
                    <Link :href="route('admin.courses.batches.edit', [course.slug || course.id, batch?.id])">
                        <Button 
                            :label="t('common.edit') || 'Edit'" 
                            icon="pi pi-pencil"
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

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <Card class="stat-card">
                    <template #content>
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-3xl font-bold text-gray-900 mb-1">{{ batch?.students?.length || 0 }}</div>
                                <div class="text-sm text-gray-600">{{ t('admin.enrolled_students') || 'Enrolled Students' }}</div>
                            </div>
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="pi pi-users text-white text-2xl"></i>
                            </div>
                        </div>
                    </template>
                </Card>

                <Card class="stat-card">
                    <template #content>
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-3xl font-bold text-gray-900 mb-1">{{ batch?.max_students || '-' }}</div>
                                <div class="text-sm text-gray-600">{{ t('admin.max_students') || 'Max Students' }}</div>
                            </div>
                            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="pi pi-users text-white text-2xl"></i>
                            </div>
                        </div>
                    </template>
                </Card>

                <Card class="stat-card">
                    <template #content>
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-3xl font-bold text-gray-900 mb-1">
                                    <Tag :value="batch?.is_active ? t('common.active') : t('common.inactive')" :severity="batch?.is_active ? 'success' : 'secondary'" />
                                </div>
                                <div class="text-sm text-gray-600 mt-2">{{ t('admin.status') || 'Status' }}</div>
                            </div>
                            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="pi pi-check-circle text-white text-2xl"></i>
                            </div>
                        </div>
                    </template>
                </Card>

                <Card class="stat-card">
                    <template #content>
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-semibold text-gray-900 mb-1">{{ batch?.instructor?.name || '-' }}</div>
                                <div class="text-sm text-gray-600">{{ t('admin.instructor') || 'Instructor' }}</div>
                            </div>
                            <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="pi pi-user text-white text-2xl"></i>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Batch Details -->
                <div class="lg:col-span-2 space-y-6">
                    <Card>
                        <template #header>
                            <div class="p-6 pb-0">
                                <h2 class="text-xl font-bold text-gray-900">{{ t('admin.batch_information') || 'Batch Information' }}</h2>
                            </div>
                        </template>
                        <template #content>
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-500">{{ t('admin.batch_name') || 'Batch Name' }}</label>
                                    <p class="mt-1 text-gray-900 font-semibold">{{ batch?.name }}</p>
                                </div>
                                <div v-if="batch?.description">
                                    <label class="text-sm font-medium text-gray-500">{{ t('admin.description') || 'Description' }}</label>
                                    <p class="mt-1 text-gray-600">{{ batch?.description }}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div v-if="batch?.start_date">
                                        <label class="text-sm font-medium text-gray-500">{{ t('common.start_date') || 'Start Date' }}</label>
                                        <p class="mt-1 text-gray-900">{{ formatDate(batch.start_date) }}</p>
                                    </div>
                                    <div v-if="batch?.end_date">
                                        <label class="text-sm font-medium text-gray-500">{{ t('common.end_date') || 'End Date' }}</label>
                                        <p class="mt-1 text-gray-900">{{ formatDate(batch.end_date) }}</p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Card>

                    <!-- Students List -->
                    <Card>
                        <template #header>
                            <div class="flex items-center justify-between p-6 pb-0">
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">{{ t('admin.enrolled_students') || 'Enrolled Students' }}</h2>
                                    <p class="text-sm text-gray-500 mt-1">{{ t('admin.manage_students_description') || 'View and manage enrolled students' }}</p>
                                </div>
                                <Button 
                                    :label="t('admin.add_students') || 'Add Students'" 
                                    icon="pi pi-plus"
                                    @click="showAddStudentsModal = true"
                                    class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700"
                                />
                            </div>
                        </template>
                        <template #content>
                            <div v-if="batch?.students && batch.students.length > 0" class="space-y-3">
                                <div 
                                    v-for="student in batch.students" 
                                    :key="student.id"
                                    class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors"
                                >
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white font-bold shadow-md">
                                            {{ student.name?.[0]?.toUpperCase() || 'S' }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ student.name }}</div>
                                            <div class="text-sm text-gray-500">{{ student.email }}</div>
                                            <div class="text-xs text-gray-400 mt-1">{{ t('admin.enrolled_at') || 'Enrolled' }}: {{ formatDate(student.enrolled_at) }}</div>
                                        </div>
                                    </div>
                                    <Button 
                                        icon="pi pi-times"
                                        severity="danger"
                                        text
                                        rounded
                                        @click="removeStudent(student)"
                                        :title="t('admin.remove_student') || 'Remove Student'"
                                    />
                                </div>
                            </div>
                            <div v-else class="text-center py-12">
                                <i class="pi pi-users text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">{{ t('admin.no_students_enrolled') || 'No students enrolled yet' }}</p>
                            </div>
                        </template>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <Card>
                        <template #header>
                            <div class="p-6 pb-0">
                                <h3 class="text-lg font-bold text-gray-900">{{ t('admin.quick_actions') || 'Quick Actions' }}</h3>
                            </div>
                        </template>
                        <template #content>
                            <div class="space-y-3">
                                <Link :href="route('admin.courses.batches.edit', [course.slug || course.id, batch?.id])" class="block w-full">
                                    <Button 
                                        :label="t('common.edit') || 'Edit Batch'" 
                                        icon="pi pi-pencil"
                                        class="w-full justify-start"
                                        outlined
                                        severity="info"
                                    />
                                </Link>
                                <Link :href="route('admin.courses.show', course.slug || course.id)" class="block w-full">
                                    <Button 
                                        :label="t('admin.back_to_course') || 'Back to Course'" 
                                        icon="pi pi-arrow-left"
                                        class="w-full justify-start"
                                        outlined
                                        severity="secondary"
                                    />
                                </Link>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>

        <!-- Add Students Modal -->
        <Dialog 
            v-model:visible="showAddStudentsModal" 
            :header="t('admin.add_students') || 'Add Students'"
            :modal="true"
            :style="{ width: '600px' }"
        >
            <div v-if="availableStudents && availableStudents.length > 0" class="space-y-3 max-h-96 overflow-y-auto">
                <div 
                    v-for="student in availableStudents" 
                    :key="student.id"
                    class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50"
                >
                    <div class="flex items-center gap-3">
                        <Checkbox 
                            v-model="selectedStudents" 
                            :inputId="`student-${student.id}`"
                            :value="student.id"
                        />
                        <label :for="`student-${student.id}`" class="cursor-pointer">
                            <div class="font-medium text-gray-900">{{ student.name }}</div>
                            <div class="text-sm text-gray-500">{{ student.email }}</div>
                        </label>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-8">
                <i class="pi pi-users text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">{{ t('admin.no_available_students') || 'No available students to add' }}</p>
            </div>
            <template #footer>
                <Button 
                    :label="t('common.cancel') || 'Cancel'" 
                    severity="secondary"
                    outlined
                    @click="showAddStudentsModal = false"
                />
                <Button 
                    :label="t('admin.add_selected') || 'Add Selected'" 
                    icon="pi pi-check"
                    @click="addStudents"
                    :disabled="selectedStudents.length === 0"
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700"
                />
            </template>
        </Dialog>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { ref } from 'vue';

const props = defineProps({
    course: Object,
    batch: Object,
    availableStudents: Array,
});

const { t } = useTranslation();
const { route } = useRoute();
const page = usePage();

const showAddStudentsModal = ref(false);
const selectedStudents = ref([]);

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const addStudents = () => {
    router.post(
        route('admin.courses.batches.add-students', [props.course.slug || props.course.id, props.batch?.id]),
        { student_ids: selectedStudents.value },
        {
            onSuccess: () => {
                showAddStudentsModal.value = false;
                selectedStudents.value = [];
            },
        }
    );
};

const removeStudent = (student) => {
    if (confirm(t('admin.confirm_remove_student') || 'Are you sure you want to remove this student?')) {
        router.delete(
            route('admin.courses.batches.remove-student', [props.course.slug || props.course.id, props.batch?.id, student.id]),
            {
                onSuccess: () => {
                    // Success handled by flash message
                },
            }
        );
    }
};
</script>

<style scoped>
:deep(.p-card) {
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
}

:deep(.p-button) {
    font-weight: 500;
}

:deep(.p-dialog) {
    border-radius: 1rem;
}
</style>

