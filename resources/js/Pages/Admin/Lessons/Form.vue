<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" :dir="direction">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="$emit('close')"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl p-6 transform transition-all max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6" :class="direction === 'rtl' ? 'flex-row-reverse' : ''">
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ isEdit ? (t('admin.edit_lesson') || 'Edit Lesson') : (t('admin.add_lesson') || 'Add Lesson') }}
                        </h3>
                        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="bi bi-x-lg text-2xl"></i>
                        </button>
                    </div>
                    <form @submit.prevent="submit" class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ t('lessons.fields.title') || 'Title' }} (English) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="formData.title"
                                    type="text"
                                    class="form-input"
                                    :class="{ 'border-red-500': filteredErrors.title }"
                                    :placeholder="t('lessons.placeholders.title') || 'Enter lesson title'"
                                />
                                <p v-if="filteredErrors.title" class="form-error">{{ filteredErrors.title }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ t('lessons.fields.title') || 'Title' }} (العربية)
                                </label>
                                <input
                                    v-model="formData.title_ar"
                                    type="text"
                                    dir="rtl"
                                    class="form-input"
                                    :placeholder="'أدخل عنوان الدرس'"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ t('lessons.fields.type') || 'Type' }} <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="formData.type"
                                    class="form-select"
                                    :class="{ 'border-red-500': filteredErrors.type }"
                                    @change="handleTypeChange"
                                >
                                    <option value="">{{ t('common.select') || 'Select' }}</option>
                                    <option v-for="type in lessonTypes" :key="type.value" :value="type.value">
                                        {{ t(`lessons.types.${type.value}`) || type.label }}
                                    </option>
                                </select>
                                <p v-if="filteredErrors.type" class="form-error">{{ filteredErrors.type }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ t('lessons.fields.section') || 'Section' }}
                                    <span v-if="isAdmin" class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="formData.section_id"
                                    class="form-select"
                                    :class="{ 'border-red-500': filteredErrors.section_id }"
                                    :required="isAdmin"
                                >
                                    <option :value="null">{{ t('lessons.no_section') || 'No Section' }}</option>
                                    <option v-for="section in sections" :key="section.id" :value="section.id">
                                        {{ section.translated_title || section.title }}
                                    </option>
                                </select>
                                <p v-if="filteredErrors.section_id" class="form-error">{{ filteredErrors.section_id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ t('lessons.fields.order') || 'Order' }}
                                </label>
                                <input
                                    v-model.number="formData.order"
                                    type="number"
                                    min="0"
                                    class="form-input"
                                    :class="{ 'border-red-500': errors.order }"
                                />
                                <p v-if="errors.order" class="form-error">{{ errors.order }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ t('lessons.fields.duration') || 'Duration' }} ({{ t('common.minutes') || 'minutes' }})
                                </label>
                                <input
                                    v-model.number="formData.duration_minutes"
                                    type="number"
                                    min="0"
                                    class="form-input"
                                    @blur="formData.duration_minutes = formData.duration_minutes || 0"
                                />
                            </div>
                        </div>
                        <!-- Video/Embed URL for YouTube, Google Drive, Embed Frame -->
                        <div v-if="['youtube_video', 'google_drive_video', 'embed_frame'].includes(formData.type)">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('lessons.fields.video_url') || 'Video/Embed URL' }}
                            </label>
                            <input
                                v-model="formData.video_url"
                                type="url"
                                class="form-input"
                                :class="{ 'border-red-500': errors.video_url }"
                                :placeholder="t('lessons.placeholders.video_url') || 'https://...'"
                            />
                            <p v-if="errors.video_url" class="form-error">{{ errors.video_url }}</p>
                        </div>
                        
                        <!-- File Upload for Video File, Image, Document -->
                        <div v-if="['video_file', 'image', 'document_file'].includes(formData.type)" class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span v-if="formData.type === 'video_file'">
                                        {{ t('lessons.fields.upload_video') || 'Upload Video File' }}
                                    </span>
                                    <span v-else-if="formData.type === 'image'">
                                        {{ t('lessons.fields.upload_image') || 'Upload Image File' }}
                                    </span>
                                    <span v-else-if="formData.type === 'document_file'">
                                        {{ t('lessons.fields.upload_document') || 'Upload Document File' }}
                                    </span>
                                </label>
                                <div class="relative">
                                    <input
                                        v-if="formData.type === 'video_file'"
                                        ref="videoFileInput"
                                        type="file"
                                        accept="video/*"
                                        @change="handleFileChange($event, 'video_file')"
                                        class="hidden"
                                    />
                                    <input
                                        v-else-if="formData.type === 'image'"
                                        ref="imageFileInput"
                                        type="file"
                                        accept="image/*"
                                        @change="handleFileChange($event, 'image')"
                                        class="hidden"
                                    />
                                    <input
                                        v-else-if="formData.type === 'document_file'"
                                        ref="documentFileInput"
                                        type="file"
                                        accept=".pdf,.doc,.docx,.txt,.rtf,.odt"
                                        @change="handleFileChange($event, 'document_file')"
                                        class="hidden"
                                    />
                                    <button
                                        type="button"
                                        @click="triggerFileInput"
                                        class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition-all flex items-center justify-center gap-2"
                                    >
                                        <i class="bi bi-cloud-upload text-2xl text-gray-400"></i>
                                        <span class="text-gray-600">
                                            {{ formData.type === 'video_file' ? (t('lessons.placeholders.upload_video') || 'Click to upload video file') :
                                               formData.type === 'image' ? (t('lessons.placeholders.upload_image') || 'Click to upload image file') :
                                               (t('lessons.placeholders.upload_document') || 'Click to upload document file') }}
                                        </span>
                                    </button>
                                </div>
                                <p v-if="selectedFileName" class="mt-2 text-sm text-gray-600">
                                    <i class="bi bi-file-check text-green-500"></i>
                                    {{ selectedFileName }}
                                </p>
                                <p v-else-if="formData.video_url && !selectedFileName && isEdit" class="mt-2 text-sm text-gray-500">
                                    <i class="bi bi-file-check text-green-500"></i>
                                    {{ t('lessons.current_file') || 'Current file' }}: {{ getFileNameFromUrl(formData.video_url) }}
                                </p>
                            </div>
                        </div>
                        <div v-if="formData.type === 'live'" class="space-y-5">
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4" :dir="direction">
                                <div class="flex items-start gap-3" :class="direction === 'rtl' ? 'flex-row-reverse' : ''">
                                    <i class="bi bi-info-circle text-blue-600 text-xl mt-0.5 flex-shrink-0"></i>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-blue-900 mb-1">
                                            {{ t('lessons.live.info_title') || 'Live Lesson Requirements' }}
                                        </p>
                                        <p class="text-xs text-blue-700">
                                            {{ t('lessons.live.info_message') || 'Please provide the meeting date and time. A meeting link will be auto-generated if not provided.' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        {{ t('lessons.live.meeting_date') || t('lessons.fields.live_meeting_date') || 'Meeting Date & Time' }} <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        ref="liveMeetingDateInput"
                                        v-model="formData.live_meeting_date"
                                        type="datetime-local"
                                        class="form-input"
                                        :class="{ 'border-red-500': errors.live_meeting_date }"
                                        required
                                        @focus="hasInteractedWithDate = true"
                                        @blur="hasInteractedWithDate = true"
                                        @input="hasInteractedWithDate = true"
                                    />
                                    <p v-if="errors.live_meeting_date" class="form-error">{{ errors.live_meeting_date }}</p>
                                    <p v-if="showDateWarning" class="mt-1 text-xs text-gray-600" :dir="direction">
                                        <i class="bi bi-info-circle me-1"></i>
                                        {{ t('lessons.live.date_required_warning') || 'Live lecture date and time is required for live lessons.' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        {{ t('lessons.live.meeting_link') || t('lessons.fields.live_meeting_link') || 'Meeting Link' }}
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <input
                                            v-model="formData.live_meeting_link"
                                            type="url"
                                            class="form-input flex-1"
                                            :class="{ 'border-red-500': errors.live_meeting_link }"
                                            :placeholder="t('lessons.placeholders.meeting_link') || 'https://meet.jit.si/... or enter your own link'"
                                        />
                                        <a
                                            v-if="formData.live_meeting_link"
                                            :href="formData.live_meeting_link"
                                            target="_blank"
                                            class="btn-secondary px-4 py-3 whitespace-nowrap"
                                            :title="t('lessons.live.join_meeting') || 'Join Meeting'"
                                        >
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                    </div>
                                    <p v-if="errors.live_meeting_link" class="form-error">{{ errors.live_meeting_link }}</p>
                                    <p class="mt-2 text-xs text-gray-500">
                                        {{ t('lessons.live.meeting_link_hint') || 'Leave empty to auto-generate a Jitsi Meet link, or enter your own Google Meet/Zoom/Teams link' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('lessons.fields.description') || 'Description' }} (English)
                            </label>
                            <textarea
                                v-model="formData.description"
                                rows="3"
                                class="form-textarea"
                                :placeholder="t('lessons.placeholders.description') || 'Enter lesson description'"
                            ></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ t('lessons.fields.description') || 'Description' }} (العربية)
                            </label>
                            <textarea
                                v-model="formData.description_ar"
                                rows="3"
                                dir="rtl"
                                class="form-textarea"
                                :placeholder="'أدخل وصف الدرس'"
                            ></textarea>
                        </div>
                        <!-- Content fields for text, assignment, and test types -->
                        <div v-if="['text', 'assignment', 'test'].includes(formData.type)">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <span v-if="formData.type === 'text'">{{ t('lessons.fields.content') || 'Content' }}</span>
                                <span v-else-if="formData.type === 'assignment'">{{ t('lessons.fields.assignment_instructions') || 'Assignment Instructions' }}</span>
                                <span v-else-if="formData.type === 'test'">{{ t('lessons.fields.test_instructions') || 'Test Instructions' }}</span>
                                <span> (English)</span>
                            </label>
                            <textarea
                                v-model="formData.content"
                                rows="6"
                                class="form-textarea font-mono text-sm"
                                :placeholder="formData.type === 'text' 
                                    ? (t('lessons.placeholders.content') || 'Enter lesson content (supports HTML)')
                                    : formData.type === 'assignment'
                                    ? (t('lessons.placeholders.assignment') || 'Enter assignment instructions...')
                                    : (t('lessons.placeholders.test') || 'Enter test instructions...')"
                            ></textarea>
                        </div>
                        <div v-if="['text', 'assignment', 'test'].includes(formData.type)">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <span v-if="formData.type === 'text'">{{ t('lessons.fields.content') || 'Content' }}</span>
                                <span v-else-if="formData.type === 'assignment'">{{ t('lessons.fields.assignment_instructions') || 'Assignment Instructions' }}</span>
                                <span v-else-if="formData.type === 'test'">{{ t('lessons.fields.test_instructions') || 'Test Instructions' }}</span>
                                <span> (العربية)</span>
                            </label>
                            <textarea
                                v-model="formData.content_ar"
                                rows="6"
                                dir="rtl"
                                class="form-textarea font-mono text-sm"
                                :placeholder="formData.type === 'text' 
                                    ? 'أدخل محتوى الدرس'
                                    : formData.type === 'assignment'
                                    ? 'أدخل تعليمات الواجب'
                                    : 'أدخل تعليمات الاختبار'"
                            ></textarea>
                        </div>
                        <div class="flex items-center gap-3 pt-4" :class="direction === 'rtl' ? 'flex-row-reverse justify-start' : 'justify-end'">
                            <button type="button" @click="$emit('close')" class="btn-secondary">
                                {{ t('common.cancel') || 'Cancel' }}
                            </button>
                            <button type="submit" :disabled="processing" class="btn-primary">
                                {{ isEdit ? (t('common.save_changes') || 'Save Changes') : (t('common.create') || 'Create') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { computed, watch, ref, nextTick } from 'vue';
import { useTranslation } from '@/composables/useTranslation';
import { useModal } from '@/composables/useModal';
import { usePermissions } from '@/composables/usePermissions';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    lesson: {
        type: Object,
        default: null,
    },
    formData: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    processing: {
        type: Boolean,
        default: false,
    },
    sections: {
        type: Array,
        default: () => [],
    },
    lessonTypes: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close', 'submit', 'type-change']);

const { t } = useTranslation();
const { setModalOpen } = useModal();
const { isAdmin } = usePermissions();
const page = usePage();

const isEdit = computed(() => !!props.lesson);
const liveMeetingDateInput = ref(null);
const direction = computed(() => page.props.direction || 'ltr');

// File input refs
const videoFileInput = ref(null);
const imageFileInput = ref(null);
const documentFileInput = ref(null);
const selectedFileName = ref('');

// Track if user has interacted with the date field
const hasInteractedWithDate = ref(false);

// Check if live meeting date is empty
const isLiveMeetingDateEmpty = computed(() => {
    const date = props.formData.live_meeting_date;
    return !date || date === '' || date === null || (typeof date === 'string' && date.trim() === '');
});

// Check if should show warning message (only show if user has interacted with the date field)
const showDateWarning = computed(() => {
    // Don't show warning if there's already a backend validation error (it will show the error instead)
    if (props.errors.live_meeting_date) {
        return false;
    }
    
    // Only show warning if:
    // 1. Type is 'live'
    // 2. Date is empty
    // 3. User has interacted with the date field (focused, blurred, or input)
    // This prevents showing the warning immediately when selecting 'live' type
    return props.formData.type === 'live' && 
           isLiveMeetingDateEmpty.value && 
           hasInteractedWithDate.value;
});

// Computed property to filter errors - only show errors for fields that are actually invalid
const filteredErrors = computed(() => {
    const filtered = { ...props.errors };
    
    // Clear title error if title has a value
    if (filtered.title && props.formData.title && props.formData.title.trim() !== '') {
        delete filtered.title;
    }
    
    // Clear type error if type has a value
    if (filtered.type && props.formData.type && props.formData.type.trim() !== '') {
        delete filtered.type;
    }
    
    // Clear section_id error if section_id has a value
    if (filtered.section_id && props.formData.section_id !== null && props.formData.section_id !== '' && props.formData.section_id !== undefined) {
        delete filtered.section_id;
    }
    
    return filtered;
});

// Update modal state when show prop changes
watch(() => props.show, (isOpen) => {
    setModalOpen(isOpen);
    // Reset interaction flag and file selection when modal closes
    if (!isOpen) {
        hasInteractedWithDate.value = false;
        selectedFileName.value = '';
        // Reset file inputs
        if (videoFileInput.value) videoFileInput.value.value = '';
        if (imageFileInput.value) imageFileInput.value.value = '';
        if (documentFileInput.value) documentFileInput.value.value = '';
    }
}, { immediate: true });

// Watch for type changes to handle lesson type specific behavior
watch(() => props.formData.type, async (newType, oldType) => {
    // Only process if type actually changed (not during initial load)
    if (!oldType || oldType === newType) {
        return;
    }
    
    // Reset interaction flag when switching types
    hasInteractedWithDate.value = false;
    
    // Clear file selection when type changes
    selectedFileName.value = '';
    
    // Clear file inputs
    if (videoFileInput.value) videoFileInput.value.value = '';
    if (imageFileInput.value) imageFileInput.value.value = '';
    if (documentFileInput.value) documentFileInput.value.value = '';
    
    // Clear file from formData
    delete props.formData.video_file;
    delete props.formData.image_file;
    delete props.formData.document_file;
    
    // When switching away from URL-based types, clear video_url if not a file type
    if (oldType && !['youtube_video', 'google_drive_video', 'embed_frame', 'video_file', 'image', 'document_file'].includes(newType)) {
        // Only clear if switching to a type that doesn't use video_url
        if (!['youtube_video', 'google_drive_video', 'embed_frame', 'video_file', 'image', 'document_file'].includes(newType)) {
            props.formData.video_url = '';
        }
    }
    
    // When switching away from file types, clear video_url if not keeping the file
    if (oldType && ['video_file', 'image', 'document_file'].includes(oldType) && !['video_file', 'image', 'document_file'].includes(newType)) {
        // Only clear if switching to a non-file type that doesn't use video_url
        if (!['youtube_video', 'google_drive_video', 'embed_frame', 'video_file', 'image', 'document_file'].includes(newType)) {
            props.formData.video_url = '';
        }
    }
    
    // When switching away from 'live', clear live-specific fields
    if (oldType === 'live' && newType !== 'live') {
        props.formData.live_meeting_date = '';
        props.formData.live_meeting_link = '';
    }
    
    // When switching to 'live', focus on the date input after a short delay
    if (newType === 'live' && oldType !== 'live') {
        await nextTick();
        if (liveMeetingDateInput.value) {
            // Small delay to ensure the input is rendered
            setTimeout(() => {
                liveMeetingDateInput.value?.focus();
            }, 100);
        }
    }
    
    // When switching to 'text', 'assignment', or 'test', ensure content fields are available
    if (['text', 'assignment', 'test'].includes(newType) && !['text', 'assignment', 'test'].includes(oldType)) {
        // Content fields are already shown conditionally, no action needed
    }
    
    // When switching away from 'text', 'assignment', or 'test', preserve content if switching to another content type
    if (['text', 'assignment', 'test'].includes(oldType) && !['text', 'assignment', 'test'].includes(newType)) {
        // Only clear content if switching to a type that doesn't use it
        if (!['text', 'assignment', 'test'].includes(newType)) {
            // Don't clear content - let user decide, or it will be cleared by backend validation
        }
    }
});

const handleTypeChange = () => {
    // Clear file selection when type changes
    selectedFileName.value = '';
    
    // Clear video_url when switching away from URL-based types
    if (!['youtube_video', 'google_drive_video', 'embed_frame'].includes(props.formData.type)) {
        // Don't clear if it's a file path (for file types)
        if (!['video_file', 'image', 'document_file'].includes(props.formData.type)) {
            props.formData.video_url = '';
        }
    }
    
    // Clear live meeting fields when switching away from live type
    if (props.formData.type !== 'live') {
        props.formData.live_meeting_date = '';
        props.formData.live_meeting_link = '';
    }
    
    // Emit type change event for parent component handling
    emit('type-change', props.formData.type);
};

const triggerFileInput = () => {
    if (props.formData.type === 'video_file' && videoFileInput.value) {
        videoFileInput.value.click();
    } else if (props.formData.type === 'image' && imageFileInput.value) {
        imageFileInput.value.click();
    } else if (props.formData.type === 'document_file' && documentFileInput.value) {
        documentFileInput.value.click();
    }
};

const handleFileChange = (event, fileType) => {
    const file = event.target.files?.[0];
    if (file) {
        selectedFileName.value = file.name;
        // Clear video_url when a file is selected - backend will set it to the file path
        props.formData.video_url = '';
        // Store the file reference for submission
        // Files will be passed separately in the emit
    } else {
        selectedFileName.value = '';
    }
};

// Helper function to extract filename from URL
const getFileNameFromUrl = (url) => {
    if (!url) return '';
    // Extract filename from URL (e.g., "lessons/videos/file.mp4" or full URL)
    const parts = url.split('/');
    return parts[parts.length - 1] || url;
};

const submit = () => {
    // Prepare form data with files
    const submitData = { ...props.formData };
    
    // Ensure title is always included (even if empty - backend will validate)
    submitData.title = props.formData.title !== undefined && props.formData.title !== null
        ? (typeof props.formData.title === 'string' ? props.formData.title.trim() : String(props.formData.title))
        : '';
    
    // Ensure type is always included
    submitData.type = props.formData.type !== undefined && props.formData.type !== null
        ? String(props.formData.type)
        : '';
    
    // Validate required fields before submitting (but still emit so backend can validate)
    if (!submitData.title || submitData.title.trim() === '') {
        // Title is required - backend will validate, but we still emit
        emit('submit', submitData);
        return;
    }
    
    if (!submitData.type || submitData.type === '') {
        // Type is required - backend will validate
        emit('submit', submitData);
        return;
    }
    
    // Validate section is required for admin users
    if (isAdmin.value && (!submitData.section_id || submitData.section_id === null || submitData.section_id === '')) {
        // The backend will also validate this, but we can show a client-side error
        // The error will be handled by the backend validation
        emit('submit', submitData);
        return;
    }
    
    // Include files if they exist (as File objects for Inertia to handle)
    if (videoFileInput.value?.files?.[0]) {
        submitData.video_file = videoFileInput.value.files[0];
    }
    if (imageFileInput.value?.files?.[0]) {
        submitData.image_file = imageFileInput.value.files[0];
    }
    if (documentFileInput.value?.files?.[0]) {
        submitData.document_file = documentFileInput.value.files[0];
    }
    
    emit('submit', submitData);
};
</script>

<style scoped>
.form-input,
.form-textarea,
.form-select {
    @apply w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white;
}

.form-textarea {
    @apply resize-none;
}

.form-error {
    @apply mt-1 text-sm text-red-500;
}

.btn-secondary {
    @apply px-5 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 font-medium transition-all;
}

.btn-primary {
    @apply px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed;
}
</style>

