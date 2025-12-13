<template>
    <div v-if="auth" class="position-relative user-dropdown-container" :dir="direction">
        <!-- Expanded State -->
        <div v-if="!minimized" class="w-100">
            <button
                ref="expandedButtonRef"
                @click.stop="toggleDropdown"
                class="btn btn-link text-decoration-none w-100 d-flex align-items-center gap-2 px-2 py-2 rounded user-dropdown-btn"
                :class="{ 'bg-light': showDropdown }"
            >
                <div class="position-relative flex-shrink-0">
                    <div
                        class="bg-primary bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                        style="width: 40px; height: 40px; font-size: 0.875rem"
                    >
                        {{ getInitials(auth?.name) }}
                    </div>
                    <span
                        class="position-absolute bottom-0 end-0 translate-middle bg-success border-2 border-white rounded-circle"
                        style="width: 12px; height: 12px"
                    ></span>
                </div>
                <div class="flex-grow-1 min-w-0 text-start">
                    <p class="text-muted small mb-0" style="font-size: 0.75rem">
                        {{ t("admin.welcome_back") }}
                    </p>
                    <p
                        class="small fw-semibold text-dark text-truncate mb-0"
                        style="font-size: 0.875rem"
                    >
                        {{ auth?.name }}
                    </p>
                </div>
                <i
                    class="bi bi-chevron-down text-secondary small"
                    :class="{ 'rotate-180': showDropdown }"
                    style="transition: transform 0.2s ease"
                ></i>
            </button>

            <!-- Dropdown Menu -->
            <Teleport to="body">
                <div
                    v-if="showDropdown"
                    class="position-fixed bg-white rounded shadow-lg border border-gray-200 overflow-hidden user-dropdown-menu"
                    :style="expandedDropdownStyle"
                >
                <div class="p-3 border-bottom border-gray-200 bg-white">
                    <p class="small fw-semibold text-dark mb-0">{{ auth?.name }}</p>
                    <p class="text-muted small mb-0" style="font-size: 0.75rem">{{ auth?.email }}</p>
                </div>
                
                <div class="py-1 bg-white">
                    <Link
                        v-if="showProfile"
                        :href="route('profile.show')"
                        class="d-flex align-items-center gap-3 px-3 py-2 small text-dark text-decoration-none hover-bg-light transition-colors bg-white"
                        @click="closeDropdown"
                    >
                        <i class="bi bi-person text-secondary"></i>
                        <span>{{ t('profile.my_profile') }}</span>
                    </Link>
                    
                    <Link
                        v-if="showDashboard"
                        :href="dashboardRoute"
                        class="d-flex align-items-center gap-3 px-3 py-2 small text-dark text-decoration-none hover-bg-light transition-colors bg-white"
                        @click="closeDropdown"
                    >
                        <i class="bi bi-house text-secondary"></i>
                        <span>{{ t('common.dashboard') }}</span>
                    </Link>
                </div>
                
                <div class="border-top border-gray-200 py-1 bg-white">
                    <button
                        @click="logout"
                        class="w-100 d-flex align-items-center gap-3 px-3 py-2 small text-danger text-decoration-none border-0 bg-white hover-bg-light transition-colors"
                    >
                        <i class="bi bi-box-arrow-right"></i>
                        <span>{{ t('common.logout') }}</span>
                    </button>
                </div>
                </div>
            </Teleport>
        </div>

        <!-- Minimized State -->
        <div v-else class="d-flex justify-content-center">
            <button
                ref="buttonRef"
                @click.stop="toggleDropdown"
                class="btn btn-link text-decoration-none p-0 position-relative user-dropdown-btn-minimized"
                :class="{ 'bg-light rounded-circle': showDropdown }"
                style="width: 40px; height: 40px;"
            >
                <div
                    class="bg-primary bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm mx-auto"
                    style="width: 40px; height: 40px; font-size: 0.875rem"
                >
                    {{ getInitials(auth?.name) }}
                </div>
                <span
                    class="position-absolute bottom-0 end-0 translate-middle bg-success border-2 border-white rounded-circle"
                    style="width: 10px; height: 10px"
                ></span>
            </button>

            <!-- Dropdown Menu for Minimized State -->
            <Teleport to="body">
                <div
                    v-if="showDropdown"
                    class="position-fixed bg-white rounded shadow-lg border border-gray-200 overflow-hidden user-dropdown-menu-minimized"
                    :style="minimizedDropdownStyle"
                >
                <div class="p-3 border-bottom border-gray-200 bg-white">
                    <p class="small fw-semibold text-dark mb-0">{{ auth?.name }}</p>
                    <p class="text-muted small mb-0" style="font-size: 0.75rem">{{ auth?.email }}</p>
                </div>
                
                <div class="py-1 bg-white">
                    <Link
                        v-if="showProfile"
                        :href="route('profile.show')"
                        class="d-flex align-items-center gap-3 px-3 py-2 small text-dark text-decoration-none hover-bg-light transition-colors bg-white"
                        @click="closeDropdown"
                    >
                        <i class="bi bi-person text-secondary"></i>
                        <span>{{ t('profile.my_profile') }}</span>
                    </Link>
                    
                    <Link
                        v-if="showDashboard"
                        :href="dashboardRoute"
                        class="d-flex align-items-center gap-3 px-3 py-2 small text-dark text-decoration-none hover-bg-light transition-colors bg-white"
                        @click="closeDropdown"
                    >
                        <i class="bi bi-house text-secondary"></i>
                        <span>{{ t('common.dashboard') }}</span>
                    </Link>
                </div>
                
                <div class="border-top border-gray-200 py-1 bg-white">
                    <button
                        @click="logout"
                        class="w-100 d-flex align-items-center gap-3 px-3 py-2 small text-danger text-decoration-none border-0 bg-white hover-bg-light transition-colors"
                    >
                        <i class="bi bi-box-arrow-right"></i>
                        <span>{{ t('common.logout') }}</span>
                    </button>
                </div>
                </div>
            </Teleport>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, defineProps, defineExpose, watch, Teleport } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import { useDirection } from '@/composables/useDirection';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';

const props = defineProps({
    minimized: {
        type: Boolean,
        default: false
    }
});

const { direction } = useDirection();
const { t } = useTranslation();
const { route } = useRoute();
const page = usePage();

const showDropdown = ref(false);
const auth = computed(() => page.props.auth?.user);
const dropdownRef = ref(null);
const buttonRef = ref(null);
const expandedButtonRef = ref(null);

// Calculate position for expanded dropdown
const expandedDropdownStyle = computed(() => {
    if (props.minimized || !expandedButtonRef.value) {
        return {};
    }
    
    try {
        const buttonRect = expandedButtonRef.value.getBoundingClientRect();
        return {
            top: `${buttonRect.bottom + 8}px`,
            left: `${buttonRect.left}px`,
            width: `${buttonRect.width}px`,
            minWidth: '200px',
            zIndex: 9999
        };
    } catch (error) {
        return {
            top: '80px',
            left: '20px',
            width: '224px',
            minWidth: '200px',
            zIndex: 9999
        };
    }
});

const dashboardRoute = computed(() => {
    const user = auth.value;
    if (!user) return route('welcome');
    
    const role = user.role;
    const isAdmin = user.is_admin === 1 || user.is_admin === true;
    
    // Only admins with is_admin == 1 get dashboard, students and instructors don't have dashboard
    if ((role === 'super_admin' || role === 'admin') && isAdmin) {
        return route('admin.dashboard');
    }
    return route('welcome');
});

const showDashboard = computed(() => {
    const user = auth.value;
    if (!user) return false;
    const role = user.role;
    const isAdmin = user.is_admin === 1 || user.is_admin === true;
    // Only show dashboard link for admins with is_admin == 1
    return (role === 'super_admin' || role === 'admin') && isAdmin;
});

const showProfile = computed(() => {
    const user = auth.value;
    if (!user) return false;
    const role = user.role;
    const isAdmin = user.is_admin === 1 || user.is_admin === true;
    // Hide profile link for admin and super_admin roles
    return !((role === 'super_admin' || role === 'admin') && isAdmin);
});

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
};

const closeDropdown = () => {
    showDropdown.value = false;
};

const logout = () => {
    router.post(route('logout'));
};

// Calculate position for minimized dropdown
const minimizedDropdownStyle = computed(() => {
    if (!props.minimized || !buttonRef.value) {
        return {};
    }
    
    try {
        const buttonRect = buttonRef.value.getBoundingClientRect();
        return {
            top: `${buttonRect.bottom + 8}px`,
            left: `${buttonRect.left}px`,
            width: '224px',
            minWidth: '224px',
            zIndex: 9999
        };
    } catch (error) {
        return {
            top: '80px',
            left: '20px',
            width: '224px',
            minWidth: '224px',
            zIndex: 9999
        };
    }
});

const handleClickOutside = (event) => {
    // Check if click is on the dropdown button or inside the dropdown menu
    const container = event.target.closest('.user-dropdown-container');
    const dropdownMenu = event.target.closest('.user-dropdown-menu, .user-dropdown-menu-minimized');
    
    // If click is inside container or dropdown menu, don't close
    if (container || dropdownMenu) {
        return;
    }
    
    // Close dropdown if click is outside
    showDropdown.value = false;
    
    // Also check if click is on the sidebar toggle button (should close dropdown)
    const sidebarToggle = event.target.closest('#kt_app_sidebar_toggle');
    if (sidebarToggle) {
        showDropdown.value = false;
    }
};

// Watch for minimized prop changes and close dropdown
watch(() => props.minimized, (newVal) => {
    if (newVal) {
        showDropdown.value = false;
    }
});

// Expose closeDropdown method for parent component
defineExpose({
    closeDropdown
});

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<style scoped>
.user-dropdown-container {
    z-index: 10;
    position: relative;
    isolation: isolate;
}

.user-dropdown-btn {
    border: none;
    background: transparent;
    transition: background-color 0.2s ease;
}

.user-dropdown-btn:hover {
    background-color: #f8f9fa !important;
}

.user-dropdown-btn-minimized {
    border: none;
    background: transparent;
    transition: background-color 0.2s ease;
}

.user-dropdown-btn-minimized:hover {
    background-color: #f8f9fa !important;
}

.user-dropdown-menu {
    animation: slideDown 0.2s ease-out;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    background: white !important;
    position: relative;
    isolation: isolate;
}

.user-dropdown-menu-minimized {
    animation: slideDown 0.2s ease-out;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    background: white !important;
    position: relative;
    isolation: isolate;
}

.hover-bg-light:hover {
    background-color: #f8f9fa !important;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.rotate-180 {
    transform: rotate(180deg);
}

/* Ensure dropdown doesn't overflow sidebar */
.user-dropdown-menu {
    max-width: calc(100vw - 20px);
}

@media (max-width: 991px) {
    .user-dropdown-menu-minimized {
        left: 20px !important;
        right: 20px !important;
        width: auto !important;
        min-width: auto !important;
    }
}
</style>

