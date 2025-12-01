import { createWebHistory, createRouter } from "vue-router";

import Home from "../pages/Home.vue";
import Dashboard from "../pages/Dashboard.vue";
import Apropos from "../pages/Apropos.vue";
import Register from "../pages/Register.vue";
import Login from "../pages/Login.vue";
import Antiques from "../components/Antiques.vue";
import AddAntique from "../components/AddAntique.vue";
import AntiqueDetail from "../pages/AntiqueDetails.vue";

export const routes = [
    {
        name: "home",
        path: "/",
        component: Antiques,
    },
    {
        name: "dashboard",
        path: "/dashboard",
        component: Dashboard,
    },
    {
        name: "apropos",
        path: "/apropos",
        component: Apropos,
    },
    {
        name: "register",
        path: "/register",
        component: Register,
    },
    {
        name: "login",
        path: "/login",
        component: Login,
    },
    {
        name: "antiques",
        path: "/antiques",
        component: Antiques,
    },
    {
        name: "add-antique",
        path: "/antiques/create",
        component: AddAntique,
        meta: { requiresAuth: true }
    },
    {
        name: "antique-details",
        path: "/antiques/:id",
        component: AntiqueDetail,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});

// Navigation guard to protect routes
router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth) {
        const isLoggedIn = window.Laravel?.isLoggedIn || localStorage.getItem('token');
        
        if (!isLoggedIn) {
            alert('Veuillez vous connecter pour accéder à cette page.');
            next('/login');
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router;
