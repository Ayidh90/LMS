import { ref, provide, inject } from 'vue';

const modalOpenState = ref(false);

/**
 * Composable for managing modal state across the application
 * Used to hide sidebar when any modal is open
 */
export function useModal() {
    const isModalOpen = inject('isModalOpen', modalOpenState);
    const setModalOpen = inject('setModalOpen', (value) => {
        modalOpenState.value = value;
    });

    return {
        isModalOpen,
        setModalOpen,
    };
}

/**
 * Provide modal state to child components
 */
export function provideModal() {
    const isModalOpen = ref(false);
    
    const setModalOpen = (value) => {
        isModalOpen.value = value;
    };

    provide('isModalOpen', isModalOpen);
    provide('setModalOpen', setModalOpen);

    return {
        isModalOpen,
        setModalOpen,
    };
}

