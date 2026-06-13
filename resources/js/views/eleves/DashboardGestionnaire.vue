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
          <button @click="gererDeconnexion" class="text-gray-400 hover:text-red-400 p-1.5 rounded-lg hover:bg-gray-700 transition" title="Déconnexion">
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
          <p class="text-3xl font-bold text-white mt-2">{{ totalEleves }}</p>
        </div>
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-lg">
          <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Nouvelles Inscriptions (Ce mois)</p>
          <p class="text-3xl font-bold text-emerald-400 mt-2">{{ nouvellesInscriptions }}</p>
        </div>
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-lg">
          <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Classes Actives</p>
          <p class="text-3xl font-bold text-indigo-400 mt-2">{{ totalClasses }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
  
  <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-lg lg:col-span-1">
    <h3 class="text-lg font-semibold text-white mb-4">📢 Publier une information</h3>
    
    <div class="space-y-4">
      <div>
        <label class="text-xs font-medium text-gray-400 block mb-1">Titre</label>
        <input v-model="nouveauTitre" type="text" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm" />
      </div>
      
      <div>
        <label class="text-xs font-medium text-gray-400 block mb-1">Message</label>
        <textarea v-model="nouveauContenu" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm"></textarea>
      </div>

      <div>
        <label class="text-xs font-medium text-gray-400 block mb-1">Joindre un document (PDF, Image)</label>
        <input id="fileInput" type="file" @change="handleFileUpload" accept=".pdf,.png,.jpg,.jpeg" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-1.5 text-gray-400 text-sm file:mr-4 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-700 file:text-white hover:file:bg-gray-600" />
      </div>

      <button @click="publierAnnonce" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm py-2 px-4 rounded-lg transition-colors">
        Diffuser l'annonce
      </button>
    </div>
  </div>

  <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-lg lg:col-span-2">
    <h3 class="text-lg font-semibold text-white mb-4">Fil d'actualité de l'école</h3>
    
    <div v-if="annonces.length === 0" class="text-center py-12 text-gray-500 text-sm">
      Aucun communiqué diffusé pour le moment.
    </div>

    <div v-else class="space-y-4 max-h-[350px] overflow-y-auto pr-2">
      <div v-for="annonce in annonces" :key="annonce.id" class="bg-gray-900 p-4 rounded-xl border border-gray-700 flex flex-col justify-between gap-3">
        <div>
          <h4 class="text-base font-medium text-white">{{ annonce.titre }}</h4>
          <p class="text-sm text-gray-400 mt-1">{{ annonce.contenu }}</p>
        </div>

        <div v-if="annonce.fichier" class="max-w-max">
          <a :href="'http://127.0.0.1:8000/storage/' + annonce.fichier" target="_blank" class="inline-flex items-center gap-2 bg-gray-800 border border-gray-700 px-3 py-1.5 rounded-lg text-xs font-medium text-blue-400 hover:text-blue-300 transition-colors">
            📂 Voir la pièce jointe
          </a>
        </div>
      </div>
    </div>
  </div>

</div>

<div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-lg w-full">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-700 pb-4 mb-4">
          <div>
            <h3 class="text-lg font-semibold text-white">🗂️ Répertoire Général des Élèves</h3>
            <p class="text-xs text-gray-400 mt-0.5">Vérification instantanée de l'inscription d'un apprenant.</p>
          </div>
          
          <div class="w-full sm:w-80 relative">
            <input 
              v-model="termeRecherche" 
              type="text" 
              placeholder="Rechercher par nom, prénom, matricule..." 
              class="w-full bg-gray-900 border border-gray-700 rounded-xl pl-9 pr-4 py-2 text-sm text-white placeholder-gray-500 outline-none focus:border-emerald-500 transition-colors"
            />
            <span class="absolute left-3 top-2.5 text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </span>
          </div>
        </div>

        <div v-if="elevesList.length === 0" class="text-center py-8 text-gray-500 text-sm">
          Aucun élève enregistré dans le système.
        </div>
        <div v-else-if="elevesFiltrés.length === 0" class="text-center py-8 text-amber-400/80 text-sm">
          ⚠️ Aucun élève ne correspond à votre recherche.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-gray-900/60 text-[11px] uppercase tracking-wider text-gray-400 border-b border-gray-700">
                <th class="py-3 px-4 font-semibold">Matricule</th>
                <th class="py-3 px-4 font-semibold">Nom Complet</th>
                <th class="py-3 px-4 font-semibold">Classe affectée</th>
                <th class="py-3 px-4 font-semibold text-center">Statut Inscription</th>
              </tr>
            </thead>
            <tbody class="text-xs divide-y divide-gray-700/50 text-gray-300">
              <tr v-for="eleve in elevesFiltrés" :key="eleve.id" class="hover:bg-gray-700/30 transition-colors">
                <td class="py-3 px-4 font-mono text-emerald-400 font-bold tracking-wider">
                  {{ eleve.matricule || 'NON ASSIGNÉ' }}
                </td>
                <td class="py-3 px-4 font-medium text-white">
                  {{ eleve.prenom }} {{ eleve.nom }}
                </td>
                <td class="py-3 px-4 text-gray-400">
                  {{ eleve.classe?.libelle || eleve.classe_nom || 'Non défini' }}
                </td>
                <td class="py-3 px-4 text-center">
                  <span 
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide border"
                    :class="eleve.statut === 'inscrit' 
                      ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' 
                      : 'bg-amber-500/10 text-amber-400 border-amber-500/20'"
                  >
                    ● {{ eleve.statut }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'; // Import unique et propre ici
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const elevesList = ref([]);
const termeRecherche = ref('');
const userProfile = ref({ name: 'Gestionnaire', email: '' });

// KPIs Statistiques
const totalEleves = ref(0);
const nouvellesInscriptions = ref(0);
const totalClasses = ref(0);

// Gestion des Annonces
const annonces = ref([]);
const nouveauTitre = ref('');
const nouveauContenu = ref('');
const fichierSelectionne = ref(null);

const elevesFiltrés = computed(() => {
  const query = termeRecherche.value.toLowerCase().trim();
  if (!query) {
    return elevesList.value;
  }
  return elevesList.value.filter(eleve => {
    const nomComplet = `${eleve.prenom} ${eleve.nom}`.toLowerCase();
    const matricule = (eleve.matricule || '').toLowerCase();
    return nomComplet.includes(query) || matricule.includes(query);
  });
});

// 1. Charger les statistiques (KPIs) ET la liste des élèves
const fetchStats = async () => {
    try {
        const token = localStorage.getItem('auth_token');
        const response = await axios.get('/api/v1/dashboard/stats', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        
        if (response.data && response.data.kpis) {
            const dataKpis = response.data.kpis;
            totalEleves.value = dataKpis.totalEtudiants || 0;
            totalClasses.value = dataKpis.totalClasses || 0;
            nouvellesInscriptions.value = dataKpis.totalEtudiants || 0;
        }

        if (response.data && response.data.eleves) {
            elevesList.value = response.data.eleves;
        }
    } catch (err) {
        console.error("Erreur lors du chargement des KPIs du gestionnaire:", err);
    }
};

const fetchAnnonces = async () => {
    try {
        const token = localStorage.getItem('auth_token');
        const response = await axios.get('/api/v1/annonces', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        annonces.value = response.data;
    } catch (err) {
        console.error("Erreur récupération annonces:", err);
    }
};

onMounted(() => {
    userProfile.value.name = localStorage.getItem('user_name') || 'Gestionnaire ISI';
    userProfile.value.email = localStorage.getItem('user_email') || '';
    
    fetchStats();
    fetchAnnonces();
});

// Capturer le fichier lors de la sélection
const handleFileUpload = (event) => {
    fichierSelectionne.value = event.target.files[0];
};

const publierAnnonce = async () => {
    if (!nouveauTitre.value || !nouveauContenu.value) return;

    try {
        const token = localStorage.getItem('auth_token');
        
        const formData = new FormData();
        formData.append('titre', nouveauTitre.value);
        formData.append('contenu', nouveauContenu.value);
        if (fichierSelectionne.value) {
            formData.append('fichier', fichierSelectionne.value);
        }

        await axios.post('/api/v1/annonces', formData, {
            headers: { 
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'multipart/form-data'
            }
        });

        // Réinitialisation propre du formulaire
        nouveauTitre.value = '';
        nouveauContenu.value = '';
        fichierSelectionne.value = null;
        
        const fileInput = document.getElementById('fileInput');
        if (fileInput) fileInput.value = ''; 
        
        fetchAnnonces();
    } catch (err) {
        if (err.response && err.response.data) {
            console.error("VRAIE ERREUR DU SERVEUR LARAVEL :", err.response.data);
        } else {
            console.error("Erreur lors de la publication :", err);
        }
    }
};

const gererDeconnexion = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    
    await fetch('/api/v1/logout', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
      }
    })
  } catch (error) {
    console.error("Erreur lors de la déconnexion serveur :", error)
  } finally {
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user_role')
    router.push('/login') 
  }
}
</script>