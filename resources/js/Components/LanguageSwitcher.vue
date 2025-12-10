<template>
    <div class="relative" :dir="direction">
        <button
            @click="toggleDropdown"
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors"
        >
            <span class="text-lg">{{ currentLocale === 'ar' ? '🇸🇦' : '🇬🇧' }}</span>
            <span class="hidden sm:inline">{{ currentLocale === 'ar' ? 'العربية' : 'English' }}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div
            v-if="showDropdown"
            class="absolute right-0 mt-2 w-40 bg-white rounded-xl shadow-lg border border-gray-200 z-50 overflow-hidden"
        >
            <button
                @click="switchLanguage('en')"
                class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                :class="{ 'bg-blue-50 text-blue-700': currentLocale === 'en' }"
            >
                <span class="text-xl">🇬🇧</span>
                <span>English</span>
                <svg v-if="currentLocale === 'en'" class="w-4 h-4 ml-auto text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
            <button
                @click="switchLanguage('ar')"
                class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors border-t border-gray-100"
                :class="{ 'bg-blue-50 text-blue-700': currentLocale === 'ar' }"
            >
                <span class="text-xl">🇸🇦</span>
                <span>العربية</span>
                <svg v-if="currentLocale === 'ar'" class="w-4 h-4 ml-auto text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useTranslation } from '@/composables/useTranslation';
import { useDirection } from '@/composables/useDirection';
import { usePage } from '@inertiajs/vue3';

const { locale, setLocale } = useTranslation();
const { direction } = useDirection();
const page = usePage();
const showDropdown = ref(false);

const currentLocale = computed(() => {
    return page.props.locale || locale.value || 'ar';
});

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
};

const switchLanguage = (newLocale) => {
    if (newLocale !== currentLocale.value) {
        setLocale(newLocale);
        showDropdown.value = false;
    }
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
