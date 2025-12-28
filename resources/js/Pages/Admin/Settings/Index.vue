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

            <!-- Error Messages -->
            <div v-if="websiteForm.hasErrors || form.hasErrors" class="error-message bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 rounded-xl p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mt-0.5">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-red-800 font-semibold mb-2">{{ t('common.error') || 'Please fix the following errors:' }}</h3>
                        <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
                            <li v-for="(error, field) in websiteForm.errors" :key="`website-${field}`">
                                {{ Array.isArray(error) ? error[0] : error }}
                            </li>
                            <li v-for="(error, field) in form.errors" :key="`form-${field}`">
                                {{ Array.isArray(error) ? error[0] : error }}
                            </li>
                        </ul>
                    </div>
                </div>
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
                            :class="[
                                'w-full px-4 py-2.5 border rounded-lg transition-colors focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                                websiteForm.errors.website_name ? 'border-red-300 bg-red-50' : 'border-gray-300 bg-white'
                            ]"
                            :placeholder="t('admin.website_name_placeholder') || 'Enter website name'"
                        />
                        <p v-if="websiteForm.errors.website_name" class="mt-1 text-sm text-red-600">
                            {{ Array.isArray(websiteForm.errors.website_name) ? websiteForm.errors.website_name[0] : websiteForm.errors.website_name }}
                        </p>
                    </div>

                    <!-- Website Logo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('admin.website_logo') || 'Website Logo' }}
                        </label>
                        <div class="flex items-start gap-4">
                            <div v-if="websiteForm.website_logo_preview || currentLogoUrl" class="flex-shrink-0">
                                <img
                                    :key="`logo-${websiteForm.website_logo_preview || currentLogoUrl}`"
                                    :src="websiteForm.website_logo_preview || currentLogoUrl"
                                    alt="Logo"
                                    class="w-24 h-24 object-contain border border-gray-200 rounded-lg p-2 bg-gray-50"
                                    @error="handleImageError"
                                    @load="handleImageLoad"
                                />
                            </div>
                            <div v-else class="flex-shrink-0 w-24 h-24 border border-gray-200 rounded-lg p-2 bg-gray-50 flex items-center justify-center">
                                <span class="text-xs text-gray-400">{{ t('admin.no_image') || 'No image' }}</span>
                            </div>
                            <div class="flex-1">
                                <input
                                    ref="logoInput"
                                    type="file"
                                    @change="handleLogoChange"
                                    accept="image/*"
                                    :class="[
                                        'block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors',
                                        websiteForm.errors.website_logo ? 'file:bg-red-50 file:text-red-700' : ''
                                    ]"
                                />
                                <p v-if="websiteForm.errors.website_logo" class="mt-1 text-xs text-red-600">
                                    {{ Array.isArray(websiteForm.errors.website_logo) ? websiteForm.errors.website_logo[0] : websiteForm.errors.website_logo }}
                                </p>
                                <p v-else class="mt-1 text-xs text-gray-500">{{ t('admin.logo_hint') || 'Recommended: PNG or JPG, max 2MB' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Website Favicon -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('admin.website_favicon') || 'Website Favicon' }}
                        </label>
                        <div class="flex items-start gap-4">
                            <div v-if="websiteForm.website_favicon_preview || currentFaviconUrl" class="flex-shrink-0">
                                <img
                                    :key="`favicon-${websiteForm.website_favicon_preview || currentFaviconUrl}`"
                                    :src="websiteForm.website_favicon_preview || currentFaviconUrl"
                                    alt="Favicon"
                                    class="w-16 h-16 object-contain border border-gray-200 rounded-lg p-2 bg-gray-50"
                                    @error="handleImageError"
                                    @load="handleImageLoad"
                                />
                            </div>
                            <div v-else class="flex-shrink-0 w-16 h-16 border border-gray-200 rounded-lg p-2 bg-gray-50 flex items-center justify-center">
                                <span class="text-xs text-gray-400">{{ t('admin.no_image') || 'No image' }}</span>
                            </div>
                            <div class="flex-1">
                                <input
                                    ref="faviconInput"
                                    type="file"
                                    @change="handleFaviconChange"
                                    accept="image/*"
                                    :class="[
                                        'block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors',
                                        websiteForm.errors.website_favicon ? 'file:bg-red-50 file:text-red-700' : ''
                                    ]"
                                />
                                <p v-if="websiteForm.errors.website_favicon" class="mt-1 text-xs text-red-600">
                                    {{ Array.isArray(websiteForm.errors.website_favicon) ? websiteForm.errors.website_favicon[0] : websiteForm.errors.website_favicon }}
                                </p>
                                <p v-else class="mt-1 text-xs text-gray-500">{{ t('admin.favicon_hint') || 'Recommended: ICO, PNG or SVG, max 512KB' }}</p>
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
                            :class="[
                                'w-full px-4 py-2.5 border rounded-lg transition-colors focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-y',
                                websiteForm.errors.website_info ? 'border-red-300 bg-red-50' : 'border-gray-300 bg-white'
                            ]"
                            :placeholder="t('admin.website_info_placeholder') || 'Enter website description or information'"
                        ></textarea>
                        <p v-if="websiteForm.errors.website_info" class="mt-1 text-sm text-red-600">
                            {{ Array.isArray(websiteForm.errors.website_info) ? websiteForm.errors.website_info[0] : websiteForm.errors.website_info }}
                        </p>
                    </div>

                    <!-- Website Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('admin.website_email') || 'Contact Email' }}
                        </label>
                        <input
                            type="email"
                            v-model="websiteForm.website_email"
                            :class="[
                                'w-full px-4 py-2.5 border rounded-lg transition-colors focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                                websiteForm.errors.website_email ? 'border-red-300 bg-red-50' : 'border-gray-300 bg-white'
                            ]"
                            :placeholder="t('admin.website_email_placeholder') || 'contact@example.com'"
                        />
                        <p v-if="websiteForm.errors.website_email" class="mt-1 text-sm text-red-600">
                            {{ Array.isArray(websiteForm.errors.website_email) ? websiteForm.errors.website_email[0] : websiteForm.errors.website_email }}
                        </p>
                    </div>

                    <!-- Website Mobile -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('admin.website_mobile') || 'Contact Mobile' }}
                        </label>
                        <input
                            type="text"
                            v-model="websiteForm.website_mobile"
                            :class="[
                                'w-full px-4 py-2.5 border rounded-lg transition-colors focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                                websiteForm.errors.website_mobile ? 'border-red-300 bg-red-50' : 'border-gray-300 bg-white'
                            ]"
                            :placeholder="t('admin.website_mobile_placeholder') || '+1234567890'"
                        />
                        <p v-if="websiteForm.errors.website_mobile" class="mt-1 text-sm text-red-600">
                            {{ Array.isArray(websiteForm.errors.website_mobile) ? websiteForm.errors.website_mobile[0] : websiteForm.errors.website_mobile }}
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button
                            type="submit"
                            :disabled="websiteForm.processing"
                            class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40"
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
                            class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40"
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
import { computed, ref, onMounted } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useForm, usePage, Head } from '@inertiajs/vue3';

const logoInput = ref(null);
const faviconInput = ref(null);

const props = defineProps({
    settings: Object,
});

const { t } = useTranslation();
const { route } = useRoute();
const page = usePage();

const form = useForm({
    instructor_can_create_sections: props.settings?.instructor_can_create_sections?.value ?? true,
    instructor_can_create_lessons: props.settings?.instructor_can_create_lessons?.value ?? true,
    instructor_can_create_questions: props.settings?.instructor_can_create_questions?.value ?? true,
});

const websiteForm = useForm({
    website_name: props.settings?.website_name?.value ?? '',
    website_logo: null,
    website_favicon: null,
    website_info: props.settings?.website_info?.value ?? '',
    website_email: props.settings?.website_email?.value ?? '',
    website_mobile: props.settings?.website_mobile?.value ?? '',
    website_logo_preview: null,
    website_favicon_preview: null,
});

// Get logo and favicon URLs from shared settings (which have full URLs) or build from props
const currentLogoUrl = computed(() => {
    // First try shared settings (has full URL from middleware)
    const sharedLogo = page.props.settings?.website?.logo || page.props.settings?.website?.logo_url;
    if (sharedLogo && sharedLogo !== '' && sharedLogo !== null) {
        return sharedLogo;
    }
    // Second try: URL from props (now included in getAll() response)
    const logoUrl = props.settings?.website_logo?.url;
    if (logoUrl && logoUrl !== '' && logoUrl !== null) {
        return logoUrl;
    }
    // Fallback: build URL from storage path
    const logo = props.settings?.website_logo?.value;
    if (!logo || logo === '' || logo === null || logo === undefined) {
        return null;
    }
    // If it's already a full URL, return it
    if (typeof logo === 'string' && (logo.startsWith('http://') || logo.startsWith('https://'))) {
        return logo;
    }
    // Build storage URL
    let cleanPath = logo.startsWith('/') ? logo.substring(1) : logo;
    cleanPath = cleanPath.replace(/^storage\//, '');
    return `/storage/${cleanPath}`;
});

const currentFaviconUrl = computed(() => {
    // First try shared settings (has full URL from middleware)
    const sharedFavicon = page.props.settings?.website?.favicon || page.props.settings?.website?.favicon_url;
    if (sharedFavicon && sharedFavicon !== '' && sharedFavicon !== null) {
        return sharedFavicon;
    }
    // Second try: URL from props (now included in getAll() response)
    const faviconUrl = props.settings?.website_favicon?.url;
    if (faviconUrl && faviconUrl !== '' && faviconUrl !== null) {
        return faviconUrl;
    }
    // Fallback: build URL from storage path
    const favicon = props.settings?.website_favicon?.value;
    if (!favicon || favicon === '' || favicon === null || favicon === undefined) {
        return null;
    }
    // If it's already a full URL, return it
    if (typeof favicon === 'string' && (favicon.startsWith('http://') || favicon.startsWith('https://'))) {
        return favicon;
    }
    // Build storage URL
    let cleanPath = favicon.startsWith('/') ? favicon.substring(1) : favicon;
    cleanPath = cleanPath.replace(/^storage\//, '');
    return `/storage/${cleanPath}`;
});

const currentLogo = computed(() => {
    return page.props.settings?.website?.logo || props.settings?.website_logo?.value;
});

const currentFavicon = computed(() => {
    return page.props.settings?.website?.favicon || props.settings?.website_favicon?.value;
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

const handleImageError = (event) => {
    console.error('Image failed to load from storage:', event.target.src);
    // Hide the broken image instead of showing placeholder
    event.target.style.display = 'none';
    // Show error message in console for debugging
    console.error('Failed to load image. Check if file exists at:', event.target.src);
    event.target.onerror = null; // Prevent infinite loop
};

const handleImageLoad = (event) => {
    // Image loaded successfully
    console.log('Image loaded successfully:', event.target.src);
};

// Debug: Log image URLs on mount and watch for changes
onMounted(() => {
    console.log('=== Settings Page Debug ===');
    console.log('Shared settings (website):', page.props.settings?.website);
    console.log('Props settings:', props.settings);
    console.log('Current logo URL:', currentLogoUrl.value);
    console.log('Current favicon URL:', currentFaviconUrl.value);
    console.log('Logo from props:', props.settings?.website_logo?.value);
    console.log('Favicon from props:', props.settings?.website_favicon?.value);
    console.log('==========================');
});

const submitForm = () => {
    form.put(route('admin.settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Form will reload automatically
        },
    });
};

const submitWebsiteSettings = () => {
    console.log('Submitting website settings...');
    console.log('Form data:', {
        website_name: websiteForm.website_name,
        has_logo: !!websiteForm.website_logo,
        has_favicon: !!websiteForm.website_favicon,
    });
    
    websiteForm.transform((data) => {
        // Exclude preview fields from submission
        const { website_logo_preview, website_favicon_preview, ...formData } = data;
        return formData;
    }).post(route('admin.settings.update'), {
        preserveScroll: false, // Don't preserve scroll to ensure page reloads
        forceFormData: true,
        _method: 'put',
        onSuccess: (page) => {
            console.log('Settings saved successfully!');
            console.log('Updated settings:', page.props.settings);
            // Clear previews and file inputs after successful save
            websiteForm.website_logo_preview = null;
            websiteForm.website_favicon_preview = null;
            websiteForm.website_logo = null;
            websiteForm.website_favicon = null;
            // Reset file input elements
            if (logoInput.value) logoInput.value.value = '';
            if (faviconInput.value) faviconInput.value.value = '';
        },
        onError: (errors) => {
            console.error('Settings save failed:', errors);
        },
    });
};
</script>

