<template>
    <AdminLayout :page-title="t('admin.settings') || 'Settings'">
        <Head :title="t('admin.settings') || 'Settings'" />
        
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ t('admin.settings') || 'Settings' }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ t('admin.settings_description') || 'Manage system settings and instructor permissions' }}</p>
                </div>
            </div>

            <!-- Success Message -->
            <div v-if="page.props.flash?.success" class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-green-800 font-medium">{{ page.props.flash.success }}</p>
            </div>

            <!-- Settings Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">{{ t('admin.instructor_permissions') || 'Instructor Permissions' }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ t('admin.instructor_permissions_description') || 'Control what instructors can create and manage' }}</p>
                </div>

                <form @submit.prevent="submitForm" class="p-6 space-y-6">
                    <!-- Instructor Can Create Sections -->
                    <div class="flex items-start justify-between p-4 border border-gray-200 rounded-xl hover:border-gray-300 transition-colors">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">{{ t('admin.allow_create_sections') || 'Allow Creating Sections/Chapters' }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ t('admin.allow_create_sections_description') || 'Instructors can create and manage course sections/chapters' }}</p>
                                </div>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer ml-4">
                            <input
                                type="checkbox"
                                v-model="form.instructor_can_create_sections"
                                class="sr-only peer"
                            />
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Instructor Can Create Lessons -->
                    <div class="flex items-start justify-between p-4 border border-gray-200 rounded-xl hover:border-gray-300 transition-colors">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">{{ t('admin.allow_create_lessons') || 'Allow Creating Lessons' }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ t('admin.allow_create_lessons_description') || 'Instructors can create and manage course lessons' }}</p>
                                </div>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer ml-4">
                            <input
                                type="checkbox"
                                v-model="form.instructor_can_create_lessons"
                                class="sr-only peer"
                            />
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Instructor Can Create Questions -->
                    <div class="flex items-start justify-between p-4 border border-gray-200 rounded-xl hover:border-gray-300 transition-colors">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">{{ t('admin.allow_create_questions') || 'Allow Creating Questions' }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ t('admin.allow_create_questions_description') || 'Instructors can create and manage lesson questions' }}</p>
                                </div>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer ml-4">
                            <input
                                type="checkbox"
                                v-model="form.instructor_can_create_questions"
                                class="sr-only peer"
                            />
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all disabled:opacity-50 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40"
                        >
                            <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? (t('common.saving') || 'Saving...') : (t('common.save') || 'Save Settings') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useForm, usePage, Head } from '@inertiajs/vue3';

const props = defineProps({
    settings: Object,
});

const { t } = useTranslation();
const { route } = useRoute();
const page = usePage();

const form = useForm({
    instructor_can_create_sections: computed(() => props.settings?.instructor_can_create_sections?.value ?? true),
    instructor_can_create_lessons: computed(() => props.settings?.instructor_can_create_lessons?.value ?? true),
    instructor_can_create_questions: computed(() => props.settings?.instructor_can_create_questions?.value ?? true),
});

const submitForm = () => {
    form.put(route('admin.settings.update'), {
        preserveScroll: true,
    });
};
</script>

