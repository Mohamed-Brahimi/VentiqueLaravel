<template>
    <div class="auth-container">
        <div class="auth-card">
            <div class="card-header auth-card-header">Inscription</div>

            <div class="card-body auth-card-body">
                <div class="alert alert-danger" v-if="error">
                    {{ error }}
                </div>

                <form @submit.prevent="handleRegister">
                    <div class="mb-4">
                        <label for="name" class="auth-form-label">Nom</label>
                        <input 
                            id="name"
                            type="text" 
                            class="form-control auth-form-control" 
                            v-model="name" 
                            required 
                            autocomplete="name"
                            autofocus
                        >
                    </div>

                    <div class="mb-4">
                        <label for="email" class="auth-form-label">Courriel</label>
                        <input 
                            id="email"
                            type="email" 
                            class="form-control auth-form-control" 
                            v-model="email" 
                            required
                            autocomplete="email"
                        >
                    </div>

                    <div class="mb-4">
                        <label for="password" class="auth-form-label">Mot de passe</label>
                        <input 
                            id="password"
                            type="password" 
                            class="form-control auth-form-control" 
                            v-model="password" 
                            required
                            autocomplete="new-password"
                        >
                    </div>

                    <div class="mb-4">
                        <label for="password-confirm" class="auth-form-label">Confirmation de mot de passe</label>
                        <input 
                            id="password-confirm"
                            type="password" 
                            class="form-control auth-form-control" 
                            v-model="c_password" 
                            required
                            autocomplete="new-password"
                        >
                    </div>

                    <div class="mb-4">
                        <!-- reCAPTCHA widget container -->
                        <div ref="recaptchaWrapper"></div>
                    </div>

                    <div class="mb-0">
                        <button type="submit" class="btn auth-btn-primary" :disabled="loading">
                            {{ loading ? 'Inscription...' : 'S\'inscrire' }}
                        </button>
                        
                        <router-link to="/login" class="auth-btn-link">
                            Vous avez déjà un compte? Se connecter
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

const name = ref('');
const email = ref('');
const password = ref('');
const c_password = ref('');
const error = ref(null);
const loading = ref(false);

const recaptchaWidgetId = ref(null);
const recaptchaToken = ref(null);
const recaptchaWrapper = ref(null);

const SITE_KEY = import.meta.env.VITE_RECAPTCHA_SITE_KEY || 
                 window.RECAPTCHA_SITE_KEY || 
                 '6LeoKB0sAAAAAJpRB-f3AYCZKLDn9v1H-wJd0wYf';

console.log('reCAPTCHA Site Key:', SITE_KEY);
console.log('API Base URL:', api.defaults.baseURL);

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

async function handleRegister() {
    error.value = null;
    
    if (!recaptchaToken.value) {
        error.value = 'Merci de confirmer que vous n\'êtes pas un robot.';
        return;
    }

    if (password.value !== c_password.value) {
        error.value = 'Les mots de passe ne correspondent pas.';
        return;
    }

    loading.value = true;
    
    try {
        console.log('Getting CSRF cookie...');
        await api.get('/sanctum/csrf-cookie');
        console.log('CSRF cookie obtained');

        console.log('Sending registration request...');
        const res = await api.post('/api/register', {
            name: name.value,
            email: email.value,
            password: password.value,
            c_password: c_password.value,
            'g-recaptcha-response': recaptchaToken.value
        });

        console.log('Registration response:', res.data);

        if (res.data.success) {
            const token = res.data.data?.token;
            if (token) {
                localStorage.setItem('token', token);
            }
            
            alert('Inscription réussie! Vous allez être redirigé vers la page de connexion.');
            router.push('/login');
        } else {
            error.value = res.data.message || 'Erreur lors de l\'inscription';
        }
    } catch (err) {
        console.error('Registration error:', err);
        console.error('Error response:', err.response);
        
        if (err.response?.status === 422) {
            const errors = err.response.data.errors || {};
            error.value = Object.values(errors).flat().join(' ');
        } else if (err.response?.data?.message) {
            error.value = err.response.data.message;
        } else if (err.message) {
            error.value = `Erreur: ${err.message}`;
        } else {
            error.value = 'Erreur lors de l\'inscription. Veuillez réessayer.';
        }
    } finally {
        loading.value = false;
        
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
    max-width: 500px;
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
    font-size: 1rem;
}

.auth-form-control {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #2e1d19;
    border-radius: 8px;
    background-color: #f9e0bb;
    color: #2e1d19;
    font-size: 1rem;
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