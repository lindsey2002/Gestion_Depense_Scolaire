<template>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-md lg:col-span-2">
      <h3 class="text-lg font-bold text-white mb-4">Évolution mensuelle des inscriptions</h3>
      
      <div class="h-64 flex items-end gap-2 pt-4 border-b border-gray-700 border-l px-2">
        <div 
          v-for="(bar, index) in inscriptionsMensuelles" 
          :key="index"
          class="flex-1 flex flex-col items-center gap-2 group relative"
        >
          <div class="absolute -top-10 scale-0 group-hover:scale-100 bg-gray-900 text-xs text-emerald-400 py-1 px-2 rounded border border-gray-700 transition-all duration-150 z-10 font-mono">
            {{ bar.total }}
          </div>
          
          <div 
            class="w-full bg-gradient-to-t from-emerald-600 to-emerald-400 rounded-t-md transition-all duration-500 ease-out"
            :style="{ height: calculerHauteurBarre(bar.total) + '%' }"
          ></div>
          
          <span class="text-xs text-gray-400 absolute -mb-6 transform font-medium">
            {{ bar.mois }}
          </span>
        </div>
      </div>
    </div>

    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-md flex flex-col justify-between">
      <h3 class="text-lg font-bold text-white mb-2">Statut des paiements</h3>
      
      <div class="flex justify-center items-center my-4 relative">
        <svg class="w-40 h-40 transform -rotate-90" viewBox="0 0 36 36">
          <path class="text-gray-700" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
          
          <path 
            class="text-emerald-500 transition-all duration-500" 
            :stroke-dasharray="`${statutPaiements.tauxAJour}, 100`"
            stroke="currentColor" stroke-width="3.2" stroke-linecap="round" fill="none" 
            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
          />
        </svg>
        
        <div class="absolute text-center">
          <span class="text-2xl font-black text-white font-mono">{{ statutPaiements.tauxAJour }}%</span>
          <p class="text-[10px] text-gray-400 uppercase tracking-wider font-medium mt-0.5">À jour</p>
        </div>
      </div>

      <div class="space-y-2 border-t border-gray-700/50 pt-3">
        <div class="flex items-center justify-between text-xs">
          <div class="flex items-center gap-2 text-gray-300">
            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
            <span>Élèves à jour</span>
          </div>
          <span class="font-bold text-white font-mono">{{ statutPaiements.totalAJour }}</span>
        </div>
        <div class="flex items-center justify-between text-xs">
          <div class="flex items-center gap-2 text-gray-300">
            <span class="w-2.5 h-2.5 bg-gray-600 rounded-full"></span>
            <span>En retard de paiement</span>
          </div>
          <span class="font-bold text-white font-mono">{{ statutPaiements.totalEnRetard }}</span>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

// Données fictives initiales (remplacées par la BD au chargement)
const inscriptionsMensuelles = ref([
  { mois: 'Oct', total: 0 }, { mois: 'Nov', total: 0 }, { mois: 'Déc', total: 0 },
  { mois: 'Jan', total: 0 }, { mois: 'Fév', total: 0 }, { mois: 'Mar', total: 0 }
])

const statutPaiements = ref({
  tauxAJour: 0,
  totalAJour: 0,
  totalEnRetard: 0
})

// Trouver la valeur maximale pour calibrer la hauteur des barres en %
const calculerHauteurBarre = (total) => {
  const max = Math.max(...inscriptionsMensuelles.value.map(b => b.total), 1)
  return (total / max) * 100
}

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
      // On récupère les inscriptions et les paiements stockés dans le bloc 'charts'
      inscriptionsMensuelles.value = data.charts.inscriptions
      statutPaiements.value = data.charts.paiements
    }
  } catch (error) {
    console.error("Erreur lors du chargement des graphiques :", error)
  }
})
</script>