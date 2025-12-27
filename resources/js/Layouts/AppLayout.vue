<template>
    <div class="min-h-screen bg-gray-50" :dir="direction">
        <!-- Navbar -->
        <nav class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <Link :href="route('welcome')" class="text-2xl font-bold text-blue-600 flex items-center gap-2">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span>LMS</span>
                        </Link>
                        <div class="hidden md:ml-10 md:flex md:space-x-8">
                       
                       
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <LanguageSwitcher />
                        <template v-if="auth && auth.user">
                            <!-- User Info & Dropdown -->
                            <div class="flex items-center gap-3">
                                <div class="hidden sm:block text-right">
                                    <!-- <p class="text-sm font-medium text-gray-900">{{ auth.user?.name }}</p>
                                    <p class="text-xs text-gray-500 capitalize">{{ auth.user?.role }}</p> -->
                                </div>
                                <UserDropdown />
                            </div>
                        </template>
                        <template v-else>
                            <Link :href="route('login')" class="text-sm text-gray-700 hover:text-gray-900 font-medium">
                                {{ t('common.login') }}
                            </Link>
                            <!-- <Link :href="route('register')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors">
                                {{ t('common.register') }}
                            </Link> -->
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            LMS
                        </h3>
                        <p class="text-gray-400 text-sm">Learn and grow with our comprehensive courses.</p>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4">{{ t('footer.quick_links') }}</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li><Link :href="route('welcome')" class="hover:text-white transition-colors">{{ t('common.home') }}</Link></li>
                            <li><Link :href="route('welcome')" class="hover:text-white transition-colors">{{ t('courses.title') }}</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4">{{ t('footer.support') }}</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li><a href="#" class="hover:text-white transition-colors">{{ t('footer.contact_us') }}</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">{{ t('footer.help_center') }}</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4">{{ t('footer.follow_us') }}</h4>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-white transition-colors" aria-label="Facebook">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors" aria-label="Twitter">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors" aria-label="LinkedIn">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t border-gray-700 text-center text-sm text-gray-400">
                    <p>&copy; {{ new Date().getFullYear() }} LMS. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useDirection } from '../composables/useDirection';
import { useTranslation } from '../composables/useTranslation';
import { useRoute } from '../composables/useRoute';
import { usePage, Link } from '@inertiajs/vue3';
import LanguageSwitcher from '../Components/LanguageSwitcher.vue';
import UserDropdown from '../Components/UserDropdown.vue';

const { direction } = useDirection();
const { t } = useTranslation();
const { route } = useRoute();
const page = usePage();

const auth = computed(() => page.props.auth);

const dashboardRoute = computed(() => {
    const user = auth.value?.user;
    if (!user) return route('welcome');
    
    const role = user.role;
    const isAdmin = user.is_admin === 1 || user.is_admin === true;
    
    // Only admins with is_admin == 1 get dashboard, students and instructors don't have dashboard
    if ((role === 'super_admin' || role === 'admin') && isAdmin) {
        return route('admin.dashboard');
    }
    return route('welcome');
});

const isActive = (url) => {
    if (typeof window === 'undefined') return false;
    const path = window.location.pathname;
    return path === url || path.startsWith(url + '/');
};
</script>
