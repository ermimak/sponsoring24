import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue, route } from 'ziggy-js'; // Import ZiggyVue and route as named exports
import { Ziggy } from './ziggy'; // Import Ziggy configuration
import i18n from './i18n';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy) // Integrate ZiggyVue with Ziggy config
            .use(i18n);

        // Make route available globally
        app.config.globalProperties.$route = route;

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});