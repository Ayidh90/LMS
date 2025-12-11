<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="$emit('close')"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl p-6 transform transition-all max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6">
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
                                </label>
                                <select
                                    v-model="formData.section_id"
                                    class="form-select"
                                >
                                    <option :value="null">{{ t('lessons.no_section') || 'No Section' }}</option>
                                    <option v-for="section in sections" :key="section.id" :value="section.id">
                                        {{ section.title }}
                                    </option>
                                </select>
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
                        <div class="flex items-center justify-end gap-3 pt-4">
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
import { computed, watch } from 'vue';
import { useTranslation } from '@/composables/useTranslation';
import { useModal } from '@/composables/useModal';

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

const emit = defineEmits(['close', 'submit']);

const { t } = useTranslation();
const { setModalOpen } = useModal();

const isEdit = computed(() => !!props.lesson);

// Update modal state when show prop changes
watch(() => props.show, (isOpen) => {
    setModalOpen(isOpen);
}, { immediate: true });

const submit = () => {
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

