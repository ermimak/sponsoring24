import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

const isProduction = process.env.NODE_ENV === 'production';
const isLocal = process.env.APP_ENV === 'local' || !process.env.APP_ENV;

// Get host and protocol from APP_URL or use defaults
let appUrl = process.env.APP_URL || (isLocal ? 'http://localhost' : 'https://sponsoring24.onrender.com');
const urlParts = new URL(appUrl);
const host = urlParts.hostname;
const protocol = urlParts.protocol.replace(':', '');

export default defineConfig({
    // Optimize build for production environments
    build: {
        // Reduce chunk size to prevent memory issues
        chunkSizeWarningLimit: 1000,
        // Optimize CSS to reduce memory usage
        cssCodeSplit: true,
        // Reduce memory usage by disabling source maps in production
        sourcemap: !isProduction,
        // Optimize rollup options
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    // Split vendor chunks to reduce memory pressure
                    if (id.includes('node_modules')) {
                        if (id.includes('@fortawesome')) {
                            return 'vendor-fortawesome';
                        }
                        if (id.includes('vue')) {
                            return 'vendor-vue';
                        }
                        return 'vendor';
                    }
                },
            },
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
            buildDirectory: 'build',
            publicDirectory: 'public',
            assetUrl: `${protocol}://${host}`,
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
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: host,
            protocol: isProduction ? 'wss' : 'ws',
            clientPort: isLocal ? 5173 : undefined,
        },
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            '~': path.resolve(__dirname, './resources/css'),
            '/vendor/tightenco/ziggy': path.resolve(__dirname, './vendor/tightenco/ziggy/dist'),
            '@fortawesome': '/node_modules/@fortawesome',
            'ziggy/vue.m': path.resolve(__dirname, './resources/js/ziggy-vue.js'),
        },
    },
    optimizeDeps: {
        include: ['ziggy-js'],
    },
});
