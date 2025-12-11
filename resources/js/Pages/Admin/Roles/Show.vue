<template>
    <AdminLayout :page-title="role?.name || t('roles.title') || 'Role Details'">
        <Head :title="role?.name || t('roles.title') || 'Role Details'" />
        
        <div class="space-y-6 min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 pb-8" :dir="direction">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-2xl p-6 shadow-xl">
                    <div class="text-white">
                    <div class="flex items-center gap-2 text-sm text-blue-100 mb-2">
                        <Link :href="route('admin.roles.index')" class="hover:text-white transition-colors">{{ t('roles.title') }}</Link>
                        <span>›</span>
                        <span class="text-white">{{ getRoleName(role) }}</span>
                    </div>
                    <h1 class="text-3xl font-bold mb-2">{{ getRoleName(role) }}</h1>
                    <p class="text-blue-100 text-sm">{{ t('roles.view_description') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <Link 
                        :href="route('admin.roles.edit', role?.id)"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-gray-700 rounded-xl hover:bg-gray-50 font-medium transition-all duration-200 shadow-lg hover:shadow-xl border border-white/20"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {{ t('common.edit') }}
                    </Link>
                </div>
            </div>

            <!-- Success Message -->
            <div v-if="page.props.flash?.success" class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-green-800 font-medium">{{ page.props.flash.success }}</p>
            </div>

            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Role Details -->
                        <Card>
                            <template #header>
                                <div class="p-6 pb-0">
                                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ t('roles.basic_info') }}
                                    </h2>
                                </div>
                            </template>
                            <template #content>
                                <div class="p-6 pt-4 space-y-6">
                                    <div class="pb-4 border-b border-gray-100">
                                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">{{ t('roles.fields.name') }}</label>
                                        <p class="text-gray-900 font-semibold text-xl">{{ getRoleName(role) }}</p>
                                        <p v-if="locale === 'ar' && role?.name" class="mt-1 text-sm text-gray-400 italic">({{ role.name }})</p>
                                        <p v-else-if="locale === 'en' && role?.name_ar" class="mt-1 text-sm text-gray-400 italic">({{ role.name_ar }})</p>
                                    </div>

                                    <div class="pb-4 border-b border-gray-100">
                                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">{{ t('roles.fields.slug') }}</label>
                                        <p class="mt-1">
                                            <Tag :value="role?.slug" severity="info" />
                                        </p>
                                    </div>

                                    <div v-if="getRoleDescription(role)" class="pb-4">
                                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">{{ t('roles.fields.description') }}</label>
                                        <p class="mt-1 text-gray-600 leading-relaxed">{{ getRoleDescription(role) }}</p>
                                    </div>
                                </div>
                            </template>
                        </Card>

                        <!-- Permissions -->
                        <Card>
                            <template #header>
                                <div class="flex items-center justify-between p-6 pb-0">
                                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                        {{ t('roles.fields.permissions') }}
                                    </h2>
                                    <Badge :value="role?.permissions?.length || 0" severity="info" />
                                </div>
                            </template>
                            <template #content>
                                <div class="p-6 pt-4">
                                    <div v-if="role?.permissions && role.permissions.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div 
                                            v-for="permission in role.permissions" 
                                            :key="permission.id"
                                            class="flex items-start gap-3 p-4 border border-gray-200 rounded-xl hover:border-blue-300 hover:bg-blue-50/30 transition-all group"
                                        >
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:from-blue-200 group-hover:to-blue-300 transition-all">
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-semibold text-gray-900 text-sm leading-tight">{{ getPermissionName(permission) }}</p>
                                                <p class="text-xs text-gray-500 mt-1 truncate">{{ permission.slug }}</p>
                                                <p v-if="locale === 'ar' && permission.name" class="text-xs text-gray-400 italic mt-1">({{ permission.name }})</p>
                                                <p v-else-if="locale === 'en' && permission.name_ar" class="text-xs text-gray-400 italic mt-1">({{ permission.name_ar }})</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="text-center py-12">
                                        <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">{{ t('roles.no_permissions') }}</p>
                                    </div>
                                </div>
                            </template>
                        </Card>

                        <!-- Users with this Role -->
                        <Card v-if="users && users.length > 0">
                            <template #header>
                                <div class="flex items-center justify-between p-6 pb-0">
                                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        {{ t('roles.users_with_role') }}
                                    </h2>
                                    <Badge :value="users.length" severity="success" />
                                </div>
                            </template>
                            <template #content>
                                <div class="p-6 pt-4">
                                    <div class="space-y-3">
                                        <div 
                                            v-for="user in users" 
                                            :key="user.id"
                                            class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl hover:border-green-300 hover:bg-green-50/30 transition-all group"
                                        >
                                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-md group-hover:shadow-lg transition-shadow">
                                                {{ (user.name?.[0] || 'U').toUpperCase() }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-semibold text-gray-900">{{ user.name }}</p>
                                                <p class="text-sm text-gray-500 truncate">{{ user.email }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Quick Actions -->
                        <Card>
                            <template #header>
                                <h3 class="text-lg font-bold text-gray-900 p-6 pb-0 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    {{ t('admin.quick_actions') || 'Quick Actions' }}
                                </h3>
                            </template>
                            <template #content>
                                <div class="p-6 pt-4 space-y-3">
                                    <Link
                                        :href="route('admin.roles.edit', role?.id)"
                                        class="flex items-center gap-3 w-full px-4 py-3 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl font-medium transition-all duration-200 border border-blue-200 hover:border-blue-300 hover:shadow-md group"
                                    >
                                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span>{{ t('common.edit') }}</span>
                                    </Link>
                                    <Link
                                        :href="route('admin.roles.index')"
                                        class="flex items-center gap-3 w-full px-4 py-3 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl font-medium transition-all duration-200 border border-gray-200 hover:border-gray-300 hover:shadow-md group"
                                    >
                                        <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                        <span>{{ t('common.back') }}</span>
                                    </Link>
                                </div>
                            </template>
                        </Card>

                        <!-- Role Info -->
                        <Card>
                            <template #header>
                                <h3 class="text-lg font-bold text-gray-900 p-6 pb-0 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    {{ t('roles.role_info') }}
                                </h3>
                            </template>
                            <template #content>
                                <div class="p-6 pt-4 space-y-6">
                                    <div class="flex items-center justify-center">
                                        <div class="w-24 h-24 rounded-2xl flex items-center justify-center shadow-lg" :class="getRoleColor(role?.slug)">
                                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                                        <div class="text-center">
                                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">{{ t('roles.total_permissions') }}</p>
                                            <p class="text-3xl font-bold text-gray-900">{{ role?.permissions?.length || 0 }}</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">{{ t('roles.total_users') }}</p>
                                            <p class="text-3xl font-bold text-gray-900">{{ users?.length || 0 }}</p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { computed } from 'vue';

const props = defineProps({
    role: Object,
    users: {
        type: Array,
        default: () => [],
    },
});

const { t, locale } = useTranslation();
const { route } = useRoute();
const page = usePage();

const direction = computed(() => page.props.direction || 'ltr');

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

// Get permission name based on locale
const getPermissionName = (permission) => {
    if (!permission) return '';
    if (locale.value === 'ar' && permission.name_ar) {
        return permission.name_ar;
    }
    return permission.name || '';
};

const getRoleColor = (slug) => {
    const colors = {
        'super_admin': 'bg-gradient-to-br from-purple-500 to-purple-700',
        'super-admin': 'bg-gradient-to-br from-purple-500 to-purple-700',
        'admin': 'bg-gradient-to-br from-red-500 to-red-700',
        'instructor': 'bg-gradient-to-br from-blue-500 to-blue-700',
        'student': 'bg-gradient-to-br from-emerald-500 to-emerald-700',
    };
    return colors[slug] || 'bg-gradient-to-br from-gray-500 to-gray-700';
};
</script>

<style scoped>
:deep(.p-card) {
    border-radius: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(229, 231, 235, 0.8);
}

:deep(.p-card:hover) {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

:deep(.p-button) {
    font-weight: 500;
    transition: all 0.3s ease;
    border-radius: 0.75rem;
}

:deep(.p-button:hover) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

:deep(.p-tag) {
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
}

:deep(.p-badge) {
    font-weight: 600;
    padding: 0.375rem 0.75rem;
    border-radius: 9999px;
}
</style>

