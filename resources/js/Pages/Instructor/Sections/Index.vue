<template>
    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50" :dir="direction">
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <nav class="text-sm text-gray-500 mb-2">
                                <Link :href="route('courses.show', course.slug || course.id)" class="hover:text-blue-600">
                                    {{ course.translated_title || course.title }}
                                </Link>
                                <span class="mx-2">›</span>
                                <span class="text-gray-900">{{ t('sections.title') || 'Sections' }}</span>
                            </nav>
                            <h1 class="text-3xl font-bold text-gray-900">{{ t('sections.title') || 'Course Sections' }}</h1>
                        </div>
                        <button
                            v-if="canCreateSections && can('sections.create')"
                            @click="showSectionModal = true"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ t('sections.actions.add') || 'Add Section' }}
                        </button>
                    </div>

                    <!-- Success Alert -->
                    <div v-if="page.props.flash?.success" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-green-800">{{ page.props.flash.success }}</p>
                    </div>

                    <!-- Sections List -->
                    <div v-if="sections && sections.length > 0" class="space-y-4">
                        <div
                            v-for="section in sections"
                            :key="section.id"
                            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
                        >
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                                                {{ t('sections.section') || 'Section' }} {{ section.order }}
                                            </span>
                                        </div>
                                        <h3 class="text-xl font-semibold text-gray-900 mb-1">
                                            {{ section.translated_title || section.title }}
                                        </h3>
                                        <p v-if="section.translated_description || section.description" class="text-gray-600 text-sm">
                                            {{ section.translated_description || section.description }}
                                        </p>
                                    </div>
                                    <div class="flex gap-2">
                                        <button
                                            v-if="can('sections.edit')"
                                            @click="editSection(section)"
                                            class="px-3 py-1 text-blue-600 hover:bg-blue-50 rounded text-sm font-medium transition-colors"
                                        >
                                            {{ t('common.edit') }}
                                        </button>
                                        <button
                                            v-if="can('sections.delete')"
                                            @click="deleteSection(section.id)"
                                            class="px-3 py-1 text-red-600 hover:bg-red-50 rounded text-sm font-medium transition-colors"
                                        >
                                            {{ t('common.delete') }}
                                        </button>
                                    </div>
                                </div>

                                <!-- Lessons in Section -->
                                <div v-if="section.lessons && section.lessons.length > 0" class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-semibold text-gray-700">
                                            {{ t('lessons.title') }} ({{ section.lessons.length }})
                                        </h4>
                                        <Link
                                            v-if="canCreateLessons && can('lessons.create')"
                                            :href="route('instructor.lessons.create', course.slug || course.id)"
                                            class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                                        >
                                            + {{ t('lessons.actions.add') }}
                                        </Link>
                                    </div>
                                    <div class="space-y-2">
                                        <div
                                            v-for="lesson in section.lessons"
                                            :key="lesson.id"
                                            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                                        >
                                            <div class="flex items-center gap-3 flex-1">
                                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                                    {{ lesson.order }}
                                                </div>
                                                <div class="flex-1">
                                                    <h5 class="font-medium text-gray-900">{{ lesson.translated_title || lesson.title }}</h5>
                                                    <p class="text-xs text-gray-500">
                                                        {{ lesson.duration_minutes }} {{ t('lessons.fields.duration_minutes') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <Link
                                                :href="route('instructor.lessons.show', [course.slug || course.id, lesson.id])"
                                                class="px-3 py-1 text-blue-600 hover:bg-blue-50 rounded text-sm font-medium transition-colors"
                                            >
                                                {{ t('common.view') }}
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="mt-4 pt-4 border-t border-gray-200 text-center py-4">
                                    <p class="text-sm text-gray-500 mb-3">{{ t('sections.no_lessons') || 'No lessons in this section' }}</p>
                                    <Link
                                        v-if="canCreateLessons && can('lessons.create')"
                                        :href="route('instructor.lessons.create', course.slug || course.id)"
                                        class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors"
                                    >
                                        {{ t('lessons.actions.add_first') }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-12 bg-white rounded-xl">
                        <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <p class="text-gray-500 text-lg mb-4">{{ t('sections.no_sections') || 'No sections yet' }}</p>
                        <button
                            v-if="canCreateSections && can('sections.create')"
                            @click="showSectionModal = true"
                            class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors"
                        >
                            {{ t('sections.actions.add_first') || 'Add First Section' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Add/Edit Section Modal -->
            <div v-if="showSectionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeModal">
                <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full" :dir="direction">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-gray-900">
                                {{ editingSection ? (t('sections.actions.edit') || 'Edit Section') : (t('sections.actions.add') || 'Add Section') }}
                            </h3>
                            <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <form @submit.prevent="saveSection" class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('sections.fields.title') || 'Title' }} *
                                </label>
                                <input
                                    v-model="sectionForm.title"
                                    type="text"
                                    required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('sections.fields.title_ar') || 'Title (Arabic)' }}
                                </label>
                                <input
                                    v-model="sectionForm.title_ar"
                                    type="text"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('sections.fields.description') || 'Description' }}
                                </label>
                                <textarea
                                    v-model="sectionForm.description"
                                    rows="3"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                ></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('sections.fields.description_ar') || 'Description (Arabic)' }}
                                </label>
                                <textarea
                                    v-model="sectionForm.description_ar"
                                    rows="3"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                ></textarea>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                            <button
                                type="button"
                                @click="closeModal"
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                            >
                                {{ t('common.cancel') }}
                            </button>
                            <button
                                type="submit"
                                :disabled="isSubmitting"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                {{ editingSection ? (t('common.update') || 'Update') : (t('common.create') || 'Create') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useDirection } from '@/composables/useDirection';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useAlert } from '@/composables/useAlert';
import { usePermissions } from '@/composables/usePermissions';
import { Link, usePage, router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    sections: Array,
});

const { direction } = useDirection();
const { t } = useTranslation();
const { route } = useRoute();
const { showSuccess, showError, showConfirm } = useAlert();
const { can } = usePermissions();
const page = usePage();

const canCreateSections = computed(() => {
    return page.props.settings?.instructor_permissions?.can_create_sections ?? true;
});

const canCreateLessons = computed(() => {
    return page.props.settings?.instructor_permissions?.can_create_lessons ?? true;
});

const showSectionModal = ref(false);
const editingSection = ref(null);
const isSubmitting = ref(false);

const sectionForm = reactive({
    title: '',
    title_ar: '',
    description: '',
    description_ar: '',
});

const closeModal = () => {
    showSectionModal.value = false;
    editingSection.value = null;
    sectionForm.title = '';
    sectionForm.title_ar = '';
    sectionForm.description = '';
    sectionForm.description_ar = '';
};

const editSection = (section) => {
    editingSection.value = section;
    sectionForm.title = section.title || '';
    sectionForm.title_ar = section.title_ar || '';
    sectionForm.description = section.description || '';
    sectionForm.description_ar = section.description_ar || '';
    showSectionModal.value = true;
};

const saveSection = () => {
    isSubmitting.value = true;
    
    const url = editingSection.value
        ? route('instructor.sections.update', { course: props.course.slug || props.course.id, section: editingSection.value.id })
        : route('instructor.sections.store', { course: props.course.slug || props.course.id });
    
    const method = editingSection.value ? 'put' : 'post';

    router[method](url, sectionForm, {
        preserveScroll: true,
        onSuccess: () => {
            showSuccess(
                editingSection.value
                    ? (t('sections.updated_successfully') || 'Section updated successfully!')
                    : (t('sections.created_successfully') || 'Section created successfully!'),
                t('common.success') || 'Success'
            );
            closeModal();
        },
        onError: (errors) => {
            showError(
                errors.message || (t('common.error') || 'An error occurred'),
                t('common.error') || 'Error'
            );
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
};

const deleteSection = async (sectionId) => {
    const result = await showConfirm(
        t('sections.confirm_delete') || 'Are you sure you want to delete this section? All lessons in this section will be moved to the course root.',
        t('common.delete') || 'Delete'
    );

    if (result.isConfirmed) {
        router.delete(route('instructor.sections.destroy', {
            course: props.course.slug || props.course.id,
            section: sectionId,
        }), {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess(
                    t('sections.deleted_successfully') || 'Section deleted successfully!',
                    t('common.success') || 'Success'
                );
            },
            onError: () => {
                showError(
                    t('common.error') || 'Failed to delete section',
                    t('common.error') || 'Error'
                );
            },
        });
    }
};
</script>

