<template>
    <AdminLayout :page-title="t('admin.create_question')">
        <div class="max-w-4xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-2">
                    <Link :href="route('admin.courses.lessons.questions.index', [course.slug || course.id, lesson.id])" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </Link>
                    <h1 class="text-2xl font-bold text-gray-900">{{ t('admin.create_question') || 'Create Question' }}</h1>
                </div>
                <p class="text-gray-500">{{ lesson.title }}</p>
            </div>

            <!-- Form Card -->
            <form @submit.prevent="submit" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 space-y-8">
                    <!-- Question Info -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            {{ t('questions.info') || 'Question Information' }}
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <!-- Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('questions.fields.type') || 'Type' }} <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.type"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white"
                                    :class="{ 'border-red-500': form.errors.type }"
                                >
                                    <option value="">{{ t('common.select') }}</option>
                                    <option v-for="type in questionTypes" :key="type.value" :value="type.value">
                                        {{ type.label }}
                                    </option>
                                </select>
                                <p v-if="form.errors.type" class="mt-1 text-sm text-red-500">{{ form.errors.type }}</p>
                            </div>
                            
                            <!-- Points -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('questions.fields.points') || 'Points' }} <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.points"
                                    type="number"
                                    min="1"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    :class="{ 'border-red-500': form.errors.points }"
                                />
                                <p v-if="form.errors.points" class="mt-1 text-sm text-red-500">{{ form.errors.points }}</p>
                            </div>
                            
                            <!-- Order -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('questions.fields.order') || 'Order' }} <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.order"
                                    type="number"
                                    min="0"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    :class="{ 'border-red-500': form.errors.order }"
                                />
                                <p v-if="form.errors.order" class="mt-1 text-sm text-red-500">{{ form.errors.order }}</p>
                            </div>
                        </div>
                        
                        <!-- Question Text -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('questions.fields.question') || 'Question' }} (English) <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    v-model="form.question"
                                    rows="4"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                    :class="{ 'border-red-500': form.errors.question }"
                                    :placeholder="t('questions.placeholders.question') || 'Enter question text'"
                                ></textarea>
                                <p v-if="form.errors.question" class="mt-1 text-sm text-red-500">{{ form.errors.question }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('questions.fields.question') || 'Question' }} (العربية)
                                </label>
                                <textarea
                                    v-model="form.question_ar"
                                    rows="4"
                                    dir="rtl"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                    :placeholder="'أدخل نص السؤال'"
                                ></textarea>
                            </div>
                        </div>
                        
                        <!-- Explanation -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('questions.fields.explanation') || 'Explanation' }} (English)
                                </label>
                                <textarea
                                    v-model="form.explanation"
                                    rows="3"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                    :placeholder="t('questions.placeholders.explanation') || 'Explanation shown after answering'"
                                ></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('questions.fields.explanation') || 'Explanation' }} (العربية)
                                </label>
                                <textarea
                                    v-model="form.explanation_ar"
                                    rows="3"
                                    dir="rtl"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                    :placeholder="'شرح يظهر بعد الإجابة'"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Answers Section (for multiple choice / true-false) -->
                    <div v-if="['multiple_choice', 'true_false'].includes(form.type)" class="pt-6 border-t border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                {{ t('questions.answers') || 'Answers' }}
                            </h3>
                            <button
                                v-if="form.type === 'multiple_choice'"
                                type="button"
                                @click="addAnswer"
                                class="inline-flex items-center gap-2 px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-xl font-medium transition-all"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                {{ t('questions.add_answer') || 'Add Answer' }}
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div
                                v-for="(answer, index) in form.answers"
                                :key="index"
                                class="p-4 bg-gray-50 rounded-xl"
                            >
                                <div class="flex items-start gap-4">
                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <input
                                                v-model="answer.answer"
                                                type="text"
                                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                                :placeholder="t('questions.placeholders.answer') || 'Answer (English)'"
                                            />
                                        </div>
                                        <div>
                                            <input
                                                v-model="answer.answer_ar"
                                                type="text"
                                                dir="rtl"
                                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                                :placeholder="'الإجابة (العربية)'"
                                            />
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                v-model="answer.is_correct"
                                                class="w-5 h-5 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500"
                                            />
                                            <span class="text-sm text-gray-600">{{ t('questions.correct') || 'Correct' }}</span>
                                        </label>
                                        <button
                                            v-if="form.type === 'multiple_choice' && form.answers.length > 2"
                                            type="button"
                                            @click="removeAnswer(index)"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-8 py-6 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-4">
                    <Link
                        :href="route('admin.courses.lessons.questions.index', [course.slug || course.id, lesson.id])"
                        class="px-6 py-3 text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 font-medium transition-all"
                    >
                        {{ t('common.cancel') }}
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 font-medium transition-all shadow-lg shadow-blue-500/25 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <svg v-if="form.processing" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ t('common.create') }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    lesson: Object,
    questionTypes: Array,
});

const { t } = useTranslation();
const { route } = useRoute();

const form = useForm({
    type: '',
    question: '',
    question_ar: '',
    explanation: '',
    explanation_ar: '',
    points: 1,
    order: 1,
    answers: [
        { answer: '', answer_ar: '', is_correct: false, order: 0 },
        { answer: '', answer_ar: '', is_correct: false, order: 1 },
    ],
});

// Initialize True/False answers when type changes
watch(() => form.type, (newType) => {
    if (newType === 'true_false') {
        form.answers = [
            { answer: 'True', answer_ar: 'صحيح', is_correct: false, order: 0 },
            { answer: 'False', answer_ar: 'خطأ', is_correct: false, order: 1 },
        ];
    } else if (newType === 'multiple_choice' && form.answers.length === 2) {
        form.answers = [
            { answer: '', answer_ar: '', is_correct: false, order: 0 },
            { answer: '', answer_ar: '', is_correct: false, order: 1 },
        ];
    }
});

const addAnswer = () => {
    form.answers.push({
        answer: '',
        answer_ar: '',
        is_correct: false,
        order: form.answers.length,
    });
};

const removeAnswer = (index) => {
    if (form.answers.length > 2) {
        form.answers.splice(index, 1);
        // Update order
        form.answers.forEach((a, i) => a.order = i);
    }
};

const submit = () => {
    form.post(route('admin.courses.lessons.questions.store', [props.course.slug || props.course.id, props.lesson.id]));
};
</script>

