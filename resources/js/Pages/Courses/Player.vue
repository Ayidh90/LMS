<template>
    <component :is="layout">
        <Head :title="lesson?.translated_title || lesson?.title || course?.translated_title || course?.title || t('courses.player') || 'Course Player'" />
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

            <div class="max-w-7xl mx-auto py-6 px-4">
                <div class="flex gap-6">
                    <!-- Main Video Player -->
                    <div class="flex-1">
                        <!-- Breadcrumb -->
                        <nav class="text-sm text-gray-600 mb-4">
                            <Link :href="route('welcome')" class="hover:text-blue-600">{{ t('common.home') || 'Home' }}</Link>
                            <span class="mx-2">›</span>
                            <Link :href="route('courses.show', course.slug || course.id)" class="hover:text-blue-600">{{ course.translated_title || course.title }}</Link>
                            <span class="mx-2">›</span>
                            <span>{{ lesson?.translated_title || lesson?.title || t('lessons.title') }}</span>
                        </nav>

                        <!-- Video Player (only show if lesson has video) -->
                        <div v-if="lesson?.video_url" class="bg-black rounded-xl overflow-hidden mb-6" style="aspect-ratio: 16/9;">
                            <div v-if="isUploadedVideo" class="w-full h-full">
                                <!-- Uploaded video file -->
                                <video
                                    :id="`video-player-${lesson?.id}`"
                                    :src="lesson.video_url"
                                    class="w-full h-full"
                                    controls
                                    @timeupdate="onVideoTimeUpdate"
                                    @loadedmetadata="onVideoLoaded"
                                ></video>
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
                        
                        <!-- Student Progress & Attendance Card -->
                        <div v-if="isStudent && currentStudentProgress" class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-5 mb-6">
                            <div class="flex flex-wrap items-center gap-6">
                                <!-- Course Progress -->
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">{{ t('lessons.course_progress') || 'Course Progress' }}</p>
                                        <div class="flex items-center gap-2">
                                            <div class="w-24 bg-blue-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" :style="`width: ${currentStudentProgress.progress_percentage}%`"></div>
                                            </div>
                                            <span class="text-sm font-bold text-blue-700">{{ currentStudentProgress.progress_percentage }}%</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ currentStudentProgress.completed_lessons }} / {{ currentStudentProgress.total_lessons }} {{ t('lessons.title') || 'Lessons' }}</p>
                                    </div>
                                </div>
                                
                                <!-- Attendance Rate -->
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">{{ t('lessons.attendance_rate') || 'Attendance Rate' }}</p>
                                        <div class="flex items-center gap-2">
                                            <div class="w-24 bg-green-200 rounded-full h-2">
                                                <div class="bg-green-600 h-2 rounded-full transition-all duration-500" :style="`width: ${currentStudentProgress.attendance_rate}%`"></div>
                                            </div>
                                            <span class="text-sm font-bold text-green-700">{{ currentStudentProgress.attendance_rate }}%</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ currentStudentProgress.attended_lessons }} / {{ currentStudentProgress.total_lessons }} {{ t('lessons.attended') || 'Attended' }}</p>
                                    </div>
                                </div>
                                
                                <!-- Current Lesson Attendance -->
                                <div v-if="currentStudentAttendance" class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center"
                                        :class="{
                                            'bg-green-100': currentStudentAttendance.status === 'present',
                                            'bg-red-100': currentStudentAttendance.status === 'absent',
                                            'bg-yellow-100': currentStudentAttendance.status === 'late',
                                            'bg-blue-100': currentStudentAttendance.status === 'excused',
                                        }">
                                        <svg v-if="currentStudentAttendance.status === 'present'" class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <svg v-else-if="currentStudentAttendance.status === 'absent'" class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <svg v-else class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">{{ t('lessons.current_attendance') || 'This Lesson' }}</p>
                                        <span class="px-2 py-1 rounded-full text-xs font-bold"
                                            :class="{
                                                'bg-green-100 text-green-700': currentStudentAttendance.status === 'present',
                                                'bg-red-100 text-red-700': currentStudentAttendance.status === 'absent',
                                                'bg-yellow-100 text-yellow-700': currentStudentAttendance.status === 'late',
                                                'bg-blue-100 text-blue-700': currentStudentAttendance.status === 'excused',
                                            }">
                                            {{ currentStudentAttendance.status ? (t('lessons.' + currentStudentAttendance.status) || currentStudentAttendance.status) : '' }}
                                        </span>
                                        <p v-if="currentStudentAttendance.marked_by === 'student'" class="text-xs text-purple-600 mt-0.5">
                                            {{ t('lessons.auto_marked') || 'Auto-marked' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Video Progress Indicator (for students - only for uploaded videos) -->
                        <div v-if="isStudent && lesson?.video_url && isUploadedVideo && videoWatchPercentage < 80" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-yellow-800">
                                        {{ t('lessons.watch_video_to_complete') || 'Watch at least 80% of the video to complete this lesson' }}
                                    </p>
                                    <div class="mt-2 bg-yellow-200 rounded-full h-2">
                                        <div class="bg-yellow-600 h-2 rounded-full transition-all duration-300" :style="`width: ${videoWatchPercentage}%`"></div>
                                    </div>
                                    <p class="text-xs text-yellow-700 mt-1">{{ Math.round(videoWatchPercentage) }}% {{ t('lessons.watched') || 'watched' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Lesson Info -->
                        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ lesson?.translated_title || lesson?.title || t('lessons.select_lesson') }}</h1>
                            <p v-if="lesson?.translated_description || lesson?.description" class="text-gray-600 mb-4">{{ lesson.translated_description || lesson.description }}</p>
                            
                            <div v-if="lesson" class="flex items-center gap-6 pt-4 border-t border-gray-200">
                                <div class="flex items-center text-gray-500">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ lesson.duration_minutes }} {{ t('lessons.fields.duration_minutes') }}
                                </div>
                            </div>
                        </div>

                        <!-- Lesson Content -->
                        <div v-if="lesson?.content" class="bg-white rounded-xl shadow-sm p-6 mb-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">{{ t('lessons.fields.content') }}</h2>
                            <div class="prose max-w-none" v-html="formatContent(lesson.content)"></div>
                        </div>

                        <!-- Questions Section -->
                        <div v-if="lesson" class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ t('lessons.questions') }} ({{ lesson?.questions?.length || 0 }})
                                </h2>
                                    <span v-if="lesson?.section_id" class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium">
                                        {{ t('sections.section') }}: {{ getSectionTitle(lesson.section_id) }}
                                    </span>
                                </div>
                                <Link
                                    v-if="isInstructor && can('questions.create')"
                                    :href="route('instructor.lessons.questions.create', [course.slug || course.id, lesson.id])"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors flex items-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    {{ t('lessons.actions.add_question') }}
                                </Link>
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
                                    <div v-if="isStudent && !question.user_answer && can('questions.view')" class="mb-4">
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
                                                        <p class="text-sm font-medium text-gray-900">{{ answer.translated_answer || answer.answer }}</p>
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
                                                {{ submittingAnswers[question.id] ? (t('common.submitting') || t('common.saving') || 'Submitting...') : (t('lessons.submit_answer') || 'Submit Answer') }}
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
                                                {{ submittingAnswers[question.id] ? (t('common.submitting') || t('common.saving') || 'Submitting...') : (t('lessons.submit_answer') || 'Submit Answer') }}
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
                                                <p class="text-sm font-medium text-gray-900">{{ answer.translated_answer || answer.answer }}</p>
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

                                    <!-- Instructor View: Show all answers -->
                                    <div v-if="isInstructor && (question.type === 'multiple_choice' || question.type === 'true_false') && question.answers && question.answers.length > 0 && !question.user_answer" class="space-y-3 mt-4">
                                        <div
                                            v-for="answer in question.answers"
                                            :key="answer.id"
                                            class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg"
                                            :class="answer.is_correct ? 'border-green-300 bg-green-50' : ''"
                                        >
                                            <div class="flex-shrink-0">
                                                <div class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center"
                                                    :class="answer.is_correct ? 'border-green-500 bg-green-50' : ''">
                                                    <svg v-if="answer.is_correct" class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <span class="flex-1 text-gray-700">{{ answer.translated_answer || answer.answer }}</span>
                                            <span v-if="answer.is_correct" class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">
                                                {{ t('lessons.correct') }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Short Answer / Essay for Instructor -->
                                    <div v-if="isInstructor && (question.type === 'short_answer' || question.type === 'essay') && !question.user_answer" class="mt-4 p-4 bg-gray-50 rounded-lg">
                                        <p class="text-sm text-gray-600 italic">{{ t('lessons.text_answer') }}</p>
                                    </div>

                                    <!-- Explanation -->
                                    <div v-if="question.translated_explanation || question.explanation" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                        <p class="text-sm font-medium text-blue-900 mb-1">{{ t('lessons.explanation') }}:</p>
                                        <p class="text-sm text-blue-800">{{ question.translated_explanation || question.explanation }}</p>
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

                        <!-- Attendance Section for Instructor (View Only) -->
                        <div v-if="isInstructor && can('attendance.mark')" class="bg-white rounded-xl shadow-sm p-6 mt-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                    {{ t('lessons.attendance') || 'Attendance' }}
                                    <span v-if="instructorStudents && instructorStudents.length > 0" class="text-sm font-normal text-gray-500">
                                        ({{ instructorStudents.length }} {{ t('courses.students') || 'Students' }})
                                    </span>
                                </h2>
                                <button
                                    type="button"
                                    v-if="lesson && instructorStudents && instructorStudents.length > 0 && can('attendance.mark')"
                                    @click="openAttendanceModal"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors flex items-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    {{ t('lessons.mark_attendance') || 'Mark Attendance' }}
                                </button>
                            </div>

                            <!-- Attendance Records (View Only) -->
                            <div v-if="lesson && instructorAttendances && instructorAttendances.length > 0" class="space-y-3">
                                <div
                                    v-for="attendance in instructorAttendances"
                                    :key="attendance.id"
                                    class="bg-gray-50 border border-gray-200 rounded-lg p-4"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                                {{ getInitials(attendance.student.name) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ attendance.student.name }}</p>
                                                <p class="text-xs text-gray-500">{{ attendance.student.email }}</p>
                                                <p class="text-xs text-gray-500 mt-0.5">{{ attendance.batch.name }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-3 py-1 rounded text-xs font-medium"
                                                :class="{
                                                    'bg-green-100 text-green-700': attendance.status === 'present',
                                                    'bg-red-100 text-red-700': attendance.status === 'absent',
                                                    'bg-yellow-100 text-yellow-700': attendance.status === 'late',
                                                    'bg-blue-100 text-blue-700': attendance.status === 'excused',
                                                }"
                                            >
                                                {{ attendance.status ? (t('lessons.' + attendance.status) || attendance.status) : '' }}
                                            </span>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ formatDate(attendance.marked_at) }}
                                            </p>
                                        </div>
                                    </div>
                                    <p v-if="attendance.notes" class="text-sm text-gray-600 mt-3 pt-3 border-t border-gray-200">
                                        <span class="font-medium">{{ t('lessons.notes') || 'Notes' }}:</span> {{ attendance.notes }}
                                    </p>
                                </div>
                            </div>
                            <div v-else-if="lesson && instructorStudents && instructorStudents.length > 0" class="text-center py-12 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                <p class="text-lg mb-4">{{ t('lessons.attendance_history') || 'No attendance records yet' }}</p>
                                <button
                                    type="button"
                                    @click="openAttendanceModal"
                                    class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors"
                                >
                                    {{ t('lessons.mark_attendance') || 'Mark Attendance' }}
                                </button>
                            </div>
                            <div v-else-if="!lesson" class="text-center py-12 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                <p class="text-lg mb-4">{{ t('lessons.select_lesson') || 'Select a lesson to view attendance' }}</p>
                            </div>
                            <div v-else class="text-center py-12 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="text-lg mb-4">{{ t('lessons.no_students') || 'No students enrolled' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar - Sections with Lessons -->
                    <div class="w-80 flex-shrink-0">
                        <div class="bg-white rounded-xl shadow-sm p-6 sticky top-4">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-bold text-gray-900">{{ t('sections.title') || 'Sections' }}</h2>
                                <span class="text-sm text-gray-500 bg-slate-100 px-2 py-1 rounded">
                                    {{ sections?.length || lessons?.length || 0 }}
                                </span>
                            </div>

                            <div class="space-y-4 max-h-[600px] overflow-y-auto">
                                <!-- Sections with Lessons -->
                                <template v-if="sections && sections.length > 0">
                                    <div
                                        v-for="section in sections"
                                        :key="section.id"
                                        class="border border-gray-200 rounded-lg overflow-hidden"
                                    >
                                        <!-- Section Header - Collapsible -->
                                        <button
                                            type="button"
                                            @click="toggleSection(section.id)"
                                            class="w-full bg-gray-50 px-4 py-3 border-b border-gray-200 hover:bg-gray-100 transition-colors flex items-center justify-between text-left"
                                        >
                                            <div class="flex items-center gap-2 flex-1">
                                                <h3 class="text-sm font-semibold text-gray-900">
                                                    {{ section.translated_title || section.title }}
                                                </h3>
                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                                                    {{ section.lessons?.length || 0 }} 
                                                </span>
                                            </div>
                                            <svg 
                                                class="w-4 h-4 text-gray-500 flex-shrink-0 transition-transform duration-200"
                                                :class="{ 'rotate-180': expandedSections[section.id] }"
                                                fill="none" 
                                                stroke="currentColor" 
                                                viewBox="0 0 24 24"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                        
                                        <!-- Lessons in Section - Collapsible -->
                                        <transition
                                            enter-active-class="transition-all duration-300 ease-out"
                                            enter-from-class="opacity-0 max-h-0"
                                            enter-to-class="opacity-100 max-h-[2000px]"
                                            leave-active-class="transition-all duration-300 ease-in"
                                            leave-from-class="opacity-100 max-h-[2000px]"
                                            leave-to-class="opacity-0 max-h-0"
                                        >
                                            <div v-show="expandedSections[section.id]" class="overflow-hidden">
                                                <div v-if="section.lessons && section.lessons.length > 0" class="p-2 space-y-1">
                                                    <Link
                                                        v-for="(l, lIndex) in section.lessons"
                                                        :key="l.id"
                                                        :href="route('courses.play.lesson', [course.slug || course.id, l.id])"
                                                        class="flex items-center gap-2 p-2 rounded-lg transition-colors group"
                                                        :class="lesson?.id === l.id ? 'bg-blue-50 border border-blue-200' : 'hover:bg-gray-50 border border-transparent'"
                                                    >
                                                        <div class="flex-shrink-0 w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                                            :class="lesson?.id === l.id ? 'bg-blue-600' : 'bg-gray-300 group-hover:bg-blue-400'">
                                                            {{ lIndex + 1 }}
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-xs font-medium text-gray-900 truncate">{{ l.translated_title || l.title }}</p>
                                                            <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                                                <span class="text-xs text-gray-500">{{ l.duration_minutes }} {{ t('lessons.fields.duration_minutes') }}</span>
                                                                <span v-if="l.type" class="px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded text-xs capitalize">
                                                                    {{ l.type ? (t('lessons.types.' + l.type) || l.type) : '' }}
                                                                </span>
                                                                <!-- Completion Status -->
                                                                <span v-if="isStudent && l.completed" class="px-1.5 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">
                                                                    {{ t('lessons.completed') || 'Completed' }}
                                                                </span>
                                                                <!-- Attendance Status -->
                                                                <span v-if="isStudent && l.attendance" class="px-1.5 py-0.5 rounded text-xs font-medium"
                                                                    :class="{
                                                                        'bg-green-100 text-green-700': l.attendance.status === 'present',
                                                                        'bg-red-100 text-red-700': l.attendance.status === 'absent',
                                                                        'bg-yellow-100 text-yellow-700': l.attendance.status === 'late',
                                                                        'bg-blue-100 text-blue-700': l.attendance.status === 'excused',
                                                                    }"
                                                                >
                                                                    {{ l.attendance?.status ? (t('lessons.' + l.attendance.status) || l.attendance.status) : '' }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <svg v-if="lesson?.id === l.id" class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <!-- Completion Checkmark -->
                                                        <svg v-if="isStudent && l.completed && lesson?.id !== l.id" class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <!-- Add Question Button for Instructor -->
                                                        <Link
                                                            v-if="isInstructor && lesson?.id === l.id && can('questions.create')"
                                                            :href="route('instructor.lessons.questions.create', [course.slug || course.id, l.id])"
                                                            @click.stop
                                                            class="flex-shrink-0 p-1.5 text-blue-600 hover:bg-blue-100 rounded transition-colors"
                                                            :title="t('lessons.actions.add_question')"
                                                        >
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                            </svg>
                                                        </Link>
                                                    </Link>
                                                </div>
                                                <div v-else class="p-4 text-center text-gray-400 text-xs">
                                                    {{ t('sections.no_lessons') }}
                                                </div>
                                            </div>
                                        </transition>
                                    </div>
                                </template>
                                
                                <!-- Fallback to Lessons List if no sections -->
                                <template v-else-if="lessons && lessons.length > 0">
                                <Link
                                    v-for="(l, index) in lessons"
                                    :key="l.id"
                                    :href="route('courses.play.lesson', [course.slug || course.id, l.id])"
                                    class="flex items-center gap-3 p-3 rounded-lg transition-colors"
                                    :class="lesson?.id === l.id ? 'bg-blue-50 border border-blue-200' : 'hover:bg-gray-50 border border-transparent'"
                                >
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                        :class="lesson?.id === l.id ? 'bg-blue-600' : 'bg-gray-300'">
                                        {{ index + 1 }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ l.translated_title || l.title }}</p>
                                            <span v-if="l.type" class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded text-xs font-medium capitalize">
                                                {{ l.type ? (t('lessons.types.' + l.type) || l.type) : '' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                                            <span class="text-xs text-gray-500">{{ l.duration_minutes }} {{ t('lessons.fields.duration_minutes') }}</span>
                                            <!-- Completion Status -->
                                            <span v-if="isStudent && l.completed" class="px-1.5 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">
                                                {{ t('lessons.completed') || 'Completed' }}
                                            </span>
                                            <!-- Attendance Status -->
                                            <span v-if="isStudent && l.attendance" class="px-1.5 py-0.5 rounded text-xs font-medium"
                                                :class="{
                                                    'bg-green-100 text-green-700': l.attendance.status === 'present',
                                                    'bg-red-100 text-red-700': l.attendance.status === 'absent',
                                                    'bg-yellow-100 text-yellow-700': l.attendance.status === 'late',
                                                    'bg-blue-100 text-blue-700': l.attendance.status === 'excused',
                                                }"
                                            >
                                                {{ t('lessons.' + l.attendance.status) || l.attendance.status }}
                                            </span>
                                        </div>
                                    </div>
                                    <svg v-if="lesson?.id === l.id" class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <!-- Completion Checkmark -->
                                    <svg v-if="isStudent && l.completed && lesson?.id !== l.id" class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                        <!-- Add Question Button for Instructor -->
                                        <Link
                                            v-if="isInstructor && lesson?.id === l.id && can('questions.create')"
                                            :href="route('instructor.lessons.questions.create', [course.slug || course.id, l.id])"
                                            @click.stop
                                            class="flex-shrink-0 p-1.5 text-blue-600 hover:bg-blue-100 rounded transition-colors"
                                            :title="t('lessons.actions.add_question')"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </Link>
                                </Link>
                                </template>
                                
                                <div v-else class="text-center py-8 text-gray-400 text-sm">
                                    {{ t('lessons.no_lessons') }}
                                </div>
                            </div>

                            <!-- Course Info -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <Link :href="route('courses.show', course.slug || course.id)" class="block">
                                    <img :src="getCourseImage(course)" :alt="course.translated_title || course.title" class="w-full h-32 object-cover rounded-lg mb-3" @error="handleImageError($event)" />
                                    <h3 class="font-semibold text-gray-900 mb-1">{{ course.translated_title || course.title }}</h3>
                                    <p class="text-sm text-gray-500">{{ course.instructor?.name }}</p>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Modal -->
            <Transition name="modal">
                <div v-if="showAttendanceModal && lesson && instructorStudents && instructorStudents.length > 0" 
                     class="fixed inset-0 z-50 overflow-y-auto" 
                     @click.self="closeAttendanceModal">
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
                    
                    <!-- Modal Container -->
                    <div class="flex min-h-full items-center justify-center p-4">
                        <div class="relative bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden transform transition-all" 
                             :dir="direction">
                            <!-- Header -->
                            <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5 border-b border-blue-800 z-10">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-white">{{ t('lessons.mark_attendance') || 'Mark Attendance' }}</h3>
                                            <p class="text-sm text-blue-100 mt-0.5">{{ lesson.translated_title || lesson.title }} - {{ instructorStudents.length }} {{ t('courses.students') || 'Students' }}</p>
                                        </div>
                                    </div>
                                    <button 
                                        type="button" 
                                        @click="closeAttendanceModal" 
                                        class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition-colors"
                                    >
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                                <form @submit.prevent="markAttendance" class="space-y-6">
                                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                        <p class="text-sm text-gray-600 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ t('lessons.attendance_note') || 'Mark attendance for each student. Attendance is separate from course progress.' }}
                                        </p>
                                    </div>
                                    
                                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-100">
                                                <tr>
                                                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                        {{ t('courses.student') || t('courses.students') || 'Student' }}
                                                    </th>
                                                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                        {{ t('lessons.batch') || 'Batch' }}
                                                    </th>
                                                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                        {{ t('lessons.status') || 'Status' }}
                                                    </th>
                                                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                        {{ t('lessons.notes') || 'Notes' }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr v-for="student in instructorStudents" :key="student.id" class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-5 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold mr-3 shadow-md">
                                                                {{ getInitials(student.name) }}
                                                            </div>
                                                            <div>
                                                                <div class="text-sm font-semibold text-gray-900">{{ student.name }}</div>
                                                                <div class="text-xs text-gray-500">{{ student.email }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-4 whitespace-nowrap">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            {{ student.batch_name }}
                                                        </span>
                                                    </td>
                                                    <td class="px-5 py-4 whitespace-nowrap">
                                                        <select 
                                                            v-model="attendanceForm[student.id].status"
                                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 text-sm transition-all"
                                                        >
                                                            <option value="present">{{ t('lessons.present') || 'Present' }}</option>
                                                            <option value="absent">{{ t('lessons.absent') || 'Absent' }}</option>
                                                            <option value="late">{{ t('lessons.late') || 'Late' }}</option>
                                                            <option value="excused">{{ t('lessons.excused') || 'Excused' }}</option>
                                                        </select>
                                                    </td>
                                                    <td class="px-5 py-4">
                                                        <input 
                                                            type="text"
                                                            v-model="attendanceForm[student.id].notes"
                                                            :placeholder="t('lessons.notes_placeholder') || 'Optional notes...'"
                                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 text-sm transition-all"
                                                        />
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Footer -->
                                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                                        <button 
                                            type="button" 
                                            @click="closeAttendanceModal"
                                            class="px-5 py-2.5 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 font-medium transition-all duration-200"
                                        >
                                            {{ t('common.cancel') || 'Cancel' }}
                                        </button>
                                        <button 
                                            type="submit"
                                            :disabled="isSubmittingAttendance"
                                            class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center gap-2 font-medium shadow-lg hover:shadow-xl"
                                        >
                                            <svg v-if="isSubmittingAttendance" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span v-else>
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </span>
                                            {{ t('lessons.mark_attendance') || 'Mark Attendance' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </component>
</template>

<script setup>
import { computed, ref, watch, onMounted, onUnmounted, reactive } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useDirection } from '@/composables/useDirection';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useAlert } from '@/composables/useAlert';
import { usePermissions } from '@/composables/usePermissions';
import { Head, Link, usePage, router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    lesson: Object,
    lessons: Array,
    sections: Array,
    isFavorited: Boolean,
    isEnrolled: Boolean,
    isInstructor: Boolean,
    instructorBatches: Array,
    instructorStudents: Array,
    instructorAttendances: Array,
    // Student attendance data
    studentAttendance: Object,
    studentProgress: Object,
});

const { direction } = useDirection();
const { t } = useTranslation();
const { route } = useRoute();
const { showSuccess, showError } = useAlert();
const { can } = usePermissions();
const page = usePage();

const auth = computed(() => page.props.auth?.user);
const layout = computed(() => auth.value ? AuthenticatedLayout : AppLayout);
const isStudent = computed(() => auth.value?.role === 'student');

// Computed property to get current lesson attendance - updates when lesson changes
const currentStudentAttendance = computed(() => {
    if (!isStudent.value || !props.lesson) {
        return null;
    }
    
    // First, try to use studentAttendance prop (most accurate, includes full details)
    if (props.studentAttendance) {
        return props.studentAttendance;
    }
    
    // Otherwise, try to find attendance status from lessons list
    // Note: lessons list contains attendance as a string (status only)
    if (props.lessons && props.lessons.length > 0) {
        const lessonData = props.lessons.find(l => l.id === props.lesson.id);
        if (lessonData && lessonData.attendance) {
            // If attendance is a string (status), convert to object
            if (typeof lessonData.attendance === 'string') {
                return {
                    status: lessonData.attendance,
                    marked_by: 'student',
                };
            }
            // If it's already an object, return as is
            return lessonData.attendance;
        }
    }
    
    // Try to find from sections
    if (props.sections && props.sections.length > 0) {
        for (const section of props.sections) {
            if (section.lessons) {
                const lessonData = section.lessons.find(l => l.id === props.lesson.id);
                if (lessonData && lessonData.attendance) {
                    // If attendance is a string (status), convert to object
                    if (typeof lessonData.attendance === 'string') {
                        return {
                            status: lessonData.attendance,
                            marked_by: 'student',
                        };
                    }
                    // If it's already an object, return as is
                    return lessonData.attendance;
                }
            }
        }
    }
    
    return null;
});

// Computed property to ensure studentProgress is always available and updated
const currentStudentProgress = computed(() => {
    if (!isStudent.value) {
        return null;
    }
    
    // Use studentProgress prop if available (most accurate, comes from server)
    if (props.studentProgress) {
        // Debug: log progress data
        if (process.env.NODE_ENV === 'development') {
            console.log('Student Progress from server:', props.studentProgress);
        }
        return props.studentProgress;
    }
    
    // Otherwise, calculate from lessons list (fallback)
    if (props.lessons && props.lessons.length > 0) {
        const totalLessons = props.lessons.length;
        const completedLessons = props.lessons.filter(l => l.completed).length;
        const attendedLessons = props.lessons.filter(l => l.attendance && ['present', 'late', 'excused'].includes(l.attendance)).length;
        
        const calculated = {
            total_lessons: totalLessons,
            completed_lessons: completedLessons,
            progress_percentage: totalLessons > 0 ? Math.round((completedLessons / totalLessons) * 100) : 0,
            attended_lessons: attendedLessons,
            attendance_rate: totalLessons > 0 ? Math.round((attendedLessons / totalLessons) * 100) : 0,
        };
        
        // Debug: log calculated progress
        if (process.env.NODE_ENV === 'development') {
            console.log('Calculated Progress from lessons:', calculated);
            console.log('Lessons with completed:', props.lessons.filter(l => l.completed));
        }
        
        return calculated;
    }
    
    return null;
});

// Initialize answer forms for each question
const answerForms = reactive({});
const submittingAnswers = reactive({});

// Video tracking for students
const videoWatchPercentage = ref(0);
const youtubePlayer = ref(null);
const videoPlayer = ref(null);
const videoCheckInterval = ref(null);
const videoDuration = ref(0);

// Initialize forms for questions that don't have answers yet
if (props.lesson?.questions) {
    props.lesson.questions.forEach(question => {
        if (!question.user_answer) {
            answerForms[question.id] = {
                answer_id: null,
                answer_text: '',
            };
        }
    });
}

// Collapsible Sections State
const expandedSections = ref({});

// Initialize sections - expand all by default, or expand the section containing the current lesson
onMounted(() => {
    // Initialize sections
    if (props.sections && props.sections.length > 0) {
        props.sections.forEach(section => {
            // Expand section if it contains the current lesson, otherwise expand by default
            const hasCurrentLesson = props.lesson && section.lessons?.some(l => l.id === props.lesson.id);
            expandedSections.value[section.id] = hasCurrentLesson !== false; // Expand all by default
        });
    }
});

// Watch for lesson changes to auto-expand the section containing the new lesson
watch(() => props.lesson, (newLesson) => {
    if (newLesson && props.sections) {
        props.sections.forEach(section => {
            const hasCurrentLesson = section.lessons?.some(l => l.id === newLesson.id);
            if (hasCurrentLesson) {
                expandedSections.value[section.id] = true;
            }
        });
    }
}, { immediate: true });

const toggleSection = (sectionId) => {
    expandedSections.value[sectionId] = !expandedSections.value[sectionId];
};

// Default course image
const defaultCourseImage = '/images/default-course.avif';

const getCourseImage = (course) => {
    return course.thumbnail_url || course.thumbnail || defaultCourseImage;
};

const handleImageError = (event) => {
    if (event.target.src !== defaultCourseImage) {
        event.target.src = defaultCourseImage;
    }
};

const formatContent = (content) => {
    if (!content) return '';
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

// Attendance Modal
const showAttendanceModal = ref(false);
const isSubmittingAttendance = ref(false);
const attendanceForm = ref({});

const openAttendanceModal = () => {
    if (!props.lesson || !props.instructorStudents || props.instructorStudents.length === 0) {
        return;
    }
    
    // Initialize attendance form with current attendance data or defaults
    const form = {};
    props.instructorStudents.forEach(student => {
        const existingAttendance = props.instructorAttendances?.find(
            a => a.student_id === student.id && a.batch_id === student.batch_id
        );
        
        form[student.id] = {
            student_id: student.id,
            batch_id: student.batch_id,
            status: existingAttendance?.status || 'present',
            notes: existingAttendance?.notes || '',
            attended_at: existingAttendance?.attended_at || new Date().toISOString(),
        };
    });
    
    attendanceForm.value = form;
    showAttendanceModal.value = true;
};

const closeAttendanceModal = () => {
    showAttendanceModal.value = false;
    attendanceForm.value = {};
};

const markAttendance = async () => {
    if (!props.lesson) return;
    
    isSubmittingAttendance.value = true;
    
    const attendances = Object.values(attendanceForm.value).map(form => ({
        student_id: form.student_id,
        batch_id: form.batch_id,
        status: form.status,
        notes: form.notes || null,
        attended_at: form.attended_at || new Date().toISOString(),
    }));

    router.post(route('instructor.lessons.attendance.mark', {
        course: props.course.slug || props.course.id,
        lesson: props.lesson.id,
    }), {
        attendances: attendances,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showSuccess(
                t('lessons.attendance_marked_successfully') || 'Attendance marked successfully!',
                t('common.success') || 'Success'
            );
            closeAttendanceModal();
            // Reload the page to refresh attendance data and student progress
            router.reload({ only: ['instructorAttendances', 'studentProgress', 'studentAttendance'] });
        },
        onError: () => {
            showError(
                t('lessons.failed_to_mark_attendance') || 'Failed to mark attendance',
                t('common.error') || 'Error'
            );
        },
        onFinish: () => {
            isSubmittingAttendance.value = false;
        },
    });
};



const getInitials = (name) => {
    if (!name) return '';
    const parts = name.split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
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

// Get section title by section ID
const getSectionTitle = (sectionId) => {
    if (!sectionId || !props.sections) return '';
    const section = props.sections.find(s => s.id === sectionId);
    return section ? (section.translated_title || section.title) : '';
};

// Check if video is uploaded file (needs 80% watch requirement)
const isUploadedVideo = computed(() => {
    return props.lesson?.type === 'video_file';
});

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

// Track video progress using YouTube IFrame API
const initVideoTracking = () => {
    if (!isStudent.value || !props.lesson?.video_url) return;
    
    // Load YouTube IFrame API if not already loaded
    if (!window.YT) {
        const tag = document.createElement('script');
        tag.src = 'https://www.youtube.com/iframe_api';
        const firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        
        window.onYouTubeIframeAPIReady = () => {
            createYouTubePlayer();
        };
    } else {
        createYouTubePlayer();
    }
};

const createYouTubePlayer = () => {
    if (!props.lesson?.video_url) return;
    
    const videoId = extractVideoId(props.lesson.video_url);
    if (!videoId) return;
    
    const playerId = `youtube-player-${props.lesson.id}`;
    const iframe = document.getElementById(playerId);
    if (!iframe) return;
    
    try {
        youtubePlayer.value = new window.YT.Player(playerId, {
            events: {
                'onStateChange': onPlayerStateChange,
                'onReady': onPlayerReady,
            }
        });
    } catch (e) {
        console.error('Error creating YouTube player:', e);
    }
};

const extractVideoId = (url) => {
    if (!url) return '';
    const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/);
    return match ? match[1] : '';
};

const onPlayerReady = () => {
    if (!youtubePlayer.value) return;
    
    // Start tracking video progress
    videoCheckInterval.value = setInterval(() => {
        if (!youtubePlayer.value) return;
        
        try {
            const currentTime = youtubePlayer.value.getCurrentTime();
            const duration = youtubePlayer.value.getDuration();
            
            if (duration > 0) {
                const percentage = (currentTime / duration) * 100;
                videoWatchPercentage.value = Math.min(percentage, 100);
                
                // Auto-complete if watched 80% and no questions
                if (percentage >= 80 && (!props.lesson?.questions || props.lesson.questions.length === 0)) {
                    markLessonCompleted();
                }
            }
        } catch (e) {
            // Player might not be ready yet
        }
    }, 1000); // Check every second
};

const onPlayerStateChange = (event) => {
    // Player state changed (playing, paused, ended, etc.)
    if (event.data === window.YT.PlayerState.ENDED) {
        videoWatchPercentage.value = 100;
        // If no questions, auto-complete
        if ((!props.lesson?.questions || props.lesson.questions.length === 0) && !props.lesson?.completed) {
            markLessonCompleted();
        }
    }
};

// Track uploaded video progress (HTML5 video)
const onVideoLoaded = () => {
    const videoElement = document.getElementById(`video-player-${props.lesson?.id}`);
    if (videoElement) {
        videoDuration.value = videoElement.duration;
        videoPlayer.value = videoElement;
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
            router.reload({ only: ['lesson', 'course', 'lessons', 'sections', 'studentProgress', 'studentAttendance'] });
        }
    } catch (error) {
        console.error('Error marking lesson as completed:', error);
    }
};

// Watch for lesson changes to initialize video tracking and ensure attendance data is updated
watch(() => props.lesson?.id, (newLessonId, oldLessonId) => {
    // Only process if lesson actually changed
    if (!newLessonId || newLessonId === oldLessonId) {
        return;
    }
    
    // Reset video tracking
    videoWatchPercentage.value = 0;
    
    // Clear previous interval
    if (videoCheckInterval.value) {
        clearInterval(videoCheckInterval.value);
        videoCheckInterval.value = null;
    }
    
    // Destroy previous player
    if (youtubePlayer.value) {
        try {
            youtubePlayer.value.destroy();
        } catch (e) {
            // Ignore errors
        }
        youtubePlayer.value = null;
    }
    
    // Initialize video tracking for new lesson (if student and has uploaded video)
    const newLesson = props.lesson;
    if (newLesson && isStudent.value) {
        const hasVideo = !!newLesson.video_url;
        const isUploaded = newLesson.type === 'video_file';
        
        // Only track uploaded videos (need 80% watch requirement)
        if (hasVideo && isUploaded) {
            setTimeout(() => {
                const videoElement = document.getElementById(`video-player-${newLesson.id}`);
                if (videoElement) {
                    videoPlayer.value = videoElement;
                    videoElement.addEventListener('loadedmetadata', onVideoLoaded);
                    videoElement.addEventListener('timeupdate', onVideoTimeUpdate);
                }
            }, 500);
        }
        
        // Reload student progress and attendance when lesson changes (for students)
        // Small delay to ensure server has processed attendance and updated progress
        if (oldLessonId) {
            setTimeout(() => {
                router.reload({ 
                    only: ['studentProgress', 'studentAttendance', 'lessons', 'sections'],
                    preserveScroll: true 
                });
            }, 300);
        }
    }
}, { immediate: false });

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
                video_watch_percentage: 0,
            }),
        });
        
        const data = await response.json();
        
        if (data.success) {
            router.reload({ only: ['lesson', 'course', 'lessons', 'sections', 'studentProgress', 'studentAttendance'] });
        }
    } catch (error) {
        console.error('Error marking lesson as completed:', error);
    }
};

// Note: Automatic attendance and completion are now handled server-side
// Frontend only tracks video watch percentage for uploaded videos

// Cleanup on unmount
onUnmounted(() => {
    if (videoCheckInterval.value) {
        clearInterval(videoCheckInterval.value);
    }
    if (youtubePlayer.value) {
        try {
            youtubePlayer.value.destroy();
        } catch (e) {
            // Ignore errors
        }
    }
});
</script>

<style scoped>
/* Modal Transitions */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-active .bg-white,
.modal-leave-active .bg-white {
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-from .bg-white,
.modal-leave-to .bg-white {
    transform: scale(0.95);
    opacity: 0;
}
</style>

