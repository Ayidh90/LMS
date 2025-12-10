<template>
    <AdminLayout :page-title="lesson.title">
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
                        <h1 class="text-2xl font-bold text-gray-900">{{ lesson.title }}</h1>
                        <p class="text-sm text-gray-500">{{ course.title }}</p>
                    </div>
                    <Link
                        :href="route('admin.courses.lessons.edit', [course.slug || course.id, lesson.id])"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 text-gray-700 font-medium transition-all"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {{ t('common.edit') }}
                    </Link>
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
                            {{ t('lessons.details') || 'Details' }}
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
                            {{ t('attendance.title') || 'Attendance' }}
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
                            {{ t('questions.title') || 'Questions' }}
                            <span v-if="lesson.questions?.length" class="px-2 py-0.5 bg-gray-100 rounded-full text-xs">
                                {{ lesson.questions.length }}
                            </span>
                        </button>
                    </nav>
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
                                        <p class="font-semibold text-gray-900">{{ lesson.section?.title || '-' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">{{ t('questions.title') || 'Questions' }}</span>
                                        <p class="font-semibold text-gray-900">{{ lesson.questions?.length || 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-if="lesson.description" class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">{{ t('lessons.fields.description') || 'Description' }}</h4>
                        <p class="text-gray-600">{{ lesson.description }}</p>
                    </div>
                    
                    <div v-if="lesson.video_url" class="px-6 py-4 border-t border-gray-100">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">{{ t('lessons.fields.video_url') || 'Video URL' }}</h4>
                        <a :href="lesson.video_url" target="_blank" class="text-blue-600 hover:text-blue-700 break-all">{{ lesson.video_url }}</a>
                    </div>
                </div>
            </div>

            <!-- Attendance Tab -->
            <div v-if="activeTab === 'attendance'" class="space-y-6">
                <!-- Attendance Stats -->
                <div v-if="attendanceStats" class="grid grid-cols-2 md:grid-cols-6 gap-4">
                    <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ attendanceStats.total }}</div>
                        <div class="text-sm text-gray-500">{{ t('attendance.total') || 'Total' }}</div>
                    </div>
                    <div class="bg-emerald-50 rounded-xl border border-emerald-100 p-4 text-center">
                        <div class="text-2xl font-bold text-emerald-600">{{ attendanceStats.present }}</div>
                        <div class="text-sm text-emerald-600">{{ t('attendance.present') || 'Present' }}</div>
                    </div>
                    <div class="bg-amber-50 rounded-xl border border-amber-100 p-4 text-center">
                        <div class="text-2xl font-bold text-amber-600">{{ attendanceStats.late }}</div>
                        <div class="text-sm text-amber-600">{{ t('attendance.late') || 'Late' }}</div>
                    </div>
                    <div class="bg-red-50 rounded-xl border border-red-100 p-4 text-center">
                        <div class="text-2xl font-bold text-red-600">{{ attendanceStats.absent }}</div>
                        <div class="text-sm text-red-600">{{ t('attendance.absent') || 'Absent' }}</div>
                    </div>
                    <div class="bg-blue-50 rounded-xl border border-blue-100 p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ attendanceStats.excused }}</div>
                        <div class="text-sm text-blue-600">{{ t('attendance.excused') || 'Excused' }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 text-center">
                        <div class="text-2xl font-bold text-gray-600">{{ attendanceStats.not_marked }}</div>
                        <div class="text-sm text-gray-500">{{ t('attendance.not_marked') || 'Not Marked' }}</div>
                    </div>
                </div>

                <!-- Filter by Batch -->
                <div class="flex items-center gap-4">
                    <select
                        v-model="selectedBatch"
                        class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white min-w-[200px]"
                    >
                        <option value="">{{ t('attendance.all_batches') || 'All Batches' }}</option>
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
                        {{ t('attendance.save') || 'Save Attendance' }}
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
                                        {{ t('attendance.batch') || 'Batch' }}
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ t('attendance.status') || 'Status' }}
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ t('attendance.marked_by') || 'Marked By' }}
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ t('attendance.progress') || 'Progress' }}
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ t('attendance.completed') || 'Completed' }}
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
                                            <option value="">{{ t('attendance.not_marked') || 'Not Marked' }}</option>
                                            <option value="present">{{ t('attendance.present') || 'Present' }}</option>
                                            <option value="late">{{ t('attendance.late') || 'Late' }}</option>
                                            <option value="absent">{{ t('attendance.absent') || 'Absent' }}</option>
                                            <option value="excused">{{ t('attendance.excused') || 'Excused' }}</option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="student.attendance" class="inline-flex items-center gap-1 text-sm">
                                            <span v-if="student.attendance.marked_by === 'student'" class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs">
                                                {{ t('attendance.auto') || 'Auto' }}
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
                                        {{ t('attendance.no_students') || 'No students found' }}
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
                        <h3 class="text-lg font-semibold text-gray-900">{{ t('questions.title') || 'Questions' }}</h3>
                        <Link
                            :href="route('admin.courses.lessons.questions.create', [course.slug || course.id, lesson.id])"
                            class="inline-flex items-center gap-2 px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-xl font-medium transition-all"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            {{ t('admin.add_question') || 'Add Question' }}
                        </Link>
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
                                    <p class="text-gray-900">{{ question.question }}</p>
                                </div>
                                <Link
                                    :href="route('admin.courses.lessons.questions.edit', [course.slug || course.id, lesson.id, question.id])"
                                    class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="p-12 text-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-gray-500">{{ t('questions.no_questions') || 'No questions yet' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    lesson: Object,
    batches: Array,
    students: Array,
    attendances: Array,
    attendanceStats: Object,
});

const { t } = useTranslation();
const { route } = useRoute();

const activeTab = ref('details');
const selectedBatch = ref('');
const saving = ref(false);
const attendanceChanges = ref({});
const changedStudents = ref(new Set());

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
    };
    return icons[type] || icons.text;
};

const formatLessonType = (type) => {
    const types = {
        text: t('lessons.types.text') || 'Text',
        youtube_video: t('lessons.types.youtube') || 'YouTube',
        google_drive_video: t('lessons.types.drive') || 'Google Drive',
        video_file: t('lessons.types.video') || 'Video',
        image: t('lessons.types.image') || 'Image',
        document_file: t('lessons.types.document') || 'Document',
        embed_frame: t('lessons.types.embed') || 'Embed',
        assignment: t('lessons.types.assignment') || 'Assignment',
        test: t('lessons.types.test') || 'Test',
    };
    return types[type] || type;
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
