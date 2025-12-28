<template>
    <AppLayout>
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-purple-50 py-12 px-4 sm:px-6 lg:px-8" :dir="direction">
            <div class="max-w-md w-full">
                <!-- Card Container -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                    <!-- Header with gradient -->
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
                        <h2 class="text-2xl font-bold text-white text-center">
                            {{ t('auth.forgot_password_title') }}
                        </h2>
                    </div>

                    <!-- Form Content -->
                    <div class="px-8 py-6">
                        <p class="text-sm text-gray-600 mb-6">
                            {{ t('auth.forgot_password_description') }}
                        </p>

                        <form class="space-y-5" @submit.prevent="submit">
                            <!-- Email or National ID Field -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('auth.email_or_national_id') }} <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="text"
                                    required
                                    autocomplete="username"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm"
                                    :class="errors.email ? 'border-red-500' : ''"
                                    :placeholder="t('auth.email_or_national_id_placeholder')"
                                />
                                <p class="mt-1 text-xs text-gray-500">{{ t('auth.email_or_national_id_hint') }}</p>
                                <div v-if="errors.email" class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ errors.email }}
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all transform hover:scale-[1.02]"
                                >
                                    <span v-if="form.processing" class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ t('auth.processing') }}
                                    </span>
                                    <span v-else>{{ t('auth.send_reset_link') }}</span>
                                </button>
                            </div>

                        </form>

                        <!-- Back to Login Link -->
                        <div class="text-center mt-5">
                            <Link :href="route('login')" class="text-sm text-blue-600 hover:text-blue-500 transition-colors">
                                {{ t('auth.back_to_login') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useDirection } from '@/composables/useDirection';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useForm, Link } from '@inertiajs/vue3';

defineProps({
    errors: Object,
});

const { direction } = useDirection();
const { t } = useTranslation();
const { route } = useRoute();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

