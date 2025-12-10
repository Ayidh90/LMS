import { ref, computed, watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useDirection() {
    const page = usePage();
    const direction = ref('ltr');

    const currentDirection = computed(() => {
        return page.props.direction || direction.value;
    });

    const setDirection = (dir) => {
        if (dir === 'rtl' || dir === 'ltr') {
            direction.value = dir;
            if (typeof window !== 'undefined') {
                localStorage.setItem('direction', dir);
                document.documentElement.setAttribute('dir', dir);
                document.documentElement.setAttribute('lang', dir === 'rtl' ? 'ar' : 'en');
            }
        }
    };

    const toggleDirection = () => {
        const newDirection = currentDirection.value === 'rtl' ? 'ltr' : 'rtl';
        setDirection(newDirection);
    };

    // Initialize direction on mount - default to RTL (Arabic)
    onMounted(() => {
        if (typeof window !== 'undefined') {
            const savedDirection = localStorage.getItem('direction') || page.props.direction || 'rtl';
            setDirection(savedDirection);
        }
    });

    watch(currentDirection, (newDir) => {
        if (typeof window !== 'undefined') {
            document.documentElement.setAttribute('dir', newDir);
            document.documentElement.setAttribute('lang', newDir === 'rtl' ? 'ar' : 'en');
        }
    }, { immediate: true });

    return {
        direction: currentDirection,
        setDirection,
        toggleDirection,
        isRTL: computed(() => currentDirection.value === 'rtl'),
        isLTR: computed(() => currentDirection.value === 'ltr'),
    };
}

