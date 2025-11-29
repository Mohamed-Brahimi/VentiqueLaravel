import { createWebHistory, createRouter } from "vue-router";

import Home from "../pages/Home.vue";
import Dashboard from "../pages/Dashboard.vue";
import Articles from "../components/Articles.vue";
import Apropos from "../pages/Apropos.vue";
import Register from "../components/Register.vue";

export const routes = [
    {
        name: "home",
        path: "/",
        component: Home,
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
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem("token");
    if (to.meta.requiresAuth && !token) {
        next({ name: "/login" }); // Rediriger vers la page de login si non authentifié
    } else {
        next(); // Continuer la navigation
    }
});
export default router;
