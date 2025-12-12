<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="$emit('close')"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl p-6 transform transition-all max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ isEdit ? (t('admin.edit_question') || 'Edit Question') : (t('admin.add_question') || 'Add Question') }}
                        </h3>
                        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="bi bi-x-lg text-2xl"></i>
                        </button>
                    </div>
                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('questions.fields.type') || 'Question Type' }} <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="formData.type"
                                @change="handleTypeChange"
                                class="form-select"
                                :class="{ 'border-red-500': errors.type }"
                            >
                                <option value="">{{ t('common.select') || 'Select' }}</option>
                                <option v-for="type in questionTypes" :key="type.value" :value="type.value">
                                    {{ t(`lessons.types.${type.value}`) || type.label }}
                                </option>
                            </select>
                            <p v-if="errors.type" class="form-error">{{ errors.type }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('questions.fields.question') || 'Question' }} (English) <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                v-model="formData.question"
                                rows="3"
                                class="form-textarea"
                                :class="{ 'border-red-500': errors.question }"
                                :placeholder="t('questions.placeholders.question') || 'Enter question'"
                            ></textarea>
                            <p v-if="errors.question" class="form-error">{{ errors.question }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('questions.fields.question') || 'Question' }} (العربية)
                            </label>
                            <textarea
                                v-model="formData.question_ar"
                                rows="3"
                                dir="rtl"
                                class="form-textarea"
                                :placeholder="'أدخل السؤال'"
                            ></textarea>
                        </div>
                        <div v-if="['multiple_choice', 'true_false'].includes(formData.type)">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                {{ t('questions.fields.answers') || 'Answers' }} <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500 font-normal ml-2">
                                    ({{ t('questions.at_least_one_correct') || 'At least one answer must be marked as correct' }})
                                </span>
                            </label>
                            <div class="space-y-3">
                                <div v-for="(answer, index) in (formData.answers || [])" :key="index" class="flex items-start gap-3 p-3 border border-gray-200 rounded-xl">
                                    <input
                                        v-model="answer.is_correct"
                                        type="checkbox"
                                        class="mt-2 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    />
                                    <div class="flex-1">
                                        <input
                                            v-model="answer.answer"
                                            type="text"
                                            class="form-input mb-2"
                                            :placeholder="'Answer (English)'"
                                            :required="formData.type === 'multiple_choice' || formData.type === 'true_false'"
                                        />
                                        <input
                                            v-model="answer.answer_ar"
                                            type="text"
                                            dir="rtl"
                                            class="form-input"
                                            :placeholder="'الإجابة (العربية)'"
                                        />
                                    </div>
                                    <button
                                        v-if="formData.answers && formData.answers.length > 2 && formData.type === 'multiple_choice'"
                                        type="button"
                                        @click="removeAnswer(index)"
                                        class="text-red-500 hover:text-red-700 transition-colors p-1"
                                        :title="t('common.delete') || 'Delete'"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <button
                                    v-if="formData.type === 'multiple_choice'"
                                    type="button"
                                    @click="addAnswer"
                                    class="w-full px-4 py-2 border-2 border-dashed border-gray-300 rounded-xl text-gray-600 hover:border-blue-500 hover:text-blue-600 transition-all"
                                >
                                    <i class="bi bi-plus-circle me-2"></i>
                                    {{ t('questions.add_answer') || 'Add Answer' }}
                                </button>
                            </div>
                            <p v-if="errors.answers" class="form-error mt-2">{{ errors.answers }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('questions.fields.points') || 'Points' }}
                            </label>
                            <input
                                v-model.number="formData.points"
                                type="number"
                                min="1"
                                class="form-input"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('questions.fields.explanation') || 'Explanation' }} (English)
                            </label>
                            <textarea
                                v-model="formData.explanation"
                                rows="2"
                                class="form-textarea"
                                :placeholder="t('questions.placeholders.explanation') || 'Enter explanation (optional)'"
                            ></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('questions.fields.explanation') || 'Explanation' }} (العربية)
                            </label>
                            <textarea
                                v-model="formData.explanation_ar"
                                rows="2"
                                dir="rtl"
                                class="form-textarea"
                                :placeholder="'أدخل الشرح'"
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
    question: {
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
    questionTypes: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close', 'submit', 'type-change']);

const { t } = useTranslation();
const { setModalOpen } = useModal();

const isEdit = computed(() => !!props.question);

// Update modal state when show prop changes
watch(() => props.show, (isOpen) => {
    setModalOpen(isOpen);
}, { immediate: true });

const handleTypeChange = () => {
    emit('type-change', props.formData.type);
};

const addAnswer = () => {
    if (!props.formData.answers) {
        props.formData.answers = [];
    }
    props.formData.answers.push({
        answer: '',
        answer_ar: '',
        is_correct: false,
        order: props.formData.answers.length,
    });
};

const removeAnswer = (index) => {
    if (props.formData.answers && props.formData.answers.length > 2) {
        props.formData.answers.splice(index, 1);
        props.formData.answers.forEach((answer, idx) => {
            if (answer) {
                answer.order = idx;
            }
        });
    }
};

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

