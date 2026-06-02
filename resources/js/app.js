import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createI18n } from 'vue-i18n';
import ar from './i18n/ar.js';
import en from './i18n/en.js';

const appName = import.meta.env.VITE_APP_NAME || 'PDF Signature';

// Determine initial locale from localStorage, then HTML lang attribute, then default 'ar'
const htmlLocale = document.documentElement.lang?.split('-')[0] || 'ar';
const locale     = localStorage.getItem('locale') || htmlLocale || 'ar';

// Sync dir on initial load
document.documentElement.lang = locale;
document.documentElement.dir  = locale === 'ar' ? 'rtl' : 'ltr';

const i18n = createI18n({
    legacy:  false,
    locale,
    fallbackLocale: 'en',
    messages: { ar, en },
});

createInertiaApp({
    title: (title) => title ? `${title} — ${appName}` : appName,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18n)
            .mount(el);
    },
    progress: {
        color: '#1B4F72',
    },
});
