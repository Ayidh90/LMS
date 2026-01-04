<template>
    <AdminLayout :page-title="t('admin.create_batch') || 'Create Batch'">
        <Head :title="t('admin.create_batch') || 'Create Batch'" />
        
        <div class="space-y-6 min-h-screen pb-8">
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
                        <span class="text-white font-semibold">{{ t('admin.create_batch') || 'Create Batch' }}</span>
                    </div>
                    <h1 class="text-4xl font-bold mb-3">{{ t('admin.create_batch') || 'Create Batch' }}</h1>
                    <p class="text-blue-100 text-base">{{ t('admin.create_batch_description') || 'Create a new batch for this course' }}</p>
                </div>
            </div>

            <!-- Form Card -->
            <Card>
                <template #content>
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Basic Information -->
                        <div class="space-y-6">
                            <h2 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-3">{{ t('admin.basic_information') || 'Basic Information' }}</h2>
                            
                            <!-- Name (English) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('admin.batch_name') || 'Batch Name' }} (English) <span class="text-red-500">*</span>
                                </label>
                                <InputText
                                    v-model="form.name"
                                    :class="{ 'p-invalid': form.errors.name }"
                                    class="w-full"
                                    :placeholder="t('admin.batch_name_placeholder') || 'Enter batch name'"
                                />
                                <small v-if="form.errors.name" class="p-error">{{ form.errors.name }}</small>
                            </div>

                            <!-- Name (Arabic) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('admin.batch_name') || 'Batch Name' }} (العربية)
                                </label>
                                <InputText
                                    v-model="form.name_ar"
                                    dir="rtl"
                                    class="w-full"
                                    :placeholder="'أدخل اسم الشعبة'"
                                />
                            </div>

                            <!-- Description (English) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('admin.description') || 'Description' }} (English)
                                </label>
                                <Textarea
                                    v-model="form.description"
                                    rows="4"
                                    class="w-full"
                                    :placeholder="t('admin.batch_description_placeholder') || 'Enter batch description'"
                                />
                            </div>

                            <!-- Description (Arabic) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('admin.description') || 'Description' }} (العربية)
                                </label>
                                <Textarea
                                    v-model="form.description_ar"
                                    rows="4"
                                    dir="rtl"
                                    class="w-full"
                                    :placeholder="'أدخل وصف الشعبة'"
                                />
                            </div>

                            <!-- Instructor -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('admin.instructor') || 'Instructor' }} <span class="text-red-500">*</span>
                                </label>
                                <Dropdown
                                    v-model="form.instructor_id"
                                    :options="instructors"
                                    optionLabel="name"
                                    optionValue="id"
                                    :class="{ 'p-invalid': form.errors.instructor_id }"
                                    :placeholder="t('admin.select_instructor') || 'Select an instructor'"
                                    class="w-full"
                                    filter
                                    showClear
                                >
                                    <template #value="slotProps">
                                        <div v-if="slotProps.value" class="flex items-center gap-2">
                                            <i class="pi pi-user"></i>
                                            <span>{{ getInstructorName(slotProps.value) }}</span>
                                        </div>
                                        <span v-else>{{ slotProps.placeholder }}</span>
                                    </template>
                                    <template #option="slotProps">
                                        <div class="flex items-center gap-2">
                                            <i class="pi pi-user"></i>
                                            <span>{{ slotProps.option.name }}</span>
                                            <span class="text-xs text-gray-500">({{ slotProps.option.email }})</span>
                                        </div>
                                    </template>
                                </Dropdown>
                                <small v-if="form.errors.instructor_id" class="p-error">{{ form.errors.instructor_id }}</small>
                            </div>
                        </div>

                        <!-- Schedule Information -->
                        <div class="space-y-6 pt-6 border-t border-gray-200">
                            <h2 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-3">{{ t('admin.schedule_information') || 'Schedule Information' }}</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Start Date -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ t('common.start_date') || 'Start Date' }}
                                    </label>
                                    <Calendar
                                        v-model="form.start_date"
                                        dateFormat="yy-mm-dd"
                                        showIcon
                                        iconDisplay="input"
                                        inputClass="w-full"
                                        :placeholder="t('admin.select_start_date') || 'Select start date'"
                                    />
                                </div>

                                <!-- End Date -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ t('common.end_date') || 'End Date' }}
                                    </label>
                                    <Calendar
                                        v-model="form.end_date"
                                        dateFormat="yy-mm-dd"
                                        showIcon
                                        iconDisplay="input"
                                        inputClass="w-full"
                                        :minDate="form.start_date"
                                        :placeholder="t('admin.select_end_date') || 'Select end date'"
                                    />
                                </div>
                            </div>

                            <!-- Max Students -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('admin.max_students') || 'Maximum Students' }}
                                </label>
                                <InputNumber
                                    v-model="form.max_students"
                                    :min="1"
                                    class="w-full"
                                    :placeholder="t('admin.max_students_placeholder') || 'Enter maximum number of students'"
                                />
                                <small class="text-gray-500 mt-1">{{ t('admin.max_students_hint') || 'Leave empty for unlimited' }}</small>
                            </div>

                            <!-- Active Status -->
                            <div class="flex items-center gap-3">
                                <Checkbox
                                    v-model="form.is_active"
                                    inputId="is_active"
                                    :binary="true"
                                />
                                <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">
                                    {{ t('admin.active_batch') || 'Active Batch' }}
                                </label>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                            <Link :href="route('admin.courses.batches.index', course.slug || course.id)">
                                <Button 
                                    :label="t('common.cancel') || 'Cancel'" 
                                    severity="secondary"
                                    outlined
                                />
                            </Link>
                            <Button 
                                type="submit"
                                :label="t('common.create') || 'Create'" 
                                icon="pi pi-check"
                                :loading="form.processing"
                                class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700"
                            />
                        </div>
                    </form>
                </template>
            </Card>
        </div>
    </AdminLayout>
</template>

<script setup>
import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useAlert } from '@/composables/useAlert';

const props = defineProps({
    course: Object,
    instructors: Array,
});

const { t } = useTranslation();
const { route } = useRoute();
const { showSuccess, showError } = useAlert();
const page = usePage();

const form = useForm({
    name: '',
    name_ar: '',
    description: '',
    description_ar: '',
    instructor_id: null,
    start_date: null,
    end_date: null,
    max_students: null,
    is_active: true,
});

const getInstructorName = (instructorId) => {
    const instructor = props.instructors.find(i => i.id === instructorId);
    return instructor ? instructor.name : '';
};

const submit = () => {
    form.post(route('admin.courses.batches.store', props.course.slug || props.course.id), {
        onSuccess: () => {
            showSuccess(
                t('admin.batch_created_successfully') || page.props.flash?.success || 'Batch created successfully!',
                t('common.success') || 'Success'
            );
        },
        onError: (errors) => {
            if (errors.message) {
                showError(errors.message, t('common.error') || 'Error');
            }
        },
    });
};

// Watch for flash messages
watch(() => page.props.flash?.success, (success) => {
    if (success) {
        showSuccess(success, t('common.success') || 'Success');
    }
});
</script>

<style scoped>
:deep(.p-card) {
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

:deep(.p-inputtext),
:deep(.p-dropdown),
:deep(.p-calendar),
:deep(.p-inputnumber) {
    width: 100%;
}

:deep(.p-button) {
    font-weight: 500;
}
</style>

