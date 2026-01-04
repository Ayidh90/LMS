<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-center justify-center p-4">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="$emit('close')"></div>
                
                <!-- Modal Content -->
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col transform transition-all">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600">
                        <h3 class="text-xl font-bold text-white">
                            {{ t('admin.add_students') || 'Add Students' }}
                        </h3>
                        <button @click="$emit('close')" class="text-white/90 hover:text-white transition-colors">
                            <i class="pi pi-times text-xl"></i>
                        </button>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto p-6 bg-gradient-to-b from-white to-gray-50">
                        <!-- Import Excel Section -->
                        <div class="mb-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-file-excel text-green-600 text-xl"></i>
                                    <p class="text-sm font-semibold text-green-900">
                                        {{ t('admin.import_from_excel') || 'Import from Excel' }}
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="downloadTemplate"
                                    class="px-3 py-1.5 text-xs font-medium text-green-700 bg-white border border-green-300 rounded-lg hover:bg-green-50 transition-colors flex items-center gap-1.5"
                                >
                                    <i class="pi pi-download text-xs"></i>
                                    {{ t('admin.download_template') || 'Download Template' }}
                                </button>
                            </div>
                            <div class="flex items-center gap-3">
                                <label class="flex-1 cursor-pointer">
                                    <input
                                        type="file"
                                        ref="fileInput"
                                        @change="handleFileSelect"
                                        accept=".xlsx,.xls,.csv"
                                        class="hidden"
                                    />
                                    <div class="px-4 py-2.5 bg-white border-2 border-dashed border-green-300 rounded-lg hover:border-green-400 hover:bg-green-50 transition-all text-center">
                                        <i class="pi pi-upload text-green-600 text-lg mb-1 block"></i>
                                        <span class="text-xs font-medium text-green-700">
                                            {{ t('admin.choose_excel_file') || 'Choose Excel file' }}
                                        </span>
                                    </div>
                                </label>
                                <button
                                    v-if="selectedFile"
                                    type="button"
                                    @click="handleImport"
                                    :disabled="importing"
                                    class="px-4 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                >
                                    <i v-if="importing" class="pi pi-spin pi-spinner text-sm"></i>
                                    <i v-else class="pi pi-upload text-sm"></i>
                                    {{ importing ? (t('admin.importing') || 'Importing...') : (t('admin.import') || 'Import') }}
                                </button>
                            </div>
                            <p v-if="selectedFile" class="mt-2 text-xs text-green-700">
                                {{ t('admin.selected_file') || 'Selected file' }}: {{ selectedFile.name }}
                            </p>
                        </div>

                        <!-- Divider -->
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex-1 border-t border-gray-300"></div>
                            <span class="text-xs font-medium text-gray-500 px-2">
                                {{ t('admin.or') || 'OR' }}
                            </span>
                            <div class="flex-1 border-t border-gray-300"></div>
                        </div>

                        <!-- Info Header -->
                        <div class="flex items-center justify-between mb-4 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border border-indigo-200">
                            <p class="text-sm font-medium text-indigo-900">
                                {{ t('admin.select_students_to_add') || 'Select students to add to this batch' }}
                            </p>
                            <span v-if="selectedStudents.length > 0" class="px-3 py-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-xs font-semibold rounded-full">
                                {{ selectedStudents.length }} {{ t('admin.selected') || 'selected' }}
                            </span>
                        </div>
                        
                        <!-- Search Input and Select All -->
                        <div v-if="availableStudents && availableStudents.length > 0" class="space-y-3 mb-4">
                            <!-- Search Input -->
                            <div class="relative">
                                <i class="pi pi-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    :placeholder="t('admin.search_students') || 'Search students by name or email...'"
                                    class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-white"
                                />
                            </div>
                            
                            <!-- Select All Section -->
                            <div v-if="filteredStudents.length > 0" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <button
                                    type="button"
                                    @click="toggleSelectAll"
                                    class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors"
                                >
                                    <span v-if="allFilteredSelected">{{ t('admin.deselect_all') || 'Deselect All' }}</span>
                                    <span v-else>{{ t('admin.select_all') || 'Select All' }}</span>
                                </button>
                                <span class="text-xs text-gray-600 font-medium px-2 py-1 bg-white rounded border border-gray-200">
                                    {{ filteredStudents.length }} {{ t('admin.students_available') || 'students available' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Students List -->
                        <div v-if="filteredStudents && filteredStudents.length > 0" class="border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm">
                            <div class="max-h-96 overflow-y-auto">
                                <div 
                                    v-for="student in filteredStudents" 
                                    :key="student.id"
                                    class="flex items-center gap-3 p-4 border-b border-gray-100 cursor-pointer transition-all hover:bg-gray-50"
                                    :class="{ 'bg-indigo-50 border-l-4 border-l-indigo-600': selectedStudents.includes(student.id) }"
                                    @click="toggleStudent(student.id)"
                                >
                                    <div class="flex-shrink-0">
                                        <input
                                            type="checkbox"
                                            :checked="selectedStudents.includes(student.id)"
                                            @change="toggleStudent(student.id)"
                                            @click.stop
                                            class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer"
                                        />
                                    </div>
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-md">
                                        {{ student.name?.[0]?.toUpperCase() || 'S' }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-gray-900 text-sm">{{ student.name }}</div>
                                        <div class="text-xs text-gray-500 truncate">{{ student.email }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- No Results - Search -->
                        <div v-else-if="availableStudents && availableStudents.length > 0 && filteredStudents.length === 0" class="text-center py-12 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                            <div class="w-16 h-16 bg-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="pi pi-search text-2xl text-gray-400"></i>
                            </div>
                            <p class="font-semibold text-gray-700 mb-1">{{ t('admin.no_students_found') || 'No students found' }}</p>
                            <p class="text-sm text-gray-500">{{ t('admin.try_different_search') || 'Try a different search term' }}</p>
                        </div>
                        
                        <!-- No Available Students -->
                        <div v-else class="text-center py-12 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                            <div class="w-16 h-16 bg-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="pi pi-users text-2xl text-gray-400"></i>
                            </div>
                            <p class="font-semibold text-gray-700 mb-1">{{ t('admin.no_available_students') || 'No available students to add' }}</p>
                            <p class="text-sm text-gray-500">{{ t('admin.all_students_enrolled') || 'All students are already enrolled in this batch' }}</p>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="px-6 py-4 bg-white border-t border-gray-200 flex items-center justify-between gap-4">
                        <button 
                            type="button"
                            @click="$emit('close')" 
                            class="px-5 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 font-medium transition-all"
                        >
                            {{ t('common.cancel') || 'Cancel' }}
                        </button>
                        <div class="flex items-center gap-3">
                            <span v-if="selectedStudents.length > 0" class="text-sm font-semibold text-gray-700 px-3 py-1.5 bg-gray-100 rounded-lg border border-gray-200">
                                {{ selectedStudents.length }} {{ t('admin.students_selected') || 'students selected' }}
                            </span>
                            <button 
                                type="button"
                                @click="handleSubmit"
                                :disabled="selectedStudents.length === 0 || processing"
                                class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                            >
                                <i class="pi pi-plus text-sm"></i>
                                {{ t('admin.add_selected') || 'Add Selected' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useTranslation } from '@/composables/useTranslation';
import { useModal } from '@/composables/useModal';
import { useRoute } from '@/composables/useRoute';
import { useAlert } from '@/composables/useAlert';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    availableStudents: {
        type: Array,
        default: () => []
    },
    processing: {
        type: Boolean,
        default: false
    },
    course: {
        type: Object,
        required: true
    },
    batch: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['close', 'submit', 'imported']);

const { t } = useTranslation();
const { setModalOpen } = useModal();
const { route } = useRoute();
const { showError, showSuccess } = useAlert();
const selectedStudents = ref([]);
const searchQuery = ref('');
const fileInput = ref(null);
const selectedFile = ref(null);
const importing = ref(false);

// Update modal state when show prop changes
watch(() => props.show, (isOpen) => {
    setModalOpen(isOpen);
    
    if (isOpen) {
        // Lock body scroll
        document.body.style.overflow = 'hidden';
    } else {
        // Unlock body scroll
        document.body.style.overflow = '';
        // Reset state when modal closes
        selectedStudents.value = [];
        searchQuery.value = '';
        selectedFile.value = null;
        if (fileInput.value) {
            fileInput.value.value = '';
        }
    }
}, { immediate: true });

// Handle ESC key to close modal
const handleEscape = (event) => {
    if (event.key === 'Escape' && props.show) {
        emit('close');
    }
};

onMounted(() => {
    document.addEventListener('keydown', handleEscape);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleEscape);
    document.body.style.overflow = '';
});

// Filter students based on search query
const filteredStudents = computed(() => {
    if (!props.availableStudents || props.availableStudents.length === 0) {
        return [];
    }
    
    if (!searchQuery.value || searchQuery.value.trim() === '') {
        return props.availableStudents;
    }
    
    const query = searchQuery.value.toLowerCase().trim();
    return props.availableStudents.filter(student => {
        const name = (student.name || '').toLowerCase();
        const email = (student.email || '').toLowerCase();
        return name.includes(query) || email.includes(query);
    });
});

// Check if all filtered students are selected
const allFilteredSelected = computed(() => {
    if (filteredStudents.value.length === 0) return false;
    return filteredStudents.value.every(student => selectedStudents.value.includes(student.id));
});

// Toggle single student
const toggleStudent = (studentId) => {
    const index = selectedStudents.value.indexOf(studentId);
    if (index === -1) {
        selectedStudents.value.push(studentId);
    } else {
        selectedStudents.value.splice(index, 1);
    }
};

// Toggle select all filtered students
const toggleSelectAll = () => {
    if (allFilteredSelected.value) {
        const filteredIds = filteredStudents.value.map(s => s.id);
        selectedStudents.value = selectedStudents.value.filter(id => !filteredIds.includes(id));
    } else {
        const filteredIds = filteredStudents.value.map(s => s.id);
        filteredIds.forEach(id => {
            if (!selectedStudents.value.includes(id)) {
                selectedStudents.value.push(id);
            }
        });
    }
};

// Handle submit
const handleSubmit = () => {
    if (selectedStudents.value.length > 0) {
        emit('submit', selectedStudents.value);
    }
};

// Handle file select
const handleFileSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Validate file type
        const validTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
                           'application/vnd.ms-excel', 
                           'text/csv'];
        const validExtensions = ['.xlsx', '.xls', '.csv'];
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        
        if (!validTypes.includes(file.type) && !validExtensions.includes(fileExtension)) {
            showError(t('admin.invalid_file_type') || 'Please select a valid Excel file (.xlsx, .xls, or .csv)', t('common.error') || 'Error');
            event.target.value = '';
            return;
        }
        
        selectedFile.value = file;
    }
};

// Download template
const downloadTemplate = () => {
    let url;
    try {
        url = route('admin.courses.batches.download-template', [
            props.course.slug || props.course.id,
            props.batch.id
        ]);
    } catch (error) {
        // Fallback: construct URL manually if route not found in Ziggy
        const courseId = props.course.slug || props.course.id;
        const batchId = props.batch.id;
        url = `/admin/courses/${courseId}/batches/${batchId}/students/template`;
    }
    
    // Use a hidden iframe to download the file without navigating away
    // This bypasses Inertia and ensures the browser handles the download directly
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.src = url;
    document.body.appendChild(iframe);
    
    // Clean up after download starts (browser will handle the download)
    setTimeout(() => {
        if (iframe.parentNode) {
            document.body.removeChild(iframe);
        }
    }, 10000);
};

// Handle import
const handleImport = () => {
    if (!selectedFile.value) {
        return;
    }
    
    importing.value = true;
    
    const formData = new FormData();
    formData.append('file', selectedFile.value);
    
    router.post(
        route('admin.courses.batches.import-students', [
            props.course.slug || props.course.id,
            props.batch.id
        ]),
        formData,
        {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: (page) => {
                importing.value = false;
                selectedFile.value = null;
                if (fileInput.value) {
                    fileInput.value.value = '';
                }
                showSuccess(t('admin.students_imported_successfully') || 'Students imported successfully!', t('common.success') || 'Success');
                emit('imported');
                emit('close');
            },
            onError: (errors) => {
                importing.value = false;
                let errorMessage = t('admin.import_failed') || 'Import failed. Please check your file and try again.';
                
                if (errors.message) {
                    errorMessage = errors.message;
                } else if (errors.file) {
                    errorMessage = Array.isArray(errors.file) ? errors.file[0] : errors.file;
                } else if (errors.student_ids) {
                    errorMessage = Array.isArray(errors.student_ids) ? errors.student_ids[0] : errors.student_ids;
                }
                
                showError(errorMessage, t('common.error') || 'Error', { modal: true });
            },
        }
    );
};
</script>

<style scoped>
/* RTL Support */
[dir="rtl"] .pi-search {
    left: auto;
    right: 1rem;
}

[dir="rtl"] input[type="text"] {
    padding-left: 1rem;
    padding-right: 3rem;
}

[dir="rtl"] .flex.items-center.justify-between {
    flex-direction: row-reverse;
}

[dir="rtl"] .border-l-4 {
    border-left: none;
    border-right: 4px solid;
}

[dir="rtl"] .flex.items-center.justify-between {
    flex-direction: row-reverse;
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
