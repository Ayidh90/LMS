<template>
    <AdminLayout :page-title="t('roles.edit') || 'Edit Role'" :dir="direction">
        <Head :title="t('roles.edit') || 'Edit Role'" />
        <div class="space-y-6 min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 pb-8">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-2xl p-6 shadow-xl">
                <div class="text-white">
                    <div class="flex items-center gap-2 text-sm text-blue-100 mb-2">
                        <Link :href="route('admin.roles.index')" class="hover:text-white transition-colors">{{ t('roles.title') }}</Link>
                        <span>›</span>
                        <Link :href="route('admin.roles.show', role?.id)" class="hover:text-white transition-colors">{{ getRoleName(role) }}</Link>
                        <span>›</span>
                        <span class="text-white">{{ t('common.edit') }}</span>
                    </div>
                    <h1 class="text-3xl font-bold mb-2">{{ t('roles.edit') }}</h1>
                    <p class="text-blue-100 text-sm">{{ t('roles.edit_description') }}</p>
                </div>
                <div class="w-16 h-16 rounded-xl flex items-center justify-center shadow-lg" :class="getRoleColor(role?.slug)">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
            </div>

            <!-- Success Message -->
            <div v-if="page.props.flash?.success" class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-green-800 font-medium">{{ page.props.flash.success }}</p>
            </div>

            <div class="max-w-5xl mx-auto">

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <form @submit.prevent="submit" class="divide-y divide-gray-100">
                    <!-- Basic Info Section -->
                    <div class="p-6 space-y-6">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ t('roles.basic_info') }}
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('roles.fields.name') }} ({{ locale === 'ar' ? 'English' : 'English' }}) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    :class="{ 'border-red-500 focus:ring-red-500': form.errors.name }"
                                    :placeholder="t('roles.name_placeholder')"
                                />
                                <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('roles.fields.name') }} ({{ locale === 'ar' ? 'العربية' : 'Arabic' }})
                                </label>
                                <input
                                    v-model="form.name_ar"
                                    type="text"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    :class="{ 'border-red-500 focus:ring-red-500': form.errors.name_ar }"
                                    :placeholder="locale === 'ar' ? 'اسم الدور' : t('roles.name_placeholder') + ' (Arabic)'"
                                />
                                <p v-if="form.errors.name_ar" class="mt-2 text-sm text-red-600">{{ form.errors.name_ar }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('roles.fields.description') }} ({{ locale === 'ar' ? 'English' : 'English' }})
                                </label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                    :placeholder="t('roles.description_placeholder')"
                                ></textarea>
                                <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('roles.fields.description') }} ({{ locale === 'ar' ? 'العربية' : 'Arabic' }})
                                </label>
                                <textarea
                                    v-model="form.description_ar"
                                    rows="3"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                    :placeholder="locale === 'ar' ? 'وصف الدور' : t('roles.description_placeholder') + ' (Arabic)'"
                                ></textarea>
                                <p v-if="form.errors.description_ar" class="mt-2 text-sm text-red-600">{{ form.errors.description_ar }}</p>
                            </div>
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
                                    {{ t('roles.select_all') }}
                                </button>
                                <span class="text-gray-300">|</span>
                                <button
                                    type="button"
                                    @click="deselectAll"
                                    class="text-sm text-gray-600 hover:text-gray-800 font-medium"
                                >
                                    {{ t('roles.deselect_all') }}
                                </button>
                            </div>
                        </div>

                        <div class="text-sm text-gray-600 flex items-center gap-2 bg-blue-50 px-4 py-2 rounded-lg border border-blue-100">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium">{{ form.permissions.length }} {{ t('roles.permissions_selected') }}</span>
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
                                    <p class="font-medium text-gray-900 text-sm">{{ getPermissionName(permission) }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ permission.slug }}</p>
                                    <p v-if="locale === 'ar' && permission.name" class="text-xs text-gray-400 italic mt-0.5">({{ permission.name }})</p>
                                    <p v-else-if="locale === 'en' && permission.name_ar" class="text-xs text-gray-400 italic mt-0.5">({{ permission.name_ar }})</p>
                                </div>
                            </label>
                        </div>

                        <div v-if="permissions.length === 0" class="text-center py-8 bg-gray-50 rounded-xl">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            <p class="text-gray-500">{{ t('roles.no_permissions') }}</p>
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
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    role: Object,
    permissions: Array,
});

const { t, locale } = useTranslation();
const { route } = useRoute();
const page = usePage();

const direction = computed(() => page.props.direction || 'ltr');

const form = useForm({
    name: props.role.name || '',
    name_ar: props.role.name_ar || '',
    slug: props.role.slug || '',
    description: props.role.description || '',
    description_ar: props.role.description_ar || '',
    permissions: props.role.permissions?.map(p => p.id) || [],
});

// Get role name based on locale
const getRoleName = (role) => {
    if (!role) return '';
    if (locale.value === 'ar' && role.name_ar) {
        return role.name_ar;
    }
    return role.name || '';
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
