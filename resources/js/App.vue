<template>
    <div id="global">
        <header id="headerSite">
            <div class="header-top">
                <router-link to="/">
                    <h1 id="titreSite">Ventique</h1>
                </router-link>
                <p id="descSite">Bienvenue sur notre site web</p>
            </div>

            <!-- Add SearchBar component -->
            <SearchBar />

            <nav class="header-nav">
                <ul class="navbar-nav">
                    <!-- Show these links when user is NOT logged in -->
                    <div v-if="!isLoggedIn">
                        <li class="nav-item">
                            <router-link to="/login" class="nav-link">
                                Connexion
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/register" class="nav-link">
                                Inscription
                            </router-link>
                        </li>
                    </div>

                    <!-- Show these links when user IS logged in -->
                    <div v-else>
                        <li class="nav-item">
                            <a @click="showAddModal = true" class="nav-link nav-link-add" style="cursor: pointer">
                                 Ajouter une antiquité
                            </a>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link">
                                 {{ userName }}
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" style="cursor: pointer" @click="logout">
                                Déconnexion
                            </a>
                        </li>
                    </div>
                </ul>
            </nav>
        </header>

        <router-view @refresh-antiques="handleRefresh" />
        
        <!-- Add Modal Component -->
        <AddAntique 
            :show="showAddModal" 
            @close="showAddModal = false"
            @success="handleAddSuccess"
        />
        
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
import SearchBar from './pages/SearchBar.vue';
import AddAntique from './components/AddAntique.vue';
import api from './axios';

export default {
    name: "App",
    components: {
        SearchBar,
        AddAntique,
    },
    data() {
        return {
            isLoggedIn: false,
            userName: '',
            showAddModal: false,
        };
    },
    created() {
        if (window.Laravel && window.Laravel.isLoggedIn) {
            this.isLoggedIn = true;
            this.userName = window.Laravel.user ? window.Laravel.user.name : 'User';
        }
    },
    methods: {
        logout(e) {
            e.preventDefault();
            
            api.get("/sanctum/csrf-cookie").then(() => {
                api.post("/api/logout")
                    .then((response) => {
                        if (response.data.success) {
                            this.isLoggedIn = false;
                            this.userName = '';
                            localStorage.removeItem('token');
                            window.location.href = "/";
                        }
                    })
                    .catch((error) => {
                        console.error("Logout error:", error);
                        window.location.href = "/";
                    });
            });
        },
        handleAddSuccess() {
            this.showAddModal = false;
            // Refresh the current page to show new antique
            if (this.$route.path === '/' || this.$route.path === '/antiques') {
                window.location.reload();
            } else {
                this.$router.push('/');
            }
        },
        handleRefresh() {
            // Method to handle refresh event from child components
            window.location.reload();
        }
    },
};
</script>

<style scoped>
#headerSite {
    background-color: #2e1d19;
    color: #edc58a;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.header-top {
    text-align: center;
    margin-bottom: 15px;
}

#titreSite {
    font-size: 2.5rem;
    font-weight: bold;
    color: #edc58a;
    margin: 0;
    text-decoration: none;
    font-family: 'Georgia', serif;
}

#titreSite:hover {
    color: #f9e0bb;
}

#descSite {
    color: #f9e0bb;
    font-size: 1rem;
    margin: 5px 0 0 0;
}

.header-nav {
    width: 100%;
    display: flex;
    justify-content: center;
}

.navbar-nav {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 20px;
}

.nav-item {
    display: inline-block;
}

.nav-link {
    color: #edc58a;
    text-decoration: none;
    font-weight: bold;
    padding: 8px 16px;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.nav-link:hover {
    background-color: #725042;
    color: #f9e0bb;
}

.nav-link-add {
    background-color: #2e1d19;
    padding: 8px 16px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.nav-link-add:hover {
    background-color: #2e1d19;
    transform: translateY(-2px);
}

#footer {
    background-color: #2e1d19;
    padding: 20px;
    color: #edc58a;
    text-align: center;
}

.header-link {
    color: #edc58a;
    text-decoration: none;
}

.header-link:hover {
    color: #f9e0bb;
}

#filler {
    min-height: 50vh;
}

@media (max-width: 768px) {
    .navbar-nav {
        flex-direction: column;
        gap: 10px;
    }
}
</style>
