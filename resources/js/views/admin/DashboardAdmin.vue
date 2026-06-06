<template>
  <div class="min-h-screen bg-gray-900 text-gray-100 font-sans">
    <header class="bg-gray-800 border-b border-gray-700 sticky top-0 z-50 px-6 py-4">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        
        <div>
          <h1 class="text-2xl font-bold text-white tracking-tight">Tableau de bord</h1>
          <p class="text-sm text-gray-400 mt-1">
            Vue d'ensemble de l'année académique <span class="text-emerald-400 font-medium">{{ anneeAcademique }}</span>
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
          <a href="/admin/gestionclasses" class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors duration-200 border border-gray-600">
            Gestion Classes
          </a>

          <a href="/admin/gestionvagues" class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors duration-200 border border-gray-600">
            Gestion Vagues
          </a>

          <a href="/eleves/inscriptioneleve" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-500 rounded-lg shadow-lg shadow-emerald-950/20 transition-all duration-200 transform hover:-translate-y-0.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Nouvel Étudiant
          </a>

          <button 
  @click="gererDeconnexion" 
  class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-400 bg-red-500/10 hover:bg-red-500 hover:text-white rounded-lg border border-red-500/20 transition-all duration-200"
>
  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
  </svg>
  Déconnexion
</button>
        </div>

      </div>
    </header>

    <main class="p-6 space-y-8 max-w-7xl mx-auto">
      
      <KpiCards />

      <StatCharts />

      <ClassesTable />

    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// Importation des composants découpés depuis le dossier js/components/
import KpiCards from '@/components/KpiCards.vue'
import StatCharts from '@/components/StatCharts.vue'
import ClassesTable from '@/components/ClassesTable.vue'
import { useRouter } from 'vue-router'

/**
 * Propriété calculée pour l'année académique.
 * Elle évite d'écrire l'année en dur dans le HTML et s'adapte automatiquement.
 */
const anneeAcademique = computed(() => {
  const dateActuelle = new Date()
  const anneeEnCours = dateActuelle.getFullYear()
  const moisEnCours = dateActuelle.getMonth() // 0 = Janvier, 5 = Juin, 8 = Septembre

  // Si on est à partir de septembre (mois >= 8), l'année scolaire est anneeEnCours / anneeEnCours + 1
  if (moisEnCours >= 8) {
    return `${anneeEnCours}-${anneeEnCours + 1}`
  } else {
    // Sinon (de janvier à août), on est dans la deuxième partie de l'année scolaire
    return `${anneeEnCours - 1}-${anneeEnCours}`
  }
})

const router = useRouter()
const gererDeconnexion = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    
    // 1. On avertit le serveur pour invalider le token
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
    // 2. Nettoyage local IMPÉRATIF (même si le serveur est KO)
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user_role')
    
    // 3. Redirection immédiate vers la page de connexion
    router.push('/login') // Ajuste le chemin selon ta route de Login
  }
}
</script>