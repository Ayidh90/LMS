<template>
    <AdminLayout :page-title="t('categories.edit') || 'Edit Category'">
        <Head :title="t('categories.edit') || 'Edit Category'" />
        <div class="max-w-3xl mx-auto space-y-6">
            <!-- Page Header -->
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.categories.index')"
                    class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">{{ t('categories.edit') || 'Edit Category' }}</h1>
                    <p class="text-sm text-gray-500">{{ t('categories.edit_description') || 'Update category information' }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl">
                    {{ category.icon || '📚' }}
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('categories.fields.name') || 'Name' }} <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <!-- Name Arabic -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('categories.fields.name_ar') || 'Name (Arabic)' }}
                            </label>
                            <input
                                v-model="form.name_ar"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            />
                        </div>
                    </div>

                    <!-- Slug -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('categories.fields.slug') || 'Slug' }}
                        </label>
                        <input
                            v-model="form.slug"
                            type="text"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-mono text-sm"
                            :class="{ 'border-red-500': form.errors.slug }"
                        />
                        <p v-if="form.errors.slug" class="mt-2 text-sm text-red-600">{{ form.errors.slug }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('categories.fields.description') || 'Description' }}
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                            ></textarea>
                        </div>

                        <!-- Description Arabic -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('categories.fields.description_ar') || 'Description (Arabic)' }}
                            </label>
                            <textarea
                                v-model="form.description_ar"
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                            ></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Icon -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('categories.fields.icon') || 'Icon' }}
                            </label>
                            <input
                                v-model="form.icon"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-center text-2xl"
                                maxlength="2"
                            />
                        </div>

                        <!-- Order -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('categories.fields.order') || 'Order' }}
                            </label>
                            <input
                                v-model.number="form.order"
                                type="number"
                                min="0"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            />
                        </div>

                        <!-- Is Active -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('categories.fields.is_active') || 'Status' }}
                            </label>
                            <label class="relative inline-flex items-center cursor-pointer w-full justify-center p-3 border border-gray-200 rounded-xl hover:bg-gray-50"
                                :class="form.is_active ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200'">
                                <input v-model="form.is_active" type="checkbox" class="sr-only peer" />
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"
                                    :class="form.is_active ? 'peer-focus:ring-emerald-300 peer-checked:bg-emerald-600' : 'peer-focus:ring-red-300'"></div>
                                <span class="mr-3 text-sm font-medium"
                                    :class="form.is_active ? 'text-emerald-700' : 'text-red-700'">
                                    {{ form.is_active ? (t('categories.active') || 'Active') : (t('categories.inactive') || 'Inactive') }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                        <Link
                            :href="route('admin.categories.index')"
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors"
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
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    category: Object,
});

const { t } = useTranslation();
const { route } = useRoute();

const form = useForm({
    name: props.category.name,
    name_ar: props.category.name_ar || '',
    slug: props.category.slug,
    description: props.category.description || '',
    description_ar: props.category.description_ar || '',
    icon: props.category.icon || '📚',
    order: props.category.order || 0,
    is_active: props.category.is_active ?? true,
});

const submit = () => {
    form.put(route('admin.categories.update', props.category.id));
};
</script>

