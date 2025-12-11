<template>
    <div class="admin-layout-wrapper" :class="[direction === 'rtl' ? 'is-rtl' : 'is-ltr']" :dir="direction">
        <!-- Sidebar -->
        <AdminSidebar v-if="!isModalOpen" />

        <!-- Main Content -->
        <div class="admin-main-content" :class="{ 
            'sidebar-expanded': !sidebarMinimized && !isModalOpen, 
            'sidebar-minimized': sidebarMinimized && !isModalOpen,
            'sidebar-hidden': isModalOpen
        }">
            <!-- Top Bar -->
            <header class="admin-header">
                <div class="admin-header-container">
                    <div class="admin-header-content">
                        <div class="admin-header-left">
                            <!-- Mobile Menu Toggle Button -->
                            <button
                                @click="toggleSidebar"
                                type="button"
                                class="admin-menu-toggle"
                                :aria-label="mobileSidebarOpen ? t('common.close_menu') || 'Close menu' : t('common.open_menu') || 'Open menu'"
                                :aria-expanded="mobileSidebarOpen"
                            >
                                <svg v-if="!mobileSidebarOpen" class="admin-menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <svg v-else class="admin-menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <h1 class="admin-header-title">{{ resolvedTitle }}</h1>
                        </div>
                        <div class="admin-header-right">
                            <LanguageSwitcher />
                            <UserDropdown />
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="admin-main">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, provide } from 'vue';
import { useDirection } from '@/composables/useDirection';
import { useTranslation } from '@/composables/useTranslation';
import { provideModal } from '@/composables/useModal';
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

// Provide modal state to child components
const { isModalOpen } = provideModal();

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
/* Admin Layout Wrapper */
.admin-layout-wrapper {
    min-height: 100vh;
    background-color: #f8f9fa;
    position: relative;
    width: 100%;
}

/* Main Content Area */
.admin-main-content {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    transition: margin 0.3s ease;
    width: auto;
}

/* Both RTL and LTR - Sidebar is on the right side, content margin on right */
.admin-main-content.sidebar-expanded {
    margin-right: 260px;
    margin-left: 0;
}

.admin-main-content.sidebar-minimized {
    margin-right: 75px;
    margin-left: 0;
}

.admin-main-content.sidebar-hidden {
    margin-right: 0;
    margin-left: 0;
}

/* Mobile - no margin */
@media (max-width: 991px) {
    .admin-main-content {
        margin-right: 0 !important;
        margin-left: 0 !important;
    }
}

/* Admin Header */
.admin-header {
    background-color: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    position: sticky;
    top: 0;
    z-index: 40;
    border-bottom: 1px solid #e9ecef;
}

.admin-header-container {
    padding: 0 1rem;
}

@media (min-width: 640px) {
    .admin-header-container {
        padding: 0 1.5rem;
    }
}

@media (min-width: 1024px) {
    .admin-header-container {
        padding: 0 2rem;
    }
}

.admin-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 64px;
}

.admin-header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
    min-width: 0;
}

.admin-header-right {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-shrink: 0;
    }

/* Menu Toggle Button */
.admin-menu-toggle {
    padding: 0.5rem;
    border-radius: 0.375rem;
    color: #6b7280;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

@media (min-width: 992px) {
    .admin-menu-toggle {
        display: none;
    }
}

.admin-menu-toggle:hover {
    color: #374151;
    background-color: #f3f4f6;
}

.admin-menu-toggle:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

.admin-menu-icon {
    width: 1.5rem;
    height: 1.5rem;
}

/* Header Title */
.admin-header-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

@media (min-width: 640px) {
    .admin-header-title {
        font-size: 1.25rem;
    }
}

/* Main Content Area */
.admin-main {
    flex: 1;
    padding: 1.25rem;
    width: 100%;
    max-width: 100%;
    overflow-x: hidden;
    background-color: #f8f9fa;
    }

@media (min-width: 640px) {
    .admin-main {
        padding: 1.5rem;
    }
}

@media (min-width: 1024px) {
    .admin-main {
        padding: 1.75rem 2rem;
    }
}

/* Ensure content doesn't overflow */
.admin-main > * {
    max-width: 100%;
}

/* Smooth transitions */
.admin-main-content {
    transition: margin 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>

