<template>
    <div>
        <h4 class="text-center">Liste des antiques</h4>
        <br />

        <!-- Le bouton est affiché même si l'utilisateur n'est pas connecté.
             Le click appelle goAdd() qui redirige vers /login si besoin. -->
        <!-- <router-link
            v-if="isLoggedIn"
            :to="{ name: 'addantique' }"
            class="btn btn-primary"
        >
            Ajouter
        </router-link> -->

        <div class="container-antiques">
            <div class="antique" v-for="antique in antiques" :key="antique.id">
                <header class="antique-header">
                    <h1 class="antique-nom">
                        {{ antique.name }}
                    </h1>

                    <!-- <div v-if="antique.image" class="image">
                        <img
                            src="{{ asset('storage/' + antique.image ) }}"
                            alt="{{ antique.name }}"
                        />
                    </div> -->

                    <h3 class="antique-desc">
                        {{ antique.description }}
                    </h3>
                </header>
                <p class="antique-prix">{{ antique.price }}$</p>
            </div>
        </div>
    </div>
</template>
<script>
import axios from "axios";

export default {
    data() {
        return {
            antiques: [],
            isLoggedIn: false,
        };
    },
    async created() {
        this.checkLoginStatus(); // Vérification de la connexion dès la création du composant

        // Chargement des antiques using async/await and handle Laravel paginator
        try {
            const response = await axios.get("/api/antiques");
            // Laravel's paginate() returns an object with a `data` array.
            // Use response.data.data if present, otherwise fall back to response.data
            this.antiques =
                response && response.data && response.data.data
                    ? response.data.data
                    : response.data;
        } catch (error) {
            console.error("Failed to load antiques:", error);
        }
    },
    methods: {
        checkLoginStatus() {
            // Si tu utilises session auth (Laravel) on lit window.Laravel.isLoggedin, sinon localStorage token
            if (
                window.Laravel &&
                typeof window.Laravel.isLoggedin !== "undefined"
            ) {
                this.isLoggedIn = !!window.Laravel.isLoggedin;
            } else {
                // fallback si tu utilises un token localStorage
                const token = localStorage.getItem("token");
                this.isLoggedIn = !!token;
            }
        },

        /*  goAdd() {
             // Si pas connecté -> redirection vers la page de login
             if (!this.isLoggedIn) {
                 // Utilise le nom de route 'login' si tu l'as défini, sinon chemin '/login'
                 this.$router.push({ name: 'login' }).catch(() => { this.$router.push('/login') });
                 return;
             }
             // sinon rediriger vers addarticle (nom de route)
             this.$router.push({ name: 'addarticle' }).catch(() => { this.$router.push('/add') });
         }, */

        // checkAuthBeforeDelete(id) {
        //     if (!this.isLoggedIn) {
        //         // redirection correcte : name ou path
        //         this.$router.push({ name: "login" }).catch(() => {
        //             this.$router.push("/login");
        //         });
        //     } else {
        //         this.deleteArticle(id);
        //     }
        // },

        // deleteArticle(id) {
        //     if (!confirm("Are you sure to delete this article ?")) {
        //         return;
        //     }
        //     axios
        //         .delete(`/api/articles/${id}`)
        //         .then(() => {
        //             // Retirer l'article du tableau local après suppression
        //             this.articles = this.articles.filter(
        //                 (article) => article.id !== id
        //             );
        //         })
        //         .catch((error) => {
        //             console.error(
        //                 "Erreur lors de la suppression de l'article :",
        //                 error
        //             );
        //             // si erreur 401/403 -> rediriger vers login
        //             if (
        //                 error.response &&
        //                 (error.response.status === 401 ||
        //                     error.response.status === 403)
        //             ) {
        //                 this.$router.push({ name: "login" }).catch(() => {
        //                     this.$router.push("/login");
        //                 });
        //             }
        //         });
        // },
    },
};
</script>
