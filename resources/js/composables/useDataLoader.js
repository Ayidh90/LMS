import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

// Global data loading state
const isDataLoading = ref(false);

/**
 * Composable to track data loading state
 * Used by ContentLoader to show skeleton when data is being fetched
 */
export function useDataLoader() {
    // Setup listeners once globally
    if (!window.__dataLoaderSetup) {
        // Store original methods before any other interceptors
        const originalGet = router.get;
        const originalReload = router.reload;

        // Helper to wrap callbacks for data loading
        const wrapCallbacks = (options) => {
            if (!options.only || !Array.isArray(options.only) || options.only.length === 0) {
                return options;
            }

            isDataLoading.value = true;
            const originalOnFinish = options.onFinish;
            const originalOnSuccess = options.onSuccess;
            const originalOnError = options.onError;

            const hideLoader = () => {
                setTimeout(() => {
                    isDataLoading.value = false;
                }, 100);
            };

            options.onFinish = (...args) => {
                hideLoader();
                if (originalOnFinish) originalOnFinish(...args);
            };

            if (originalOnSuccess) {
                options.onSuccess = (...args) => {
                    hideLoader();
                    originalOnSuccess(...args);
                };
            }

            if (originalOnError) {
                options.onError = (...args) => {
                    hideLoader();
                    originalOnError(...args);
                };
            }

            return options;
        };

        // Wrap router.get to track data loading
        // Note: This works with useNavigationLoader's wrapper
        const currentGet = router.get;
        router.get = function(url, data = {}, options = {}) {
            const wrappedOptions = wrapCallbacks({ ...options });
            // Call the current router.get (which may be wrapped by useNavigationLoader)
            return currentGet.call(this, url, data, wrappedOptions);
        };

        // Wrap router.reload to track data loading
        const currentReload = router.reload;
        router.reload = function(options = {}) {
            const wrappedOptions = wrapCallbacks({ ...options });
            // Call the current router.reload
            return currentReload.call(this, wrappedOptions);
        };

        window.__dataLoaderSetup = true;
    }

    return {
        isDataLoading
    };
}

