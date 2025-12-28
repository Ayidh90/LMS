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
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            v-model="search"
                            type="text"
                            :placeholder="t('common.search') || 'Search users by name or email...'"
                            class="w-full pr-12 pl-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-gray-50 focus:bg-white"
                            @input="debouncedSearch"
                        />
                    </div>
                    <div class="relative">
                        <select
                            v-model="roleFilter"
                            @change="applyFilters"
                            class="w-full sm:w-auto px-4 py-3 pr-10 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 focus:bg-white min-w-[180px] appearance-none cursor-pointer transition-all"
                        >
                            <option value="">{{ t('users.all_roles') || 'All Roles' }}</option>
                            <option
                                v-for="role in roles"
                                :key="role.id"
                                :value="role.slug || role.name"
                            >
                                {{ getRoleDisplayName(role) }}
                            </option>
                        </select>
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Users List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table table-hover mb-0 w-100">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-right text-uppercase fw-bold text-muted small border-bottom-2">
                                    <button
                                        @click="sortBy('name')"
                                        class="flex items-center gap-2 text-muted hover:text-gray-900 transition-colors bg-transparent border-0 p-0 cursor-pointer"
                                        :class="{ 'text-gray-900 font-bold': currentSort === 'name' }"
                                    >
                                        {{ t('users.fields.name') }}
                                        <span v-if="currentSort === 'name'" class="text-xs">
                                            {{ sortOrder === 'asc' ? '↑' : '↓' }}
                                        </span>
                                    </button>
                                </th>
                                <th scope="col" class="text-right text-uppercase fw-bold text-muted small border-bottom-2">
                                    <button
                                        @click="sortBy('email')"
                                        class="flex items-center gap-2 text-muted hover:text-gray-900 transition-colors bg-transparent border-0 p-0 cursor-pointer"
                                        :class="{ 'text-gray-900 font-bold': currentSort === 'email' }"
                                    >
                                        {{ t('users.fields.email') }}
                                        <span v-if="currentSort === 'email'" class="text-xs">
                                            {{ sortOrder === 'asc' ? '↑' : '↓' }}
                                        </span>
                                    </button>
                                </th>
                                <th scope="col" class="text-right text-uppercase fw-bold text-muted small border-bottom-2">
                                    {{ t('users.fields.roles') || t('users.fields.role') }}
                                </th>
                                <th scope="col" class="text-right text-uppercase fw-bold text-muted small border-bottom-2">
                                    <button
                                        @click="sortBy('is_active')"
                                        class="flex items-center gap-2 text-muted hover:text-gray-900 transition-colors bg-transparent border-0 p-0 cursor-pointer"
                                        :class="{ 'text-gray-900 font-bold': currentSort === 'is_active' }"
                                    >
                                        {{ t('users.fields.status') }}
                                        <span v-if="currentSort === 'is_active'" class="text-xs">
                                            {{ sortOrder === 'asc' ? '↑' : '↓' }}
                                        </span>
                                    </button>
                                </th>
                                <th scope="col" class="text-right text-uppercase fw-bold text-muted small border-bottom-2">
                                    {{ t('common.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            <tr 
                                v-for="user in users.data" 
                                :key="user.id" 
                                class="table-row-hover align-middle"
                            >
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <div 
                                                class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-md group-hover:shadow-lg transition-all transform group-hover:scale-105"
                                                :class="getAvatarColor(getPrimaryRole(user))"
                                            >
                                                {{ getInitials(user.name) }}
                                            </div>
                                            <div 
                                                v-if="user.is_active"
                                                class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"
                                            ></div>
                                            <div 
                                                v-else
                                                class="absolute -bottom-1 -right-1 w-4 h-4 bg-red-500 border-2 border-white rounded-full"
                                            ></div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                                {{ user.name }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ t('common.joined') }} {{ formatDate(user.created_at) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="d-flex align-items-center gap-2 text-muted small">
                                        <svg class="w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;">{{ user.email }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <!-- Display user roles - show first role, or all if only one -->
                                        <template v-if="getUserRoles(user).length > 1">
                                            <!-- Multiple roles - show first with clickable indicator -->
                                            <span
                                                @click="showRolesModal(user)"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg shadow-sm border transition-all hover:scale-105 cursor-pointer"
                                                :class="getRoleBadgeClass(getUserRoles(user)[0].slug || getUserRoles(user)[0].name)"
                                                :title="t('users.view_all_roles') || 'Click to view all roles'"
                                            >
                                                <span class="w-2 h-2 rounded-full" :class="getRoleDotClass(getUserRoles(user)[0].slug || getUserRoles(user)[0].name)"></span>
                                                {{ getRoleDisplayName(getUserRoles(user)[0]) }}
                                                <span class="ml-1 text-xs font-bold">+{{ getUserRoles(user).length - 1 }}</span>
                                            </span>
                                        </template>
                                        <template v-else>
                                            <!-- Single role or no roles -->
                                            <span
                                                v-for="role in getUserRoles(user)"
                                                :key="role.id || role.slug || role"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg shadow-sm border transition-all hover:scale-105"
                                                :class="getRoleBadgeClass(role.slug || role)"
                                            >
                                                <span class="w-2 h-2 rounded-full" :class="getRoleDotClass(role.slug || role)"></span>
                                                {{ getRoleDisplayName(role) }}
                                            </span>
                                            <!-- Fallback to legacy role if no roles found -->
                                            <span
                                                v-if="!user.roles || user.roles.length === 0"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg shadow-sm border transition-all hover:scale-105"
                                                :class="getRoleBadgeClass(user.role)"
                                            >
                                                <span class="w-2 h-2 rounded-full" :class="getRoleDotClass(user.role)"></span>
                                                {{ t('users.roles.' + user.role) || user.role }}
                                            </span>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span 
                                        class="badge d-inline-flex align-items-center gap-2"
                                        :class="user.is_active ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25'"
                                    >
                                        <span class="badge-dot" :class="user.is_active ? 'bg-success' : 'bg-danger'"></span>
                                        {{ user.is_active ? t('users.active') : t('users.inactive') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="d-flex align-items-center gap-2">
                                        <button
                                            v-if="can('users.manage')"
                                            @click="impersonateUser(user)"
                                            class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1"
                                            title="Impersonate User"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                            </svg>
                                            <span class="d-none d-sm-inline">{{ t('users.impersonate') || 'Impersonate' }}</span>
                                        </button>
                                        <Link
                                            v-if="can('users.edit')"
                                            :href="route('admin.users.edit', user.id)"
                                            class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1"
                                            title="Edit User"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <span class="d-none d-sm-inline">{{ t('common.edit') }}</span>
                                        </Link>
                                        <button
                                            v-if="can('users.delete')"
                                            @click="confirmDelete(user)"
                                            class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1"
                                            title="Delete User"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <span class="d-none d-sm-inline">{{ t('common.delete') }}</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="users.data.length === 0">
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="w-20 h-20 rounded-circle bg-light d-flex align-items-center justify-content-center mb-4">
                                            <svg class="w-10 h-10 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-muted fw-semibold fs-5 mb-1">{{ t('users.no_users') }}</p>
                                        <p class="text-muted small">{{ t('users.no_users_description') || 'No users found matching your criteria' }}</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="users.links && users.links.length > 3" class="px-6 py-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100/50">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-gray-600 font-medium">
                            {{ t('common.showing') }} <span class="text-gray-900 font-bold">{{ users.from }}</span> {{ t('common.to') }} <span class="text-gray-900 font-bold">{{ users.to }}</span> {{ t('common.of') }} <span class="text-gray-900 font-bold">{{ users.total }}</span> {{ t('users.total_users') || 'users' }}
                        </div>
                        <nav class="flex items-center gap-1">
                            <template v-for="(link, index) in users.links" :key="index">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    :class="[
                                        'px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200',
                                        link.active
                                            ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/30'
                                            : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 border border-gray-200'
                                    ]"
                                >
                                    <span v-html="link.label"></span>
                                </Link>
                                <span
                                    v-else
                                    class="px-4 py-2 text-sm text-gray-400 font-semibold"
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

        <!-- User Roles Modal -->
        <Teleport to="body">
            <div v-if="showRolesModalVisible" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-black/50 transition-opacity" @click="showRolesModalVisible = false"></div>
                    <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ t('users.user_roles') || 'User Roles' }}</h3>
                                    <p class="text-sm text-gray-500">{{ selectedUserForRoles?.name }}</p>
                                </div>
                            </div>
                            <button
                                @click="showRolesModalVisible = false"
                                class="text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="space-y-2 mb-6">
                            <div
                                v-for="role in getUserRoles(selectedUserForRoles)"
                                :key="role.id || role.slug || role"
                                class="flex items-center gap-3 p-3 rounded-lg border"
                                :class="getRoleBadgeClass(role.slug || role.name || role)"
                            >
                                <span class="w-3 h-3 rounded-full" :class="getRoleDotClass(role.slug || role.name || role)"></span>
                                <div class="flex-1">
                                    <div class="font-semibold text-sm">{{ getRoleDisplayName(role) }}</div>
                                    <div v-if="role.description || role.description_ar" class="text-xs text-gray-500 mt-1">
                                        {{ locale === 'ar' && role.description_ar ? role.description_ar : (role.description || '') }}
                                    </div>
                                </div>
                            </div>
                            <div
                                v-if="!selectedUserForRoles?.roles || selectedUserForRoles.roles.length === 0"
                                class="flex items-center gap-3 p-3 rounded-lg border"
                                :class="getRoleBadgeClass(selectedUserForRoles?.role)"
                            >
                                <span class="w-3 h-3 rounded-full" :class="getRoleDotClass(selectedUserForRoles?.role)"></span>
                                <div class="flex-1">
                                    <div class="font-semibold text-sm">{{ t('users.roles.' + selectedUserForRoles?.role) || selectedUserForRoles?.role }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button
                                @click="showRolesModalVisible = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                            >
                                {{ t('common.close') || 'Close' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { usePermissions } from '@/composables/usePermissions';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    users: Object,
    roles: {
        type: Array,
        default: () => [],
    },
});

const { can } = usePermissions();

const { t, locale } = useTranslation();
const { route } = useRoute();
const page = usePage();

// Initialize from URL query parameters
const getQueryParam = (key, defaultValue = '') => {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(key) || defaultValue;
};

const search = ref(getQueryParam('search', ''));
const roleFilter = ref(getQueryParam('role', ''));
const currentSort = ref(getQueryParam('sort_by', 'created_at'));
const sortOrder = ref(getQueryParam('sort_order', 'desc'));
const showDeleteModal = ref(false);
const userToDelete = ref(null);
const showRolesModalVisible = ref(false);
const selectedUserForRoles = ref(null);

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
    if (currentSort.value) params.sort_by = currentSort.value;
    if (sortOrder.value) params.sort_order = sortOrder.value;
    
    router.get(route('admin.users.index'), params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const sortBy = (column) => {
    if (currentSort.value === column) {
        // Toggle sort order
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        // New column, default to ascending
        currentSort.value = column;
        sortOrder.value = 'asc';
    }
    applyFilters();
};

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};

const getAvatarColor = (role) => {
    const colors = {
        'super_admin': 'bg-gradient-to-br from-purple-500 via-purple-600 to-purple-700',
        'admin': 'bg-gradient-to-br from-red-500 via-red-600 to-red-700',
        'instructor': 'bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700',
        'student': 'bg-gradient-to-br from-emerald-500 via-emerald-600 to-emerald-700',
    };
    return colors[role] || 'bg-gradient-to-br from-gray-500 via-gray-600 to-gray-700';
};

// Get primary role for avatar color (first role or legacy role)
const getPrimaryRole = (user) => {
    if (user.roles && user.roles.length > 0) {
        return user.roles[0].slug || user.roles[0].name;
    }
    return user.role || 'student';
};

const getRoleBadgeClass = (role) => {
    const classes = {
        'super_admin': 'bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100',
        'admin': 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100',
        'instructor': 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100',
        'student': 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100',
    };
    return classes[role] || 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100';
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

// Get all roles for a user
const getUserRoles = (user) => {
    if (user.roles && user.roles.length > 0) {
        return user.roles;
    }
    // Fallback to legacy role
    if (user.role) {
        return [{ slug: user.role, name: user.role }];
    }
    return [];
};

// Get role display name
const getRoleDisplayName = (role) => {
    if (typeof role === 'string') {
        return t('users.roles.' + role) || role;
    }
    if (role.name_ar && locale.value === 'ar') {
        return role.name_ar;
    }
    return t('users.roles.' + (role.slug || role.name)) || role.name || role.slug || '';
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

const showRolesModal = (user) => {
    selectedUserForRoles.value = user;
    showRolesModalVisible.value = true;
};

const impersonateUser = (user) => {
    if (!user || !user.id) {
        console.error('Invalid user data for impersonation');
        alert('Invalid user data');
        return;
    }

    // Build route URL - use route helper first, fallback to manual construction
    let routeUrl;
    try {
        routeUrl = route('admin.users.impersonate', user.id);
        // Check if route helper returned a valid URL
        if (!routeUrl || routeUrl === '#' || routeUrl === '') {
            throw new Error('Route helper returned invalid URL');
        }
    } catch (error) {
        console.warn('Route helper failed, building URL manually:', error);
        // Fallback: build URL manually based on route pattern
        routeUrl = `/admin/users/${user.id}/impersonate`;
    }

    // Ensure URL starts with /
    if (!routeUrl.startsWith('/')) {
        routeUrl = `/${routeUrl}`;
    }

    console.log('Impersonating user:', user.id, 'Route URL:', routeUrl);

    // Use form submission to ensure full page reload and proper redirect
    // This is more reliable than Inertia router for redirects
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = routeUrl;
    form.style.display = 'none';
    
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token not found');
        alert('CSRF token not found. Please refresh the page.');
        return;
    }

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    // Add to body, submit, then remove
    document.body.appendChild(form);
    form.submit();
};
</script>

<style scoped>
/* Bootstrap Table Styles */
.table {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
}

.table thead {
    background: linear-gradient(to bottom, #f8f9fa 0%, #e9ecef 100%);
}

.table thead th {
    border-bottom: 2px solid #dee2e6;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
    padding: 1rem 1.5rem;
    color: #495057;
    vertical-align: middle;
}

.table tbody tr {
    transition: all 0.15s ease-in-out;
    border-bottom: 1px solid #f1f3f5;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.table tbody tr:last-child {
    border-bottom: none;
}

.table td {
    padding: 1rem 1.5rem;
    vertical-align: middle;
    border-top: 1px solid #f1f3f5;
}

.table-group-divider {
    border-top: 2px solid #dee2e6;
}

.table-light {
    background-color: #f8f9fa;
    color: #495057;
}

.border-bottom-2 {
    border-bottom: 2px solid #dee2e6 !important;
}

/* Badge Styles */
.badge-dot {
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
    display: inline-block;
}

/* Button Styles */
.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 0.375rem;
}

.btn-outline-primary {
    border: 1px solid #0d6efd;
    color: #0d6efd;
    background-color: transparent;
}

.btn-outline-primary:hover {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
}

.btn-outline-danger {
    border: 1px solid #dc3545;
    color: #dc3545;
    background-color: transparent;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
    color: #fff;
}

.btn-outline-info {
    border: 1px solid #0dcaf0;
    color: #0dcaf0;
    background-color: transparent;
}

.btn-outline-info:hover {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: #fff;
}

/* RTL Support */
[dir="rtl"] .table thead th,
[dir="rtl"] .table td {
    text-align: right;
}

[dir="ltr"] .table thead th,
[dir="ltr"] .table td {
    text-align: left;
}
</style>
