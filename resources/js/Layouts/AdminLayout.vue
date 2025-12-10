<template>
    <div class="min-h-screen bg-gray-50" :dir="direction">
        <!-- Sidebar -->
        <AdminSidebar />

        <!-- Main Content -->
        <div class="flex flex-col flex-1" :class="{ 'lg:mr-[260px]': !sidebarMinimized, 'lg:mr-[75px]': sidebarMinimized }">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm sticky top-0 z-40">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <!-- Mobile Menu Toggle Button -->
                            <button
                                @click="toggleSidebar"
                                type="button"
                                class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 lg:hidden transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                :aria-label="mobileSidebarOpen ? t('common.close_menu') || 'Close menu' : t('common.open_menu') || 'Open menu'"
                                :aria-expanded="mobileSidebarOpen"
                            >
                                <svg v-if="!mobileSidebarOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <h1 class="ml-4 text-xl font-semibold text-gray-900">{{ resolvedTitle }}</h1>
                        </div>
                        <div class="flex items-center gap-4">
                            <LanguageSwitcher />
                            <UserDropdown />
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, provide } from 'vue';
import { useDirection } from '@/composables/useDirection';
import { useTranslation } from '@/composables/useTranslation';
import AdminSidebar from '@/Components/AdminSidebar.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import UserDropdown from '@/Components/UserDropdown.vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    pageTitle: {
        type: String,
        default: ''
    }
});

const { direction } = useDirection();
const { t } = useTranslation();
const page = usePage();

const resolvedTitle = computed(() => props.pageTitle || t('admin.dashboard'));

const sidebarMinimized = ref(false);
const mobileSidebarOpen = ref(false);

const toggleSidebar = () => {
    mobileSidebarOpen.value = !mobileSidebarOpen.value;
    // Prevent body scroll when sidebar is open on mobile
    if (mobileSidebarOpen.value) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
};

const toggleMinimize = () => {
    sidebarMinimized.value = !sidebarMinimized.value;
    document.body.classList.toggle('app-sidebar-minimize');
};

provide('sidebarMinimized', sidebarMinimized);
provide('mobileSidebarOpen', mobileSidebarOpen);
provide('toggleSidebar', toggleSidebar);
provide('toggleMinimize', toggleMinimize);
</script>

<style scoped>
/* Sidebar transition styles */
@media (min-width: 1024px) {
    .lg\:mr-\[260px\] {
        margin-right: 260px;
    }
    .lg\:mr-\[75px\] {
        margin-right: 75px;
    }
}
</style>

