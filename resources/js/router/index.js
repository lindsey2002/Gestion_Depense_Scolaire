import { createRouter, createWebHistory } from "vue-router";
import DashboardCompta from '../views/compta/DashboardCompta.vue';
import Login from '../views/auth/Login.vue';


// on definit les routes cad les correspondances entre url et pages
const routes = [
    {
        path: '/compta/dashboard',
        name: 'ComptaDashboard',
        component: DashboardCompta
    },
    {
        path: '/login',
        name: 'Login',
        component: Login
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;