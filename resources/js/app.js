import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp, Head, Link } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import ToastContainer from './Components/ToastContainer.vue';
import ConfirmModal from './Components/ConfirmModal.vue';

createInertiaApp({
    title: (title) => title ? `${title} - PLN UP Sengkang` : 'Sistem Koordinasi UP Sengkang',
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({
            render: () => h('div', { id: 'app-wrapper', class: 'contents' }, [
                h(App, props),
                h(ToastContainer),
                h(ConfirmModal),
            ]),
        })
            .use(plugin)
            .component('Link', Link)
            .component('Head', Head)
            .mount(el);
    },
    progress: {
        color: '#2563eb',
        showSpinner: true,
    },
});
