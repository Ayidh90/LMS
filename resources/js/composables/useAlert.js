import Swal from 'sweetalert2';

/**
 * Get current direction (RTL/LTR)
 */
const getDirection = () => {
    if (typeof document !== 'undefined') {
        return document.documentElement.dir || 'ltr';
    }
    return 'ltr';
};

/**
 * SweetAlert2 composable for clean alert notifications
 * Small alerts with blue theme (no green)
 */
export function useAlert() {
    const showSuccess = (message, title = null) => {
        const isRTL = getDirection() === 'rtl';
        return Swal.fire({
            icon: 'success',
            title: title || 'Success!',
            text: message,
            toast: true,
            position: isRTL ? 'top-start' : 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            width: 'auto',
            maxWidth: '16rem',
            padding: '0.75rem 1rem',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            },
            customClass: {
                popup: 'sweet-alert-popup sweet-alert-success',
                title: 'sweet-alert-title',
                htmlContainer: 'sweet-alert-content',
            },
        });
    };

    const showError = (message, title = null) => {
        const isRTL = getDirection() === 'rtl';
        return Swal.fire({
            icon: 'error',
            title: title || 'Error!',
            text: message,
            toast: true,
            position: isRTL ? 'top-start' : 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            width: 'auto',
            maxWidth: '16rem',
            padding: '0.75rem 1rem',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            },
            customClass: {
                popup: 'sweet-alert-popup sweet-alert-error',
                title: 'sweet-alert-title',
                htmlContainer: 'sweet-alert-content',
            },
        });
    };

    const showWarning = (message, title = null) => {
        const isRTL = getDirection() === 'rtl';
        return Swal.fire({
            icon: 'warning',
            title: title || 'Warning!',
            text: message,
            toast: true,
            position: isRTL ? 'top-start' : 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            width: 'auto',
            maxWidth: '16rem',
            padding: '0.75rem 1rem',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            },
            customClass: {
                popup: 'sweet-alert-popup sweet-alert-warning',
                title: 'sweet-alert-title',
                htmlContainer: 'sweet-alert-content',
            },
        });
    };

    const showInfo = (message, title = null) => {
        const isRTL = getDirection() === 'rtl';
        return Swal.fire({
            icon: 'info',
            title: title || 'Info',
            text: message,
            toast: true,
            position: isRTL ? 'top-start' : 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            width: 'auto',
            maxWidth: '16rem',
            padding: '0.75rem 1rem',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            },
            customClass: {
                popup: 'sweet-alert-popup sweet-alert-info',
                title: 'sweet-alert-title',
                htmlContainer: 'sweet-alert-content',
            },
        });
    };

    const showConfirm = (message, title = 'Are you sure?') => {
        return Swal.fire({
            title: title,
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            customClass: {
                popup: 'sweet-alert-popup',
                title: 'sweet-alert-title',
                htmlContainer: 'sweet-alert-content',
            },
        });
    };

    return {
        showSuccess,
        showError,
        showWarning,
        showInfo,
        showConfirm,
    };
}
