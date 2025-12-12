<template>
    <Teleport to="body">
        <div v-if="show" class="modal-backdrop" @click.self="$emit('close')">
            <div class="modal-container">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h3 class="modal-title">
                            {{ isEdit ? (t('admin.edit_section') || 'Edit Section') : (t('admin.add_section') || 'Add Section') }}
                        </h3>
                        <button type="button" @click="$emit('close')" class="btn-close-modal">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form id="section-form" @submit.prevent="submit" class="section-form">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    {{ t('sections.fields.title') || 'Title' }} (English) <span class="text-danger">*</span>
                                </label>
                                <input
                                    v-model="formData.title"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': errors.title }"
                                    :placeholder="t('sections.placeholders.title') || 'Enter section title'"
                                />
                                <div v-if="errors.title" class="invalid-feedback d-block">{{ errors.title }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    {{ t('sections.fields.title') || 'Title' }} (العربية)
                                </label>
                                <input
                                    v-model="formData.title_ar"
                                    type="text"
                                    dir="rtl"
                                    class="form-control"
                                    :class="{ 'is-invalid': errors.title_ar }"
                                    placeholder="أدخل عنوان القسم"
                                />
                                <div v-if="errors.title_ar" class="invalid-feedback d-block">{{ errors.title_ar }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    {{ t('sections.fields.description') || 'Description' }} (English)
                                </label>
                                <textarea
                                    v-model="formData.description"
                                    rows="3"
                                    class="form-control"
                                    :class="{ 'is-invalid': errors.description }"
                                    :placeholder="t('sections.placeholders.description') || 'Enter section description'"
                                ></textarea>
                                <div v-if="errors.description" class="invalid-feedback d-block">{{ errors.description }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    {{ t('sections.fields.description') || 'Description' }} (العربية)
                                </label>
                                <textarea
                                    v-model="formData.description_ar"
                                    rows="3"
                                    dir="rtl"
                                    class="form-control"
                                    :class="{ 'is-invalid': errors.description_ar }"
                                    placeholder="أدخل وصف القسم"
                                ></textarea>
                                <div v-if="errors.description_ar" class="invalid-feedback d-block">{{ errors.description_ar }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    {{ t('sections.fields.order') || 'Order' }}
                                </label>
                                <input
                                    v-model.number="formData.order"
                                    type="number"
                                    min="0"
                                    class="form-control"
                                    :class="{ 'is-invalid': errors.order }"
                                    :placeholder="t('sections.placeholders.order') || 'Enter section order (optional)'"
                                />
                                <div v-if="errors.order" class="invalid-feedback d-block">{{ errors.order }}</div>
                                <small v-else class="form-text text-muted">{{ t('sections.order_hint') || 'Leave empty to add at the end' }}</small>
                            </div>
                        </form>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" @click="$emit('close')" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i>
                            {{ t('common.cancel') || 'Cancel' }}
                        </button>
                        <button type="submit" form="section-form" :disabled="processing" class="btn btn-primary">
                            <span v-if="processing" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            <i v-else class="bi bi-check-circle me-1"></i>
                            {{ isEdit ? (t('common.save_changes') || 'Save Changes') : (t('common.create') || 'Create') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { computed, watch, onUnmounted } from 'vue';
import { useTranslation } from '@/composables/useTranslation';
import { useModal } from '@/composables/useModal';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    section: {
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
});

const emit = defineEmits(['close', 'submit']);

const { t } = useTranslation();
const { setModalOpen } = useModal();

const isEdit = computed(() => !!props.section);

// Update modal state when show prop changes - this hides sidebar when modal is open
watch(() => props.show, (isOpen) => {
    setModalOpen(isOpen);
    // Prevent body scroll when modal is open
    if (isOpen) {
        document.body.style.overflow = 'hidden';
        // Prevent scrolling on the backdrop
        document.body.style.paddingRight = '0px';
    } else {
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }
}, { immediate: true });

// Cleanup on unmount
onUnmounted(() => {
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    setModalOpen(false);
});

// Handle ESC key to close modal
const handleEscape = (e) => {
    if (e.key === 'Escape' && props.show) {
        emit('close');
    }
};

watch(() => props.show, (isOpen) => {
    if (isOpen) {
        window.addEventListener('keydown', handleEscape);
    } else {
        window.removeEventListener('keydown', handleEscape);
    }
}, { immediate: true });

const submit = () => {
    emit('submit', props.formData);
};
</script>

<style scoped>
/* Modal Backdrop */
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.65);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    animation: fadeIn 0.2s ease-in-out;
    overflow-y: auto;
    overflow-x: hidden;
}

/* Modal Container */
.modal-container {
    width: 100%;
    max-width: 600px;
    max-height: calc(100vh - 2rem);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: auto;
}

/* Modal Content */
.modal-content {
    background: #ffffff;
    border-radius: 0.75rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    width: 100%;
    max-width: 600px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: slideUp 0.3s ease-out;
    position: relative;
    z-index: 10000;
    margin: auto;
}

/* Modal Header */
.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    flex-shrink: 0;
}

.modal-title {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: #212529;
}

.btn-close-modal {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #6c757d;
    cursor: pointer;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
}

.btn-close-modal:hover {
    background-color: #f8f9fa;
    color: #212529;
}

/* Modal Body */
.modal-body {
    padding: 1.5rem;
    overflow-y: auto;
    flex: 1;
    min-height: 0;
    max-height: calc(90vh - 140px);
}

.modal-body::-webkit-scrollbar {
    width: 8px;
}

.modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.section-form {
    margin: 0;
}

.form-label {
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
    color: #495057;
}

.form-control {
    border-radius: 0.5rem;
    border: 1px solid #ced4da;
    padding: 0.625rem 1rem;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    outline: none;
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.form-control.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #dc3545;
}

.form-text {
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Modal Footer */
.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.75rem;
    background-color: #f8f9fa;
    flex-shrink: 0;
}

.btn {
    padding: 0.625rem 1.5rem;
    font-weight: 500;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
    color: #ffffff;
}

.btn-secondary:hover {
    background-color: #5c636a;
    border-color: #565e64;
    color: #ffffff;
}

.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-primary:hover:not(:disabled) {
    background-color: #0b5ed7;
    border-color: #0a58ca;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
}

.btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-secondary:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(108, 117, 125, 0.2);
}

.btn-primary:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.15em;
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* RTL Support */
[dir="rtl"] .modal-header {
    flex-direction: row-reverse;
}

[dir="rtl"] .modal-footer {
    flex-direction: row-reverse;
    justify-content: flex-start;
}

[dir="rtl"] .btn-close-modal {
    margin-left: 0;
    margin-right: auto;
}

[dir="rtl"] .modal-title {
    text-align: right;
}

[dir="rtl"] .form-label {
    text-align: right;
}

[dir="rtl"] .me-1 {
    margin-right: 0 !important;
    margin-left: 0.25rem !important;
}

[dir="rtl"] .me-2 {
    margin-right: 0 !important;
    margin-left: 0.5rem !important;
}

/* Responsive */
@media (max-width: 576px) {
    .modal-backdrop {
        padding: 0.5rem;
    }
    
    .modal-content {
        max-height: 95vh;
    }
    
    .modal-header {
        padding: 1rem;
    }
    
    .modal-title {
        font-size: 1.25rem;
    }
    
    .modal-body {
        padding: 1rem;
    }
    
    .modal-footer {
        padding: 0.75rem 1rem;
        flex-direction: column-reverse;
    }
    
    .btn {
        width: 100%;
    }
}
</style>
