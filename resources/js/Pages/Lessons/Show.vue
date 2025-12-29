<template>
    <component :is="layout">
        <Head :title="lesson?.translated_title || lesson?.title || course?.translated_title || course?.title || t('lessons.title') || 'Lesson'" />
        <div class="min-h-screen bg-slate-100" :dir="direction">
            <!-- Top Bar -->
            <div class="bg-slate-800 text-white py-2 text-sm">
                <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            +1(323) 555-9876
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            info@lms.com
                        </span>
                    </div>
                </div>
            </div>

            <div class="container-fluid py-4">
                <div class="row g-4">
                    <!-- Main Content -->
                    <div class="col-lg-9 col-md-8">
                        <!-- Breadcrumb -->
                        <nav class="text-sm text-gray-600 mb-4">
                            <Link :href="route('welcome')" class="hover:text-blue-600">{{ t('common.home') || 'Home' }}</Link>
                            <span class="mx-2">›</span>
                            <Link :href="route('courses.show', course.slug || course.id)" class="hover:text-blue-600">{{ course.translated_title || course.title }}</Link>
                            <span class="mx-2">›</span>
                            <span>{{ lesson?.translated_title || lesson?.title || t('lessons.title') }}</span>
                        </nav>

                        <!-- Video Player (only show if lesson has video) -->
                        <div v-if="lesson?.video_url && lesson?.type !== 'live'" class="bg-black rounded-xl overflow-hidden mb-6" style="aspect-ratio: 16/9;">
                            <div v-if="isUploadedVideo" class="w-full h-full relative">
                                <!-- Uploaded video file -->
                                <video
                                    :id="`video-player-${lesson?.id}`"
                                    class="w-full h-full"
                                    controls
                                    preload="metadata"
                                    crossorigin="anonymous"
                                    @timeupdate="onVideoTimeUpdate"
                                    @loadedmetadata="onVideoLoaded"
                                    @error="onVideoError"
                                    @canplay="onVideoCanPlay"
                                    @loadstart="onVideoLoadStart"
                                >
                                    <!-- Primary source with MIME type -->
                                    <source v-if="getVideoMimeType(lesson.video_url)" :src="lesson.video_url" :type="getVideoMimeType(lesson.video_url)">
                                    <!-- Additional WMV sources for better browser compatibility -->
                                    <source v-if="isWMVFile(lesson.video_url)" :src="lesson.video_url" type="video/x-ms-wmv">
                                    <source v-if="isWMVFile(lesson.video_url)" :src="lesson.video_url" type="video/x-ms-asf">
                                    <source v-if="isWMVFile(lesson.video_url)" :src="lesson.video_url" type="application/vnd.ms-asf">
                                    <!-- Fallback source without type -->
                                    <source :src="lesson.video_url">
                                    {{ t('lessons.video_not_supported') || 'Your browser does not support the video tag.' }}
                                </video>
                                <div v-if="videoError" class="absolute inset-0 flex items-center justify-center bg-gray-900 text-white p-4 z-10">
                                    <div class="text-center max-w-md">
                                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-lg font-semibold mb-2">{{ t('lessons.video_error') || 'Video Error' }}</p>
                                        <p class="text-sm text-gray-300 mb-2">{{ videoError }}</p>
                                        <p v-if="lesson.video_url" class="text-xs text-gray-500 break-all mt-2">{{ lesson.video_url }}</p>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="w-full h-full">
                                <!-- YouTube or other video links -->
                                <iframe
                                    :id="`youtube-player-${lesson?.id}`"
                                    :src="getEmbedUrl(lesson.video_url)"
                                    class="w-full h-full"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                ></iframe>
                            </div>
                        </div>
                        
                        <!-- Live Meeting Section -->
                        <div v-if="lesson && lesson.type === 'live'" class="bg-gradient-to-r from-red-50 to-orange-50 border-2 border-red-200 rounded-xl p-6 mb-6 shadow-lg">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center shadow-md">
                                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-gray-900 mb-2 flex items-center gap-2">
                                        <i class="bi bi-camera-video text-red-600"></i>
                                        {{ t('lessons.types.live') || 'Live Meeting' }}
                                    </h3>
                                    <div v-if="lesson.live_meeting_date" class="mb-4">
                                        <p class="text-sm text-gray-600 mb-1 flex items-center gap-2">
                                            <i class="bi bi-calendar-event"></i>
                                            {{ t('lessons.live.meeting_date') || 'Meeting Date & Time' }}:
                                        </p>
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ formatDateTime(lesson.live_meeting_date) }}
                                        </p>
                                    </div>
                                    <div v-if="lesson.live_meeting_link" class="mt-4">
                                        <a
                                            :href="lesson.live_meeting_link"
                                            target="_blank"
                                            @click="handleLiveMeetingClick"
                                            class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 font-semibold transition-all shadow-lg hover:shadow-xl transform hover:scale-105"
                                        >
                                            <i class="bi bi-camera-video me-2"></i>
                                            {{ t('lessons.live.join_meeting') || 'Join Live Meeting' }}
                                            <i class="bi bi-box-arrow-up-right ms-2"></i>
                                        </a>
                                        <!-- Warning for students: Do not click Log-in -->
                                        <div class="mt-3 p-4 bg-red-50 border-2 border-red-300 rounded-lg">
                                            <p class="text-sm text-red-800 flex items-start gap-2 font-semibold mb-2">
                                                <i class="bi bi-exclamation-triangle-fill text-red-600 mt-0.5 text-lg"></i>
                                                <span>
                                                    <strong>{{ t('lessons.live.student_warning_title') || 'Important:' }}</strong>
                                                </span>
                                            </p>
                                            <p class="text-xs text-red-700 ml-7 leading-relaxed">
                                                {{ t('lessons.live.student_warning') || 'Please wait in the waiting room. Do NOT click "Log-in" button. Wait for the instructor to join first.' }}
                                            </p>
                                            <p class="text-xs text-red-600 mt-2 ml-7 font-medium">
                                                {{ t('lessons.live.student_warning_detail') || 'If you click "Log-in", you will become a moderator which is not allowed. Please wait for the instructor.' }}
                                            </p>
                                        </div>
                                        <!-- Meeting Link with Copy Button -->
                                        <div class="mt-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-xs text-gray-600 font-medium flex-1 truncate">
                                                    {{ getMeetingLinkDisplay(lesson.live_meeting_link) }}
                                                </p>
                                                <button
                                                    @click="copyMeetingLink(lesson.live_meeting_link)"
                                                    class="flex-shrink-0 px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-all text-xs font-medium flex items-center gap-1.5"
                                                    :title="t('common.copy') || 'Copy Link'"
                                                >
                                                    <i :class="copiedLink ? 'bi bi-check-circle-fill text-green-600' : 'bi bi-clipboard'"></i>
                                                    <span v-if="copiedLink">{{ t('common.copied') || 'Copied!' }}</span>
                                                    <span v-else>{{ t('common.copy') || 'Copy' }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <p class="text-sm text-yellow-800 flex items-center gap-2">
                                            <i class="bi bi-info-circle"></i>
                                            {{ t('lessons.live.meeting_link_generated') || 'Meeting link will be generated automatically' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Video Progress Indicator (for students - only for uploaded videos) -->
                        <div v-if="isStudent && lesson?.video_url && isUploadedVideo && videoWatchPercentage < 80" class="alert alert-warning border-0 shadow-sm mb-4">
                            <div class="d-flex align-items-start gap-3">
                                <i class="bi bi-info-circle-fill fs-5 flex-shrink-0"></i>
                                <div class="flex-grow-1">
                                    <p class="mb-2 fw-medium">
                                        {{ t('lessons.watch_video_to_complete') || 'Watch at least 80% of the video to complete this lesson' }}
                                    </p>
                                    <div class="progress mb-2" style="height: 10px;">
                                        <div class="progress-bar bg-warning" role="progressbar" 
                                            :style="`width: ${videoWatchPercentage}%`"
                                            :aria-valuenow="Math.round(videoWatchPercentage)" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100"
                                            style="transition: width 0.3s ease;">
                                        </div>
                                    </div>
                                    <p class="mb-0 small">{{ Math.round(videoWatchPercentage) }}% {{ t('lessons.watched') || 'watched' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Lesson Info -->
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body p-4">
                                <h1 class="card-title h3 fw-bold mb-3">{{ lesson?.translated_title || lesson?.title || t('lessons.select_lesson') }}</h1>
                                <p v-if="lesson?.translated_description || lesson?.description" class="card-text text-muted mb-3">{{ lesson.translated_description || lesson.description }}</p>
                                
                                <div v-if="lesson" class="d-flex align-items-center gap-3 pt-3 border-top">
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="bi bi-clock me-2"></i>
                                        <span class="small">{{ lesson.duration_minutes }} {{ t('lessons.fields.duration_minutes') }}</span>
                                    </div>
                                    <div v-if="lesson.questions && lesson.questions.length > 0" class="d-flex align-items-center text-muted">
                                        <i class="bi bi-question-circle me-2"></i>
                                        <span class="small">{{ lesson.questions.length }} {{ t('lessons.questions') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lesson Content -->
                        <div v-if="lesson?.content" class="card shadow-sm border-0 mb-4">
                            <div class="card-body p-4">
                                <h2 class="card-title h5 fw-bold mb-3">{{ t('lessons.fields.content') }}</h2>
                                <div class="lesson-content" v-html="formatContent(lesson.content)"></div>
                            </div>
                        </div>

                        <!-- Questions Section -->
                        <div v-if="lesson" class="card shadow-sm border-0">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                                    <h2 class="card-title h5 fw-bold mb-0 d-flex align-items-center gap-2">
                                        <i class="bi bi-question-circle fs-5"></i>
                                        {{ t('lessons.questions') }} ({{ lesson?.questions?.length || 0 }})
                                    </h2>
                                </div>

                                <div v-if="lesson?.questions && lesson.questions.length > 0" class="space-y-6">
                                    <div
                                        v-for="(question, qIndex) in lesson.questions"
                                        :key="question.id"
                                        class="border-2 border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow"
                                    >
                                        <div class="mb-4">
                                            <div class="flex items-center gap-3 mb-3">
                                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                                    {{ t('lessons.question') }} {{ qIndex + 1 }}
                                                </span>
                                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs capitalize">
                                                    {{ question.type ? (t('lessons.types.' + question.type) || question.type) : '' }}
                                                </span>
                                                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs">
                                                    {{ question.points }} {{ t('lessons.points') }}
                                                </span>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ question.translated_question || question.question }}</h3>
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
                                <div v-else class="text-center py-12 text-gray-500">
                                    <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-lg mb-4">{{ t('lessons.no_questions') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-3 col-md-4 d-none d-md-block">
                        <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                            <div class="card-body p-0">
                                <!-- Course Info -->
                                <div class="p-3 border-top bg-light">
                                    <Link :href="route('courses.show', course.slug || course.id)" class="text-decoration-none">
                                        <h6 class="fw-bold text-dark mb-1 small">{{ course.translated_title || course.title }}</h6>
                                        <p class="text-muted small mb-0">
                                            <i class="bi bi-book"></i>
                                            {{ t('courses.title') || 'Course' }}
                                        </p>
                                    </Link>
                                </div>
                                
                                <!-- Navigation -->
                                <div class="p-3 border-top">
                                    <Link
                                        :href="route('courses.show', course.slug || course.id)"
                                        class="btn btn-outline-secondary btn-sm w-100 mb-2 d-flex align-items-center justify-content-center gap-2"
                                    >
                                        <i class="bi bi-arrow-left"></i>
                                        {{ t('lessons.actions.back') }}
                                    </Link>
                                    <Link
                                        :href="route('courses.play', course.slug || course.id)"
                                        class="btn btn-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-2"
                                    >
                                        {{ t('courses.actions.continue') || 'Continue Learning' }}
                                        <i class="bi bi-arrow-right"></i>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </component>
</template>

<script setup>
import { computed, reactive, ref, watch, onMounted, onUnmounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useDirection } from '@/composables/useDirection';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useAlert } from '@/composables/useAlert';
import { Head, Link, usePage, router } from '@inertiajs/vue3';

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

// Check if video is uploaded file
const isUploadedVideo = computed(() => {
    return props.lesson?.type === 'video_file';
});

// Video tracking for students
const videoWatchPercentage = ref(0);
const videoPlayer = ref(null);
const videoDuration = ref(0);
const videoError = ref(null);

// Copy meeting link functionality
const copiedLink = ref(false);

const copyMeetingLink = async (link) => {
    try {
        await navigator.clipboard.writeText(link);
        copiedLink.value = true;
        showSuccess(t('common.link_copied') || 'Link copied to clipboard!', t('common.success') || 'Success');
        setTimeout(() => {
            copiedLink.value = false;
        }, 2000);
    } catch (err) {
        showError(t('common.copy_failed') || 'Failed to copy link', t('common.error') || 'Error');
    }
};

const getMeetingLinkDisplay = (link) => {
    if (!link) return '';
    // Extract base URL and room name, hide long config parameters
    const match = link.match(/https:\/\/meet\.jit\.si\/([a-z0-9]+)/);
    if (match) {
        return `https://meet.jit.si/${match[1]}`;
    }
    return link;
};

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

const formatDateTime = (dateTime) => {
    if (!dateTime) return '';
    
    try {
        const dateString = String(dateTime);
        let date;
        
        // Check if the date string has timezone info (Z, +, or - after position 10)
        const hasTimezone = dateString.includes('Z') || 
                           dateString.match(/[+-]\d{2}:\d{2}$/) ||
                           (dateString.includes('T') && dateString.length > 16);
        
        if (hasTimezone) {
            // Date has timezone info - extract UTC components to preserve the stored time
            const utcDate = new Date(dateString);
            const year = utcDate.getUTCFullYear();
            const month = utcDate.getUTCMonth();
            const day = utcDate.getUTCDate();
            const hours = utcDate.getUTCHours();
            const minutes = utcDate.getUTCMinutes();
            
            // Create a date object with UTC values but display as local format
            date = new Date(year, month, day, hours, minutes);
        } else {
            // No timezone info - parse date components directly (what was stored)
            const match = dateString.match(/^(\d{4})-(\d{2})-(\d{2})(?:\s+(\d{2}):(\d{2})(?::(\d{2}))?)?/);
            if (match) {
                const [, year, month, day, hour = '00', minute = '00'] = match;
                // Create date in local timezone (no UTC conversion)
                date = new Date(parseInt(year), parseInt(month) - 1, parseInt(day), parseInt(hour), parseInt(minute));
            } else {
                date = new Date(dateString);
            }
        }
        
        if (isNaN(date.getTime())) {
            return '';
        }
        
        // Format the date in local time
        return date.toLocaleString(undefined, {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    } catch (e) {
        console.error('Error formatting date:', e);
        return '';
    }
};

// Handle live meeting link click - mark attendance
const handleLiveMeetingClick = async (event) => {
    if (!isStudent.value || !props.lesson || props.lesson.type !== 'live') {
        return; // Let the link open normally
    }
    
    // Prevent default to handle the click first
    event.preventDefault();
    
    try {
        const response = await fetch(route('lessons.complete', {
            course: props.course.slug || props.course.id,
            lesson: props.lesson.id,
        }), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({}),
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Open the meeting link in a new tab
            window.open(props.lesson.live_meeting_link, '_blank');
            
            // Reload to update progress and attendance
            router.reload({ only: ['lesson'] });
        } else {
            // If marking failed, still open the link
            window.open(props.lesson.live_meeting_link, '_blank');
        }
    } catch (error) {
        console.error('Error marking live lesson attendance:', error);
        // If error, still open the link
        window.open(props.lesson.live_meeting_link, '_blank');
    }
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

// Get MIME type from video URL extension
// Check if file is WMV format
const isWMVFile = (url) => {
    if (!url) return false;
    const urlPath = url.split('?')[0];
    const extension = urlPath.split('.').pop()?.toLowerCase();
    return extension === 'wmv';
};

const getVideoMimeType = (url) => {
    if (!url) return '';
    
    // Extract extension from URL (handle query strings)
    const urlPath = url.split('?')[0];
    const extension = urlPath.split('.').pop()?.toLowerCase();
    
    if (!extension) return '';
    
    const mimeTypes = {
        'mp4': 'video/mp4',
        'webm': 'video/webm',
        'ogg': 'video/ogg',
        'ogv': 'video/ogg',
        'mov': 'video/quicktime',
        'avi': 'video/x-msvideo',
        'wmv': 'video/x-ms-wmv', // Primary WMV MIME type
        'flv': 'video/x-flv',
        'mkv': 'video/x-matroska',
        'm3u8': 'application/x-mpegURL',
        'm4v': 'video/x-m4v',
    };
    
    return mimeTypes[extension] || '';
};

// Check if lesson can be completed
const canCompleteLesson = computed(() => {
    if (!isStudent.value || !props.lesson) return false;
    
    const hasVideo = !!props.lesson.video_url;
    const hasQuestions = props.lesson.questions && props.lesson.questions.length > 0;
    const isUploaded = isUploadedVideo.value;
    
    // If no video and no questions, can complete immediately
    if (!hasVideo && !hasQuestions) {
        return true;
    }
    
    // If has uploaded video, must watch at least 80%
    // If has video link (YouTube, Google Drive), no watch requirement
    if (hasVideo && isUploaded && videoWatchPercentage.value < 80) {
        return false;
    }
    
    // If has questions, must answer all
    if (hasQuestions) {
        const totalQuestions = props.lesson.questions.length;
        const answeredQuestions = props.lesson.questions.filter(q => q.user_answer).length;
        return answeredQuestions >= totalQuestions;
    }
    
    return true;
});

// Track uploaded video progress (HTML5 video)
const onVideoLoaded = () => {
    const videoElement = document.getElementById(`video-player-${props.lesson?.id}`);
    if (videoElement) {
        videoDuration.value = videoElement.duration;
        videoPlayer.value = videoElement;
        videoError.value = null; // Clear any previous errors
    }
};

const onVideoTimeUpdate = () => {
    if (!videoPlayer.value) return;
    
    const currentTime = videoPlayer.value.currentTime;
    const duration = videoPlayer.value.duration;
    
    if (duration > 0) {
        const percentage = (currentTime / duration) * 100;
        videoWatchPercentage.value = Math.min(percentage, 100);
        
        // Auto-complete if watched 80% and no questions
        if (percentage >= 80 && (!props.lesson?.questions || props.lesson.questions.length === 0)) {
            markLessonCompleted();
        }
    }
    
    // Auto-complete when video ends
    if (currentTime >= duration && duration > 0) {
        videoWatchPercentage.value = 100;
        if ((!props.lesson?.questions || props.lesson.questions.length === 0) && !props.lesson?.completed) {
            markLessonCompleted();
        }
    }
};

const onVideoError = (event) => {
    const videoElement = event.target;
    videoError.value = null;
    
    if (videoElement.error) {
        let errorMessage = t('lessons.video_load_error') || 'Failed to load video.';
        
        switch (videoElement.error.code) {
            case videoElement.error.MEDIA_ERR_ABORTED:
                errorMessage = t('lessons.video_error_aborted') || 'Video loading was aborted.';
                break;
            case videoElement.error.MEDIA_ERR_NETWORK:
                errorMessage = t('lessons.video_error_network') || 'Network error while loading video.';
                break;
            case videoElement.error.MEDIA_ERR_DECODE:
                errorMessage = t('lessons.video_error_decode') || 'Video decoding error. The file may be corrupted or in an unsupported format.';
                break;
            case videoElement.error.MEDIA_ERR_SRC_NOT_SUPPORTED:
                errorMessage = t('lessons.video_error_unsupported') || 'Video format not supported or MIME type not found.';
                // For this specific error, provide more helpful message
                if (props.lesson?.video_url) {
                    const mimeType = getVideoMimeType(props.lesson.video_url);
                    if (!mimeType) {
                        errorMessage = t('lessons.video_error_no_mime') || 'No video with supported format and MIME type found. Please ensure the video file has a valid extension (.mp4, .webm, etc.).';
                    }
                }
                break;
        }
        
        videoError.value = errorMessage;
        console.error('Video error:', errorMessage, videoElement.error, 'Video URL:', props.lesson?.video_url);
        
        // Try to fetch the video URL to check if it's accessible
        if (props.lesson?.video_url) {
            fetch(props.lesson.video_url, { method: 'HEAD' })
                .then(response => {
                    if (!response.ok) {
                        console.error('Video URL not accessible:', response.status, response.statusText);
                        videoError.value = `${errorMessage} (HTTP ${response.status})`;
                    } else {
                        const contentType = response.headers.get('content-type');
                        console.log('Video Content-Type from server:', contentType);
                        if (!contentType || !contentType.startsWith('video/')) {
                            console.warn('Video URL does not have video Content-Type:', contentType);
                            // If server returns wrong content type, update error message
                            if (contentType && !contentType.startsWith('video/')) {
                                videoError.value = `${errorMessage} (Server returned: ${contentType})`;
                            }
                        }
                    }
                })
                .catch(err => {
                    console.error('Error checking video URL:', err);
                    videoError.value = `${errorMessage} (Network error: ${err.message})`;
                });
        }
    }
};

const onVideoCanPlay = () => {
    videoError.value = null; // Clear error when video can play
};

const onVideoLoadStart = () => {
    videoError.value = null; // Clear error when video starts loading
};

// Mark lesson as completed (only for uploaded videos with 80% watch requirement)
const markLessonCompleted = async () => {
    if (!isStudent.value || !props.lesson || !canCompleteLesson.value) return;
    
    // Check if already completed to avoid duplicate requests
    if (props.lesson.completed) return;
    
    // Only send completion request for uploaded videos (need to track watch percentage)
    const hasVideo = !!props.lesson.video_url;
    const isUploaded = props.lesson.type === 'video_file';
    
    if (!hasVideo || !isUploaded) {
        // For text lessons, video links, and questions - attendance is handled server-side automatically
        return;
    }
    
    // Only for uploaded videos - send watch percentage
    if (videoWatchPercentage.value < 80) {
        return; // Not ready to complete yet
    }
    
    try {
        const response = await fetch(route('lessons.complete', {
            course: props.course.slug || props.course.id,
            lesson: props.lesson.id,
        }), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({
                video_watch_percentage: videoWatchPercentage.value,
            }),
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Reload to update progress and attendance (no alert)
            router.reload({ only: ['lesson'] });
        }
    } catch (error) {
        console.error('Error marking lesson as completed:', error);
    }
};

// Watch for question answers to check completion (for lessons with questions)
watch(() => props.lesson?.questions, (questions) => {
    if (!isStudent.value || !questions || questions.length === 0) {
        return;
    }
    
    // Check if all questions are answered
    const allAnswered = questions.every(q => q.user_answer);
    if (allAnswered && canCompleteLesson.value) {
        // For lessons with questions, send completion request after all answered
        const hasVideo = !!props.lesson.video_url;
        const isUploaded = props.lesson.type === 'video_file';
        
        // Only send request if not uploaded video (uploaded videos handle via watch percentage)
        if (!hasVideo || !isUploaded) {
            markLessonCompletedForQuestions();
        }
    }
}, { deep: true });

// Mark lesson completed after answering all questions
const markLessonCompletedForQuestions = async () => {
    if (!isStudent.value || !props.lesson) return;
    if (props.lesson.completed) return;
    
    try {
        const response = await fetch(route('lessons.complete', {
            course: props.course.slug || props.course.id,
            lesson: props.lesson.id,
        }), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({
                video_watch_percentage: videoWatchPercentage.value || 0,
            }),
        });
        
        const data = await response.json();
        
        if (data.success) {
            router.reload({ only: ['lesson'] });
        }
    } catch (error) {
        console.error('Error marking lesson as completed:', error);
    }
};

// Initialize video tracking when component mounts or lesson changes
onMounted(() => {
    if (isStudent.value && props.lesson?.video_url && isUploadedVideo.value) {
        setTimeout(() => {
            const videoElement = document.getElementById(`video-player-${props.lesson.id}`);
            if (videoElement) {
                videoPlayer.value = videoElement;
                videoElement.addEventListener('loadedmetadata', onVideoLoaded);
                videoElement.addEventListener('timeupdate', onVideoTimeUpdate);
                videoElement.addEventListener('error', onVideoError);
                videoElement.addEventListener('canplay', onVideoCanPlay);
                videoElement.addEventListener('loadstart', onVideoLoadStart);
            }
        }, 500);
    }
});

// Watch for lesson changes to initialize video tracking
watch(() => props.lesson?.id, (newLessonId, oldLessonId) => {
    // Only process if lesson actually changed
    if (!newLessonId || newLessonId === oldLessonId) {
        return;
    }
    
    // Reset video tracking
    videoWatchPercentage.value = 0;
    videoError.value = null;
    videoPlayer.value = null;
    
    // Initialize video tracking for new lesson (if student and has uploaded video)
    const newLesson = props.lesson;
    if (newLesson && isStudent.value && isUploadedVideo.value) {
        setTimeout(() => {
            const videoElement = document.getElementById(`video-player-${newLesson.id}`);
            if (videoElement) {
                videoPlayer.value = videoElement;
                videoElement.addEventListener('loadedmetadata', onVideoLoaded);
                videoElement.addEventListener('timeupdate', onVideoTimeUpdate);
                videoElement.addEventListener('error', onVideoError);
                videoElement.addEventListener('canplay', onVideoCanPlay);
                videoElement.addEventListener('loadstart', onVideoLoadStart);
            }
        }, 500);
    }
}, { immediate: false });

// Cleanup on unmount
onUnmounted(() => {
    if (videoPlayer.value) {
        videoPlayer.value.removeEventListener('loadedmetadata', onVideoLoaded);
        videoPlayer.value.removeEventListener('timeupdate', onVideoTimeUpdate);
        videoPlayer.value.removeEventListener('error', onVideoError);
        videoPlayer.value.removeEventListener('canplay', onVideoCanPlay);
        videoPlayer.value.removeEventListener('loadstart', onVideoLoadStart);
    }
});
</script>

