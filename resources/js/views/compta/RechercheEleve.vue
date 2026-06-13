<template>
  <div class="p-6 max-w-xl mx-auto">
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
    
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Rechercher un élève pour encaissement</h2>

    <!-- Barre de recherche -->
    <div class="relative">
      <!-- Ajout de @keydown.enter pour intercepter la touche Entrée -->
      <input 
        v-model="searchQuery" 
        type="text" 
        @keydown.enter="validerParEntree"
        placeholder="Tapez le prénom, nom ou matricule et appuyez sur Entrée..." 
        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
      />
      
      <!-- Liste des résultats -->
      <ul v-if="elevesFiltres.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
        <li 
          v-for="eleve in elevesFiltres" 
          :key="eleve.id"
          @click="redirigerVersPaiement(eleve.id)"
          class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-none transition flex justify-between items-center"
        >
          <!-- Zone de texte de l'élève -->
          <div class="flex-1">
            <div class="font-medium text-gray-800">{{ eleve.prenom }} {{ eleve.nom }}</div>
            <div class="text-sm text-gray-500">
              Matricule : {{ eleve.matricule || 'Aucun' }} | Classe : {{ eleve.classe?.nom || 'Non assignée' }}
            </div>
          </div>
          
          <!-- Bouton visuel avec sécurité .stop pour intercepter le clic à coup sûr -->
          <button 
            type="button"
            @click.stop="redirigerVersPaiement(eleve.id)"
            class="text-xs bg-blue-600 text-white px-3 py-1.5 rounded-md font-medium hover:bg-blue-700 transition"
          >
            Sélectionner
          </button>
        </li>
      </ul>

      <!-- Aucun résultat trouvé -->
      <div v-if="searchQuery && elevesFiltres.length === 0" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg p-4 text-center text-gray-500 shadow-lg">
        Aucun élève trouvé pour "{{ searchQuery }}"
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const searchQuery = ref('');
const eleves = ref([]);

const fetchEleves = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    const response = await axios.get('/api/v1/eleves?include=classe', {
      headers: { Authorization: `Bearer ${token}` }
    });
    eleves.value = response.data;
  } catch (err) {
    console.error("Erreur lors du chargement des élèves :", err);
  }
};

const elevesFiltres = computed(() => {
  if (!searchQuery.value.trim()) return [];
  const recherche = searchQuery.value.toLowerCase().trim();
  
  return eleves.value.filter(eleve => {
    const prenom = eleve.prenom ? eleve.prenom.toLowerCase() : '';
    const nom = eleve.nom ? eleve.nom.toLowerCase() : '';
    const nomComplet = `${prenom} ${nom}`;
    const matricule = eleve.matricule ? eleve.matricule.toLowerCase() : '';
    
    return prenom.includes(recherche) || 
           nom.includes(recherche) || 
           nomComplet.includes(recherche) || 
           matricule.includes(recherche);
  });
});

const redirigerVersPaiement = (id) => {
  if (!id) return;
  router.push({ name: 'compta.paiement', params: { id: id } });
};

const validerParEntree = () => {
  if (elevesFiltres.value.length === 1) {
    redirigerVersPaiement(elevesFiltres.value[0].id);
  } else if (elevesFiltres.value.length > 1) {
    redirigerVersPaiement(elevesFiltres.value[0].id);
  }
};

onMounted(() => {
  fetchEleves();
});
</script>