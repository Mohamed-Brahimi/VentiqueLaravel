<template>
    <div>
        <h4 class="text-center">Liste des articles</h4><br />

        <!-- Le bouton est affiché même si l'utilisateur n'est pas connecté.
             Le click appelle goAdd() qui redirige vers /login si besoin. -->
        <router-link v-if="isLoggedIn" :to="{ name: 'addarticle' }" class="btn btn-primary">
            Ajouter
        </router-link>

        <!-- Version alternative : toujours visible, redirige vers login si non connecté -->
        <!--  <button v-else @click="goAdd" class="btn btn-primary">
            Ajouter
        </button> -->

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th scope="col" class="text-center">Image</th>
                    <th scope="col" class="text-center">Title</th>
                    <th scope="col" class="text-center">Content</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="article in articles" :key="article.id">
                    <td style="text-align: center; vertical-align: middle;">
                        <div v-if="article.photo">
                            <img class="img-thumbnail" :src="'/images/upload/' + article.photo" />
                            <!-- // il est possible d'ajouter le style à l'image
                                <img class="img-thumbnail" :src="'/images/upload/' + article.photo"
                                style="height:100px;width:150px" /> -->

                        </div>
                    </td>
                    <td style="text-align: center; vertical-align: middle;">{{ article.title }}</td>
                    <td style="text-align: center; vertical-align: middle;">{{ article.content }}</td>
                    <td>
                        <div style="text-align: center; vertical-align: middle;">
                            <!--  <router-link :to="{ name: 'showarticle', params: { id: article.id } }"
                                class="btn btn-primary">View
                            </router-link>

                            <router-link :to="{ name: 'editarticle', params: { id: article.id } }"
                                class="btn btn-warning" v-if="isLoggedIn">Edit
                            </router-link> -->

                            <button class="btn btn-danger" @click="checkAuthBeforeDelete(article.id)">
                                Delete
                            </button>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    data() {
        return {
            articles: [],
            isLoggedIn: false,
        }
    },
    created() {
        this.checkLoginStatus(); // Vérification de la connexion dès la création du composant
        // Chargement des articles
        axios.get('/api/articles')
            .then(response => {
                this.articles = response.data;
            })
            .catch(error => {
                console.error(error);
            });
    },
    methods: {
        checkLoginStatus() {
            // Si tu utilises session auth (Laravel) on lit window.Laravel.isLoggedin, sinon localStorage token
            if (window.Laravel && typeof window.Laravel.isLoggedin !== 'undefined') {
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

        checkAuthBeforeDelete(id) {
            if (!this.isLoggedIn) {
                // redirection correcte : name ou path
                this.$router.push({ name: 'login' }).catch(() => { this.$router.push('/login') });
            } else {
                this.deleteArticle(id);
            }
        },

        deleteArticle(id) {
            if (!confirm("Are you sure to delete this article ?")) {
                return;
            }
            axios
                .delete(`/api/articles/${id}`)
                .then(() => {
                    // Retirer l'article du tableau local après suppression
                    this.articles = this.articles.filter(
                        (article) => article.id !== id
                    );
                })
                .catch((error) => {
                    console.error("Erreur lors de la suppression de l'article :", error);
                    // si erreur 401/403 -> rediriger vers login
                    if (error.response && (error.response.status === 401 || error.response.status === 403)) {
                        this.$router.push({ name: 'login' }).catch(() => { this.$router.push('/login') });
                    }
                });
        },
    },
}
</script>
