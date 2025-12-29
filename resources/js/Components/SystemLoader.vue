<template>
    <Transition
        enter-active-class="transition-opacity duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="isLoading"
            class="fixed inset-0 z-[9999] bg-white/80 backdrop-blur-sm flex items-center justify-center cursor-pointer"
            :dir="direction"
        >
            <div class="flex items-center justify-center cursor-pointer">
                <!-- Spinner -->
                <div class="relative cursor-pointer">
                    <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin cursor-pointer"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-8 h-8 bg-blue-600 rounded-full opacity-20 cursor-pointer"></div>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { computed, watch, onUnmounted } from 'vue';
import { useDirection } from '../composables/useDirection';
import { useNavigationLoader } from '../composables/useNavigationLoader';

const { direction } = useDirection();
const { isNavigating } = useNavigationLoader();

const isLoading = computed(() => isNavigating.value);

// Update body cursor when loading
watch(isLoading, (loading) => {
    if (loading) {
        document.body.style.cursor = 'pointer';
    } else {
        document.body.style.cursor = '';
    }
});

onUnmounted(() => {
    document.body.style.cursor = '';
});
</script>

<style scoped>
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>

