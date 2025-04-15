import './bootstrap';
import '@/css/app.css';

import { createApp } from 'vue';
import App from './App.vue';
import router from './router/index';

const app = createApp(App)
    .use(router);

app.mount('#app');
