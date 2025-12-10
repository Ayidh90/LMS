<template>
    <component :is="layout">
        <div class="min-h-screen bg-slate-100 flex items-center justify-center" :dir="direction">
            <div class="text-center px-4">
                <div class="max-w-md mx-auto">
                    <svg class="mx-auto h-24 w-24 text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ t('courses.not_found_title') }}</h1>
                    <p class="text-lg text-gray-600 mb-8">{{ t('courses.not_found_message') }}</p>
                    <div class="flex justify-center gap-4">
                        <Link
                            :href="route('welcome')"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors"
                        >
                            {{ t('courses.browse_all_courses') }}
                        </Link>
                        <Link
                            :href="route('welcome')"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors"
                        >
                            {{ t('common.home') }}
                        </Link>
                    </div>
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
import { Link, usePage } from '@inertiajs/vue3';

const { direction } = useDirection();
const { t } = useTranslation();
const { route } = useRoute();
const page = usePage();

const auth = computed(() => page.props.auth?.user);
const layout = computed(() => auth.value ? AuthenticatedLayout : AppLayout);
</script>

