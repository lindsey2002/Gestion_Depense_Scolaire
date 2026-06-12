<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-900 px-4 font-sans">
    <div class="max-w-md w-full bg-gray-800 p-8 rounded-xl shadow-2xl border border-gray-700 space-y-6">
      
      <div>
        <h2 class="text-2xl font-bold text-center text-white tracking-tight">Inscrire un nouvel agent</h2>
        <p class="text-sm text-center text-gray-400 mt-1">L'identifiant professionnel et le mot de passe seront générés automatiquement.</p>
      </div>
      
      <div v-if="error" class="bg-red-500/10 border border-red-500/20 text-red-400 p-3 rounded-lg text-sm font-medium">
        {{ error }}
      </div>

      <div v-if="successMessage" class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-3 rounded-lg text-sm font-medium">
        {{ successMessage }}
      </div>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-300">Prénom</label>
          <input v-model="form.prenom" type="text" required class="mt-1 block w-full p-2.5 bg-gray-700 border border-gray-600 rounded-lg text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-300">Nom</label>
          <input v-model="form.nom" type="text" required class="mt-1 block w-full p-2.5 bg-gray-700 border border-gray-600 rounded-lg text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-300">Adresse Email Personnelle (SMTP)</label>
          <input v-model="form.email_perso" type="email" required class="mt-1 block w-full p-2.5 bg-gray-700 border border-gray-600 rounded-lg text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500" placeholder="Ex: agent@gmail.com">
          <p class="text-xs text-gray-500 mt-1">C'est sur cette adresse que l'agent recevra ses accès.</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-300">Rôle de l'agent</label>
          <select v-model="form.role" required class="mt-1 block w-full p-2.5 bg-gray-700 border border-gray-600 rounded-lg text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
            <option value="" disabled selected>Choisir un rôle</option>
            <option value="comptable">Comptable</option>
            <option value="gestionnaire">Gestionnaire</option>
          </select>
        </div>

        <button type="submit" :disabled="loading" class="w-full bg-emerald-600 text-white p-2.5 rounded-lg font-bold hover:bg-emerald-500 transition duration-200 disabled:opacity-50">
          {{ loading ? 'Génération et envoi du mail...' : "Créer le compte de l'agent" }}
        </button>
      </form>

      <p class="text-sm text-center">
        <router-link to="/admin/dashboard" class="text-gray-400 font-medium hover:text-white underline">Retour au tableau de bord</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const loading = ref(false);
const error = ref(null);
const successMessage = ref(null);

const form = ref({
    prenom: '',
    nom: '',
    email_perso: '',
    role: ''
});

const handleRegister = async () => {
    try {
        loading.value = true;
        error.value = null;
        successMessage.value = null;
        
        // Récupération du token d'authentification de l'Admin connecté
        const token = localStorage.getItem('auth_token');
        
        if (!token) {
            error.value = "Votre session a expiré. Veuillez vous reconnecter.";
            return;
        }

        // Envoi des données à ta route sécurisée par le middleware admin
        const response = await axios.post('/api/v1/register', form.value, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        if (response.status === 201) {
            successMessage.value = "Compte créé ! L'agent va recevoir ses accès par email.";
            
            // Redirection vers le dashboard après une courte attente (2 secondes) pour laisser lire le message
            setTimeout(() => {
                router.push('/admin/dashboard');
            }, 2000);
        }

    } catch (err) {
        console.error(err);
        error.value = err.response?.data?.message || "Erreur lors de la création de l'agent. Vérifie la connexion avec le serveur.";
    } finally {
        loading.value = false;
    }
};
</script>