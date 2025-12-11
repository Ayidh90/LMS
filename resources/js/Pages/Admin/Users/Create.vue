<template>
    <AdminLayout :page-title="t('users.create') || 'Create User'">
        <Head :title="t('users.create') || 'Create User'" />
        <div class="max-w-3xl mx-auto space-y-6">
            <!-- Page Header -->
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.users.index')"
                    class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ t('users.create') }}</h1>
                    <p class="text-sm text-gray-500">{{ t('users.create_description') || 'Add a new user to the system' }}</p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Name Field -->
                        <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('users.fields.name') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full pr-10 pl-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                :class="{ 'border-red-500 focus:ring-red-500': form.errors.name }"
                                :placeholder="t('users.name_placeholder') || 'Enter user name'"
                            />
                        </div>
                        <p v-if="form.errors.name" class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ form.errors.name }}
                        </p>
                        </div>

                    <!-- Email Field -->
                        <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('users.fields.email') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full pr-10 pl-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                :class="{ 'border-red-500 focus:ring-red-500': form.errors.email }"
                                :placeholder="t('users.email_placeholder') || 'user@example.com'"
                            />
                        </div>
                        <p v-if="form.errors.email" class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ form.errors.email }}
                        </p>
                        </div>

                    <!-- National ID Field -->
                        <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('users.fields.national_id') }} <span class="text-gray-400 text-xs">({{ t('common.optional') }})</span>
                        </label>
                        <div class="relative">
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                            <input
                                v-model="form.national_id"
                                type="text"
                                class="w-full pr-10 pl-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                :class="{ 'border-red-500 focus:ring-red-500': form.errors.national_id }"
                                :placeholder="t('users.fields.national_id_placeholder') || 'National ID (Optional)'"
                            />
                        </div>
                        <p v-if="form.errors.national_id" class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ form.errors.national_id }}
                        </p>
                        </div>

                    <!-- Password Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('users.fields.password') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            <input
                                v-model="form.password"
                                type="password"
                                    class="w-full pr-10 pl-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    :class="{ 'border-red-500 focus:ring-red-500': form.errors.password }"
                                    placeholder="••••••••"
                            />
                            </div>
                            <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">{{ form.errors.password }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('users.fields.password_confirmation') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            <input
                                v-model="form.password_confirmation"
                                type="password"
                                    class="w-full pr-10 pl-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    placeholder="••••••••"
                            />
                            </div>
                        </div>
                        </div>

                    <!-- Role Selection -->
                        <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('users.fields.role') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label
                                v-for="role in roles"
                                :key="role.id"
                                class="relative flex items-center gap-3 p-4 border rounded-xl cursor-pointer transition-all"
                                :class="form.role_id === role.id ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-500' : 'border-gray-200 hover:border-gray-300'"
                            >
                                <input
                                    v-model="form.role_id"
                                    type="radio"
                                    :value="role.id"
                                    class="sr-only"
                                />
                                <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="getRoleColorClass(role.slug)">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900 text-sm">{{ getRoleName(role) }}</p>
                                    <p v-if="role.description || role.description_ar" class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ getRoleDescription(role) }}</p>
                                </div>
                                <div v-if="form.role_id === role.id" class="absolute top-2 left-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </label>
                        </div>
                        <p v-if="form.errors.role_id || form.errors.role" class="mt-2 text-sm text-red-600">{{ form.errors.role_id || form.errors.role }}</p>
                        </div>

                    <!-- Admin Checkbox -->
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input v-model="form.is_admin" type="checkbox" class="sr-only peer" />
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                        <div>
                            <p class="font-medium text-gray-900">{{ t('users.fields.is_admin') }}</p>
                            <p class="text-sm text-gray-500">{{ t('users.is_admin_description') || 'Grant admin access to dashboard' }}</p>
                        </div>
                        </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                            <Link
                                :href="route('admin.users.index')"
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors"
                            >
                                {{ t('common.cancel') }}
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-blue-500/25"
                        >
                            <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                                {{ form.processing ? t('common.saving') : t('common.save') }}
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
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    roles: {
        type: Array,
        default: () => [],
    },
});

const { t, locale } = useTranslation();
const { route } = useRoute();
const page = usePage();

const form = useForm({
    name: '',
    email: '',
    national_id: '',
    password: '',
    password_confirmation: '',
    role_id: null,
    role: '', // Keep for backward compatibility
    is_admin: false,
    is_active: true,
});

const roles = computed(() => props.roles || []);

// Get role name based on locale
const getRoleName = (role) => {
    if (!role) return '';
    if (locale.value === 'ar' && role.name_ar) {
        return role.name_ar;
    }
    return role.name || '';
};

// Get role description based on locale
const getRoleDescription = (role) => {
    if (!role) return '';
    if (locale.value === 'ar' && role.description_ar) {
        return role.description_ar;
    }
    return role.description || '';
};

// Get role color class based on slug
const getRoleColorClass = (slug) => {
    const colors = {
        'super_admin': 'bg-gradient-to-br from-purple-500 to-purple-700',
        'super-admin': 'bg-gradient-to-br from-purple-500 to-purple-700',
        'admin': 'bg-gradient-to-br from-red-500 to-red-700',
        'instructor': 'bg-gradient-to-br from-blue-500 to-blue-700',
        'student': 'bg-gradient-to-br from-emerald-500 to-emerald-700',
    };
    return colors[slug] || 'bg-gradient-to-br from-gray-500 to-gray-700';
};

const submit = () => {
    // Set role name/slug for backward compatibility
    if (form.role_id) {
        const selectedRole = roles.value.find(r => r.id === form.role_id);
        if (selectedRole) {
            form.role = selectedRole.slug || selectedRole.name;
        }
    }
    form.post(route('admin.users.store'));
};
</script>
