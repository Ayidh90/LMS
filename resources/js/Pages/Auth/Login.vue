<template>
    <AppLayout>
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-purple-50 py-12 px-4 sm:px-6 lg:px-8" :dir="direction">
            <div class="max-w-md w-full">
                <!-- Card Container -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                    <!-- Header with gradient -->
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
                        <h2 class="text-2xl font-bold text-white text-center">
                            {{ t('auth.login_title') }}
                        </h2>
                    </div>

                    <!-- Form Content -->
                    <div class="px-8 py-6">
                        <!-- Quick Login Buttons (Local Only) -->
                        <div v-if="isLocal" class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-xs font-semibold text-yellow-800 mb-2">{{ t('auth.quick_login') }}</p>
                            <p class="text-xs text-yellow-700 mb-3">{{ t('auth.quick_login_hint') }}</p>
                            <div class="grid grid-cols-3 gap-2">
                                <button
                                    @click="fillCredentials('admin@lms.com')"
                                    type="button"
                                    class="px-3 py-2 text-xs font-medium text-yellow-800 bg-yellow-100 hover:bg-yellow-200 rounded-lg transition-colors border border-yellow-300"
                                >
                                    {{ t('auth.login_as_admin') }}
                                </button>
                                <button
                                    @click="fillCredentials('student@lms.com')"
                                    type="button"
                                    class="px-3 py-2 text-xs font-medium text-yellow-800 bg-yellow-100 hover:bg-yellow-200 rounded-lg transition-colors border border-yellow-300"
                                >
                                    {{ t('auth.login_as_student') }}
                                </button>
                                <button
                                    @click="fillCredentials('instructor@lms.com')"
                                    type="button"
                                    class="px-3 py-2 text-xs font-medium text-yellow-800 bg-yellow-100 hover:bg-yellow-200 rounded-lg transition-colors border border-yellow-300"
                                >
                                    {{ t('auth.login_as_instructor') }}
                                </button>
                            </div>
                        </div>

                        <form class="space-y-5" @submit.prevent="submit">
                            <!-- Email Field -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('auth.email') }}
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    autocomplete="email"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm"
                                    :class="errors.email ? 'border-red-500' : ''"
                                    :placeholder="t('auth.email')"
                                />
                                <div v-if="errors.email" class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ errors.email }}
                                </div>
                            </div>

                            <!-- Password Field -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('auth.password_optional') }}
                                </label>
                                <input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    autocomplete="current-password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm"
                                    :class="errors.password ? 'border-red-500' : ''"
                                    :placeholder="t('auth.password')"
                                />
                                <p class="mt-1 text-xs text-gray-500">{{ t('auth.password_optional_hint') }}</p>
                                <div v-if="errors.password" class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ errors.password }}
                                </div>
                            </div>

                            <!-- Remember Me -->
                            <div class="flex items-center">
                                <input
                                    id="remember"
                                    v-model="form.remember"
                                    type="checkbox"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                />
                                <label for="remember" class="ml-2 block text-sm text-gray-700">
                                    {{ t('auth.remember_me') }}
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div>
                                <button
                                    type="submit"
                                    :disabled="processing"
                                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all transform hover:scale-[1.02]"
                                >
                                    <span v-if="processing" class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ t('auth.signing_in') }}
                                    </span>
                                    <span v-else>{{ t('auth.sign_in') }}</span>
                                </button>
                            </div>

                            <!-- Register Link -->
                            <div class="text-center">
                                <p class="text-sm text-gray-600">
                                    {{ t('auth.no_account') }}
                                    <Link :href="route('register')" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                                        {{ t('auth.register_link') }}
                                    </Link>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useDirection } from '@/composables/useDirection';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { usePage } from '@inertiajs/vue3';
import { useForm, Link } from '@inertiajs/vue3';

defineProps({
    errors: Object,
});

const { direction } = useDirection();
const { t } = useTranslation();
const { route } = useRoute();
const page = usePage();

const isLocal = computed(() => page.props.isLocal || false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const processing = computed(() => form.processing);

const fillCredentials = (email) => {
    form.email = email;
    form.password = '';
    // Auto-submit after filling
    setTimeout(() => {
        form.post(route('login'));
    }, 100);
};

const submit = () => {
    form.post(route('login'));
};
</script>
