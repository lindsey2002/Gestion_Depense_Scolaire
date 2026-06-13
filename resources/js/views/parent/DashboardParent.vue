<template>
  <div class="p-6 bg-slate-50 min-height-screen">
    
    <div class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Espace Parent</h1>
        <p class="text-sm text-gray-500">Suivi financier et scolarité de vos enfants</p>
      </div>
      <button @click="gererDeconnexion" class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-xl hover:bg-red-600 transition">
        Déconnexion
      </button>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 width-12 border-b-2 border-sky-600"></div>
    </div>

    <div v-else-if="error" class="p-4 mb-6 bg-red-50 border border-red-200 text-red-700 rounded-xl">
      {{ error }}
    </div>

    <div v-else>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="p-6 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4">
          <div class="p-3 bg-sky-50 text-sky-600 rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
          </div>
          <div>
            <p class="text-sm text-gray-500 font-medium">Nombre d'enfants</p>
            <p class="text-2xl font-bold text-gray-900">{{ kpis.nombre_enfants }}</p>
          </div>
        </div>

        <div class="p-6 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4">
          <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
          </div>
          <div>
            <p class="text-sm text-gray-500 font-medium">Total Dépensé (Payé)</p>
            <p class="text-2xl font-bold text-emerald-600">{{ formatDevise(kpis.total_general_depense) }}</p>
          </div>
        </div>

        <div class="p-6 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4">
          <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          </div>
          <div>
            <p class="text-sm text-gray-500 font-medium">Reste à payer</p>
            <p class="text-2xl font-bold text-amber-600">{{ formatDevise(kpis.total_general_restant) }}</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-6">
          <h2 class="text-lg font-bold text-gray-800">Scolarité par Enfant</h2>
          
          <div v-for="enfant in enfants" :key="enfant.id" class="p-6 bg-white rounded-2xl shadow-sm border border-slate-100">
            <div class="flex justify-between items-start border-b border-slate-100 pb-4 mb-4">
              <div>
                <h3 class="text-base font-bold text-gray-900">{{ enfant.nom }}</h3>
                <p class="text-xs text-gray-500">Matricule : <span class="font-mono font-semibold text-gray-700">{{ enfant.matricule }}</span></p>
              </div>
              <span class="px-3 py-1 text-xs font-semibold rounded-full uppercase"
                    :class="enfant.statut === 'inscrit' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'">
                {{ enfant.statut === 'en_attente' ? 'En attente d\'inscription' : 'Inscrit' }}
              </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4 text-xs">
              <div class="p-3 bg-slate-50 rounded-xl">
                <p class="text-gray-400 mb-1">Classe</p>
                <p class="font-bold text-gray-800">{{ enfant.classe?.libelle || 'Non assignée' }}</p>
              </div>
              <div class="p-3 bg-slate-50 rounded-xl">
                <p class="text-gray-400 mb-1">Frais Inscription</p>
                <p class="font-bold text-gray-800">{{ formatDevise(enfant.details_financiers.paye_inscription) }}</p>
              </div>
              <div class="p-3 bg-slate-50 rounded-xl">
                <p class="text-gray-400 mb-1">Mensualités Payées</p>
                <p class="font-bold text-emerald-600">{{ formatDevise(enfant.details_financiers.paye_mensualites) }}</p>
              </div>
              <div class="p-3 bg-slate-50 rounded-xl">
                <p class="text-gray-400 mb-1">Reste à payer</p>
                <p class="font-bold text-amber-600">{{ formatDevise(enfant.details_financiers.reste_a_payer) }}</p>
              </div>
            </div>

            <div v-if="enfant.statut === 'inscrit'">
              <p class="text-xs font-semibold text-gray-600 mb-2">Suivi et état des mensualités :</p>
              
              <div class="flex flex-wrap gap-2">
                <span 
                  v-for="mois in genererMoisVague(enfant)" 
                  :key="mois" 
                  class="px-2.5 py-1 text-[10px] font-bold rounded-lg uppercase tracking-wider transition-colors border flex items-center gap-1.5"
                  :class="enfant.details_financiers.mois_payes.includes(mois.toLowerCase()) 
                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200' 
                    : 'bg-rose-50 text-rose-700 border-rose-200'"
                >
                  <span 
                    class="w-1.5 h-1.5 rounded-full" 
                    :class="enfant.details_financiers.mois_payes.includes(mois.toLowerCase()) ? 'bg-emerald-500' : 'bg-rose-500'"
                  ></span>
                  
                  {{ enfant.details_financiers.mois_payes.includes(mois.toLowerCase()) ? '✓' : '✗' }} {{ mois }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div class="space-y-6">
          <h2 class="text-lg font-bold text-gray-800">Informations & Notes</h2>
          <div class="p-4 bg-white rounded-2xl shadow-sm border border-slate-100 max-h-[600px] overflow-y-auto space-y-4">
            
            <div v-for="annonce in annonces" :key="annonce.id" class="p-4 bg-slate-50 rounded-xl border border-slate-100">
              <h3 class="text-sm font-bold text-gray-900 mb-1">{{ annonce.titre }}</h3>
              <p class="text-xs text-gray-600 leading-relaxed mb-3 whitespace-pre-line">{{ annonce.contenu }}</p>
              
              <div class="flex justify-between items-center text-[10px] text-gray-400 pt-2 border-t border-slate-200">
                <span>Posté le {{ formatDate(annonce.created_at) }}</span>
                
                <a v-if="annonce.fichier" :href="`/${annonce.fichier}`" target="_blank" 
                   class="text-sky-600 hover:underline font-semibold flex items-center space-x-1">
                  <span>Télécharger la pièce</span>
                </a>
              </div>
            </div>

            <p v-if="annonces.length === 0" class="text-sm text-gray-400 text-center py-6 italic">
              Aucune annonce disponible.
            </p>
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

const kpis = ref({ nombre_enfants: 0, total_general_depense: 0, total_general_restant: 0 });
const enfants = ref([]);
const annonces = ref([]);
const loading = ref(true);
const error = ref(null);


const moisAnneeScolaire = [
  'Octobre', 'Novembre', 'Décembre', 'Janvier', 
  'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet',
  'Aout', 'Septembre'
];

const fetchParentData = async () => {
  try {
    loading.value = true;
    error.value = null;
    const token = localStorage.getItem('auth_token');

    const response = await axios.get('/api/v1/parent/dashboard', {
      headers: { Authorization: `Bearer ${token}` }
    });

    if (response.data) {
      kpis.value = response.data.kpis_globaux;
      enfants.value = response.data.enfants;
      annonces.value = response.data.annonces;
    }
  } catch (err) {
    console.error("Erreur lors de la récupération du dashboard parent :", err);
    error.value = "Impossible de récupérer les informations de votre espace parent.";
  } finally {
    loading.value = false;
  }
};

const genererMoisVague = (enfant) => {
  const ordreMoisAnnee = [
    'septembre', 'octobre', 'novembre', 'décembre', 
    'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août'
  ];

  // Si pas de vague trouvée, on retourne une année scolaire standard de 9 mois par défaut par sécurité
  if (!enfant.classe || !enfant.classe.vague) {
    return ['Octobre', 'Novembre', 'Décembre', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin'];
  }

  const vague = enfant.classe.vague;
  const moisDebutNom = vague.nom.toLowerCase().trim();
  
  // Trouve le mois de début correspondant dans notre référentiel
  const nomMoisTrouve = ordreMoisAnnee.find(m => moisDebutNom.includes(m)) || 'octobre';
  const indexDebut = ordreMoisAnnee.indexOf(nomMoisTrouve);
  
  const listeMoisGeneres = [];
  const totalMois = vague.nombre_mois || 9;

  for (let i = 0; i < totalMois; i++) {
    const moisMinuscule = ordreMoisAnnee[(indexDebut + i) % 12];
    // Met la première lettre en majuscule pour un affichage élégant à l'écran
    const moisFormate = moisMinuscule.charAt(0).toUpperCase() + moisMinuscule.slice(1);
    listeMoisGeneres.push(moisFormate);
  }

  return listeMoisGeneres;
};

const formatDevise = (valeur) => {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF', minimumFractionDigits: 0 }).format(valeur);
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
};

const gererDeconnexion = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    await axios.post('/api/v1/logout', {}, { headers: { Authorization: `Bearer ${token}` } });
  } catch (err) {
    console.error(err);
  } finally {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_role');
    router.push('/login');
  }
};

onMounted(() => {
  fetchParentData();
});
</script>