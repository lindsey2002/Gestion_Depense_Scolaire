<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-100 space-y-6">
      <div class="mb-4">
      <button 
        @click="$router.push({ name: 'ComptaDashboard' })" 
        class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-800 transition-colors group"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Retour au Tableau de bord
      </button>
    </div>
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Inscrire un nouvel élève</h1>
        <p class="text-sm text-gray-500">Création de la fiche de l'élève (Statut initial : En attente de validation).</p>
      </div>

      <div v-if="error" class="bg-red-50 text-red-600 p-3 rounded-lg text-sm font-semibold">{{ error }}</div>
      <div v-if="success" class="bg-green-50 text-green-600 p-3 rounded-lg text-sm font-semibold">{{ success }}</div>

      <form @submit.prevent="handleRegisterStudent" class="space-y-6">
        
        <div class="space-y-4">
          <h2 class="text-sm font-bold text-orange-600 uppercase tracking-wider border-b pb-1">Informations de l'élève</h2>
          
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
        </div>

        <div class="space-y-4 pt-2">
          <h2 class="text-sm font-bold text-orange-600 uppercase tracking-wider border-b pb-1">Informations du Tuteur / Parent</h2>
          <p class="text-[11px] text-gray-400 -mt-2 italic">Si l'email existe déjà, le système rattachera automatiquement l'enfant à ce parent.</p>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Prénom du tuteur</label>
              <input v-model="form.parent_prenom" type="text" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Nom du tuteur</label>
              <input v-model="form.parent_nom" type="text" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Adresse Email du tuteur</label>
            <input v-model="form.parent_email" type="email" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
          </div>
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
    classe_id: '',
    // Nouveaux champs transmis
    parent_prenom: '',
    parent_nom: '',
    parent_email: ''
});

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

const handleRegisterStudent = async () => {
    try {
        loading.value = true;
        error.value = null;
        success.value = null;

        const token = localStorage.getItem('auth_token');
        const response = await axios.post('/api/v1/eleves', form.value, {
            headers: { Authorization: `Bearer ${token}` }
        });

        success.value = `Fiche créée avec succès ! Matricule généré : ${response.data.eleve.matricule}. Un compte d'accès parent a été configuré/associé.`;
        
        // Réinitialisation complète du formulaire
        form.value = { 
            nom: '', 
            prenom: '', 
            date_naissance: '', 
            classe_id: '',
            parent_prenom: '',
            parent_nom: '',
            parent_email: ''
        };
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

