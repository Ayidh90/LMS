<template>
    <AdminLayout :page-title="t('roles.create') || 'Create Role'">
        <Head :title="t('roles.create') || 'Create Role'" />
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
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ t('roles.create') }}</h1>
                    <p class="text-sm text-gray-500">{{ t('roles.create_description') || 'Create a new role with specific permissions' }}</p>
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
                                    @input="generateSlug"
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

                    <!-- Admin Access Section -->
                    <div class="p-6 space-y-6 bg-gray-50/50 border-t border-gray-100">
                        <div class="flex items-center gap-3 p-4 bg-white rounded-xl border border-gray-200">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input v-model="form.is_admin" type="checkbox" class="sr-only peer" />
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ t('roles.fields.is_admin') || 'Admin Dashboard Access' }}</p>
                                <p class="text-sm text-gray-500">{{ t('roles.is_admin_description') || 'This role allows access to admin dashboard. Users with this role must also have is_admin = 1 in their user profile.' }}</p>
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

                        <!-- Permissions by Category -->
                        <div v-for="(groupPermissions, groupName) in groupedPermissions" :key="groupName" class="mb-8">
                            <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ getGroupName(groupName) }}
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                <label
                                    v-for="permission in groupPermissions"
                                    :key="permission.id"
                                    class="relative flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition-all"
                                    :class="form.permissions.includes(permission.id) ? 'border-blue-500 bg-blue-50/50 ring-1 ring-blue-500' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'"
                                >
                                    <input
                                        type="checkbox"
                                        :checked="form.permissions.includes(permission.id)"
                                        @change="togglePermission(permission.id)"
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
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    permissions: Array,
});

const { t, locale } = useTranslation();
const { route } = useRoute();

const form = useForm({
    name: '',
    slug: '',
    description: '',
    permissions: [],
    is_admin: false,
});

// Get permission name based on locale
const getPermissionName = (permission) => {
    if (!permission) return '';
    if (locale.value === 'ar' && permission.name_ar) {
        return permission.name_ar;
    }
    return permission.name || '';
};

// Group permissions by category (prefix before dot in slug)
const groupedPermissions = computed(() => {
    const groups = {};
    const groupOrder = [
        'programs',
        'tracks',
        'courses',
        'sections',
        'lessons',
        'questions',
        'batches',
        'enrollments',
        'users',
        'roles',
        'permissions',
        'settings',
        'dashboard',
        'attendance',
    ];

    // Group permissions (exclude categories and faqs)
    props.permissions?.forEach(permission => {
        const slug = permission.slug || '';
        const groupName = slug.split('.')[0] || 'other';
        
        // Skip categories and faqs permissions
        if (groupName === 'categories' || groupName === 'faqs') {
            return;
        }
        
        if (!groups[groupName]) {
            groups[groupName] = [];
        }
        groups[groupName].push(permission);
    });

    // Sort groups by predefined order
    const sortedGroups = {};
    groupOrder.forEach(groupName => {
        if (groups[groupName]) {
            sortedGroups[groupName] = groups[groupName];
        }
    });

    // Add any remaining groups not in the order list
    Object.keys(groups).forEach(groupName => {
        if (!sortedGroups[groupName]) {
            sortedGroups[groupName] = groups[groupName];
        }
    });

    return sortedGroups;
});

// Get group display name
const getGroupName = (groupName) => {
    const groupNames = {
        programs: locale.value === 'ar' ? 'البرامج' : 'Programs',
        tracks: locale.value === 'ar' ? 'المسارات' : 'Tracks',
        courses: locale.value === 'ar' ? 'الدورات' : 'Courses',
        sections: locale.value === 'ar' ? 'الأقسام' : 'Sections',
        lessons: locale.value === 'ar' ? 'الدروس' : 'Lessons',
        questions: locale.value === 'ar' ? 'الأسئلة' : 'Questions',
        batches: locale.value === 'ar' ? 'الدفعات' : 'Batches',
        enrollments: locale.value === 'ar' ? 'التسجيلات' : 'Enrollments',
        users: locale.value === 'ar' ? 'المستخدمين' : 'Users',
        roles: locale.value === 'ar' ? 'الأدوار' : 'Roles',
        permissions: locale.value === 'ar' ? 'الصلاحيات' : 'Permissions',
        settings: locale.value === 'ar' ? 'الإعدادات' : 'Settings',
        dashboard: locale.value === 'ar' ? 'لوحات التحكم' : 'Dashboards',
        attendance: locale.value === 'ar' ? 'الحضور' : 'Attendance',
        other: locale.value === 'ar' ? 'أخرى' : 'Other',
    };
    return groupNames[groupName] || groupName;
};

const generateSlug = () => {
    form.slug = form.name
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
};

const togglePermission = (permissionId) => {
    const index = form.permissions.indexOf(permissionId);
    if (index > -1) {
        form.permissions.splice(index, 1);
    } else {
        form.permissions.push(permissionId);
    }
};

const selectAll = () => {
    // Select all permissions from all groups
    form.permissions = [...(props.permissions?.map(p => p.id) || [])];
};

const deselectAll = () => {
    form.permissions = [];
};

const submit = () => {
    form.post(route('admin.roles.store'));
};
</script>
