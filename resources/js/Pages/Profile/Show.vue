<template>
    <component :is="layout">
        <Head :title="user?.name || t('profile.title') || 'Profile'" />
        <div class="min-h-screen bg-slate-100" :dir="direction">
            <!-- Top Bar -->
            <div class="bg-slate-800 text-white py-2 text-sm">
                <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            +1(323) 555-9876
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            info@lms.com
                        </span>
                    </div>
                </div>
            </div>

            <!-- Hero Section -->
            <div class="relative bg-gradient-to-r from-slate-700 to-slate-800 py-16 overflow-hidden">
                <div class="absolute inset-0 opacity-10 bg-pattern"></div>
                <div class="max-w-7xl mx-auto px-4 relative z-10">
                    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>
                        <nav class="text-sm text-gray-500 mb-2">
                            <Link :href="route('welcome')" class="hover:text-blue-600">LMS</Link>
                            <span class="mx-2">›</span>
                            <span class="text-gray-900">{{ t('profile.my_profile') }}</span>
                        </nav>
                        <h1 class="text-2xl font-bold text-gray-900">{{ t('profile.my_profile') }}</h1>
                    </div>
                </div>
            </div>

            <div class="max-w-6xl mx-auto py-8 px-4">
                <!-- Success Alert -->
                <div v-if="page.props.flash?.success" class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-green-800 font-medium">{{ page.props.flash.success }}</p>
                </div>

                <!-- Tabs Navigation -->
                <div class="bg-white rounded-xl shadow-sm mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px" aria-label="Tabs">
                            <!-- Courses Tab (Default for students) -->
                            <button
                                v-if="user.role === 'student'"
                                @click="activeTab = 'courses'"
                                :class="activeTab === 'courses' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors"
                            >
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    {{ t('profile.my_courses') }}
                                    <span v-if="myBatches && myBatches.length > 0" class="bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full text-xs font-semibold">{{ myBatches.length }}</span>
                                </div>
                            </button>
                            
                            <!-- Assigned Courses Tab (Default for instructors) -->
                            <button
                                v-if="user.role === 'instructor'"
                                @click="activeTab = 'assigned'"
                                :class="activeTab === 'assigned' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors"
                            >
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    {{ t('profile.assigned_courses') }}
                                    <span v-if="assignedBatches && assignedBatches.length > 0" class="bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full text-xs font-semibold">{{ assignedBatches.length }}</span>
                                </div>
                            </button>
                            
                            <!-- Profile Settings Tab (Hidden by default, shown as secondary) -->
                            <button
                                @click="activeTab = 'profile'"
                                :class="activeTab === 'profile' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors"
                            >
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ t('profile.my_profile') || 'Settings' }}
                                </div>
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Profile Tab -->
                <div v-show="activeTab === 'profile'" class="bg-white rounded-xl shadow-sm p-8">
                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <!-- Avatar -->
                            <div class="flex items-center gap-6">
                                <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                                    {{ getInitials(user.name) }}
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">{{ user.name }}</h2>
                                    <p class="text-gray-500">{{ user.email }}</p>
                                    <span class="inline-block mt-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium capitalize">
                                        {{ user.role }}
                                    </span>
                                </div>
                            </div>

                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('profile.fields.name') }}</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': form.errors.name }"
                                />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('profile.fields.email') }}</label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': form.errors.email }"
                                />
                                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                            </div>

                            <!-- Bio -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('profile.fields.bio') }}</label>
                                <textarea
                                    v-model="form.bio"
                                    rows="4"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': form.errors.bio }"
                                ></textarea>
                                <p v-if="form.errors.bio" class="mt-1 text-sm text-red-600">{{ form.errors.bio }}</p>
                            </div>

                            <!-- Password Section -->
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ t('profile.change_password') }}</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('profile.fields.new_password') }}</label>
                                        <input
                                            v-model="form.password"
                                            type="password"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            :class="{ 'border-red-500': form.errors.password }"
                                        />
                                        <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('profile.fields.confirm_password') }}</label>
                                        <input
                                            v-model="form.password_confirmation"
                                            type="password"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end gap-4 pt-4 border-t border-gray-200">
                                <Link
                                    :href="dashboardRoute"
                                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors"
                                >
                                    {{ t('common.cancel') }}
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors disabled:opacity-50"
                                >
                                    {{ form.processing ? t('common.saving') : t('common.save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- My Courses Tab (For Students) -->
                <div v-show="activeTab === 'courses'" class="bg-white rounded-xl shadow-sm p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">{{ t('profile.my_courses') }}</h2>
                        <p class="text-gray-500 text-sm mt-1">{{ t('profile.my_courses_description') || 'Your enrolled courses will appear here. Contact admin to enroll in new courses.' }}</p>
                    </div>
                    
                    <div v-if="myBatches && myBatches.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="batch in myBatches" :key="batch.id" class="bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900 mb-2 line-clamp-2">{{ batch.course?.translated_title || batch.course?.title || 'N/A' }}</h3>
                                    <p class="text-sm text-gray-500 mb-1">{{ batch.translated_name || batch.name }}</p>
                                    <p v-if="batch.instructor" class="text-sm text-gray-400">{{ t('courses.instructor') }}: {{ batch.instructor.name }}</p>
                                </div>
                                <div class="w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden ml-3">
                                    <img :src="getCourseImage(batch.course || {})" :alt="batch.course?.translated_title || batch.course?.title" class="w-full h-full object-cover" @error="handleImageError($event)" />
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                                    <span>{{ t('profile.progress') }}</span>
                                    <span class="font-semibold">{{ batch.progress || 0 }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all" :style="{ width: (batch.progress || 0) + '%' }"></div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ batch.enrolled_at ? new Date(batch.enrolled_at).toLocaleDateString() : '' }}</span>
                                </div>
                                <Link
                                    v-if="batch.course?.slug"
                                    :href="route('courses.play', batch.course.slug)"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                >
                                    {{ t('courses.actions.continue') }}
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <p class="text-gray-500 text-lg mb-4">{{ t('profile.my_courses') }}</p>
                        <p class="text-gray-400 mb-6">{{ t('courses.no_courses') || 'You are not enrolled in any courses yet.' }}</p>
                        <p class="text-gray-500 text-sm">{{ t('profile.contact_admin_to_enroll') || 'Please contact the administrator to enroll in courses.' }}</p>
                    </div>
                </div>

                <!-- Assigned Courses Tab (For Instructors) -->
                <div v-show="activeTab === 'assigned'" class="bg-white rounded-xl shadow-sm p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ t('profile.assigned_courses') }}</h2>
                    
                    <div v-if="assignedBatches && assignedBatches.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="batch in assignedBatches" :key="batch.id" class="bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900 mb-2 line-clamp-2">{{ batch.course?.translated_title || batch.course?.title || 'N/A' }}</h3>
                                    <p class="text-sm text-gray-500 mb-1">{{ batch.translated_name || batch.name }}</p>
                                </div>
                                <div class="w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden ml-3">
                                    <img :src="getCourseImage(batch.course || {})" :alt="batch.course?.translated_title || batch.course?.title" class="w-full h-full object-cover" @error="handleImageError($event)" />
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <span>{{ batch.enrollments_count || 0 }} {{ t('profile.enrolled_students') }}</span>
                                </div>
                                <Link
                                    v-if="batch.course?.slug"
                                    :href="route('courses.show', batch.course.slug)"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                >
                                    {{ t('courses.actions.view') }}
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        <p class="text-gray-500 text-lg mb-4">{{ t('profile.assigned_courses') }}</p>
                        <p class="text-gray-400 mb-6">{{ t('profile.no_assigned_courses') || 'You have not been assigned to any courses yet.' }}</p>
                        <p class="text-gray-500 text-sm">{{ t('profile.contact_admin_to_assign') || 'Please contact the administrator to get assigned to courses.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </component>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useDirection } from '@/composables/useDirection';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    assignedBatches: {
        type: Array,
        default: () => [],
    },
    myBatches: {
        type: Array,
        default: () => [],
    },
    // Keep for backward compatibility
    assignedCourses: {
        type: Array,
        default: () => [],
    },
    myCourses: {
        type: Array,
        default: () => [],
    },
});

const { direction } = useDirection();
const { t } = useTranslation();
const { route } = useRoute();
const page = usePage();

const auth = computed(() => page.props.auth?.user);
const layout = computed(() => auth.value ? AuthenticatedLayout : AppLayout);

// Active tab state - Default to courses/batches, not profile
const activeTab = ref('profile');

// Set default tab based on user role - show courses/batches tab by default
if (props.user.role === 'student') {
    activeTab.value = 'courses';
} else if (props.user.role === 'instructor') {
    activeTab.value = 'assigned';
} else {
    activeTab.value = 'profile';
}

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

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    bio: props.user.bio || '',
    password: '',
    password_confirmation: '',
});

// Default course image
const defaultCourseImage = '/images/default-course.avif';

const getCourseImage = (course) => {
    return course.thumbnail_url || course.thumbnail || defaultCourseImage;
};

const handleImageError = (event) => {
    if (event.target.src !== defaultCourseImage) {
        event.target.src = defaultCourseImage;
    }
};

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};

const submit = () => {
    form.put(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Success message is handled by flash message
        },
    });
};

// Auto-hide success message after 5 seconds
watch(() => page.props.flash?.success, (success) => {
    if (success) {
        setTimeout(() => {
            router.reload({ only: [] });
        }, 5000);
    }
});
</script>

