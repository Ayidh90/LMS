<template>
    <AdminLayout :page-title="role?.name || t('roles.title')">
        <Head :title="role?.name || t('roles.title')" />
        
        <div class="space-y-6 min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 pb-8" :dir="direction">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-2xl p-6 shadow-xl">
                <div class="text-white">
                    <div class="flex items-center gap-2 text-sm text-blue-100 mb-2">
                        <Link :href="route('admin.roles.index')" class="hover:text-white transition-colors">{{ t('roles.title') }}</Link>
                        <span>›</span>
                        <span class="text-white">{{ role?.name }}</span>
                    </div>
                    <h1 class="text-3xl font-bold mb-2">{{ role?.name }}</h1>
                    <p class="text-blue-100 text-sm">{{ t('roles.view_description') || 'View role details and permissions' }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <Link :href="route('admin.roles.edit', role?.id)">
                        <Button 
                            :label="t('common.edit')" 
                            icon="pi pi-pencil"
                            severity="secondary"
                            outlined
                            class="bg-white"
                        />
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Role Details -->
                    <Card>
                        <template #content>
                            <h2 class="text-xl font-bold text-gray-900 mb-4">{{ t('roles.basic_info') || 'Basic Information' }}</h2>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-500">{{ t('roles.fields.name') || 'Name' }}</label>
                                    <p class="mt-1 text-gray-900 font-semibold">{{ role?.name }}</p>
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-gray-500">{{ t('roles.fields.slug') || 'Slug' }}</label>
                                    <p class="mt-1">
                                        <Tag :value="role?.slug" severity="info" />
                                    </p>
                                </div>

                                <div v-if="role?.description">
                                    <label class="text-sm font-medium text-gray-500">{{ t('roles.fields.description') || 'Description' }}</label>
                                    <p class="mt-1 text-gray-600 leading-relaxed">{{ role?.description }}</p>
                                </div>
                            </div>
                        </template>
                    </Card>

                    <!-- Permissions -->
                    <Card>
                        <template #header>
                            <div class="flex items-center justify-between p-6 pb-0">
                                <h2 class="text-xl font-bold text-gray-900">{{ t('roles.fields.permissions') || 'Permissions' }}</h2>
                                <Badge :value="role?.permissions?.length || 0" severity="info" />
                            </div>
                        </template>
                        <template #content>
                            <div v-if="role?.permissions && role.permissions.length > 0" class="space-y-3">
                                <div 
                                    v-for="permission in role.permissions" 
                                    :key="permission.id"
                                    class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50/30 transition-all"
                                >
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="pi pi-key text-blue-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ permission.name }}</p>
                                        <p class="text-sm text-gray-500">{{ permission.slug }}</p>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 text-gray-500">
                                <i class="pi pi-inbox text-4xl text-gray-300 mb-4"></i>
                                <p>{{ t('roles.no_permissions') || 'No permissions assigned' }}</p>
                            </div>
                        </template>
                    </Card>

                    <!-- Users with this Role -->
                    <Card v-if="users && users.length > 0">
                        <template #header>
                            <div class="flex items-center justify-between p-6 pb-0">
                                <h2 class="text-xl font-bold text-gray-900">{{ t('roles.users_with_role') || 'Users with this Role' }}</h2>
                                <Badge :value="users.length" severity="success" />
                            </div>
                        </template>
                        <template #content>
                            <div class="space-y-3">
                                <div 
                                    v-for="user in users" 
                                    :key="user.id"
                                    class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50/30 transition-all"
                                >
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-semibold flex-shrink-0">
                                        {{ (user.name?.[0] || 'U').toUpperCase() }}
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ user.name }}</p>
                                        <p class="text-sm text-gray-500">{{ user.email }}</p>
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
                            <h3 class="text-lg font-bold text-gray-900 p-6 pb-0">{{ t('admin.quick_actions') || 'Quick Actions' }}</h3>
                        </template>
                        <template #content>
                            <div class="space-y-2">
                                <Link
                                    :href="route('admin.roles.edit', role?.id)"
                                    class="block w-full"
                                >
                                    <Button 
                                        :label="t('common.edit') || 'Edit Role'" 
                                        icon="pi pi-pencil"
                                        class="w-full justify-start"
                                        outlined
                                        severity="info"
                                    />
                                </Link>
                                <Link
                                    :href="route('admin.roles.index')"
                                    class="block w-full"
                                >
                                    <Button 
                                        :label="t('common.back') || 'Back to List'" 
                                        icon="pi pi-arrow-left"
                                        class="w-full justify-start"
                                        outlined
                                        severity="secondary"
                                    />
                                </Link>
                            </div>
                        </template>
                    </Card>

                    <!-- Role Info -->
                    <Card>
                        <template #header>
                            <h3 class="text-lg font-bold text-gray-900 p-6 pb-0">{{ t('roles.role_info') || 'Role Information' }}</h3>
                        </template>
                        <template #content>
                            <div class="space-y-4">
                                <div class="flex items-center justify-center">
                                    <div class="w-20 h-20 rounded-xl flex items-center justify-center" :class="getRoleColor(role?.slug)">
                                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-500 mb-1">{{ t('roles.total_permissions') || 'Total Permissions' }}</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ role?.permissions?.length || 0 }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-500 mb-1">{{ t('roles.total_users') || 'Total Users' }}</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ users?.length || 0 }}</p>
                                </div>
                            </div>
                        </template>
                    </Card>
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

const { t } = useTranslation();
const { route } = useRoute();
const page = usePage();

const direction = computed(() => page.props.direction || 'ltr');

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
}

:deep(.p-button) {
    font-weight: 500;
    transition: all 0.3s ease;
}

:deep(.p-button:hover) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

:deep(.p-tag) {
    font-weight: 600;
    padding: 0.375rem 0.75rem;
}

:deep(.p-badge) {
    font-weight: 600;
}
</style>

