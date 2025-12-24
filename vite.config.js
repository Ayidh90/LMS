import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    // Check specific libraries first (before more general patterns)
                    
                    // Split ApexCharts into its own chunk (check before vue)
                    if (id.includes('apexcharts') || id.includes('vue3-apexcharts')) {
                        return 'apexcharts';
                    }
                    // Split PrimeVue into its own chunk (check before vue)
                    if (id.includes('primevue')) {
                        return 'primevue';
                    }
                    // Split Bootstrap into its own chunk
                    if (id.includes('bootstrap')) {
                        return 'bootstrap';
                    }
                    // Split Inertia into its own chunk
                    if (id.includes('@inertiajs')) {
                        return 'inertia';
                    }
                    // Split Ziggy (route helper) into its own chunk
                    if (id.includes('ziggy')) {
                        return 'ziggy';
                    }
                    // Split Vue core and Vue-related libraries into their own chunk
                    // (check after primevue and vue3-apexcharts to avoid conflicts)
                    if (id.includes('node_modules/vue') || id.includes('@vue')) {
                        return 'vue';
                    }
                    // Split remaining node_modules into vendor chunk
                    if (id.includes('node_modules')) {
                        return 'vendor';
                    }
                },
            },
        },
        chunkSizeWarningLimit: 1000, // Increase warning limit to 1MB
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
    },
});

