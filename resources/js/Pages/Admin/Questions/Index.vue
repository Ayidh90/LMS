<template>
    <AdminLayout :page-title="t('admin.questions_management') || 'Questions Management'">
        <Head :title="t('admin.questions_management') || 'Questions Management'" />
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link :href="route('admin.courses.lessons.index', course.slug || course.id)" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ t('admin.questions_management') || 'Questions' }}</h1>
                        <p class="text-sm text-gray-500">{{ lesson.title }} • {{ course.title }}</p>
                    </div>
                </div>
                <button
                    @click="openQuestionModal(null)"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 font-medium transition-all shadow-lg shadow-blue-500/25"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ t('admin.add_question') || 'Add Question' }}
                </button>
            </div>

            <!-- Questions List -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div v-if="questions.length > 0" class="divide-y divide-gray-100">
                    <div
                        v-for="(question, index) in questions"
                        :key="question.id"
                        class="p-6 hover:bg-gray-50/50 transition-colors"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-4 flex-1">
                                <!-- Order Badge -->
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0">
                                    {{ index + 1 }}
                                </div>
                                
                                <!-- Question Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span :class="getQuestionTypeBadge(question.type)" class="px-2.5 py-1 rounded-full text-xs font-medium">
                                            {{ formatQuestionType(question.type) }}
                                        </span>
                                        <span class="text-sm text-gray-500">{{ question.points }} {{ t('questions.points') || 'points' }}</span>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ question.question }}</h3>
                                    
                                    <!-- Answers Preview -->
                                    <div v-if="question.answers_count > 0" class="text-sm text-gray-500">
                                        {{ question.answers_count }} {{ t('questions.answers') || 'answers' }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                <Link
                                    :href="route('admin.courses.lessons.questions.show', [course.slug || course.id, lesson.id, question.id])"
                                    class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                    :title="t('common.view')"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </Link>
                                <button
                                    @click="openQuestionModal(question)"
                                    class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                    :title="t('common.edit')"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button
                                    @click="confirmDelete(question)"
                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    :title="t('common.delete')"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Empty State -->
                <div v-else class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ t('questions.no_questions') || 'No questions yet' }}</h3>
                    <p class="text-gray-500 mb-6">{{ t('questions.no_questions_hint') || 'Add questions to create a test or quiz' }}</p>
                    <button
                        @click="openQuestionModal(null)"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium transition-all"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        {{ t('admin.add_question') || 'Add Question' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Question Form Modal -->
        <QuestionForm
            :show="showQuestionModal"
            :question="editingQuestion"
            :form-data="questionForm"
            :errors="questionForm.errors"
            :processing="questionForm.processing"
            :question-types="questionTypes"
            @close="closeQuestionModal"
            @submit="submitQuestion"
            @type-change="handleQuestionTypeChange"
        />
    </AdminLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useAlert } from '@/composables/useAlert';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import QuestionForm from '@/Pages/Admin/Questions/Form.vue';

const props = defineProps({
    course: Object,
    lesson: Object,
    questions: Array,
    questionTypes: Array,
});

const { t } = useTranslation();
const { route } = useRoute();
const { showSuccess, showError } = useAlert();

// Modal state
const showQuestionModal = ref(false);
const editingQuestion = ref(null);

// Question form
const questionForm = useForm({
    type: '',
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

// Watch for type changes to initialize answers
watch(() => questionForm.type, (newType) => {
    if (newType === 'true_false') {
        questionForm.answers = [
            { answer: 'True', answer_ar: 'صحيح', is_correct: false, order: 0 },
            { answer: 'False', answer_ar: 'خطأ', is_correct: false, order: 1 },
        ];
    } else if (newType === 'multiple_choice' && questionForm.answers.length < 2) {
        questionForm.answers = [
            { answer: '', answer_ar: '', is_correct: false, order: 0 },
            { answer: '', answer_ar: '', is_correct: false, order: 1 },
        ];
    }
});

const openQuestionModal = (question = null) => {
    editingQuestion.value = question;
    questionForm.reset();
    
    if (question) {
        // Load question data - need to fetch full question with answers
        router.get(route('admin.courses.lessons.questions.show', [
            props.course.slug || props.course.id,
            props.lesson.id,
            question.id
        ]), {}, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                const fullQuestion = page.props.question;
                questionForm.type = fullQuestion.type || '';
                questionForm.question = fullQuestion.question || '';
                questionForm.question_ar = fullQuestion.question_ar || '';
                questionForm.explanation = fullQuestion.explanation || '';
                questionForm.explanation_ar = fullQuestion.explanation_ar || '';
                questionForm.points = fullQuestion.points || 1;
                questionForm.order = fullQuestion.order || 0;
                
                if (fullQuestion.answers && fullQuestion.answers.length > 0) {
                    questionForm.answers = fullQuestion.answers.map((answer, index) => ({
                        id: answer.id || null,
                        answer: answer.answer || '',
                        answer_ar: answer.answer_ar || '',
                        is_correct: answer.is_correct || false,
                        order: answer.order !== undefined ? answer.order : index,
                    }));
                } else {
                    if (fullQuestion.type === 'true_false') {
                        questionForm.answers = [
                            { answer: 'True', answer_ar: 'صحيح', is_correct: false, order: 0 },
                            { answer: 'False', answer_ar: 'خطأ', is_correct: false, order: 1 },
                        ];
                    } else {
                        questionForm.answers = [
                            { answer: '', answer_ar: '', is_correct: false, order: 0 },
                            { answer: '', answer_ar: '', is_correct: false, order: 1 },
                        ];
                    }
                }
                showQuestionModal.value = true;
            },
        });
    } else {
        questionForm.answers = [
            { answer: '', answer_ar: '', is_correct: false, order: 0 },
            { answer: '', answer_ar: '', is_correct: false, order: 1 },
        ];
        showQuestionModal.value = true;
    }
};

const closeQuestionModal = () => {
    showQuestionModal.value = false;
    editingQuestion.value = null;
    questionForm.reset();
    questionForm.clearErrors();
};

const handleQuestionTypeChange = (type) => {
    if (type === 'true_false') {
        questionForm.answers = [
            { answer: 'True', answer_ar: 'صحيح', is_correct: false, order: 0 },
            { answer: 'False', answer_ar: 'خطأ', is_correct: false, order: 1 },
        ];
    } else if (type === 'multiple_choice' && questionForm.answers.length < 2) {
        questionForm.answers = [
            { answer: '', answer_ar: '', is_correct: false, order: 0 },
            { answer: '', answer_ar: '', is_correct: false, order: 1 },
        ];
    }
};

const submitQuestion = (formData) => {
    // Prepare form data - convert empty strings to null for nullable fields
    ['question_ar', 'explanation', 'explanation_ar'].forEach(field => {
        if (questionForm[field] === '') {
            questionForm[field] = null;
        }
    });
    
    // Ensure points and order are integers
    if (questionForm.points === '' || questionForm.points === null) {
        questionForm.points = 1;
    } else {
        questionForm.points = parseInt(questionForm.points) || 1;
    }
    
    if (questionForm.order === '' || questionForm.order === null) {
        questionForm.order = 0;
    } else {
        questionForm.order = parseInt(questionForm.order) || 0;
    }
    
    // Clean up answers - remove empty answers and ensure order is set
    if (questionForm.answers && Array.isArray(questionForm.answers)) {
        questionForm.answers = questionForm.answers
            .filter(answer => answer.answer && answer.answer.trim() !== '')
            .map((answer, index) => ({
                ...answer,
                order: index,
            }));
    }
    
    if (editingQuestion.value) {
        // Update existing question
        questionForm.put(route('admin.courses.lessons.questions.update', [
            props.course.slug || props.course.id,
            props.lesson.id,
            editingQuestion.value.id
        ]), {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess(t('questions.updated_successfully') || 'Question updated successfully!', t('common.success') || 'Success');
                closeQuestionModal();
            },
            onError: (errors) => {
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
                }
            },
        });
    } else {
        // Create new question
        questionForm.post(route('admin.courses.lessons.questions.store', [
            props.course.slug || props.course.id,
            props.lesson.id
        ]), {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess(t('questions.created_successfully') || 'Question created successfully!', t('common.success') || 'Success');
                closeQuestionModal();
            },
            onError: (errors) => {
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
                }
            },
        });
    }
};

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
    return t(`lessons.types.${type}`) || type;
};

const confirmDelete = (question) => {
    if (confirm(t('questions.confirm_delete') || 'Are you sure you want to delete this question?')) {
        router.delete(route('admin.courses.lessons.questions.destroy', [props.course.slug || props.course.id, props.lesson.id, question.id]));
    }
};
</script>

