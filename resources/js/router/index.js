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
        path: '/register',
        name: 'register',
        component: Register
    },
    {
        path: '/admin/gestionclasses',
        name: 'GestionClasses',
        component: GestionClasses
    },
    {
        path: '/compta/inscription-eleve',
        name: 'InscriptionEleve',
        component: InscriptionEleve
    },
    {
        path: '/admin/gestionvagues',
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
    },
    {
        path: '/compta/depenses',
        name: 'compta.depenses',
        component: () => import('@/views/compta/GestionDepenses.vue')
    },
    {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: () => import('@/views/admin/DashboardAdmin.vue'),
        meta: { requiresAuth: true }
        // Ajoute ici tes méta pour protéger la route plus tard
    },
    {
        path: '/admin/classes/recherche',
        name: 'admin.classes.recherche',
        component: () => import('@/views/admin/RechercheClasse.vue')
    },
    {
        path: '/admin/detailclasse',
        name: 'admin.detailclasse',
        component: () => import('@/views/admin/DetailClasse.vue'),
        meta: { requiresAuth: true }
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
  // 1. On vérifie si la page demandée nécessite d'être connecté
  const pageProtegee = to.matched.some(record => record.meta.requiresAuth)
  
  // 2. On vérifie si le token existe toujours dans le localStorage
  const estConnecte = localStorage.getItem('auth_token')

  // 3. Si la page est protégée et que le token n'existe plus (ex: après déconnexion)
  if (pageProtegee && !estConnecte) {
    // On refuse l'accès et on force la redirection vers le login
    next('/login')
  } else {
    // Sinon, on laisse passer normalement
    next()
  }
})

export default router;