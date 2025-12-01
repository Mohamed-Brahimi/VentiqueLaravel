<template>
    <Teleport to="body">
        <div v-if="show" class="modal-overlay" @click.self="closeModal">
            <div class="modal-content auth-modal">
                <button class="close-btn" @click="closeModal" type="button">×</button>
                
                <h2 class="modal-title">Inscription</h2>

                <div class="alert alert-danger" v-if="error">
                    {{ error }}
                </div>

                <form @submit.prevent="handleRegister">
                    <div class="form-group">
                        <label for="name" class="form-label">Nom</label>
                        <input 
                            id="name"
                            type="text" 
                            class="form-control" 
                            v-model="name" 
                            required 
                            autocomplete="name"
                            autofocus
                        >
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Courriel</label>
                        <input 
                            id="email"
                            type="email" 
                            class="form-control" 
                            v-model="email" 
                            required
                            autocomplete="email"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input 
                            id="password"
                            type="password" 
                            class="form-control" 
                            v-model="password" 
                            required
                            autocomplete="new-password"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password-confirm" class="form-label">Confirmation de mot de passe</label>
                        <input 
                            id="password-confirm"
                            type="password" 
                            class="form-control" 
                            v-model="c_password" 
                            required
                            autocomplete="new-password"
                        >
                    </div>

                    <div class="form-group">
                        <div ref="recaptchaWrapper"></div>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" class="btn btn-primary" :disabled="loading">
                            {{ loading ? 'Inscription...' : 'S\'inscrire' }}
                        </button>
                        
                        <a @click="switchToLogin" class="btn-link" style="cursor: pointer;">
                            Vous avez déjà un compte? Se connecter
                        </a>
                    </div>
                </form>
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

const emit = defineEmits(['close', 'success', 'switch-to-login']);

const name = ref('');
const email = ref('');
const password = ref('');
const c_password = ref('');
const error = ref(null);
const loading = ref(false);
const recaptchaWidgetId = ref(null);
const recaptchaToken = ref(null);
const recaptchaWrapper = ref(null);

const SITE_KEY = import.meta.env.VITE_RECAPTCHA_SITE_KEY || '6LeoKB0sAAAAAJpRB-f3AYCZKLDn9v1H-wJd0wYf';

watch(() => props.show, (newVal) => {
    if (newVal) {
        setTimeout(renderRecaptcha, 100);
    }
});

function renderRecaptcha() {
    if (!window.grecaptcha || !recaptchaWrapper.value) return;
    
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
            'callback': (token) => { recaptchaToken.value = token; },
            'expired-callback': () => { recaptchaToken.value = null; }
        });
    } catch (e) {
        console.error('reCAPTCHA render error:', e);
    }
}

function closeModal() {
    name.value = '';
    email.value = '';
    password.value = '';
    c_password.value = '';
    error.value = null;
    emit('close');
}

function switchToLogin() {
    emit('switch-to-login');
}

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
        await api.get('/sanctum/csrf-cookie');
        const res = await api.post('/api/register', {
            name: name.value,
            email: email.value,
            password: password.value,
            c_password: c_password.value,
            'g-recaptcha-response': recaptchaToken.value
        });

        if (res.data.success) {
            alert('Inscription réussie!');
            emit('success');
        } else {
            error.value = res.data.message || 'Erreur lors de l\'inscription';
        }
    } catch (err) {
        if (err.response?.status === 422) {
            const errors = err.response.data.errors || {};
            error.value = Object.values(errors).flat().join(' ');
        } else {
            error.value = 'Erreur lors de l\'inscription';
        }
    } finally {
        loading.value = false;
        if (window.grecaptcha && recaptchaWidgetId.value !== null) {
            window.grecaptcha.reset(recaptchaWidgetId.value);
            recaptchaToken.value = null;
        }
    }
}

onBeforeUnmount(() => {
    if (recaptchaWidgetId.value !== null && window.grecaptcha) {
        try {
            window.grecaptcha.reset(recaptchaWidgetId.value);
        } catch (e) {}
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
    overflow-y: auto;
}

.modal-content {
    background-color: #f9e0bb;
    border: 3px solid #2e1d19;
    border-radius: 12px;
    padding: 2rem;
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
}

.close-btn:hover {
    background-color: #725042;
    transform: rotate(90deg);
}

.modal-title {
    text-align: center;
    color: #2e1d19;
    font-size: 2rem;
    margin-bottom: 2rem;
    padding-right: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    color: #2e1d19;
    font-weight: bold;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #2e1d19;
    border-radius: 8px;
    background-color: #fff;
}

.form-control:focus {
    outline: none;
    border-color: #725042;
}

.form-buttons {
    margin-top: 2rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.btn {
    width: 100%;
    padding: 1rem;
    border: 2px solid;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 1rem;
}

.btn-primary {
    background-color: #2e1d19;
    border-color: #2e1d19;
    color: #f9e0bb;
}

.btn-primary:hover:not(:disabled) {
    background-color: #725042;
    transform: translateY(-2px);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-link {
    color: #2e1d19;
    text-align: center;
    text-decoration: underline;
}

.btn-link:hover {
    color: #725042;
}

.alert-danger {
    background-color: #f8d7da;
    border: 2px solid #dc3545;
    color: #721c24;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}
</style>