<template>
    <AdminLayout :page-title="t('admin.users') || 'Users'">
        <Head :title="t('admin.users') || 'Users'" />
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ t('admin.users') }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ t('admin.users_description') || 'Manage system users and their roles' }}</p>
                </div>
                <Link
                    :href="route('admin.users.create')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 font-medium transition-all duration-200 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ t('users.create') }}
                </Link>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            v-model="search"
                            type="text"
                            :placeholder="t('common.search') || 'Search users...'"
                            class="w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            @input="debouncedSearch"
                        />
                    </div>
                    <select
                        v-model="roleFilter"
                        @change="applyFilters"
                        class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white min-w-[150px]"
                    >
                        <option value="">{{ t('users.all_roles') || 'All Roles' }}</option>
                        <option value="student">{{ t('users.roles.student') }}</option>
                        <option value="instructor">{{ t('users.roles.instructor') }}</option>
                        <option value="admin">{{ t('users.roles.admin') }}</option>
                        <option value="super_admin">{{ t('users.roles.super_admin') }}</option>
                    </select>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50">
                        <tr>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('users.fields.name') }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('users.fields.email') }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('users.fields.role') }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('users.fields.status') }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ t('common.actions') }}
                                </th>
                        </tr>
                    </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-sm"
                                            :class="getAvatarColor(user.role)">
                                            {{ getInitials(user.name) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ user.name }}</div>
                                            <div class="text-xs text-gray-500">{{ t('common.joined') }} {{ formatDate(user.created_at) }}</div>
                                        </div>
                                    </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600">{{ user.email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-full"
                                        :class="getRoleBadgeClass(user.role)">
                                        <span class="w-1.5 h-1.5 rounded-full" :class="getRoleDotClass(user.role)"></span>
                                        {{ t('users.roles.' + user.role) || user.role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-full"
                                        :class="user.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'">
                                        <span class="w-1.5 h-1.5 rounded-full" :class="user.is_active ? 'bg-emerald-500' : 'bg-red-500'"></span>
                                    {{ user.is_active ? t('users.active') : t('users.inactive') }}
                                </span>
                            </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <Link
                                        :href="route('admin.users.edit', user.id)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"
                                    >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        {{ t('common.edit') }}
                                    </Link>
                                    <button
                                            @click="confirmDelete(user)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors"
                                    >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        {{ t('common.delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <p class="text-gray-500 font-medium">{{ t('users.no_users') }}</p>
                                    </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>

                <!-- Pagination -->
                <div v-if="users.links && users.links.length > 3" class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-gray-600">
                            {{ t('common.showing') }} <span class="font-medium">{{ users.from }}</span> {{ t('common.to') }} <span class="font-medium">{{ users.to }}</span> {{ t('common.of') }} <span class="font-medium">{{ users.total }}</span>
                        </div>
                        <nav class="flex items-center gap-1">
                            <template v-for="(link, index) in users.links" :key="index">
                            <Link
                                    v-if="link.url"
                                    :href="link.url"
                                :class="[
                                        'px-3 py-2 rounded-lg text-sm font-medium transition-all',
                                    link.active
                                            ? 'bg-blue-600 text-white shadow-sm'
                                            : 'text-gray-600 hover:bg-gray-100'
                                    ]"
                                >
                                    <span v-html="link.label"></span>
                                </Link>
                                <span
                                    v-else
                                    class="px-3 py-2 text-sm text-gray-400"
                                v-html="link.label"
                                ></span>
                            </template>
                        </nav>
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
                                <h3 class="text-lg font-semibold text-gray-900">{{ t('users.confirm_delete_title') || 'Delete User' }}</h3>
                                <p class="text-sm text-gray-500">{{ t('users.confirm_delete') }}</p>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-6">
                            {{ t('users.delete_warning') || 'This action cannot be undone. The user' }} <strong>{{ userToDelete?.name }}</strong> {{ t('users.will_be_deleted') || 'will be permanently deleted.' }}
                        </p>
                        <div class="flex justify-end gap-3">
                            <button
                                @click="showDeleteModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                            >
                                {{ t('common.cancel') }}
                            </button>
                            <button
                                @click="deleteUser"
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
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    users: Object,
});

const { t } = useTranslation();
const { route } = useRoute();

const search = ref('');
const roleFilter = ref('');
const showDeleteModal = ref(false);
const userToDelete = ref(null);

let searchTimeout = null;

const debouncedSearch = () => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 300);
};

const applyFilters = () => {
    const params = {};
    if (search.value) params.search = search.value;
    if (roleFilter.value) params.role = roleFilter.value;
    
    router.get(route('admin.users.index'), params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};

const getAvatarColor = (role) => {
    const colors = {
        'super_admin': 'bg-gradient-to-br from-purple-500 to-purple-700',
        'admin': 'bg-gradient-to-br from-red-500 to-red-700',
        'instructor': 'bg-gradient-to-br from-blue-500 to-blue-700',
        'student': 'bg-gradient-to-br from-emerald-500 to-emerald-700',
    };
    return colors[role] || 'bg-gradient-to-br from-gray-500 to-gray-700';
};

const getRoleBadgeClass = (role) => {
    const classes = {
        'super_admin': 'bg-purple-50 text-purple-700',
        'admin': 'bg-red-50 text-red-700',
        'instructor': 'bg-blue-50 text-blue-700',
        'student': 'bg-emerald-50 text-emerald-700',
    };
    return classes[role] || 'bg-gray-50 text-gray-700';
};

const getRoleDotClass = (role) => {
    const classes = {
        'super_admin': 'bg-purple-500',
        'admin': 'bg-red-500',
        'instructor': 'bg-blue-500',
        'student': 'bg-emerald-500',
    };
    return classes[role] || 'bg-gray-500';
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString();
};

const confirmDelete = (user) => {
    userToDelete.value = user;
    showDeleteModal.value = true;
};

const deleteUser = () => {
    if (userToDelete.value) {
        router.delete(route('admin.users.destroy', userToDelete.value.id), {
            onSuccess: () => {
                showDeleteModal.value = false;
                userToDelete.value = null;
            },
        });
    }
};
</script>
