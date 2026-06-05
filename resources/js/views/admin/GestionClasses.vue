<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto space-y-6">
      
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Gestion des Classes</h1>
          <p class="text-sm text-gray-500">Configurez les filières, les tarifs et associez-les aux vagues académiques.</p>
        </div>
        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Espace Admin</span>
      </div>

      <div v-if="error" class="bg-red-50 text-red-600 p-3 rounded-lg text-sm font-semibold border border-red-100">{{ error }}</div>
      <div v-if="success" class="bg-green-50 text-green-600 p-3 rounded-lg text-sm font-semibold border border-green-100">{{ success }}</div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-fit space-y-4">
          <h2 class="text-xl font-bold text-gray-700 pb-2 border-b border-gray-100">Ajouter une classe</h2>
          
          <form @submit.prevent="handleCreateClasse" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Nom de la filière / classe</label>
              <input v-model="form.nom" type="text" placeholder="ex: Génie Logiciel" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Niveau</label>
                <select v-model="form.niveau" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  <option value="licence 1">Licence 1</option>
                  <option value="licence 2">Licence 2</option>
                  <option value="licence 3">Licence 3</option>
                  <option value="master 1">Master 1</option>
                  <option value="master 2">Master 2</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Diminutif (code)</label>
                <input v-model="form.diminutif" type="text" placeholder="ex: gl" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Cursus</label>
                <select v-model="form.cursus" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  <option value="cj">Cours du Jour (cj)</option>
                  <option value="cs">Cours du Soir (cs)</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Vague de rentrée</label>
                <select v-model="form.vague_id" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  <option value="" disabled>Choisir une vague</option>
                  <option v-for="vague in vagues" :key="vague.id" :value="vague.id">
                    {{ vague.nom }}
                  </option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Mensualité (FCFA)</label>
                <input v-model="form.tarif_mensuel" type="number" min="0" placeholder="ex: 50000" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Inscription (FCFA)</label>
                <input v-model="form.frais_inscription" type="number" min="0" placeholder="ex: 120000" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
              </div>
            </div>

            <button type="submit" :disabled="loadingBtn" class="w-full bg-blue-600 text-white p-2.5 rounded-lg font-bold hover:bg-blue-700 transition duration-200 shadow-sm">
              {{ loadingBtn ? 'Création en cours...' : 'Enregistrer la classe' }}
            </button>
          </form>
        </div>

        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
          
          <div class="p-4 bg-gray-50 border-b border-gray-100 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <input v-model="searchQuery" type="text" placeholder="🔍 Rechercher une classe (ex: Génie)..." class="w-full p-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
              <select v-model="filterNiveau" class="w-full p-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-blue-500 focus:border-blue-500">
                <option value="">Tous les niveaux</option>
                <option value="licence 1">Licence 1</option>
                <option value="licence 2">Licence 2</option>
                <option value="licence 3">Licence 3</option>
                <option value="master 1">Master 1</option>
                <option value="master 2">Master 2</option>
              </select>
            </div>
            <div>
              <select v-model="filterVague" class="w-full p-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-blue-500 focus:border-blue-500">
                <option value="">Toutes les vagues</option>
                <option v-for="vague in vagues" :key="vague.id" :value="vague.id">{{ vague.nom }}</option>
              </select>
            </div>
          </div>

          <div class="overflow-x-auto flex-1">
            <div v-if="loadingTable" class="p-8 text-center text-gray-500">Chargement des données...</div>
            <div v-else-if="filteredClasses.length === 0" class="p-8 text-center text-gray-500">Aucune classe ne correspond à vos critères de recherche.</div>
            
            <table v-else class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold border-b border-gray-200">
                  <th class="p-4">Classe / Filière</th>
                  <th class="p-4">Détails techniques</th>
                  <th class="p-4">Vague Académique</th>
                  <th class="p-4">Grille Tarifaire</th>
                  <th class="p-4 text-center">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 text-gray-700">
                <tr v-for="classe in filteredClasses" :key="classe.id" class="hover:bg-gray-50 transition">
                  <td class="p-4">
                    <div class="font-bold text-gray-900">{{ classe.nom }}</div>
                    <div class="text-xs font-medium text-gray-500 capitalize">{{ classe.niveau }}</div>
                  </td>
                  
                  <td class="p-4 text-sm">
                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-mono mr-1 uppercase">{{ classe.diminutif }}</span>
                    <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-semibold uppercase">{{ classe.cursus }}</span>
                  </td>

                  <td class="p-4 text-sm text-gray-600 font-medium">
                    {{ classe.vague ? classe.vague.nom : 'Aucune vague' }}
                  </td>

                  <td class="p-4">
                    <div class="text-sm font-bold text-blue-600">{{ formatCurrency(classe.tarif_mensuel) }} / mois</div>
                    <div class="text-xs text-gray-500">Frais Inscription : {{ formatCurrency(classe.frais_inscription) }}</div>
                  </td>

                  <td class="p-4 text-center">
                    <button @click="handleDeleteClasse(classe.id)" class="text-red-500 hover:text-red-800 transition text-sm font-bold">
                      Supprimer
                    </button>
                  </td>
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
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

// --- États réactifs ---
const classes = ref([]);
const vagues = ref([]);
const loadingTable = ref(false);
const loadingBtn = ref(false);
const error = ref(null);
const success = ref(null);

// --- Filtres de recherche ---
const searchQuery = ref('');
const filterNiveau = ref('');
const filterVague = ref('');

// --- Structure réactive du formulaire alignée avec la validation Laravel ---
const form = ref({
    nom: '',
    niveau: 'licence 1',
    diminutif: '',
    cursus: 'cj',        
    vague_id: '', 
    tarif_mensuel: '',
    frais_inscription: ''
});

// --- API : Récupérer les classes (Charge la relation 'vague') ---
const fetchClasses = async () => {
    try {
        loadingTable.value = true;
        const token = localStorage.getItem('auth_token');
        const response = await axios.get('/api/v1/classes', {
            headers: { Authorization: `Bearer ${token}` }
        });
        classes.value = response.data; 
    } catch (err) {
        console.error(err);
        error.value = "Impossible de charger la liste des classes.";
    } finally {
        loadingTable.value = false;
    }
};

// --- API : Récupérer les vagues pour les menus déroulants ---
const fetchVagues = async () => {
    try {
        const token = localStorage.getItem('auth_token');
        const response = await axios.get('/api/v1/vagues', {
            headers: { Authorization: `Bearer ${token}` }
        });
        vagues.value = response.data;
    } catch (err) {
        console.error(err);
    }
};

// --- API : Soumettre le formulaire vers `ClasseController::store` ---
const handleCreateClasse = async () => {
    try {
        loadingBtn.value = true;
        error.value = null;
        success.value = null;
        
        const token = localStorage.getItem('auth_token');
        await axios.post('/api/v1/classes', form.value, {
            headers: { Authorization: `Bearer ${token}` }
        });

        success.value = "La classe a été configurée et enregistrée avec succès !";
        
        // Réinitialisation complète du formulaire après succès
        form.value = {
            nom: '',
            niveau: 'licence 1',
            diminutif: '',
            cursus: 'cj',
            vague_id: '',
            tarif_mensuel: '',
            frais_inscription: ''
        };
        
        await fetchClasses(); // Rafraîchit instantanément le tableau
    } catch (err) {
        console.error(err);
        error.value = err.response?.data?.message || "Erreur lors de la création de la classe.";
    } finally {
        loadingBtn.value = false;
    }
};

// --- API : Supprimer une classe via `ClasseController::destroy` ---
const handleDeleteClasse = async (id) => {
    if (!confirm("Êtes-vous sûr de vouloir supprimer définitivement cette classe ?")) return;
    
    try {
        error.value = null;
        success.value = null;
        const token = localStorage.getItem('auth_token');
        await axios.delete(`/api/v1/classes/${id}`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        success.value = "Classe supprimée avec succès.";
        await fetchClasses();
    } catch (err) {
        console.error(err);
        error.value = "Impossible de supprimer la classe.";
    }
};

// --- Propriété calculée (Computed) pour filtrer et rechercher en temps réel sans recharger la page ---
const filteredClasses = computed(() => {
    return classes.value.filter(classe => {
        // Filtre 1 : Recherche textuelle (insensible à la casse) sur le nom ou le diminutif
        const matchesSearch = classe.nom.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                              classe.diminutif.toLowerCase().includes(searchQuery.value.toLowerCase());
        
        // Filtre 2 : Filtrage par niveau d'étude
        const matchesNiveau = filterNiveau.value === '' || classe.niveau === filterNiveau.value;
        
        // Filtre 3 : Filtrage par identifiant de vague
        const matchesVague = filterVague.value === '' || classe.vague_id == filterVague.value;
        
        return matchesSearch && matchesNiveau && matchesVague;
    });
});

// --- Utilitaire pour le formatage monétaire (FCFA) ---
const formatCurrency = (value) => {
    return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
};

// --- Cycle de vie ---
onMounted(() => {
    fetchClasses();
    fetchVagues();
});
</script>