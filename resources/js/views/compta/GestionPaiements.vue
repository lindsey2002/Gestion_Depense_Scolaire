<template>
  <div class="p-6 max-w-4xl mx-auto">
    
    <!-- 1. ÉCRAN DE CHARGEMENT (Pendant que l'API cherche l'élève) -->
    <div v-if="loadingPage" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
      <p class="text-gray-500 mt-4">Chargement du dossier de l'élève...</p>
    </div>

    <!-- 2. AFFICHAGE DU BLOC DE PAIEMENT UNIQUE -->
    <div v-else-if="currentEleve" class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
      
      <!-- En-tête avec les détails de l'élève sélectionné -->
      <div class="bg-gradient-to-r 'from-blue-600 to-blue-700' p-6 text-white">
        <div class="flex justify-between items-start">
          <div>
            <span class="text-xs font-semibold uppercase tracking-wider bg-white/20 px-2 py-1 rounded">
              {{ currentEleve.matricule || 'Sans Matricule' }}
            </span>
            <h2 class="text-2xl font-bold mt-1">{{ currentEleve.prenom }} {{ currentEleve.nom }}</h2>
            <p class="text-blue-100 text-sm mt-1">
                Niveau : {{ currentEleve.classe?.niveau?.nom || currentEleve.classe?.niveau || 'Non défini' }} | 
                Classe : {{ currentEleve.classe?.nom }} | 
                Vague : {{ currentEleve.classe?.vague?.nom }}
            </p>
          </div>
          <div class="text-right">
            <span class="block text-xs text-blue-200">Statut Financier</span>
            <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-medium"
                  :class="currentEleve.statut === 'en_attente' ? 'bg-orange-500 text-white' : 'bg-green-500 text-white'">
              {{ currentEleve.statut === 'en_attente' ? 'Inscription Due' : 'Scolarité Active' }}
            </span>
          </div>
        </div>
      </div>

      <!-- Corps du Formulaire de Caisse -->
      <div class="p-6">
        <!-- Messages d'Alerte -->
        <div v-if="error" class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg text-sm">
          {{ error }}
        </div>

        <form @submit.prevent="handleProcessPayment" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- CONFIGURATION TECHNIQUE -->
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nature de l'encaissement</label>
                <select 
                  v-model="form.type_paiement" 
                  @change="adaptAmount"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                >
                  <option value="inscription" :disabled="currentEleve.statut !== 'en_attente'">
                    Frais d'inscription ({{ formatCurrency(currentEleve.classe?.frais_inscription) }})
                  </option>
                  <option value="mensualite">Mensualité (Scolarité)</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Mode de versement</label>
                <div class="grid grid-cols-3 gap-3">
                  <label v-for="mode in ['especes', 'wave', 'orange_money']" :key="mode"
                         class="border rounded-lg p-3 text-center cursor-pointer transition capitalize block"
                         :class="form.mode_paiement === mode ? 'border-blue-600 bg-blue-50 text-blue-700 font-medium' : 'border-gray-200 text-gray-600 hover:bg-gray-50'">
                    <input type="radio" v-model="form.mode_paiement" :value="mode" class="sr-only" />
                    {{ mode.replace('_', ' ') }}
                  </label>
                </div>
              </div>

              <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                <span class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Montant Total Émis</span>
                <span class="text-3xl font-black text-gray-900 block mt-1">{{ formatCurrency(form.montant) }}</span>
                <span class="text-xs text-gray-500 mt-1 block" v-if="form.type_paiement === 'mensualite'">
                  Tarif : {{ formatCurrency(currentEleve.classe?.tarif_mensuel) }} / mois
                </span>
              </div>
            </div>

            <!-- GRILLE TRICOLORE DES MOIS (Affichée uniquement si type_paiement === 'mensualite') -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Sélection chronologique des mensualités
              </label>
              
              <div v-if="form.type_paiement === 'inscription'" class="bg-gray-50 border border-dashed border-gray-300 rounded-xl p-8 text-center text-gray-400 text-sm h-[280px] flex flex-col justify-center items-center">
                🔒 Grille indisponible pour les frais d'inscription.<br>Passez sur "Mensualité" pour l'activer.
              </div>

              <div v-else class="grid grid-cols-2 gap-3 max-h-[300px] overflow-y-auto pr-1">
                <button
                  type="button"
                  v-for="mois in moisDeLaVague"
                  :key="mois"
                  :disabled="getMoisStatutClass(mois).disabled"
                  @click="toggleMois(mois)"
                  class="p-3 rounded-xl border text-left transition flex flex-col justify-between h-16 outline-none"
                  :class="getMoisStatutClass(mois).style"
                >
                  <span class="font-bold text-sm capitalize">{{ mois }}</span>
                  <span class="text-[10px] font-medium tracking-wide uppercase">
                    {{ getMoisStatutClass(mois).label }}
                  </span>
                </button>
              </div>
            </div>

          </div>

          <!-- Boutons de commande -->
          <div class="flex items-center justify-end space-x-3 border-t border-gray-100 pt-4">
            <router-link 
              :to="{ name: 'compta.recherche' }" 
              class="px-5 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition"
            >
              Annuler et retourner
            </router-link>
            <button 
              type="submit" 
              :disabled="loadingBtn || (form.type_paiement === 'mensualite' && form.mois.length === 0)"
              class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
            >
              <span v-if="loadingBtn" class="animate-spin inline-block h-4 w-4 border-2 border-white border-b-transparent rounded-full"></span>
              <span>{{ loadingBtn ? 'Validation...' : 'Encaisser et valider' }}</span>
            </button>
          </div>
        </form>
      </div>

    </div>

    <!-- 3. GESTION DES ERREURS DE ROUTE -->
    <div v-else class="text-center py-12 bg-white rounded-xl border">
      <p class="text-red-500 font-medium">Une erreur est survenue lors de la récupération des données.</p>
      <router-link :to="{ name: 'compta.recherche' }" class="mt-4 inline-block text-blue-600 font-semibold hover:underline">
        Retourner à la recherche
      </router-link>
    </div>

  </div>
</template>


<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

// L'ID passé dans l'URL (/compta/paiement/4)
const eleveId = route.params.id;

// ÉTATS RÉACTIFS
const currentEleve = ref(null);
const loadingBtn = ref(false);
const loadingPage = ref(true); // Pour afficher un spinner pendant le chargement de l'élève
const error = ref(null);
const success = ref(null);
const moisSelectionnes = ref([]);

const form = ref({
    eleve_id: eleveId,
    montant: 0,
    mode_paiement: 'especes',
    type_paiement: 'inscription',
    mois: []
});

const ordreMoisAnnee = [
    'septembre', 'octobre', 'novembre', 'décembre', 
    'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août'
];

// 1. CHARGEMENT UNIQUE DE L'ÉLÈVE SÉLECTIONNÉ
const fetchCurrentEleve = async () => {
  try {
    loadingPage.value = true;
    const token = localStorage.getItem('auth_token');
    // On cible directement l'ID de l'élève avec ses relations nécessaires
    const response = await axios.get(`/api/v1/eleves/${eleveId}?include=paiements,classe.vague`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    
    currentEleve.value = response.data;

    // Configuration par défaut du formulaire après chargement
    if (currentEleve.value.statut === 'en_attente') {
        form.value.type_paiement = 'inscription';
        form.value.montant = currentEleve.value.classe?.frais_inscription || 0;
    } else {
        form.value.type_paiement = 'mensualite';
        form.value.montant = 0;
    }
  } catch (err) {
    console.error(err);
    error.value = "Impossible de récupérer la fiche de cet élève.";
  } finally {
    loadingPage.value = false;
  }
};

// 2. LOGIQUE DU CALENDRIER (Inchangée mais ciblée sur l'élève unique)
const moisDeLaVague = computed(() => {
    if (!currentEleve.value || !currentEleve.value.classe?.vague) return [];
    const vague = currentEleve.value.classe.vague;
    const moisDebutNom = vague.nom.toLowerCase().trim(); 
    const nomMoisTrouve = ordreMoisAnnee.find(m => moisDebutNom.includes(m)) || 'octobre';
    let indexDebut = ordreMoisAnnee.indexOf(nomMoisTrouve);
    
    const listeMoisGeneres = [];
    const totalMois = vague.nombre_mois || 9; 
    
    for (let i = 0; i < totalMois; i++) {
        const indexActuel = (indexDebut + i) % 12;
        listeMoisGeneres.push(ordreMoisAnnee[indexActuel]);
    }
    return listeMoisGeneres;
});

const getStatutMois = (nomMois) => {
    if (!currentEleve.value) return 'bloque';

    const dejaPayeEnBase = currentEleve.value.paiements?.some(
        p => p.type_paiement === 'mensualite' && p.mois === nomMois
    );
    if (dejaPayeEnBase) return 'paye';

    const indexMoisDansVague = moisDeLaVague.value.indexOf(nomMois);
    for (let i = 0; i < indexMoisDansVague; i++) {
        const moisPrecedent = moisDeLaVague.value[i];
        const payeEnBase = currentEleve.value.paiements?.some(
            p => p.type_paiement === 'mensualite' && p.mois === moisPrecedent
        );
        const cocheDansFormulaire = moisSelectionnes.value.includes(moisPrecedent);
        if (!payeEnBase && !cocheDansFormulaire) return 'bloque';
    }
    return 'payable';
};

const toggleMois = (nomMois) => {
    const statut = getStatutMois(nomMois);
    if (statut === 'paye' || statut === 'bloque') return;

    const index = moisSelectionnes.value.indexOf(nomMois);
    if (index > -1) {
        const indexDansVague = moisDeLaVague.value.indexOf(nomMois);
        moisSelectionnes.value = moisSelectionnes.value.filter(m => {
            return moisDeLaVague.value.indexOf(m) < indexDansVague;
        });
    } else {
        moisSelectionnes.value.push(nomMois);
    }
    form.value.mois = moisSelectionnes.value;
    form.value.montant = moisSelectionnes.value.length * (currentEleve.value.classe?.tarif_mensuel || 0);
};

const adaptAmount = () => {
    if (!currentEleve.value) return;
    if (form.value.type_paiement === 'inscription') {
        form.value.montant = currentEleve.value.classe?.frais_inscription || 0;
        moisSelectionnes.value = [];
        form.value.mois = [];
    } else {
        form.value.montant = moisSelectionnes.value.length * (currentEleve.value.classe?.tarif_mensuel || 0);
    }
};

// 3. SOUCOUTE COMPTABLE ET RETOUR À LA RECHERCHE
const handleProcessPayment = async () => {
    try {
        loadingBtn.value = true;
        error.value = null;

        if (form.value.type_paiement === 'mensualite' && form.value.mois.length === 0) {
            error.value = "Veuillez cocher au moins un mois à encaisser.";
            loadingBtn.value = false;
            return;
        }

        const token = localStorage.getItem('auth_token');
        await axios.post('/api/v1/paiements', form.value, {
            headers: { Authorization: `Bearer ${token}` }
        });

        // Succès ! On redirige immédiatement le comptable vers la recherche pour l'élève suivant
        router.push({ name: 'compta.recherche' });
    } catch (err) {
        console.error(err);
        error.value = err.response?.data?.message || "Erreur lors du traitement.";
    } finally {
        loadingBtn.value = false;
    }
};

const getMoisStatutClass = (nomMois) => {
  const statut = getStatutMois(nomMois);
  
  // 1. Déjà Payé : Gris clair, texte barré ou discret
  if (statut === 'paye') {
    return { 
      style: 'bg-gray-100 border-gray-200 text-gray-400 cursor-not-allowed opacity-70', 
      label: '✅ Réglé', 
      disabled: true 
    };
  }
  
  // 2. Bloqué (Pas de saut) : Fond blanc, texte rouge/orange bien visible avec un cadenas
  if (statut === 'bloque') {
    return { 
      style: 'bg-white border-2 border-dashed border-red-200 text-red-500 cursor-not-allowed font-medium', 
      label: '🔒 Bloqué (Suivre l\'ordre)', 
      disabled: true 
    };
  }
  
  // 3. Disponible et Coché par le comptable : Bleu roi intense, écriture blanche
  const estCoche = form.value.mois.includes(nomMois);
  if (estCoche) {
    return { 
      style: 'bg-blue-700 border-blue-800 text-white font-bold shadow-md transform scale-102 ring-2 ring-blue-400', 
      label: '🎯 À encaisser', 
      disabled: false 
    };
  }
  
  // 4. Disponible mais pas encore coché : Fond blanc, bordure bleue vive, texte bleu très lisible
  return { 
    style: 'bg-white border-2 border-blue-600 text-blue-700 hover:bg-blue-50 font-semibold cursor-pointer shadow-sm', 
    label: '💵 Disponible', 
    disabled: false 
  };
};

const formatCurrency = (val) => {
  if (!val) return '0 FCFA';
  return new Intl.NumberFormat('fr-FR').format(val) + ' FCFA';
};

onMounted(() => {
  fetchCurrentEleve();
});
</script>