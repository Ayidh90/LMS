<template>
    <AdminLayout :page-title="t('categories.title')">
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ t('categories.title') || 'Categories' }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ t('categories.description') || 'Manage course categories' }}</p>
                </div>
                <Link
                    :href="route('admin.categories.create')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 font-medium transition-all duration-200 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ t('categories.create') || 'Create Category' }}
                </Link>
            </div>

            <!-- Categories Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="category in categories.data"
                    :key="category.id"
                    class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 group"
                >
                    <!-- Category Header -->
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl">
                                    {{ category.icon || '📚' }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ category.translated_name || category.name }}</h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 mt-1">
                                        {{ category.slug }}
                                    </span>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                :class="category.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'">
                                {{ category.is_active ? t('categories.active') || 'Active' : t('categories.inactive') || 'Inactive' }}
                            </span>
                        </div>
                        <p v-if="category.translated_description || category.description" class="text-sm text-gray-500 line-clamp-2">
                            {{ category.translated_description || category.description }}
                        </p>
                    </div>

                    <!-- Category Footer -->
                    <div class="px-6 py-4 bg-gray-50/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <span class="text-sm font-medium text-gray-700">
                                    {{ category.courses_count || 0 }} {{ t('categories.courses') || 'courses' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1">
                                <Link
                                    :href="route('admin.categories.edit', category.id)"
                                    class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                    :title="t('common.edit')"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </Link>
                                <button
                                    @click="confirmDelete(category)"
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
                <div v-if="categories.data.length === 0" class="col-span-full">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                        <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ t('categories.no_categories') || 'No categories found' }}</h3>
                        <p class="text-gray-500 mb-6">{{ t('categories.no_categories_description') || 'Get started by creating your first category' }}</p>
                        <Link
                            :href="route('admin.categories.create')"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            {{ t('categories.create') || 'Create Category' }}
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="categories.links && categories.links.length > 3" class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-4">
                <nav class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        {{ t('common.showing') }} <span class="font-medium">{{ categories.from }}</span> {{ t('common.to') }} <span class="font-medium">{{ categories.to }}</span> {{ t('common.of') }} <span class="font-medium">{{ categories.total }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <template v-for="(link, index) in categories.links" :key="index">
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
                    </div>
                </nav>
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
                                <h3 class="text-lg font-semibold text-gray-900">{{ t('categories.delete_title') || 'Delete Category' }}</h3>
                                <p class="text-sm text-gray-500">{{ t('categories.confirm_delete') || 'Are you sure you want to delete this category?' }}</p>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-6">
                            {{ t('categories.delete_warning') || 'This will permanently delete the category' }} <strong>{{ categoryToDelete?.translated_name || categoryToDelete?.name }}</strong>.
                            <span v-if="categoryToDelete?.courses_count > 0" class="block mt-2 text-red-600 text-sm">
                                {{ t('categories.has_courses_warning') || 'Warning: This category has courses associated with it.' }}
                            </span>
                        </p>
                        <div class="flex justify-end gap-3">
                            <button
                                @click="showDeleteModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                            >
                                {{ t('common.cancel') }}
                            </button>
                            <button
                                @click="deleteCategory"
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
    categories: Object,
});

const { t } = useTranslation();
const { route } = useRoute();

const showDeleteModal = ref(false);
const categoryToDelete = ref(null);

const confirmDelete = (category) => {
    categoryToDelete.value = category;
    showDeleteModal.value = true;
};

const deleteCategory = () => {
    if (categoryToDelete.value) {
        router.delete(route('admin.categories.destroy', categoryToDelete.value.id), {
            onSuccess: () => {
                showDeleteModal.value = false;
                categoryToDelete.value = null;
            },
        });
    }
};
</script>

