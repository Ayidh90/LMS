<template>
    <Transition
        enter-active-class="transition-opacity duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="isLoading"
            class="w-full py-8"
            :dir="direction"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Skeleton Loader -->
                <div class="space-y-6 animate-pulse">
                    <!-- Header Skeleton -->
                    <div class="space-y-3">
                        <div class="h-8 bg-gray-200 rounded-lg w-3/4"></div>
                        <div class="h-4 bg-gray-200 rounded-lg w-1/2"></div>
                    </div>

                    <!-- Content Cards Skeleton -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div
                            v-for="i in skeletonCount"
                            :key="i"
                            class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4"
                        >
                            <!-- Card Header -->
                            <div class="h-6 bg-gray-200 rounded w-2/3"></div>
                            <!-- Card Content -->
                            <div class="space-y-2">
                                <div class="h-4 bg-gray-200 rounded w-full"></div>
                                <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                                <div class="h-4 bg-gray-200 rounded w-4/6"></div>
                            </div>
                            <!-- Card Footer -->
                            <div class="flex gap-2 pt-4">
                                <div class="h-8 bg-gray-200 rounded w-20"></div>
                                <div class="h-8 bg-gray-200 rounded w-20"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Table Skeleton (alternative layout) -->
                    <div v-if="showTable" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="space-y-4">
                            <!-- Table Header -->
                            <div class="flex gap-4 pb-4 border-b border-gray-200">
                                <div class="h-5 bg-gray-200 rounded w-24"></div>
                                <div class="h-5 bg-gray-200 rounded w-32"></div>
                                <div class="h-5 bg-gray-200 rounded w-28"></div>
                                <div class="h-5 bg-gray-200 rounded w-20"></div>
                            </div>
                            <!-- Table Rows -->
                            <div
                                v-for="i in 5"
                                :key="i"
                                class="flex gap-4 py-3 border-b border-gray-100"
                            >
                                <div class="h-4 bg-gray-200 rounded w-24"></div>
                                <div class="h-4 bg-gray-200 rounded w-32"></div>
                                <div class="h-4 bg-gray-200 rounded w-28"></div>
                                <div class="h-4 bg-gray-200 rounded w-20"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<!--
ContentLoader Component
Usage: Add <ContentLoader /> to any page where you want to show a content skeleton loader.
Props:
  - loading: Boolean to control when loader shows (if not provided, shows during navigation)
  - skeletonCount: Number of skeleton cards to show (default: 6)
  - showTable: Show table skeleton instead of cards (default: false)

Example:
  <ContentLoader :loading="isLoadingData" />
  <ContentLoader :skeleton-count="9" />
  <ContentLoader :show-table="true" />
-->
<script setup>
import { computed } from 'vue';
import { useDirection } from '../composables/useDirection';
import { useNavigationLoader } from '../composables/useNavigationLoader';
import { useDataLoader } from '../composables/useDataLoader';

const { direction } = useDirection();
const { isNavigating } = useNavigationLoader();
const { isDataLoading } = useDataLoader();

// Props to customize loader
const props = defineProps({
    loading: {
        type: Boolean,
        default: null // null means auto-detect from navigation/data loading
    },
    skeletonCount: {
        type: Number,
        default: 6
    },
    showTable: {
        type: Boolean,
        default: false
    }
});

// Determine if loader should show
const isLoading = computed(() => {
    // If loading prop is explicitly provided, use it
    if (props.loading !== null) {
        return props.loading;
    }
    // Otherwise, show during navigation or data loading
    return isNavigating.value || isDataLoading.value;
});

const skeletonCount = computed(() => props.skeletonCount);
const showTable = computed(() => props.showTable);
</script>

<style scoped>
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>

