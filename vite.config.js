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
    // Ultra-optimized build for low-memory environments
    build: {
        // Reduce chunk size to prevent memory issues
        chunkSizeWarningLimit: 500,
        // Optimize CSS to reduce memory usage
        cssCodeSplit: true,
        // Disable source maps in production to save memory
        sourcemap: false,
        // Minimize asset inlining to reduce memory pressure
        assetsInlineLimit: 4096, // 4kb
        // Use the fastest minifier
        minify: 'esbuild',
        // Optimize rollup options for minimal memory usage
        rollupOptions: {
            treeshake: true,
            output: {
                // Simplify chunk naming for faster builds
                entryFileNames: 'assets/[name].js',
                chunkFileNames: 'assets/[name].js',
                assetFileNames: 'assets/[name].[ext]',
                // Use manual chunks for better memory efficiency
                manualChunks: (id) => {
                    if (id.includes('node_modules')) {
                        return 'vendor';
                    }
                }
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
            // Allow overriding asset base URL (useful for Plesk Site Preview). If unset, defaults to absolute /build paths.
            assetUrl: process.env.ASSET_URL || undefined,
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
