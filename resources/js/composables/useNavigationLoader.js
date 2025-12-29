import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

// Global navigation state
const isNavigating = ref(false);
// Track if navigation was initiated by user click
let isUserClick = false;

/**
 * Composable to track Inertia navigation state
 * Only shows loader for user-initiated clicks, not background API calls
 */
export function useNavigationLoader() {
    // Setup listeners once globally
    if (!window.__navigationLoaderSetup) {
        // Track user clicks on links and buttons
        const handleClick = (event) => {
            const target = event.target;
            const link = target.closest('a[href]') || target.closest('button[type="button"]') || target.closest('[data-inertia]');
            
            // Check if it's a navigation link or button that will trigger navigation
            if (link) {
                const href = link.getAttribute('href');
                // If it's a link with href (not just # or javascript:)
                if (href && href !== '#' && !href.startsWith('javascript:') && !href.startsWith('mailto:') && !href.startsWith('tel:')) {
                    isUserClick = true;
                    // Reset after navigation completes
                    setTimeout(() => {
                        isUserClick = false;
                    }, 500);
                }
                // Check for Inertia Link component (has data-inertia attribute)
                else if (link.hasAttribute('data-inertia') || link.closest('[data-inertia]')) {
                    isUserClick = true;
                    setTimeout(() => {
                        isUserClick = false;
                    }, 500);
                }
            }
        };

        // Listen for clicks on document (capture phase to catch before Inertia handles it)
        if (typeof document !== 'undefined') {
            document.addEventListener('click', handleClick, true);
        }

        // Intercept router methods
        const originalVisit = router.visit;
        const originalGet = router.get;
        const originalPost = router.post;
        const originalPut = router.put;
        const originalPatch = router.patch;
        const originalDelete = router.delete;

        // Helper to show loader
        const showLoader = () => {
            isNavigating.value = true;
        };

        // Helper to hide loader with delay
        const hideLoader = () => {
            setTimeout(() => {
                isNavigating.value = false;
            }, 100);
        };

        // Wrap router.visit - only show loader for user-initiated navigation
        router.visit = function(url, options = {}) {
            // Check if this is a background call (should not show loader)
            const isBackground = options.preserveState === true && 
                                 options.only && 
                                 Array.isArray(options.only) && 
                                 options.only.length > 0;
            
            // Only show loader if:
            // 1. It was a user click (not programmatic)
            // 2. It's explicitly requested
            // 3. It's NOT a background call
            const shouldShowLoader = (isUserClick || options.showLoader === true) && !isBackground;
            
            if (shouldShowLoader) {
                showLoader();
            }
            
            const originalOnFinish = options.onFinish;
            const originalOnSuccess = options.onSuccess;
            const originalOnError = options.onError;
            const originalOnCancel = options.onCancel;

            options.onFinish = (...args) => {
                if (shouldShowLoader) {
                    hideLoader();
                }
                if (originalOnFinish) originalOnFinish(...args);
            };

            options.onSuccess = (...args) => {
                if (shouldShowLoader) {
                    hideLoader();
                }
                if (originalOnSuccess) originalOnSuccess(...args);
            };

            options.onError = (...args) => {
                if (shouldShowLoader) {
                    hideLoader();
                }
                if (originalOnError) originalOnError(...args);
            };

            options.onCancel = (...args) => {
                if (shouldShowLoader) {
                    hideLoader();
                }
                if (originalOnCancel) originalOnCancel(...args);
            };

            return originalVisit.call(this, url, options);
        };

        // Don't show loader for router.get (background calls)
        router.get = function(url, data = {}, options = {}) {
            // Only show if explicitly requested
            if (options.showLoader === true) {
                showLoader();
                const originalOnFinish = options.onFinish;
                options.onFinish = (...args) => {
                    hideLoader();
                    if (originalOnFinish) originalOnFinish(...args);
                };
            }
            return originalGet.call(this, url, data, options);
        };

        // Don't show loader for router.post (background/form submissions)
        router.post = function(url, data = {}, options = {}) {
            // Only show if explicitly requested
            if (options.showLoader === true) {
                showLoader();
                const originalOnFinish = options.onFinish;
                const originalOnSuccess = options.onSuccess;
                const originalOnError = options.onError;
                options.onFinish = (...args) => {
                    hideLoader();
                    if (originalOnFinish) originalOnFinish(...args);
                };
                options.onSuccess = (...args) => {
                    hideLoader();
                    if (originalOnSuccess) originalOnSuccess(...args);
                };
                options.onError = (...args) => {
                    hideLoader();
                    if (originalOnError) originalOnError(...args);
                };
            }
            return originalPost.call(this, url, data, options);
        };

        // Don't show loader for router.put (background)
        router.put = function(url, data = {}, options = {}) {
            if (options.showLoader === true) {
                showLoader();
                const originalOnFinish = options.onFinish;
                options.onFinish = (...args) => {
                    hideLoader();
                    if (originalOnFinish) originalOnFinish(...args);
                };
            }
            return originalPut.call(this, url, data, options);
        };

        // Don't show loader for router.patch (background)
        router.patch = function(url, data = {}, options = {}) {
            if (options.showLoader === true) {
                showLoader();
                const originalOnFinish = options.onFinish;
                options.onFinish = (...args) => {
                    hideLoader();
                    if (originalOnFinish) originalOnFinish(...args);
                };
            }
            return originalPatch.call(this, url, data, options);
        };

        // Don't show loader for router.delete (background)
        router.delete = function(url, options = {}) {
            if (options.showLoader === true) {
                showLoader();
                const originalOnFinish = options.onFinish;
                options.onFinish = (...args) => {
                    hideLoader();
                    if (originalOnFinish) originalOnFinish(...args);
                };
            }
            return originalDelete.call(this, url, options);
        };

        window.__navigationLoaderSetup = true;
    }

    return {
        isNavigating
    };
}

