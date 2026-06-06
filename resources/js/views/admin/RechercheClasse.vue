<template>
  <div class="min-h-screen bg-gray-900 text-gray-100 font-sans flex flex-col justify-center items-center px-4">
    
    <div class="w-full max-w-md mb-6">
      <router-link to="/admin/dashboard" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-emerald-400 transition-colors group">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Retour au tableau de bord</span>
      </router-link>
    </div>

    <div class="w-full max-w-md bg-gray-800 rounded-2xl border border-gray-700 shadow-xl p-8">
      <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-white tracking-tight">Accéder à une classe</h2>
        <p class="text-sm text-gray-400 mt-1">Sélectionnez les critères précis du groupe à piloter.</p>
      </div>

      <form @submit.prevent="redirigerVersDetail" class="space-y-5">
        
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Filière / Formation</label>
          <select v-model="selection.formation" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500 transition-colors">
            <option value="" disabled selected>Choisir une filière...</option>
            <option v-for="f in criteres.formations" :key="f" :value="f">{{ f }}</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Niveau d'études</label>
          <select v-model="selection.niveau" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500 transition-colors">
            <option value="" disabled selected>Choisir un niveau...</option>
            <option v-for="n in criteres.niveaux" :key="n" :value="n">{{ n }}</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Vague de rentrée</label>
          <select v-model="selection.vagueId" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500 transition-colors">
            <option value="" disabled selected>Choisir une vague...</option>
            <option v-for="v in criteres.vagues" :key="v.id" :value="v.id">{{ v.nom }}</option>
          </select>
        </div>

        <button type="submit" :disabled="loading" class="w-full mt-2 inline-flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-500 disabled:bg-gray-700 rounded-lg shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
          <span v-if="loading">Chargement...</span>
          <span v-else class="inline-flex items-center gap-2">
            Ouvrir la classe
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </span>
        </button>

      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const loading = ref(false)

// Données reçues du Backend
const criteres = ref({
  formations: [],
  niveaux: [],
  vagues: []
})

// Ce que l'utilisateur sélectionne
const selection = ref({
  formation: '',
  niveau: '',
  vagueId: ''
})

onMounted(async () => {
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch('/api/v1/dashboard/criteres-recherche', {
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
      }
    })
    if (response.ok) {
      criteres.value = await response.json()
    }
  } catch (error) {
    console.error("Erreur lors de la récupération des critères :", error)
  }
})

// Redirection intelligente vers la page détail
const redirigerVersDetail = () => {
  // On envoie l'admin vers la page détail en passant les critères dans l'URL (Query Parameters)
  router.push({
    path: '/admin/detailclasse',
    query: {
      formation: selection.value.formation,
      niveau: selection.value.niveau,
      vague: selection.value.vagueId
    }
  })
}
</script>