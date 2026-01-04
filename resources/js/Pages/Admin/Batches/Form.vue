<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="$emit('close')"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-6 transform transition-all max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ isEdit ? (t('admin.edit_batch') || 'Edit Batch') : (t('admin.create_batch') || 'Create Batch') }}
                        </h3>
                        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="bi bi-x-lg text-2xl"></i>
                        </button>
                    </div>
                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('admin.batch_name') || 'Batch Name' }} (English) <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="formData.name"
                                type="text"
                                class="form-input"
                                :class="{ 'border-red-500': errors.name }"
                                :placeholder="t('admin.batch_name_placeholder') || 'Enter batch name'"
                            />
                            <p v-if="errors.name" class="form-error">{{ errors.name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('admin.batch_name') || 'Batch Name' }} (العربية)
                            </label>
                            <input
                                v-model="formData.name_ar"
                                type="text"
                                dir="rtl"
                                class="form-input"
                                :placeholder="'أدخل اسم الشعبة'"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('admin.description') || 'Description' }} (English)
                            </label>
                            <textarea
                                v-model="formData.description"
                                rows="3"
                                class="form-textarea"
                                :placeholder="t('admin.batch_description_placeholder') || 'Enter batch description'"
                            ></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('admin.description') || 'Description' }} (العربية)
                            </label>
                            <textarea
                                v-model="formData.description_ar"
                                rows="3"
                                dir="rtl"
                                class="form-textarea"
                                :placeholder="'أدخل وصف الشعبة'"
                            ></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('admin.instructor') || 'Instructor' }} <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="formData.instructor_id"
                                class="form-select"
                                :class="{ 'border-red-500': errors.instructor_id }"
                            >
                                <option :value="null">{{ t('admin.select_instructor') || 'Select an instructor' }}</option>
                                <option v-for="instructor in instructors" :key="instructor.id" :value="instructor.id">
                                    {{ instructor.name }} ({{ instructor.email }})
                                </option>
                            </select>
                            <p v-if="errors.instructor_id" class="form-error">{{ errors.instructor_id }}</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ t('common.start_date') || 'Start Date' }}
                                </label>
                                <input
                                    v-model="formData.start_date"
                                    type="date"
                                    class="form-input"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ t('common.end_date') || 'End Date' }}
                                </label>
                                <input
                                    v-model="formData.end_date"
                                    type="date"
                                    :min="formData.start_date"
                                    class="form-input"
                                />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('admin.max_students') || 'Maximum Students' }}
                            </label>
                            <input
                                v-model.number="formData.max_students"
                                type="number"
                                min="1"
                                class="form-input"
                                :placeholder="t('admin.max_students_placeholder') || 'Enter maximum number of students'"
                            />
                            <small class="text-gray-500 mt-1 block">{{ t('admin.max_students_hint') || 'Leave empty for unlimited' }}</small>
                        </div>
                        <div class="flex items-center gap-3">
                            <input
                                v-model="formData.is_active"
                                type="checkbox"
                                id="is_active"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            />
                            <label for="is_active" class="text-sm font-semibold text-gray-700 cursor-pointer">
                                {{ t('admin.active_batch') || 'Active Batch' }}
                            </label>
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
    batch: {
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
    instructors: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close', 'submit']);

const { t } = useTranslation();
const { setModalOpen } = useModal();

const isEdit = computed(() => !!props.batch);

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

