import axios from 'axios';

const baseURL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000';

const api = axios.create({
    baseURL: baseURL,
    withCredentials: true,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    }
});

// Get CSRF token function
const getCSRFToken = () => {
    const metaTag = document.head.querySelector('meta[name="csrf-token"]');
    if (metaTag) return metaTag.content;
    if (window.Laravel?.csrfToken) return window.Laravel.csrfToken;
    return null;
};

// Add CSRF token to requests
api.interceptors.request.use(config => {
    const token = getCSRFToken();
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token;
    }
    
    // Add Authorization token if it exists
    const authToken = localStorage.getItem('token');
    if (authToken) {
        config.headers['Authorization'] = `Bearer ${authToken}`;
    }
    
    return config;
}, error => {
    return Promise.reject(error);
});

// Handle response errors globally
api.interceptors.response.use(
    response => response,
    error => {
        console.error('API Error:', error.response || error);
        
        if (error.response?.status === 401) {
            // Unauthorized - clear token and redirect to login
            localStorage.removeItem('token');
            if (window.location.pathname !== '/login') {
                window.location.href = '/login';
            }
        }
        return Promise.reject(error);
    }
);

export default api;