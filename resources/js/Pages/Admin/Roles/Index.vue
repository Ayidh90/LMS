<template>
    <AdminLayout :page-title="t('roles.title')">
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ t('roles.title') }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ t('roles.description') || 'Manage system roles and their permissions' }}</p>
                </div>
                    <Link
                        :href="route('admin.roles.create')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 font-medium transition-all duration-200 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        {{ t('roles.create') }}
                    </Link>
                </div>

            <!-- Roles Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="role in roles"
                    :key="role.id"
                    class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 group"
                >
                    <!-- Role Header -->
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center" :class="getRoleColor(role.slug)">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ role.name }}</h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        {{ role.slug }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <p v-if="role.description" class="mt-3 text-sm text-gray-500 line-clamp-2">
                            {{ role.description }}
                        </p>
                    </div>

                    <!-- Permissions Count -->
                    <div class="px-6 py-4 bg-gray-50/50">
                        <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                <span class="text-sm font-medium text-gray-700">
                                    {{ role.permissions_count || role.permissions?.length || 0 }} {{ t('roles.permissions') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1">
                                        <Link
                                            :href="route('admin.roles.show', role.id)"
                                    class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                    :title="t('common.view')"
                                        >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                        </Link>
                                        <Link
                                            :href="route('admin.roles.edit', role.id)"
                                    class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                    :title="t('common.edit')"
                                        >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                        </Link>
                                        <button
                                    @click="confirmDelete(role)"
                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    :title="t('common.delete')"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="roles.length === 0" class="col-span-full">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                        <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ t('roles.no_roles') }}</h3>
                        <p class="text-gray-500 mb-6">{{ t('roles.no_roles_description') || 'Get started by creating your first role' }}</p>
                        <Link
                            :href="route('admin.roles.create')"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            {{ t('roles.create') }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Teleport to="body">
            <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-black/50 transition-opacity" @click="showDeleteModal = false"></div>
                    <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ t('roles.delete_title') || 'Delete Role' }}</h3>
                                <p class="text-sm text-gray-500">{{ t('roles.confirm_delete') }}</p>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-6">
                            {{ t('roles.delete_warning') || 'This will permanently delete the role' }} <strong>{{ roleToDelete?.name }}</strong>.
                        </p>
                        <div class="flex justify-end gap-3">
                            <button
                                @click="showDeleteModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                            >
                                {{ t('common.cancel') }}
                            </button>
                            <button
                                @click="deleteRole"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors"
                                        >
                                            {{ t('common.delete') }}
                                        </button>
                                    </div>
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
import { Link, router } from '@inertiajs/vue3';

defineProps({
    roles: Array,
    permissions: Array,
});

const { t } = useTranslation();
const { route } = useRoute();

const showDeleteModal = ref(false);
const roleToDelete = ref(null);

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

const confirmDelete = (role) => {
    roleToDelete.value = role;
    showDeleteModal.value = true;
};

const deleteRole = () => {
    if (roleToDelete.value) {
        router.delete(route('admin.roles.destroy', roleToDelete.value.id), {
            onSuccess: () => {
                showDeleteModal.value = false;
                roleToDelete.value = null;
            },
        });
    }
};
</script>
