import { createApp } from 'vue';
import { createPinia } from 'pinia';
import axios from 'axios';
import App from './App.vue';
import router from './router/index.js';
import './style.css';

// Konfiguracja URL backendu API z kontenera Laravel
axios.defaults.baseURL = import.meta.env.VITE_API_URL || 'http://localhost:8900/api';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

const token = localStorage.getItem('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

const app = createApp(App);
app.use(createPinia());
app.use(router);
app.mount('#app');