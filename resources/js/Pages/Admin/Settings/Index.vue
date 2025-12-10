<template>
    <AdminLayout :page-title="t('admin.settings') || 'Settings'">
        <Head :title="t('admin.settings') || 'Settings'" />
        
        <div class="settings-page space-y-6 pb-8">
            <!-- Page Header -->
            <div class="page-header bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ t('admin.settings') || 'Settings' }}</h1>
                        <p class="mt-1 text-sm text-gray-600">{{ t('admin.settings_description') || 'Manage system settings and instructor permissions' }}</p>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            <div v-if="page.props.flash?.success" class="success-message bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 flex items-center gap-3 shadow-sm animate-fade-in">
                <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-green-800 font-medium flex-1">{{ page.props.flash.success }}</p>
            </div>

            <!-- Website Settings Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">{{ t('admin.website_settings') || 'Website Settings' }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ t('admin.website_settings_description') || 'Manage website information, logo, and contact details' }}</p>
                </div>

                <form @submit.prevent="submitWebsiteSettings" class="p-6 space-y-6">
                    <!-- Website Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('admin.website_name') || 'Website Name' }}
                        </label>
                        <input
                            type="text"
                            v-model="websiteForm.website_name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            :placeholder="t('admin.website_name_placeholder') || 'Enter website name'"
                        />
                    </div>

                    <!-- Website Logo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('admin.website_logo') || 'Website Logo' }}
                        </label>
                        <div class="flex items-start gap-4">
                            <div v-if="websiteForm.website_logo_preview || currentLogo" class="flex-shrink-0">
                                <img
                                    :src="websiteForm.website_logo_preview || currentLogoUrl"
                                    alt="Logo"
                                    class="w-24 h-24 object-contain border border-gray-200 rounded-lg p-2 bg-gray-50"
                                />
                            </div>
                            <div class="flex-1">
                                <input
                                    type="file"
                                    @change="handleLogoChange"
                                    accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                />
                                <p class="mt-1 text-xs text-gray-500">{{ t('admin.logo_hint') || 'Recommended: PNG or JPG, max 2MB' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Website Favicon -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('admin.website_favicon') || 'Website Favicon' }}
                        </label>
                        <div class="flex items-start gap-4">
                            <div v-if="websiteForm.website_favicon_preview || currentFavicon" class="flex-shrink-0">
                                <img
                                    :src="websiteForm.website_favicon_preview || currentFaviconUrl"
                                    alt="Favicon"
                                    class="w-16 h-16 object-contain border border-gray-200 rounded-lg p-2 bg-gray-50"
                                />
                            </div>
                            <div class="flex-1">
                                <input
                                    type="file"
                                    @change="handleFaviconChange"
                                    accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                />
                                <p class="mt-1 text-xs text-gray-500">{{ t('admin.favicon_hint') || 'Recommended: ICO, PNG or SVG, max 512KB' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Website Info -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('admin.website_info') || 'Website Information' }}
                        </label>
                        <textarea
                            v-model="websiteForm.website_info"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            :placeholder="t('admin.website_info_placeholder') || 'Enter website description or information'"
                        ></textarea>
                    </div>

                    <!-- Website Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('admin.website_email') || 'Contact Email' }}
                        </label>
                        <input
                            type="email"
                            v-model="websiteForm.website_email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            :placeholder="t('admin.website_email_placeholder') || 'contact@example.com'"
                        />
                    </div>

                    <!-- Website Mobile -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('admin.website_mobile') || 'Contact Mobile' }}
                        </label>
                        <input
                            type="text"
                            v-model="websiteForm.website_mobile"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            :placeholder="t('admin.website_mobile_placeholder') || '+1234567890'"
                        />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button
                            type="submit"
                            :disabled="websiteForm.processing"
                            class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all disabled:opacity-50 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40"
                        >
                            <svg v-if="websiteForm.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ websiteForm.processing ? (t('common.saving') || 'Saving...') : (t('admin.save_website_settings') || t('common.save') || 'Save Website Settings') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Instructor Permissions Form -->
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

const websiteForm = useForm({
    website_name: computed(() => props.settings?.website_name?.value ?? ''),
    website_logo: null,
    website_favicon: null,
    website_info: computed(() => props.settings?.website_info?.value ?? ''),
    website_email: computed(() => props.settings?.website_email?.value ?? ''),
    website_mobile: computed(() => props.settings?.website_mobile?.value ?? ''),
    website_logo_preview: null,
    website_favicon_preview: null,
});

const currentLogo = computed(() => props.settings?.website_logo?.value);
const currentFavicon = computed(() => props.settings?.website_favicon?.value);

const currentLogoUrl = computed(() => {
    if (!currentLogo.value) return null;
    if (currentLogo.value.startsWith('http')) return currentLogo.value;
    return `/storage/${currentLogo.value}`;
});

const currentFaviconUrl = computed(() => {
    if (!currentFavicon.value) return null;
    if (currentFavicon.value.startsWith('http')) return currentFavicon.value;
    return `/storage/${currentFavicon.value}`;
});

const handleLogoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        websiteForm.website_logo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            websiteForm.website_logo_preview = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const handleFaviconChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        websiteForm.website_favicon = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            websiteForm.website_favicon_preview = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submitForm = () => {
    form.put(route('admin.settings.update'), {
        preserveScroll: true,
    });
};

const submitWebsiteSettings = () => {
    websiteForm.transform((data) => {
        const formData = { ...data };
        if (websiteForm.website_logo) {
            formData.website_logo = websiteForm.website_logo;
        }
        if (websiteForm.website_favicon) {
            formData.website_favicon = websiteForm.website_favicon;
        }
        return formData;
    }).post(route('admin.settings.update'), {
        preserveScroll: true,
        forceFormData: true,
        method: 'put',
    });
};
</script>

