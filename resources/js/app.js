import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp, Head, Link, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import ToastContainer from './Components/ToastContainer.vue';
import ConfirmModal from './Components/ConfirmModal.vue';

// =========================================================================
// GLOBAL SAFEGUARD: Single-Click & Anti-Double-Submit Protection
// Mencegah tabrakan request database (race condition / double insert)
// =========================================================================
let lastSubmitTimestamp = 0;
let lastSubmitUrl = '';

if (typeof window !== 'undefined') {
    // 1. Intersep klik tombol submit di fase CAPTURE (sebelum event handler komponen berjalan)
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('button[type="submit"], input[type="submit"], button.btn-submit');
        if (!btn) return;

        const now = Date.now();

        // Jika tombol sudah berstatus submitting, disabled, atau diklik dalam jeda < 900ms
        if (btn.disabled || btn.dataset.submitting === 'true' || (now - lastSubmitTimestamp < 900)) {
            e.preventDefault();
            e.stopImmediatePropagation();
            return;
        }

        lastSubmitTimestamp = now;
        btn.dataset.submitting = 'true';
        btn.classList.add('pointer-events-none', 'opacity-70');

        // Batas waktu pemulihan otomatis jika request tidak memicu event finish
        setTimeout(() => {
            delete btn.dataset.submitting;
            btn.classList.remove('pointer-events-none', 'opacity-70');
        }, 3500);
    }, true);

    // 2. Intersep form submit event di fase CAPTURE
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (!form) return;

        if (form.dataset.formSubmitting === 'true') {
            e.preventDefault();
            e.stopImmediatePropagation();
            return;
        }

        form.dataset.formSubmitting = 'true';
        setTimeout(() => {
            delete form.dataset.formSubmitting;
        }, 3000);
    }, true);

    // 3. Intersep event Inertia Router sebelum request dikirim
    router.on('before', (event) => {
        const visit = event.detail?.visit || {};
        const method = String(visit.method || 'get').toLowerCase();

        // Khusus mutasi database: POST, PUT, PATCH, DELETE
        if (['post', 'put', 'patch', 'delete'].includes(method)) {
            const now = Date.now();
            const targetUrl = String(visit.url || '');

            // Kecualikan endpoint polling / ping atau otentikasi login
            if (targetUrl.includes('/ping') || targetUrl.includes('/login')) {
                return;
            }

            // Jika ada request mutasi identik dikirim dalam jeda < 1000ms, batalkan duplikatnya
            if (targetUrl === lastSubmitUrl && (now - lastSubmitTimestamp < 1000)) {
                event.preventDefault();
                return false;
            }

            lastSubmitUrl = targetUrl;
            lastSubmitTimestamp = now;
        }
    });

    // 4. Pulihkan gembok submit setelah request Inertia selesai
    router.on('finish', () => {
        document.querySelectorAll('[data-submitting="true"]').forEach((el) => {
            delete el.dataset.submitting;
            el.classList.remove('pointer-events-none', 'opacity-70');
        });
        document.querySelectorAll('[data-form-submitting="true"]').forEach((el) => {
            delete el.dataset.formSubmitting;
        });
    });
}

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
