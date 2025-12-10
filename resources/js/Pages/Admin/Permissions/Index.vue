<template>
    <AdminLayout :page-title="t('permissions.title')">
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ t('permissions.title') }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ t('permissions.description') || 'Manage system permissions' }}</p>
                </div>
                    <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 font-medium transition-all duration-200 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        {{ t('permissions.create') }}
                    </button>
                </div>

            <!-- Permissions Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('permissions.fields.name') }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('permissions.fields.slug') }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('permissions.fields.roles') }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('permissions.fields.description') }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('common.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr v-for="permission in permissions" :key="permission.id" class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                            </svg>
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900">{{ permission.name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-700 font-mono">
                                        {{ permission.slug }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        {{ permission.role_models_count || permission.roles?.length || 0 }} {{ t('permissions.roles') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500 max-w-xs truncate">{{ permission.description || '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1">
                                    <button
                                            @click="openEditModal(permission)"
                                            class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                            :title="t('common.edit')"
                                    >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                    </button>
                                    <button
                                            @click="confirmDelete(permission)"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            :title="t('common.delete')"
                                    >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                    </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="permissions.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 font-medium mb-2">{{ t('permissions.no_permissions') }}</p>
                                        <button
                                            @click="openCreateModal"
                                            class="text-blue-600 hover:text-blue-800 font-medium text-sm"
                                        >
                                            {{ t('permissions.create_first') || 'Create your first permission' }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        </div>
        </div>

        <!-- Create/Edit Modal -->
        <Teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-black/50 transition-opacity" @click="closeModal"></div>
                    <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full z-10">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ isEditing ? t('permissions.edit') : t('permissions.create') }}
                                </h3>
                                <button @click="closeModal" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <form @submit.prevent="submitForm" class="p-6 space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('permissions.fields.name') }} <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    :class="{ 'border-red-500': form.errors.name }"
                                    :placeholder="t('permissions.name_placeholder') || 'Permission name'"
                                    @input="!isEditing && generateSlug()"
                                />
                                <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('permissions.fields.slug') }} <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.slug"
                                    type="text"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-mono text-sm"
                                    :class="{ 'border-red-500': form.errors.slug }"
                                    :placeholder="t('permissions.slug_placeholder') || 'permission-slug'"
                                />
                                <p v-if="form.errors.slug" class="mt-2 text-sm text-red-600">{{ form.errors.slug }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('permissions.fields.description') }}
                                </label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                    :placeholder="t('permissions.description_placeholder') || 'Brief description'"
                                ></textarea>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                                <button
                                    type="button"
                                    @click="closeModal"
                                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors"
                                >
                                    {{ t('common.cancel') }}
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all disabled:opacity-50"
                                >
                                    <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ form.processing ? t('common.saving') : (isEditing ? t('common.update') : t('common.save')) }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Teleport>

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
                                <h3 class="text-lg font-semibold text-gray-900">{{ t('permissions.delete_title') || 'Delete Permission' }}</h3>
                                <p class="text-sm text-gray-500">{{ t('permissions.confirm_delete') }}</p>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-6">
                            {{ t('permissions.delete_warning') || 'This will permanently delete the permission' }} <strong>{{ permissionToDelete?.name }}</strong>.
                        </p>
                        <div class="flex justify-end gap-3">
                            <button
                                @click="showDeleteModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                            >
                                {{ t('common.cancel') }}
                            </button>
                            <button
                                @click="deletePermission"
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
import { router, useForm } from '@inertiajs/vue3';

defineProps({
    permissions: Array,
});

const { t } = useTranslation();
const { route } = useRoute();

const showModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const permissionToDelete = ref(null);
const editingPermission = ref(null);

const form = useForm({
    name: '',
    slug: '',
    description: '',
});

const generateSlug = () => {
    form.slug = form.name
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
};

const openCreateModal = () => {
    isEditing.value = false;
    editingPermission.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (permission) => {
    isEditing.value = true;
    editingPermission.value = permission;
    form.name = permission.name;
    form.slug = permission.slug;
    form.description = permission.description || '';
    form.clearErrors();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    form.clearErrors();
};

const submitForm = () => {
    if (isEditing.value && editingPermission.value) {
        form.put(route('admin.permissions.update', editingPermission.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('admin.permissions.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const confirmDelete = (permission) => {
    permissionToDelete.value = permission;
    showDeleteModal.value = true;
};

const deletePermission = () => {
    if (permissionToDelete.value) {
        router.delete(route('admin.permissions.destroy', permissionToDelete.value.id), {
            onSuccess: () => {
                showDeleteModal.value = false;
                permissionToDelete.value = null;
            },
        });
    }
};
</script>
