<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="alert alert-danger" v-if="error">
                    {{ error }}
                </div>

                <div class="card">
                    <div class="card-header">Register</div>
                    <div class="card-body">

                        <form @submit.prevent="handleSubmit">

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label text-md-right">Name</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" v-model="name" required autocomplete="off">
                                </div>
                            </div><br>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label text-md-right">Email</label>
                                <div class="col-md-6">
                                    <input type="email" class="form-control" v-model="email" required
                                        autocomplete="off">
                                </div>
                            </div><br>

                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right">Password</label>
                                <div class="col-md-6">
                                    <input type="password" class="form-control" v-model="password" required
                                        autocomplete="off">
                                </div>
                            </div><br>

                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right">Confirm Password</label>
                                <div class="col-md-6">
                                    <input type="password" class="form-control" v-model="c_password" required
                                        autocomplete="off">
                                </div>
                            </div><br>

                            <div class="form-group row">
                                <div class="col-md-8 offset-md-4">
                                    <button class="btn btn-primary">Register</button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            name: "",
            email: "",
            password: "",
            c_password: "",
            error: null
        }
    },
    methods: {
        async handleSubmit() {
            this.error = null;

            try {
                await axios.get('/sanctum/csrf-cookie');

                const res = await axios.post('/api/register', {
                    name: this.name,
                    email: this.email,
                    password: this.password,
                    c_password: this.c_password
                });

                if (res.data.success) {
                    this.$router.push("/login");
                }
            } catch (err) {

                if (err.response && err.response.status === 422) {
                    // Laravel validation errors
                    this.error = Object.values(err.response.data.errors).flat().join(' ');
                } else {
                    this.error = "Erreur lors de l'inscription";
                }
            }
        }

    }
}
</script>