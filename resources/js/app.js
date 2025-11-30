import { createApp } from "vue";

import "./bootstrap.js";
import App from "./App.vue";
import axios from "axios";
import router from "./router";

const app = createApp(App);
app.config.globalProperties.$axios = axios;
app.use(router);

// wait for DOM if script may run before <div id="app"> is parsed
document.addEventListener("DOMContentLoaded", () => {
    app.mount("#app");
});
