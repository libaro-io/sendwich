import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import mitt from 'mitt'
import Toast, { POSITION } from "vue-toastification";
import "vue-toastification/dist/index.css";

/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core';

/* import specific icons */
import { faTrash } from '@fortawesome/free-solid-svg-icons';

/* Import permissions */
import LaravelPermissionToVueJS from 'laravel-permission-to-vuejs'

/* add icons to the library */
library.add(faTrash);

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';
const emitter = mitt()

const Inert = createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, app, props, plugin }) {
        const VueApp = createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(LaravelPermissionToVueJS)
            .use(Toast, {
                position: POSITION.BOTTOM_LEFT,
            });

        VueApp.config.globalProperties.emitter = emitter;
        VueApp.mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
