<template>
    <Teleport to="body">
        <div v-if="show" class="modal-overlay" @click.self="closeModal">
            <div class="modal-content auth-modal">
                <button class="close-btn" @click="closeModal" type="button">×</button>
                
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
                                v-model="email"
                                required
                                autocomplete="email"
                            />
                        </div>

                        <div class="mb-4">
                            <label for="password" class="auth-form-label">Mot de passe</label>
                            <input
                                id="password"
                                type="password"
                                class="form-control auth-form-control"
                                v-model="password"
                                required
                                autocomplete="current-password"
                            />
                        </div>

                        <div class="form-check mb-4">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="remember"
                                v-model="remember"
                            />
                            <label class="form-check-label auth-form-check-label" for="remember">
                                Se souvenir de moi
                            </label>
                        </div>

                        <div class="mb-4">
                            <div ref="recaptchaWrapper"></div>
                        </div>

                        <div class="mb-0">
                            <button type="submit" class="btn auth-btn-primary" :disabled="loading">
                                {{ loading ? 'Connexion...' : 'Se connecter' }}
                            </button>
                            
                            <a @click="switchToRegister" class="auth-btn-link" style="cursor: pointer">
                                Pas encore de compte? S'inscrire
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, watch, onBeforeUnmount } from 'vue';
import api from '../axios';

const props = defineProps({
    show: { type: Boolean, required: true }
});

const emit = defineEmits(['close', 'success', 'switch-to-register']);

const email = ref('');
const password = ref('');
const remember = ref(false);
const error = ref(null);
const loading = ref(false);
const recaptchaWidgetId = ref(null);
const recaptchaToken = ref(null);
const recaptchaWrapper = ref(null);

const SITE_KEY = import.meta.env.VITE_RECAPTCHA_SITE_KEY || window.RECAPTCHA_SITE_KEY;

watch(() => props.show, (newVal) => {
    if (newVal) {
        setTimeout(() => renderRecaptcha(), 300);
    } else {
        resetForm();
    }
});

function renderRecaptcha() {
    if (!window.grecaptcha || !recaptchaWrapper.value || !SITE_KEY) return;
    
    if (recaptchaWidgetId.value !== null) {
        try {
            window.grecaptcha.reset(recaptchaWidgetId.value);
            return;
        } catch (e) {
            console.error('reCAPTCHA reset error:', e);
        }
    }
    
    try {
        recaptchaWidgetId.value = window.grecaptcha.render(recaptchaWrapper.value, {
            'sitekey': SITE_KEY,
            'callback': (token) => { recaptchaToken.value = token; },
            'expired-callback': () => { recaptchaToken.value = null; }
        });
    } catch (e) {
        console.error('reCAPTCHA render error:', e);
    }
}

function closeModal() {
    emit('close');
}

function switchToRegister() {
    emit('switch-to-register');
}

function resetForm() {
    email.value = '';
    password.value = '';
    remember.value = false;
    error.value = null;
    recaptchaToken.value = null;
}

async function handleLogin() {
    error.value = null;
    
    if (!recaptchaToken.value) {
        error.value = 'Merci de confirmer que vous n\'êtes pas un robot.';
        return;
    }
    
    loading.value = true;

    try {
        await api.get('/sanctum/csrf-cookie');
        const res = await api.post('/api/login', {
            email: email.value,
            password: password.value,
            'g-recaptcha-response': recaptchaToken.value
        });

        if (res.data.success && res.data.data) {
            const token = res.data.data.token;
            if (token) {
                localStorage.setItem('token', token);
                api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            }
            
            const userResponse = await api.get('/api/user');
            localStorage.setItem('user', JSON.stringify(userResponse.data));
            
            if (window.Laravel) {
                window.Laravel.isLoggedIn = true;
                window.Laravel.user = userResponse.data;
            }
            
            emit('success');
            closeModal();
            window.location.reload();
        } else {
            error.value = res.data.message || 'Erreur de connexion';
        }
    } catch (err) {
        if (err.response?.status === 401) {
            error.value = 'Email ou mot de passe incorrect';
        } else {
            error.value = err.response?.data?.message || 'Erreur de connexion';
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

onBeforeUnmount(() => {
    if (recaptchaWidgetId.value !== null && window.grecaptcha) {
        try {
            window.grecaptcha.reset(recaptchaWidgetId.value);
        } catch (e) {
            console.error('Cleanup error:', e);
        }
    }
});
</script>

<style scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    padding: 1rem;
}

.modal-content {
    background-color: #f9e0bb;
    border: 3px solid #2e1d19;
    border-radius: 12px;
    max-width: 500px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
}

.close-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: #2e1d19;
    color: #edc58a;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-size: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    z-index: 10;
}

.close-btn:hover {
    background-color: #725042;
    transform: rotate(90deg);
}

.auth-card-header {
    background-color: #2e1d19;
    color: #edc58a;
    font-weight: bold;
    font-size: 1.5rem;
    text-align: center;
    padding: 1.5rem;
    border-radius: 8px 8px 0 0;
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