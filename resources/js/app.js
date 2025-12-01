import { createApp } from "vue";

import "./bootstrap.js";
import App from "./App.vue";
import axios from "axios";
import router from "./router";

// Configure axios
axios.defaults.withCredentials = true;
axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL || window.location.origin;

const app = createApp(App);
app.config.globalProperties.$axios = axios;
app.use(router);

// Wait for DOM to be ready before mounting
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        const appElement = document.getElementById('app');
        if (appElement) {
            app.mount('#app');
        } else {
            console.error('Could not find #app element to mount Vue app');
        }
    });
} else {
    // DOM is already ready
    const appElement = document.getElementById('app');
    if (appElement) {
        app.mount('#app');
    } else {
        console.error('Could not find #app element to mount Vue app');
    }
}
