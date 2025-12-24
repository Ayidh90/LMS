<template>
    <AdminLayout :page-title="lesson.translated_title || lesson.title || 'Lesson Details'">
        <Head :title="lesson.translated_title || lesson.title || 'Lesson Details'" />
        <div class="max-w-6xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <Link :href="route('admin.courses.lessons.index', course.slug || course.id)" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </Link>
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900">{{ lesson.translated_title || lesson.title }}</h1>
                        <p class="text-sm text-gray-500">{{ course.translated_title || course.title }}</p>
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
                    
                    <div v-if="lesson.video_url && lesson.type !== 'live'" class="px-6 py-4 border-t border-gray-100">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">{{ t('lessons.fields.video_url') || 'Video URL' }}</h4>
                        <a :href="lesson.video_url" target="_blank" class="text-blue-600 hover:text-blue-700 break-all">{{ lesson.video_url }}</a>
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
import { ref, computed, onMounted, watch } from 'vue';
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
    live_meeting_date: '',
    live_meeting_link: '',
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
                router.reload({ only: ['lesson'] });
            },
            onError: (errors) => {
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
                }
            },
        });
    }
};

// Lesson Modal Functions
const openLessonModal = (lesson = null) => {
    // Clear any existing errors first
    lessonForm.clearErrors();
    
    editingLesson.value = lesson;
    if (lesson) {
        // Get section_id from lesson - check both section relationship and direct section_id property
        const sectionId = lesson.section?.id || lesson.section_id || null;
        lessonForm.title = lesson.title || '';
        lessonForm.title_ar = lesson.title_ar || '';
        lessonForm.type = lesson.type || '';
        lessonForm.section_id = sectionId;
        lessonForm.order = lesson.order || 1;
        lessonForm.duration_minutes = lesson.duration_minutes || 0;
        lessonForm.description = lesson.description || '';
        lessonForm.description_ar = lesson.description_ar || '';
        lessonForm.content = lesson.content || '';
        lessonForm.content_ar = lesson.content_ar || '';
        lessonForm.video_url = lesson.video_url || '';
        
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
    } else {
        // It's a new lesson - create mode
        lessonForm.reset();
        lessonForm.clearErrors();
        lessonForm.order = 1;
        lessonForm.duration_minutes = 0;
        lessonForm.live_meeting_date = '';
        lessonForm.live_meeting_link = '';
        lessonForm.type = ''; // Reset type for new lessons
    }
    showLessonModal.value = true;
};

const closeLessonModal = () => {
    showLessonModal.value = false;
    editingLesson.value = null;
    lessonForm.reset();
    lessonForm.clearErrors();
    // Reset form defaults
    lessonForm.order = 1;
    lessonForm.duration_minutes = 0;
    lessonForm.live_meeting_date = '';
    lessonForm.live_meeting_link = '';
};

const submitLesson = (formData) => {
    // Validate section is required for admin users
    if (isAdmin.value && (!lessonForm.section_id || lessonForm.section_id === null || lessonForm.section_id === '')) {
        showError(
            t('lessons.validation.section_required') || 'Section is required for admin users. Please select a section.',
            t('common.error') || 'Validation Error'
        );
        return;
    }
    
    // Convert empty strings to null for nullable fields
    if (lessonForm.section_id === '' || lessonForm.section_id === null) {
        lessonForm.section_id = null;
    } else if (lessonForm.section_id) {
        lessonForm.section_id = parseInt(lessonForm.section_id);
    }
    
    // Convert empty strings to null for other nullable fields
    ['description', 'description_ar', 'content', 'content_ar', 'video_url', 'title_ar', 'live_meeting_link'].forEach(field => {
        if (lessonForm[field] === '') {
            lessonForm[field] = null;
        }
    });
    
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
    
    // Ensure order is integer or null
    if (lessonForm.order === '' || lessonForm.order === null) {
        lessonForm.order = null;
    } else {
        lessonForm.order = parseInt(lessonForm.order) || null;
    }
    
    // Ensure duration_minutes is integer (default to 0, not null, as DB doesn't allow null)
    if (lessonForm.duration_minutes === '' || lessonForm.duration_minutes === null || lessonForm.duration_minutes === undefined) {
        lessonForm.duration_minutes = 0;
    } else {
        const parsed = parseInt(lessonForm.duration_minutes);
        lessonForm.duration_minutes = isNaN(parsed) ? 0 : parsed;
    }
    
    if (editingLesson.value) {
        // Update existing lesson
        lessonForm.put(route('admin.courses.lessons.update', [props.course.slug || props.course.id, editingLesson.value.id]), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                showSuccess(t('lessons.updated_successfully') || 'Lesson updated successfully!', t('common.success') || 'Success');
                closeLessonModal();
                router.reload({ only: ['lesson'] });
            },
            onError: (errors) => {
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
                }
            },
        });
    } else {
        // Create new lesson
        lessonForm.post(route('admin.courses.lessons.store', props.course.slug || props.course.id), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                showSuccess(t('lessons.created_successfully') || 'Lesson created successfully!', t('common.success') || 'Success');
                closeLessonModal();
                router.reload({ only: ['lesson'] });
            },
            onError: (errors) => {
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
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
