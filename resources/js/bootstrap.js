import _ from "lodash";
window._ = _;

// Attempt to load Bootstrap JS (dynamic import so failures are non-fatal)
import("bootstrap").catch(() => {});

/**
 * Load axios as an ES module so the browser / Vite dev server receives
 * proper ESM code instead of CommonJS `require` calls.
 */
import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.axios.defaults.withCredentials = true;
const getCSRFToken = () => {
    const metaTag = document.head.querySelector("meta[name=\"csrf-token\"]");
    if (metaTag) {
        return metaTag.content;
    }
    if (window.Laravel && window.Laravel.csrfToken) {
        return window.Laravel.csrfToken;
    }
    return null;
};

const token = getCSRFToken();

if (token) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token;
} else {
    console.warn("CSRF token not found");
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = await import('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
