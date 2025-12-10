<template>
    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50" :dir="direction">
            <!-- Hero Section -->
            <div class="relative bg-gradient-to-r from-slate-700 to-slate-800 py-16 overflow-hidden">
                <div class="absolute inset-0 opacity-10 bg-pattern"></div>
                <div class="max-w-7xl mx-auto px-4 relative z-10">
                    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-3xl">
                        <nav class="text-sm text-gray-500 mb-4">
                            <Link :href="route('courses.show', course.slug || course.id)" class="hover:text-blue-600">
                                {{ course.translated_title || course.title }}
                            </Link>
                            <span class="mx-2">›</span>
                            <span class="text-gray-900">{{ lesson.title }}</span>
                        </nav>
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ lesson.title }}</h1>
                                <p v-if="lesson.description" class="text-gray-600">{{ lesson.description }}</p>
                            </div>
                            <div v-if="canCreateQuestions" class="ml-4 flex gap-2">
                                <Link
                                    :href="route('instructor.lessons.questions.create', [course.slug || course.id, lesson.id])"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors flex items-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    {{ t('lessons.actions.add_question') }}
                                </Link>
                           
                            </div>
                        </div>
                        <div class="flex items-center gap-4 mt-4 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ lesson.duration_minutes || 0 }} {{ t('lessons.fields.duration_minutes') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ lesson.questions?.length || 0 }} {{ t('lessons.questions') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto py-8 px-4">
                <!-- Content Section -->
                <div v-if="lesson.content" class="bg-white rounded-xl shadow-sm p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-blue-600 rounded-full"></span>
                        {{ t('lessons.fields.content') }}
                    </h2>
                    <div class="prose prose-lg max-w-none text-gray-700" v-html="formatContent(lesson.content)"></div>
                </div>

                <!-- Questions Section -->
                <div class="bg-white rounded-xl shadow-sm p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                            <span class="w-1 h-6 bg-blue-600 rounded-full"></span>
                            {{ t('lessons.questions') }} ({{ lesson.questions?.length || 0 }})
                        </h2>
                        <Link
                            v-if="canCreateQuestions && can('questions.create')"
                            :href="route('instructor.lessons.questions.create', [course.slug || course.id, lesson.id])"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ t('lessons.actions.add_question') }}
                        </Link>
                    </div>

                    <div v-if="lesson.questions && lesson.questions.length > 0" class="space-y-6">
                        <div
                            v-for="(question, index) in lesson.questions"
                            :key="question.id"
                            class="border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow"
                        >
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                            {{ t('lessons.question') }} {{ index + 1 }}
                                        </span>
                                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs capitalize">
                                            {{ question.type ? (t('lessons.types.' + question.type) || question.type) : '' }}
                                        </span>
                                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs">
                                            {{ question.points }} {{ t('lessons.points') }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ question.question }}</h3>
                                    <p v-if="question.explanation" class="text-sm text-gray-600 italic">
                                        {{ t('lessons.explanation') }}: {{ question.explanation }}
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <button
                                        v-if="can('questions.delete')"
                                        @click="deleteQuestion(question.id)"
                                        class="px-3 py-1 text-red-600 hover:bg-red-50 rounded text-sm font-medium transition-colors"
                                    >
                                        {{ t('common.delete') }}
                                    </button>
                                </div>
                            </div>

                            <!-- Answers -->
                            <div v-if="question.answers && question.answers.length > 0" class="mt-4 pt-4 border-t border-gray-200">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">{{ t('lessons.questions') }}:</h4>
                                <div class="space-y-2">
                                    <div
                                        v-for="answer in question.answers"
                                        :key="answer.id"
                                        class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg"
                                        :class="answer.is_correct ? 'border-2 border-blue-500' : ''"
                                    >
                                        <div class="flex-shrink-0">
                                            <div class="w-5 h-5 border-2 rounded-full flex items-center justify-center"
                                                :class="answer.is_correct ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                                <svg v-if="answer.is_correct" class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ answer.answer }}</p>
                                            <p v-if="answer.answer_ar" class="text-xs text-gray-500 mt-1">{{ answer.answer_ar }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- User Answers -->
                            <div v-if="question.user_answers && question.user_answers.length > 0" class="mt-6 pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-sm font-semibold text-gray-700">
                                        {{ t('courses.students') }} {{ t('lessons.questions') }} ({{ question.user_answers_count }})
                                    </h4>
                                </div>
                                <div class="space-y-4">
                                    <div
                                        v-for="userAnswer in question.user_answers"
                                        :key="userAnswer.id"
                                        class="bg-gradient-to-r from-gray-50 to-white border-2 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow"
                                        :class="userAnswer.is_correct ? 'border-green-200' : 'border-red-200'"
                                    >
                                        <!-- Student Info Header -->
                                        <div class="flex items-start justify-between mb-4 pb-4 border-b border-gray-200">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-md">
                                                    {{ getInitials(userAnswer.user.name) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-gray-900">{{ userAnswer.user.name }}</p>
                                                    <p class="text-xs text-gray-500">{{ userAnswer.user.email }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span class="px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm"
                                                    :class="userAnswer.is_correct ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'"
                                                >
                                                    {{ userAnswer.is_correct ? t('lessons.correct') : t('lessons.incorrect') || 'Incorrect' }}
                                                </span>
                                                <p class="text-xs text-gray-600 mt-2 font-medium">
                                                    {{ userAnswer.points_earned }} / {{ question.points }} {{ t('lessons.points') }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Question Display -->
                                        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                            <p class="text-xs font-bold text-blue-900 mb-2 uppercase tracking-wide">
                                                {{ t('lessons.question') }}
                                            </p>
                                            <p class="text-sm font-semibold text-gray-900">{{ question.translated_question || question.question }}</p>
                                        </div>

                                        <!-- Correct Answer Display -->
                                        <div v-if="question.type === 'multiple_choice' || question.type === 'true_false'" class="mb-4 p-4 bg-green-50 border-2 border-green-300 rounded-lg">
                                            <p class="text-xs font-bold text-green-900 mb-2 uppercase tracking-wide">
                                                {{ t('lessons.correct_answer') }}
                                            </p>
                                            <div class="space-y-2">
                                                <template v-for="answer in question.answers" :key="answer.id">
                                                    <div
                                                        v-if="answer.is_correct"
                                                        class="flex items-center gap-2"
                                                    >
                                                        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                        <p class="text-sm font-semibold text-gray-900">{{ answer.translated_answer || answer.answer }}</p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Selected Answer Display -->
                                        <div class="mb-4 p-4 rounded-lg border-2"
                                            :class="userAnswer.is_correct ? 'bg-green-50 border-green-300' : 'bg-red-50 border-red-300'"
                                        >
                                            <p class="text-xs font-bold mb-2 uppercase tracking-wide"
                                                :class="userAnswer.is_correct ? 'text-green-900' : 'text-red-900'"
                                            >
                                                {{ t('lessons.selected_answer') }}
                                            </p>
                                            <div v-if="userAnswer.selected_answer" class="flex items-center gap-2">
                                                <svg v-if="userAnswer.is_correct" class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                <svg v-else class="w-5 h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                                <p class="text-sm font-semibold text-gray-900">{{ userAnswer.selected_answer.answer }}</p>
                                            </div>
                                            <div v-else-if="userAnswer.answer_text" class="flex items-start gap-2">
                                                <svg v-if="userAnswer.is_correct" class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                <svg v-else class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                                <p class="text-sm font-semibold text-gray-900">{{ userAnswer.answer_text }}</p>
                                            </div>
                                        </div>

                                        <!-- Submission Date -->
                                        <div class="pt-3 border-t border-gray-200">
                                            <p class="text-xs text-gray-500">
                                                <span class="font-medium">{{ t('lessons.submitted_at') }}:</span>
                                                {{ formatDate(userAnswer.submitted_at) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else-if="question.user_answers_count === 0" class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-sm text-gray-500 text-center py-4">
                                    {{ t('lessons.no_student_answers') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-12 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-lg mb-4">{{ t('lessons.no_questions') }}</p>
                        <Link
                            v-if="canCreateQuestions && can('questions.create')"
                            :href="route('instructor.lessons.questions.create', [course.slug || course.id, lesson.id])"
                            class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors"
                        >
                            {{ t('lessons.actions.add_question') }}
                        </Link>
                    </div>
                </div>

                <!-- Attendance Section -->
                <div v-if="can('attendance.mark')" class="bg-white rounded-xl shadow-sm p-8 mt-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                            <span class="w-1 h-6 bg-blue-600 rounded-full"></span>
                            {{ t('lessons.attendance') || 'Attendance' }} ({{ students?.length || 0 }})
                        </h2>
                    </div>

                    <!-- Attendance Stats -->
                    <div v-if="attendanceStats" class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                            <div class="text-2xl font-bold text-blue-700">{{ attendanceStats.total }}</div>
                            <div class="text-xs text-blue-600 font-medium">{{ t('lessons.total_students') || 'Total Students' }}</div>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
                            <div class="text-2xl font-bold text-green-700">{{ attendanceStats.present }}</div>
                            <div class="text-xs text-green-600 font-medium">{{ t('lessons.present') || 'Present' }}</div>
                        </div>
                        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4 border border-red-200">
                            <div class="text-2xl font-bold text-red-700">{{ attendanceStats.absent }}</div>
                            <div class="text-xs text-red-600 font-medium">{{ t('lessons.absent') || 'Absent' }}</div>
                        </div>
                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-4 border border-yellow-200">
                            <div class="text-2xl font-bold text-yellow-700">{{ attendanceStats.late }}</div>
                            <div class="text-xs text-yellow-600 font-medium">{{ t('lessons.late') || 'Late' }}</div>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200">
                            <div class="text-2xl font-bold text-purple-700">{{ attendanceStats.excused }}</div>
                            <div class="text-xs text-purple-600 font-medium">{{ t('lessons.excused') || 'Excused' }}</div>
                        </div>
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200">
                            <div class="text-2xl font-bold text-gray-700">{{ attendanceStats.attendance_rate }}%</div>
                            <div class="text-xs text-gray-600 font-medium">{{ t('lessons.attendance_rate') || 'Attendance Rate' }}</div>
                        </div>
                    </div>

                    <!-- Batch Filter -->
                    <div v-if="batches && batches.length > 1" class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('lessons.filter_by_batch') || 'Filter by Batch' }}</label>
                        <select v-model="selectedBatch" class="block w-full md:w-64 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                            <option value="">{{ t('lessons.all_batches') || 'All Batches' }}</option>
                            <option v-for="batch in batches" :key="batch.id" :value="batch.id">
                                {{ batch.name }} ({{ batch.students_count }} {{ t('courses.students') }})
                            </option>
                        </select>
                    </div>

                    <div v-if="filteredStudents && filteredStudents.length > 0" class="space-y-6">
                        <!-- Info Banner -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ t('lessons.attendance_note') || 'Mark attendance for each student. Attendance is separate from course progress.' }}
                            </p>
                        </div>
                        
                        <form @submit.prevent="markAttendance" class="space-y-4">
                            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                        <tr>
                                            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                {{ t('courses.students') || 'Student' }}
                                            </th>
                                            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                {{ t('lessons.batch') || 'Batch' }}
                                            </th>
                                            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                {{ t('lessons.progress') || 'Progress' }}
                                            </th>
                                            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                {{ t('lessons.current_status') || 'Current Status' }}
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
                                        <tr v-for="student in filteredStudents" :key="`${student.id}-${student.batch_id}`" class="hover:bg-gray-50 transition-colors">
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
                                                <div class="flex items-center gap-2">
                                                    <div class="w-20 bg-gray-200 rounded-full h-2 overflow-hidden">
                                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" :style="{ width: `${student.enrollment_progress || 0}%` }"></div>
                                                    </div>
                                                    <span class="text-xs font-medium text-gray-700 min-w-[2.5rem]">{{ student.enrollment_progress || 0 }}%</span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <span v-if="student.attendance" class="px-2 py-1 rounded-full text-xs font-medium"
                                                        :class="{
                                                            'bg-green-100 text-green-700': student.attendance.status === 'present',
                                                            'bg-red-100 text-red-700': student.attendance.status === 'absent',
                                                            'bg-yellow-100 text-yellow-700': student.attendance.status === 'late',
                                                            'bg-blue-100 text-blue-700': student.attendance.status === 'excused',
                                                        }"
                                                    >
                                                        {{ t('lessons.' + student.attendance.status) || student.attendance.status }}
                                                    </span>
                                                    <span v-else class="px-2 py-1 bg-gray-100 text-gray-500 rounded-full text-xs">
                                                        {{ t('lessons.not_marked') || 'Not Marked' }}
                                                    </span>
                                                    <span v-if="student.attendance?.marked_by === 'student'" class="px-2 py-0.5 bg-purple-100 text-purple-600 rounded text-xs">
                                                        {{ t('lessons.auto') || 'Auto' }}
                                                    </span>
                                                    <span v-if="student.completed" class="text-green-600">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <select 
                                                    v-model="attendanceForm[`${student.id}-${student.batch_id}`].status"
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
                                                    v-model="attendanceForm[`${student.id}-${student.batch_id}`].notes"
                                                    :placeholder="t('lessons.notes_placeholder') || 'Optional notes...'"
                                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 text-sm transition-all"
                                                />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="isSubmittingAttendance"
                                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 shadow-lg hover:shadow-xl"
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
                    <div v-else class="text-center py-12 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p class="text-lg mb-4">{{ t('lessons.no_students') || 'No students enrolled' }}</p>
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
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { onMounted, ref, reactive, computed, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useDirection } from '@/composables/useDirection';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useAlert } from '@/composables/useAlert';
import { usePermissions } from '@/composables/usePermissions';
import { Link, usePage, router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    lesson: Object,
    batches: Array,
    students: Array,
    attendances: Array,
    attendanceStats: Object,
    userAnswers: Object,
});

const { direction } = useDirection();
const { t } = useTranslation();
const { route } = useRoute();
const { showSuccess, showError, showConfirm } = useAlert();
const { can } = usePermissions();
const page = usePage();

const canCreateQuestions = computed(() => {
    return page.props.settings?.instructor_permissions?.can_create_questions ?? true;
});

// Initialize attendance form
const attendanceForm = reactive({});
const isSubmittingAttendance = ref(false);
const selectedBatch = ref('');

// Filter students by selected batch
const filteredStudents = computed(() => {
    if (!props.students) return [];
    if (!selectedBatch.value) return props.students;
    return props.students.filter(s => s.batch_id === selectedBatch.value);
});

// Initialize attendance form data early so template bindings are always defined
const initializeAttendanceForm = () => {
    if (!props.students) return;
    
    // Clear existing entries to avoid stale data when props change
    Object.keys(attendanceForm).forEach(key => delete attendanceForm[key]);

    props.students.forEach(student => {
        const key = `${student.id}-${student.batch_id}`;
        attendanceForm[key] = {
            student_id: student.id,
            batch_id: student.batch_id,
            status: student.attendance?.status || 'present',
            notes: student.attendance?.notes || '',
            attended_at: student.attendance?.attended_at || null,
        };
    });
};

watch(() => props.students, initializeAttendanceForm, { immediate: true });

// Show success alert if question was created
onMounted(() => {
    if (page.props.flash?.success) {
        showSuccess(
            page.props.flash.success,
            t('common.success') || 'Success'
        );
    }
});

const formatContent = (content) => {
    if (!content) return '';
    return content.replace(/\n/g, '<br>');
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleString();
};

const getInitials = (name) => {
    if (!name) return '';
    const parts = name.split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
};

const markAttendance = async () => {
    isSubmittingAttendance.value = true;
    
    // Only submit filtered students if a batch is selected
    const studentsToSubmit = selectedBatch.value 
        ? filteredStudents.value 
        : props.students;
    
    const attendances = studentsToSubmit.map(student => {
        const key = `${student.id}-${student.batch_id}`;
        const form = attendanceForm[key];
        return {
            student_id: form.student_id,
            batch_id: form.batch_id,
            status: form.status,
            notes: form.notes || null,
            attended_at: form.attended_at || new Date().toISOString(),
        };
    });

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
            // Reload to refresh attendance data and student progress
            router.reload({ only: ['students', 'attendances', 'attendanceStats'] });
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

const deleteQuestion = async (questionId) => {
    const result = await showConfirm(
        t('common.confirm_delete') || 'Are you sure you want to delete this question?',
        t('common.delete') || 'Delete'
    );

    if (result.isConfirmed) {
        router.delete(route('instructor.lessons.questions.destroy', {
            course: props.course.slug || props.course.id,
            lesson: props.lesson.id,
            question: questionId,
        }), {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess(
                    t('lessons.deleted_successfully') || 'Question deleted successfully!',
                    t('common.success') || 'Success'
                );
            },
            onError: () => {
                showError(
                    t('common.error') || 'Failed to delete question',
                    t('common.error') || 'Error'
                );
            },
        });
    }
};
</script>

