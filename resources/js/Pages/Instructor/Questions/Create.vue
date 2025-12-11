<template>
    <AuthenticatedLayout>
        <Head :title="t('lessons.actions.add_question') || 'Add Question'" />
        <div class="min-h-screen bg-gray-50" :dir="direction">
            <!-- Hero Section -->
            <div class="relative bg-gradient-to-r from-slate-700 to-slate-800 py-12 overflow-hidden">
                <div class="absolute inset-0 opacity-10 bg-pattern"></div>
                <div class="max-w-7xl mx-auto px-4 relative z-10">
                    <div class="bg-white rounded-2xl shadow-xl p-6 max-w-4xl">
                        <nav class="text-sm text-gray-500 mb-4">
                            <Link :href="route('courses.show', course.slug || course.id)" class="hover:text-blue-600 transition-colors">
                                {{ t('courses.title') }}
                            </Link>
                            <span class="mx-2">›</span>
                            <Link :href="route('instructor.lessons.show', [course.slug || course.id, lesson.id])" class="hover:text-blue-600 transition-colors">
                                {{ lesson.translated_title || lesson.title }}
                            </Link>
                            <span class="mx-2">›</span>
                            <span class="text-gray-900 font-medium">{{ t('lessons.actions.add_question') }}</span>
                        </nav>
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center gap-3">
                                    <span class="w-1 h-8 bg-blue-600 rounded-full"></span>
                                    {{ t('lessons.actions.add_question') }}
                                </h1>
                                <p class="text-gray-600 mt-1">{{ t('lessons.lesson') }}: {{ lesson.translated_title || lesson.title }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-4xl mx-auto py-8 px-4">
                <form @submit.prevent="submit" class="bg-white shadow-lg rounded-2xl p-8">
                        <div class="space-y-8">
                            <!-- Question Type -->
                            <div>
                                <label for="type" class="block text-sm font-semibold text-gray-900 mb-3">
                                    {{ t('lessons.fields.question_type') }} <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="type"
                                    v-model="form.type"
                                    required
                                    @change="handleTypeChange"
                                    class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-gray-900 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:border-gray-400"
                                >
                                    <option value="">{{ t('common.select') }}</option>
                                    <option value="multiple_choice">{{ t('lessons.types.multiple_choice') }}</option>
                                    <option value="true_false">{{ t('lessons.types.true_false') }}</option>
                                    <option value="short_answer">{{ t('lessons.types.short_answer') }}</option>
                                    <option value="essay">{{ t('lessons.types.essay') }}</option>
                                </select>
                                <div v-if="errors.type" class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ errors.type }}
                                </div>
                            </div>

                            <!-- Question Text -->
                            <div>
                                <label for="question" class="block text-sm font-semibold text-gray-900 mb-3">
                                    {{ t('lessons.question') }} <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="question"
                                    v-model="form.question"
                                    rows="4"
                                    required
                                    class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-gray-900 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:border-gray-400 resize-none"
                                    :placeholder="t('lessons.question')"
                                ></textarea>
                                <div v-if="errors.question" class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ errors.question }}
                                </div>
                            </div>

                            <!-- Question Arabic -->
                            <div>
                                <label for="question_ar" class="block text-sm font-semibold text-gray-900 mb-3">
                                    {{ t('lessons.fields.title_ar') }}
                                </label>
                                <textarea
                                    id="question_ar"
                                    v-model="form.question_ar"
                                    rows="4"
                                    class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-gray-900 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:border-gray-400 resize-none"
                                    :placeholder="t('lessons.fields.title_ar')"
                                ></textarea>
                            </div>

                            <!-- Explanation -->
                            <div>
                                <label for="explanation" class="block text-sm font-semibold text-gray-900 mb-3">
                                    {{ t('lessons.explanation') }}
                                </label>
                                <textarea
                                    id="explanation"
                                    v-model="form.explanation"
                                    rows="3"
                                    class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-gray-900 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:border-gray-400 resize-none"
                                    :placeholder="t('lessons.explanation')"
                                ></textarea>
                            </div>

                            <!-- Explanation Arabic -->
                            <div>
                                <label for="explanation_ar" class="block text-sm font-semibold text-gray-900 mb-3">
                                    {{ t('lessons.explanation') }} ({{ t('lessons.fields.title_ar') }})
                                </label>
                                <textarea
                                    id="explanation_ar"
                                    v-model="form.explanation_ar"
                                    rows="3"
                                    class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-gray-900 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:border-gray-400 resize-none"
                                    :placeholder="t('lessons.explanation')"
                                ></textarea>
                            </div>

                            <!-- Points and Order -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="points" class="block text-sm font-semibold text-gray-900 mb-3">
                                        {{ t('lessons.points') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="points"
                                        v-model.number="form.points"
                                        type="number"
                                        min="1"
                                        required
                                        class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-gray-900 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:border-gray-400"
                                    />
                                    <div v-if="errors.points" class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ errors.points }}
                                    </div>
                                </div>
                                <div>
                                    <label for="order" class="block text-sm font-semibold text-gray-900 mb-3">
                                        {{ t('lessons.fields.order') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="order"
                                        v-model.number="form.order"
                                        type="number"
                                        min="0"
                                        required
                                        class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-gray-900 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:border-gray-400"
                                    />
                                    <div v-if="errors.order" class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ errors.order }}
                                    </div>
                                </div>
                            </div>

                            <!-- Answers Section (for multiple choice and true/false) -->
                            <div v-if="form.type === 'multiple_choice' || form.type === 'true_false'" class="border-t-2 border-gray-200 pt-8">
                                <div class="flex items-center justify-between mb-6">
                                    <label class="block text-sm font-semibold text-gray-900">
                                        {{ t('lessons.questions') }} <span class="text-red-500">*</span>
                                    </label>
                                    <button
                                        type="button"
                                        @click="addAnswer"
                                        class="px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 text-sm font-semibold transition-all shadow-sm hover:shadow-md flex items-center gap-2"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        {{ t('lessons.actions.add_question') }}
                                    </button>
                                </div>
                                <div v-if="errors.answers" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ errors.answers }}
                                </div>
                                <div class="space-y-4">
                                    <div
                                        v-for="(answer, index) in form.answers"
                                        :key="index"
                                        class="border-2 border-gray-200 rounded-xl p-5 bg-gray-50 hover:bg-gray-100 transition-all"
                                        :class="answer.is_correct ? 'border-blue-500 bg-blue-50' : ''"
                                    >
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex items-center gap-3">
                                                <span class="px-3 py-1 bg-white border-2 border-gray-300 rounded-full text-sm font-semibold text-gray-700">
                                                    {{ t('lessons.question') }} {{ index + 1 }}
                                                </span>
                                                <span v-if="answer.is_correct" class="px-3 py-1 bg-blue-600 text-white rounded-full text-xs font-semibold flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ t('lessons.correct') }}
                                                </span>
                                            </div>
                                            <button
                                                type="button"
                                                @click="removeAnswer(index)"
                                                class="text-red-600 hover:text-red-700 text-sm font-medium px-2 py-1 hover:bg-red-50 rounded-lg transition-colors flex items-center gap-1"
                                                v-if="form.answers.length > 2"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                {{ t('common.delete') }}
                                            </button>
                                        </div>
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">{{ t('lessons.question') }}</label>
                                                <input
                                                    v-model="answer.answer"
                                                    type="text"
                                                    required
                                                    :placeholder="t('lessons.question')"
                                                    class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-gray-900 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:border-gray-400"
                                                />
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">{{ t('lessons.fields.title_ar') }}</label>
                                                <input
                                                    v-model="answer.answer_ar"
                                                    type="text"
                                                    :placeholder="t('lessons.fields.title_ar')"
                                                    class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-gray-900 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:border-gray-400"
                                                />
                                            </div>
                                            <div class="flex items-center gap-6 pt-2 border-t border-gray-300">
                                                <label class="flex items-center cursor-pointer group">
                                                    <input
                                                        v-model="answer.is_correct"
                                                        type="checkbox"
                                                        class="w-5 h-5 rounded border-2 border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 cursor-pointer"
                                                    />
                                                    <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-gray-900">{{ t('lessons.correct') }}</span>
                                                </label>
                                                <div class="flex-1 max-w-xs">
                                                    <label class="block text-xs font-semibold text-gray-700 mb-2">{{ t('lessons.fields.order') }}</label>
                                                    <input
                                                        v-model.number="answer.order"
                                                        type="number"
                                                        min="0"
                                                        required
                                                        class="w-full border-2 border-gray-300 rounded-xl px-4 py-2 text-sm text-gray-900 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:border-gray-400"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-8 border-t-2 border-gray-200">
                                <Link
                                    :href="route('instructor.lessons.show', [course.slug || course.id, lesson.id])"
                                    class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 hover:border-gray-400 font-semibold transition-all text-center"
                                >
                                    {{ t('common.cancel') }}
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-8 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-md hover:shadow-lg font-semibold flex items-center justify-center gap-2"
                                >
                                    <svg v-if="form.processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    {{ form.processing ? t('common.saving') : t('common.create') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Back Navigation -->
                    <div class="mt-6">
                        <Link
                            :href="route('instructor.lessons.show', [course.slug || course.id, lesson.id])"
                            class="inline-flex items-center gap-2 px-4 py-2 text-gray-700 hover:text-blue-600 transition-colors font-medium"
                        >
                            <svg class="w-5 h-5" :class="direction === 'rtl' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            {{ t('lessons.actions.back') }}
                        </Link>
                    </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useDirection } from '@/composables/useDirection';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useAlert } from '@/composables/useAlert';
import { usePermissions } from '@/composables/usePermissions';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    lesson: Object,
    errors: Object,
});

const { direction } = useDirection();
const { t } = useTranslation();
const { route } = useRoute();
const { showSuccess, showError } = useAlert();
const { can } = usePermissions();
const page = usePage();

// Get question type from route data if passed from modal
const questionType = page.props.type || null;

const form = useForm({
    type: questionType || '',
    question: '',
    question_ar: '',
    explanation: '',
    explanation_ar: '',
    points: 1,
    order: 0,
    answers: [
        { answer: '', answer_ar: '', is_correct: false, order: 0 },
        { answer: '', answer_ar: '', is_correct: false, order: 1 },
    ],
});

const handleTypeChange = () => {
    if (form.type === 'true_false') {
        form.answers = [
            { answer: 'True', answer_ar: 'صحيح', is_correct: false, order: 0 },
            { answer: 'False', answer_ar: 'خطأ', is_correct: false, order: 1 },
        ];
    } else if (form.type === 'multiple_choice' && form.answers.length < 2) {
        form.answers = [
            { answer: '', answer_ar: '', is_correct: false, order: 0 },
            { answer: '', answer_ar: '', is_correct: false, order: 1 },
        ];
    }
};

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
        form.answers.forEach((answer, idx) => {
            answer.order = idx;
        });
    }
};

const submit = () => {
    form.post(route('instructor.lessons.questions.store', {
        course: props.course.slug || props.course.id,
        lesson: props.lesson.id,
    }), {
        preserveScroll: true,
        onSuccess: () => {
            showSuccess(
                t('lessons.created_successfully') || 'Question created successfully!',
                t('common.success') || 'Success'
            );
            // Redirect after a short delay to show the alert
            setTimeout(() => {
                router.visit(route('instructor.lessons.show', [
                    props.course.slug || props.course.id,
                    props.lesson.id
                ]));
            }, 1500);
        },
        onError: (errors) => {
            if (errors.message) {
                showError(errors.message, t('common.error') || 'Error');
            }
        },
    });
};

// Initialize true/false answers if type is set
if (questionType === 'true_false') {
    handleTypeChange();
}
</script>

