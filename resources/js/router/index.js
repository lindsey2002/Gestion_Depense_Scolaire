import { createRouter, createWebHistory } from "vue-router";
import DashboardCompta from '../views/compta/DashboardCompta.vue';
import Login from '../views/auth/Login.vue';
import GestionClasses from '../views/admin/GestionClasses.vue';
import InscriptionEleve from "../views/eleves/InscriptionEleve.vue";
import GestionVagues from '../views/admin/GestionVagues.vue';
import GestionPaiements from "../views/compta/GestionPaiements.vue";


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
    },
    {
        path: '/admin/classes',
        name: 'GestionClasses',
        component: GestionClasses
    },
    {
        path: '/compta/inscription-eleve',
        name: 'InscriptionEleve',
        component: InscriptionEleve
    },
    {
        path: '/admin/vagues',
        name: 'GestionVagues',
        component: GestionVagues
    },
    {
        path: '/compta/paiements',
        name: 'GestionPaiements',
        component: GestionPaiements,
        props: true
    },
    {
        path: '/compta/recherche',
        name: 'compta.recherche',
        component: () => import('../views/compta/RechercheEleve.vue')
    },
    {
    path: '/compta/paiement/:id', // L'URL finale ressemblera à /compta/paiement/1
    name: 'compta.paiement',      // C'EST CE NOM QUE VUE-ROUTER CHERCHE !
    component: () => import('@/views/compta/GestionPaiements.vue'),
    props: true
}
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;