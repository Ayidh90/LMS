<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="$emit('close')"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-6 transform transition-all max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ isEdit ? (t('admin.edit_section') || 'Edit Section') : (t('admin.add_section') || 'Add Section') }}
                        </h3>
                        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="bi bi-x-lg text-2xl"></i>
                        </button>
                    </div>
                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('sections.fields.title') || 'Title' }} (English) <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="formData.title"
                                type="text"
                                class="form-input"
                                :class="{ 'border-red-500': errors.title }"
                                :placeholder="t('sections.placeholders.title') || 'Enter section title'"
                            />
                            <p v-if="errors.title" class="form-error">{{ errors.title }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('sections.fields.title') || 'Title' }} (العربية)
                            </label>
                            <input
                                v-model="formData.title_ar"
                                type="text"
                                dir="rtl"
                                class="form-input"
                                :class="{ 'border-red-500': errors.title_ar }"
                                :placeholder="'أدخل عنوان القسم'"
                            />
                            <p v-if="errors.title_ar" class="form-error">{{ errors.title_ar }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('sections.fields.description') || 'Description' }} (English)
                            </label>
                            <textarea
                                v-model="formData.description"
                                rows="3"
                                class="form-textarea"
                                :class="{ 'border-red-500': errors.description }"
                                :placeholder="t('sections.placeholders.description') || 'Enter section description'"
                            ></textarea>
                            <p v-if="errors.description" class="form-error">{{ errors.description }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('sections.fields.description') || 'Description' }} (العربية)
                            </label>
                            <textarea
                                v-model="formData.description_ar"
                                rows="3"
                                dir="rtl"
                                class="form-textarea"
                                :class="{ 'border-red-500': errors.description_ar }"
                                :placeholder="'أدخل وصف القسم'"
                            ></textarea>
                            <p v-if="errors.description_ar" class="form-error">{{ errors.description_ar }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('sections.fields.order') || 'Order' }}
                            </label>
                            <input
                                v-model.number="formData.order"
                                type="number"
                                min="0"
                                class="form-input"
                                :class="{ 'border-red-500': errors.order }"
                                :placeholder="t('sections.placeholders.order') || 'Enter section order (optional)'"
                            />
                            <p v-if="errors.order" class="form-error">{{ errors.order }}</p>
                            <small v-else class="text-gray-500 mt-1 block">{{ t('sections.order_hint') || 'Leave empty to add at the end' }}</small>
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
    section: {
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
});

const emit = defineEmits(['close', 'submit']);

const { t } = useTranslation();
const { setModalOpen } = useModal();

const isEdit = computed(() => !!props.section);

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
.form-textarea {
    @apply w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all;
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

