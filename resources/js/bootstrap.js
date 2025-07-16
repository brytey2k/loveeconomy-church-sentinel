import axios from 'axios';
import $ from 'jquery';
import 'bootstrap';
import 'admin-lte';
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

window.axios = axios;
window.$ = window.jQuery = $;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el)
    },
    progress: {
        delay: 1,
        color: '#29d',
        includeCSS: true,
        showSpinner: true,
    },
})
