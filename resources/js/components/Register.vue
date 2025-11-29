<template>
    <div class="auth-container">
        <div class="auth-card">
            <div class="card-header auth-card-header">{{ title }}</div>

            <div class="card-body auth-card-body">
                <form :action="action" method="POST">
                    <input type="hidden" name="_token" :value="csrfToken" />

                    <div class="mb-4">
                        <label for="name" class="auth-form-label">{{
                            labels.name
                        }}</label>
                        <input
                            id="name"
                            type="text"
                            class="form-control auth-form-control"
                            name="name"
                            v-model="form.name"
                            required
                            autocomplete="name"
                            autofocus
                        />
                    </div>

                    <div class="mb-4">
                        <label for="email" class="auth-form-label">{{
                            labels.email
                        }}</label>
                        <input
                            id="email"
                            type="email"
                            class="form-control auth-form-control"
                            name="email"
                            v-model="form.email"
                            required
                            autocomplete="email"
                        />
                    </div>

                    <div class="mb-4">
                        <label for="password" class="auth-form-label">{{
                            labels.password
                        }}</label>
                        <input
                            id="password"
                            type="password"
                            class="form-control auth-form-control"
                            name="password"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                        />
                    </div>

                    <div class="mb-4">
                        <label for="password-confirm" class="auth-form-label">{{
                            labels.password_confirmation
                        }}</label>
                        <input
                            id="password-confirm"
                            type="password"
                            class="form-control auth-form-control"
                            name="password_confirmation"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                        />
                    </div>

                    <div class="mb-0">
                        <button type="submit" class="btn auth-btn-primary">
                            {{ labels.register }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Register",
    props: {
        action: {
            type: String,
            default: "/register",
        },
        title: {
            type: String,
            default: "Inscription",
        },
        labels: {
            type: Object,
            default() {
                return {
                    name: "Nom",
                    email: "Addresse courriel",
                    password: "Mot de passe",
                    password_confirmation: "Confirmer Mot de Passe",
                    register: "S'inscrire",
                };
            },
        },
    },
    data() {
        return {
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
        };
    },
    methods: {
        async handleSubmit() {
            this.error = null;

            try {
                await axios.get("/sanctum/csrf-cookie");

                const res = await axios.post("/api/register", {
                    name: this.name,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                });

                if (res.data.success) {
                    this.$router.push("/login");
                }
            } catch (err) {
                if (err.response && err.response.status === 422) {
                    // Laravel validation errors
                    this.error = Object.values(err.response.data.errors)
                        .flat()
                        .join(" ");
                } else {
                    this.error = "Erreur lors de l'inscription";
                }
            }
        },
    },
};
</script>

<style scoped>
.auth-container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
}
.auth-card {
    width: 100%;
    max-width: 420px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}
.auth-card-header {
    font-weight: 700;
    padding: 1rem;
    border-bottom: 1px solid #eee;
}
.auth-card-body {
    padding: 1rem;
}
.auth-form-label {
    display: block;
    margin-bottom: 0.5rem;
}
.auth-form-control {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid #dcdcdc;
    border-radius: 4px;
}
.auth-btn-primary {
    background-color: #0d6efd;
    color: #fff;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
}
</style>
