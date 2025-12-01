<template>
    <div class="auth-container">
        <div class="auth-card">
            <div class="card-header auth-card-header">Connexion</div>

            <div class="card-body auth-card-body">
                <div class="alert alert-danger" v-if="error">
                    {{ error }}
                </div>

                <form @submit.prevent="handleLogin">
                    <div class="mb-4">
                        <label for="email" class="auth-form-label">Email</label>
                        <input
                            id="email"
                            type="email"
                            class="form-control auth-form-control"
                            name="email"
                            v-model="email"
                            required
                            autocomplete="email"
                            autofocus
                        />
                    </div>

                    <div class="mb-4">
                        <label for="password" class="auth-form-label">Mot de passe</label>
                        <input
                            id="password"
                            type="password"
                            class="form-control auth-form-control"
                            name="password"
                            v-model="password"
                            required
                            autocomplete="current-password"
                        />
                    </div>

                    <div class="form-check mb-4">
                        <input
                            class="form-check-input auth-form-check-input"
                            type="checkbox"
                            name="remember"
                            id="remember"
                            v-model="remember"
                        />
                        <label class="form-check-label auth-form-check-label" for="remember">
                            Se souvenir de moi
                        </label>
                    </div>

                    <div class="mb-4">
                        <!-- reCAPTCHA widget container -->
                        <div ref="recaptchaWrapper"></div>
                    </div>

                    <div class="mb-0">
                        <button type="submit" class="btn auth-btn-primary" :disabled="loading">
                            {{ loading ? 'Connexion...' : 'Se connecter' }}
                        </button>
                        
                        <router-link to="/register" class="auth-btn-link">
                            Pas encore de compte? S'inscrire
                        </router-link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import api from '../axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const email = ref('');
const password = ref('');
const remember = ref(false);
const error = ref(null);
const loading = ref(false);

const recaptchaWidgetId = ref(null);
const recaptchaToken = ref(null);
const recaptchaWrapper = ref(null);

const SITE_KEY = import.meta.env.VITE_RECAPTCHA_SITE_KEY || 
                 window.RECAPTCHA_SITE_KEY || 
                 '6LeoKB0sAAAAAJpRB-f3AYCZKLDn9v1H-wJd0wYf';

console.log('reCAPTCHA Site Key:', SITE_KEY);

function renderRecaptcha() {
    if (!window.grecaptcha || !recaptchaWrapper.value) {
        console.log('reCAPTCHA not ready or wrapper missing');
        return;
    }
    
    if (!SITE_KEY || SITE_KEY.includes('example')) {
        console.error('reCAPTCHA site key is missing or invalid');
        error.value = 'Configuration reCAPTCHA manquante';
        return;
    }
    
    if (recaptchaWidgetId.value !== null) {
        try {
            window.grecaptcha.reset(recaptchaWidgetId.value);
        } catch (e) {
            console.error('reCAPTCHA reset error:', e);
        }
        return;
    }
    
    try {
        recaptchaWidgetId.value = window.grecaptcha.render(recaptchaWrapper.value, {
            'sitekey': SITE_KEY,
            'callback': (token) => {
                recaptchaToken.value = token;
                console.log('reCAPTCHA token received');
            },
            'expired-callback': () => {
                recaptchaToken.value = null;
                console.log('reCAPTCHA token expired');
            }
        });
        console.log('reCAPTCHA rendered successfully');
    } catch (e) {
        console.error('reCAPTCHA render error:', e);
        error.value = 'Erreur de chargement reCAPTCHA';
    }
}

onMounted(() => {
    let tries = 0;
    const interval = setInterval(() => {
        if (window.grecaptcha && window.grecaptcha.render) {
            renderRecaptcha();
            clearInterval(interval);
        } else if (tries++ > 20) {
            clearInterval(interval);
            console.warn('reCAPTCHA not loaded after 6 seconds');
            error.value = 'reCAPTCHA n\'a pas pu être chargé';
        }
    }, 300);
});

onBeforeUnmount(() => {
    if (recaptchaWidgetId.value !== null && window.grecaptcha && window.grecaptcha.reset) {
        try {
            window.grecaptcha.reset(recaptchaWidgetId.value);
        } catch (e) {
            console.error('reCAPTCHA cleanup error:', e);
        }
    }
});

async function handleLogin() {
    error.value = null;
    
    if (!recaptchaToken.value) {
        error.value = 'Merci de confirmer que vous n\'êtes pas un robot.';
        return;
    }
    
    loading.value = true;

    try {
        console.log('Getting CSRF cookie...');
        await api.get('/sanctum/csrf-cookie');
        console.log('CSRF cookie obtained');

        console.log('Attempting login...');
        const res = await api.post('/api/login', {
            email: email.value,
            password: password.value,
            'g-recaptcha-response': recaptchaToken.value
        });

        console.log('Login response:', res.data);

        if (res.data.success && res.data.data) {
            const token = res.data.data.token;
            if (token) {
                localStorage.setItem('token', token);
                api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            }
            
            try {
                const userResponse = await api.get('/api/user');
                const userData = userResponse.data;
                localStorage.setItem('user', JSON.stringify(userData));
                
                if (window.Laravel) {
                    window.Laravel.isLoggedIn = true;
                    window.Laravel.user = userData;
                }
                
                console.log('User data stored:', userData);
            } catch (userErr) {
                console.error('Error fetching user data:', userErr);
            }
            
            console.log('Login successful, redirecting...');
            alert('Connexion réussie!');
            
            window.location.reload();
            
        } else {
            error.value = res.data.message || 'Erreur de connexion';
        }
    } catch (err) {
        console.error('Login error:', err);
        console.error('Error response:', err.response);
        
        if (err.response?.status === 401) {
            error.value = 'Email ou mot de passe incorrect';
        } else if (err.response?.status === 422) {
            const errors = err.response.data.errors || {};
            error.value = Object.values(errors).flat().join(' ');
        } else if (err.response?.data?.message) {
            error.value = err.response.data.message;
        } else {
            error.value = 'Erreur de connexion au serveur';
        }
    } finally {
        loading.value = false;
        
        // Reset reCAPTCHA
        if (window.grecaptcha && recaptchaWidgetId.value !== null) {
            try {
                window.grecaptcha.reset(recaptchaWidgetId.value);
                recaptchaToken.value = null;
            } catch (e) {
                console.error('reCAPTCHA reset error:', e);
            }
        }
    }
}
</script>

<style scoped>
.auth-container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
    min-height: calc(100vh - 200px);
    background-color: #f9e0bb;
}

.auth-card {
    width: 100%;
    max-width: 420px;
    background: #f9e0bb;
    border: 3px solid #2e1d19;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(46, 29, 25, 0.1);
    position: relative;
    z-index: 1;
}

.auth-card-header {
    background-color: #2e1d19;
    color: #edc58a;
    font-weight: bold;
    font-size: 1.5rem;
    text-align: center;
    padding: 1.5rem;
    border-radius: 8px 8px 0 0;
    border-bottom: 2px solid #725042;
}

.auth-card-body {
    padding: 2rem;
    overflow: visible;
}

.auth-form-label {
    display: block;
    margin-bottom: 0.5rem;
    color: #2e1d19;
    font-weight: bold;
}

.auth-form-control {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #2e1d19;
    border-radius: 8px;
    background-color: #f9e0bb;
    color: #2e1d19;
    transition: all 0.3s ease;
}

.auth-form-control:focus {
    background-color: #ffffff;
    border-color: #725042;
    box-shadow: 0 0 0 3px rgba(237, 197, 138, 0.3);
    outline: none;
}

.auth-btn-primary {
    background-color: #2e1d19;
    border: 2px solid #2e1d19;
    color: #edc58a;
    font-weight: bold;
    font-size: 1.1rem;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    text-transform: uppercase;
    width: 100%;
    cursor: pointer;
    margin-bottom: 1rem;
}

.auth-btn-primary:hover:not(:disabled) {
    background-color: #725042;
    border-color: #725042;
    color: #f9e0bb;
    transform: translateY(-2px);
}

.auth-btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.auth-btn-link {
    color: #2e1d19;
    text-decoration: none;
    font-weight: bold;
    display: block;
    text-align: center;
    padding: 0.5rem;
    transition: color 0.3s ease;
}

.auth-btn-link:hover {
    color: #725042;
    text-decoration: underline;
}

.alert-danger {
    background-color: #f8d7da;
    border: 2px solid #dc3545;
    color: #721c24;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    font-weight: bold;
}

.form-check {
    margin-bottom: 1rem;
}

.auth-form-check-label {
    color: #2e1d19;
    font-weight: 500;
    margin-left: 0.5rem;
}

.auth-form-check-input:checked {
    background-color: #2e1d19;
    border-color: #2e1d19;
}

.mb-4 {
    margin-bottom: 1.5rem;
}

.mb-0 {
    margin-bottom: 0;
}

.mb-4:has([ref="recaptchaWrapper"]) {
    margin-bottom: 2rem;
    min-height: 80px;
    overflow: visible;
}

[ref="recaptchaWrapper"] {
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: visible !important;
    transform: scale(1);
    transform-origin: center;
}

:deep(iframe[src*="recaptcha"]) {
    overflow: visible !important;
    position: relative !important;
    z-index: 1000 !important;
}

:deep(.g-recaptcha) {
    overflow: visible !important;
    transform: scale(1);
    transform-origin: 0 0;
}

:deep(body > div[style*="z-index"]) {
    z-index: 10000 !important;
}
</style>