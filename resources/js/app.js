import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import ZiggyPlugin from './ziggy-plugin';
import axios from 'axios';
import i18n from './i18n';
import '@fortawesome/fontawesome-free/css/all.min.css';

// Import Vue Toastification
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';

// Toast options
const toastOptions = {
    position: 'top-right',
    timeout: 5000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: 'button',
    icon: true,
    rtl: false
};

// Configure axios to use the same protocol as the current page
axios.defaults.baseURL = window.location.origin;
// Force HTTP for local development
if (window.location.hostname === 'localhost' && window.location.port) {
    axios.defaults.baseURL = 'http://localhost:' + window.location.port;
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyPlugin)
            .use(i18n)
            .use(Toast, toastOptions);

        app.mount(el);
    },
    progress: {
        // color according to our brand color rgb between purple and blue #9333ea and #2563eb
        color: 'rgb(147, 51, 234)',
    },
});