import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

const isProduction = process.env.NODE_ENV === 'production';
const isLocal = process.env.APP_ENV === 'local' || !process.env.APP_ENV;
const host = isLocal ? 'localhost' : 'fundoo.onrender.com';
const protocol = isLocal ? 'http' : 'https';

export default defineConfig({
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
