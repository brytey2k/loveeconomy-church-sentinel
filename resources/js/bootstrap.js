import axios from 'axios';
import $ from 'jquery';
import 'bootstrap';
import 'admin-lte';
import { createApp, h } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'

window.axios = axios;
window.$ = window.jQuery = $;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Helper: close the AdminLTE sidebar on small screens
function closeSidebarOnSmallScreens() {
    try {
        const width = window.innerWidth || document.documentElement.clientWidth;
        if (width < 992) { // AdminLTE switches to overlay sidebar below 992px
            const body = document.body.classList;
            // Remove the "open" state if present
            if (body.contains('sidebar-open')) {
                body.remove('sidebar-open');
            }
            // Ensure it's collapsed
            if (!body.contains('sidebar-collapse')) {
                body.add('sidebar-collapse');
            }
            // Remove potential overlay if AdminLTE added one
            document.querySelectorAll('.sidebar-overlay, .control-sidebar-slide-open').forEach(el => {
                if (el instanceof HTMLElement && el !== document.body) {
                    el.classList.remove('sidebar-overlay', 'control-sidebar-slide-open');
                }
            });
        }
    } catch (e) {
        // noop: defensive guard, we don't want to break navigation
    }
}

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin);

        // Close the sidebar after every Inertia navigation on small screens
        // Inertia v2 router event
        router.on('navigate', () => {
            closeSidebarOnSmallScreens();
        });
        // Fallback to global event listener (works across versions)
        window.addEventListener('inertia:navigate', closeSidebarOnSmallScreens);

        app.mount(el)
    },
    progress: {
        delay: 1,
        color: '#29d',
        includeCSS: true,
        showSpinner: true,
    },
})
