<template>
    <AdminLayout :page-title="t('questions.view')">
        <div class="max-w-4xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <Link :href="route('admin.courses.lessons.questions.index', [course.slug || course.id, lesson.id])" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </Link>
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900">{{ t('questions.view') || 'View Question' }}</h1>
                        <p class="text-sm text-gray-500">{{ lesson.title }}</p>
                    </div>
                    <Link
                        :href="route('admin.courses.lessons.questions.edit', [course.slug || course.id, lesson.id, question.id])"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 text-gray-700 font-medium transition-all"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {{ t('common.edit') }}
                    </Link>
                </div>
            </div>

            <!-- Question Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="p-6">
                    <!-- Question Meta -->
                    <div class="flex items-center gap-3 mb-6">
                        <span :class="getQuestionTypeBadge(question.type)" class="px-3 py-1.5 rounded-full text-sm font-medium">
                            {{ formatQuestionType(question.type) }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 rounded-full text-sm font-medium text-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            {{ question.points }} {{ t('questions.points') || 'points' }}
                        </span>
                        <span class="text-sm text-gray-500">{{ t('questions.fields.order') || 'Order' }}: {{ question.order }}</span>
                    </div>
                    
                    <!-- Question Text -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ t('questions.fields.question') || 'Question' }}</h3>
                        <p class="text-gray-700 text-lg">{{ question.question }}</p>
                        <p v-if="question.question_ar" class="text-gray-600 mt-2" dir="rtl">{{ question.question_ar }}</p>
                    </div>
                    
                    <!-- Explanation -->
                    <div v-if="question.explanation" class="p-4 bg-blue-50 rounded-xl">
                        <h4 class="text-sm font-medium text-blue-800 mb-1">{{ t('questions.fields.explanation') || 'Explanation' }}</h4>
                        <p class="text-blue-700">{{ question.explanation }}</p>
                        <p v-if="question.explanation_ar" class="text-blue-600 mt-2" dir="rtl">{{ question.explanation_ar }}</p>
                    </div>
                </div>
            </div>

            <!-- Answers Card -->
            <div v-if="question.answers && question.answers.length > 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">{{ t('questions.answers') || 'Answers' }}</h3>
                </div>
                
                <div class="divide-y divide-gray-100">
                    <div
                        v-for="(answer, index) in question.answers"
                        :key="answer.id"
                        class="p-4 flex items-start gap-4"
                        :class="{ 'bg-emerald-50/50': answer.is_correct }"
                    >
                        <div
                            class="w-8 h-8 rounded-lg flex items-center justify-center font-medium text-sm flex-shrink-0"
                            :class="answer.is_correct ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'"
                        >
                            {{ String.fromCharCode(65 + index) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-900" :class="{ 'font-medium': answer.is_correct }">{{ answer.answer }}</p>
                            <p v-if="answer.answer_ar" class="text-gray-600 text-sm mt-1" dir="rtl">{{ answer.answer_ar }}</p>
                        </div>
                        <div v-if="answer.is_correct" class="flex items-center gap-1 text-emerald-600 text-sm font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ t('questions.correct') || 'Correct' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { Link } from '@inertiajs/vue3';

defineProps({
    course: Object,
    lesson: Object,
    question: Object,
});

const { t } = useTranslation();
const { route } = useRoute();

const getQuestionTypeBadge = (type) => {
    const badges = {
        multiple_choice: 'bg-blue-100 text-blue-700',
        true_false: 'bg-emerald-100 text-emerald-700',
        short_answer: 'bg-amber-100 text-amber-700',
        essay: 'bg-purple-100 text-purple-700',
    };
    return badges[type] || 'bg-gray-100 text-gray-700';
};

const formatQuestionType = (type) => {
    const types = {
        multiple_choice: t('questions.types.multiple_choice') || 'Multiple Choice',
        true_false: t('questions.types.true_false') || 'True/False',
        short_answer: t('questions.types.short_answer') || 'Short Answer',
        essay: t('questions.types.essay') || 'Essay',
    };
    return types[type] || type;
};
</script>

