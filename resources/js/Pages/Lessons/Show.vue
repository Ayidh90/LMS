<template>
    <component :is="layout">
        <div class="min-h-screen bg-slate-100" :dir="direction">
            <!-- Hero Section -->
            <div class="relative bg-gradient-to-r from-slate-700 to-slate-800 py-16 overflow-hidden">
                <div class="absolute inset-0 opacity-10 bg-pattern"></div>
                <div class="max-w-7xl mx-auto px-4 relative z-10">
                    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-3xl">
                        <nav class="text-sm text-gray-500 mb-4">
                            <Link :href="route('courses.show', course.slug || course.id)" class="hover:text-blue-600">
                                {{ course.title }}
                            </Link>
                            <span class="mx-2">›</span>
                            <span class="text-gray-900">{{ lesson.title }}</span>
                        </nav>
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ lesson.title }}</h1>
                                <p v-if="lesson.description" class="text-gray-600">{{ lesson.description }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 mt-4 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ lesson.duration_minutes || 0 }} {{ t('lessons.fields.duration_minutes') }}</span>
                            </div>
                            <div v-if="lesson.questions && lesson.questions.length > 0" class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ lesson.questions.length }} {{ t('lessons.questions') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-4xl mx-auto py-8 px-4">
                <!-- Video Section -->
                <div v-if="lesson.video_url" class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        {{ t('lessons.fields.video_url') }}
                    </h2>
                    <div class="aspect-video bg-gray-900 rounded-lg overflow-hidden">
                        <iframe
                            :src="getEmbedUrl(lesson.video_url)"
                            class="w-full h-full"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                        ></iframe>
                    </div>
                </div>

                <!-- Content Section -->
                <div v-if="lesson.content" class="bg-white rounded-xl shadow-sm p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-blue-600 rounded-full"></span>
                        {{ t('lessons.fields.content') }}
                    </h2>
                    <div class="prose prose-lg max-w-none text-gray-700" v-html="formatContent(lesson.content)"></div>
                </div>

                <!-- Questions Section -->
                <div v-if="lesson.questions && lesson.questions.length > 0" class="bg-white rounded-xl shadow-sm p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-blue-600 rounded-full"></span>
                        {{ t('lessons.questions') }} ({{ lesson.questions.length }})
                    </h2>

                    <div class="space-y-8">
                        <div
                            v-for="(question, index) in lesson.questions"
                            :key="question.id"
                            class="border-2 border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow"
                        >
                            <div class="mb-4">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                        {{ t('lessons.question') }} {{ index + 1 }}
                                    </span>
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs capitalize">
                                        {{ t('lessons.types.' + question.type) }}
                                    </span>
                                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs">
                                        {{ question.points }} {{ t('lessons.points') }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ question.question }}</h3>
                                <p v-if="question.explanation" class="text-sm text-gray-600 italic mb-4">
                                    {{ t('lessons.explanation') }}: {{ question.explanation }}
                                </p>
                            </div>

                            <!-- Student Answer Form (if not answered yet) -->
                            <div v-if="isStudent && !question.user_answer" class="mb-4">
                                <!-- Multiple Choice / True False Form -->
                                <form v-if="question.type === 'multiple_choice' || question.type === 'true_false'" 
                                    @submit.prevent="submitAnswer(question)"
                                    class="space-y-3"
                                >
                                    <div class="space-y-2">
                                        <label
                                            v-for="answer in question.answers"
                                            :key="answer.id"
                                            class="flex items-center gap-3 p-3 rounded-lg border-2 border-gray-200 bg-gray-50 hover:bg-gray-100 hover:border-gray-300 cursor-pointer transition-all"
                                            :class="{ 'border-blue-500 bg-blue-50': answerForms[question.id]?.answer_id === answer.id }"
                                        >
                                            <input
                                                type="radio"
                                                :name="`question_${question.id}`"
                                                :value="answer.id"
                                                v-model="answerForms[question.id].answer_id"
                                                class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer"
                                                required
                                            />
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">{{ answer.answer }}</p>
                                                <p v-if="answer.answer_ar" class="text-xs text-gray-500 mt-1">{{ answer.answer_ar }}</p>
                                            </div>
                                        </label>
                                    </div>
                                    <button
                                        type="submit"
                                        :disabled="submittingAnswers[question.id]"
                                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition-colors flex items-center justify-center gap-2"
                                    >
                                        <svg v-if="submittingAnswers[question.id]" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ submittingAnswers[question.id] ? (t('common.saving') || 'Submitting...') : (t('lessons.submit_answer') || 'Submit Answer') }}
                                    </button>
                                </form>

                                <!-- Short Answer / Essay Form -->
                                <form v-else-if="question.type === 'short_answer' || question.type === 'essay'"
                                    @submit.prevent="submitAnswer(question)"
                                    class="space-y-3"
                                >
                                    <textarea
                                        v-model="answerForms[question.id].answer_text"
                                        :rows="question.type === 'essay' ? 6 : 3"
                                        :placeholder="t('lessons.enter_your_answer')"
                                        class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-gray-900 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                        required
                                    ></textarea>
                                    <button
                                        type="submit"
                                        :disabled="submittingAnswers[question.id]"
                                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition-colors flex items-center justify-center gap-2"
                                    >
                                        <svg v-if="submittingAnswers[question.id]" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ submittingAnswers[question.id] ? (t('common.saving') || 'Submitting...') : (t('lessons.submit_answer') || 'Submit Answer') }}
                                    </button>
                                </form>
                            </div>

                            <!-- User Answer Display (if answered) -->
                            <div v-if="question.user_answer" class="mb-4 p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-900">{{ t('lessons.your_answer') }}</span>
                                    <span class="px-3 py-1 rounded text-xs font-medium"
                                        :class="question.user_answer.is_correct ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                    >
                                        {{ question.user_answer.is_correct ? t('lessons.correct') : t('lessons.incorrect') || 'Incorrect' }}
                                    </span>
                                </div>
                                <p v-if="question.user_answer.selected_answer" class="text-sm text-gray-700 mb-1">
                                    {{ question.user_answer.selected_answer.answer }}
                                </p>
                                <p v-if="question.user_answer.answer_text" class="text-sm text-gray-700 mb-1">
                                    {{ question.user_answer.answer_text }}
                                </p>
                                <p class="text-xs text-gray-500 mt-2">
                                    {{ question.user_answer.points_earned }} / {{ question.points }} {{ t('lessons.points') }} • 
                                    {{ formatDate(question.user_answer.submitted_at) }}
                                </p>
                            </div>

                            <!-- Answer Options Display (for multiple choice/true_false - show results after answering) -->
                            <div v-if="(question.type === 'multiple_choice' || question.type === 'true_false') && question.answers && question.answers.length > 0 && question.user_answer" class="space-y-2">
                                <div
                                    v-for="answer in question.answers"
                                    :key="answer.id"
                                    class="flex items-center gap-3 p-3 rounded-lg border-2 transition-all"
                                    :class="getAnswerClass(answer, question)"
                                >
                                    <div class="flex-shrink-0">
                                        <div class="w-5 h-5 border-2 rounded-full flex items-center justify-center"
                                            :class="getAnswerIndicatorClass(answer, question)">
                                            <svg v-if="shouldShowCheckmark(answer, question)" class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ answer.answer }}</p>
                                        <p v-if="answer.answer_ar" class="text-xs text-gray-500 mt-1">{{ answer.answer_ar }}</p>
                                    </div>
                                    <div v-if="answer.is_correct" class="flex-shrink-0">
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">
                                            {{ t('lessons.correct') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Short Answer / Essay Display (show after answering) -->
                            <div v-if="(question.type === 'short_answer' || question.type === 'essay') && question.user_answer" class="mt-4 p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-700">{{ question.user_answer.answer_text }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="mt-6 flex items-center justify-between">
                    <Link
                        :href="route('courses.show', course.slug || course.id)"
                        class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:text-blue-600 transition-colors"
                    >
                        <svg class="w-5 h-5" :class="direction === 'rtl' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        {{ t('lessons.actions.back') }}
                    </Link>
                    <Link
                        v-if="course.lessons && course.lessons.length > 0"
                        :href="route('courses.play', course.slug || course.id)"
                        class="flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors"
                    >
                        {{ t('courses.actions.continue') }}
                        <svg class="w-5 h-5" :class="direction === 'rtl' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </component>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useDirection } from '@/composables/useDirection';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useAlert } from '@/composables/useAlert';
import { Link, usePage, router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    lesson: Object,
});

const { direction } = useDirection();
const { t } = useTranslation();
const { route } = useRoute();
const { showSuccess, showError } = useAlert();
const page = usePage();

const auth = computed(() => page.props.auth?.user);
const layout = computed(() => auth.value ? AuthenticatedLayout : AppLayout);
const isStudent = computed(() => auth.value?.role === 'student');

// Initialize answer forms for each question
const answerForms = reactive({});
const submittingAnswers = reactive({});

// Initialize forms for questions that don't have answers yet
if (props.lesson.questions) {
    props.lesson.questions.forEach(question => {
        if (!question.user_answer) {
            answerForms[question.id] = {
                answer_id: null,
                answer_text: '',
            };
        }
    });
}

const formatContent = (content) => {
    if (!content) return '<p class="text-gray-500">' + t('lessons.no_video') + '</p>';
    return content.replace(/\n/g, '<br>');
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleString();
};

const getAnswerClass = (answer, question) => {
    if (!question.user_answer) {
        return 'border-gray-200 bg-gray-50';
    }
    
    const isSelected = question.user_answer.answer_id === answer.id;
    const isCorrect = answer.is_correct;
    
    if (isSelected && isCorrect) {
        return 'border-green-500 bg-green-50';
    } else if (isSelected && !isCorrect) {
        return 'border-red-500 bg-red-50';
    } else if (!isSelected && isCorrect) {
        return 'border-green-300 bg-green-50';
    }
    
    return 'border-gray-200 bg-gray-50';
};

const getAnswerIndicatorClass = (answer, question) => {
    if (!question.user_answer) {
        return 'border-gray-300';
    }
    
    const isSelected = question.user_answer.answer_id === answer.id;
    const isCorrect = answer.is_correct;
    
    if (isSelected && isCorrect) {
        return 'border-green-500 bg-green-100';
    } else if (isSelected && !isCorrect) {
        return 'border-red-500 bg-red-100';
    } else if (!isSelected && isCorrect) {
        return 'border-green-400 bg-green-50';
    }
    
    return 'border-gray-300';
};

const shouldShowCheckmark = (answer, question) => {
    if (!question.user_answer) {
        return false;
    }
    
    const isSelected = question.user_answer.answer_id === answer.id;
    return isSelected;
};

const submitAnswer = async (question) => {
    if (!isStudent.value) {
        return;
    }

    submittingAnswers[question.id] = true;

    const formData = {
        answer_id: answerForms[question.id]?.answer_id || null,
        answer_text: answerForms[question.id]?.answer_text || null,
    };

    router.post(route('student.question-answers.store', {
        lesson: props.lesson.id,
        question: question.id,
    }), formData, {
        preserveScroll: true,
        onSuccess: () => {
            showSuccess(
                t('lessons.answer_submitted') || 'Answer submitted successfully!',
                t('common.success') || 'Success'
            );
            // Reload the page to show the answer
            setTimeout(() => {
                router.reload({ only: ['lesson'] });
            }, 500);
        },
        onError: (errors) => {
            if (errors.message) {
                showError(errors.message, t('common.error') || 'Error');
            }
        },
        onFinish: () => {
            submittingAnswers[question.id] = false;
        },
    });
};

// Convert YouTube URL to embed format
const getEmbedUrl = (url) => {
    if (!url) return '';
    
    // If already embed URL, return as is
    if (url.includes('youtube.com/embed') || url.includes('youtu.be/embed')) {
        return url;
    }
    
    // Extract video ID from various YouTube URL formats
    let videoId = '';
    
    // Standard YouTube URL: https://www.youtube.com/watch?v=VIDEO_ID
    const watchMatch = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/);
    if (watchMatch) {
        videoId = watchMatch[1];
    }
    
    // Short URL: https://youtu.be/VIDEO_ID
    const shortMatch = url.match(/youtu\.be\/([^&\n?#]+)/);
    if (shortMatch) {
        videoId = shortMatch[1];
    }
    
    // If we found a video ID, return embed URL
    if (videoId) {
        return `https://www.youtube.com/embed/${videoId}`;
    }
    
    // If no match, return original URL (might be other video platform)
    return url;
};
</script>

