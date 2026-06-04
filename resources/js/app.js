import './bootstrap';
import { createApp } from 'vue';
import router from './router';
import RootApp from './App.vue';

const app = createApp(RootApp);

app.use(router);

app.mount('#app')
