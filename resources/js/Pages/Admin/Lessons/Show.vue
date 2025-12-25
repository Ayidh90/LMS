<template>
    <AdminLayout :page-title="lesson.translated_title || lesson.title || 'Lesson Details'">
        <Head :title="lesson.translated_title || lesson.title || 'Lesson Details'" />
        <div class="max-w-6xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <Link v-if="course" :href="route('admin.courses.lessons.index', course.slug || course.id)" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </Link>
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900">{{ lesson.translated_title || lesson.title }}</h1>
                        <p v-if="course" class="text-sm text-gray-500">{{ course.translated_title || course.title }}</p>
                    </div>
                    <button
                        @click="openLessonModal(lesson)"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 text-gray-700 font-medium transition-all"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {{ t('common.edit') }}
                    </button>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="flex gap-8" aria-label="Tabs">
                        <button
                            @click="activeTab = 'details'"
                            :class="[
                                'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                                activeTab === 'details'
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            ]"
                        >
                            {{ t('lessons.details') || t('common.details') || 'Details' }}
                        </button>
                        <button
                            @click="activeTab = 'attendance'"
                            :class="[
                                'py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2',
                                activeTab === 'attendance'
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            ]"
                        >
                            {{ t('lessons.attendance') || t('attendance.title') || 'Attendance' }}
                            <span v-if="attendanceStats" class="px-2 py-0.5 bg-gray-100 rounded-full text-xs">
                                {{ attendanceStats.attendance_rate }}%
                            </span>
                        </button>
                        <button
                            @click="activeTab = 'questions'"
                            :class="[
                                'py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2',
                                activeTab === 'questions'
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            ]"
                        >
                            {{ t('lessons.questions') || t('questions.title') || 'Questions' }}
                            <span v-if="lesson.questions?.length" class="px-2 py-0.5 bg-gray-100 rounded-full text-xs">
                                {{ lesson.questions.length }}
                            </span>
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Live Meeting Banner (always visible when lesson is live) -->
            <div v-if="lesson.type === 'live'" class="mb-6 bg-gradient-to-r from-red-50 to-orange-50 border-2 border-red-200 rounded-xl p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
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
                        <div v-if="lesson.live_meeting_date" class="mb-3">
                            <p class="text-sm text-gray-600 mb-1">{{ t('lessons.live.meeting_date') || 'Meeting Date & Time' }}:</p>
                            <p class="text-lg font-semibold text-gray-900">{{ formatDateTime(lesson.live_meeting_date) }}</p>
                        </div>
                        <div v-if="lesson.live_meeting_link" class="mt-3">
                            <a
                                :href="lesson.live_meeting_link"
                                target="_blank"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 font-semibold transition-all shadow-lg hover:shadow-xl"
                            >
                                <i class="bi bi-camera-video me-2"></i>
                                {{ t('lessons.live.join_meeting') || 'Join Live Meeting' }}
                                <i class="bi bi-box-arrow-up-right ms-2"></i>
                            </a>
                            <p class="text-xs text-gray-500 mt-2 break-all">{{ lesson.live_meeting_link }}</p>
                        </div>
                        <p v-else class="text-sm text-gray-500 mt-2">
                            {{ t('lessons.live.meeting_link_generated') || 'Meeting link will be generated automatically' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Details Tab -->
            <div v-if="activeTab === 'details'" class="space-y-6">
                <!-- Lesson Info Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start gap-6">
                            <div :class="getLessonTypeIcon(lesson.type).bg" class="w-16 h-16 rounded-2xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-8 h-8" :class="getLessonTypeIcon(lesson.type).text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getLessonTypeIcon(lesson.type).path" />
                                </svg>
                            </div>
                            
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700 capitalize">
                                        {{ formatLessonType(lesson.type) }}
                                    </span>
                                    <span v-if="lesson.is_free" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-700">
                                        {{ t('lessons.free') || 'Free' }}
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">{{ t('lessons.fields.order') || 'Order' }}</span>
                                        <p class="font-semibold text-gray-900">{{ lesson.order }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">{{ t('lessons.fields.duration') || 'Duration' }}</span>
                                        <p class="font-semibold text-gray-900">{{ lesson.duration_minutes || 0 }} {{ t('common.minutes') || 'min' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">{{ t('lessons.fields.section') || 'Section' }}</span>
                                        <p class="font-semibold text-gray-900">{{ lesson.section?.translated_title || lesson.section?.title || '-' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">{{ t('questions.title') || 'Questions' }}</span>
                                        <p class="font-semibold text-gray-900">{{ lesson.questions?.length || 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-if="lesson.translated_description || lesson.description" class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">{{ t('lessons.fields.description') || 'Description' }}</h4>
                        <p class="text-gray-600">{{ lesson.translated_description || lesson.description }}</p>
                    </div>
                    
                    <!-- Video Player (for video types) -->
                    <div v-if="lesson.video_url && ['video_file', 'youtube_video', 'google_drive_video', 'embed_frame'].includes(lesson.type)" class="px-6 py-4 border-t border-gray-100">
                        <h4 class="text-sm font-medium text-gray-700 mb-4">{{ t('lessons.fields.video') || 'Video' }}</h4>
                        <div v-if="isUploadedVideo" class="bg-black rounded-xl overflow-hidden" style="aspect-ratio: 16/9;">
                            <!-- Uploaded video file -->
                            <video
                                :id="`video-player-${lesson.id}`"
                                class="w-full h-full"
                                controls
                                preload="metadata"
                                crossorigin="anonymous"
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
                        </div>
                        <div v-else-if="lesson.type === 'embed_frame'" class="bg-black rounded-xl overflow-hidden" style="aspect-ratio: 16/9;">
                            <!-- Embed frame -->
                            <iframe
                                :src="lesson.video_url"
                                class="w-full h-full"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                            ></iframe>
                        </div>
                        <div v-else class="bg-black rounded-xl overflow-hidden" style="aspect-ratio: 16/9;">
                            <!-- YouTube or Google Drive video -->
                            <iframe
                                :src="getEmbedUrl(lesson.video_url)"
                                class="w-full h-full"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                            ></iframe>
                        </div>
                    </div>
                    
                    <!-- Image Display (for image type) -->
                    <div v-if="lesson.video_url && lesson.type === 'image'" class="px-6 py-4 border-t border-gray-100">
                        <h4 class="text-sm font-medium text-gray-700 mb-4">{{ t('lessons.fields.image') || 'Image' }}</h4>
                        <div class="flex justify-center">
                            <img 
                                :src="lesson.video_url" 
                                :alt="lesson.translated_title || lesson.title"
                                class="max-w-full h-auto rounded-lg shadow-lg"
                                @error="handleImageError"
                            />
                        </div>
                        <a :href="lesson.video_url" target="_blank" class="mt-3 inline-block text-sm text-blue-600 hover:text-blue-700 break-all">
                            {{ lesson.video_url }}
                        </a>
                    </div>
                    
                    <!-- Document Display (for document_file type) -->
                    <div v-if="lesson.video_url && lesson.type === 'document_file'" class="px-6 py-4 border-t border-gray-100">
                        <h4 class="text-sm font-medium text-gray-700 mb-4">{{ t('lessons.fields.document') || 'Document' }}</h4>
                        <div class="bg-gray-50 rounded-lg p-6 border-2 border-dashed border-gray-300">
                            <div class="text-center">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <p class="text-gray-600 mb-4">{{ t('lessons.document_preview') || 'Document Preview' }}</p>
                                <a 
                                    :href="lesson.video_url" 
                                    target="_blank"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-semibold transition-all"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    {{ t('lessons.open_document') || 'Open Document' }}
                                </a>
                                <p class="text-xs text-gray-500 mt-3 break-all">{{ lesson.video_url }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-if="lesson.type === 'live'" class="px-6 py-4 border-t border-gray-100 bg-gradient-to-r from-red-50 to-orange-50">
                        <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
                            <i class="bi bi-camera-video text-red-600"></i>
                            {{ t('lessons.types.live') || 'Live Meeting' }}
                        </h4>
                        <div v-if="lesson.live_meeting_date" class="mb-3">
                            <p class="text-sm text-gray-600 mb-1">{{ t('lessons.live.meeting_date') || 'Meeting Date & Time' }}:</p>
                            <p class="text-lg font-semibold text-gray-900">{{ formatDateTime(lesson.live_meeting_date) }}</p>
                        </div>
                        <div v-if="lesson.live_meeting_link" class="mt-3">
                            <p class="text-sm text-gray-600 mb-2">{{ t('lessons.live.meeting_link') || 'Meeting Link' }}:</p>
                            <a :href="lesson.live_meeting_link" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all">
                                {{ t('lessons.live.join_meeting') || 'Join Live Meeting' }}
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                            <p class="text-xs text-gray-500 mt-2 break-all">{{ lesson.live_meeting_link }}</p>
                        </div>
                        <p v-else class="text-sm text-gray-500">{{ t('lessons.live.meeting_link_generated') || 'Meeting link will be generated automatically' }}</p>
                    </div>
                    
                    <!-- Content (for text type lessons) -->
                    <div v-if="lesson.content && lesson.type === 'text'" class="px-6 py-4 border-t border-gray-100">
                        <h4 class="text-sm font-medium text-gray-700 mb-4">{{ t('lessons.fields.content') || 'Content' }}</h4>
                        <div class="prose max-w-none text-gray-700" v-html="formatContent(lesson.content)"></div>
                    </div>
                </div>
            </div>

            <!-- Attendance Tab -->
            <div v-if="activeTab === 'attendance'" class="space-y-6">
                <!-- Attendance Stats -->
                <div v-if="attendanceStats" class="grid grid-cols-2 md:grid-cols-6 gap-4">
                    <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ attendanceStats.total }}</div>
                        <div class="text-sm text-gray-500">{{ t('lessons.total_students') || t('attendance.total') || t('common.total') || 'Total' }}</div>
                    </div>
                    <div class="bg-emerald-50 rounded-xl border border-emerald-100 p-4 text-center">
                        <div class="text-2xl font-bold text-emerald-600">{{ attendanceStats.present }}</div>
                        <div class="text-sm text-emerald-600">{{ formatAttendanceStatus('present') }}</div>
                    </div>
                    <div class="bg-amber-50 rounded-xl border border-amber-100 p-4 text-center">
                        <div class="text-2xl font-bold text-amber-600">{{ attendanceStats.late }}</div>
                        <div class="text-sm text-amber-600">{{ formatAttendanceStatus('late') }}</div>
                    </div>
                    <div class="bg-red-50 rounded-xl border border-red-100 p-4 text-center">
                        <div class="text-2xl font-bold text-red-600">{{ attendanceStats.absent }}</div>
                        <div class="text-sm text-red-600">{{ formatAttendanceStatus('absent') }}</div>
                    </div>
                    <div class="bg-blue-50 rounded-xl border border-blue-100 p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ attendanceStats.excused }}</div>
                        <div class="text-sm text-blue-600">{{ formatAttendanceStatus('excused') }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 text-center">
                        <div class="text-2xl font-bold text-gray-600">{{ attendanceStats.not_marked }}</div>
                        <div class="text-sm text-gray-500">{{ formatAttendanceStatus('not_marked') }}</div>
                    </div>
                </div>

                <!-- Filter by Batch -->
                <div class="flex items-center gap-4">
                    <select
                        v-model="selectedBatch"
                        class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white min-w-[200px]"
                    >
                        <option value="">{{ t('lessons.all_batches') || t('attendance.all_batches') || t('common.all_batches') || 'All Batches' }}</option>
                        <option v-for="batch in batches" :key="batch.id" :value="batch.id">
                            {{ batch.name }} ({{ batch.students_count }})
                        </option>
                    </select>
                    <button
                        v-if="hasChanges"
                        @click="saveAttendance"
                        :disabled="saving"
                        class="px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium transition-all disabled:opacity-50 flex items-center gap-2"
                    >
                        <svg v-if="saving" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        {{ t('lessons.mark_attendance') || t('attendance.save') || t('common.save') || 'Save Attendance' }}
                    </button>
                </div>

                <!-- Attendance Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ t('attendance.student') || 'Student' }}
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ t('lessons.batch') || t('attendance.batch') || t('common.batch') || 'Batch' }}
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ t('lessons.status') || t('attendance.status') || t('common.status') || 'Status' }}
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ t('attendance.marked_by') || 'Marked By' }}
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ t('lessons.progress') || t('attendance.progress') || t('common.progress') || 'Progress' }}
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ t('lessons.completed') || t('attendance.completed') || t('common.completed') || 'Completed' }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <tr v-for="student in filteredStudents" :key="`${student.id}-${student.batch_id}`" class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                                {{ student.name.charAt(0) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">{{ student.name }}</div>
                                                <div class="text-xs text-gray-500">{{ student.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ student.batch_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <select
                                            v-model="attendanceChanges[`${student.id}-${student.batch_id}`]"
                                            @change="markChanged(student)"
                                            class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"
                                            :class="getStatusClass(attendanceChanges[`${student.id}-${student.batch_id}`])"
                                        >
                                            <option value="">{{ formatAttendanceStatus('not_marked') }}</option>
                                            <option value="present">{{ formatAttendanceStatus('present') }}</option>
                                            <option value="late">{{ formatAttendanceStatus('late') }}</option>
                                            <option value="absent">{{ formatAttendanceStatus('absent') }}</option>
                                            <option value="excused">{{ formatAttendanceStatus('excused') }}</option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="student.attendance" class="inline-flex items-center gap-1 text-sm">
                                            <span v-if="student.attendance.marked_by === 'student'" class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs">
                                                {{ t('lessons.auto') || t('attendance.auto') || 'Auto' }}
                                            </span>
                                            <span v-else class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs">
                                                {{ t('attendance.instructor') || 'Instructor' }}
                                            </span>
                                        </span>
                                        <span v-else class="text-gray-400">-</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                                <div
                                                    class="bg-blue-600 h-2 rounded-full transition-all"
                                                    :style="{ width: `${student.enrollment_progress}%` }"
                                                ></div>
                                            </div>
                                            <span class="text-sm text-gray-600">{{ student.enrollment_progress }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="student.completed" class="inline-flex items-center gap-1 text-emerald-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm">{{ t('common.yes') || 'Yes' }}</span>
                                        </span>
                                        <span v-else class="text-gray-400">-</span>
                                    </td>
                                </tr>
                                <tr v-if="filteredStudents.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        {{ t('lessons.no_students') || t('attendance.no_students') || t('common.no_students') || 'No students found' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Questions Tab -->
            <div v-if="activeTab === 'questions'" class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">{{ t('lessons.questions') || t('questions.title') || 'Questions' }}</h3>
                        <button
                            @click="openQuestionModal(null)"
                            class="inline-flex items-center gap-2 px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-xl font-medium transition-all"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            {{ t('admin.add_question') || 'Add Question' }}
                        </button>
                    </div>
                    
                    <div v-if="lesson.questions && lesson.questions.length > 0" class="divide-y divide-gray-100">
                        <div
                            v-for="(question, index) in lesson.questions"
                            :key="question.id"
                            class="p-6 hover:bg-gray-50/50 transition-colors"
                        >
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 font-bold text-sm flex-shrink-0">
                                    {{ index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="px-2 py-0.5 bg-gray-100 rounded text-xs font-medium text-gray-600 capitalize">
                                            {{ formatQuestionType(question.type) }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ question.points }} {{ t('questions.points') || 'pts' }}</span>
                                    </div>
                                    <p class="text-gray-900">{{ question.translated_question || question.question }}</p>
                                </div>
                                <button
                                    @click="openQuestionModal(question)"
                                    class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="p-12 text-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-gray-500">{{ t('questions.no_questions') || t('lessons.no_questions') || 'No questions yet' }}</p>
                    </div>
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

        <!-- Lesson Form Modal -->
        <LessonForm
            :show="showLessonModal"
            :lesson="editingLesson"
            :form-data="lessonForm"
            :errors="lessonForm.errors"
            :processing="lessonForm.processing"
            :sections="sections"
            :lesson-types="lessonTypes"
            @close="closeLessonModal"
            @submit="submitLesson"
        />
    </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useAlert } from '@/composables/useAlert';
import { usePermissions } from '@/composables/usePermissions';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import QuestionForm from '@/Pages/Admin/Questions/Form.vue';
import LessonForm from '@/Pages/Admin/Lessons/Form.vue';

const props = defineProps({
    course: Object,
    lesson: Object,
    batches: Array,
    students: Array,
    attendances: Array,
    attendanceStats: Object,
    questionTypes: Array,
    sections: Array,
    lessonTypes: Array,
});

const { t } = useTranslation();
const { route } = useRoute();
const { showSuccess, showError } = useAlert();
const { isAdmin } = usePermissions();

const activeTab = ref('details');
const selectedBatch = ref('');
const saving = ref(false);
const attendanceChanges = ref({});
const changedStudents = ref(new Set());

// Question modal state
const showQuestionModal = ref(false);
const editingQuestion = ref(null);

// Lesson modal state
const showLessonModal = ref(false);
const editingLesson = ref(null);

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

// Lesson form
const lessonForm = useForm({
    title: '',
    title_ar: '',
    type: '',
    section_id: null,
    order: 1,
    duration_minutes: 0,
    description: '',
    description_ar: '',
    content: '',
    content_ar: '',
    video_url: '',
    video_file: null,
    image_file: null,
    document_file: null,
    live_meeting_date: '',
    live_meeting_link: '',
    is_free: false,
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

// Watch for live_meeting_date changes to clear errors when date is filled
watch(() => lessonForm.live_meeting_date, (newDate, oldDate) => {
    // Clear error if date is now filled and valid
    if (newDate && newDate.trim() !== '' && newDate !== oldDate) {
        // Check if the date is in valid format
        const isValidFormat = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/.test(newDate.trim());
        if (isValidFormat && lessonForm.errors.live_meeting_date) {
            lessonForm.clearErrors('live_meeting_date');
        }
    }
});

// Watch for lesson type changes to handle live lesson fields
watch(() => lessonForm.type, (newType, oldType) => {
    // When switching away from 'live', clear live-specific fields
    if (oldType === 'live' && newType !== 'live') {
        lessonForm.live_meeting_date = '';
        lessonForm.live_meeting_link = '';
        // Clear any errors related to live fields
        if (lessonForm.errors.live_meeting_date) {
            lessonForm.clearErrors('live_meeting_date');
        }
        if (lessonForm.errors.live_meeting_link) {
            lessonForm.clearErrors('live_meeting_link');
        }
    }
    // When switching to 'live', ensure fields are initialized
    if (newType === 'live' && oldType !== 'live') {
        // If live_meeting_date is not set, keep it empty (user needs to fill it)
        // But ensure it's a string, not null
        if (lessonForm.live_meeting_date === null) {
            lessonForm.live_meeting_date = '';
        }
        if (lessonForm.live_meeting_link === null) {
            lessonForm.live_meeting_link = '';
        }
        // Clear any existing errors when switching to live type
        if (lessonForm.errors.live_meeting_date) {
            lessonForm.clearErrors('live_meeting_date');
        }
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
                router.reload({ only: ['lesson'] });
            },
            onError: (errors) => {
                console.error('Update lesson error:', errors);
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
                } else {
                    // Show validation errors
                    const errorMessages = Object.values(errors).flat();
                    if (errorMessages.length > 0) {
                        showError(errorMessages[0], t('common.error') || 'Validation Error');
                    }
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
                router.reload({ only: ['lesson'] });
            },
            onError: (errors) => {
                console.error('Update lesson error:', errors);
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
                } else {
                    // Show validation errors
                    const errorMessages = Object.values(errors).flat();
                    if (errorMessages.length > 0) {
                        showError(errorMessages[0], t('common.error') || 'Validation Error');
                    }
                }
            },
        });
    }
};

// Lesson Modal Functions
const openLessonModal = (lesson = null) => {
    // Clear any existing errors first
    lessonForm.clearErrors();
    
    // Always reset form first to ensure clean state
    lessonForm.reset();
    
    editingLesson.value = lesson;
    if (lesson) {
        // Get section_id from lesson - check both section relationship and direct section_id property
        const sectionId = lesson.section?.id || lesson.section_id || null;
        
        // Use original title field for editing (not translated_title which is for display only)
        // The backend now provides both 'title' (original English) and 'translated_title' (for display)
        const titleValue = (lesson.title && lesson.title.trim() !== '') ? lesson.title.trim() : '';
        lessonForm.title = titleValue;
        lessonForm.title_ar = (lesson.title_ar && lesson.title_ar.trim() !== '') ? lesson.title_ar.trim() : '';
        
        // Clear title error if title has a value
        if (titleValue && titleValue.trim() !== '' && lessonForm.errors.title) {
            lessonForm.clearErrors('title');
        }
        
        // Ensure type is set - it's required and must be a valid type
        const typeValue = (lesson.type && lesson.type.trim() !== '') ? lesson.type : '';
        lessonForm.type = typeValue;
        
        // Clear type error if type has a value
        if (typeValue && typeValue.trim() !== '' && lessonForm.errors.type) {
            lessonForm.clearErrors('type');
        }
        
        // Ensure section_id is set - it's required for admin
        // Convert to number if it's a string, ensure it's not empty string
        let sectionIdValue = null;
        if (sectionId !== null && sectionId !== undefined && sectionId !== '') {
            sectionIdValue = typeof sectionId === 'string' ? parseInt(sectionId) : sectionId;
        }
        lessonForm.section_id = sectionIdValue;
        
        // Clear section_id error if section_id has a value
        if (sectionIdValue !== null && sectionIdValue !== '' && sectionIdValue !== undefined && lessonForm.errors.section_id) {
            lessonForm.clearErrors('section_id');
        }
        
        lessonForm.order = (lesson.order !== null && lesson.order !== undefined) ? lesson.order : 1;
        lessonForm.duration_minutes = (lesson.duration_minutes !== null && lesson.duration_minutes !== undefined) ? lesson.duration_minutes : 0;
        
        // Use original description/content fields for editing (not translated versions)
        // The backend provides both 'description' (original) and 'translated_description' (for display)
        lessonForm.description = lesson.description || '';
        lessonForm.description_ar = lesson.description_ar || '';
        lessonForm.content = lesson.content || '';
        lessonForm.content_ar = lesson.content_ar || '';
        
        // For video_url, use the original video_url from backend (file path or URL)
        // The backend provides both 'video_url' (original) and 'translated_video_url' (formatted for display)
        // We use the original for editing so it can be sent back correctly when updating
        // For file types, this will be the file path (e.g., 'lessons/videos/file.mp4')
        // For URL types, this will be the original URL
        lessonForm.video_url = lesson.video_url || '';
        
        // Clear file fields when editing (user can upload new file if needed)
        lessonForm.video_file = null;
        lessonForm.image_file = null;
        lessonForm.document_file = null;
        
        // Format live_meeting_date for datetime-local input (YYYY-MM-DDTHH:mm)
        // Parse the date string directly without timezone conversion to preserve the stored time
        if (lesson.live_meeting_date) {
            try {
                const dateString = String(lesson.live_meeting_date);
                let year, month, day, hours, minutes;
                
                // Check if date string has timezone info
                const hasTimezone = dateString.includes('Z') || 
                                   dateString.match(/[+-]\d{2}:\d{2}$/) ||
                                   (dateString.includes('T') && dateString.length > 16);
                
                if (hasTimezone) {
                    // Has timezone - extract UTC components to get the stored time
                    const utcDate = new Date(dateString);
                    year = utcDate.getUTCFullYear();
                    month = String(utcDate.getUTCMonth() + 1).padStart(2, '0');
                    day = String(utcDate.getUTCDate()).padStart(2, '0');
                    hours = String(utcDate.getUTCHours()).padStart(2, '0');
                    minutes = String(utcDate.getUTCMinutes()).padStart(2, '0');
                } else {
                    // No timezone - parse directly from string (format: YYYY-MM-DD HH:mm:ss or YYYY-MM-DDTHH:mm:ss)
                    const match = dateString.match(/^(\d{4})-(\d{2})-(\d{2})(?:[\sT](\d{2}):(\d{2})(?::(\d{2}))?)?/);
                    if (match) {
                        [, year, month, day, hours = '00', minutes = '00'] = match;
                        year = parseInt(year);
                        month = String(parseInt(month)).padStart(2, '0');
                        day = String(parseInt(day)).padStart(2, '0');
                        hours = String(parseInt(hours)).padStart(2, '0');
                        minutes = String(parseInt(minutes)).padStart(2, '0');
                    } else {
                        // Fallback to Date parsing
                        const date = new Date(dateString);
                        if (!isNaN(date.getTime())) {
                            year = date.getFullYear();
                            month = String(date.getMonth() + 1).padStart(2, '0');
                            day = String(date.getDate()).padStart(2, '0');
                            hours = String(date.getHours()).padStart(2, '0');
                            minutes = String(date.getMinutes()).padStart(2, '0');
                        } else {
                            lessonForm.live_meeting_date = '';
                            return;
                        }
                    }
                }
                
                lessonForm.live_meeting_date = `${year}-${month}-${day}T${hours}:${minutes}`;
            } catch (e) {
                console.error('Error parsing date:', e);
                lessonForm.live_meeting_date = '';
            }
        } else {
            lessonForm.live_meeting_date = '';
        }
        lessonForm.live_meeting_link = lesson.live_meeting_link || '';
        lessonForm.is_free = lesson.is_free || false;
    } else {
        // It's a new lesson - create mode
        lessonForm.clearErrors();
        lessonForm.order = 1;
        lessonForm.duration_minutes = 0;
        lessonForm.live_meeting_date = '';
        lessonForm.live_meeting_link = '';
        lessonForm.type = ''; // Reset type for new lessons
        lessonForm.is_free = false;
    }
    // Use nextTick to ensure form data is set before modal opens
    nextTick(() => {
        showLessonModal.value = true;
    });
};

const closeLessonModal = () => {
    showLessonModal.value = false;
    editingLesson.value = null;
    // Clear errors before resetting to prevent stale errors
    lessonForm.clearErrors();
    lessonForm.reset();
    // Reset form defaults
    lessonForm.order = 1;
    lessonForm.duration_minutes = 0;
    lessonForm.live_meeting_date = '';
    lessonForm.live_meeting_link = '';
    lessonForm.video_file = null;
    lessonForm.image_file = null;
    lessonForm.document_file = null;
};

const submitLesson = (formData) => {
    // Update lessonForm with formData values (including files)
    // Handle files separately to ensure they're properly set
    Object.keys(formData).forEach(key => {
        // Special handling for files - ensure they're File objects
        if (key === 'video_file' || key === 'image_file' || key === 'document_file') {
            if (formData[key] instanceof File) {
                lessonForm[key] = formData[key];
                // Clear video_url when a new file is uploaded - backend will set it to the file path
                lessonForm.video_url = '';
            }
        } else if (key === 'title') {
            // Always set title, even if empty (will be validated)
            // Trim title to remove leading/trailing whitespace
            lessonForm[key] = (formData[key] !== undefined && formData[key] !== null && typeof formData[key] === 'string') 
                ? formData[key].trim() 
                : (formData[key] || '');
        } else if (key === 'type') {
            // Always set type, even if empty (will be validated)
            lessonForm[key] = formData[key] || '';
        } else if (key === 'section_id') {
            // For section_id, ensure it's a number or null
            if (formData[key] !== null && formData[key] !== undefined && formData[key] !== '') {
                lessonForm[key] = typeof formData[key] === 'string' ? parseInt(formData[key]) : formData[key];
            } else {
                lessonForm[key] = null;
            }
        } else {
            // For all other fields, set them if they're not undefined
            if (formData[key] !== undefined) {
                lessonForm[key] = formData[key];
            }
        }
    });
    
    // Ensure title is always set (even if formData didn't include it)
    if (lessonForm.title === undefined || lessonForm.title === null) {
        lessonForm.title = '';
    } else if (typeof lessonForm.title === 'string') {
        lessonForm.title = lessonForm.title.trim();
    }
    
    // Ensure type is always set (even if formData didn't include it)
    if (lessonForm.type === undefined || lessonForm.type === null) {
        lessonForm.type = '';
    } else {
        lessonForm.type = String(lessonForm.type);
    }
    
    // Ensure section_id is always set (even if null) - critical for FormData
    if (lessonForm.section_id === undefined || lessonForm.section_id === '' || lessonForm.section_id === 0) {
        lessonForm.section_id = null;
    } else if (lessonForm.section_id !== null) {
        // Convert to integer if it's a string
        const parsed = typeof lessonForm.section_id === 'string' 
            ? parseInt(lessonForm.section_id) 
            : lessonForm.section_id;
        // Only set if it's a valid positive integer
        if (isNaN(parsed) || parsed <= 0) {
            lessonForm.section_id = null;
        } else {
            lessonForm.section_id = parsed;
        }
    }
    
    // Validate section is required for admin users - check after updating formData
    const sectionId = lessonForm.section_id;
    if (isAdmin.value && (!sectionId || sectionId === null || sectionId === '' || sectionId === 0)) {
        showError(
            t('lessons.validation.section_required') || 'Section is required for admin users. Please select a section.',
            t('common.error') || 'Validation Error'
        );
        return;
    }
    
    // Validate title is required
    if (!lessonForm.title || lessonForm.title.trim() === '') {
        showError(
            t('lessons.validation.title_required') || 'Title is required.',
            t('common.error') || 'Validation Error'
        );
        return;
    }
    
    // Validate type is required
    if (!lessonForm.type || lessonForm.type.trim() === '') {
        showError(
            t('lessons.validation.type_required') || 'Lesson type is required.',
            t('common.error') || 'Validation Error'
        );
        return;
    }
    
    // Check if we have files to upload (File objects) - do this after setting files in lessonForm
    // Also check if this is a file type lesson (might need FormData even without new file for updates)
    const fileTypesList = ['video_file', 'image', 'document_file'];
    const isFileTypeLesson = fileTypesList.includes(lessonForm.type);
    
    // If files are present, ensure video_url is cleared (will be set by backend)
    if (isFileTypeLesson && (lessonForm.video_file instanceof File || lessonForm.image_file instanceof File || lessonForm.document_file instanceof File)) {
        // Clear video_url when uploading a new file - backend will set it to the uploaded file path
        lessonForm.video_url = '';
    }
    const hasFiles = (lessonForm.video_file instanceof File) || 
                     (lessonForm.image_file instanceof File) || 
                     (lessonForm.document_file instanceof File);
    
    // Only use FormData if we actually have files to upload
    // If no files, use regular JSON to ensure all data is sent correctly
    const useFormData = hasFiles;
    
    // Debug: Log file detection (only in development)
    if (import.meta.env.DEV) {
        console.log('File upload check:', {
            hasFiles,
            isFileTypeLesson,
            useFormData,
            type: lessonForm.type,
            video_file: lessonForm.video_file instanceof File ? 'File' : (lessonForm.video_file ? 'Other' : 'null'),
            image_file: lessonForm.image_file instanceof File ? 'File' : (lessonForm.image_file ? 'Other' : 'null'),
            document_file: lessonForm.document_file instanceof File ? 'File' : (lessonForm.document_file ? 'Other' : 'null'),
        });
    }
    
    // Convert empty strings to null for other nullable fields (but not files or video_url for file types)
    // For file types, preserve video_url if it exists (it might be an existing file path)
    
    ['description', 'description_ar', 'content', 'content_ar', 'title_ar', 'live_meeting_link'].forEach(field => {
        if (lessonForm[field] === '') {
            lessonForm[field] = null;
        }
    });
    
    // Handle video_url separately based on lesson type
    const urlBasedTypes = ['youtube_video', 'google_drive_video', 'embed_frame'];
    
    if (urlBasedTypes.includes(lessonForm.type)) {
        // For URL-based types, preserve video_url (can be empty for validation)
        // Don't convert to null - let backend validate
        if (lessonForm.video_url === '' && editingLesson.value?.video_url && !hasFiles) {
            // If empty and editing, preserve existing URL
            lessonForm.video_url = editingLesson.value.video_url;
        }
    } else if (isFileTypeLesson) {
        // For file types when editing, preserve existing video_url if no new file is uploaded
        if (lessonForm.video_url === '' && editingLesson.value && !hasFiles && editingLesson.value.video_url) {
            lessonForm.video_url = editingLesson.value.video_url;
        }
    } else {
        // For other types (text, assignment, test), convert empty to null
        if (lessonForm.video_url === '') {
            lessonForm.video_url = null;
        }
    }
    
    // Handle live_meeting_date separately - format and validate for live lessons
    if (lessonForm.type === 'live') {
        // For live lessons, send the date as-is without timezone conversion
        // This ensures the database stores exactly what the user selected
        if (lessonForm.live_meeting_date && lessonForm.live_meeting_date.trim() !== '') {
            // datetime-local input provides local time in format YYYY-MM-DDTHH:mm (no timezone)
            // We need to convert this local time to UTC/ISO format for the backend
            const dateValue = lessonForm.live_meeting_date.trim();
            
            // Validate format matches YYYY-MM-DDTHH:mm
            if (/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/.test(dateValue)) {
                // Convert from datetime-local format (YYYY-MM-DDTHH:mm) to MySQL format (YYYY-MM-DD HH:mm:ss)
                // This preserves the exact time the user selected without timezone conversion
                const [datePart, timePart] = dateValue.split('T');
                lessonForm.live_meeting_date = `${datePart} ${timePart}:00`;
            } else {
                // Invalid format, try to parse as-is
                try {
                    const date = new Date(dateValue);
                    if (!isNaN(date.getTime())) {
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        const hours = String(date.getHours()).padStart(2, '0');
                        const minutes = String(date.getMinutes()).padStart(2, '0');
                        lessonForm.live_meeting_date = `${year}-${month}-${day} ${hours}:${minutes}:00`;
                    } else {
                        lessonForm.live_meeting_date = null;
                    }
                } catch (e) {
                    lessonForm.live_meeting_date = null;
                }
            }
        } else {
            // Empty date for live lesson - will be validated by backend
            lessonForm.live_meeting_date = null;
        }
    } else {
        // For non-live lessons, convert empty to null
        if (lessonForm.live_meeting_date === '' || !lessonForm.live_meeting_date) {
            lessonForm.live_meeting_date = null;
        }
    }
    
    // Keep order and duration_minutes as they are (will be converted to strings in formDataToSend)
    // Don't convert to integers here - keep as strings to match StoreLessonRequest format
    if (lessonForm.order === '' || lessonForm.order === null || lessonForm.order === undefined) {
        lessonForm.order = null;
    }
    
    if (lessonForm.duration_minutes === '' || lessonForm.duration_minutes === null || lessonForm.duration_minutes === undefined) {
        lessonForm.duration_minutes = null;
    }
    
    if (editingLesson.value) {
        // Update existing lesson
        // Store files reference before transform
        const videoFile = lessonForm.video_file instanceof File ? lessonForm.video_file : null;
        const imageFile = lessonForm.image_file instanceof File ? lessonForm.image_file : null;
        const documentFile = lessonForm.document_file instanceof File ? lessonForm.document_file : null;
        
        // Build the data object directly from lessonForm before transform
        // This ensures all data is available even when FormData is used
        // Format data to match StoreLessonRequest format EXACTLY (strings for numbers, null for missing files)
        const formDataToSend = {
            title: (lessonForm.title && typeof lessonForm.title === 'string') ? lessonForm.title.trim() : (lessonForm.title || ''),
            type: lessonForm.type ? String(lessonForm.type) : '',
            // Convert section_id to STRING to match StoreLessonRequest format
            section_id: lessonForm.section_id !== null && lessonForm.section_id !== undefined && lessonForm.section_id !== '' && lessonForm.section_id !== 0
                ? String(lessonForm.section_id) 
                : null,
            title_ar: lessonForm.title_ar || null,
            description: lessonForm.description || null,
            description_ar: lessonForm.description_ar || null,
            content: lessonForm.content || null,
            content_ar: lessonForm.content_ar || null,
            // Convert order to STRING to match StoreLessonRequest format
            order: lessonForm.order !== null && lessonForm.order !== undefined && lessonForm.order !== '' 
                ? String(lessonForm.order) 
                : null,
            // Convert duration_minutes to STRING to match StoreLessonRequest format
            duration_minutes: lessonForm.duration_minutes !== null && lessonForm.duration_minutes !== undefined && lessonForm.duration_minutes !== '' 
                ? String(lessonForm.duration_minutes) 
                : null,
            is_free: lessonForm.is_free !== undefined ? lessonForm.is_free : false,
            video_url: lessonForm.video_url || null,
            live_meeting_date: lessonForm.live_meeting_date || null,
            live_meeting_link: lessonForm.live_meeting_link || null,
            // Always include ALL file fields (null if not present) to match StoreLessonRequest format
            video_file: videoFile || null,
            image_file: imageFile || null,
            document_file: documentFile || null,
        };
        
        // Handle video_url based on type
        const urlBasedTypes = ['youtube_video', 'google_drive_video', 'embed_frame'];
        if (urlBasedTypes.includes(lessonForm.type)) {
            formDataToSend.video_url = lessonForm.video_url || '';
        } else if (isFileTypeLesson) {
            if (lessonForm.video_url) {
                formDataToSend.video_url = lessonForm.video_url;
            } else if (!hasFiles && editingLesson.value?.video_url) {
                formDataToSend.video_url = editingLesson.value.video_url;
            } else {
                formDataToSend.video_url = null;
            }
        } else {
            formDataToSend.video_url = null;
        }
        
        // Handle live meeting fields
        if (lessonForm.type === 'live') {
            formDataToSend.live_meeting_date = lessonForm.live_meeting_date || null;
            formDataToSend.live_meeting_link = lessonForm.live_meeting_link || null;
        } else {
            formDataToSend.live_meeting_date = null;
            formDataToSend.live_meeting_link = null;
        }
        
        console.log('Form data to send:', formDataToSend);
        
        // CRITICAL: When using forceFormData, Inertia creates FormData from lessonForm.data()
        // We need to ensure ALL fields are set on lessonForm BEFORE calling .put()
        // The transform function should use the form's data, but we need to ensure it's all there
        
        // CRITICAL: When forceFormData is true, we MUST set ALL fields individually on lessonForm
        // lessonForm.set() might not work correctly with files, so we set everything explicitly
        // This ensures all fields are in lessonForm.data() when Inertia creates FormData
        
        // Set all non-file fields individually - convert null to empty string for FormData
        lessonForm.title = formDataToSend.title || '';
        lessonForm.type = formDataToSend.type || '';
        lessonForm.section_id = formDataToSend.section_id !== null && formDataToSend.section_id !== undefined 
            ? String(formDataToSend.section_id) 
            : '';
        lessonForm.title_ar = formDataToSend.title_ar !== null ? (formDataToSend.title_ar || '') : '';
        lessonForm.description = formDataToSend.description !== null ? (formDataToSend.description || '') : '';
        lessonForm.description_ar = formDataToSend.description_ar !== null ? (formDataToSend.description_ar || '') : '';
        lessonForm.content = formDataToSend.content !== null ? (formDataToSend.content || '') : '';
        lessonForm.content_ar = formDataToSend.content_ar !== null ? (formDataToSend.content_ar || '') : '';
        lessonForm.order = formDataToSend.order !== null && formDataToSend.order !== undefined 
            ? String(formDataToSend.order) 
            : '';
        lessonForm.duration_minutes = formDataToSend.duration_minutes !== null && formDataToSend.duration_minutes !== undefined 
            ? String(formDataToSend.duration_minutes) 
            : '';
        lessonForm.is_free = formDataToSend.is_free !== undefined ? formDataToSend.is_free : false;
        lessonForm.video_url = formDataToSend.video_url !== null ? (formDataToSend.video_url || '') : '';
        lessonForm.live_meeting_date = formDataToSend.live_meeting_date !== null ? (formDataToSend.live_meeting_date || '') : '';
        lessonForm.live_meeting_link = formDataToSend.live_meeting_link !== null ? (formDataToSend.live_meeting_link || '') : '';
        
        // Set files explicitly - these MUST be File objects
        if (videoFile instanceof File) {
            lessonForm.video_file = videoFile;
        } else {
            // Clear video_file if not a File (FormData will skip null/undefined)
            delete lessonForm.video_file;
        }
        if (imageFile instanceof File) {
            lessonForm.image_file = imageFile;
        } else {
            delete lessonForm.image_file;
        }
        if (documentFile instanceof File) {
            lessonForm.document_file = documentFile;
        } else {
            delete lessonForm.document_file;
        }
        
        // Debug: Verify all data is set correctly
        console.log('After setting form data:', {
            hasVideoFile: lessonForm.video_file instanceof File,
            hasImageFile: lessonForm.image_file instanceof File,
            hasDocumentFile: lessonForm.document_file instanceof File,
            title: lessonForm.title,
            type: lessonForm.type,
            section_id: lessonForm.section_id,
            formDataKeys: Object.keys(lessonForm.data()),
            formDataSample: {
                title: lessonForm.data().title,
                type: lessonForm.data().type,
                section_id: lessonForm.data().section_id,
            },
        });
        
        // Use transform to ensure all data is included and properly formatted
        // When forceFormData is true, Inertia creates FormData from the transform return value
        // So we MUST return ALL fields in the transform function
        lessonForm.transform((data) => {
            // CRITICAL: When forceFormData is true, the transform function's return value
            // is used to create FormData, so we MUST return ALL fields explicitly
            // Use formDataToSend as source of truth, but also merge with data parameter
            // to ensure we don't miss anything that was set on the form
            const transformed = {};
            
            // First, include all fields from the data parameter (lessonForm.data())
            // This ensures we get any fields that were set on the form
            if (data && typeof data === 'object') {
                Object.keys(data).forEach(key => {
                    if (data[key] !== undefined) {
                        transformed[key] = data[key];
                    }
                });
            }
            
            // Then, override/ensure all fields from formDataToSend (our source of truth)
            Object.keys(formDataToSend).forEach(key => {
                const value = formDataToSend[key];
                
                // For files, only include if it's a File object
                if (key === 'video_file' || key === 'image_file' || key === 'document_file') {
                    if (value instanceof File) {
                        transformed[key] = value;
                    }
                    // Don't include null files in FormData - they'll be skipped
                } else {
                    // Convert null to empty string for FormData - backend will handle conversion
                    transformed[key] = value === null ? '' : value;
                }
            });
            
            // Ensure critical fields are always present and properly formatted
            // Title
            if (transformed.title && typeof transformed.title === 'string') {
                transformed.title = transformed.title.trim();
            } else if (!transformed.title) {
                transformed.title = '';
            }
            
            // Type
            if (transformed.type !== undefined && transformed.type !== null) {
                transformed.type = String(transformed.type);
            } else {
                transformed.type = '';
            }
            
            // Section ID - CRITICAL for admin users
            if (transformed.section_id === null || transformed.section_id === undefined) {
                transformed.section_id = '';
            } else {
                transformed.section_id = String(transformed.section_id);
            }
            
            // Order
            if (transformed.order === null || transformed.order === undefined) {
                transformed.order = '';
            } else {
                transformed.order = String(transformed.order);
            }
            
            // Duration minutes
            if (transformed.duration_minutes === null || transformed.duration_minutes === undefined) {
                transformed.duration_minutes = '';
            } else {
                transformed.duration_minutes = String(transformed.duration_minutes);
            }
            
            // Ensure is_free is boolean or string
            if (transformed.is_free === undefined) {
                transformed.is_free = false;
            } else if (typeof transformed.is_free === 'boolean') {
                transformed.is_free = transformed.is_free;
            } else {
                transformed.is_free = String(transformed.is_free);
            }
            
            // Debug: Log the transformed data to see what's being sent
            console.log('=== TRANSFORM FUNCTION ===');
            console.log('Transformed data being sent:', transformed);
            console.log('Transformed keys:', Object.keys(transformed));
            console.log('Using FormData:', useFormData);
            console.log('Has files:', hasFiles);
            console.log('Form data on lessonForm:', lessonForm.data());
            console.log('Data parameter in transform:', data);
            console.log('==========================');
            
            return transformed;
        }).put(route('admin.courses.lessons.update', [props.course.slug || props.course.id, editingLesson.value.id]), {
            preserveScroll: true,
            preserveState: true,
            forceFormData: useFormData, // Use FormData if files are present or for file type updates
            onSuccess: () => {
                showSuccess(t('lessons.updated_successfully') || 'Lesson updated successfully!', t('common.success') || 'Success');
                closeLessonModal();
                router.reload({ only: ['lesson'] });
            },
            onError: (errors) => {
                console.error('Update lesson error:', errors);
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
                } else {
                    // Show validation errors
                    const errorMessages = Object.values(errors).flat();
                    if (errorMessages.length > 0) {
                        showError(errorMessages[0], t('common.error') || 'Validation Error');
                    }
                }
            },
        });
    } else {
        // Create new lesson
        // Store files reference before transform
        const videoFile = lessonForm.video_file instanceof File ? lessonForm.video_file : null;
        const imageFile = lessonForm.image_file instanceof File ? lessonForm.image_file : null;
        const documentFile = lessonForm.document_file instanceof File ? lessonForm.document_file : null;
        
        lessonForm.transform((data) => {
            // Include all form data including files
            const transformed = { ...data };
            
            // Ensure required fields are ALWAYS included (even if empty string)
            // This is critical when using FormData as it might filter out empty values
            // Get title directly from lessonForm - always include it explicitly
            let titleValue = '';
            if (lessonForm.title !== undefined && lessonForm.title !== null) {
                if (typeof lessonForm.title === 'string') {
                    titleValue = lessonForm.title.trim();
                } else {
                    titleValue = String(lessonForm.title);
                }
            }
            // ALWAYS include title - even if empty (backend will validate)
            transformed.title = titleValue;
            
            // Get type directly from lessonForm - always include it explicitly
            let typeValue = '';
            if (lessonForm.type !== undefined && lessonForm.type !== null) {
                typeValue = String(lessonForm.type);
            }
            transformed.type = typeValue;
            
            // Section ID - always include it explicitly (even if null)
            // This is critical when using FormData as it might filter out null values
            // Get section_id directly from lessonForm to ensure it's always included
            let sectionIdValue = null;
            if (lessonForm.section_id !== null && lessonForm.section_id !== undefined && lessonForm.section_id !== '' && lessonForm.section_id !== 0) {
                // Ensure it's a number
                if (typeof lessonForm.section_id === 'string') {
                    const parsed = parseInt(lessonForm.section_id);
                    sectionIdValue = (!isNaN(parsed) && parsed > 0) ? parsed : null;
                } else if (typeof lessonForm.section_id === 'number') {
                    sectionIdValue = (lessonForm.section_id > 0) ? lessonForm.section_id : null;
                }
            }
            // ALWAYS include section_id - even if null (backend will validate for admin users)
            // For FormData, we need to explicitly set it, even if null
            transformed.section_id = sectionIdValue;
            
            // Include nullable fields - handle them based on type
            // Title Arabic
            if (lessonForm.title_ar !== null && lessonForm.title_ar !== undefined) {
                transformed.title_ar = lessonForm.title_ar === '' ? null : lessonForm.title_ar;
            }
            
            // Description fields
            if (lessonForm.description !== null && lessonForm.description !== undefined) {
                transformed.description = lessonForm.description === '' ? null : lessonForm.description;
            }
            if (lessonForm.description_ar !== null && lessonForm.description_ar !== undefined) {
                transformed.description_ar = lessonForm.description_ar === '' ? null : lessonForm.description_ar;
            }
            
            // Content fields (for text, assignment, test types)
            if (lessonForm.content !== null && lessonForm.content !== undefined) {
                transformed.content = lessonForm.content === '' ? null : lessonForm.content;
            }
            if (lessonForm.content_ar !== null && lessonForm.content_ar !== undefined) {
                transformed.content_ar = lessonForm.content_ar === '' ? null : lessonForm.content_ar;
            }
            
            // Order and duration
            if (lessonForm.order !== null && lessonForm.order !== undefined) transformed.order = lessonForm.order;
            if (lessonForm.duration_minutes !== null && lessonForm.duration_minutes !== undefined) transformed.duration_minutes = lessonForm.duration_minutes;
            
            // Video URL - handle based on type
            const urlBasedTypes = ['youtube_video', 'google_drive_video', 'embed_frame'];
            if (urlBasedTypes.includes(lessonForm.type)) {
                // For URL-based types, include video_url (can be empty string for validation)
                transformed.video_url = lessonForm.video_url || '';
            } else if (isFileTypeLesson) {
                // For file types, video_url will be set by backend after upload
                transformed.video_url = lessonForm.video_url || null;
            } else {
                // For other types (text, assignment, test), set to null if empty
                transformed.video_url = lessonForm.video_url || null;
            }
            
            // Live meeting fields (only for live type)
            if (lessonForm.type === 'live') {
                transformed.live_meeting_date = lessonForm.live_meeting_date || null;
                transformed.live_meeting_link = lessonForm.live_meeting_link || null;
            } else {
                // Clear live fields for non-live types
                transformed.live_meeting_date = null;
                transformed.live_meeting_link = null;
            }
            
            // Is free flag
            if (lessonForm.is_free !== null && lessonForm.is_free !== undefined) transformed.is_free = lessonForm.is_free;
            
            // Always include files if they exist (as File objects)
            if (videoFile) transformed.video_file = videoFile;
            if (imageFile) transformed.image_file = imageFile;
            if (documentFile) transformed.document_file = documentFile;
            
            return transformed;
        }).post(route('admin.courses.lessons.store', props.course.slug || props.course.id), {
            preserveScroll: true,
            preserveState: true,
            forceFormData: useFormData, // Use FormData if files are present or for file type updates
            onSuccess: () => {
                showSuccess(t('lessons.created_successfully') || 'Lesson created successfully!', t('common.success') || 'Success');
                closeLessonModal();
                router.reload({ only: ['lesson'] });
            },
            onError: (errors) => {
                console.error('Update lesson error:', errors);
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
                } else {
                    // Show validation errors
                    const errorMessages = Object.values(errors).flat();
                    if (errorMessages.length > 0) {
                        showError(errorMessages[0], t('common.error') || 'Validation Error');
                    }
                }
            },
        });
    }
};

// Initialize attendance changes from existing data
onMounted(() => {
    props.students?.forEach(student => {
        const key = `${student.id}-${student.batch_id}`;
        attendanceChanges.value[key] = student.attendance?.status || '';
    });
});

const filteredStudents = computed(() => {
    if (!props.students) return [];
    if (!selectedBatch.value) return props.students;
    return props.students.filter(s => s.batch_id === parseInt(selectedBatch.value));
});

const hasChanges = computed(() => changedStudents.value.size > 0);

const markChanged = (student) => {
    const key = `${student.id}-${student.batch_id}`;
    const originalStatus = student.attendance?.status || '';
    
    if (attendanceChanges.value[key] !== originalStatus) {
        changedStudents.value.add(key);
    } else {
        changedStudents.value.delete(key);
    }
};

const saveAttendance = () => {
    saving.value = true;
    
    const attendances = [];
    changedStudents.value.forEach(key => {
        const [studentId, batchId] = key.split('-');
        const status = attendanceChanges.value[key];
        if (status) {
            attendances.push({
                student_id: parseInt(studentId),
                batch_id: parseInt(batchId),
                status: status,
            });
        }
    });
    
    router.post(route('admin.courses.lessons.attendance.mark', [props.course.slug || props.course.id, props.lesson.id]), {
        attendances: attendances,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            changedStudents.value.clear();
            saving.value = false;
        },
        onError: () => {
            saving.value = false;
        },
    });
};

const getStatusClass = (status) => {
    const classes = {
        present: 'bg-emerald-50 text-emerald-700 border-emerald-200',
        late: 'bg-amber-50 text-amber-700 border-amber-200',
        absent: 'bg-red-50 text-red-700 border-red-200',
        excused: 'bg-blue-50 text-blue-700 border-blue-200',
    };
    return classes[status] || '';
};

const getLessonTypeIcon = (type) => {
    const icons = {
        text: { bg: 'bg-blue-100', text: 'text-blue-600', path: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
        youtube_video: { bg: 'bg-red-100', text: 'text-red-600', path: 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
        google_drive_video: { bg: 'bg-yellow-100', text: 'text-yellow-600', path: 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z' },
        video_file: { bg: 'bg-purple-100', text: 'text-purple-600', path: 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z' },
        image: { bg: 'bg-emerald-100', text: 'text-emerald-600', path: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z' },
        document_file: { bg: 'bg-orange-100', text: 'text-orange-600', path: 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z' },
        embed_frame: { bg: 'bg-cyan-100', text: 'text-cyan-600', path: 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4' },
        assignment: { bg: 'bg-indigo-100', text: 'text-indigo-600', path: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' },
        test: { bg: 'bg-rose-100', text: 'text-rose-600', path: 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
        live: { bg: 'bg-red-100', text: 'text-red-600', path: 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z' },
    };
    return icons[type] || icons.text;
};

const formatLessonType = (type) => {
    // Use direct translation key lookup for proper localization
    return t(`lessons.types.${type}`) || type;
};

const formatQuestionType = (type) => {
    return t(`lessons.types.${type}`) || type;
};

const formatAttendanceStatus = (status) => {
    if (!status || status === '') {
        return t('lessons.not_marked') || 'Not Marked';
    }
    
    const statusMap = {
        present: t('lessons.present') || 'Present',
        absent: t('lessons.absent') || 'Absent',
        late: t('lessons.late') || 'Late',
        excused: t('lessons.excused') || 'Excused',
        not_marked: t('lessons.not_marked') || 'Not Marked',
    };
    
    return statusMap[status] || status;
};

// Check if video is uploaded file
const isUploadedVideo = computed(() => {
    return props.lesson?.type === 'video_file';
});

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
    
    // If no match, return original URL (might be other video platform or Google Drive)
    return url;
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

// Handle image loading errors
const handleImageError = (event) => {
    event.target.src = '/images/default-image.png';
    event.target.onerror = null; // Prevent infinite loop
};

// Format content for display
const formatContent = (content) => {
    if (!content) return '';
    return content.replace(/\n/g, '<br>');
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
</script>
