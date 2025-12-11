<template>
    <div
        id="kt_app_sidebar"
        class="app-sidebar d-flex flex-column position-fixed top-0 h-100 bg-white"
        :class="{ 
            'sidebar-expanded': !minimized, 
            'sidebar-minimized': minimized, 
            'sidebar-hidden': !mobileOpen, 
            'sidebar-visible': mobileOpen
        }"
        :style="{ width: minimized ? '75px' : '260px' }"
    >
        <!-- Header Section - Bootstrap Style -->
        <div class="app-sidebar-header p-3 border-bottom position-relative bg-white">
            <!-- Sidebar Title -->
            <div v-if="!minimized" class="sidebar-title-wrapper mb-3 position-relative">
                <h5 class="sidebar-title text-dark fw-bold mb-0 d-flex align-items-center gap-2">
                   
                    <span class="sidebar-title-text">{{ websiteName }}</span>
                </h5>
            </div>
            
            <!-- Toggle Button -->
            <button
                id="kt_app_sidebar_toggle"
                @click.stop="handleToggleMinimize"
                @keydown.enter.prevent.stop="handleToggleMinimize"
                @keydown.space.prevent.stop="handleToggleMinimize"
                type="button"
                class="btn btn-sm position-absolute sidebar-toggle-btn d-none d-lg-flex align-items-center justify-content-center"
                :class="{ 
                    'sidebar-toggle-minimized': minimized,
                    'sidebar-toggle-expanded': !minimized
                }"
                :title="minimized ? t('common.expand') : t('common.collapse')"
                :aria-label="minimized ? t('common.expand') : t('common.collapse')"
                :aria-expanded="!minimized"
                style="width: 32px; height: 32px; z-index: 50; transition: all 0.3s ease;"
            >
                <i class="bi sidebar-toggle-icon text-secondary" :class="minimized ? 'bi-chevron-left' : 'bi-chevron-right'"></i>
            </button>
            
            <!-- User Info Card - Bootstrap Style -->
            <div v-if="!minimized" class="user-info-card">
                <div class="d-flex align-items-center gap-3 p-2 rounded">
                    <div class="position-relative flex-shrink-0">
                        <div class="bg-primary bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 48px; height: 48px; font-size: 1rem;">
                            {{ getUserInitials() }}
                        </div>
                        <span class="position-absolute bottom-0 end-0 translate-middle bg-success border-2 border-white rounded-circle" style="width: 14px; height: 14px;"></span>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <p class="text-muted small mb-0" style="font-size: 0.75rem;">{{ t('admin.welcome_back') }}</p>
                        <p class="small fw-semibold text-dark text-truncate mb-0" style="font-size: 0.875rem;">{{ userName }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Minimized Avatar - Bootstrap Style -->
            <div v-else class="d-flex justify-content-center pt-2">
                <div class="position-relative">
                    <div class="bg-primary bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 40px; height: 40px; font-size: 0.875rem;">
                        {{ getUserInitials() }}
                    </div>
                    <span class="position-absolute bottom-0 end-0 translate-middle bg-success border-2 border-white rounded-circle" style="width: 10px; height: 10px;"></span>
                </div>
            </div>
        </div>
        
        <!-- Logo Section (Hidden in new design) -->
        <div class="app-sidebar-logo px-6 py-3 border-b border-gray-100 relative hidden" id="kt_app_sidebar_logo">
            <Link :href="safeRoute('admin.dashboard')" class="flex items-center justify-center gap-3 hover:opacity-80 transition-opacity">
                <img
                    v-if="!minimized"
                    alt="Logo"
                    src="/images/default-course.avif"
                    class="h-10 w-auto app-sidebar-logo-default transition-all duration-300"
                    @error="handleLogoError"
                />
                <img
                    v-else
                    alt="Logo"
                    src="/images/default-course.avif"
                    class="h-8 w-auto app-sidebar-logo-minimize transition-all duration-300 mx-auto"
                    @error="handleLogoError"
                />
            </Link>
            <!-- Mobile Close Button -->
            <button
                @click="closeMobile"
                type="button"
                class="lg:hidden absolute top-4 left-4 p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors z-10"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Sidebar Menu - Bootstrap Style -->
        <div class="app-sidebar-menu flex-grow-1 overflow-hidden">
            <div
                id="kt_app_sidebar_menu_wrapper"
                class="app-sidebar-wrapper py-3 overflow-y-auto h-100 px-2"
            >
                <nav class="menu" id="kt_app_sidebar_menu">
                    <template v-for="(item, index) in menuItems" :key="index">
                        <div v-if="item">
                            <!-- Section Label -->
                            <div
                                v-if="item && item.title && !item.icon && !item.route && hasPermission(item)"
                                class="menu-section-label px-3 py-2"
                            >
                                <span v-if="!minimized" class="text-muted small fw-semibold text-uppercase">
                                    {{ item.title }}
                                </span>
                            </div>

                            <!-- Separator -->
                            <div
                                v-else-if="item.icon === 'separator' && shouldShowSeparator(index)"
                                class="menu-separator my-2"
                            >
                                <hr class="my-0">
                            </div>

                            <!-- Main menu items -->
                            <div
                                v-else-if="hasPermission(item)"
                                :class="{
                                    'menu-accordion': hasChildren(item),
                                    'show': checkRoute(item) || openSubmenus[index] || item?.show,
                                }"
                                class="menu-item"
                            >
                                <!-- Menu Item with Children (Accordion) -->
                                <a
                                    v-if="hasChildren(item)"
                                    href="#"
                                    class="menu-link d-flex align-items-center px-3 py-2 text-decoration-none rounded mb-1"
                                    :class="{
                                        'bg-primary bg-opacity-10 text-primary': checkRoute(item),
                                        'text-dark': !checkRoute(item),
                                    }"
                                    @click.prevent="toggleSubmenu(index)"
                                >
                                    <span class="menu-icon me-3 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                                        <i v-if="item.icon" :class="getBootstrapIcon(item.icon)" class="fs-6"></i>
                                    </span>
                                    <span v-if="!minimized" class="menu-title fw-medium small flex-grow-1 text-nowrap text-truncate">
                                        {{ item.title }}
                                    </span>
                                    <span v-if="item.count && !minimized" class="menu-badge ms-2">
                                        <span class="badge bg-primary">
                                            {{ item.count }}
                                        </span>
                                    </span>
                                    <span v-if="hasChildren(item) && !minimized" class="menu-arrow ms-2">
                                        <i class="bi bi-chevron-down small" :class="{ 'rotate-180': openSubmenus[index] }"></i>
                                    </span>
                                </a>
                                
                                <!-- Menu Item without Children (Link) -->
                                <Link
                                    v-else-if="item?.route"
                                    :href="safeRoute(item.route, item.routeParams || {})"
                                    class="menu-link d-flex align-items-center px-3 py-2 text-decoration-none rounded mb-1"
                                    :class="{
                                        'bg-primary bg-opacity-10 text-primary': checkRoute(item),
                                        'text-dark': !checkRoute(item),
                                    }"
                                    @click="handleMenuItemClick"
                                >
                                    <span class="menu-icon me-3 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                                        <i v-if="item.icon" :class="getBootstrapIcon(item.icon)" class="fs-6"></i>
                                    </span>
                                    <span v-if="!minimized" class="menu-title fw-medium small flex-grow-1 text-nowrap text-truncate">
                                        {{ item.title }}
                                    </span>
                                    <span v-if="item.count && !minimized" class="menu-badge ms-2">
                                        <span class="badge bg-primary">
                                            {{ item.count }}
                                        </span>
                                    </span>
                                </Link>

                                <!-- Submenu -->
                                <div
                                    v-if="hasChildren(item) && (openSubmenus[index] || item.show || checkRoute(item))"
                                    class="menu-sub mt-1 ms-4"
                                >
                                    <template v-for="(subItem, subIndex) in item.children" :key="subIndex">
                                        <div v-if="hasPermission(subItem)" class="menu-item">
                                            <Link
                                                v-if="subItem && subItem.route"
                                                class="menu-link d-flex align-items-center px-3 py-2 small text-decoration-none rounded"
                                                :class="{
                                                    'bg-primary bg-opacity-10 text-primary': safeRouteCurrent(subItem.route, subItem.routeParams || {}),
                                                    'text-muted': !safeRouteCurrent(subItem.route, subItem.routeParams || {}),
                                                }"
                                                :href="subItem?.route ? safeRoute(subItem.route, subItem.routeParams || {}) : '#'"
                                                @click="typeof window !== 'undefined' && window.innerWidth < 1024 ? closeMobile() : null"
                                            >
                                                <span class="menu-bullet me-3 d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-circle-fill" style="font-size: 0.375rem;"></i>
                                                </span>
                                                <span class="menu-title fw-medium flex-grow-1">
                                                    {{ subItem.title }}
                                                </span>
                                                <span
                                                    v-if="subItem.count || subItem.count === null"
                                                    class="menu-badge ms-2"
                                                >
                                                    <span
                                                        v-if="subItem.count === null"
                                                        class="badge bg-primary rounded-pill"
                                                        style="width: 8px; height: 8px; padding: 0;"
                                                    ></span>
                                                    <span
                                                        v-else
                                                        class="badge bg-primary"
                                                    >{{ subItem.count }}</span>
                                                </span>
                                            </Link>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </nav>
            </div>
        </div>
    </div>

    <!-- Mobile Overlay - Bootstrap Style -->
    <div
        v-if="mobileOpen"
        @click="closeMobile"
        class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 z-40 d-lg-none"
        style="transition: opacity 0.3s;"
    ></div>
</template>

<script setup>
import { computed, ref, inject, watch, onMounted, onUnmounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';
import { usePermissions } from '@/composables/usePermissions';
import { Ziggy } from '../ziggy';
import { route as ziggyRoute } from '../../../vendor/tightenco/ziggy/dist/index.esm.js';

const { t } = useTranslation();
const page = usePage();
const { can: canPermission } = usePermissions();

// Route function - use window.route (set globally in app.js) or Ziggy directly
const route = (name, params = {}, absolute = false) => {
    if (!name) {
        console.warn('Route name is required');
        return '#';
    }
    
    // First try window.route if available (set in app.js)
    if (typeof window !== 'undefined' && typeof window.route === 'function') {
        try {
            const result = window.route(name, params, absolute);
            if (result && result !== '#') {
                return result;
            }
        } catch (error) {
            console.warn(`Error using window.route for [${name}]:`, error);
            // Fall through to Ziggy route
        }
    }
    
    // Use Ziggy route directly
    try {
        if (!Ziggy || !Ziggy.routes) {
            console.warn('Ziggy routes not available');
            return '#';
        }
        
        if (!Ziggy.routes[name]) {
            console.warn(`Route [${name}] not found in Ziggy.routes`);
            return '#';
        }
        
        const result = ziggyRoute(name, params, absolute, Ziggy);
        if (!result || result === '#') {
            console.warn(`Route [${name}] generated empty URL`);
            return '#';
        }
        
        return result;
    } catch (error) {
        console.error(`Error generating route [${name}]:`, error);
        return '#';
    }
};

const openSubmenus = ref({});

// User information
const userName = computed(() => {
    return page.props?.auth?.user?.name || 'Admin User';
});

const getUserInitials = () => {
    const name = userName.value;
    if (!name) return 'AU';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};

// Website name from settings
const websiteName = computed(() => {
    return page.props?.settings?.website?.name || t('admin.dashboard') || 'لوحة تحكم المسؤول';
});

// Get injected values from AdminLayout (with fallback)
const sidebarMinimized = inject('sidebarMinimized', ref(false));
const mobileSidebarOpen = inject('mobileSidebarOpen', ref(false));
const toggleMinimizeFn = inject('toggleMinimize', null);

// Use injected state for minimized - remove local state to avoid conflicts
const minimized = computed(() => {
    return sidebarMinimized && typeof sidebarMinimized.value !== 'undefined' 
        ? sidebarMinimized.value 
        : false;
});

const handleToggleMinimize = (event) => {
    // Prevent event bubbling
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    // Only toggle on desktop (lg and above)
    if (typeof window !== 'undefined' && window.innerWidth < 1024) {
        return;
    }
    
    try {
        // Use the injected function if available, otherwise update directly
        if (toggleMinimizeFn && typeof toggleMinimizeFn === 'function') {
            toggleMinimizeFn();
        } else if (sidebarMinimized && typeof sidebarMinimized.value !== 'undefined') {
            sidebarMinimized.value = !sidebarMinimized.value;
            document.body.classList.toggle('app-sidebar-minimize');
        }
    } catch (error) {
        console.error('Error toggling sidebar:', error);
    }
};

// Keep the old function name for backward compatibility if needed
const toggleMinimize = handleToggleMinimize;

// Use injected mobileSidebarOpen or local state
const mobileOpen = computed(() => {
    if (mobileSidebarOpen && typeof mobileSidebarOpen.value !== 'undefined') {
        return mobileSidebarOpen.value;
    }
    return false;
});

const toggleSubmenu = (index) => {
    const currentState = openSubmenus.value[index] || false;
    openSubmenus.value[index] = !currentState;
    // Also update the item's show property for reactivity
    if (menuItems.value[index]) {
        menuItems.value[index].show = !currentState;
    }
};

const closeMobile = () => {
    if (mobileSidebarOpen && typeof mobileSidebarOpen.value !== 'undefined') {
        mobileSidebarOpen.value = false;
        document.body.style.overflow = '';
    }
};

const handleMenuItemClick = () => {
    // Close mobile sidebar on mobile devices when clicking menu items
    if (typeof window !== 'undefined' && window.innerWidth < 1024) {
        closeMobile();
    }
    // Let Inertia Link component handle the navigation automatically
    // No need to prevent default or manually navigate - Link handles it
};

const handleLogoError = (event) => {
    event.target.style.display = 'none';
};

// Bootstrap Icons mapping
const bootstrapIcons = {
    'chart-simple-3': 'bi-graph-up',
    'plus-circle': 'bi-plus-circle',
    'briefcase': 'bi-briefcase',
    'people': 'bi-people',
    'file-up': 'bi-file-earmark-arrow-up',
    'shield-cross': 'bi-shield-check',
    'file-down': 'bi-file-earmark-arrow-down',
    'abstract-30': 'bi-grid-3x3',
    'setting-4': 'bi-gear',
    'map': 'bi-map',
    'document': 'bi-file-earmark-text',
    'category': 'bi-folder',
    'key-square': 'bi-key',
    'text-circle': 'bi-file-text',
    'check-circle': 'bi-check-circle',
};

const getBootstrapIcon = (iconName) => {
    return bootstrapIcons[iconName] || bootstrapIcons['chart-simple-3'];
};

const menuItems = computed(() => {
    try {
        const itemsArray = [
            {
                title: t('common.dashboard'),
                icon: 'chart-simple-3',
                route: 'admin.dashboard',
            },
            {
                icon: 'separator',
            },
            {
                title: t('admin.courses_management'),
                permission: 'courses.view-all',
            },
            {
                title: t('admin.view_courses'),
                icon: 'briefcase',
                route: 'admin.courses.index',
                permission: 'courses.view-all',
            },
            {
                title: t('admin.create_course'),
                icon: 'plus-circle',
                route: 'admin.courses.create',
                permission: 'courses.create',
            },
            {
                icon: 'separator',
            },
            {
                title: t('admin.users_management'),
                permission: 'users.manage',
            },
            {
                title: t('admin.users'),
                icon: 'people',
                route: 'admin.users.index',
                permission: 'users.manage',
            },
            {
                icon: 'separator',
            },
            {
                title: t('admin.system_settings'),
                permission: 'role-list',
            },
            {
                title: t('admin.roles_permissions'),
                icon: 'key-square',
                route: 'admin.roles.index',
                permission: 'role-list',
            },
            {
                title: t('admin.settings') || 'Settings',
                icon: 'setting-4',
                route: 'admin.settings.index',
                permission: 'settings.view',
                // Allow admins to access settings even without explicit permission
                allowAdmin: true,
            },
            {
                title: t('admin.activity_logs'),
                icon: 'text-circle',
                route: 'admin.activity-logs.index',
                permission: 'users.manage',
            },
        ];

        return itemsArray.filter(item => item && typeof item === 'object');
    } catch (error) {
        console.error('Error building sidebar items:', error);
        return [];
    }
});

// Use the composable's can function
const can = canPermission;

const hasChildren = (item) => {
    if (!item || typeof item !== 'object') {
        return false;
    }
    return (
        item.hasOwnProperty('children') &&
        Array.isArray(item.children) &&
        item.children.length > 0
    );
};

const checkRoute = (item) => {
    if (!item || typeof item !== 'object') {
        return false;
    }
    if (!item.route) return false;
    try {
        const currentUrl = page.url;
        if (!hasChildren(item)) {
            try {
                const routeUrl = route(item.route, item.routeParams || {});
                if (!routeUrl || routeUrl === '#') return false;
                // Normalize URLs for comparison
                const normalizedCurrent = currentUrl.split('?')[0].replace(/\/$/, '');
                const normalizedRoute = routeUrl.split('?')[0].replace(/\/$/, '');
                // Compare URLs - exact match or starts with
                return normalizedCurrent === normalizedRoute || normalizedCurrent.startsWith(normalizedRoute + '/');
            } catch (e) {
                return false;
            }
        }
        if (!item.children || !Array.isArray(item.children)) {
            return false;
        }
        return item.children.some((child) => {
            if (!child || !child.route) return false;
            try {
                const routeUrl = route(child.route, child.routeParams || {});
                if (!routeUrl || routeUrl === '#') return false;
                const normalizedCurrent = currentUrl.split('?')[0].replace(/\/$/, '');
                const normalizedRoute = routeUrl.split('?')[0].replace(/\/$/, '');
                return normalizedCurrent === normalizedRoute || normalizedCurrent.startsWith(normalizedRoute + '/');
            } catch (e) {
                return false;
            }
        });
    } catch (error) {
        return false;
    }
};

const safeRoute = (routeName, routeParams = {}) => {
    if (!routeName) {
        console.warn('safeRoute: routeName is required');
        return '#';
    }
    
    try {
        const routeUrl = route(routeName, routeParams);
        if (!routeUrl || routeUrl === '#') {
            // Only warn in development
            if (process.env.NODE_ENV === 'development') {
                console.warn(`Route [${routeName}] generated empty URL or returned '#'.`);
            }
            // Fallback: try to construct URL manually for known routes
            if (routeName === 'admin.settings.index') {
                return '/admin/settings';
            }
            return '#';
        }
        return routeUrl;
    } catch (error) {
        console.error(`Error in safeRoute for [${routeName}]:`, error);
        // Fallback: try to construct URL manually for known routes
        if (routeName === 'admin.settings.index') {
            return '/admin/settings';
        }
        return '#';
    }
};

const safeRouteCurrent = (routeName, routeParams = {}) => {
    if (!routeName) {
        return false;
    }
    try {
        const currentUrl = page.url;
        const routeUrl = route(routeName, routeParams);
        if (!routeUrl || routeUrl === '#') return false;
        // Normalize URLs for comparison
        const normalizedCurrent = currentUrl.split('?')[0].replace(/\/$/, '');
        const normalizedRoute = routeUrl.split('?')[0].replace(/\/$/, '');
        // Compare URLs - exact match or starts with
        return normalizedCurrent === normalizedRoute || normalizedCurrent.startsWith(normalizedRoute + '/');
    } catch (error) {
        return false;
    }
};

const hasPermission = (item) => {
    if (!item || typeof item !== 'object') return false;
    
    const user = page.props?.auth?.user;
    
    // Super admin always sees all items
    if (user?.role === 'super_admin') {
        // For separators and labels, still check if they should be shown
        if (item.icon === 'separator' || (item.title && !item.icon && !item.route)) {
            // Let the normal logic handle separators and labels
        } else {
            return true;
        }
    }
    
    // Admin with is_admin flag also sees all items
    if (user?.is_admin && (user?.role === 'admin' || user?.role === 'super_admin')) {
        if (item.icon === 'separator' || (item.title && !item.icon && !item.route)) {
            // Let the normal logic handle separators and labels
        } else {
            return true;
        }
    }
    
    // If item has allowAdmin flag and user is admin, allow access (check this before permission check)
    if (item.allowAdmin && user && (user.role === 'admin' || user.role === 'super_admin' || user.is_admin)) {
        return true;
    }
    
    // If item has no permission requirement, show it
    if (!item.permission) {
        // For label items (title only), check if any items below have permissions
        if (item.title && !item.icon && !item.route) {
            return hasAnyChildPermission(item);
        }
        return true;
    }
    
    try {
        // Check if user has the required permission
        const hasItemPermission = can(item.permission);
        
        // If item has children, also check if any child is accessible
        if (hasChildren(item)) {
            if (!item.children || !Array.isArray(item.children)) {
                return hasItemPermission;
            }
            // Show parent if it has permission OR if any child has permission
            const hasChildPermission = item.children.some((child) => {
                if (!child || typeof child !== 'object') return false;
                return !child.permission || can(child.permission);
            });
            return hasItemPermission || hasChildPermission;
        }
        
        // If user is admin and doesn't have permission but item has allowAdmin, allow it
        if (!hasItemPermission && item.allowAdmin && user && (user.role === 'admin' || user.role === 'super_admin' || user.is_admin)) {
            return true;
        }
        
        return hasItemPermission;
    } catch (error) {
        console.error('Error checking permission:', error);
        return false;
    }
};

// Check if any menu item after a label has permission
const hasAnyChildPermission = (labelItem) => {
    if (!menuItems.value || !Array.isArray(menuItems.value)) {
        return false;
    }
    
    const labelIndex = menuItems.value.findIndex(item => item === labelItem);
    if (labelIndex === -1) return false;
    
    // Check items after the label until we hit a separator or end
    for (let i = labelIndex + 1; i < menuItems.value.length; i++) {
        const item = menuItems.value[i];
        if (!item) continue;
        
        // Stop at next separator
        if (item.icon === 'separator') {
            break;
        }
        
        // Check if this item has permission
        if (item.permission) {
            if (can(item.permission)) {
                return true;
            }
        } else if (item.title && !item.icon && !item.route) {
            // Another label - stop here
            break;
        } else if (!item.permission) {
            // Item without permission requirement - show it
            return true;
        }
    }
    
    return false;
};

const shouldShowSeparator = (separatorIndex) => {
    if (separatorIndex === undefined || separatorIndex === null || typeof separatorIndex !== 'number') {
        return false;
    }
    if (!menuItems.value || !Array.isArray(menuItems.value)) {
        return false;
    }
    if (separatorIndex < 0 || separatorIndex >= menuItems.value.length) {
        return false;
    }
    for (let i = separatorIndex + 1; i < menuItems.value.length; i++) {
        const item = menuItems.value[i];
        if (!item) {
            continue;
        }
        if (item.icon === 'separator') {
            break;
        }
        try {
            if (hasPermission(item)) {
                return true;
            }
        } catch (error) {
            console.error('Error checking permission in separator:', error);
            continue;
        }
    }
    return false;
};

// Close mobile sidebar on route change
watch(() => page.url, () => {
    if (mobileSidebarOpen && typeof mobileSidebarOpen.value !== 'undefined') {
        mobileSidebarOpen.value = false;
        document.body.style.overflow = '';
    }
});

// Handle window resize to ensure toggle button works correctly
const isMobile = ref(false);

const handleResize = () => {
    if (typeof window !== 'undefined') {
        isMobile.value = window.innerWidth < 1024;
        // On mobile, ensure sidebar is closed if window resizes to mobile size
        if (isMobile.value && mobileSidebarOpen && typeof mobileSidebarOpen.value !== 'undefined') {
            mobileSidebarOpen.value = false;
            document.body.style.overflow = '';
        }
    }
};

// Close mobile sidebar when clicking outside (for mobile)
const handleClickOutside = (event) => {
    if (typeof window !== 'undefined' && window.innerWidth < 1024 && mobileOpen.value) {
        const sidebar = document.getElementById('kt_app_sidebar');
        if (sidebar && !sidebar.contains(event.target) && !event.target.closest('.lg\\:hidden')) {
            closeMobile();
        }
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    // Handle window resize
    if (typeof window !== 'undefined') {
        isMobile.value = window.innerWidth < 1024;
        window.addEventListener('resize', handleResize);
    }
    // Close sidebar on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && mobileOpen.value) {
            closeMobile();
        }
    });
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    if (typeof window !== 'undefined') {
        window.removeEventListener('resize', handleResize);
    }
    document.body.style.overflow = '';
});
</script>

<style scoped>
.app-sidebar {
    transition: width 0.3s ease, transform 0.3s ease;
    background: #ffffff;
    height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 1040;
}

.sidebar-expanded {
    width: 260px;
}

.sidebar-minimized {
    width: 75px;
}

/* Mobile Styles */
@media (max-width: 991px) {
    .app-sidebar {
        width: 280px !important;
        max-width: 85vw;
        box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
        z-index: 50;
    }
    
    .menu-link {
        padding: 0.875rem 1rem !important;
        font-size: 0.9375rem;
        min-height: 44px;
    }
    
    .menu-icon {
        width: 1.5rem;
        height: 1.5rem;
    }
    
    .menu-title {
        font-size: 0.9375rem;
    }
    
    .menu-sub .menu-link {
        padding: 0.75rem 1rem !important;
        min-height: 40px;
    }
}

.app-sidebar-header {
    background: #ffffff;
    border-bottom: 1px solid #dee2e6;
}

.user-info-card {
    transition: all 0.2s ease;
}

.user-info-card:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
}

.sidebar-toggle-btn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #e9ecef;
    background: #f8f9fa;
    color: #6c757d;
    padding: 0;
}

.sidebar-toggle-btn:hover {
    background: #e9ecef;
    border-color: #dee2e6;
    color: #495057;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.sidebar-toggle-expanded:hover {
    transform: scale(1.05);
}

.sidebar-toggle-minimized:hover {
    transform: translateX(-50%) scale(1.05);
}

.sidebar-toggle-btn:active {
    background: #dee2e6;
}

.sidebar-toggle-expanded:active {
    transform: scale(0.95);
}

.sidebar-toggle-minimized:active {
    transform: translateX(-50%) scale(0.95);
}

.sidebar-toggle-btn:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    outline: none;
}

.sidebar-toggle-expanded {
    top: 0.5rem;
    left: 0.5rem;
}

.sidebar-toggle-minimized {
    top: 0.5rem;
    left: 50% !important;
    transform: translateX(-50%);
}

.sidebar-toggle-icon {
    transition: transform 0.3s ease;
    font-size: 0.875rem;
    font-weight: 600;
}

.sidebar-title-wrapper {
    padding-right: 40px;
}

.sidebar-title {
    font-size: 0.95rem;
    line-height: 1.4;
    transition: all 0.2s ease;
}

.sidebar-title-icon {
    color: #0d6efd;
    font-size: 1rem;
    display: flex;
    align-items: center;
    transition: transform 0.2s ease;
}

.sidebar-title-wrapper:hover .sidebar-title-icon {
    transform: scale(1.1);
}

.sidebar-title-text {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.user-info-card {
    transition: all 0.2s ease;
}

.user-info-card:hover {
    background-color: #f8f9fa;
}

.menu-link {
    transition: all 0.2s ease;
    position: relative;
}

.menu-link:hover {
    background-color: #f8f9fa !important;
}

.menu-link.bg-primary {
    background-color: rgba(13, 110, 253, 0.1) !important;
    color: #0d6efd !important;
    font-weight: 500;
}

.menu-link.bg-primary::before {
    content: '';
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: #0d6efd;
    border-radius: 0 3px 3px 0;
}

.menu-icon {
    transition: color 0.2s ease;
    color: #6c757d;
}

.menu-link:hover .menu-icon {
    color: #0d6efd;
}

.menu-link.bg-primary .menu-icon {
    color: #0d6efd;
}

.menu-title {
    transition: color 0.2s ease;
    color: inherit;
}

.menu-badge {
    flex-shrink: 0;
}

.menu-section-label {
    margin-top: 0.5rem;
    margin-bottom: 0.25rem;
}

.menu-separator {
    margin: 0.5rem 0;
}

.menu-sub {
    animation: slideDown 0.2s ease-out;
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

.menu-sub .menu-link.bg-primary .menu-bullet i {
    color: #0d6efd;
}

.menu-arrow {
    transition: transform 0.2s ease;
    flex-shrink: 0;
}

.menu-arrow.rotate-180 {
    transform: rotate(180deg);
}

.app-sidebar-wrapper {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
}

.app-sidebar-wrapper::-webkit-scrollbar {
    width: 4px;
}

.app-sidebar-wrapper::-webkit-scrollbar-track {
    background: transparent;
}

.app-sidebar-wrapper::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

.app-sidebar-wrapper::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Minimized state */
.sidebar-minimized .menu-link {
    justify-content: center !important;
    padding: 0.75rem !important;
}

.sidebar-minimized .menu-title,
.sidebar-minimized .menu-badge,
.sidebar-minimized .menu-arrow,
.sidebar-minimized .menu-section-label span {
    display: none !important;
}

.sidebar-minimized .menu-icon {
    margin-right: 0 !important;
}

.sidebar-minimized .menu-sub {
    display: none !important;
}

.sidebar-minimized .menu-separator {
    margin: 0.25rem 0;
}

/* Small Mobile Styles */
@media (max-width: 575px) {
    .app-sidebar {
        width: 260px !important;
        max-width: 90vw;
    }
    
    .menu-link {
        padding: 0.75rem 0.875rem !important;
        font-size: 0.875rem;
        min-height: 44px;
    }
    
    .menu-icon {
        width: 1.25rem;
        height: 1.25rem;
    }
    
    .menu-title {
        font-size: 0.875rem;
    }
    
    .menu-sub .menu-link {
        padding: 0.625rem 0.75rem !important;
        font-size: 0.8125rem;
        min-height: 40px;
    }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
    .menu-link {
        padding: 1rem !important;
        min-height: 44px;
    }
    
    .app-sidebar-toggle {
        min-width: 44px;
        min-height: 44px;
    }
}

/* Print styles */
@media print {
    .app-sidebar {
        display: none !important;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .app-sidebar {
        border-left: 2px solid #000;
    }
    
    .menu-link.bg-blue-50 {
        border: 1px solid #2563eb;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .app-sidebar,
    .menu-link,
    .menu-icon,
    .menu-arrow,
    .menu-sub {
        transition: none !important;
        animation: none !important;
    }
}
</style>

