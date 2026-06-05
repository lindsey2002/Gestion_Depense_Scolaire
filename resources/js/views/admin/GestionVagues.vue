<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto space-y-6">
      
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Gestion des Vagues de Rentrée</h1>
          <p class="text-sm text-gray-500">Configurez les périodes de rentrée et la durée des cursus pour les échéanciers.</p>
        </div>
        <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-0.5 rounded">Espace Admin</span>
      </div>

      <div v-if="error" class="bg-red-50 text-red-600 p-3 rounded-lg text-sm font-semibold">{{ error }}</div>
      <div v-if="success" class="bg-green-50 text-green-600 p-3 rounded-lg text-sm font-semibold">{{ success }}</div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-fit">
          <h2 class="text-xl font-bold text-gray-700 mb-4">Créer une vague</h2>
          <form @submit.prevent="handleCreateVague" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Nom de la vague</label>
              <input v-model="form.nom" type="text" placeholder="ex: Vague d'Octobre 2026" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Date de début de rentrée</label>
              <input v-model="form.date_debut" type="date" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Nombre de mois de scolarité</label>
              <input v-model="form.nombre_mois" type="number" placeholder="ex: 9" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">
            </div>

            <button type="submit" :disabled="loadingBtn" class="w-full bg-purple-600 text-white p-2.5 rounded-lg font-bold hover:bg-purple-700 transition duration-200">
              {{ loadingBtn ? 'Création...' : 'Enregistrer la vague' }}
            </button>
          </form>
        </div>

        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-700">Vagues configurées</h2>
          </div>

          <div v-if="loadingTable" class="p-6 text-center text-gray-500">Chargement des vagues...</div>

          <div v-else-if="vagues.length === 0" class="p-6 text-center text-gray-500">Aucune vague configurée pour le moment.</div>

          <table v-else class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-gray-50 text-gray-600 uppercase text-xs font-bold border-b border-gray-100">
                <th class="p-4">ID</th>
                <th class="p-4">Nom de la Vague</th>
                <th class="p-4">Date de début</th>
                <th class="p-4">Durée</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
              <tr v-for="vague in vagues" :key="vague.id" class="hover:bg-gray-50 transition">
                <td class="p-4 font-semibold text-gray-500">#{{ vague.id }}</td>
                <td class="p-4 font-bold text-gray-800">{{ vague.nom }}</td>
                <td class="p-4 font-semibold text-gray-600">{{ formatDate(vague.date_debut) }}</td>
                <td class="p-4 font-semibold text-purple-600">{{ vague.nombre_mois }} mois</td>
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

const vagues = ref([]);
const loadingTable = ref(false);
const loadingBtn = ref(false);
const error = ref(null);
const success = ref(null);

const form = ref({
    nom: '',
    date_debut: '',
    nombre_mois: ''
});

// Récupérer la liste des vagues (Route GET /api/v1/vagues)
const fetchVagues = async () => {
    try {
        loadingTable.value = true;
        const token = localStorage.getItem('auth_token');
        const response = await axios.get('/api/v1/vagues', {
            headers: { Authorization: `Bearer ${token}` }
        });
        vagues.value = response.data;
    } catch (err) {
        console.error(err);
        error.value = "Impossible de charger la liste des vagues.";
    } finally {
        loadingTable.value = false;
    }
};

// Enregistrer une nouvelle vague (Route POST /api/v1/vagues)
const handleCreateVague = async () => {
    try {
        loadingBtn.value = true;
        error.value = null;
        success.value = null;
        
        const token = localStorage.getItem('auth_token');
        await axios.post('/api/v1/vagues', form.value, {
            headers: { Authorization: `Bearer ${token}` }
        });

        success.value = "La vague a été configurée avec succès !";
        form.value = { nom: '', date_debut: '', nombre_mois: '' };
        
        await fetchVagues(); // Rafraîchissement
    } catch (err) {
        console.error(err);
        error.value = err.response?.data?.message || "Erreur lors de la création de la vague.";
    } finally {
        loadingBtn.value = false;
    }
};

// Formater la date pour l'affichage local
const formatDate = (dateString) => {
    if (!dateString) return '';
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('fr-FR', options);
};

onMounted(() => {
    fetchVagues();
});
</script>