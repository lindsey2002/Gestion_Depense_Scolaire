<template>
  <div class="min-h-screen bg-gray-900 text-gray-100 flex">
    
    <aside class="w-64 bg-gray-800 border-r border-gray-700 flex flex-col justify-between">
      <div class="p-6">
        <h2 class="text-xl font-bold text-emerald-400 tracking-wide">ISI GESTION</h2>
        <p class="text-xs text-gray-400 mt-1">Espace Gestionnaire</p>
        
        <nav class="mt-8 space-y-2">
          <a href="#" class="flex items-center gap-3 px-4 py-2.5 bg-gray-700 text-white rounded-lg font-medium transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
            </svg>
            Tableau de bord
          </a>

          <router-link to="/gestionnaire/inscription-eleve" class="flex items-center gap-3 px-4 py-2.5 text-gray-400 hover:bg-gray-700 hover:text-white rounded-lg font-medium transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            Inscrire un Élève
          </router-link>
        </nav>
      </div>

      <div class="p-4 border-t border-gray-700 bg-gray-850">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-white">{{ userProfile.name }}</p>
            <p class="text-xs text-gray-400">{{ userProfile.email }}</p>
          </div>
          <button @click="handleLogout" class="text-gray-400 hover:text-red-400 p-1.5 rounded-lg hover:bg-gray-700 transition" title="Déconnexion">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </button>
        </div>
      </div>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
      <header class="flex justify-between items-center border-b border-gray-700 pb-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-white">Aperçu Général</h1>
          <p class="text-sm text-gray-400">Suivi des inscriptions et des effectifs scolaires.</p>
        </div>
        <div class="bg-gray-800 px-4 py-2 rounded-lg border border-gray-700 text-sm font-medium">
          Année Académique : <span class="text-emerald-400">2026-2027</span>
        </div>
      </header>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-lg">
          <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Total Élèves Inscrits</p>
          <p class="text-3xl font-bold text-white mt-2">0</p>
        </div>
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-lg">
          <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Nouvelles Inscriptions (Ce mois)</p>
          <p class="text-3xl font-bold text-emerald-400 mt-2">0</p>
        </div>
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-lg">
          <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Classes Actives</p>
          <p class="text-3xl font-bold text-indigo-400 mt-2">0</p>
        </div>
      </div>

      <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 shadow-lg">
        <h3 class="text-lg font-semibold text-white mb-4">Derniers élèves enregistrés</h3>
        <p class="text-sm text-gray-400 text-center py-8">Aucun élève inscrit pour le moment. Utilisez le menu pour enregistrer un nouvel élève.</p>
      </div>
    </main>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const userProfile = ref({ name: 'Gestionnaire', email: '' });

onMounted(() => {
    // Récupération des données utilisateur stockées au login pour l'affichage de la sidebar
    userProfile.value.name = localStorage.getItem('user_name') || 'Gestionnaire ISI';
    userProfile.value.email = localStorage.getItem('user_email') || '';
});

const handleLogout = async () => {
    try {
        const token = localStorage.getItem('auth_token');
        await axios.post('/api/v1/logout', {}, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
    } catch (err) {
        console.error("Erreur lors de la déconnexion déportée:", err);
    } finally {
        // Nettoyage complet du localStorage et redirection
        localStorage.clear();
        router.push('/login');
    }
};
</script>