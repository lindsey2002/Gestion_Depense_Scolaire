<template>
  <div class="p-6 max-w-4xl mx-auto">
    
    <!-- ÉCRAN DE CHARGEMENT -->
    <div v-if="loadingPage" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
      <p class="text-gray-500 mt-4">Chargement du dossier de l'élève...</p>
    </div>

    

    <!-- BLOC PRINCIPAL -->
    <div v-else-if="currentEleve" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
      
      <div class="px-8 pt-6 pb-2 bg-slate-50 border-b border-slate-100">
        <button 
          @click="$router.back()" 
          class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-blue-600 transition-colors duration-200 group"
        >
          <svg 
            xmlns="http://www.w3.org/2000/svg" 
            class="h-4 w-4 transform group-hover:-translate-x-1 transition-transform duration-200" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor" 
            stroke-width="2.5"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          <span>Retour à la liste des élèves</span>
        </button>
      </div>
      
      <!-- ═══ EN-TÊTE ÉLÈVE ═══ -->
      <div style="background: linear-gradient(135deg, #1e3a5f 0%, #1e4d8c 60%, #2563a8 100%); padding: 1.75rem 2rem;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem">
          <div>
            <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,0.15); backdrop-filter:blur(4px); border:1px solid rgba(255,255,255,0.2); border-radius:20px; padding:4px 14px; margin-bottom:10px">
              <span style="width:7px;height:7px;border-radius:50%;background:#7dd3fc;display:inline-block"></span>
              <span style="font-size:11px; font-weight:700; letter-spacing:.08em; color:rgba(255,255,255,0.9); text-transform:uppercase">
                {{ currentEleve.matricule || 'Sans matricule' }}
              </span>
            </div>
            <h2 style="font-size:1.6rem; font-weight:800; color:#ffffff; letter-spacing:-.02em; line-height:1.2; margin-bottom:10px">
              {{ currentEleve.prenom }} {{ currentEleve.nom }}
            </h2>
            <div style="display:flex; flex-wrap:wrap; gap:8px">
              <span style="display:inline-flex;align-items:center;gap:5px; background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.18); border-radius:8px; padding:5px 12px; font-size:12px; color:rgba(255,255,255,0.9); font-weight:500">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                Niveau : {{ currentEleve.classe?.niveau?.nom || currentEleve.classe?.niveau || 'Non défini' }}
              </span>
              <span style="display:inline-flex;align-items:center;gap:5px; background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.18); border-radius:8px; padding:5px 12px; font-size:12px; color:rgba(255,255,255,0.9); font-weight:500">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                Classe : {{ currentEleve.classe?.nom }}
              </span>
              <span style="display:inline-flex;align-items:center;gap:5px; background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.18); border-radius:8px; padding:5px 12px; font-size:12px; color:rgba(255,255,255,0.9); font-weight:500">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Vague : {{ currentEleve.classe?.vague?.nom }}
              </span>
            </div>
          </div>
          <div style="text-align:right">
            <span style="display:block; font-size:10px; font-weight:600; letter-spacing:.07em; text-transform:uppercase; color:rgba(255,255,255,0.5); margin-bottom:6px">Statut financier</span>
            <span style="display:inline-flex; align-items:center; gap:6px; padding:7px 16px; border-radius:20px; font-size:12px; font-weight:700;"
                  :style="currentEleve.statut === 'en_attente' 
                    ? 'background:rgba(251,191,36,0.2); border:1px solid rgba(251,191,36,0.4); color:#fbbf24' 
                    : 'background:rgba(52,211,153,0.2); border:1px solid rgba(52,211,153,0.4); color:#34d399'">
              <span style="width:6px;height:6px;border-radius:50%;display:inline-block"
                    :style="currentEleve.statut === 'en_attente' ? 'background:#fbbf24' : 'background:#34d399'"></span>
              {{ currentEleve.statut === 'en_attente' ? 'Inscription Due' : 'Scolarité Active' }}
            </span>
          </div>
        </div>
      </div>

      <!-- ═══ REÇU (après paiement réussi) ═══ -->
      <div v-if="afficherRecu && paiementGenere" class="p-6">
        <div class="mb-5 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-r-lg text-sm flex items-start gap-2 print:hidden">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mt-0.5 shrink-0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          <span>Enregistrement réussi ! Le reçu de l'élève a été généré.</span>
        </div>

        <RecuPaiement :paiement="paiementGenere" :eleve="currentEleve" />

        <div class="text-center mt-6 print:hidden">
          <router-link 
            :to="{ name: 'compta.recherche' }" 
            class="px-5 py-2.5 text-sm font-medium bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-xl transition inline-block"
          >
            ← Retourner à la recherche
          </router-link>
        </div>
      </div>

      <!-- ═══ FORMULAIRE ═══ -->
      <div v-else class="p-6">

        <div v-if="error" class="mb-5 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-800 rounded-r-lg text-sm flex items-start gap-2">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mt-0.5 shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          {{ error }}
        </div>

        <form @submit.prevent="handleProcessPayment" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="space-y-4">
              <div>
                <label class="block text-xs uppercase tracking-wider text-slate-500 mb-2" style="font-weight:700;letter-spacing:.06em">Nature de l'encaissement</label>
                <select 
                  v-model="form.type_paiement" 
                  @change="adaptAmount"
                  class="w-full px-3 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm font-medium text-slate-700"
                >
                  <option value="inscription" :disabled="currentEleve.statut !== 'en_attente'">
                    Frais d'inscription ({{ formatCurrency(currentEleve.classe?.frais_inscription) }})
                  </option>
                  <option value="mensualite">Mensualité (Scolarité)</option>
                </select>
              </div>

              <div>
                <label class="block text-xs uppercase tracking-wider text-slate-500 mb-2" style="font-weight:700;letter-spacing:.06em">Mode de versement</label>
                <div class="grid grid-cols-3 gap-2">
                  <label v-for="mode in ['especes', 'wave', 'orange_money']" :key="mode"
                         class="border rounded-xl p-3 text-center cursor-pointer transition capitalize block"
                         :class="form.mode_paiement === mode 
                           ? 'border-blue-600 bg-blue-600 text-white font-semibold shadow-sm' 
                           : 'border-slate-200 text-slate-500 hover:border-blue-300 hover:bg-blue-50 bg-white'">
                    <input type="radio" v-model="form.mode_paiement" :value="mode" class="sr-only" />
                    <span class="text-xs font-medium">{{ mode.replace('_', ' ') }}</span>
                  </label>
                </div>
              </div>

              <div style="background: linear-gradient(135deg, #f0f7ff, #e8f2ff); border:1px solid #bfdbfe; border-radius:14px; padding:1.2rem 1.3rem">
                <span style="display:block; font-size:10px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:#3b82f6; margin-bottom:6px">Montant Total Émis</span>
                <span style="font-size:2rem; font-weight:900; color:#1e3a5f; display:block; letter-spacing:-.03em">{{ formatCurrency(form.montant) }}</span>
                <span v-if="form.type_paiement === 'mensualite'" style="font-size:11px; color:#6b7280; margin-top:4px; display:block">
                  {{ formatCurrency(currentEleve.classe?.tarif_mensuel) }} / mois × {{ form.mois.length }} mois
                </span>
              </div>
            </div>

            <div>
              <label class="block text-xs uppercase tracking-wider text-slate-500 mb-2" style="font-weight:700;letter-spacing:.06em">
                Sélection des mensualités
              </label>
              
              <div v-if="form.type_paiement === 'inscription'" 
                   style="background:#f8faff; border:2px dashed #bfdbfe; border-radius:14px; padding:2.5rem 1rem; text-align:center; height:280px; display:flex; flex-direction:column; justify-content:center; align-items:center; gap:8px">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#93c5fd" stroke-width="1.5" style="opacity:.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                <span style="font-size:13px; font-weight:500; color:#93c5fd">Grille indisponible<br>pour les frais d'inscription</span>
              </div>

              <div v-else class="grid grid-cols-2 gap-2 max-h-[300px] overflow-y-auto pr-1">
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
                  <span class="text-[10px] font-semibold tracking-wide uppercase">
                    {{ getMoisStatutClass(mois).label }}
                  </span>
                </button>
              </div>
            </div>

          </div>

          <div class="flex items-center justify-end space-x-3 border-t border-slate-100 pt-5">
            <router-link 
              :to="{ name: 'compta.recherche' }" 
              class="px-5 py-2.5 text-sm font-medium text-slate-500 hover:bg-slate-100 rounded-xl transition"
            >
              Annuler
            </router-link>
            <button 
              type="submit" 
              :disabled="loadingBtn || (form.type_paiement === 'mensualite' && form.mois.length === 0)"
              style="background:linear-gradient(135deg,#1e4d8c,#2563a8); color:#fff; border:none; border-radius:12px; padding:10px 28px; font-size:13px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:all .15s; box-shadow:0 4px 14px rgba(30,77,140,0.3)"
              :style="(loadingBtn || (form.type_paiement === 'mensualite' && form.mois.length === 0)) ? 'opacity:.5;cursor:not-allowed' : 'opacity:1'"
            >
              <span v-if="loadingBtn" class="animate-spin inline-block h-4 w-4 border-2 border-white border-b-transparent rounded-full"></span>
              <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              {{ loadingBtn ? 'Validation...' : 'Encaisser et valider' }}
            </button>
          </div>
        </form>
      </div>

    </div>

    <!-- ERREUR DE ROUTE -->
    <div v-else class="text-center py-12 bg-white rounded-xl border border-slate-200">
      <p class="text-slate-500 font-medium">Une erreur est survenue lors de la récupération des données.</p>
      <router-link :to="{ name: 'compta.recherche' }" class="mt-4 inline-block text-blue-600 font-semibold hover:underline">
        Retourner à la recherche
      </router-link>
    </div>

  </div>
</template>


<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import RecuPaiement from '@/views/compta/RecuPaiement.vue';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

const eleveId = route.params.id;

const paiementGenere = ref(null);
const afficherRecu = ref(false);
const currentEleve = ref(null);
const loadingBtn = ref(false);
const loadingPage = ref(true);
const error = ref(null);
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

const fetchCurrentEleve = async () => {
  try {
    loadingPage.value = true;
    const token = localStorage.getItem('auth_token');
    const response = await axios.get(`/api/v1/eleves/${eleveId}?include=paiements,classe.vague`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    currentEleve.value = response.data;
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

const moisDeLaVague = computed(() => {
    if (!currentEleve.value || !currentEleve.value.classe?.vague) return [];
    const vague = currentEleve.value.classe.vague;
    const moisDebutNom = vague.nom.toLowerCase().trim(); 
    const nomMoisTrouve = ordreMoisAnnee.find(m => moisDebutNom.includes(m)) || 'octobre';
    const indexDebut = ordreMoisAnnee.indexOf(nomMoisTrouve);
    const listeMoisGeneres = [];
    const totalMois = vague.nombre_mois || 9; 
    for (let i = 0; i < totalMois; i++) {
        listeMoisGeneres.push(ordreMoisAnnee[(indexDebut + i) % 12]);
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

const handleProcessPayment = async () => {
    loadingBtn.value = true;
    error.value = null;
    try {
        const token = localStorage.getItem('auth_token');
        const response = await axios.post('/api/v1/paiements', form.value, {
            headers: { Authorization: `Bearer ${token}` }
        });
        paiementGenere.value = response.data.paiement;
        afficherRecu.value = true;
    } catch (err) {
        console.error(err);
        error.value = err.response?.data?.message || "Une erreur est survenue lors de l'encaissement.";
    } finally {
        loadingBtn.value = false;
    }
};

const getMoisStatutClass = (nomMois) => {
  const statut = getStatutMois(nomMois);
  if (statut === 'paye') {
    return { style: 'bg-slate-100 border-slate-200 text-slate-400 cursor-not-allowed opacity-60', label: '✓ Réglé', disabled: true };
  }
  if (statut === 'bloque') {
    return { style: 'bg-blue-50 border-2 border-dashed border-blue-200 text-blue-300 cursor-not-allowed', label: '🔒 Ordre requis', disabled: true };
  }
  const estCoche = form.value.mois.includes(nomMois);
  if (estCoche) {
    return { style: 'bg-blue-700 border-blue-800 text-white font-bold shadow-md ring-2 ring-blue-300', label: '✓ À encaisser', disabled: false };
  }
  return { style: 'bg-white border-2 border-blue-400 text-blue-700 hover:bg-blue-50 font-semibold cursor-pointer', label: '💵 Disponible', disabled: false };
};

const formatCurrency = (val) => {
  if (!val) return '0 FCFA';
  return new Intl.NumberFormat('fr-FR').format(val) + ' FCFA';
};

onMounted(() => {
  fetchCurrentEleve();
});
</script>