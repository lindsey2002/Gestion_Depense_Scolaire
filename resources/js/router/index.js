import { createRouter, createWebHistory } from "vue-router";
import DashboardCompta from '../views/compta/DashboardCompta.vue';
import Login from '../views/auth/Login.vue';
import GestionClasses from '../views/admin/GestionClasses.vue';
import InscriptionEleve from '../views/eleves/InscriptionEleve.vue';
import GestionVagues from '../views/admin/GestionVagues.vue';
import AdminRegister from '../views/admin/Register.vue';
import GestionPaiements from '../views/compta/GestionPaiements.vue';
import DashboardGestionnaire from '../views/eleves/DashboardGestionnaire.vue';
import DashboardParent from '../views/parent/DashboardParent.vue';


// on definit les routes cad les correspondances entre url et pages
const routes = [
    // ═══ ACCÈS PUBLIC / AUTHENTIFICATION ═══
    {
        path: '/login',
        name: 'Login',
        component: Login
    },
    {
        path: '/admin/register',
        name: 'AdminRegister',
        component: () => import('@/views/admin/Register.vue'),
        meta: {requiresAuth: true, role: 'admin'}
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
        path: '/gestionnaire/inscription-eleve',
        name: 'InscriptionEleveGestionnaire',
        component: InscriptionEleve,
        meta: { requiresAuth: true, role: 'gestionnaire' }
    },
    {
        path: '/compta/rechercheeleve', 
        name: 'compta.rechercheeleve',
        component: () => import('../views/compta/RechercheEleve.vue'),
        meta: { requiresAuth: true, role: 'comptable' }
    },
    {
        path: '/compta/paiement/:id',
        name: 'compta.paiement',
        component: () => import('@/views/compta/GestionPaiements.vue'),
        props: true,
        meta: { requiresAuth: true, role: 'comptable' }
    },
    {
        path: '/compta/gestiondepense',
        name: 'compta.gestiondepense',
        component: () => import('@/views/compta/GestionDepenses.vue'),
        meta: { requiresAuth: true, role: 'comptable' }
    },

    // ═══ ESPACE GESTIONNAIRE (Protégé) ═══
    {
        path: '/gestionnaire/dashboard',
        name: 'DashboardGestionnaire',
        component: () => import('@/views/eleves/DashboardGestionnaire.vue'),
        meta: { requiresAuth: true, role: 'gestionnaire' }
    },
    {
        path: '/parent/dashboard',
        name: 'ParentDashboard',
        component: DashboardParent,
        meta: { 
            requiresAuth: true, 
            role: 'parent'
        }
    },
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