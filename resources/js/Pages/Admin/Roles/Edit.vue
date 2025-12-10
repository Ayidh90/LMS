<template>
    <AdminLayout :page-title="t('roles.edit')">
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Page Header -->
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.roles.index')"
                    class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    </Link>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">{{ t('roles.edit') }}</h1>
                    <p class="text-sm text-gray-500">{{ t('roles.edit_description') || 'Update role information and permissions' }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" :class="getRoleColor(role.slug)">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <form @submit.prevent="submit" class="divide-y divide-gray-100">
                    <!-- Basic Info Section -->
                    <div class="p-6 space-y-6">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ t('roles.basic_info') || 'Basic Information' }}
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('roles.fields.name') }} <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    :class="{ 'border-red-500 focus:ring-red-500': form.errors.name }"
                                    :placeholder="t('roles.name_placeholder') || 'Role name'"
                                />
                                <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('roles.fields.slug') }} <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.slug"
                                    type="text"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    :class="{ 'border-red-500 focus:ring-red-500': form.errors.slug }"
                                    :placeholder="t('roles.slug_placeholder') || 'role-slug'"
                                />
                                <p v-if="form.errors.slug" class="mt-2 text-sm text-red-600">{{ form.errors.slug }}</p>
                            </div>
                            </div>

                            <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('roles.fields.description') }}
                            </label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                :placeholder="t('roles.description_placeholder') || 'Brief description of this role'"
                                ></textarea>
                            </div>
                    </div>

                    <!-- Permissions Section -->
                    <div class="p-6 space-y-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                {{ t('roles.fields.permissions') }}
                            </h2>
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    @click="selectAll"
                                    class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                                >
                                    {{ t('roles.select_all') || 'Select All' }}
                                </button>
                                <span class="text-gray-300">|</span>
                                <button
                                    type="button"
                                    @click="deselectAll"
                                    class="text-sm text-gray-600 hover:text-gray-800 font-medium"
                                >
                                    {{ t('roles.deselect_all') || 'Deselect All' }}
                                </button>
                            </div>
                        </div>

                        <div class="text-sm text-gray-500 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ form.permissions.length }} {{ t('roles.permissions_selected') || 'permissions selected' }}
                        </div>

                        <!-- Permissions Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            <label
                                v-for="permission in permissions"
                                :key="permission.id"
                                class="relative flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition-all"
                                :class="form.permissions.includes(permission.id) ? 'border-blue-500 bg-blue-50/50 ring-1 ring-blue-500' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'"
                            >
                                        <input
                                            v-model="form.permissions"
                                            type="checkbox"
                                            :value="permission.id"
                                    class="mt-0.5 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        />
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 text-sm">{{ permission.name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ permission.slug }}</p>
                                </div>
                            </label>
                        </div>

                        <div v-if="permissions.length === 0" class="text-center py-8 bg-gray-50 rounded-xl">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            <p class="text-gray-500">{{ t('permissions.no_permissions') }}</p>
                                </div>
                            </div>

                    <!-- Form Actions -->
                    <div class="px-6 py-4 bg-gray-50/50 flex items-center justify-end gap-3">
                                <Link
                                    :href="route('admin.roles.index')"
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors"
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
                            {{ form.processing ? t('common.saving') : t('common.update') }}
                                </button>
                        </div>
                    </form>
                </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    role: Object,
    permissions: Array,
});

const { t } = useTranslation();
const { route } = useRoute();

const form = useForm({
    name: props.role.name,
    slug: props.role.slug,
    description: props.role.description || '',
    permissions: props.role.permissions?.map(p => p.id) || [],
});

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

const selectAll = () => {
    form.permissions = props.permissions.map(p => p.id);
};

const deselectAll = () => {
    form.permissions = [];
};

const submit = () => {
    form.put(route('admin.roles.update', props.role.id));
};
</script>
