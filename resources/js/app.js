import './bootstrap';
import '../css/app.css';

import {createApp, h} from 'vue';
import {createInertiaApp} from '@inertiajs/vue3';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {ZiggyVue} from '../../vendor/tightenco/ziggy/dist/vue.m';
import {OhVueIcon} from 'oh-vue-icons';
import {i18nVue} from 'laravel-vue-i18n';
import {VueReCaptcha} from 'vue-recaptcha-v3';

const appName = import.meta.env.VITE_APP_NAME || 'Poland Study';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`,
        import.meta.glob('./Pages/**/*.vue')),
    setup({el, App, props, plugin}) {
        const captchaKey = props.initialPage.props.recaptcha_site_key;
        
        return createApp({render: () => h(App, props)}).
            use(plugin).
            component('v-icon', OhVueIcon).
            use(ZiggyVue, Ziggy).
            use(VueReCaptcha, {siteKey: captchaKey}).
            use(i18nVue, {
                resolve: async lang => {
                    const langs = import.meta.glob('../../lang/*.json');
                    return await langs[`../../lang/${lang}.json`]();
                },
            }).
            mount(el);
    },
    progress: {
        // The delay after which the progress bar will appear, in milliseconds...
        delay: 250,

        // The color of the progress bar...
        color: '#4c5a9a',

        // Whether to include the default NProgress styles...
        includeCSS: true,

        // Whether the NProgress spinner will be shown...
        showSpinner: true,

    },
});
