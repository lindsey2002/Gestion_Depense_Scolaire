<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-md space-y-6">
      <h2 class="text-2xl font-bold text-center text-gray-800">Créer un compte</h2>
      
      <div v-if="error" class="bg-red-50 text-red-600 p-3 rounded-lg text-sm font-semibold">
        {{ error }}
      </div>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Nom complet</label>
          <input v-model="form.name" type="text" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Adresse Email</label>
          <input v-model="form.email" type="email" autocomplete="email" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
          <input v-model="form.password" type="password" autocomplete="new-password" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
          <input v-model="form.password_confirmation" type="password" autocomplete="new-password" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500">
        </div>

        <button type="submit" :disabled="loading" class="w-full bg-orange-600 text-white p-2.5 rounded-lg font-bold hover:bg-orange-700 transition duration-200">
          {{ loading ? 'Inscription...' : "S'inscrire" }}
        </button>
      </form>

      <p class="text-sm text-center text-gray-600">
        Déjà un compte ? <router-link to="/login" class="text-orange-600 font-bold hover:underline">Se connecter</router-link>
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

const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
});

const handleRegister = async () => {
    try {
        loading.value = true;
        error.value = null;
        
        // 1. Initialise le cookie CSRF (protection Laravel obligatoire)
        await axios.get('/sanctum/csrf-cookie');
        
        // 2. Envoie les données d'inscription au endpoint par défaut de Laravel
        await axios.post('/register', form.value);
        
        // 3. Redirige vers le dashboard après succès
        router.push('/compta/dashboard');
    } catch (err) {
        console.error(err);
        error.value = err.response?.data?.message || "Une erreur est survenue lors de l'inscription.";
    } finally {
        loading.value = false;
    }
};
</script>