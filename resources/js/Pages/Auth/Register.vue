<template>
    <AppLayout>
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-50 via-white to-blue-50 py-12 px-4 sm:px-6 lg:px-8" :dir="direction">
            <div class="max-w-md w-full">
                <!-- Card Container -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                    <!-- Header with gradient -->
                    <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-8 py-6">
                        <h2 class="text-2xl font-bold text-white text-center">
                            {{ t('auth.register_title') }}
                        </h2>
                    </div>

                    <!-- Form Content -->
                    <div class="px-8 py-6">
                        <!-- Quick Register Buttons (Local Only) -->
                        <!-- Info: All registrations automatically get student role -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-xs font-semibold text-blue-800 mb-1">
                                {{ t('auth.registration_note') || 'Note: All new registrations will automatically receive student role. Admin can change your role later if needed.' }}
                            </p>
                        </div>
                        
                        <!-- Quick Register Buttons (Local Only) - Only for testing -->
                        <div v-if="isLocal" class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-xs font-semibold text-yellow-800 mb-2">{{ t('auth.quick_register') }}</p>
                            <p class="text-xs text-yellow-700 mb-3">{{ t('auth.quick_register_hint') }}</p>
                            <div class="grid grid-cols-1 gap-2">
                                <button
                                    @click="fillForm('student@lms.com')"
                                    type="button"
                                    class="px-3 py-2 text-xs font-medium text-yellow-800 bg-yellow-100 hover:bg-yellow-200 rounded-lg transition-colors border border-yellow-300"
                                >
                                    {{ t('auth.register_as_student') }}
                                </button>
                            </div>
                        </div>

                        <form class="space-y-5" @submit.prevent="submit">
                            <!-- Name Field -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('auth.name') }}
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    autocomplete="name"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all text-sm"
                                    :class="errors.name ? 'border-red-500' : ''"
                                    :placeholder="t('auth.name_placeholder')"
                                />
                                <div v-if="errors.name" class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ errors.name }}
                                </div>
                            </div>

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
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all text-sm"
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

                            <!-- National ID Field -->
                            <div>
                                <label for="national_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('auth.national_id') || 'National ID' }}
                                </label>
                                <input
                                    id="national_id"
                                    v-model="form.national_id"
                                    type="text"
                                    autocomplete="off"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all text-sm"
                                    :class="errors.national_id ? 'border-red-500' : ''"
                                    :placeholder="t('auth.national_id_placeholder') || 'National ID (Optional)'"
                                />
                                <div v-if="errors.national_id" class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ errors.national_id }}
                                </div>
                            </div>

                            <!-- Password Field -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('auth.password') }}
                                </label>
                                <input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    required
                                    autocomplete="new-password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all text-sm"
                                    :class="errors.password ? 'border-red-500' : ''"
                                    :placeholder="t('auth.password_placeholder')"
                                />
                                <div v-if="errors.password" class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ errors.password }}
                                </div>
                            </div>

                            <!-- Confirm Password Field -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('auth.confirm_password') }}
                                </label>
                                <input
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    required
                                    autocomplete="new-password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all text-sm"
                                    :placeholder="t('auth.confirm_password_placeholder')"
                                />
                            </div>

                            <!-- Submit Button -->
                            <div>
                                <button
                                    type="submit"
                                    :disabled="processing"
                                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all transform hover:scale-[1.02]"
                                >
                                    <span v-if="processing" class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ t('auth.creating_account') }}
                                    </span>
                                    <span v-else>{{ t('auth.register') }}</span>
                                </button>
                            </div>

                            <!-- Login Link -->
                            <div class="text-center">
                                <p class="text-sm text-gray-600">
                                    {{ t('auth.already_have_account') }}
                                    <Link :href="route('login')" class="font-medium text-purple-600 hover:text-purple-500 transition-colors">
                                        {{ t('auth.sign_in_link') }}
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
    name: '',
    email: '',
    national_id: '',
    password: 'password123',
    password_confirmation: 'password123',
    // Note: role is NOT included - backend always sets it to 'student'
});

const processing = computed(() => form.processing);

const fillForm = (email) => {
    form.name = 'Student User';
    form.email = email;
    form.national_id = '';
    form.password = 'password123';
    form.password_confirmation = 'password123';
};

const submit = () => {
    // Remove role if somehow it exists - backend will ignore it anyway
    const data = { ...form.data() };
    delete data.role;
    form.transform(() => data).post(route('register'));
};
</script>
