<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" :dir="direction">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="$emit('close')"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl p-6 transform transition-all max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6" :class="direction === 'rtl' ? 'flex-row-reverse' : ''">
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ isEdit ? (t('admin.edit_lesson') || 'Edit Lesson') : (t('admin.add_lesson') || 'Add Lesson') }}
                        </h3>
                        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="bi bi-x-lg text-2xl"></i>
                        </button>
                    </div>
                    <form @submit.prevent="submit" class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ t('lessons.fields.title') || 'Title' }} (English) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="formData.title"
                                    type="text"
                                    class="form-input"
                                    :class="{ 'border-red-500': errors.title }"
                                    :placeholder="t('lessons.placeholders.title') || 'Enter lesson title'"
                                />
                                <p v-if="errors.title" class="form-error">{{ errors.title }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ t('lessons.fields.title') || 'Title' }} (العربية)
                                </label>
                                <input
                                    v-model="formData.title_ar"
                                    type="text"
                                    dir="rtl"
                                    class="form-input"
                                    :placeholder="'أدخل عنوان الدرس'"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ t('lessons.fields.type') || 'Type' }} <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="formData.type"
                                    class="form-select"
                                    :class="{ 'border-red-500': errors.type }"
                                    @change="handleTypeChange"
                                >
                                    <option value="">{{ t('common.select') || 'Select' }}</option>
                                    <option v-for="type in lessonTypes" :key="type.value" :value="type.value">
                                        {{ t(`lessons.types.${type.value}`) || type.label }}
                                    </option>
                                </select>
                                <p v-if="errors.type" class="form-error">{{ errors.type }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ t('lessons.fields.section') || 'Section' }}
                                    <span v-if="isAdmin" class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="formData.section_id"
                                    class="form-select"
                                    :class="{ 'border-red-500': errors.section_id }"
                                    :required="isAdmin"
                                >
                                    <option :value="null">{{ t('lessons.no_section') || 'No Section' }}</option>
                                    <option v-for="section in sections" :key="section.id" :value="section.id">
                                        {{ section.translated_title || section.title }}
                                    </option>
                                </select>
                                <p v-if="errors.section_id" class="form-error">{{ errors.section_id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ t('lessons.fields.order') || 'Order' }}
                                </label>
                                <input
                                    v-model.number="formData.order"
                                    type="number"
                                    min="0"
                                    class="form-input"
                                    :class="{ 'border-red-500': errors.order }"
                                />
                                <p v-if="errors.order" class="form-error">{{ errors.order }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ t('lessons.fields.duration') || 'Duration' }} ({{ t('common.minutes') || 'minutes' }})
                                </label>
                                <input
                                    v-model.number="formData.duration_minutes"
                                    type="number"
                                    min="0"
                                    class="form-input"
                                    @blur="formData.duration_minutes = formData.duration_minutes || 0"
                                />
                            </div>
                        </div>
                        <div v-if="['youtube_video', 'google_drive_video', 'embed_frame'].includes(formData.type)">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('lessons.fields.video_url') || 'Video/Embed URL' }}
                            </label>
                            <input
                                v-model="formData.video_url"
                                type="url"
                                class="form-input"
                                :placeholder="t('lessons.placeholders.video_url') || 'https://...'"
                            />
                        </div>
                        <div v-if="formData.type === 'live'" class="space-y-5">
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4" :dir="direction">
                                <div class="flex items-start gap-3" :class="direction === 'rtl' ? 'flex-row-reverse' : ''">
                                    <i class="bi bi-info-circle text-blue-600 text-xl mt-0.5 flex-shrink-0"></i>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-blue-900 mb-1">
                                            {{ t('lessons.live.info_title') || 'Live Lesson Requirements' }}
                                        </p>
                                        <p class="text-xs text-blue-700">
                                            {{ t('lessons.live.info_message') || 'Please provide the meeting date and time. A meeting link will be auto-generated if not provided.' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        {{ t('lessons.live.meeting_date') || t('lessons.fields.live_meeting_date') || 'Meeting Date & Time' }} <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        ref="liveMeetingDateInput"
                                        v-model="formData.live_meeting_date"
                                        type="datetime-local"
                                        class="form-input"
                                        :class="{ 'border-red-500': errors.live_meeting_date }"
                                        required
                                        @focus="hasInteractedWithDate = true"
                                        @blur="hasInteractedWithDate = true"
                                        @input="hasInteractedWithDate = true"
                                    />
                                    <p v-if="errors.live_meeting_date" class="form-error">{{ errors.live_meeting_date }}</p>
                                    <p v-if="showDateWarning" class="mt-1 text-xs text-gray-600" :dir="direction">
                                        <i class="bi bi-info-circle me-1"></i>
                                        {{ t('lessons.live.date_required_warning') || 'Live lecture date and time is required for live lessons.' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        {{ t('lessons.live.meeting_link') || t('lessons.fields.live_meeting_link') || 'Meeting Link' }}
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <input
                                            v-model="formData.live_meeting_link"
                                            type="url"
                                            class="form-input flex-1"
                                            :class="{ 'border-red-500': errors.live_meeting_link }"
                                            :placeholder="t('lessons.placeholders.meeting_link') || 'https://meet.jit.si/... or enter your own link'"
                                        />
                                        <a
                                            v-if="formData.live_meeting_link"
                                            :href="formData.live_meeting_link"
                                            target="_blank"
                                            class="btn-secondary px-4 py-3 whitespace-nowrap"
                                            :title="t('lessons.live.join_meeting') || 'Join Meeting'"
                                        >
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                    </div>
                                    <p v-if="errors.live_meeting_link" class="form-error">{{ errors.live_meeting_link }}</p>
                                    <p class="mt-2 text-xs text-gray-500">
                                        {{ t('lessons.live.meeting_link_hint') || 'Leave empty to auto-generate a Jitsi Meet link, or enter your own Google Meet/Zoom/Teams link' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('lessons.fields.description') || 'Description' }} (English)
                            </label>
                            <textarea
                                v-model="formData.description"
                                rows="3"
                                class="form-textarea"
                                :placeholder="t('lessons.placeholders.description') || 'Enter lesson description'"
                            ></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('lessons.fields.description') || 'Description' }} (العربية)
                            </label>
                            <textarea
                                v-model="formData.description_ar"
                                rows="3"
                                dir="rtl"
                                class="form-textarea"
                                :placeholder="'أدخل وصف الدرس'"
                            ></textarea>
                        </div>
                        <div v-if="formData.type === 'text'">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('lessons.fields.content') || 'Content' }} (English)
                            </label>
                            <textarea
                                v-model="formData.content"
                                rows="6"
                                class="form-textarea font-mono text-sm"
                                :placeholder="t('lessons.placeholders.content') || 'Enter lesson content (supports HTML)'"
                            ></textarea>
                        </div>
                        <div v-if="formData.type === 'text'">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('lessons.fields.content') || 'Content' }} (العربية)
                            </label>
                            <textarea
                                v-model="formData.content_ar"
                                rows="6"
                                dir="rtl"
                                class="form-textarea font-mono text-sm"
                                :placeholder="'أدخل محتوى الدرس'"
                            ></textarea>
                        </div>
                        <div class="flex items-center gap-3 pt-4" :class="direction === 'rtl' ? 'flex-row-reverse justify-start' : 'justify-end'">
                            <button type="button" @click="$emit('close')" class="btn-secondary">
                                {{ t('common.cancel') || 'Cancel' }}
                            </button>
                            <button type="submit" :disabled="processing" class="btn-primary">
                                {{ isEdit ? (t('common.save_changes') || 'Save Changes') : (t('common.create') || 'Create') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { computed, watch, ref, nextTick } from 'vue';
import { useTranslation } from '@/composables/useTranslation';
import { useModal } from '@/composables/useModal';
import { usePermissions } from '@/composables/usePermissions';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    lesson: {
        type: Object,
        default: null,
    },
    formData: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    processing: {
        type: Boolean,
        default: false,
    },
    sections: {
        type: Array,
        default: () => [],
    },
    lessonTypes: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close', 'submit', 'type-change']);

const { t } = useTranslation();
const { setModalOpen } = useModal();
const { isAdmin } = usePermissions();
const page = usePage();

const isEdit = computed(() => !!props.lesson);
const liveMeetingDateInput = ref(null);
const direction = computed(() => page.props.direction || 'ltr');

// Track if user has interacted with the date field
const hasInteractedWithDate = ref(false);

// Check if live meeting date is empty
const isLiveMeetingDateEmpty = computed(() => {
    const date = props.formData.live_meeting_date;
    return !date || date === '' || date === null || (typeof date === 'string' && date.trim() === '');
});

// Check if should show warning message (only show if user has interacted with the date field)
const showDateWarning = computed(() => {
    // Don't show warning if there's already a backend validation error (it will show the error instead)
    if (props.errors.live_meeting_date) {
        return false;
    }
    
    // Only show warning if:
    // 1. Type is 'live'
    // 2. Date is empty
    // 3. User has interacted with the date field (focused, blurred, or input)
    // This prevents showing the warning immediately when selecting 'live' type
    return props.formData.type === 'live' && 
           isLiveMeetingDateEmpty.value && 
           hasInteractedWithDate.value;
});

// Update modal state when show prop changes
watch(() => props.show, (isOpen) => {
    setModalOpen(isOpen);
    // Reset interaction flag when modal closes
    if (!isOpen) {
        hasInteractedWithDate.value = false;
    }
}, { immediate: true });

// Watch for type changes to handle live lesson specific behavior
watch(() => props.formData.type, async (newType, oldType) => {
    // Reset interaction flag when switching types
    hasInteractedWithDate.value = false;
    
    // When switching to 'live', focus on the date input after a short delay
    if (newType === 'live' && oldType !== 'live') {
        await nextTick();
        if (liveMeetingDateInput.value) {
            // Small delay to ensure the input is rendered
            setTimeout(() => {
                liveMeetingDateInput.value?.focus();
            }, 100);
        }
    }
});

const handleTypeChange = () => {
    // Emit type change event for parent component handling
    emit('type-change', props.formData.type);
};

const submit = () => {
    // Validate section is required for admin users
    if (isAdmin.value && (!props.formData.section_id || props.formData.section_id === null || props.formData.section_id === '')) {
        // The backend will also validate this, but we can show a client-side error
        // The error will be handled by the backend validation
        emit('submit', props.formData);
        return;
    }
    
    emit('submit', props.formData);
};
</script>

<style scoped>
.form-input,
.form-textarea,
.form-select {
    @apply w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white;
}

.form-textarea {
    @apply resize-none;
}

.form-error {
    @apply mt-1 text-sm text-red-500;
}

.btn-secondary {
    @apply px-5 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 font-medium transition-all;
}

.btn-primary {
    @apply px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed;
}
</style>

