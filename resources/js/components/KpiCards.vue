<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    
    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-md">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-400">Total Étudiants</p>
          <h3 class="text-3xl font-bold text-white mt-1">
            {{ stats.totalEtudiants ?? '-' }}
          </h3>
        </div>
        <div class="p-3 bg-blue-500/10 text-blue-400 rounded-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        </div>
      </div>
    </div>

    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-md">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-400">Classes Actives</p>
          <h3 class="text-3xl font-bold text-white mt-1">
            {{ stats.totalClasses ?? '-' }}
          </h3>
        </div>
        <div class="p-3 bg-purple-500/10 text-purple-400 rounded-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
        </div>
      </div>
    </div>

    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-md">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-400">Vagues Ouvertes</p>
          <h3 class="text-3xl font-bold text-white mt-1">
            {{ stats.totalVagues ?? '-' }}
          </h3>
        </div>
        <div class="p-3 bg-amber-500/10 text-amber-400 rounded-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.253 8H18" />
          </svg>
        </div>
      </div>
    </div>

    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-md">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-400">Recettes Globales</p>
          <h3 class="text-3xl font-bold text-emerald-400 mt-1">
            {{ stats.totalRecettes ?? '-' }} FCFA
          </h3>
        </div>
        <div class="p-3 bg-emerald-500/10 text-emerald-400 rounded-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

// Variable réactive qui contiendra les données de la base de données
const stats = ref({
  totalEtudiants: 0,
  totalClasses: 0,
  totalVagues: 0,
  totalRecettes: '0'
})

// Fonction qui se lance automatiquement à l'ouverture de la page
onMounted(async () => {
  try {
    const token = localStorage.getItem('auth_token') 

    // 2. On l'envoie au fetch
    const response = await fetch('/api/v1/dashboard/stats', {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}` 
      }
    }) 
    if (response.ok) {
      const data = await response.json()
      // On extrait uniquement le bloc 'kpis' créé dans ton contrôleur PHP
      stats.value = data.kpis
    }
  } catch (error) {
    console.error("Erreur lors du chargement des statistiques KPI :", error)
  }
})
</script>