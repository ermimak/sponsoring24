import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import ZiggyPlugin from './ziggy-plugin';
import axios from 'axios';
import i18n from './i18n';
import '@fortawesome/fontawesome-free/css/all.min.css';

// Configure axios to use the same protocol as the current page
axios.defaults.baseURL = window.location.origin;
// Force HTTP for local development
if (window.location.hostname === 'localhost') {
  axios.defaults.baseURL = 'http://localhost';
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyPlugin)
            .use(i18n);

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});