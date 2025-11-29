<template>
    <div id="global">
        <header id="headerSite">
            <div class="header-top">
                <router-link to="/">
                    <h1 id="titreSite">Ventique</h1>
                </router-link>
                <p id="descSite">Bienvenue sur notre site web</p>
            </div>

            <nav class="header-nav">
                <ul class="navbar-nav">
                    <div v-if="isLoggedIn">
                        <li class="nav-item">
                            <router-link to="/login" class="nav-link">
                                Connexion
                            </router-link>
                            <router-link to="/register" class="nav-link">
                                Inscription
                            </router-link>
                        </li>
                    </div>
                    <li>Hallo</li>
                    <li>Test</li>
                </ul>
            </nav>
        </header>

        <router-view />
        <div id="filler"></div>
        <footer id="footer">
            <p>Ventique &copy; 2025</p>
            <router-link class="header-link" to="/apropos">
                <p>À propos</p>
            </router-link>
        </footer>
    </div>
</template>

<script>
export default {
    name: "App",
    data() {
        return {
            isLoggedIn: false,
        };
    },
    created() {
        if (window.Laravel.isLoggedin) {
            this.isLoggedIn = true;
        }
    },
    methods: {
        logout(e) {
            console.log("ss");
            e.preventDefault();
            this.$axios.get("/sanctum/csrf-cookie").then((response) => {
                this.$axios
                    .post("/api/logout")
                    .then((response) => {
                        if (response.data.success) {
                            window.location.href = "/articles";
                        } else {
                            console.log(response);
                        }
                    })
                    .catch(function (error) {
                        console.error(error);
                    });
            });
        },
    },
};
</script>

<style scoped>
.footer {
    background-color: #08539d;
    padding: 20px;
    color: rgb(255, 255, 255);
    text-align: center;
    position: relative;
    bottom: 0;
    width: 100%;
}

.header-link {
    color: rgb(236, 203, 159);
}
</style>
