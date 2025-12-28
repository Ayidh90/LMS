<template>
  <div
    id="kt_app_sidebar"
    class="app-sidebar d-flex flex-column position-fixed top-0 h-100 bg-white"
    :class="{
      'sidebar-expanded': !minimized,
      'sidebar-minimized': minimized,
      'sidebar-hidden': !mobileOpen,
      'sidebar-visible': mobileOpen,
      'sidebar-rtl': isRTL,
      'sidebar-ltr': isLTR,
    }"
    :style="{ width: minimized ? '75px' : '260px' }"
    :dir="isRTL ? 'rtl' : 'ltr'"
  >
    <!-- Header Section - Bootstrap Style -->
    <div
      class="app-sidebar-header p-3 border-bottom position-relative bg-white"
    >
      <!-- Sidebar Title -->
      <div
        v-if="!minimized"
        class="sidebar-title-wrapper mb-3 position-relative"
      >
        <h5
          class="sidebar-title text-dark fw-bold mb-0 d-flex align-items-center gap-2"
        >
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
          'sidebar-toggle-expanded': !minimized,
        }"
        :title="minimized ? t('common.expand') : t('common.collapse')"
        :aria-label="minimized ? t('common.expand') : t('common.collapse')"
        :aria-expanded="!minimized"
        style="
          width: 32px;
          height: 32px;
          z-index: 50;
          transition: all 0.3s ease;
        "
      >
        <i
          class="bi sidebar-toggle-icon text-secondary"
          :class="
            isRTL
              ? minimized
                ? 'bi-chevron-right'
                : 'bi-chevron-left'
              : minimized
              ? 'bi-chevron-right'
              : 'bi-chevron-left'
          "
        ></i>
      </button>

      <!-- User Dropdown - Bootstrap Style -->
      <div class="user-dropdown-wrapper">
        <UserDropdown 
          :minimized="minimized" 
          @dropdown-toggle="handleUserDropdownToggle"
          ref="userDropdownRef"
        />
      </div>
    </div>

    <!-- Logo Section (Hidden in new design) -->
    <div
      class="app-sidebar-logo px-6 py-3 border-b border-gray-100 relative hidden"
      id="kt_app_sidebar_logo"
    >
      <Link
        :href="safeRoute('admin.dashboard')"
        class="flex items-center justify-center gap-3 hover:opacity-80 transition-opacity"
      >
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
        <svg
          class="w-5 h-5"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M6 18L18 6M6 6l12 12"
          />
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
                v-if="
                  item &&
                  item.title &&
                  !item.icon &&
                  !item.route &&
                  hasPermission(item)
                "
                class="menu-section-label px-3 py-2"
              >
                <span
                  v-if="!minimized"
                  class="text-muted small fw-semibold text-uppercase"
                >
                  {{ item.title }}
                </span>
              </div>

              <!-- Separator -->
              <div
                v-else-if="
                  item.icon === 'separator' && shouldShowSeparator(index)
                "
                class="menu-separator my-2"
              >
                <hr class="my-0" />
              </div>

              <!-- Main menu items -->
              <div
                v-else-if="hasPermission(item)"
                :class="{
                  'menu-accordion': hasChildren(item),
                  show: checkRoute(item) || openSubmenus[index] || item?.show,
                }"
                class="menu-item"
              >
                <!-- Menu Item with Children (Accordion) -->
                <button
                  v-if="hasChildren(item)"
                  type="button"
                  class="menu-link d-flex align-items-center px-3 py-2 text-decoration-none rounded mb-1 border-0 bg-transparent w-100 text-start"
                  :class="{
                    'bg-primary bg-opacity-10 text-primary': checkRoute(item),
                    'text-dark': !checkRoute(item),
                  }"
                  @click.prevent="toggleSubmenu(index)"
                >
                  <span
                    class="menu-icon flex-shrink-0 d-flex align-items-center justify-content-center position-relative"
                    :class="isRTL ? 'ms-3' : 'me-3'"
                    style="width: 20px; height: 20px"
                  >
                    <i
                      v-if="item.icon"
                      :class="getBootstrapIcon(item.icon)"
                      class="fs-6"
                    ></i>
                    <!-- Live Indicator Dot -->
                    <span
                      v-if="item.hasLiveIndicator && hasLiveMeetings"
                      class="live-indicator-dot position-absolute"
                      :style="isRTL ? 'top: -2px; left: -2px;' : 'top: -2px; right: -2px;'"
                    ></span>
                  </span>
                  <span
                    v-if="!minimized"
                    class="menu-title fw-medium small flex-grow-1 text-nowrap text-truncate"
                  >
                    {{ item.title }}
                  </span>
                  <span
                    v-if="hasChildren(item) && !minimized"
                    class="menu-arrow"
                    :class="isRTL ? 'me-2' : 'ms-2'"
                  >
                    <i
                      class="bi bi-chevron-down small"
                      :class="{ 'rotate-180': openSubmenus[index] }"
                    ></i>
                  </span>
                </button>

                <!-- Menu Item without Children (Link) -->
                <Link
                  v-else-if="item?.route"
                  :href="getMenuItemUrl(item)"
                  class="menu-link d-flex align-items-center px-3 py-2 text-decoration-none rounded mb-1"
                  :class="{
                    'bg-primary bg-opacity-10 text-primary': checkRoute(item),
                    'text-dark': !checkRoute(item),
                  }"
                  @click="handleMenuItemClick"
                >
                  <span
                    class="menu-icon flex-shrink-0 d-flex align-items-center justify-content-center position-relative"
                    :class="isRTL ? 'ms-3' : 'me-3'"
                    style="width: 20px; height: 20px"
                  >
                    <i
                      v-if="item.icon"
                      :class="getBootstrapIcon(item.icon)"
                      class="fs-6"
                    ></i>
                    <!-- Live Indicator Dot -->
                    <span
                      v-if="item.hasLiveIndicator && hasLiveMeetings"
                      class="live-indicator-dot position-absolute"
                      :style="isRTL ? 'top: -2px; left: -2px;' : 'top: -2px; right: -2px;'"
                    ></span>
                  </span>
                  <span
                    v-if="!minimized"
                    class="menu-title fw-medium small flex-grow-1 text-nowrap text-truncate"
                  >
                    {{ item.title }}
                  </span>
                </Link>

                <!-- Submenu -->
                <div
                  v-if="
                    hasChildren(item) &&
                    (openSubmenus[index] || item.show || checkRoute(item))
                  "
                  class="menu-sub mt-1"
                  :class="isRTL ? 'me-4' : 'ms-4'"
                >
                  <template
                    v-for="(subItem, subIndex) in item.children"
                    :key="subIndex"
                  >
                    <div v-if="hasPermission(subItem)" class="menu-item">
                      <Link
                        v-if="subItem && subItem.route"
                        class="menu-link d-flex align-items-center px-3 py-2 small text-decoration-none rounded"
                        :class="{
                          'bg-primary bg-opacity-10 text-primary':
                            safeRouteCurrent(
                              subItem.route,
                              subItem.routeParams || {}
                            ),
                          'text-muted': !safeRouteCurrent(
                            subItem.route,
                            subItem.routeParams || {}
                          ),
                        }"
                        :href="getMenuItemUrl(subItem)"
                        @click="
                          typeof window !== 'undefined' &&
                          window.innerWidth < 1024
                            ? closeMobile()
                            : null
                        "
                      >
                        <span
                          class="menu-bullet d-flex align-items-center justify-content-center"
                          :class="isRTL ? 'ms-3' : 'me-3'"
                        >
                          <i
                            class="bi bi-circle-fill"
                            style="font-size: 0.375rem"
                          ></i>
                        </span>
                        <span class="menu-title fw-medium flex-grow-1">
                          {{ subItem.title }}
                        </span>
                        <span
                          v-if="subItem.count || subItem.count === null"
                          class="menu-badge"
                          :class="isRTL ? 'me-2' : 'ms-2'"
                        >
                          <span
                            v-if="subItem.count === null"
                            class="badge bg-primary rounded-pill"
                            style="width: 8px; height: 8px; padding: 0"
                          ></span>
                          <span v-else class="badge bg-primary">{{
                            subItem.count
                          }}</span>
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
    class="position-fixed top-0 w-100 h-100 bg-dark bg-opacity-50 z-40 d-lg-none"
    :class="isRTL ? 'start-0' : 'end-0'"
    style="transition: opacity 0.3s"
  ></div>
</template>

<script setup>
import { computed, ref, inject, watch, onMounted, onUnmounted } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";
import axios from "axios";
import { useTranslation } from "@/composables/useTranslation";
import { useRoute } from "@/composables/useRoute";
import UserDropdown from "./UserDropdown.vue";

const { t } = useTranslation();
const page = usePage();
const { route: routeHelper } = useRoute();

// Get current locale and direction
const currentLocale = computed(() => {
  return page.props?.locale || 'ar';
});

const isRTL = computed(() => {
  return currentLocale.value === 'ar';
});

const isLTR = computed(() => {
  return currentLocale.value === 'en';
});

// Live meetings state - initialize from page props
const getInitialLiveMeetingsCount = () => {
  // Get from page props (shared props are available directly in page.props)
  if (page.props?.liveMeetingsCount !== undefined) {
    return page.props.liveMeetingsCount;
  }
  // Default to 0
  return 0;
};

const liveMeetingsCount = ref(getInitialLiveMeetingsCount());

// Function to calculate live meetings count from page props
const fetchLiveMeetingsCount = () => {
  // Try to get from liveMeetings array if available
  if (page.props?.liveMeetings && Array.isArray(page.props.liveMeetings)) {
    const now = new Date();
    const count = page.props.liveMeetings.filter(meeting => {
      if (!meeting || !meeting.live_meeting_date) return false;
      try {
        const meetingDate = new Date(meeting.live_meeting_date);
        if (isNaN(meetingDate.getTime())) return false;
        const duration = meeting.duration_minutes || 60;
        const endDate = new Date(meetingDate.getTime() + duration * 60000);
        return meetingDate <= now && endDate >= now;
      } catch (e) {
        return false;
      }
    }).length;
    liveMeetingsCount.value = count;
    if (process.env.NODE_ENV === 'development') {
      console.log('Live meetings count calculated from props:', liveMeetingsCount.value);
    }
  } else if (page.props?.liveMeetingsCount !== undefined) {
    // Fallback to direct count if provided
    liveMeetingsCount.value = page.props.liveMeetingsCount;
    if (process.env.NODE_ENV === 'development') {
      console.log('Live meetings count from page props:', liveMeetingsCount.value);
    }
  }
};

// Check if there are live meetings
const hasLiveMeetings = computed(() => {
  const hasLive = liveMeetingsCount.value > 0;
  // Always log for debugging (remove in production if needed)
  console.log('🔍 hasLiveMeetings check:', {
    hasLive,
    count: liveMeetingsCount.value,
    type: typeof liveMeetingsCount.value
  });
  return hasLive;
});

// Route function - use the useRoute composable
const route = (name, params = {}, absolute = false) => {
  if (!name) {
    if (process.env.NODE_ENV === "development") {
      console.warn("Route name is required");
    }
    return "#";
  }

  try {
    const result = routeHelper(name, params, absolute);
    if (!result || result === "#") {
      if (process.env.NODE_ENV === "development") {
        console.warn(`Route [${name}] generated empty URL`);
      }
      return "#";
    }
    return result;
  } catch (error) {
    if (process.env.NODE_ENV === "development") {
      console.error(`Error generating route [${name}]:`, error);
    }
    return "#";
  }
};

const openSubmenus = ref({});
const userDropdownRef = ref(null);

// Handle user dropdown toggle
const handleUserDropdownToggle = (isOpen) => {
  // Close dropdown when sidebar is minimized or mobile sidebar closes
  if (isOpen && (minimized.value || !mobileOpen.value)) {
    if (userDropdownRef.value && userDropdownRef.value.closeDropdown) {
      userDropdownRef.value.closeDropdown();
    }
  }
};

// Website name from settings
const websiteName = computed(() => {
  return (
    page.props?.settings?.website?.name ||
    t("admin.dashboard") ||
    "لوحة تحكم المسؤول"
  );
});

// Get injected values from AdminLayout (with fallback)
const sidebarMinimized = inject("sidebarMinimized", ref(false));
const mobileSidebarOpen = inject("mobileSidebarOpen", ref(false));
const toggleMinimizeFn = inject("toggleMinimize", null);

// Use injected state for minimized - remove local state to avoid conflicts
const minimized = computed(() => {
  return sidebarMinimized && typeof sidebarMinimized.value !== "undefined"
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
  if (typeof window !== "undefined" && window.innerWidth < 1024) {
    return;
  }

  try {
    // Use the injected function if available, otherwise update directly
    if (toggleMinimizeFn && typeof toggleMinimizeFn === "function") {
      toggleMinimizeFn();
    } else if (
      sidebarMinimized &&
      typeof sidebarMinimized.value !== "undefined"
    ) {
      sidebarMinimized.value = !sidebarMinimized.value;
      document.body.classList.toggle("app-sidebar-minimize");
    }
  } catch (error) {
    console.error("Error toggling sidebar:", error);
  }
};

// Keep the old function name for backward compatibility if needed
const toggleMinimize = handleToggleMinimize;

// Use injected mobileSidebarOpen or local state
const mobileOpen = computed(() => {
  if (mobileSidebarOpen && typeof mobileSidebarOpen.value !== "undefined") {
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
  if (mobileSidebarOpen && typeof mobileSidebarOpen.value !== "undefined") {
    mobileSidebarOpen.value = false;
    document.body.style.overflow = "";
  }
};

// Get URL for menu item, ensuring it's never '#'
const getMenuItemUrl = (item) => {
  if (!item || !item.route) {
    return "#";
  }
  
  const url = safeRoute(item.route, item.routeParams || {});
  
  // Ensure we never return '#' - use fallback instead
  if (!url || url === "#") {
    const fallback = getFallbackRoute(item.route, item.routeParams || {});
    if (process.env.NODE_ENV === "development" && fallback === "#") {
      console.error(`Failed to generate URL for route [${item.route}]`);
    }
    return fallback;
  }
  
  return url;
};

const handleMenuItemClick = (event) => {
  // Close mobile sidebar on mobile devices when clicking menu items
  if (typeof window !== "undefined" && window.innerWidth < 1024) {
    closeMobile();
  }
  // Let Inertia Link component handle the navigation automatically
  // Don't prevent default - Link handles navigation
  
  // Debug: Log the href being used
  if (process.env.NODE_ENV === "development") {
    const href = event?.currentTarget?.href || event?.target?.closest('a')?.href;
    if (href) {
      console.log('Menu item clicked, navigating to:', href);
    }
  }
};

const handleLogoError = (event) => {
  event.target.style.display = "none";
};

// Bootstrap Icons mapping
const bootstrapIcons = {
  "chart-simple-3": "bi-graph-up",
  "plus-circle": "bi-plus-circle",
  briefcase: "bi-briefcase",
  people: "bi-people",
  "file-up": "bi-file-earmark-arrow-up",
  "shield-cross": "bi-shield-check",
  "file-down": "bi-file-earmark-arrow-down",
  "abstract-30": "bi-diagram-3",
  "setting-4": "bi-gear",
  map: "bi-diagram-2",
  document: "bi-file-earmark-text",
  category: "bi-folder",
  "key-square": "bi-key",
  "text-circle": "bi-file-text",
  "check-circle": "bi-check-circle",
  "camera-video": "bi-camera-video",
  "diagram-3": "bi-diagram-3",
  "diagram-2": "bi-diagram-2",
};

const getBootstrapIcon = (iconName) => {
  return bootstrapIcons[iconName] || bootstrapIcons["chart-simple-3"];
};

// Check if we're on a course show page (exclude create and edit pages)
const isCourseShowPage = computed(() => {
  const currentUrl = page.url.split("?")[0];
  
  // Exclude create and edit pages
  if (currentUrl === "/admin/courses/create" || currentUrl.endsWith("/edit")) {
    return false;
  }
  
  // Check if URL matches pattern: /admin/courses/{slug or id} (but not create/edit)
  const courseShowPattern = /^\/admin\/courses\/[^\/]+$/;
  return courseShowPattern.test(currentUrl);
});

// Get course identifier from URL
const courseIdentifier = computed(() => {
  if (!isCourseShowPage.value) return null;
  const currentUrl = page.url.split("?")[0];
  const match = currentUrl.match(/^\/admin\/courses\/([^\/]+)/);
  return match ? match[1] : null;
});

const menuItems = computed(() => {
  try {
    const itemsArray = [
      {
        title: t("common.dashboard"),
        icon: "chart-simple-3",
        route: "admin.dashboard",
      },
      {
        icon: "separator",
      },
      {
        title: t("admin.programs_management"),
        permission: "programs.view",
      },
      {
        title: t("admin.view_programs"),
        icon: "diagram-3",
        route: "admin.programs.index",
        permission: "programs.view",
      },
      {
        title: t("admin.create_program"),
        icon: "plus-circle",
        route: "admin.programs.create",
        permission: "programs.create",
      },
      {
        icon: "separator",
      },
      {
        title: t("admin.tracks_management"),
        permission: "tracks.view",
      },
      {
        title: t("admin.view_tracks"),
        icon: "diagram-2",
        route: "admin.tracks.index",
        permission: "tracks.view",
      },
      {
        title: t("admin.create_track"),
        icon: "plus-circle",
        route: "admin.tracks.create",
        permission: "tracks.create",
      },
      {
        icon: "separator",
      },
      {
        title: t("admin.courses_management"),
        permission: "courses.view-all",
      },
      {
        title: t("admin.view_courses"),
        icon: "briefcase",
        route: "admin.courses.index",
        permission: "courses.view-all",
        // Add children when on course show page
        children:
          isCourseShowPage.value && courseIdentifier.value
            ? [
                {
                  title: t("admin.view_batches") || "View Batches",
                  route: "admin.courses.batches.index",
                  routeParams: { course: courseIdentifier.value },
                  permission: "batches.view",
                },
                {
                  title: t("admin.view_sections") || "View Sections",
                  route: "admin.courses.sections.index",
                  routeParams: { course: courseIdentifier.value },
                  permission: "sections.view",
                },
                {
                  title: t("admin.view_lessons") || "View Lessons",
                  route: "admin.courses.lessons.index",
                  routeParams: { course: courseIdentifier.value },
                  permission: "lessons.view",
                },
                {
                  title: t("admin.view_questions") || "View Questions",
                  route: "admin.courses.lessons.index",
                  routeParams: { course: courseIdentifier.value },
                  permission: "questions.view",
                },
              ]
            : [],
        // Auto-expand when on course show page
        show: isCourseShowPage.value,
      },
      {
        title: t("admin.create_course"),
        icon: "plus-circle",
        route: "admin.courses.create",
        permission: "courses.create",
      },
      {
        title: t("admin.live_meetings") || "Live Meetings",
        icon: "camera-video",
        route: "admin.live_meetings.index",
        permission: "lessons.view",
        hasLiveIndicator: true,
      },
      {
        icon: "separator",
      },
      {
        title: t("admin.users_management"),
        permission: "users.view",
      },
      {
        title: t("admin.users"),
        icon: "people",
        route: "admin.users.index",
        permission: "users.view",
      },
      {
        icon: "separator",
      },
      {
        title: t("admin.system_settings"),
        permission: "roles.view",
      },
      {
        title: t("admin.roles_permissions"),
        icon: "key-square",
        route: "admin.roles.index",
        permission: "roles.view",
      },
      {
        title: t("admin.settings") || "Settings",
        icon: "setting-4",
        route: "admin.settings.index",
        permission: "settings.view",
        // Allow admins to access settings even without explicit permission
      },
      {
        title: t("admin.activity_logs"),
        icon: "text-circle",
        route: "admin.activity-logs.index",
        permission: "users.manage",
      },
    ];

    return itemsArray.filter((item) => item && typeof item === "object");
  } catch (error) {
    console.error("Error building sidebar items:", error);
    return [];
  }
});

// Check permission using page.props.auth.can (from HandleInertiaRequests)
const can = (permissionOrPermissions) => {
  // Safety check: ensure auth and can exist
  if (!page.props?.auth?.can) {
    return false;
  }
  try {
    // Safety check: ensure permissionOrPermissions is valid
    if (permissionOrPermissions === null || permissionOrPermissions === undefined) {
      return false;
    }
    let permissions = [].concat(permissionOrPermissions);
    let allPermissions = page.props.auth.can;
    // Safety check: ensure allPermissions is an object
    if (!allPermissions || typeof allPermissions !== 'object') {
      return false;
    }
    let hasPermission = false;
    permissions.forEach((item) => {
      if (item && allPermissions[item]) {
        hasPermission = true;
      }
    });
    return hasPermission;
  } catch (error) {
    console.error('Error in can method:', error);
    return false;
  }
};

const hasChildren = (item) => {
  if (!item || typeof item !== "object") {
    return false;
  }
  return (
    item.hasOwnProperty("children") &&
    Array.isArray(item.children) &&
    item.children.length > 0
  );
};

const checkRoute = (item) => {
  if (!item || typeof item !== "object") {
    return false;
  }
  if (!item.route) return false;
  try {
    const currentUrl = page.url;
    if (!hasChildren(item)) {
      try {
        const routeUrl = route(item.route, item.routeParams || {});
        if (!routeUrl || routeUrl === "#") return false;
        // Normalize URLs for comparison
        const normalizedCurrent = currentUrl.split("?")[0].replace(/\/$/, "");
        const normalizedRoute = routeUrl.split("?")[0].replace(/\/$/, "");
        // Compare URLs - exact match or starts with
        return (
          normalizedCurrent === normalizedRoute ||
          normalizedCurrent.startsWith(normalizedRoute + "/")
        );
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
        if (!routeUrl || routeUrl === "#") return false;
        const normalizedCurrent = currentUrl.split("?")[0].replace(/\/$/, "");
        const normalizedRoute = routeUrl.split("?")[0].replace(/\/$/, "");
        return (
          normalizedCurrent === normalizedRoute ||
          normalizedCurrent.startsWith(normalizedRoute + "/")
        );
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
    if (process.env.NODE_ENV === "development") {
      console.warn("safeRoute: routeName is required");
    }
    return "#";
  }

  try {
    // Handle route parameters properly
    let params = routeParams;
    
    // If routeParams is a simple value (string/number), convert to object
    if (routeParams && typeof routeParams !== 'object' && routeParams !== null) {
      // For routes like admin.courses.show, use 'course' as key
      if (routeName.includes('courses')) {
        params = { course: routeParams };
      } else if (routeName.includes('programs')) {
        params = { program: routeParams };
      } else if (routeName.includes('tracks')) {
        params = { track: routeParams };
      } else {
        // Try to infer from route name
        const routeParts = routeName.split('.');
        if (routeParts.length > 1) {
          const resourceName = routeParts[routeParts.length - 2];
          params = { [resourceName]: routeParams };
        }
      }
    }
    
    const routeUrl = route(routeName, params);
    if (!routeUrl || routeUrl === "#") {
      if (process.env.NODE_ENV === "development") {
        console.warn(
          `Route [${routeName}] generated empty URL or returned '#'. Using fallback.`
        );
      }
      // Fallback: construct URL manually for known routes
      return getFallbackRoute(routeName, params);
    }
    return routeUrl;
  } catch (error) {
    if (process.env.NODE_ENV === "development") {
      console.error(`Error in safeRoute for [${routeName}]:`, error);
    }
    // Fallback: construct URL manually for known routes
    return getFallbackRoute(routeName, routeParams);
  }
};

// Fallback route generator for when route helper fails
const getFallbackRoute = (routeName, params = {}) => {
  // Dashboard
  if (routeName === "admin.dashboard") {
    return "/admin/dashboard";
  }
  
  // Programs
  if (routeName === "admin.programs.index") {
    return "/admin/programs";
  }
  if (routeName === "admin.programs.create") {
    return "/admin/programs/create";
  }
  if (routeName === "admin.programs.show" || routeName === "admin.programs.edit") {
    const identifier = params?.program || params?.id || (typeof params === 'object' && Object.keys(params).length > 0 ? Object.values(params)[0] : null);
    if (identifier) {
      return routeName.includes('edit') ? `/admin/programs/${identifier}/edit` : `/admin/programs/${identifier}`;
    }
  }
  
  // Tracks
  if (routeName === "admin.tracks.index") {
    return "/admin/tracks";
  }
  if (routeName === "admin.tracks.create") {
    return "/admin/tracks/create";
  }
  if (routeName === "admin.tracks.show" || routeName === "admin.tracks.edit") {
    const identifier = params?.track || params?.id || (typeof params === 'object' && Object.keys(params).length > 0 ? Object.values(params)[0] : null);
    if (identifier) {
      return routeName.includes('edit') ? `/admin/tracks/${identifier}/edit` : `/admin/tracks/${identifier}`;
    }
  }
  
  // Courses
  if (routeName === "admin.courses.index") {
    return "/admin/courses";
  }
  if (routeName === "admin.courses.create") {
    return "/admin/courses/create";
  }
  if (routeName === "admin.courses.show" || routeName === "admin.courses.edit") {
    const identifier = params?.course || params?.id || (typeof params === 'object' && Object.keys(params).length > 0 ? Object.values(params)[0] : null);
    if (identifier) {
      return routeName.includes('edit') ? `/admin/courses/${identifier}/edit` : `/admin/courses/${identifier}`;
    }
  }
  if (routeName === "admin.courses.batches.index") {
    const identifier = params?.course || params?.id || (typeof params === 'object' && Object.keys(params).length > 0 ? Object.values(params)[0] : null);
    if (identifier) {
      return `/admin/courses/${identifier}/batches`;
    }
  }
  if (routeName === "admin.courses.sections.index") {
    const identifier = params?.course || params?.id || (typeof params === 'object' && Object.keys(params).length > 0 ? Object.values(params)[0] : null);
    if (identifier) {
      return `/admin/courses/${identifier}/sections`;
    }
  }
  if (routeName === "admin.courses.lessons.index") {
    const identifier = params?.course || params?.id || (typeof params === 'object' && Object.keys(params).length > 0 ? Object.values(params)[0] : null);
    if (identifier) {
      return `/admin/courses/${identifier}/lessons`;
    }
  }
  
  // Users
  if (routeName === "admin.users.index") {
    return "/admin/users";
  }
  
  // Roles
  if (routeName === "admin.roles.index") {
    return "/admin/roles";
  }
  
  // Settings
  if (routeName === "admin.settings.index") {
    return "/admin/settings";
  }
  
  // Live Meetings
  if (routeName === "admin.live_meetings.index") {
    return "/admin/live-meetings";
  }
  
  // Activity Logs
  if (routeName === "admin.activity-logs.index") {
    return "/admin/activity-logs";
  }
  
  // Default fallback
  if (process.env.NODE_ENV === "development") {
    console.warn(`No fallback route found for [${routeName}], returning '#'`);
  }
  return "#";
};

const safeRouteCurrent = (routeName, routeParams = {}) => {
  if (!routeName) {
    return false;
  }
  try {
    const currentUrl = page.url;
    
    // Handle route parameters properly (same logic as safeRoute)
    let params = routeParams;
    if (routeParams && typeof routeParams !== 'object') {
      if (routeName.includes('courses')) {
        params = { course: routeParams };
      } else if (routeName.includes('programs')) {
        params = { program: routeParams };
      } else if (routeName.includes('tracks')) {
        params = { track: routeParams };
      } else {
        const routeParts = routeName.split('.');
        if (routeParts.length > 1) {
          const resourceName = routeParts[routeParts.length - 2];
          params = { [resourceName]: routeParams };
        }
      }
    }
    
    const routeUrl = route(routeName, params);
    if (!routeUrl || routeUrl === "#") return false;
    // Normalize URLs for comparison
    const normalizedCurrent = currentUrl.split("?")[0].replace(/\/$/, "");
    const normalizedRoute = routeUrl.split("?")[0].replace(/\/$/, "");
    // Compare URLs - exact match or starts with
    return (
      normalizedCurrent === normalizedRoute ||
      normalizedCurrent.startsWith(normalizedRoute + "/")
    );
  } catch (error) {
    return false;
  }
};

/**
 * Check if menu item should be displayed based on user permissions
 * Uses can() function which checks page.props.auth.can from HandleInertiaRequests
 */
const hasPermission = (item) => {
  // Safety check: ensure item exists
  if (!item || typeof item !== 'object') return false;
  
  // If item has no permission requirement, show it
  if (!item.permission) {
    // For label items (title only), check if any items below have permissions
    if (item.title && !item.icon && !item.route) {
      return hasAnyChildPermission(item);
    }
    // For separators, check if any items after have permissions
    if (item.icon === "separator") {
      return shouldShowSeparator(menuItems.value.findIndex(i => i === item));
    }
    return true;
  }
  
  // Check permission using can() function
  try {
    const hasItemPermission = can(item.permission);
    
    // If item has children, also check if any child is accessible
    if (hasChildren(item)) {
      // Safety check: ensure children exists and is an array
      if (!item.children || !Array.isArray(item.children)) {
        return hasItemPermission;
      }
      // Show parent if it has permission OR if any child has permission
      const hasChildPermission = item.children.some((child) => {
        if (!child || typeof child !== "object") return false;
        return !child.permission || can(child.permission);
      });
      return hasItemPermission || hasChildPermission;
    }
    
    // If user is admin and doesn't have permission but item has allowAdmin, allow it
    const user = page.props?.auth?.user;
    if (
      !hasItemPermission &&
      item.allowAdmin &&
      user &&
      (user.role === "admin" || user.role === "super_admin" || user.is_admin)
    ) {
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

  const labelIndex = menuItems.value.findIndex((item) => item === labelItem);
  if (labelIndex === -1) return false;

  // Check items after the label until we hit a separator or end
  for (let i = labelIndex + 1; i < menuItems.value.length; i++) {
    const item = menuItems.value[i];
    if (!item) continue;

    // Stop at next separator
    if (item.icon === "separator") {
      break;
    }

    // Check if this item should be shown based on permissions
    if (hasPermission(item)) {
      return true;
    }
  }

  return false;
};

const shouldShowSeparator = (separatorIndex) => {
  if (
    separatorIndex === undefined ||
    separatorIndex === null ||
    typeof separatorIndex !== "number"
  ) {
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
    if (item.icon === "separator") {
      break;
    }
    try {
      if (hasPermission(item)) {
        return true;
      }
    } catch (error) {
      console.error("Error checking permission in separator:", error);
      continue;
    }
  }
  return false;
};

// Close mobile sidebar on route change and update live meetings count
watch(
  () => page.url,
  (newUrl, oldUrl) => {
    if (mobileSidebarOpen && typeof mobileSidebarOpen.value !== "undefined") {
      mobileSidebarOpen.value = false;
      document.body.style.overflow = "";
    }
    // Close user dropdown on route change
    if (userDropdownRef.value && userDropdownRef.value.closeDropdown) {
      userDropdownRef.value.closeDropdown();
    }
    // Update live meetings count from props immediately (no delay needed)
    if (page.props?.liveMeetingsCount !== undefined) {
      liveMeetingsCount.value = page.props.liveMeetingsCount;
    }
    // Always fetch from API to ensure we have latest data
    console.log('🔄 Route changed, fetching live meetings count...', { newUrl, oldUrl });
    // Small delay to ensure page props are loaded
    setTimeout(() => {
      fetchLiveMeetingsCount();
    }, 100);
  },
  { immediate: false }
);

// Watch for props changes to update live meetings count
watch(
  () => [page.props?.liveMeetings, page.props?.liveMeetingsCount],
  (newValues, oldValues) => {
    // Update count when props change, but preserve current value if new value is not available
    if (page.props?.liveMeetingsCount !== undefined) {
      liveMeetingsCount.value = page.props.liveMeetingsCount;
    }
    // Still fetch from API to ensure we have latest data
    fetchLiveMeetingsCount();
  },
  { deep: true, immediate: false }
);

// Close user dropdown when sidebar is minimized
watch(
  () => minimized.value,
  (newVal) => {
    if (newVal && userDropdownRef.value && userDropdownRef.value.closeDropdown) {
      userDropdownRef.value.closeDropdown();
    }
  }
);

// Close user dropdown when mobile sidebar closes
watch(
  () => mobileOpen.value,
  (newVal) => {
    if (!newVal && userDropdownRef.value && userDropdownRef.value.closeDropdown) {
      userDropdownRef.value.closeDropdown();
    }
  }
);

// Watch for locale changes and update document direction
watch(
  () => currentLocale.value,
  (newLocale) => {
    if (typeof window !== 'undefined') {
      const direction = newLocale === 'ar' ? 'rtl' : 'ltr';
      document.documentElement.setAttribute('dir', direction);
      document.documentElement.setAttribute('lang', newLocale);
    }
  },
  { immediate: true }
);

// Handle window resize to ensure toggle button works correctly
const isMobile = ref(false);

const handleResize = () => {
  if (typeof window !== "undefined") {
    isMobile.value = window.innerWidth < 1024;
    // On mobile, ensure sidebar is closed if window resizes to mobile size
    if (
      isMobile.value &&
      mobileSidebarOpen &&
      typeof mobileSidebarOpen.value !== "undefined"
    ) {
      mobileSidebarOpen.value = false;
      document.body.style.overflow = "";
    }
  }
};

// Close mobile sidebar when clicking outside (for mobile)
const handleClickOutside = (event) => {
  if (
    typeof window !== "undefined" &&
    window.innerWidth < 1024 &&
    mobileOpen.value
  ) {
    const sidebar = document.getElementById("kt_app_sidebar");
    if (
      sidebar &&
      !sidebar.contains(event.target) &&
      !event.target.closest(".lg\\:hidden")
    ) {
      closeMobile();
    }
  }
  
  // Close user dropdown when clicking outside sidebar (on mobile)
  if (
    typeof window !== "undefined" &&
    window.innerWidth < 1024 &&
    userDropdownRef.value &&
    userDropdownRef.value.closeDropdown
  ) {
    const sidebar = document.getElementById("kt_app_sidebar");
    if (sidebar && !sidebar.contains(event.target)) {
      userDropdownRef.value.closeDropdown();
    }
  }
};

// Polling interval for live meetings (30 seconds)
let liveMeetingsPollInterval = null;

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
  // Handle window resize
  if (typeof window !== "undefined") {
    isMobile.value = window.innerWidth < 1024;
    window.addEventListener("resize", handleResize);
    
    // Ensure document direction is set correctly on mount
    const direction = currentLocale.value === 'ar' ? 'rtl' : 'ltr';
    document.documentElement.setAttribute('dir', direction);
    document.documentElement.setAttribute('lang', currentLocale.value);
  }
  // Close sidebar on escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && mobileOpen.value) {
      closeMobile();
    }
  });
  
  // Fetch live meetings count on mount immediately
  console.log('🚀 AdminSidebar mounted, fetching live meetings count...');
  fetchLiveMeetingsCount();
  
  // Set up polling to check for live meetings every 30 seconds
  if (typeof window !== "undefined") {
    // Start polling immediately, will update every 30 seconds
    liveMeetingsPollInterval = setInterval(() => {
      console.log('⏰ Polling: fetching live meetings count...');
      fetchLiveMeetingsCount();
    }, 30000); // 30 seconds
  }
});

onUnmounted(() => {
  document.removeEventListener("click", handleClickOutside);
  if (typeof window !== "undefined") {
    window.removeEventListener("resize", handleResize);
  }
  document.body.style.overflow = "";
  
  // Clear polling interval
  if (liveMeetingsPollInterval) {
    clearInterval(liveMeetingsPollInterval);
    liveMeetingsPollInterval = null;
  }
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

.sidebar-ltr.app-sidebar,
.app-sidebar:not(.sidebar-rtl):not([dir="rtl"]) {
  left: 0 !important;
  right: auto !important;
  border-left: none !important;
  border-right: 1px solid #dee2e6 !important;
  box-shadow: 2px 0 8px rgba(0, 0, 0, 0.08) !important;
}

.sidebar-rtl.app-sidebar {
  right: 0 !important;
  left: auto !important;
  border-right: none !important;
  border-left: 1px solid #dee2e6 !important;
  box-shadow: -2px 0 8px rgba(0, 0, 0, 0.08) !important;
}

.sidebar-expanded {
  width: 260px;
}

.sidebar-minimized {
  width: 75px;
}

/* Desktop: Always visible */
@media (min-width: 992px) {
  .app-sidebar.sidebar-hidden,
  .sidebar-rtl.app-sidebar.sidebar-hidden,
  .sidebar-ltr.app-sidebar.sidebar-hidden,
  .app-sidebar:not(.sidebar-rtl):not([dir="rtl"]).sidebar-hidden {
    transform: translateX(0) !important;
  }
  
  /* Ensure LTR sidebar is always visible on desktop */
  .sidebar-ltr.app-sidebar,
  .app-sidebar:not(.sidebar-rtl):not([dir="rtl"]) {
    transform: translateX(0) !important;
  }
  
  /* Ensure RTL sidebar is always visible on desktop */
  .sidebar-rtl.app-sidebar {
    transform: translateX(0) !important;
  }
}

/* Mobile Styles */
@media (max-width: 991px) {
  .app-sidebar {
    width: 280px !important;
    max-width: 85vw;
    z-index: 50;
  }

  .sidebar-ltr.app-sidebar {
    box-shadow: 2px 0 20px rgba(0, 0, 0, 0.15);
  }

  .sidebar-ltr.app-sidebar.sidebar-hidden {
    transform: translateX(-100%) !important;
  }

  .sidebar-ltr.app-sidebar.sidebar-visible {
    transform: translateX(0) !important;
  }

  .sidebar-rtl.app-sidebar {
    box-shadow: -2px 0 20px rgba(0, 0, 0, 0.15);
  }

  .sidebar-rtl.app-sidebar.sidebar-hidden {
    transform: translateX(100%) !important;
  }

  .sidebar-rtl.app-sidebar.sidebar-visible {
    transform: translateX(0) !important;
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

.sidebar-rtl .app-sidebar-header {
  text-align: right;
}

.sidebar-ltr .app-sidebar-header {
  text-align: left;
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

.sidebar-rtl .sidebar-toggle-minimized:hover {
  transform: translateX(50%) scale(1.05);
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

.sidebar-rtl .sidebar-toggle-minimized:active {
  transform: translateX(50%) scale(0.95);
}

.sidebar-toggle-btn:focus {
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
  outline: none;
}

.sidebar-toggle-expanded {
  top: 0.5rem;
}

.sidebar-ltr .sidebar-toggle-expanded {
  left: 0.5rem;
  right: auto;
}

.sidebar-rtl .sidebar-toggle-expanded {
  right: 0.5rem;
  left: auto;
}

.sidebar-toggle-minimized {
  top: 0.5rem;
  left: 50% !important;
  right: 50% !important;
  transform: translateX(-50%);
}

.sidebar-rtl .sidebar-toggle-minimized {
  transform: translateX(50%);
}

.sidebar-toggle-icon {
  transition: transform 0.3s ease;
  font-size: 0.875rem;
  font-weight: 600;
}

.sidebar-title-wrapper {
  padding-right: 40px;
}

.sidebar-rtl .sidebar-title-wrapper {
  padding-right: 40px;
  padding-left: 0;
}

.sidebar-ltr .sidebar-title-wrapper {
  padding-left: 40px;
  padding-right: 0;
}

.sidebar-title {
  font-size: 0.95rem;
  line-height: 1.4;
  transition: all 0.2s ease;
}

.sidebar-rtl .sidebar-title {
  text-align: right;
}

.sidebar-ltr .sidebar-title {
  text-align: left;
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

.user-dropdown-wrapper {
  width: 100%;
  padding: 0.5rem;
}

.sidebar-rtl .user-dropdown-wrapper {
  direction: rtl;
}

.sidebar-ltr .user-dropdown-wrapper {
  direction: ltr;
}

.menu-link {
  transition: all 0.2s ease;
  position: relative;
  cursor: pointer;
}

.menu-link:hover {
  background-color: #f8f9fa !important;
}

.menu-link:focus {
  outline: none;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.menu-link.bg-primary {
  background-color: rgba(13, 110, 253, 0.1) !important;
  color: #0d6efd !important;
  font-weight: 500;
}

.menu-link.bg-primary::before {
  content: "";
  position: absolute;
  top: 0;
  bottom: 0;
  width: 3px;
  background: #0d6efd;
}

.sidebar-rtl .menu-link.bg-primary::before {
  right: 0;
  left: auto;
  border-radius: 0 3px 3px 0;
}

.sidebar-ltr .menu-link.bg-primary::before {
  left: 0;
  right: auto;
  border-radius: 3px 0 0 3px;
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

.sidebar-rtl .menu-section-label {
  text-align: right;
}

.sidebar-ltr .menu-section-label {
  text-align: left;
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

.menu-bullet {
  flex-shrink: 0;
}

.sidebar-rtl .menu-bullet {
  margin-left: 0.75rem;
  margin-right: 0;
}

.sidebar-ltr .menu-bullet {
  margin-right: 0.75rem;
  margin-left: 0;
}

.menu-arrow {
  transition: transform 0.2s ease;
  flex-shrink: 0;
}

.menu-arrow.rotate-180 {
  transform: rotate(180deg);
}

.sidebar-rtl .menu-arrow.rotate-180 {
  transform: rotate(-180deg);
}

.app-sidebar-wrapper {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 transparent;
}

.sidebar-rtl .app-sidebar-wrapper {
  direction: rtl;
}

.sidebar-ltr .app-sidebar-wrapper {
  direction: ltr;
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

/* RTL scrollbar positioning */
.sidebar-rtl .app-sidebar-wrapper::-webkit-scrollbar {
  direction: rtl;
}

.sidebar-ltr .app-sidebar-wrapper::-webkit-scrollbar {
  direction: ltr;
}

/* Minimized state */
.sidebar-minimized .menu-link {
  justify-content: center !important;
  padding: 0.75rem !important;
}

.sidebar-rtl .menu-link {
  text-align: right;
}

.sidebar-ltr .menu-link {
  text-align: left;
}

.sidebar-minimized .menu-title,
.sidebar-minimized .menu-badge,
.sidebar-minimized .menu-arrow,
.sidebar-minimized .menu-section-label span {
  display: none !important;
}

.sidebar-minimized .menu-icon {
  margin-right: 0 !important;
  margin-left: 0 !important;
}

.sidebar-minimized .menu-sub {
  display: none !important;
}

.sidebar-rtl .menu-sub {
  padding-right: 0;
}

.sidebar-ltr .menu-sub {
  padding-left: 0;
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
  }
}

/* Live Indicator Styles */
.live-indicator {
  animation: pulse-live 2s ease-in-out infinite;
  z-index: 20 !important;
  display: block !important;
  visibility: visible !important;
  opacity: 1 !important;
  pointer-events: none;
}

.menu-icon {
  position: relative !important;
  overflow: visible !important;
}

.menu-icon .live-indicator {
  position: absolute !important;
  top: -2px !important;
  width: 10px !important;
  height: 10px !important;
  background: #10b981 !important;
  border-radius: 50% !important;
  border: 2px solid white !important;
  box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.4) !important;
  z-index: 20 !important;
  display: block !important;
  visibility: visible !important;
  opacity: 1 !important;
}

/* Live Indicator Dot - Small green dot badge */
.live-indicator-dot {
  width: 8px;
  height: 8px;
  background: #10b981;
  border-radius: 50%;
  border: 2px solid white;
  z-index: 20;
  display: block !important;
  visibility: visible !important;
  opacity: 1 !important;
  pointer-events: none;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

@keyframes pulse-live {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.8;
    transform: scale(1.1);
  }
}

.menu-link:hover .live-indicator {
  animation: pulse-live-hover 1s ease-in-out infinite;
}

@keyframes pulse-live-hover {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
    box-shadow: 0 0 0 1px rgba(16, 185, 129, 0.3), 0 0 8px rgba(16, 185, 129, 0.5);
  }
  50% {
    opacity: 1;
    transform: scale(1.15);
    box-shadow: 0 0 0 1px rgba(16, 185, 129, 0.5), 0 0 12px rgba(16, 185, 129, 0.7);
  }
}

@media (max-width: 575px) {
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
  .sidebar-rtl .app-sidebar {
    border-left: 2px solid #000;
    border-right: none;
  }

  .sidebar-ltr .app-sidebar {
    border-right: 2px solid #000;
    border-left: none;
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
