import { createRouter, createWebHistory } from "vue-router";
import DashboardCompta from '../views/compta/DashboardCompta.vue';
import Login from '../views/auth/Login.vue';
import Register from '../views/auth/Register.vue';
import GestionClasses from '../views/admin/GestionClasses.vue';
import InscriptionEleve from "../views/eleves/InscriptionEleve.vue";
import GestionVagues from '../views/admin/GestionVagues.vue';
import GestionPaiements from "../views/compta/GestionPaiements.vue";


// on definit les routes cad les correspondances entre url et pages
const routes = [
    // ═══ ACCÈS PUBLIC / AUTHENTIFICATION ═══
    {
        path: '/login',
        name: 'Login',
        component: Login
    },
    {
        path: '/register',
        name: 'register',
        component: Register
    },

    // ═══ ESPACE ADMINISTRATEUR (Protégé) ═══
    {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: () => import('@/views/admin/DashboardAdmin.vue'),
        meta: { requiresAuth: true, role: 'admin' }
    },
    {
        path: '/admin/gestionclasses',
        name: 'GestionClasses',
        component: GestionClasses, // Gardé tel quel selon ton import du haut
        meta: { requiresAuth: true, role: 'admin' }
    },
    {
        path: '/admin/gestionvagues',
        name: 'GestionVagues',
        component: GestionVagues, // Gardé tel quel selon ton import du haut
        meta: { requiresAuth: true, role: 'admin' }
    },
    {
        path: '/admin/classes/recherche',
        name: 'admin.classes.recherche',
        component: () => import('@/views/admin/RechercheClasse.vue'),
        meta: { requiresAuth: true, role: 'admin' }
    },
    {
        path: '/admin/detailclasse',
        name: 'admin.detailclasse',
        component: () => import('@/views/admin/DetailClasse.vue'),
        meta: { requiresAuth: true, role: 'admin' }
    },

    // ═══ ESPACE COMPTABILITÉ (Protégé) ═══
    {
        path: '/compta/dashboard',
        name: 'ComptaDashboard',
        component: DashboardCompta, // Gardé tel quel selon ton import du haut
        meta: { requiresAuth: true, role: 'comptable' }
    },
    {
        path: '/compta/inscription-eleve',
        name: 'InscriptionEleve',
        component: InscriptionEleve,
        meta: { requiresAuth: true, role: 'comptable' }
    },
    {
        path: '/compta/rechercheeleve', // Aligné sur le "to" de ta carte Encaisser
        name: 'compta.rechercheeleve',
        component: () => import('../views/compta/RechercheEleve.vue'),
        meta: { requiresAuth: true, role: 'comptable' }
    },
    {
        path: '/compta/paiement/:id', // L'entonnoir après la recherche d'élève
        name: 'compta.paiement',
        component: () => import('@/views/compta/GestionPaiements.vue'),
        props: true,
        meta: { requiresAuth: true, role: 'comptable' }
    },
    {
        path: '/compta/gestiondepense', // Aligné sur le "to" de ta carte Dépenses
        name: 'compta.gestiondepense',
        component: () => import('@/views/compta/GestionDepenses.vue'),
        meta: { requiresAuth: true, role: 'comptable' }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
  const pageProtegee = to.matched.some(record => record.meta.requiresAuth)
  
  const estConnecte = localStorage.getItem('auth_token')

  if (pageProtegee && !estConnecte) {
    return '/login'
  } else {
    next()
  }
})

export default router;