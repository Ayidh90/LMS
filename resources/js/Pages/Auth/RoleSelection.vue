<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-indigo-600">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    {{ t('auth.select_role') || 'Select Your Role' }}
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    {{ t('auth.select_role_description') || 'You have multiple roles. Please select which role you want to use.' }}
                </p>
            </div>

            <div class="mt-8 space-y-4">
                <div
                    v-for="role in roles"
                    :key="role.id"
                    @click="selectRole(role.slug)"
                    class="relative flex items-center gap-4 p-6 bg-white rounded-xl shadow-sm border-2 cursor-pointer transition-all hover:border-blue-500 hover:shadow-md"
                    :class="selectedRole === role.slug ? 'border-blue-500 bg-blue-50' : 'border-gray-200'"
                >
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-full flex items-center justify-center" :class="getRoleColor(role.slug)">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-lg font-semibold text-gray-900">
                            {{ getDashboardName(role.slug) }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ getRoleName(role) }}
                        </p>
                    </div>
                    <div v-if="selectedRole === role.slug" class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button
                    @click="submit"
                    :disabled="!selectedRole || form.processing"
                    class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                >
                    <svg v-if="form.processing" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span v-else>
                        {{ t('auth.continue') || 'Continue' }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    roles: {
        type: Array,
        default: () => [],
    },
});

const { t, locale } = useTranslation();
const { route } = useRoute();

const selectedRole = ref(null);

const form = useForm({
    role_slug: '',
});

// Get role name based on locale
const getRoleName = (role) => {
    if (!role) return '';
    if (locale.value === 'ar' && role.name_ar) {
        return role.name_ar;
    }
    return role.name || role.slug || '';
};

// Get dashboard name for role
const getDashboardName = (roleSlug) => {
    if (!roleSlug) return '';
    
    // Map role slugs to dashboard translation keys
    const dashboardKeys = {
        'student': 'student.dashboard',
        'instructor': 'instructor.dashboard',
        'admin': 'admin.dashboard',
        'super_admin': 'admin.dashboard',
    };
    
    const key = dashboardKeys[roleSlug] || 'admin.dashboard';
    return t(key) || getRoleName({ slug: roleSlug });
};

// Get role color class
const getRoleColor = (slug) => {
    const colors = {
        'instructor': 'bg-gradient-to-br from-blue-500 to-blue-700',
        'student': 'bg-gradient-to-br from-emerald-500 to-emerald-700',
        'admin': 'bg-gradient-to-br from-red-500 to-red-700',
        'super_admin': 'bg-gradient-to-br from-purple-500 to-purple-700',
    };
    return colors[slug] || 'bg-gradient-to-br from-gray-500 to-gray-700';
};

const selectRole = (roleSlug) => {
    selectedRole.value = roleSlug;
    form.role_slug = roleSlug;
};

const submit = () => {
    if (!selectedRole.value) {
        return;
    }
    form.post(route('role-selection.store'));
};
</script>

