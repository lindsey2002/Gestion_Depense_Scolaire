<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-100 space-y-6">
      
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Inscrire un nouvel élève</h1>
        <p class="text-sm text-gray-500">Création de la fiche de l'élève (Statut initial : En attente de validation).</p>
      </div>

      <div v-if="error" class="bg-red-50 text-red-600 p-3 rounded-lg text-sm font-semibold">{{ error }}</div>
      <div v-if="success" class="bg-green-50 text-green-600 p-3 rounded-lg text-sm font-semibold">{{ success }}</div>

      <form @submit.prevent="handleRegisterStudent" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Nom</label>
            <input v-model="form.nom" type="text" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Prénom</label>
            <input v-model="form.prenom" type="text" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Date de naissance</label>
          <input v-model="form.date_naissance" type="date" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Classe d'affectation</label>
          <select v-model="form.classe_id" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg bg-white focus:ring-orange-500 focus:border-orange-500">
            <option value="" disabled>Sélectionnez une classe</option>
            <option v-for="classe in classes" :key="classe.id" :value="classe.id">
              {{ classe.nom }} ({{ classe.niveau }})
            </option>
          </select>
        </div>

        <div class="pt-4">
          <button type="submit" :disabled="loading" class="w-full bg-orange-600 text-white p-3 rounded-lg font-bold hover:bg-orange-700 transition duration-200">
            {{ loading ? "Enregistrement en cours..." : "Enregistrer et mettre en attente" }}
          </button>
        </div>
      </form>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const classes = ref([]);
const loading = ref(false);
const error = ref(null);
const success = ref(null);

const form = ref({
    nom: '',
    prenom: '',
    date_naissance: '', 
    classe_id: ''
});

// Récupération des classes
const fetchClasses = async () => {
    try {
        const token = localStorage.getItem('auth_token');
        const response = await axios.get('/api/v1/classes', {
            headers: { Authorization: `Bearer ${token}` }
        });
        classes.value = response.data;
    } catch (err) {
        console.error(err);
        error.value = "Impossible de charger les classes.";
    }
};

// Soumission vers EleveController::store
const handleRegisterStudent = async () => {
    try {
        loading.value = true;
        error.value = null;
        success.value = null;

        const token = localStorage.getItem('auth_token');
        const response = await axios.post('/api/v1/eleves', form.value, {
            headers: { Authorization: `Bearer ${token}` }
        });

        success.value = `Fiche créée avec succès ! Matricule généré : ${response.data.eleve.matricule}. En attente du règlement des frais.`;
        
        // Réinitialisation du formulaire
        form.value = { nom: '', prenom: '', date_naissance: '', classe_id: '' };
    } catch (err) {
        console.error(err);
        error.value = err.response?.data?.message || "Erreur lors de l'enregistrement de l'élève.";
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchClasses();
});
</script>