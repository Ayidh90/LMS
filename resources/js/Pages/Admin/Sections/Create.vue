<template>
    <AdminLayout :page-title="t('admin.create_section') || 'Create Section'">
        <Head :title="t('admin.create_section') || 'Create Section'" />
        
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
                        <Link :href="route('admin.courses.sections.index', course.slug || course.id)" class="hover:text-white transition-colors font-medium">{{ t('admin.sections') || 'Sections' }}</Link>
                        <span>›</span>
                        <span class="text-white font-semibold">{{ t('admin.create_section') || 'Create Section' }}</span>
                    </div>
                    <h1 class="text-4xl font-bold mb-3">{{ t('admin.create_section') || 'Create Section' }}</h1>
                    <p class="text-blue-100 text-base">{{ t('admin.create_section_description') || 'Create a new section for this course' }}</p>
                </div>
            </div>

            <!-- Form Card -->
            <Card>
                <template #content>
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Basic Information -->
                        <div class="space-y-6">
                            <h2 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-3">{{ t('admin.basic_information') || 'Basic Information' }}</h2>
                            
                            <!-- Title (English) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('sections.fields.title') || 'Title' }} (English) <span class="text-red-500">*</span>
                                </label>
                                <InputText
                                    v-model="form.title"
                                    :class="{ 'p-invalid': form.errors.title }"
                                    class="w-full"
                                    :placeholder="t('sections.placeholders.title') || 'Enter section title'"
                                />
                                <small v-if="form.errors.title" class="p-error">{{ form.errors.title }}</small>
                            </div>

                            <!-- Title (Arabic) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('sections.fields.title') || 'Title' }} (العربية)
                                </label>
                                <InputText
                                    v-model="form.title_ar"
                                    dir="rtl"
                                    class="w-full"
                                    :placeholder="'أدخل عنوان القسم'"
                                />
                            </div>

                            <!-- Description (English) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('sections.fields.description') || 'Description' }} (English)
                                </label>
                                <Textarea
                                    v-model="form.description"
                                    rows="4"
                                    class="w-full"
                                    :placeholder="t('sections.placeholders.description') || 'Enter section description'"
                                />
                            </div>

                            <!-- Description (Arabic) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('sections.fields.description') || 'Description' }} (العربية)
                                </label>
                                <Textarea
                                    v-model="form.description_ar"
                                    rows="4"
                                    dir="rtl"
                                    class="w-full"
                                    :placeholder="'أدخل وصف القسم'"
                                />
                            </div>

                            <!-- Order -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('sections.fields.order') || 'Order' }}
                                </label>
                                <InputNumber
                                    v-model="form.order"
                                    :min="1"
                                    class="w-full"
                                    :placeholder="t('sections.placeholders.order') || 'Enter section order (optional)'"
                                />
                                <small class="text-gray-500 mt-1">{{ t('sections.order_hint') || 'Leave empty to add at the end' }}</small>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                            <Link :href="route('admin.courses.sections.index', course.slug || course.id)">
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
});

const { t } = useTranslation();
const { route } = useRoute();
const { showSuccess, showError } = useAlert();
const page = usePage();

const form = useForm({
    title: '',
    title_ar: '',
    description: '',
    description_ar: '',
    order: null,
});

const submit = () => {
    form.post(route('admin.courses.sections.store', props.course.slug || props.course.id), {
        onSuccess: () => {
            showSuccess(
                t('sections.created_successfully') || page.props.flash?.success || 'Section created successfully!',
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
:deep(.p-inputnumber) {
    width: 100%;
}

:deep(.p-button) {
    font-weight: 500;
}
</style>

