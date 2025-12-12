<template>
    <AdminLayout :page-title="t('admin.lessons_management') || 'Lessons Management'">
        <Head :title="t('admin.lessons_management') || 'Lessons Management'" />
        
        <div class="container-fluid px-3 px-md-4 px-lg-5 py-4 lessons-index-page">
            <!-- Page Header - Bootstrap Style -->
            <div class="card border-0 shadow-sm mb-3 header-card">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <Link :href="route('admin.courses.show', course.slug || course.id)" class="text-muted text-decoration-none back-link">
                                <i class="bi bi-arrow-left fs-5"></i>
                            </Link>
                            <div>
                                <h1 class="h4 fw-bold mb-1 page-title">{{ t('admin.lessons_management') || 'Lessons Management' }}</h1>
                                <p class="text-muted small mb-0 course-subtitle">{{ course.translated_title || course.title }}</p>
                            </div>
                        </div>
                        <button
                            @click="openLessonModal()"
                            class="btn btn-primary add-lesson-btn d-flex align-items-center gap-2"
                        >
                            <i class="bi bi-plus-circle"></i>
                            {{ t('admin.add_lesson') || 'Add Lesson' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filter by Section - Bootstrap Style -->
            <div class="card border-0 shadow-sm mb-3 filter-card">
                <div class="card-body p-3">
                    <div class="row align-items-end">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold mb-2 small text-uppercase text-muted">{{ t('lessons.fields.section') || 'Section' }}</label>
                            <select
                                v-model="selectedSection"
                                @change="filterBySection"
                                class="form-select filter-select"
                            >
                                <option value="">{{ t('lessons.all_sections') || 'All Sections' }}</option>
                                <option v-for="section in sections" :key="section.id" :value="section.id">
                                    {{ section.translated_title || section.title }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lessons Table - Bootstrap Style -->
            <div class="card border-0 shadow-sm table-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 lessons-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 60px;">#</th>
                                    <th>{{ t('lessons.fields.title') || 'Title' }}</th>
                                    <th>{{ t('lessons.fields.type') || 'Type' }}</th>
                                    <th>{{ t('lessons.fields.section') || 'Section' }}</th>
                                    <th>{{ t('lessons.fields.duration') || t('courses.fields.duration') || 'Duration' }}</th>
                                    <th class="text-center">{{ t('lessons.questions') || 'Questions' }}</th>
                                    <th class="text-center">{{ t('lessons.attendance') || t('attendance.title') || 'Attendance' }}</th>
                                    <th class="text-center" style="width: 180px;">{{ t('common.actions') || 'Actions' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="lesson in filteredLessons" :key="lesson.id" class="lesson-row">
                                    <td class="text-center text-muted fw-medium">{{ lesson.order }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div 
                                                :class="getLessonTypeIcon(lesson.type).bg" 
                                                class="lesson-type-icon rounded d-flex align-items-center justify-content-center"
                                                :style="getLessonTypeIcon(lesson.type).style || ''"
                                            >
                                                <i :class="getLessonTypeIcon(lesson.type).icon" :style="{ color: getLessonTypeIcon(lesson.type).color, fontSize: '1rem' }"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold lesson-title">{{ lesson.translated_title || lesson.title }}</div>
                                                <span v-if="lesson.is_free" class="badge bg-success text-white mt-1">{{ t('lessons.free') || 'Free' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge lesson-type-badge">{{ formatLessonType(lesson.type) }}</span>
                                    </td>
                                    <td class="text-muted">
                                        {{ lesson.section?.translated_title || lesson.section?.title || '-' }}
                                    </td>
                                    <td class="text-muted duration-cell">
                                        <span v-if="lesson.duration_minutes" class="duration-value">
                                            {{ lesson.duration_minutes }} {{ t('common.minutes') || 'min' }}
                                        </span>
                                        <span v-else class="text-muted">-</span>
                                    </td>
                                    <td class="text-center questions-cell">
                                        <Link
                                            v-if="lesson.questions_count > 0"
                                            :href="route('admin.courses.lessons.questions.index', [course.slug || course.id, lesson.id])"
                                            class="text-decoration-none questions-link"
                                            :title="t('lessons.questions') || 'Questions'"
                                        >
                                            <span class="badge bg-info text-white questions-badge">{{ lesson.questions_count }}</span>
                                        </Link>
                                        <span v-else class="text-muted questions-empty">{{ t('common.no') || '0' }}</span>
                                    </td>
                                    <td class="text-center attendance-cell">
                                        <Link
                                            :href="route('admin.courses.lessons.show', [course.slug || course.id, lesson.id])"
                                            class="btn btn-sm btn-outline-success attendance-btn"
                                            :title="t('lessons.attendance') || t('attendance.title') || 'Attendance'"
                                        >
                                            <i class="bi bi-clipboard-check me-1"></i>
                                            <span>{{ t('lessons.mark_attendance') || t('attendance.view') || t('common.view') || 'View' }}</span>
                                        </Link>
                                    </td>
                                    <td class="actions-cell">
                                        <div class="action-buttons-wrapper">
                                            <div class="d-flex flex-column flex-md-row justify-content-center gap-2 action-buttons">
                                                <Link
                                                    :href="route('admin.courses.lessons.show', [course.slug || course.id, lesson.id])"
                                                    class="btn btn-sm btn-outline-primary action-btn action-btn-view"
                                                    :title="t('common.view') || t('lessons.actions.view') || 'View'"
                                                >
                                                    <i class="bi bi-eye"></i>
                                                    <span class="action-btn-text">{{ t('common.view') || t('lessons.actions.view') || 'View' }}</span>
                                                </Link>
                                                <button
                                                    @click="openLessonModal(lesson)"
                                                    type="button"
                                                    class="btn btn-sm btn-outline-warning action-btn action-btn-edit"
                                                    :title="t('common.edit') || t('lessons.actions.edit') || 'Edit'"
                                                >
                                                    <i class="bi bi-pencil"></i>
                                                    <span class="action-btn-text">{{ t('common.edit') || t('lessons.actions.edit') || 'Edit' }}</span>
                                                </button>
                                                <Link
                                                    :href="route('admin.courses.lessons.questions.index', [course.slug || course.id, lesson.id])"
                                                    class="btn btn-sm btn-outline-info action-btn action-btn-questions"
                                                    :title="t('admin.manage_questions') || t('lessons.actions.add_question') || 'Manage Questions'"
                                                >
                                                    <i class="bi bi-question-circle"></i>
                                                    <span class="action-btn-text">{{ t('lessons.questions') || 'Questions' }}</span>
                                                </Link>
                                                <button
                                                    @click="confirmDelete(lesson)"
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger action-btn action-btn-delete"
                                                    :title="t('common.delete') || t('lessons.actions.delete') || 'Delete'"
                                                >
                                                    <i class="bi bi-trash"></i>
                                                    <span class="action-btn-text">{{ t('common.delete') || t('lessons.actions.delete') || 'Delete' }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredLessons.length === 0">
                                    <td colspan="8" class="text-center py-5 empty-state">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-book empty-state-icon"></i>
                                            <p class="text-muted fw-medium mb-0 mt-3">{{ t('lessons.no_lessons') || 'No lessons found' }}</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

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
import { ref, computed, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useAlert } from '@/composables/useAlert';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import LessonForm from '@/Pages/Admin/Lessons/Form.vue';

const props = defineProps({
    course: Object,
    lessons: Array,
    sections: Array,
    lessonTypes: Array,
});

const { t } = useTranslation();
const { route } = useRoute();
const { showSuccess, showError } = useAlert();

const selectedSection = ref('');

// Modal state
const showLessonModal = ref(false);
const editingLesson = ref(null);

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

const filteredLessons = computed(() => {
    if (!selectedSection.value) return props.lessons;
    return props.lessons.filter(l => l.section?.id === parseInt(selectedSection.value));
});

const filterBySection = () => {
    // Filter is handled by computed property
};

const getLessonTypeIcon = (type) => {
    const icons = {
        text: { 
            bg: 'bg-primary bg-opacity-10', 
            icon: 'bi bi-file-text', 
            color: '#0d6efd',
            style: ''
        },
        youtube_video: { 
            bg: 'bg-danger bg-opacity-10', 
            icon: 'bi bi-youtube', 
            color: '#dc3545',
            style: ''
        },
        google_drive_video: { 
            bg: 'bg-warning bg-opacity-10', 
            icon: 'bi bi-google', 
            color: '#ffc107',
            style: ''
        },
        video_file: { 
            bg: '', 
            icon: 'bi bi-play-circle', 
            color: '#6f42c1',
            style: 'background-color: rgba(111, 66, 193, 0.1);'
        },
        image: { 
            bg: 'bg-success bg-opacity-10', 
            icon: 'bi bi-image', 
            color: '#198754',
            style: ''
        },
        document_file: { 
            bg: 'bg-warning bg-opacity-10', 
            icon: 'bi bi-file-earmark', 
            color: '#ffc107',
            style: ''
        },
        embed_frame: { 
            bg: 'bg-info bg-opacity-10', 
            icon: 'bi bi-code-square', 
            color: '#0dcaf0',
            style: ''
        },
        assignment: { 
            bg: 'bg-primary bg-opacity-10', 
            icon: 'bi bi-file-earmark-text', 
            color: '#0d6efd',
            style: ''
        },
        test: { 
            bg: 'bg-danger bg-opacity-10', 
            icon: 'bi bi-question-circle', 
            color: '#dc3545',
            style: ''
        },
    };
    return icons[type] || icons.text;
};

const formatLessonType = (type) => {
    // Use direct translation key lookup for proper localization
    return t(`lessons.types.${type}`) || type;
};

const confirmDelete = (lesson) => {
    if (confirm(t('lessons.confirm_delete') || 'Are you sure you want to delete this lesson?')) {
        router.delete(route('admin.courses.lessons.destroy', [props.course.slug || props.course.id, lesson.id]), {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess(t('lessons.deleted_successfully') || 'Lesson deleted successfully!', t('common.success') || 'Success');
            },
            onError: () => {
                showError(t('common.error') || 'Error', t('common.error') || 'Error');
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
            onSuccess: () => {
                showSuccess(t('lessons.updated_successfully') || 'Lesson updated successfully!', t('common.success') || 'Success');
                closeLessonModal();
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
            onSuccess: () => {
                showSuccess(t('lessons.created_successfully') || 'Lesson created successfully!', t('common.success') || 'Success');
                closeLessonModal();
            },
            onError: (errors) => {
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
                }
            },
        });
    }
};
</script>

<style scoped>
/* Page Container */
.lessons-index-page {
    background-color: #f8f9fa;
    min-height: calc(100vh - 80px);
}

.lessons-index-page .container-fluid {
    padding-top: 1rem;
    padding-bottom: 1rem;
}

/* Cards */
.card {
    border-radius: 0.625rem;
    border: 1px solid #e9ecef;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: #ffffff;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    transform: translateY(-1px);
}

.header-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: none;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
}

.header-card .card-body {
    padding: 0.875rem 1rem !important;
}

.filter-card {
    background: #ffffff;
    border: 1px solid #e9ecef;
}

.filter-card .card-body {
    padding: 0.75rem 1rem !important;
}

.table-card {
    overflow: hidden;
    border: 1px solid #e9ecef;
}

/* Header Styles */
.page-title {
    color: #1a202c;
    font-size: 1.375rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    margin-bottom: 0.125rem;
}

.course-subtitle {
    color: #64748b;
    font-size: 0.8125rem;
    font-weight: 500;
}

.back-link {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 0.5rem;
    border-radius: 0.375rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
}

.back-link:hover {
    background-color: #f1f5f9;
    color: #0d6efd !important;
    transform: translateX(-3px);
}

[dir="rtl"] .back-link:hover {
    transform: translateX(3px);
}

.back-link i {
    font-size: 1.125rem;
}

/* Add Lesson Button */
.add-lesson-btn {
    background: linear-gradient(135deg, #0d6efd 0%, #6366f1 50%, #4f46e5 100%);
    border: none;
    padding: 0.5rem 1.25rem;
    font-weight: 600;
    border-radius: 0.5rem;
    box-shadow: 0 3px 10px rgba(13, 110, 253, 0.3);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 0.8125rem;
    letter-spacing: 0.01em;
}

.add-lesson-btn:hover {
    background: linear-gradient(135deg, #0b5ed7 0%, #4f46e5 50%, #4338ca 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(13, 110, 253, 0.45);
    color: #ffffff;
}

.add-lesson-btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
}

.add-lesson-btn i {
    font-size: 0.9375rem;
}

/* Filter Select */
.filter-select {
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
    padding: 0.5rem 0.75rem;
    transition: all 0.2s ease;
    font-size: 0.8125rem;
    background-color: #ffffff;
}

.filter-select:hover {
    border-color: #cbd5e1;
}

.filter-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    outline: none;
}

/* Table Styles */
.lessons-table {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
}

.lessons-table thead th {
    border-bottom: 2px solid #e2e8f0;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.6875rem;
    letter-spacing: 0.05em;
    padding: 0.75rem 1rem;
    color: #475569;
    background-color: #f8fafc;
    position: sticky;
    top: 0;
    z-index: 10;
}

.lessons-table thead th:first-child {
    border-top-left-radius: 0.875rem;
}

.lessons-table thead th:last-child {
    border-top-right-radius: 0.875rem;
}

.lessons-table tbody td {
    padding: 0.875rem 1rem;
    vertical-align: middle;
    border-top: 1px solid #f1f5f9;
    color: #334155;
    font-size: 0.8125rem;
}

.lessons-table tbody tr:last-child td {
    border-bottom: none;
}

.lesson-row {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: default;
}

.lesson-row:hover {
    background-color: #f8fafc;
    box-shadow: inset 4px 0 0 #0d6efd;
}

.lesson-row:hover td {
    color: #1e293b;
}

/* Lesson Type Icon */
.lesson-type-icon {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 0.5rem;
}

.lesson-row:hover .lesson-type-icon {
    transform: scale(1.05) rotate(2deg);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Lesson Title */
.lesson-title {
    color: #1e293b;
    font-size: 0.875rem;
    line-height: 1.4;
    font-weight: 600;
    margin-bottom: 0.125rem;
}

/* Duration Cell */
.duration-cell {
    min-width: 80px;
}

.duration-value {
    font-weight: 500;
    color: #475569;
    font-size: 0.75rem;
}

/* Badges */
.lesson-type-badge {
    background: linear-gradient(135deg, #64748b 0%, #475569 100%);
    color: #ffffff;
    font-weight: 500;
    padding: 0.35em 0.75em;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    letter-spacing: 0.01em;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Questions Cell */
.questions-cell {
    min-width: 80px;
}

.questions-link {
    display: inline-block;
    transition: all 0.2s ease;
}

.questions-badge {
    font-weight: 500;
    padding: 0.35em 0.7em;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    box-shadow: 0 1px 3px rgba(13, 202, 240, 0.2);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-block;
    cursor: pointer;
}

.questions-link:hover .questions-badge {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 4px 12px rgba(13, 202, 240, 0.4);
    background-color: #0dcaf0 !important;
}

.questions-empty {
    font-size: 0.75rem;
    color: #94a3b8;
}

/* Action Buttons Container */
.actions-cell {
    min-width: 140px;
    width: 140px;
    max-width: 140px;
}

.action-buttons-wrapper {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.action-buttons {
    flex-wrap: wrap;
    gap: 0.375rem;
    justify-content: center;
    align-items: stretch;
    width: 100%;
}

/* Base Action Button Styles */
.action-btn {
    min-width: 100%;
    width: 100%;
    height: 32px;
    padding: 0.375rem 0.625rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.375rem;
    border-radius: 0.375rem;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    border-width: 1px;
    font-size: 0.75rem;
    font-weight: 500;
    position: relative;
    overflow: hidden;
    white-space: nowrap;
    flex-shrink: 0;
}

.action-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    transform: translate(-50%, -50%);
    transition: width 0.4s ease, height 0.4s ease;
}

.action-btn:hover::before {
    width: 300px;
    height: 300px;
}

.action-btn i {
    font-size: 0.875rem;
    position: relative;
    z-index: 1;
}

.action-btn-text {
    position: relative;
    z-index: 1;
    white-space: nowrap;
    font-size: 0.75rem;
    font-weight: 500;
}

/* Hover Effects */
.action-btn:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border-width: 1.5px;
}

.action-btn:active {
    transform: translateY(0) scale(0.98);
}

/* View Button */
.action-btn-view {
    color: #0d6efd;
    border-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.05);
}

.action-btn-view:hover {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(13, 110, 253, 0.35);
}

/* Edit Button */
.action-btn-edit {
    color: #ffc107;
    border-color: #ffc107;
    background-color: rgba(255, 193, 7, 0.05);
}

.action-btn-edit:hover {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000000;
    box-shadow: 0 4px 14px rgba(255, 193, 7, 0.35);
}

/* Questions Button */
.action-btn-questions {
    color: #0dcaf0;
    border-color: #0dcaf0;
    background-color: rgba(13, 202, 240, 0.05);
}

.action-btn-questions:hover {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: #000000;
    box-shadow: 0 4px 14px rgba(13, 202, 240, 0.35);
}

/* Delete Button */
.action-btn-delete {
    color: #dc3545;
    border-color: #dc3545;
    background-color: rgba(220, 53, 69, 0.05);
}

.action-btn-delete:hover {
    background-color: #dc3545;
    border-color: #dc3545;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(220, 53, 69, 0.35);
}

/* Action Button Loading/Disabled States */
.action-btn:disabled,
.action-btn[disabled] {
    opacity: 0.6;
    cursor: not-allowed;
    pointer-events: none;
}

.action-btn.loading {
    position: relative;
    color: transparent !important;
    pointer-events: none;
}

.action-btn.loading::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid currentColor;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Action Button Group Spacing */
.action-buttons .action-btn:not(:last-child) {
    margin-bottom: 0;
}

/* Ensure buttons don't shrink */
.action-btn {
    flex-shrink: 0;
}

/* Attendance Button */
.attendance-cell {
    min-width: 120px;
}

.attendance-btn {
    border-radius: 0.375rem;
    font-weight: 500;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
    border-width: 1px;
    color: #198754;
    border-color: #198754;
    background-color: rgba(25, 135, 84, 0.05);
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    position: relative;
    overflow: hidden;
}

.attendance-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    transform: translate(-50%, -50%);
    transition: width 0.4s ease, height 0.4s ease;
}

.attendance-btn:hover::before {
    width: 300px;
    height: 300px;
}

.attendance-btn i {
    position: relative;
    z-index: 1;
}

.attendance-btn span {
    position: relative;
    z-index: 1;
}

.attendance-btn:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 4px 14px rgba(25, 135, 84, 0.35);
    background-color: #198754;
    border-color: #198754;
    color: #ffffff;
}

.attendance-btn:active {
    transform: translateY(0) scale(0.98);
}

/* Empty State */
.empty-state {
    padding: 5rem 2rem !important;
}

.empty-state-icon {
    font-size: 5rem;
    color: #cbd5e1;
    opacity: 0.6;
    margin-bottom: 1rem;
}

.empty-state p {
    font-size: 1.0625rem;
    color: #64748b;
    font-weight: 500;
}

/* Form Label */
.form-label {
    font-weight: 600;
    color: #475569;
    margin-bottom: 0.5rem;
}

/* Responsive */
@media (max-width: 991px) {
    .page-title {
        font-size: 1.375rem;
    }
    
    .add-lesson-btn {
        padding: 0.625rem 1.25rem;
        font-size: 0.875rem;
    }
}

@media (min-width: 768px) {
    .actions-cell {
        min-width: 160px;
        width: auto;
        max-width: none;
    }
    
    .action-buttons {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 0.25rem;
    }
    
    .action-btn {
        min-width: auto;
        width: auto;
        height: 30px;
        padding: 0.375rem 0.5rem;
        flex: 0 0 auto;
    }
    
    .action-btn-text {
        display: inline;
        font-size: 0.6875rem;
    }
    
    .action-btn i {
        font-size: 0.8125rem;
    }
}

@media (max-width: 991px) and (min-width: 768px) {
    .action-btn {
        min-width: 30px;
        width: 30px;
        height: 30px;
        padding: 0.375rem;
    }
    
    .action-btn-text {
        display: none;
    }
    
    .action-btn i {
        margin: 0;
        font-size: 0.8125rem;
    }
}

@media (max-width: 768px) {
    .page-title {
        font-size: 1rem;
    }
    
    .lessons-table thead th {
        padding: 0.625rem 0.5rem;
        font-size: 0.625rem;
    }
    
    .lessons-table tbody td {
        padding: 0.75rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .actions-cell {
        min-width: 120px;
        width: 120px;
        max-width: 120px;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 0.375rem;
    }
    
    .action-btn {
        width: 100%;
        min-width: 100%;
        height: 30px;
        font-size: 0.6875rem;
        padding: 0.375rem 0.625rem;
        justify-content: flex-start;
    }
    
    .action-btn-text {
        display: inline !important;
        flex: 1;
        text-align: start;
        font-size: 0.6875rem;
    }
    
    .action-btn i {
        flex-shrink: 0;
        width: 16px;
        text-align: center;
        font-size: 0.75rem;
    }
    
    .lesson-type-icon {
        width: 36px;
        height: 36px;
    }
    
    .lesson-title {
        font-size: 0.8125rem;
    }
    
    .empty-state {
        padding: 3rem 1rem !important;
    }
    
    .empty-state-icon {
        font-size: 3.5rem;
    }
    
    .attendance-btn {
        padding: 0.375rem 0.625rem;
        font-size: 0.6875rem;
    }
    
    .attendance-btn i {
        font-size: 0.75rem;
    }
}

@media (max-width: 576px) {
    .header-card .card-body {
        padding: 1.25rem !important;
    }
    
    .filter-card .card-body {
        padding: 1rem !important;
    }
    
    .lessons-table thead th,
    .lessons-table tbody td {
        padding: 0.75rem 0.5rem;
    }
    
    .actions-cell {
        min-width: 100px;
        width: 100px;
        max-width: 100px;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 0.25rem;
        width: 100%;
    }
    
    .action-btn {
        width: 100%;
        min-width: 100%;
        height: 28px;
        justify-content: flex-start;
        padding: 0.375rem 0.5rem;
        font-size: 0.6875rem;
    }
    
    .action-btn-text {
        display: inline !important;
        font-size: 0.6875rem;
    }
    
    .action-btn i {
        font-size: 0.75rem;
        width: 14px;
    }
    
    .attendance-cell {
        min-width: auto;
    }
    
    .attendance-btn {
        width: 100%;
        justify-content: center;
        padding: 0.375rem;
        font-size: 0.6875rem;
        height: 28px;
    }
    
    .attendance-btn span {
        display: none;
    }
    
    .attendance-btn i {
        font-size: 0.75rem;
    }
    
    .questions-cell {
        min-width: auto;
    }
    
    .duration-cell {
        min-width: auto;
    }
}

/* RTL Support */
[dir="rtl"] .bi-arrow-left::before {
    content: "\f138";
}

[dir="rtl"] .me-1 {
    margin-right: 0 !important;
    margin-left: 0.25rem !important;
}

[dir="rtl"] .me-2 {
    margin-right: 0 !important;
    margin-left: 0.5rem !important;
}

[dir="rtl"] .ms-1 {
    margin-left: 0 !important;
    margin-right: 0.25rem !important;
}

[dir="rtl"] .ms-2 {
    margin-left: 0 !important;
    margin-right: 0.5rem !important;
}

[dir="rtl"] .gap-2 {
    gap: 0.5rem !important;
}

[dir="rtl"] .gap-3 {
    gap: 1rem !important;
}

[dir="rtl"] .lesson-row:hover {
    box-shadow: inset -4px 0 0 #0d6efd;
}

[dir="rtl"] .action-btn {
    justify-content: flex-start;
    text-align: right;
}

[dir="rtl"] .action-btn-text {
    text-align: right;
}

[dir="ltr"] .action-btn {
    justify-content: flex-start;
    text-align: left;
}

[dir="ltr"] .action-btn-text {
    text-align: left;
}

@media (min-width: 768px) {
    [dir="rtl"] .action-btn,
    [dir="ltr"] .action-btn {
        justify-content: center;
        text-align: center;
    }
    
    [dir="rtl"] .action-btn-text,
    [dir="ltr"] .action-btn-text {
        text-align: center;
    }
}

/* Button Styles */
.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 0.5rem;
}

.badge {
    font-weight: 500;
    padding: 0.4em 0.75em;
    border-radius: 0.375rem;
    display: inline-block;
}

/* Smooth Transitions */
* {
    transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
}

/* Loading State (if needed) */
.lesson-row.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Focus States for Accessibility */
.action-btn:focus,
.attendance-btn:focus,
.add-lesson-btn:focus,
.filter-select:focus,
.questions-link:focus {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
    border-radius: 0.5rem;
}

.action-btn:focus-visible,
.attendance-btn:focus-visible {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
}

/* Keyboard Navigation */
.action-btn:focus-visible {
    z-index: 10;
    position: relative;
}

/* Touch Device Improvements */
@media (hover: none) and (pointer: coarse) {
    .action-btn {
        min-height: 44px;
        height: 44px;
        padding: 0.625rem 0.875rem;
    }
    
    .action-buttons {
        gap: 0.625rem;
    }
}

/* Print Styles */
@media print {
    .add-lesson-btn,
    .action-buttons,
    .attendance-btn {
        display: none;
    }
    
    .card {
        box-shadow: none;
        border: 1px solid #e9ecef;
    }
}
</style>
