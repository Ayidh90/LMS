<template>
    <AdminLayout :page-title="t('admin.create_lesson') || 'Create Lesson'">
        <Head :title="t('admin.create_lesson') || 'Create Lesson'" />
        <div class="max-w-4xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-2">
                    <Link :href="route('admin.courses.lessons.index', course.slug || course.id)" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </Link>
                    <h1 class="text-2xl font-bold text-gray-900">{{ t('admin.create_lesson') || 'Create Lesson' }}</h1>
                </div>
                <p class="text-gray-500">{{ course.translated_title || course.title }}</p>
            </div>

            <!-- Form Card -->
            <form @submit.prevent="submit" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 space-y-8">
                    <!-- Basic Info -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            {{ t('lessons.basic_info') || 'Basic Information' }}
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Title English -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('lessons.fields.title') || 'Title' }} (English) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.title"
                                    type="text"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    :class="{ 'border-red-500': form.errors.title }"
                                    :placeholder="t('lessons.placeholders.title') || 'Enter lesson title'"
                                />
                                <p v-if="form.errors.title" class="mt-1 text-sm text-red-500">{{ form.errors.title }}</p>
                            </div>
                            
                            <!-- Title Arabic -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('lessons.fields.title') || 'Title' }} (العربية)
                                </label>
                                <input
                                    v-model="form.title_ar"
                                    type="text"
                                    dir="rtl"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    :placeholder="'أدخل عنوان الدرس'"
                                />
                            </div>
                            
                            <!-- Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('lessons.fields.type') || 'Type' }} <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.type"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white"
                                    :class="{ 'border-red-500': form.errors.type }"
                                >
                                    <option value="">{{ t('common.select') }}</option>
                                    <option v-for="type in lessonTypes" :key="type.value" :value="type.value">
                                        {{ type.label }}
                                    </option>
                                </select>
                                <p v-if="form.errors.type" class="mt-1 text-sm text-red-500">{{ form.errors.type }}</p>
                            </div>
                            
                            <!-- Section -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('lessons.fields.section') || 'Section' }}
                                </label>
                                <select
                                    v-model="form.section_id"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white"
                                >
                                    <option value="">{{ t('lessons.no_section') || 'No Section' }}</option>
                                    <option v-for="section in sections" :key="section.id" :value="section.id">
                                        {{ section.translated_title || section.title }}
                                    </option>
                                </select>
                            </div>
                            
                            <!-- Order -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('lessons.fields.order') || 'Order' }} <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.order"
                                    type="number"
                                    min="0"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    :class="{ 'border-red-500': form.errors.order }"
                                />
                                <p v-if="form.errors.order" class="mt-1 text-sm text-red-500">{{ form.errors.order }}</p>
                            </div>
                            
                            <!-- Duration -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('lessons.fields.duration') || 'Duration' }} ({{ t('common.minutes') || 'minutes' }})
                                </label>
                                <input
                                    v-model="form.duration_minutes"
                                    type="number"
                                    min="0"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Content Section -->
                    <div class="pt-6 border-t border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                                </svg>
                            </div>
                            {{ t('lessons.content') || 'Content' }}
                        </h3>
                        
                        <!-- Video URL (for video types) -->
                        <div v-if="['youtube_video', 'google_drive_video', 'embed_frame'].includes(form.type)" class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('lessons.fields.video_url') || 'Video/Embed URL' }}
                            </label>
                            <input
                                v-model="form.video_url"
                                type="url"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                :placeholder="t('lessons.placeholders.video_url') || 'https://...'"
                            />
                        </div>
                        
                        <!-- Description English -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('lessons.fields.description') || 'Description' }} (English)
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                :placeholder="t('lessons.placeholders.description') || 'Enter lesson description'"
                            ></textarea>
                        </div>
                        
                        <!-- Description Arabic -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('lessons.fields.description') || 'Description' }} (العربية)
                            </label>
                            <textarea
                                v-model="form.description_ar"
                                rows="3"
                                dir="rtl"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                :placeholder="'أدخل وصف الدرس'"
                            ></textarea>
                        </div>
                        
                        <!-- Content English (for text type) -->
                        <div v-if="form.type === 'text'" class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('lessons.fields.content') || 'Content' }} (English)
                            </label>
                            <textarea
                                v-model="form.content"
                                rows="8"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none font-mono text-sm"
                                :placeholder="t('lessons.placeholders.content') || 'Enter lesson content (supports HTML)'"
                            ></textarea>
                        </div>
                        
                        <!-- Content Arabic (for text type) -->
                        <div v-if="form.type === 'text'">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('lessons.fields.content') || 'Content' }} (العربية)
                            </label>
                            <textarea
                                v-model="form.content_ar"
                                rows="8"
                                dir="rtl"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none font-mono text-sm"
                                :placeholder="'أدخل محتوى الدرس'"
                            ></textarea>
                        </div>
                    </div>

                </div>

                <!-- Form Actions -->
                <div class="px-8 py-6 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-4">
                    <Link
                        :href="route('admin.courses.lessons.index', course.slug || course.id)"
                        class="px-6 py-3 text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 font-medium transition-all"
                    >
                        {{ t('common.cancel') }}
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 font-medium transition-all shadow-lg shadow-blue-500/25 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <svg v-if="form.processing" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ t('common.create') }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    sections: Array,
    lessonTypes: Array,
});

const { t } = useTranslation();
const { route } = useRoute();

const form = useForm({
    title: '',
    title_ar: '',
    type: '',
    section_id: '',
    order: 1,
    duration_minutes: 0,
    description: '',
    description_ar: '',
    content: '',
    content_ar: '',
    video_url: '',
});

const submit = () => {
    form.post(route('admin.courses.lessons.store', props.course.slug || props.course.id));
};
</script>

