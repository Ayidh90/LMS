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

            <div class="container-fluid py-4">
                <div class="row g-4">
                    <!-- Main Video Player -->
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
                                        <p class="text-xs text-gray-500 mt-2 break-all">{{ lesson.live_meeting_link }}</p>
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
                        
                        <!-- Student Progress & Attendance Card -->
                        <div v-if="isStudent && currentStudentProgress" class="card border-0 shadow-sm mb-4" style="background: linear-gradient(to right, #e7f3ff, #e0e7ff); border-color: #bfdbfe !important;">
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <!-- Current Lesson Attendance -->
                                    <div v-if="currentStudentAttendance" class="col-md-12 col-lg-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                                                :class="{
                                                    'bg-success bg-opacity-10': currentStudentAttendance.status === 'present',
                                                    'bg-danger bg-opacity-10': currentStudentAttendance.status === 'absent',
                                                    'bg-warning bg-opacity-10': currentStudentAttendance.status === 'late',
                                                    'bg-info bg-opacity-10': currentStudentAttendance.status === 'excused',
                                                }"
                                                style="width: 48px; height: 48px;"
                                            >
                                                <i class="bi fs-5"
                                                    :class="{
                                                        'bi-check-circle-fill text-success': currentStudentAttendance.status === 'present',
                                                        'bi-x-circle-fill text-danger': currentStudentAttendance.status === 'absent',
                                                        'bi-clock-fill text-warning': currentStudentAttendance.status === 'late',
                                                        'bi-info-circle-fill text-info': currentStudentAttendance.status === 'excused',
                                                    }"
                                                ></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-1 small text-muted fw-medium">{{ t('lessons.current_attendance') || 'This Lesson' }}</p>
                                                <span class="badge mb-1"
                                                    :class="{
                                                        'bg-success bg-opacity-10 text-success': currentStudentAttendance.status === 'present',
                                                        'bg-danger bg-opacity-10 text-danger': currentStudentAttendance.status === 'absent',
                                                        'bg-warning bg-opacity-10 text-warning': currentStudentAttendance.status === 'late',
                                                        'bg-info bg-opacity-10 text-info': currentStudentAttendance.status === 'excused',
                                                    }"
                                                >
                                                    {{ currentStudentAttendance.status ? (t('lessons.' + currentStudentAttendance.status) || currentStudentAttendance.status) : '' }}
                                                </span>
                                                <p v-if="currentStudentAttendance.marked_by === 'student'" class="mb-0 small text-purple mt-1">
                                                    <i class="bi bi-check-circle"></i>
                                                    {{ t('lessons.auto_marked') || 'Auto-marked' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Attendance Rate -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 flex-shrink-0" style="width: 48px; height: 48px;">
                                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-1 small text-muted fw-medium">{{ t('lessons.attendance_rate') || 'Attendance Rate' }}</p>
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <div class="progress flex-grow-1" style="height: 8px;">
                                                        <div class="progress-bar bg-success" role="progressbar" 
                                                            :style="`width: ${(currentStudentProgress?.attendance_rate || 0)}%`"
                                                            :aria-valuenow="currentStudentProgress?.attendance_rate || 0" 
                                                            aria-valuemin="0" 
                                                            aria-valuemax="100"
                                                            style="transition: width 0.5s ease;">
                                                        </div>
                                                    </div>
                                                    <span class="small fw-bold text-success">{{ currentStudentProgress?.attendance_rate || 0 }}%</span>
                                                </div>
                                                <p class="mb-0 small text-muted">{{ currentStudentProgress?.attended_lessons || 0 }} / {{ currentStudentProgress?.total_lessons || 0 }} {{ t('lessons.attended') || 'Attended' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Course Progress -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 flex-shrink-0" style="width: 48px; height: 48px;">
                                                <i class="bi bi-bar-chart-fill text-primary fs-5"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-1 small text-muted fw-medium">{{ t('lessons.course_progress') || 'Course Progress' }}</p>
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <div class="progress flex-grow-1" style="height: 8px;">
                                                        <div class="progress-bar bg-primary" role="progressbar" 
                                                            :style="`width: ${(currentStudentProgress?.progress_percentage || 0)}%`"
                                                            :aria-valuenow="currentStudentProgress?.progress_percentage || 0" 
                                                            aria-valuemin="0" 
                                                            aria-valuemax="100"
                                                            style="transition: width 0.5s ease;">
                                                        </div>
                                                    </div>
                                                    <span class="small fw-bold text-primary">{{ currentStudentProgress?.progress_percentage || 0 }}%</span>
                                                </div>
                                                <p class="mb-0 small text-muted">{{ currentStudentProgress?.completed_lessons || 0 }} / {{ currentStudentProgress?.total_lessons || 0 }} {{ t('lessons.title') || 'Lessons' }}</p>
                                            </div>
                                        </div>
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
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <h2 class="card-title h5 fw-bold mb-0 d-flex align-items-center gap-2">
                                            <i class="bi bi-question-circle fs-5"></i>
                                            {{ t('lessons.questions') }} ({{ lesson?.questions?.length || 0 }})
                                        </h2>
                                        <span v-if="lesson?.section_id" class="badge bg-secondary">
                                            {{ t('sections.section') }}: {{ getSectionTitle(lesson.section_id) }}
                                        </span>
                                    </div>
                                    <Link
                                        v-if="isInstructor && can('questions.create')"
                                        :href="route('instructor.lessons.questions.create', [course.slug || course.id, lesson.id])"
                                        class="btn btn-primary btn-sm d-flex align-items-center gap-2"
                                    >
                                        <i class="bi bi-plus-circle"></i>
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
                    <div v-if="!showAttendanceModal" class="col-lg-3 col-md-4 d-none d-md-block">
                        <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                            <div class="card-body p-0">
                                <!-- Header -->
                                <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                                    <h5 class="mb-0 fw-bold text-dark">{{ t('sections.title') || 'Course Content' }}</h5>
                                    <span class="badge bg-secondary">
                                        {{ sections?.length || lessons?.length || 0 }}
                                    </span>
                                </div>

                                <!-- Sections/Lessons List -->
                                <div class="course-content-list" style="max-height: 600px; overflow-y: auto;">
                                    <!-- Sections with Lessons -->
                                    <template v-if="sections && sections.length > 0">
                                        <div
                                            v-for="section in sections"
                                            :key="section.id"
                                            class="border-bottom"
                                        >
                                            <!-- Section Header - Collapsible -->
                                            <button
                                                type="button"
                                                @click="toggleSection(section.id)"
                                                class="btn btn-link w-100 text-start text-decoration-none p-3 d-flex align-items-center justify-content-between border-0 bg-light hover-bg-light"
                                                style="transition: background-color 0.2s;"
                                                :class="{ 'bg-white': expandedSections[section.id] }"
                                            >
                                                <div class="d-flex align-items-center gap-2 flex-grow-1">
                                                    <h6 class="mb-0 fw-bold text-dark small">
                                                        {{ section.translated_title || section.title }}
                                                    </h6>
                                                    <span class="badge bg-primary">
                                                        {{ section.lessons?.length || 0 }}
                                                    </span>
                                                </div>
                                                <i 
                                                    class="bi bi-chevron-down transition-transform"
                                                    :class="{ 'rotate-180': expandedSections[section.id] }"
                                                    style="transition: transform 0.3s ease;"
                                                ></i>
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
                                                    <div v-if="section.lessons && section.lessons.length > 0" class="p-2">
                                                        <Link
                                                            v-for="(l, lIndex) in section.lessons"
                                                            :key="l.id"
                                                            :href="route('courses.play.lesson', [course.slug || course.id, l.id])"
                                                            class="d-flex align-items-start gap-2 p-2 rounded text-decoration-none lesson-item"
                                                            :class="lesson?.id === l.id ? 'bg-primary bg-opacity-10 border-start border-primary border-3' : 'hover-bg-light'"
                                                            style="transition: all 0.2s;"
                                                        >
                                                            <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-circle text-white small fw-bold"
                                                                :class="lesson?.id === l.id ? 'bg-primary' : 'bg-secondary bg-opacity-50'"
                                                                style="width: 24px; height: 24px; font-size: 11px;"
                                                            >
                                                                {{ lIndex + 1 }}
                                                            </div>
                                                            <div class="flex-grow-1 min-w-0">
                                                                <p class="mb-1 small fw-medium text-dark text-truncate" style="font-size: 13px;">
                                                                    {{ l.translated_title || l.title }}
                                                                </p>
                                                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                                                    <span class="text-muted small">
                                                                        <i class="bi bi-clock"></i>
                                                                        {{ l.duration_minutes }} {{ t('lessons.fields.duration_minutes') }}
                                                                    </span>
                                                                    <span v-if="l.type" class="badge bg-purple bg-opacity-10 text-purple small">
                                                                        <i class="bi" :class="{
                                                                            'bi-play-circle': l.type === 'video' || l.type === 'video_file',
                                                                            'bi-camera-video': l.type === 'live',
                                                                            'bi-file-text': l.type === 'text'
                                                                        }"></i>
                                                                        {{ l.type ? (t('lessons.types.' + l.type) || l.type) : '' }}
                                                                    </span>
                                                                    <!-- Completion Status -->
                                                                    <span v-if="isStudent && l.completed" class="badge bg-success bg-opacity-10 text-success small">
                                                                        <i class="bi bi-check-circle"></i>
                                                                        {{ t('lessons.completed') || 'Completed' }}
                                                                    </span>
                                                                    <!-- Attendance Status -->
                                                                    <span v-if="isStudent && l.attendance" class="badge small"
                                                                        :class="{
                                                                            'bg-success bg-opacity-10 text-success': l.attendance.status === 'present',
                                                                            'bg-danger bg-opacity-10 text-danger': l.attendance.status === 'absent',
                                                                            'bg-warning bg-opacity-10 text-warning': l.attendance.status === 'late',
                                                                            'bg-info bg-opacity-10 text-info': l.attendance.status === 'excused',
                                                                        }"
                                                                    >
                                                                        {{ l.attendance?.status ? (t('lessons.' + l.attendance.status) || l.attendance.status) : '' }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                                                <!-- Active Lesson Indicator -->
                                                                <i v-if="lesson?.id === l.id" class="bi bi-play-circle-fill text-primary"></i>
                                                                <!-- Completion Checkmark -->
                                                                <i v-if="isStudent && l.completed && lesson?.id !== l.id" class="bi bi-check-circle-fill text-success"></i>
                                                                <!-- Add Question Button for Instructor -->
                                                                <Link
                                                                    v-if="isInstructor && lesson?.id === l.id && can('questions.create')"
                                                                    :href="route('instructor.lessons.questions.create', [course.slug || course.id, l.id])"
                                                                    @click.stop
                                                                    class="btn btn-sm btn-link text-primary p-1 text-decoration-none"
                                                                    :title="t('lessons.actions.add_question')"
                                                                >
                                                                    <i class="bi bi-plus-circle"></i>
                                                                </Link>
                                                            </div>
                                                        </Link>
                                                    </div>
                                                    <div v-else class="p-4 text-center text-muted small">
                                                        <i class="bi bi-inbox d-block mb-2" style="font-size: 24px;"></i>
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
                                            class="d-flex align-items-start gap-3 p-3 border-bottom text-decoration-none lesson-item"
                                            :class="lesson?.id === l.id ? 'bg-primary bg-opacity-10 border-start border-primary border-3' : 'hover-bg-light'"
                                            style="transition: all 0.2s;"
                                        >
                                            <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-circle text-white small fw-bold"
                                                :class="lesson?.id === l.id ? 'bg-primary' : 'bg-secondary bg-opacity-50'"
                                                style="width: 32px; height: 32px; font-size: 12px;"
                                            >
                                                {{ index + 1 }}
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                    <p class="mb-0 small fw-medium text-dark text-truncate">
                                                        {{ l.translated_title || l.title }}
                                                    </p>
                                                    <span v-if="l.type" class="badge bg-purple bg-opacity-10 text-purple small">
                                                        <i class="bi" :class="{
                                                            'bi-play-circle': l.type === 'video' || l.type === 'video_file',
                                                            'bi-camera-video': l.type === 'live',
                                                            'bi-file-text': l.type === 'text'
                                                        }"></i>
                                                        {{ l.type ? (t('lessons.types.' + l.type) || l.type) : '' }}
                                                    </span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                                    <span class="text-muted small">
                                                        <i class="bi bi-clock"></i>
                                                        {{ l.duration_minutes }} {{ t('lessons.fields.duration_minutes') }}
                                                    </span>
                                                    <!-- Completion Status -->
                                                    <span v-if="isStudent && l.completed" class="badge bg-success bg-opacity-10 text-success small">
                                                        <i class="bi bi-check-circle"></i>
                                                        {{ t('lessons.completed') || 'Completed' }}
                                                    </span>
                                                    <!-- Attendance Status -->
                                                    <span v-if="isStudent && l.attendance" class="badge small"
                                                        :class="{
                                                            'bg-success bg-opacity-10 text-success': l.attendance.status === 'present',
                                                            'bg-danger bg-opacity-10 text-danger': l.attendance.status === 'absent',
                                                            'bg-warning bg-opacity-10 text-warning': l.attendance.status === 'late',
                                                            'bg-info bg-opacity-10 text-info': l.attendance.status === 'excused',
                                                        }"
                                                    >
                                                        {{ t('lessons.' + l.attendance.status) || l.attendance.status }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                                <!-- Active Lesson Indicator -->
                                                <i v-if="lesson?.id === l.id" class="bi bi-play-circle-fill text-primary"></i>
                                                <!-- Completion Checkmark -->
                                                <i v-if="isStudent && l.completed && lesson?.id !== l.id" class="bi bi-check-circle-fill text-success"></i>
                                                <!-- Add Question Button for Instructor -->
                                                <Link
                                                    v-if="isInstructor && lesson?.id === l.id && can('questions.create')"
                                                    :href="route('instructor.lessons.questions.create', [course.slug || course.id, l.id])"
                                                    @click.stop
                                                    class="btn btn-sm btn-link text-primary p-1 text-decoration-none"
                                                    :title="t('lessons.actions.add_question')"
                                                >
                                                    <i class="bi bi-plus-circle"></i>
                                                </Link>
                                            </div>
                                        </Link>
                                    </template>
                                    
                                    <div v-else class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox d-block mb-2" style="font-size: 32px;"></i>
                                        <p class="mb-0 small">{{ t('lessons.no_lessons') }}</p>
                                    </div>
                                </div>

                                <!-- Course Info -->
                                <div class="p-3 border-top bg-light">
                                    <Link :href="route('courses.show', course.slug || course.id)" class="text-decoration-none">
                                        <img 
                                            :src="getCourseImage(course)" 
                                            :alt="course.translated_title || course.title" 
                                            class="img-fluid rounded mb-2 w-100" 
                                            style="height: 120px; object-fit: cover;"
                                            @error="handleImageError($event)" 
                                        />
                                        <h6 class="fw-bold text-dark mb-1 small">{{ course.translated_title || course.title }}</h6>
                                        <p class="text-muted small mb-0">
                                            <i class="bi bi-person"></i>
                                            {{ course.instructor?.name }}
                                        </p>
                                    </Link>
                                </div>
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
                    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity z-40"></div>
                    
                    <!-- Modal Container -->
                    <div class="flex min-h-full items-center justify-center p-4 relative z-50">
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
                                                        <button 
                                                            type="button"
                                                            class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-600 text-white hover:bg-blue-700 transition-colors"
                                                            disabled
                                                        >
                                                            {{ student.batch_name || t('lessons.batch') || 'Batch' }}
                                                        </button>
                                                    </td>
                                                    <td class="px-5 py-4 whitespace-nowrap">
                                                        <select 
                                                            v-if="attendanceForm[student.id]"
                                                            v-model="attendanceForm[student.id].status"
                                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 text-sm transition-all"
                                                        >
                                                            <option value="present">{{ t('lessons.present') || 'Present' }}</option>
                                                            <option value="absent">{{ t('lessons.absent') || 'Absent' }}</option>
                                                            <option value="late">{{ t('lessons.late') || 'Late' }}</option>
                                                            <option value="excused">{{ t('lessons.excused') || 'Excused' }}</option>
                                                        </select>
                                                        <select 
                                                            v-else
                                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 text-sm transition-all"
                                                            disabled
                                                        >
                                                            <option>{{ t('common.loading') || 'Loading...' }}</option>
                                                        </select>
                                                    </td>
                                                    <td class="px-5 py-4">
                                                        <input 
                                                            type="text"
                                                            v-if="attendanceForm[student.id]"
                                                            v-model="attendanceForm[student.id].notes"
                                                            :placeholder="t('lessons.notes_placeholder') || 'Optional notes...'"
                                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 text-sm transition-all"
                                                        />
                                                        <input 
                                                            v-else
                                                            type="text"
                                                            :placeholder="t('lessons.notes_placeholder') || 'Optional notes...'"
                                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 text-sm transition-all"
                                                            disabled
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
import { computed, ref, watch, onMounted, onUnmounted, reactive, Transition } from 'vue';
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
const { can, getEffectiveRole } = usePermissions();
const page = usePage();

const auth = computed(() => page.props.auth?.user);
const layout = computed(() => auth.value ? AuthenticatedLayout : AppLayout);

// Use getEffectiveRole from usePermissions composable
const effectiveRole = getEffectiveRole;

const isStudent = computed(() => {
    if (!auth.value) return false;
    // Check if effective role is student
    if (effectiveRole.value !== 'student') return false;
    // Also verify user actually has student role
    const userRoles = page.props.auth?.roles || [];
    return userRoles.includes('student') || auth.value.role === 'student';
});

const isInstructor = computed(() => {
    if (props.isInstructor) return true;
    if (!auth.value) return false;
    // Check if effective role is instructor
    if (effectiveRole.value !== 'instructor') return false;
    // Also verify user actually has instructor role
    const userRoles = page.props.auth?.roles || [];
    return userRoles.includes('instructor') || auth.value.role === 'instructor';
});

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
    // Check if user is student based on effective role
    if (!isStudent.value) {
        return null;
    }
    
    // Use studentProgress prop if available (most accurate, comes from server)
    if (props.studentProgress) {
        // Debug: log progress data
        if (process.env.NODE_ENV === 'development') {
            console.log('Student Progress from server:', props.studentProgress);
            console.log('Effective Role:', effectiveRole.value);
            console.log('Is Student:', isStudent.value);
            console.log('Selected Role:', page.props.auth?.selectedRole);
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
const videoError = ref(null);

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

// Get current lesson title for dropdown
const getCurrentLessonTitle = () => {
    if (!props.lesson) return '';
    return props.lesson.translated_title || props.lesson.title || '';
};

// Handle section selection from dropdown
const selectSection = (sectionId) => {
    // Expand the selected section
    expandedSections.value[sectionId] = true;
    
    // Scroll to section in sidebar after navigation
    setTimeout(() => {
        const sectionElement = document.querySelector(`[data-section-id="${sectionId}"]`);
        if (sectionElement) {
            sectionElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }, 300);
};

// Handle lesson selection from dropdown
const selectLesson = (lessonId, sectionId = null) => {
    // If sectionId is provided, expand that section
    if (sectionId) {
        expandedSections.value[sectionId] = true;
    }
    
    // Scroll to lesson in sidebar after navigation
    setTimeout(() => {
        const lessonElement = document.querySelector(`[data-lesson-id="${lessonId}"]`);
        if (lessonElement) {
            lessonElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }, 300);
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
            // Laravel returns dates in UTC format (e.g., "2025-12-12T15:15:00.000000Z")
            // The database stores the time correctly, but Laravel serializes it as UTC
            // We need to parse it and display the UTC time directly (not convert to local)
            // This matches what was stored in the database
            const utcDate = new Date(dateString);
            
            // Use UTC methods to get the time that matches the database
            // This prevents adding timezone offset
            const year = utcDate.getUTCFullYear();
            const month = utcDate.getUTCMonth();
            const day = utcDate.getUTCDate();
            const hours = utcDate.getUTCHours();
            const minutes = utcDate.getUTCMinutes();
            
            // Create a date object with UTC values but display as local format
            // This ensures we show the same time as stored in database
            date = new Date(year, month, day, hours, minutes);
        } else {
            // No timezone info - treat as local time (what was stored)
            // Parse date components directly
            const match = dateString.match(/^(\d{4})-(\d{2})-(\d{2})(?:T(\d{2}):(\d{2})(?::(\d{2}))?)?/);
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
const attendanceForm = reactive({});

const openAttendanceModal = () => {
    if (!props.lesson || !props.instructorStudents || props.instructorStudents.length === 0) {
        return;
    }
    
    // Clear previous form data
    Object.keys(attendanceForm).forEach(key => {
        delete attendanceForm[key];
    });
    
    // Initialize attendance form with current attendance data or defaults
    props.instructorStudents.forEach(student => {
        const existingAttendance = props.instructorAttendances?.find(
            a => a.student_id === student.id && a.batch_id === student.batch_id
        );
        
        attendanceForm[student.id] = {
            student_id: student.id,
            batch_id: student.batch_id,
            status: existingAttendance?.status || 'present',
            notes: existingAttendance?.notes || '',
            attended_at: existingAttendance?.attended_at || new Date().toISOString(),
        };
    });
    
    showAttendanceModal.value = true;
};

const closeAttendanceModal = () => {
    showAttendanceModal.value = false;
    // Clear form data
    Object.keys(attendanceForm).forEach(key => {
        delete attendanceForm[key];
    });
};

const markAttendance = async () => {
    if (!props.lesson) return;
    
    isSubmittingAttendance.value = true;
    
    const attendances = Object.values(attendanceForm).map(form => ({
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
                errorMessage = t('lessons.video_error_unsupported') || 'Video format not supported or MIME type not found. Please check the video file format and URL.';
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
                        console.log('Video Content-Type:', contentType);
                        if (!contentType || !contentType.startsWith('video/')) {
                            console.warn('Video URL does not have video Content-Type:', contentType);
                        }
                    }
                })
                .catch(err => {
                    console.error('Error checking video URL:', err);
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

// Check if file is WMV format
const isWMVFile = (url) => {
    if (!url) return false;
    const urlPath = url.split('?')[0];
    const extension = urlPath.split('.').pop()?.toLowerCase();
    return extension === 'wmv';
};

// Get MIME type from video URL extension
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
            
            // Show success message
            showSuccess(
                t('lessons.attendance_marked_successfully') || 'Attendance marked successfully!',
                t('common.success') || 'Success'
            );
            
            // Reload to update progress and attendance with a small delay to ensure server has processed
            setTimeout(() => {
                router.reload({ 
                    only: ['lesson', 'course', 'lessons', 'sections', 'studentProgress', 'studentAttendance'],
                    preserveScroll: true 
                });
            }, 800);
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
    videoError.value = null; // Reset video error
    
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
    
    // Check if user is student based on effective role
    if (newLesson && isStudent.value && effectiveRole.value === 'student') {
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
                    videoElement.addEventListener('error', onVideoError);
                    videoElement.addEventListener('canplay', onVideoCanPlay);
                }
            }, 500);
        }
        
        // Reload student progress and attendance when lesson changes (for students)
        // Small delay to ensure server has processed attendance and updated progress
        // This ensures progress is updated when clicking on lessons
        setTimeout(() => {
            router.reload({ 
                only: ['studentProgress', 'studentAttendance', 'lessons', 'sections', 'lesson'],
                preserveScroll: true 
            });
        }, 500);
    }
}, { immediate: false });

// Watch for effectiveRole changes to reload progress when role switches
watch(() => effectiveRole.value, (newRole, oldRole) => {
    // If role changed and user is now a student, reload progress
    if (newRole === 'student' && oldRole !== 'student' && isStudent.value) {
        setTimeout(() => {
            router.reload({ 
                only: ['studentProgress', 'studentAttendance', 'lessons', 'sections'],
                preserveScroll: true 
            });
        }, 300);
    }
}, { immediate: false });

// Watch for selectedRole changes to reload progress when role switches
watch(() => page.props.auth?.selectedRole, (newSelectedRole, oldSelectedRole) => {
    // If selected role changed and user is now a student, reload progress
    if (newSelectedRole === 'student' && oldSelectedRole !== 'student' && isStudent.value) {
        setTimeout(() => {
            router.reload({ 
                only: ['studentProgress', 'studentAttendance', 'lessons', 'sections'],
                preserveScroll: true 
            });
        }, 300);
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

/* Udemy-like Sidebar Styles */
.course-content-list {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

.course-content-list::-webkit-scrollbar {
    width: 6px;
}

.course-content-list::-webkit-scrollbar-track {
    background: #f7fafc;
}

.course-content-list::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 3px;
}

.course-content-list::-webkit-scrollbar-thumb:hover {
    background-color: #a0aec0;
}

.lesson-item {
    border-left: 3px solid transparent;
    transition: all 0.2s ease;
}

.lesson-item:hover {
    background-color: #f8f9fa !important;
    border-left-color: #0d6efd;
}

.lesson-item.bg-primary.bg-opacity-10 {
    background-color: rgba(13, 110, 253, 0.08) !important;
}

.hover-bg-light:hover {
    background-color: #f8f9fa !important;
}

/* Rotate chevron icon */
.rotate-180 {
    transform: rotate(180deg);
}

.transition-transform {
    transition: transform 0.3s ease;
}

/* Badge colors - Purple theme for lesson types */
.bg-purple {
    background-color: #6f42c1 !important;
}

.bg-purple.bg-opacity-10 {
    background-color: rgba(111, 66, 193, 0.1) !important;
}

.bg-purple.bg-opacity-25 {
    background-color: rgba(111, 66, 193, 0.25) !important;
}

.text-purple {
    color: #6f42c1 !important;
}

.text-purple-600 {
    color: #6f42c1 !important;
}

.text-purple-700 {
    color: #5a32a3 !important;
}

.bg-purple-100 {
    background-color: rgba(111, 66, 193, 0.1) !important;
}

.bg-purple-700 {
    background-color: #5a32a3 !important;
}

/* Ensure badge text is readable with purple background */
.badge.bg-purple.bg-opacity-10.text-purple {
    border: 1px solid rgba(111, 66, 193, 0.2);
    font-weight: 500;
}

/* Active lesson indicator */
.bi-play-circle-fill {
    font-size: 18px;
}

.bi-check-circle-fill {
    font-size: 18px;
}

/* Section header hover effect */
.btn-link.hover-bg-light:hover {
    background-color: #f8f9fa !important;
}

/* Smooth transitions */
.transition-all {
    transition: all 0.3s ease;
}

/* Course content list item spacing */
.course-content-list .lesson-item:last-child {
    border-bottom: none;
}

/* Improve card shadow */
.card.shadow-sm {
    box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.08) !important;
}

/* Section border styling */
.border-bottom:last-child {
    border-bottom: none !important;
}
</style>

