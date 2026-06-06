<template>
  <div class="min-h-screen bg-gray-900 text-gray-100 font-sans p-6">
    <div class="max-w-7xl mx-auto space-y-8">
      
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-gray-800 pb-6">
        <div>
          <router-link to="/admin/classes/recherche" class="inline-flex items-center gap-2 text-sm font-medium text-gray-400 hover:text-emerald-400 transition-colors group mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Retour aux critères</span>
          </router-link>
          <h1 class="text-3xl font-bold text-white tracking-tight">
            {{ loading ? 'Chargement...' : `${classeInfo.nom} — ${classeInfo.niveau}` }}
          </h1>
          <p class="text-sm text-gray-400 mt-1">
            Gestion de la cohorte : Vague <span class="text-emerald-400 font-medium">{{ classeInfo.vague }}</span>
          </p>
        </div>
      </div>

      <div v-if="error" class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl text-sm">
        {{ error }}
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-md flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-400">Effectif de la classe</p>
            <h3 class="text-3xl font-bold text-white mt-1">{{ kpis.effectif }} / {{ kpis.capacite_max }}</h3>
          </div>
          <div class="p-3 bg-blue-500/10 text-blue-400 rounded-lg">
            <i class="fa-solid fa-users text-xl"></i>
          </div>
        </div>

        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-md flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-400">Total Encaissé</p>
            <h3 class="text-3xl font-bold text-emerald-400 mt-1">{{ kpis.total_encaisse }} FCFA</h3>
          </div>
          <div class="p-3 bg-emerald-500/10 text-emerald-400 rounded-lg">
            <i class="fa-solid fa-wallet text-xl"></i>
          </div>
        </div>

        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-md flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-400">Reste à percevoir</p>
            <h3 class="text-3xl font-bold text-amber-500 mt-1">{{ kpis.reste_a_percevoir }} FCFA</h3>
          </div>
          <div class="p-3 bg-amber-500/10 text-amber-400 rounded-lg">
            <i class="fa-solid fa-circle-exclamation text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-700">
          <h3 class="text-lg font-bold text-white">Liste des étudiants inscrits</h3>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-gray-800/50 text-gray-400 text-xs uppercase tracking-wider font-semibold border-b border-gray-700">
                <th class="p-4 pl-6">Matricule</th>
                <th class="p-4">Nom Complet</th>
                <th class="p-4">Statut</th>
                <th class="p-4">Versements Effectués</th>
                <th class="p-4">État Financier</th>
                <th class="p-4 pr-6 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/50 text-sm text-gray-300">
              <tr v-for="eleve in eleves" :key="eleve.id" class="hover:bg-gray-700/20 transition-colors">
                <td class="p-4 pl-6 font-mono font-semibold text-emerald-400">{{ eleve.matricule || 'N/A' }}</td>
                <td class="p-4 font-semibold text-white">{{ eleve.prenom }} {{ eleve.nom }}</td>
                <td class="p-4">
                  <span :class="eleve.statut === 'Actif' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-gray-700 text-gray-300 border-gray-600'" class="px-2 py-0.5 text-xs rounded border font-medium">
                    {{ eleve.statut }}
                  </span>
                </td>
                <td class="p-4 font-mono">{{ eleve.total_paye }} FCFA</td>
                <td class="p-4">
                  <span v-if="eleve.est_a_jour" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-400">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> À jour
                  </span>
                  <span v-else class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-400">
                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span> En retard
                  </span>
                </td>
                <td class="p-4 pr-6 text-right">
                  <router-link :to="`/compta/paiement/${eleve.id}`" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 hover:underline transition-colors">
                    Voir Historique →
                  </router-link>
                </td>
              </tr>
              <tr v-if="eleves.length === 0 && !loading">
                <td colspan="6" class="p-8 text-center text-gray-500 text-sm">
                  Aucun étudiant trouvé dans ce groupe pour le moment.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const loading = ref(true)
const error = ref(null)

const classeInfo = ref({ nom: '', niveau: '', vague: '' })
const kpis = ref({ effectif: 0, capacite_max: 0, total_encaisse: 0, reste_a_percevoir: 0 })
const eleves = ref([])

onMounted(async () => {
  try {
    const token = localStorage.getItem('auth_token')
    
    // Extraction des critères depuis l'URL de recherche
    const { formation, niveau, vague } = route.query

    const response = await fetch(`/api/v1/dashboard/detail-classe?formation=${encodeURIComponent(formation)}&niveau=${encodeURIComponent(niveau)}&vague=${vague}`, {
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
      }
    })

    if (response.ok) {
      const data = await response.json()
      classeInfo.value = data.classe_info
      kpis.value = data.kpis
      eleves.value = data.eleves
    } else {
      const errData = await response.json()
      error.value = errData.message || "Impossible de charger les détails."
    }
  } catch (err) {
    console.error(err)
    error.value = "Une erreur réseau est survenue."
  } finally {
    loading.value = false;
  }
})
</script>