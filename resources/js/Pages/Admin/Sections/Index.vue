<template>
    <AdminLayout :page-title="t('admin.sections_management')">
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link :href="route('admin.courses.edit', course.slug || course.id)" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ t('admin.sections_management') || 'Sections' }}</h1>
                        <p class="text-sm text-gray-500">{{ course.title }}</p>
                    </div>
                </div>
                <button
                    @click="showCreateModal = true"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 font-medium transition-all shadow-lg shadow-blue-500/25"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ t('admin.add_section') || 'Add Section' }}
                </button>
            </div>

            <!-- Sections List -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div v-if="sections.length > 0" class="divide-y divide-gray-100">
                    <div
                        v-for="(section, index) in sections"
                        :key="section.id"
                        class="p-6 hover:bg-gray-50/50 transition-colors"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-4 flex-1">
                                <!-- Order Badge -->
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0">
                                    {{ index + 1 }}
                                </div>
                                
                                <!-- Section Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ section.title }}</h3>
                                    <p v-if="section.description" class="text-sm text-gray-500 line-clamp-2 mb-3">{{ section.description }}</p>
                                    
                                    <!-- Lessons Count -->
                                    <div class="flex items-center gap-4 text-sm">
                                        <span class="inline-flex items-center gap-1.5 text-gray-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                            {{ section.lessons_count }} {{ t('common.lessons') || 'lessons' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                <button
                                    @click="editSection(section)"
                                    class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                    :title="t('common.edit')"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button
                                    @click="confirmDelete(section)"
                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    :title="t('common.delete')"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Lessons Preview -->
                        <div v-if="section.lessons && section.lessons.length > 0" class="mt-4 mr-14">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="space-y-2">
                                    <div
                                        v-for="lesson in section.lessons.slice(0, 3)"
                                        :key="lesson.id"
                                        class="flex items-center gap-3 text-sm"
                                    >
                                        <span class="w-6 h-6 bg-white rounded-lg flex items-center justify-center text-xs font-medium text-gray-500 shadow-sm">
                                            {{ lesson.order }}
                                        </span>
                                        <span class="text-gray-700">{{ lesson.title }}</span>
                                        <span class="px-2 py-0.5 bg-white rounded-full text-xs text-gray-500 capitalize">{{ t('lessons.types.' + lesson.type) || lesson.type }}</span>
                                    </div>
                                    <div v-if="section.lessons.length > 3" class="text-xs text-gray-500 pr-9">
                                        +{{ section.lessons.length - 3 }} {{ t('common.more') || 'more' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Empty State -->
                <div v-else class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ t('sections.no_sections') || 'No sections yet' }}</h3>
                    <p class="text-gray-500 mb-6">{{ t('sections.no_sections_hint') || 'Create your first section to organize lessons' }}</p>
                    <button
                        @click="showCreateModal = true"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium transition-all"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        {{ t('admin.add_section') || 'Add Section' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Teleport to="body">
            <div v-if="showCreateModal || showEditModal" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-screen items-center justify-center p-4">
                    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="closeModals"></div>
                    
                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 transform transition-all">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900">
                                {{ showEditModal ? t('admin.edit_section') || 'Edit Section' : t('admin.add_section') || 'Add Section' }}
                            </h3>
                            <button @click="closeModals" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <form @submit.prevent="submitSection" class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('sections.fields.title') || 'Title' }} (English) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="sectionForm.title"
                                    type="text"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    :class="{ 'border-red-500': sectionForm.errors.title }"
                                    :placeholder="t('sections.placeholders.title') || 'Enter section title'"
                                />
                                <p v-if="sectionForm.errors.title" class="mt-1 text-sm text-red-500">{{ sectionForm.errors.title }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('sections.fields.title') || 'Title' }} (العربية)
                                </label>
                                <input
                                    v-model="sectionForm.title_ar"
                                    type="text"
                                    dir="rtl"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    :placeholder="'أدخل عنوان القسم'"
                                />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('sections.fields.description') || 'Description' }} (English)
                                </label>
                                <textarea
                                    v-model="sectionForm.description"
                                    rows="3"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                    :placeholder="t('sections.placeholders.description') || 'Enter section description'"
                                ></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('sections.fields.description') || 'Description' }} (العربية)
                                </label>
                                <textarea
                                    v-model="sectionForm.description_ar"
                                    rows="3"
                                    dir="rtl"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                    :placeholder="'أدخل وصف القسم'"
                                ></textarea>
                            </div>
                            
                            <div class="flex items-center justify-end gap-3 pt-4">
                                <button
                                    type="button"
                                    @click="closeModals"
                                    class="px-5 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 font-medium transition-all"
                                >
                                    {{ t('common.cancel') }}
                                </button>
                                <button
                                    type="submit"
                                    :disabled="sectionForm.processing"
                                    class="px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium transition-all disabled:opacity-50"
                                >
                                    {{ showEditModal ? t('common.save_changes') : t('common.create') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    sections: Array,
});

const { t } = useTranslation();
const { route } = useRoute();

const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingSection = ref(null);

const sectionForm = useForm({
    title: '',
    title_ar: '',
    description: '',
    description_ar: '',
    order: null,
});

const editSection = (section) => {
    editingSection.value = section;
    sectionForm.title = section.title || '';
    sectionForm.title_ar = section.title_ar || '';
    sectionForm.description = section.description || '';
    sectionForm.description_ar = section.description_ar || '';
    sectionForm.order = section.order;
    showEditModal.value = true;
};

const closeModals = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    editingSection.value = null;
    sectionForm.reset();
};

const submitSection = () => {
    if (showEditModal.value && editingSection.value) {
        sectionForm.put(route('admin.courses.sections.update', [props.course.slug || props.course.id, editingSection.value.id]), {
            onSuccess: () => closeModals(),
        });
    } else {
        sectionForm.post(route('admin.courses.sections.store', props.course.slug || props.course.id), {
            onSuccess: () => closeModals(),
        });
    }
};

const confirmDelete = (section) => {
    if (confirm(t('sections.confirm_delete') || 'Are you sure you want to delete this section?')) {
        router.delete(route('admin.courses.sections.destroy', [props.course.slug || props.course.id, section.id]));
    }
};
</script>

