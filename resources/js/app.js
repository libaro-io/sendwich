import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';
import mitt from 'mitt'
import Toast, { POSITION } from "vue-toastification";
import "vue-toastification/dist/index.css";

/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core';

/* import specific icons */
import {
    faTrash,
    faBasketShopping,
    faShield,
    faClock,
    faUtensils,
    faArrowRight,
    faXmark,
    faArrowLeft,
    faPaperPlane,
    faPersonRunning,
    faChevronDown,
    faBars,
    faStore,
} from '@fortawesome/free-solid-svg-icons';

/* Import permissions */
import LaravelPermissionToVueJS from 'laravel-permission-to-vuejs'

/* add icons to the library */
library.add(
    faTrash,
    faBasketShopping,
    faShield,
    faClock,
    faUtensils,
    faArrowRight,
    faXmark,
    faArrowLeft,
    faPaperPlane,
    faPersonRunning,
    faChevronDown,
    faBars,
    faStore,
);

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';
const emitter = mitt()

// On mobile most action buttons sit at the bottom of the screen, so show toasts
// at the top there to avoid covering them. (Matches vue-toastification's 600px
// full-width mobile breakpoint; evaluated once at load.)
const toastPosition = window.matchMedia('(max-width: 600px)').matches
    ? POSITION.TOP_CENTER
    : POSITION.BOTTOM_LEFT;

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const VueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(LaravelPermissionToVueJS)
            .use(Toast, {
                position: toastPosition,
            });

        VueApp.config.globalProperties.emitter = emitter;
        VueApp.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
