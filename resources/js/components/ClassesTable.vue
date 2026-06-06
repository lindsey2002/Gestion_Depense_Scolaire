<template>
  <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-md overflow-hidden">
    <div class="p-6 border-b border-gray-700 flex items-center justify-between">
      <h3 class="text-lg font-bold text-white">Vue d'ensemble des classes</h3>
      <span class="text-xs bg-gray-700 text-gray-300 px-2.5 py-1 rounded-full font-medium">
        {{ classes.length }} Classes au total
      </span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-gray-800/50 text-gray-400 text-xs uppercase tracking-wider font-semibold border-b border-gray-700">
            <th class="p-4 pl-6">Nom de la classe</th>
            <th class="p-4">Vague</th>
            <th class="p-4">Effectif actuel</th>
            <th class="p-4">Taux de remplissage</th>
            <th class="p-4 pr-6 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-700/50 text-sm text-gray-300">
          <tr v-for="classe in classes" :key="classe.id" class="hover:bg-gray-700/20 transition-colors">
            <td class="p-4 pl-6 font-semibold text-white">{{ classe.nom }}</td>
            <td class="p-4">
              <span class="px-2 py-1 text-xs bg-gray-700 text-gray-300 rounded border border-gray-600">
                {{ classe.vague_nom }}
              </span>
            </td>
            <td class="p-4 font-mono">{{ classe.eleves_count }} / {{ classe.capacite_max }}</td>
            <td class="p-4 w-1/4">
              <div class="flex items-center gap-3">
                <div class="w-full bg-gray-700 rounded-full h-2 overflow-hidden">
                  <div 
                    class="bg-emerald-500 h-2 rounded-full transition-all duration-500"
                    :style="{ width: Math.min((classe.eleves_count / classe.capacite_max) * 100, 100) + '%' }"
                  ></div>
                </div>
                <span class="text-xs font-bold text-gray-400 font-mono">
                  {{ Math.round((classe.eleves_count / classe.capacite_max) * 100) }}%
                </span>
              </div>
            </td>
            <td class="p-4 pr-6 text-right">
              <router-link to="/admin/classes/recherche" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 hover:underline transition-colors">
                Ouvrir la classe →
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const classes = ref([])

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
      // On extrait le tableau 'classes' formaté par ton contrôleur PHP
      classes.value = data.classes
    }
  } catch (error) {
    console.error("Erreur lors du chargement du tableau des classes :", error)
  }
})
</script>