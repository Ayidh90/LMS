<template>
    <div v-if="auth" class="relative" :dir="direction">
        <button
            @click="toggleDropdown"
            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors"
        >
            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                {{ getInitials(auth?.name) }}
            </div>
            <span class="text-sm font-medium text-gray-700 hidden md:block">{{ auth?.name }}</span>
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div
            v-if="showDropdown"
            class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 z-50 overflow-hidden"
        >
            <div class="p-3 border-b border-gray-200">
                <p class="text-sm font-semibold text-gray-900">{{ auth?.name }}</p>
                <p class="text-xs text-gray-500">{{ auth?.email }}</p>
            </div>
            
            <div class="py-1">
                <Link
                    v-if="showProfile"
                    :href="route('profile.show')"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                    @click="showDropdown = false"
                >
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ t('profile.my_profile') }}
                </Link>
                
                <Link
                    v-if="showDashboard"
                    :href="dashboardRoute"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                    @click="showDropdown = false"
                >
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    {{ t('common.dashboard') }}
                </Link>
                
                <!-- <Link
                    :href="route('courses.index')"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                    @click="showDropdown = false"
                >
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    {{ t('courses.my_courses') }}
                </Link> -->
            </div>
            
            <div class="border-t border-gray-200 py-1">
                <button
                    @click="logout"
                    class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    {{ t('common.logout') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import { useDirection } from '@/composables/useDirection';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';

const { direction } = useDirection();
const { t } = useTranslation();
const { route } = useRoute();
const page = usePage();

const showDropdown = ref(false);
const auth = computed(() => page.props.auth?.user);

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

const logout = () => {
    router.post(route('logout'));
};

const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) {
        showDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

