<template>
  <div class="p-6 max-w-7xl mx-auto space-y-6">
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Recettes Encaissées</span>
        <span class="text-2xl font-black text-emerald-600 mt-2">{{ formatCurrency(totalRecettes) }}</span>
      </div>

      <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Dépenses Émises</span>
        <span class="text-2xl font-black text-rose-600 mt-2">- {{ formatCurrency(totalDepenses) }}</span>
      </div>

      <div class="rounded-2xl p-5 shadow-sm flex flex-col justify-between border"
           :class="soldeCaisse < 50000 ? 'bg-amber-50 border-amber-200 text-amber-900' : 'bg-blue-50 border-blue-100 text-blue-950'">
        <span class="text-xs font-bold uppercase tracking-wider" :class="soldeCaisse < 50000 ? 'text-amber-600' : 'text-blue-500'">
          Solde Disponible en Caisse
        </span>
        <span class="text-2xl font-black mt-2">{{ formatCurrency(soldeCaisse) }}</span>
      </div>
    </div>

    <div v-if="successMessage" class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-xl text-sm flex items-center gap-2">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      {{ successMessage }}
    </div>

    <div v-if="error" class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-xl text-sm flex items-center gap-2">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      {{ error }}
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden h-fit">
        <div class="p-5 border-b border-slate-50" :class="estEnModeEdition ? 'bg-amber-50/50' : 'bg-transparent'">
          <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide">
            {{ estEnModeEdition ? '📝 Modifier la dépense' : '➕ Saisir une dépense' }}
          </h3>
        </div>

        <form @submit.prevent="soumettreFormulaire" class="p-5 space-y-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Catégorie</label>
            <select v-model="form.categorie" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 text-sm font-medium text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
              <option value="fournitures">Fournitures de bureau</option>
              <option value="salaires">Salaires & Vacations</option>
              <option value="entretien">Entretien & Réparations</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Montant (FCFA)</label>
            <input type="number" v-model.number="form.montant" required min="1" placeholder="Ex: 25000" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 text-sm font-medium text-slate-700 outline-none focus:ring-2 focus:ring-blue-500" />
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Date d'effet</label>
            <input type="date" v-model="form.date" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 text-sm font-medium text-slate-700 outline-none focus:ring-2 focus:ring-blue-500" />
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Description / Motif (Max 700 car.)</label>
            <textarea v-model="form.description" required rows="4" max="700" placeholder="Précisez le motif exact de la dépense..." class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 text-sm font-medium text-slate-700 outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
          </div>

          <div class="flex items-center justify-end gap-2 pt-2">
            <button v-if="estEnModeEdition" type="button" @click="annulerEdition" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition">
              Annuler
            </button>
            <button type="submit" :disabled="loadingBtn" class="px-5 py-2 text-xs font-bold text-white rounded-xl transition flex items-center gap-1.5 shadow-sm"
                    :class="estEnModeEdition ? 'bg-amber-600 hover:bg-amber-700' : 'bg-slate-800 hover:bg-slate-900'">
              <span v-if="loadingBtn" class="animate-spin h-3 w-3 border-2 border-white border-b-transparent rounded-full"></span>
              {{ estEnModeEdition ? 'Enregistrer les modifications' : 'Valider la dépense' }}
            </button>
          </div>
        </form>
      </div>

      <div class="lg:grid-cols-1 lg:col-span-2 bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden flex flex-col justify-between">
        <div class="p-5 border-b border-slate-50 flex justify-between items-center">
          <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Historique des mouvements de sortie</h3>
          <span class="text-xs bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full font-semibold">{{ depenses.length }} enregistrement(s)</span>
        </div>

        <div v-if="loading" class="p-12 text-center text-slate-400 text-sm">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-slate-800 mx-auto mb-3"></div>
          Chargement du grand livre des dépenses...
        </div>

        <div v-else-if="depenses.length === 0" class="p-12 text-center text-slate-400 text-sm">
          Aucun mouvement de dépenses enregistré pour le moment.
        </div>

        <div v-else class="overflow-x-auto flex-1">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400 border-b border-slate-100">
                <th class="py-3 px-4 font-bold">Date</th>
                <th class="py-3 px-4 font-bold">Catégorie</th>
                <th class="py-3 px-4 font-bold">Motif / Description</th>
                <th class="py-3 px-4 font-bold text-right">Montant</th>
                <th class="py-3 px-4 font-bold text-center">Actions</th>
              </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
              <tr v-for="depense in depenses" :key="depense.id" class="hover:bg-slate-50/50 transition">
                <td class="py-3.5 px-4 font-medium whitespace-nowrap">
                  {{ new Date(depense.date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                </td>
                <td class="py-3.5 px-4 whitespace-nowrap">
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide"
                        :class="{
                          'bg-purple-50 text-purple-700 border border-purple-100': depense.categorie === 'fournitures',
                          'bg-blue-50 text-blue-700 border border-blue-100': depense.categorie === 'salaires',
                          'bg-amber-50 text-amber-700 border border-amber-100': depense.categorie === 'entretien'
                        }">
                    {{ depense.categorie }}
                  </span>
                </td>
                <td class="py-3.5 px-4 max-w-xs break-words font-medium text-slate-500">
                  {{ depense.description }}
                </td>
                <td class="py-3.5 px-4 text-right font-bold text-slate-900 whitespace-nowrap">
                  {{ formatCurrency(depense.montant) }}
                </td>
                <td class="py-3.5 px-4">
                  <div class="flex items-center justify-center gap-1">
                    <button @click="activerEdition(depense)" title="Modifier" class="p-1.5 hover:bg-amber-50 text-slate-400 hover:text-amber-600 rounded-lg transition">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"/></svg>
                    </button>
                    <button @click="confirmerSuppression(depense.id)" title="Supprimer" class="p-1.5 hover:bg-rose-50 text-slate-400 hover:text-rose-600 rounded-lg transition">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <div v-if="montrePopupSuppression" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
      
      <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 transform transition-all animate-in fade-in zoom-in-95 duration-200">
        
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-rose-50 text-rose-600 mb-4">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2M10 11v6M14 11v6"/>
          </svg>
        </div>

        <div class="text-center space-y-2">
          <h3 class="text-base font-bold text-slate-950">Confirmer la suppression</h3>
          <p class="text-sm text-slate-500">
            Êtes-vous sûre de vouloir supprimer définitivement cette dépense ? Cette action annulera le débit et réajustera le solde de la caisse.
          </p>
        </div>

        <div class="flex items-center justify-center gap-3 mt-6">
          <button type="button" @click="annulerSuppression" :disabled="loadingBtn" class="w-full px-4 py-2.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
            Non, Annuler
          </button>
          
          <button type="button" @click="supprimerDepense" :disabled="loadingBtn" class="w-full px-4 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm shadow-rose-100">
            <span v-if="loadingBtn" class="animate-spin h-3 w-3 border-2 border-white border-b-transparent rounded-full"></span>
            Oui, Supprimer
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useGestionDepense } from '@/views/compta/useGestionDepense.js';

// Destructuration complète de la logique depuis ton Composable externe
const {
  depenses, totalRecettes, loading, loadingBtn, error, successMessage,
  estEnModeEdition, form, totalDepenses, soldeCaisse,
  chargerDonnees, soumettreFormulaire, supprimerDepense,
  activerEdition, annulerEdition, formatCurrency,
  montrePopupSuppression, confirmerSuppression, annulerSuppression
} = useGestionDepense();

// Déclenchement automatique du chargement dès l'apparition de la page
onMounted(() => {
  chargerDonnees();
});
</script>