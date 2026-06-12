<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-md space-y-6">
      <h2 class="text-2xl font-bold text-center text-gray-800">Connexion</h2>
      
      <div v-if="error" class="bg-red-50 text-red-600 p-3 rounded-lg text-sm font-semibold">
        {{ error }}
      </div>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Adresse Email</label>
          <input v-model="form.email" type="email" autocomplete="email" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
          <input v-model="form.password" type="password" autocomplete="current-password" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500">
        </div>

        <button type="submit" :disabled="loading" class="w-full bg-orange-600 text-white p-2.5 rounded-lg font-bold hover:bg-orange-700 transition duration-200">
          {{ loading ? 'Connexion...' : 'Se connecter' }}
        </button>
      </form>

      <p class="text-sm text-center text-gray-600">
        Pas encore de compte ? <router-link to="/register" class="text-orange-600 font-bold hover:underline">S'inscrire</router-link>
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
    email: '',
    password: ''
});

const handleLogin = async () => {
    try {
        loading.value = true;
        error.value = null;
        const response = await axios.post('/api/v1/login', form.value);

        localStorage.setItem('auth_token', response.data.token);
        localStorage.setItem('user_name', response.data.user.name);
        localStorage.setItem('user_email', response.data.user.email);
        
        const token = response.data.access_token || response.data.token;
        const role = response.data.user?.role;
        const name = response.data.user?.name || 'Gestionnaire ISI';
        const email = response.data.user?.email || '';
            
        localStorage.setItem('auth_token', token);
        if (role) {
            localStorage.setItem('user_role', role); 
        }
        
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        
        if (role === 'admin') {
            router.push('/admin/dashboard');
        } else if (role === 'comptable') {
            router.push('/compta/dashboard');
        } else if (role === 'gestionnaire') {
            router.push('/gestionnaire/dashboard');
        }else if (role === 'parent') {
            router.push('/parent/dashboard');
        }else {
            router.push('/login'); 
        }

    } catch (err) {
        console.error(err);
        error.value = err.response?.data?.message || "Identifiants incorrects.";
    } finally {
        loading.value = false;
    }
};
</script>