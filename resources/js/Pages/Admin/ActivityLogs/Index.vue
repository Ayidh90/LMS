<template>
    <AdminLayout :page-title="t('admin.activity_logs')">
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ t('admin.activity_logs') }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ t('activity.description') || 'Track all system activities and changes' }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <select
                        v-model="typeFilter"
                        @change="applyFilters"
                        class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-sm"
                    >
                        <option value="">{{ t('activity.all_types') || 'All Types' }}</option>
                        <option value="user_created">{{ t('activity.types.user_created') }}</option>
                        <option value="course_created">{{ t('activity.types.course_created') }}</option>
                        <option value="enrollment">{{ t('activity.types.enrollment') }}</option>
                    </select>
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <div v-if="activities.length > 0" class="relative">
                        <!-- Timeline Line -->
                        <div class="absolute top-0 bottom-0 right-6 w-0.5 bg-gray-200"></div>

                        <!-- Activity Items -->
                        <div class="space-y-6">
                            <div
                                v-for="activity in activities"
                                :key="activity.id"
                                class="relative flex items-start gap-4"
                            >
                                <!-- Timeline Dot -->
                                <div class="absolute right-4 w-4 h-4 rounded-full border-2 border-white shadow-sm z-10"
                                    :class="getActivityDotColor(activity.type)">
                                </div>

                                <!-- Activity Card -->
                                <div class="flex-1 ml-12 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                                                :class="getActivityIconBg(activity.type)">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getActivityIcon(activity.type)" />
                                                </svg>
                                            </div>
                                            <div>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mb-1"
                                                    :class="getActivityBadgeClass(activity.type)">
                                        {{ getActivityTypeLabel(activity.type) }}
                                    </span>
                                                <p class="text-sm font-medium text-gray-900">{{ activity.name || activity.description }}</p>
                                                <p v-if="activity.user" class="text-xs text-gray-500 mt-1">
                                                    {{ t('common.by') }} {{ activity.user.name }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-left">
                                            <p class="text-xs text-gray-500">{{ formatDate(activity.created_at) }}</p>
                                            <p class="text-xs text-gray-400">{{ formatTime(activity.created_at) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="text-center py-12">
                        <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ t('activity.no_activities') }}</h3>
                        <p class="text-gray-500">{{ t('activity.no_activities_description') || 'No activities recorded yet' }}</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="activities.length > 0" class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-600">
                            {{ t('activity.showing_count', { count: activities.length }) || `Showing ${activities.length} activities` }}
                        </p>
                        <button
                            v-if="hasMore"
                            @click="loadMore"
                            class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                        >
                            {{ t('common.load_more') || 'Load More' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { router } from '@inertiajs/vue3';

defineProps({
    activities: {
        type: Array,
        default: () => [],
    },
});

const { t } = useTranslation();
const { route } = useRoute();

const typeFilter = ref('');
const hasMore = ref(false);

const applyFilters = () => {
    const params = {};
    if (typeFilter.value) params.type = typeFilter.value;
    
    router.get(route('admin.activity-logs.index'), params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const loadMore = () => {
    // Implement load more logic
};

const getActivityTypeLabel = (type) => {
    const labels = {
        'user_created': t('activity.types.user_created'),
        'course_created': t('activity.types.course_created'),
        'enrollment': t('activity.types.enrollment'),
        'login': t('activity.types.login') || 'Login',
        'logout': t('activity.types.logout') || 'Logout',
    };
    return labels[type] || type;
};

const getActivityDotColor = (type) => {
    const colors = {
        'user_created': 'bg-blue-500',
        'course_created': 'bg-emerald-500',
        'enrollment': 'bg-purple-500',
        'login': 'bg-cyan-500',
        'logout': 'bg-gray-500',
    };
    return colors[type] || 'bg-gray-400';
};

const getActivityIconBg = (type) => {
    const colors = {
        'user_created': 'bg-gradient-to-br from-blue-500 to-blue-700',
        'course_created': 'bg-gradient-to-br from-emerald-500 to-emerald-700',
        'enrollment': 'bg-gradient-to-br from-purple-500 to-purple-700',
        'login': 'bg-gradient-to-br from-cyan-500 to-cyan-700',
        'logout': 'bg-gradient-to-br from-gray-500 to-gray-700',
    };
    return colors[type] || 'bg-gradient-to-br from-gray-500 to-gray-700';
};

const getActivityBadgeClass = (type) => {
    const classes = {
        'user_created': 'bg-blue-50 text-blue-700',
        'course_created': 'bg-emerald-50 text-emerald-700',
        'enrollment': 'bg-purple-50 text-purple-700',
        'login': 'bg-cyan-50 text-cyan-700',
        'logout': 'bg-gray-50 text-gray-700',
    };
    return classes[type] || 'bg-gray-50 text-gray-700';
};

const getActivityIcon = (type) => {
    const icons = {
        'user_created': 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
        'course_created': 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        'enrollment': 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
        'login': 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1',
        'logout': 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
    };
    return icons[type] || 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString();
};

const formatTime = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};
</script>
