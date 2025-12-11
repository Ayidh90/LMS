import './bootstrap';
import '../css/app.css';

// Bootstrap JS
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { Ziggy } from './ziggy';
import { route as ziggyRoute } from '../../vendor/tightenco/ziggy/dist/index.esm.js';

// PrimeVue
import PrimeVue from 'primevue/config';
import Button from 'primevue/button';
import Card from 'primevue/card';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';
import Checkbox from 'primevue/checkbox';
import Badge from 'primevue/badge';
import Tag from 'primevue/tag';
import Panel from 'primevue/panel';
import Divider from 'primevue/divider';
import ProgressBar from 'primevue/progressbar';
import Avatar from 'primevue/avatar';
import AvatarGroup from 'primevue/avatargroup';
import Sidebar from 'primevue/sidebar';
import Menu from 'primevue/menu';
import Menubar from 'primevue/menubar';
import Toast from 'primevue/toast';
import ToastService from 'primevue/toastservice';
import ConfirmationService from 'primevue/confirmationservice';
import ConfirmDialog from 'primevue/confirmdialog';
import Dialog from 'primevue/dialog';

// ApexCharts
import VueApexCharts from 'vue3-apexcharts';

const appName = import.meta.env.VITE_APP_NAME || 'LMS';

// Make route function available globally
window.route = (name, params, absolute) => {
    return ziggyRoute(name, params, absolute, Ziggy);
};

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(PrimeVue, {
                ripple: true,
                inputStyle: 'outlined'
            })
            .use(ToastService)
            .use(ConfirmationService)
            .component('Button', Button)
            .component('Card', Card)
            .component('DataTable', DataTable)
            .component('Column', Column)
            .component('InputText', InputText)
            .component('InputNumber', InputNumber)
            .component('Textarea', Textarea)
            .component('Dropdown', Dropdown)
            .component('Checkbox', Checkbox)
            .component('Badge', Badge)
            .component('Tag', Tag)
            .component('Panel', Panel)
            .component('Divider', Divider)
            .component('ProgressBar', ProgressBar)
            .component('Avatar', Avatar)
            .component('AvatarGroup', AvatarGroup)
            .component('Sidebar', Sidebar)
            .component('Menu', Menu)
            .component('Menubar', Menubar)
            .component('Toast', Toast)
            .component('ConfirmDialog', ConfirmDialog)
            .component('Dialog', Dialog)
            .use(VueApexCharts);
        
        // Make route available in template context
        app.config.globalProperties.route = window.route;
        
        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

