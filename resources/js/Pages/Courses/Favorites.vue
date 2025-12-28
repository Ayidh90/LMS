<template>
    <component :is="layout">
        <div class="min-h-screen bg-slate-100" :dir="direction">
            <!-- Top Bar -->
            <div class="bg-slate-800 text-white py-2 text-sm">
                <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            +1(323) 555-9876
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            info@lms.com
                        </span>
                    </div>
                </div>
            </div>

            <!-- Hero Section -->
            <div class="relative bg-gradient-to-r from-slate-700 to-slate-800 py-16 overflow-hidden">
                <div class="absolute inset-0 opacity-10 bg-pattern"></div>
                <div class="max-w-7xl mx-auto px-4 relative z-10">
                    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                        </div>
                        <nav class="text-sm text-gray-500 mb-2">
                            <Link :href="route('welcome')" class="hover:text-blue-600">LMS</Link>
                            <span class="mx-2">›</span>
                            <span class="text-gray-900">{{ t('favorites.title') }}</span>
                        </nav>
                        <h1 class="text-2xl font-bold text-gray-900">{{ t('favorites.title') }}</h1>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto py-8 px-4">
                <div v-if="favorites.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <div v-for="course in favorites.data" :key="course.id" class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition-all duration-300 group">
                        <div class="relative overflow-hidden">
                            <img :src="getCourseImage(course)" :alt="course.translated_title || course.title" class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-300" @error="handleImageError($event)" />
                            <button
                                @click="toggleFavorite(course)"
                                class="absolute top-3 right-3 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-red-50 transition-colors"
                            >
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <div class="p-5">
                            <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 min-h-[3rem] group-hover:text-blue-600 transition-colors">
                                {{ course.translated_title || course.title }}
                            </h3>
                            <div v-if="course.course_type" class="mb-2">
                                <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold uppercase">
                                    {{ t(`courses.types.${course.course_type}`) || course.course_type }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    {{ getInitials(course.instructor?.name) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ course.instructor?.name || t('courses.unknown_instructor') }}</p>
                                    <p class="text-xs text-gray-500">{{ course.category?.translated_name || course.category?.name || 'Web Development' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                <span class="text-lg font-bold text-blue-600">
                                </span>
                                <div class="flex items-center text-gray-500 text-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ course.duration_hours || 0 }} {{ t('courses.hours') }}
                                </div>
                            </div>
                        </div>
                        <Link :href="route('courses.show', course.slug || course.id)" class="block w-full py-3 bg-gray-50 text-center text-blue-600 font-medium hover:bg-blue-600 hover:text-white transition-colors">
                            {{ t('courses.actions.view') }}
                        </Link>
                    </div>
                </div>

                <div v-else class="text-center py-16 bg-white rounded-xl">
                    <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <p class="text-gray-500 text-lg mb-4">{{ t('favorites.no_favorites') }}</p>
                    <Link :href="route('welcome')" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                        {{ t('favorites.browse_courses') }}
                    </Link>
                </div>

                <!-- Pagination -->
                <div v-if="favorites.links && favorites.links.length > 3" class="mt-8 flex justify-center">
                    <nav class="flex items-center gap-1">
                        <template v-for="(link, index) in favorites.links" :key="index">
                            <button v-if="index === 0" @click="link.url && router.visit(link.url)" :disabled="!link.url" class="px-3 py-2 rounded-lg border border-gray-300 text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button v-else-if="index === favorites.links.length - 1" @click="link.url && router.visit(link.url)" :disabled="!link.url" class="px-3 py-2 rounded-lg border border-gray-300 text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <button v-else @click="link.url && router.visit(link.url)" :class="link.active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'" class="px-4 py-2 rounded-lg border font-medium transition-colors">
                                {{ link.label }}
                            </button>
                        </template>
                    </nav>
                </div>
            </div>
        </div>
    </component>
</template>

<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useDirection } from '@/composables/useDirection';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useAlert } from '@/composables/useAlert';
import { Link, usePage, router } from '@inertiajs/vue3';

defineProps({
    favorites: Object,
});

const { direction } = useDirection();
const { t } = useTranslation();
const { route } = useRoute();
const { showSuccess, showError } = useAlert();
const page = usePage();

const auth = computed(() => page.props.auth?.user);
const layout = computed(() => auth.value ? AuthenticatedLayout : AppLayout);

// Default course image
const defaultCourseImage = '/images/default-course.avif';

const getCourseImage = (course) => {
    return course.thumbnail_url || course.thumbnail || defaultCourseImage;
};

const handleImageError = (event) => {
    if (event.target.src !== defaultCourseImage) {
        event.target.src = defaultCourseImage;
    }
};

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};

const toggleFavorite = (course) => {
    router.post(route('courses.favorite.toggle', { course: course.slug || course.id }), {}, {
        preserveScroll: true,
        onSuccess: (page) => {
            const isFavoritedNow = page.props.isFavorited || false;
            const favoriteKey = isFavoritedNow ? 'courses.favorite_added' : 'courses.favorite_removed';
            const message = t(favoriteKey) || (isFavoritedNow ? 'Course added to favorites!' : 'Course removed from favorites');
            
            showSuccess(message, t('common.success') || 'Success');
            router.reload({ only: ['favorites'] });
        },
        onError: (errors) => {
            const message = errors.message || t('courses.favorite_failed') || 'Failed to update favorite';
            showError(message, t('common.error') || 'Error');
        },
    });
};
</script>

