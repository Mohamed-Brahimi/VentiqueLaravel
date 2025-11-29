<template>
    <div id="global">
        <header id="headerSite">
            <div class="header-top">
                <router-link to="/" class="nav-item nav-link">
                    <h1 id="titreSite">Ventique</h1>
                </router-link>
                <p id="descSite">Bienvenue sur notre site web</p>
            </div>
        </header>
        <!-- <div
            class="text-center"
            style="
                margin: 20px 0px 20px 0px;
                background-color: #2769b0;
                color: #ffff;
            "
        >
            <h2>Site monopage Laravel-Vue avec authentification</h2>
        </div> -->
        <nav
            class="navbar navbar-expand-lg navbar-light bg-light"
            style="background-color: #3485dc; color: #ffff"
        >
            <div
                class="collapse navbar-collapse"
                style="background-color: #3485dc; color: #ffff"
            >
                <!-- for logged-in user-->
                <div
                    class="navbar-nav"
                    v-if="isLoggedIn"
                    style="background-color: #3485dc; color: #ffff"
                >
                    <router-link to="/dashboard" class="nav-item nav-link"
                        >Dashboard</router-link
                    >
                    <router-link to="/articles" class="nav-item nav-link"
                        >Articles</router-link
                    >
                    <a
                        class="nav-item nav-link"
                        style="cursor: pointer"
                        @click="logout"
                        >Logout</a
                    >
                </div>
                <!-- for non-logged user-->
                <div
                    class="navbar-nav"
                    v-else
                    style="background-color: #3485dc; color: #ffff"
                >
                    <router-link to="/" class="nav-item nav-link"
                        >Home</router-link
                    >
                    <!--   <router-link to="/about" class="nav-item nav-link">About</router-link>
                    <router-link to="/login" class="nav-item nav-link">login</router-link>
                    <router-link to="/register" class="nav-item nav-link">Register </router-link> -->
                </div>
            </div>
        </nav>
        <br />
        <router-view />
        <div id="filler"></div>
        <footer id="footer">
            <p>Ventique &copy; 2025</p>
            <a style="color: rgb(236, 203, 159)" href="{{ url('/apropos') }}">
                <p>@lang("footer_apropos")</p>
            </a>
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
</style>
