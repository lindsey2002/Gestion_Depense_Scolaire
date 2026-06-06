<template>
  <div class="min-h-screen bg-gray-900 text-gray-100 font-sans p-6">
    <div class="max-w-7xl mx-auto space-y-8">
      
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-gray-800 pb-6">
        <div>
          <h1 class="text-3xl font-bold text-white tracking-tight">Espace Comptabilité</h1>
          <p class="text-sm text-gray-400 mt-1">
            Gestion de la caisse générale et suivi des encaissements scolaires.
          </p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
          <router-link :to="{ name: 'compta.rechercheeleve' }" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-500 rounded-lg shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
    </svg>
    Encaisser un élève
  </router-link>

          <router-link to="/compta/gestiondepense" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-gray-300 bg-gray-800 hover:bg-gray-700 border border-gray-700 rounded-lg transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Journal de Caisse
          </router-link>

          <button @click="exporterExcel" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500 hover:text-white border border-emerald-500/20 rounded-lg transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Exporter Excel
          </button>

          <button @click="gererDeconnexion" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-red-400 bg-red-500/10 hover:bg-red-500 hover:text-white border border-red-500/20 rounded-lg transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </button>
        </div>
      </div>

      <div v-if="loading" class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl text-sm flex items-center gap-3">
        <svg class="animate-spin h-5 w-5 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Chargement des flux et données de caisse en cours...</span>
      </div>
      
      <div v-else-if="error" class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl text-sm">
        {{ error }}
      </div>

      <div v-else-if="caisseGenerale" class="space-y-8">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
          
          <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-md flex flex-col justify-between">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Recettes</h3>
            <p class="text-2xl font-bold text-emerald-400 mt-2 font-mono">{{ caisseGenerale.total_recettes }}</p>
            <div class="text-xs text-gray-500 mt-1">Fonds réels encaissés</div>
          </div>

          <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-md flex flex-col justify-between">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Part Inscriptions</h3>
            <p class="text-2xl font-bold text-purple-400 mt-2 font-mono">{{ caisseGenerale.part_inscriptions }}</p>
            <div class="text-xs text-purple-400/50 mt-1">Frais d'immatriculation</div>
          </div>

          <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-md flex flex-col justify-between">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Part Pensions</h3>
            <p class="text-2xl font-bold text-amber-400 mt-2 font-mono">{{ caisseGenerale.part_pensions }}</p>
            <div class="text-xs text-amber-400/50 mt-1">Mensualités perçues</div>
          </div>

          <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-md flex flex-col justify-between">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Scolarité Attendue</h3>
            <p class="text-2xl font-bold text-blue-400 mt-2 font-mono">{{ caisseGenerale.total_scolarite_attendue }}</p>
            <div class="text-xs text-blue-400/50 mt-1">Objectif budgétaire global</div>
          </div>

          <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-md flex flex-col justify-between">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Reste à Percevoir</h3>
            <p class="text-2xl font-bold text-red-400 mt-2 font-mono">{{ caisseGenerale.reste_a_percevoir }}</p>
            <div class="text-xs text-red-400/50 mt-1">Arriérés et recouvrements</div>
          </div>
        </div>

        <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-md overflow-hidden">
          <div class="p-6 border-b border-gray-700">
            <h2 class="text-lg font-bold text-white">Suivi Financier par Vague d'Enseignement</h2>
            <p class="text-xs text-gray-400 mt-0.5">Ventilation des encaissements par groupe d'étudiants.</p>
          </div>
          
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-gray-800/50 text-gray-400 text-xs uppercase tracking-wider font-semibold border-b border-gray-700">
                  <th class="p-4 pl-6">ID unique</th>
                  <th class="p-4">Nom de la Vague</th>
                  <th class="p-4">Effectif Affecté</th>
                  <th class="p-4 pr-6">Volume Encaissé</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-700/50 text-sm text-gray-300">
                <tr v-for="vague in statistiquesVagues" :key="vague.id" class="hover:bg-gray-700/20 transition-colors">
                  <td class="p-4 pl-6 font-mono text-gray-500">#0{{ vague.id }}</td>
                  <td class="p-4 font-semibold text-white">{{ vague.nom }}</td>
                  <td class="p-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                      {{ vague.nombre_eleves }} étudiants
                    </span>
                  </td>
                  <td class="p-4 pr-6 font-bold text-emerald-400 font-mono">{{ vague.total_encaisse }} FCFA</td>
                </tr>
                <tr v-if="statistiquesVagues.length === 0">
                  <td colspan="4" class="p-8 text-center text-gray-500">Aucune statistique disponible pour les vagues.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

const caisseGenerale = ref(null);
const statistiquesVagues = ref([]);
const loading = ref(true);
const error = ref(null);

const fetchDashboardData = async () => {
    try {
        loading.value = true;
        error.value = null;
        
        const token = localStorage.getItem('auth_token');
        
        const response = await axios.get('/api/v1/dashboard/stats', {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });
        
        if (response.data && response.data.caisse_generale) {
            caisseGenerale.value = response.data.caisse_generale;
            statistiquesVagues.value = response.data.statistiques_vagues || [];
        } else {
            error.value = "Le serveur n'a pas renvoyé le format de données attendu.";
        }
    } catch (err) {
        console.error("Erreur d'appel API :", err);
        error.value = "Impossible de récupérer les données depuis l'API Laravel.";
    } finally {
        loading.value = false;
    }
};

// Logique d'exportation Excel brute
const exporterExcel = () => {
    const token = localStorage.getItem('auth_token');
    // On force le téléchargement direct via l'endpoint de ton API Laravel
    window.open(`/api/v1/finance/export-excel?token=${token}`, '_blank');
};

// Logique de déconnexion sécurisée (Nettoyage + Redirection)
const gererDeconnexion = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    await axios.post('/api/v1/logout', {}, {
        headers: { Authorization: `Bearer ${token}` }
    });
  } catch (err) {
    console.error("Erreur déconnexion serveur :", err);
  } finally {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_role');
    router.push('/login');
  }
};

onMounted(() => {
    fetchDashboardData();
});
</script>