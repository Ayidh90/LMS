<template>
    <div class="min-h-screen bg-gray-50" :dir="direction">
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <Link :href="homeRoute" class="text-xl font-bold text-indigo-600">
                                {{ navbarTitle }}
                            </Link>
                        </div>
                        <!-- <div v-if="showDashboard" class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <Link :href="dashboardRoute" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                {{ t('common.dashboard') }}
                            </Link>
                        </div> -->
                    </div>
                    <div class="flex items-center gap-4">
                        <LanguageSwitcher />
                        <template v-if="page.props.auth?.user">
                            <!-- User Info & Dropdown -->
                            <div class="flex items-center gap-3">
                                <div class="hidden sm:block text-right">
                                    <!-- <p class="text-sm font-medium text-gray-900">{{ page.props.auth?.user?.name }}</p>
                                    <p class="text-xs text-gray-500 capitalize">{{ page.props.auth?.user?.role }}</p> -->
                                </div>
                                <UserDropdown />
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <slot />
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useDirection } from '@/composables/useDirection';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import UserDropdown from '@/Components/UserDropdown.vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const { direction } = useDirection();
const { t } = useTranslation();
const { route } = useRoute();
const page = usePage();

const user = computed(() => page.props.auth?.user);

const homeRoute = computed(() => {
    if (!user.value) return route('welcome');
    
    const role = user.value.role;
    const isAdmin = user.value.is_admin === 1 || user.value.is_admin === true;
    
    // Route to appropriate dashboard based on role
    if ((role === 'super_admin' || role === 'admin') && isAdmin) {
        return route('admin.dashboard');
    } else if (role === 'student') {
        return route('student.dashboard');
    } else if (role === 'instructor') {
        return route('instructor.dashboard');
    }
    
    return route('welcome');
});

const navbarTitle = computed(() => {
    if (!user.value) return 'LMS';
    
    const role = user.value.role;
    const isAdmin = user.value.is_admin === 1 || user.value.is_admin === true;
    
    // Show "Dashboard" for students and instructors, "LMS" for others
    if (role === 'student') {
        return t('student.dashboard') || 'Dashboard';
    } else if (role === 'instructor') {
        return t('instructor.dashboard') || 'Dashboard';
    } else if ((role === 'super_admin' || role === 'admin') && isAdmin) {
        return t('admin.dashboard') || 'Dashboard';
    }
    
    return 'LMS';
});

const dashboardRoute = computed(() => {
    if (!user.value) return route('welcome');
    
    const role = user.value.role;
    const isAdmin = user.value.is_admin === 1 || user.value.is_admin === true;
    
    // Only admins with is_admin == 1 get dashboard, students and instructors don't have dashboard
    if ((role === 'super_admin' || role === 'admin') && isAdmin) {
        return route('admin.dashboard');
    }
    return route('welcome');
});

const showDashboard = computed(() => {
    const user = page.props.auth?.user;
    if (!user) return false;
    const role = user.role;
    const isAdmin = user.is_admin === 1 || user.is_admin === true;
    // Only show dashboard link for admins with is_admin == 1
    return (role === 'super_admin' || role === 'admin') && isAdmin;
});

const logout = () => {
    router.post(route('logout'));
};
</script>

