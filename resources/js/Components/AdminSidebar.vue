<template>
    <div
        id="kt_app_sidebar"
        class="app-sidebar flex flex-col fixed top-0 right-0 h-full bg-white z-50 transition-all duration-300"
        :class="{ 
            'w-[260px]': !minimized, 
            'w-[75px]': minimized, 
            'translate-x-full': !mobileOpen, 
            'translate-x-0': mobileOpen,
            'lg:translate-x-0': true,
            'lg:flex': true,
            'flex': mobileOpen
        }"
    >
        <!-- Header Section -->
        <div class="app-sidebar-header px-4 py-4 border-b border-gray-100 relative">
            <!-- Toggle Button (Top Left) -->
            <button
                id="kt_app_sidebar_toggle"
                @click.stop="handleToggleMinimize"
                @keydown.enter.prevent.stop="handleToggleMinimize"
                @keydown.space.prevent.stop="handleToggleMinimize"
                type="button"
                class="app-sidebar-toggle h-8 w-8 absolute top-2 left-2 cursor-pointer hidden lg:flex items-center justify-center rounded-lg bg-gray-50 border border-gray-200 shadow-sm hover:bg-gray-100 hover:border-gray-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 z-50"
                :class="{ 
                    'rotate-180': minimized,
                    'opacity-0 pointer-events-none': typeof window !== 'undefined' && window.innerWidth < 1024
                }"
                :title="minimized ? t('common.expand') : t('common.collapse')"
                :aria-label="minimized ? t('common.expand') : t('common.collapse')"
                :aria-expanded="!minimized"
                :disabled="false"
            >
                <svg 
                    class="w-4 h-4 text-gray-600 transition-transform duration-200" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24" 
                    aria-hidden="true"
                >
                    <path 
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2.5" 
                        d="M9 5l7 7-7 7" 
                    />
                </svg>
            </button>
            
            <!-- Title -->
            <!-- <h2 v-if="!minimized" class="text-lg font-bold text-gray-900 mb-4 pr-10">
                {{ t('admin.dashboard') || 'لوحة تحكم المسؤول' }}
            </h2> -->
            
            <!-- User Info Card -->
            <div v-if="!minimized" class="user-info-card bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="relative flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                            {{ getUserInitials() }}
                        </div>
                        <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-500 mb-1">{{ t('admin.welcome_back') }}</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ userName }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Minimized Avatar -->
            <div v-else class="flex justify-center">
                <div class="relative">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md">
                        {{ getUserInitials() }}
                    </div>
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
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

        <!-- Sidebar Menu -->
        <div class="app-sidebar-menu flex-1 overflow-hidden">
            <div
                id="kt_app_sidebar_menu_wrapper"
                class="app-sidebar-wrapper py-4 overflow-y-auto h-full px-3"
            >
                <div
                    class="menu menu-column"
                    id="kt_app_sidebar_menu"
                >
                    <template v-for="(item, index) in menuItems" :key="index">
                        <div v-if="item" class="menu-item">
                            <!-- Section Label -->
                            <div
                                v-if="item && item.title && !item.icon && !item.route && hasPermission(item)"
                                class="menu-section-label px-4 py-3"
                            >
                                <span v-if="!minimized" class="text-gray-500 text-xs font-semibold uppercase tracking-wider">
                                    {{ item.title }}
                                </span>
                            </div>

                            <!-- Separator -->
                            <div
                                v-else-if="item.icon === 'separator' && shouldShowSeparator(index)"
                                class="menu-separator my-2"
                            >
                                <div class="h-px bg-gray-100"></div>
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
                                    class="menu-link flex items-center px-4 py-3 text-gray-700 rounded-lg transition-all duration-200 cursor-pointer group mb-1"
                                    :class="{
                                        'bg-blue-50 text-blue-600': checkRoute(item),
                                        'hover:bg-gray-50': !checkRoute(item),
                                    }"
                                    @click.prevent="toggleSubmenu(index)"
                                >
                                    <span class="menu-icon mr-3 flex-shrink-0 flex items-center justify-center w-5 h-5">
                                        <svg v-if="item.icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" :d="getIconPath(item.icon)" />
                                        </svg>
                                    </span>
                                    <span v-if="!minimized" class="menu-title font-medium text-sm flex-1 whitespace-nowrap overflow-hidden text-ellipsis">
                                        {{ item.title }}
                                    </span>
                                    <span v-if="item.count && !minimized" class="menu-badge ml-2">
                                        <span class="bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full font-medium">
                                            {{ item.count }}
                                        </span>
                                    </span>
                                    <span v-if="hasChildren(item) && !minimized" class="menu-arrow ml-2">
                                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openSubmenus[index] }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </span>
                                </a>
                                
                                <!-- Menu Item without Children (Link) -->
                                <Link
                                    v-else-if="item?.route"
                                    :href="safeRoute(item.route, item.routeParams || {})"
                                    class="menu-link flex items-center px-4 py-3 text-gray-700 rounded-lg transition-all duration-200 cursor-pointer group mb-1"
                                    :class="{
                                        'bg-blue-50 text-blue-600': checkRoute(item),
                                        'hover:bg-gray-50': !checkRoute(item),
                                    }"
                                    @click="handleMenuItemClick"
                                >
                                    <span class="menu-icon mr-3 flex-shrink-0 flex items-center justify-center w-5 h-5">
                                        <svg v-if="item.icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" :d="getIconPath(item.icon)" />
                                        </svg>
                                    </span>
                                    <span v-if="!minimized" class="menu-title font-medium text-sm flex-1 whitespace-nowrap overflow-hidden text-ellipsis">
                                        {{ item.title }}
                                    </span>
                                    <span v-if="item.count && !minimized" class="menu-badge ml-2">
                                        <span class="bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full font-medium">
                                            {{ item.count }}
                                        </span>
                                    </span>
                                </Link>

                                <!-- Submenu -->
                                <div
                                    v-if="hasChildren(item) && (openSubmenus[index] || item.show || checkRoute(item))"
                                    class="menu-sub mt-1 mr-4"
                                >
                                    <template v-for="(subItem, subIndex) in item.children" :key="subIndex">
                                        <div v-if="hasPermission(subItem)" class="menu-item">
                                            <Link
                                                v-if="subItem && subItem.route"
                                                class="menu-link flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200"
                                                :class="{
                                                    'bg-blue-50 text-blue-600': safeRouteCurrent(subItem.route, subItem.routeParams || {}),
                                                    'text-gray-600 hover:bg-gray-50': !safeRouteCurrent(subItem.route, subItem.routeParams || {}),
                                                }"
                                                :href="subItem?.route ? safeRoute(subItem.route, subItem.routeParams || {}) : '#'"
                                                @click="typeof window !== 'undefined' && window.innerWidth < 1024 ? closeMobile() : null"
                                            >
                                                <span class="menu-bullet mr-3 flex items-center justify-center">
                                                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                                </span>
                                                <span class="menu-title font-medium flex-1">
                                                    {{ subItem.title }}
                                                </span>
                                                <span
                                                    v-if="subItem.count || subItem.count === null"
                                                    class="menu-badge ml-2"
                                                >
                                                    <span
                                                        v-if="subItem.count === null"
                                                        class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"
                                                    ></span>
                                                    <span
                                                        v-else
                                                        class="bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full font-medium"
                                                    >{{ subItem.count }}</span>
                                                </span>
                                            </Link>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div
        v-if="mobileOpen"
        @click="closeMobile"
        class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden transition-opacity duration-300"
    ></div>
</template>

<script setup>
import { computed, ref, inject, watch, onMounted, onUnmounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';
import { Ziggy } from '../ziggy';
import { route as ziggyRoute } from '../../../vendor/tightenco/ziggy/dist/index.esm.js';

const { t } = useTranslation();
const page = usePage();

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

// Icon paths mapping
const iconPaths = {
    'chart-simple-3': 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    'plus-circle': 'M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z',
    'briefcase': 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    'people': 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
    'file-up': 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'shield-cross': 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    'file-down': 'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'abstract-30': 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z',
    'setting-4': 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
    'map': 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7',
    'document': 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'category': 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
    'key-square': 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',
    'text-circle': 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'check-circle': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
};

const getIconPath = (iconName) => {
    return iconPaths[iconName] || iconPaths['chart-simple-3'];
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
                title: t('admin.permissions'),
                icon: 'key-square',
                route: 'admin.permissions.index',
                permission: 'role-list',
            },
            {
                title: t('admin.settings') || 'Settings',
                icon: 'setting-4',
                route: 'admin.settings.index',
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

const can = (permissionOrPermissions) => {
    const user = page.props?.auth?.user;
    
    // Super admin always has all permissions
    if (user?.role === 'super_admin') {
        return true;
    }
    
    // Admin with is_admin flag also has all permissions
    if (user?.is_admin && (user?.role === 'admin' || user?.role === 'super_admin')) {
        return true;
    }
    
    // Get auth.can from page props
    const authCan = page.props?.auth?.can;
    
    if (!authCan || typeof authCan !== 'object') {
        return false;
    }
    
    // Check if user has the wildcard permission (super admin)
    if (authCan['*'] === true) {
        return true;
    }
    
    try {
        if (permissionOrPermissions === null || permissionOrPermissions === undefined || permissionOrPermissions === '') {
            return false;
        }
        
        // Handle array of permissions or single permission
        const permissions = Array.isArray(permissionOrPermissions) 
            ? permissionOrPermissions 
            : [permissionOrPermissions];
        
        // Check if user has at least one of the required permissions
        return permissions.some((permission) => {
            if (!permission || typeof permission !== 'string') {
                return false;
            }
            // Check if permission exists in auth.can and is truthy
            return authCan[permission] === true;
        });
    } catch (error) {
        console.error('Error in can method:', error);
        return false;
    }
};

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
            return '#';
        }
        return routeUrl;
    } catch (error) {
        console.error(`Error in safeRoute for [${routeName}]:`, error);
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
const handleResize = () => {
    if (typeof window !== 'undefined') {
        // On mobile, ensure sidebar is closed if window resizes to mobile size
        if (window.innerWidth < 1024 && mobileSidebarOpen && typeof mobileSidebarOpen.value !== 'undefined') {
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
/* Font Family */
.app-sidebar,
.app-sidebar * {
    font-family: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.app-sidebar {
    transition: width 0.3s ease, transform 0.3s ease;
    background: #ffffff;
    border-left: 1px solid #e5e7eb;
    box-shadow: -1px 0 3px rgba(0, 0, 0, 0.05);
    height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
}

/* Mobile Styles */
@media (max-width: 1023px) {
    .app-sidebar {
        width: 280px !important;
        max-width: 85vw;
        box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
        z-index: 50;
    }
    
    .app-sidebar.translate-x-full {
        transform: translateX(100%);
    }
    
    .app-sidebar.translate-x-0 {
        transform: translateX(0);
    }
    
    .app-sidebar-logo {
        padding: 1rem;
    }
    
    .menu-link {
        padding: 0.875rem 1rem !important;
        font-size: 0.9375rem;
        min-height: 44px;
    }
    
    .menu-icon {
        width: 1.5rem;
        height: 1.5rem;
        flex-shrink: 0;
    }
    
    .menu-icon svg {
        width: 1.25rem;
        height: 1.25rem;
    }
    
    .menu-title {
        font-size: 0.9375rem;
    }
    
    .menu-sub .menu-link {
        padding: 0.75rem 1rem !important;
        min-height: 40px;
    }
}

/* Tablet Styles */
@media (min-width: 768px) and (max-width: 1023px) {
    .app-sidebar {
        width: 240px !important;
    }
}

/* Desktop Styles */
@media (min-width: 1024px) {
    .app-sidebar {
        width: 260px;
    }
    
    .app-sidebar.w-\[75px\] {
        width: 75px;
    }
}

.app-sidebar-header {
    background: #ffffff;
    border-bottom: 1px solid #e5e7eb;
}

.user-info-card {
    transition: all 0.2s ease;
}

.user-info-card:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.app-sidebar-logo {
    background: #ffffff;
    border-bottom: 1px solid #e5e7eb;
}

.app-sidebar-toggle {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    font-family: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    z-index: 50;
}

.app-sidebar-toggle:hover {
    background: #f9fafb;
    border-color: #d1d5db;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transform: scale(1.05);
}

.app-sidebar-toggle:active {
    transform: scale(0.95);
}

.app-sidebar-toggle:focus {
    outline: none;
    ring: 2px;
    ring-color: #2563eb;
    ring-offset: 2px;
}

.app-sidebar-toggle:focus-visible {
    outline: 2px solid #2563eb;
    outline-offset: 2px;
}

.app-sidebar-toggle:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

.app-sidebar-toggle svg {
    pointer-events: none;
    transition: transform 0.2s ease;
}

.app-sidebar-toggle.rotate-180 svg {
    transform: rotate(180deg);
}

/* Minimized state - toggle button centered */
.app-sidebar.w-\[75px\] .app-sidebar-toggle {
    left: 50%;
    transform: translateX(-50%);
    top: 0.5rem;
}

.app-sidebar.w-\[75px\] .app-sidebar-toggle:hover {
    transform: translateX(-50%) scale(1.05);
}

.app-sidebar.w-\[75px\] .app-sidebar-toggle:active {
    transform: translateX(-50%) scale(0.95);
}

.menu-link {
    transition: all 0.2s ease;
    position: relative;
}

.menu-link:hover {
    background: #f3f4f6;
}

.menu-link.bg-blue-50 {
    background: #eff6ff !important;
    color: #2563eb !important;
    font-weight: 500;
}

.menu-link.bg-blue-50::before {
    content: '';
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: #2563eb;
    border-radius: 0 3px 3px 0;
}

.menu-icon {
    transition: color 0.2s ease;
    color: #6b7280;
}

.menu-link:hover .menu-icon {
    color: #2563eb;
}

.menu-link.bg-blue-50 .menu-icon {
    color: #2563eb;
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

.menu-sub .menu-link {
    position: relative;
}

.menu-sub .menu-link.bg-blue-50 .menu-bullet span {
    background: #2563eb;
    width: 6px;
    height: 6px;
}

.menu-arrow {
    transition: transform 0.2s ease;
    flex-shrink: 0;
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
.app-sidebar.w-\[75px\] .menu-link {
    justify-content: center;
    padding: 0.75rem !important;
}

.app-sidebar.w-\[75px\] .menu-title,
.app-sidebar.w-\[75px\] .menu-badge,
.app-sidebar.w-\[75px\] .menu-arrow,
.app-sidebar.w-\[75px\] .menu-section-label span {
    display: none !important;
}

.app-sidebar.w-\[75px\] .menu-icon {
    margin-right: 0 !important;
}

.app-sidebar.w-\[75px\] .menu-sub {
    display: none !important;
}

.app-sidebar.w-\[75px\] .app-sidebar-logo {
    padding: 1rem 0.5rem;
    justify-content: center;
}

.app-sidebar.w-\[75px\] .app-sidebar-logo img {
    margin: 0 auto;
}

.app-sidebar.w-\[75px\] .menu-separator {
    margin: 0.25rem 0;
}

/* Active route indicator */
.menu-link.bg-blue-50 {
    position: relative;
}

/* Mobile overlay enhancement */
.fixed.inset-0.bg-black {
    backdrop-filter: blur(2px);
}

/* Small Mobile Styles */
@media (max-width: 640px) {
    .app-sidebar {
        width: 260px !important;
        max-width: 90vw;
    }
    
    .app-sidebar-logo {
        padding: 0.75rem 1rem;
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
    
    .menu-icon svg {
        width: 1rem;
        height: 1rem;
    }
    
    .menu-title {
        font-size: 0.875rem;
    }
    
    .menu-sub .menu-link {
        padding: 0.625rem 0.75rem !important;
        font-size: 0.8125rem;
        min-height: 40px;
    }
    
    .menu-content span.text-gray-500 {
        font-size: 0.625rem;
        padding: 0.375rem 0;
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

