<template>
  <div class="p-6 bg-gray-100 min-h-screen">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Tableau de Bord - Comptabilité</h1>

    <div v-if="loading" class="text-blue-600 font-semibold p-4 bg-blue-50 rounded shadow-sm">
      Chargement des données comptables...
    </div>
    
    <div v-else-if="error" class="text-red-600 font-semibold p-4 bg-red-50 rounded shadow-sm">
      {{ error }}
    </div>

    <div v-else-if="caisseGenerale">
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
          <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Recettes</h3>
          <p class="text-2xl font-bold text-gray-800 mt-2">{{ caisseGenerale.total_recettes }} FCFA</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-purple-500">
          <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Part Inscriptions</h3>
          <p class="text-2xl font-bold text-gray-800 mt-2">{{ caisseGenerale.part_inscriptions }} FCFA</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-yellow-500">
          <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Part Pensions</h3>
          <p class="text-2xl font-bold text-gray-800 mt-2">{{ caisseGenerale.part_pensions }} FCFA</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-slate-700">
          <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Scolarité Attendue</h3>
          <p class="text-2xl font-bold text-gray-800 mt-2">{{ caisseGenerale.total_scolarite_attendue }} FCFA</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-red-500">
          <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Reste à Percevoir</h3>
          <p class="text-2xl font-bold text-gray-800 mt-2">{{ caisseGenerale.reste_a_percevoir }} FCFA</p>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-sm">
        <h2 class="text-xl font-bold text-gray-700 mb-4">Statistiques par Vague d'Enseignement</h2>
        
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-200">
                <th class="p-4 text-gray-600 font-semibold">ID</th>
                <th class="p-4 text-gray-600 font-semibold">Nom de la Vague</th>
                <th class="p-4 text-gray-600 font-semibold">Nombre d'Élèves</th>
                <th class="p-4 text-gray-600 font-semibold">Total Encaissé</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="vague in statistiquesVagues" :key="vague.id" class="border-b border-gray-100 hover:bg-gray-50">
                <td class="p-4 text-gray-800">{{ vague.id }}</td>
                <td class="p-4 font-semibold text-gray-700">{{ vague.nom }}</td>
                <td class="p-4">
                  <span class="bg-cyan-100 text-cyan-800 px-2.5 py-1 rounded-full text-sm font-bold">
                    {{ vague.nombre_eleves }}
                  </span>
                </td>
                <td class="p-4 font-bold text-green-600">{{ vague.total_encaisse }} FCFA</td>
              </tr>
              <tr v-if="statistiquesVagues.length === 0">
                <td colspan="4" class="p-4 text-center text-gray-500">Aucune statistique disponible pour les vagues.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Initialisation propre pour éviter le crash au premier rendu HTML
const caisseGenerale = ref(null);
const statistiquesVagues = ref([]);
const loading = ref(true);
const error = ref(null);

const fetchDashboardData = async () => { // <-- Accolade ouverte ici
    try {
        loading.value = true;
        error.value = null;
        
        const token = localStorage.getItem('auth_token');
        
        // Regarde bien ici, les accolades sont à l'intérieur des parenthèses de get()
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
}; // <-- Accolade fermée ici avec le point-virgule

onMounted(() => {
    fetchDashboardData();
});
</script>