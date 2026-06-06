import { ref, computed } from 'vue';
import axios from 'axios';

export function useGestionDepense() {
  // ═══ ÉTATS ET VARIABLES RÉACTIVES ═══
  const depenses = ref([]);
  const totalRecettes = ref(0);
  const loading = ref(false);
  const loadingBtn = ref(false);
  const error = ref(null);
  const successMessage = ref(null);

  const montrePopupSuppression = ref(false);
  const idA_Supprimer = ref(null);

  // Mode édition
  const estEnModeEdition = ref(false);
  const idDepenseEnCours = ref(null);

  // Formulaire (Structure par défaut)
  const form = ref({
    categorie: 'fournitures',
    montant: '',
    date: new Date().toISOString().split('T')[0], // Date du jour
    description: ''
  });

  // ═══ PROPRIÉTÉS CALCULÉES ═══
  const totalDepenses = computed(() => {
    return depenses.value.reduce((sum, d) => sum + Math.round(parseFloat(d.montant || 0)), 0);
  });

  const soldeCaisse = computed(() => {
    return Math.round(totalRecettes.value) - totalDepenses.value;
  });

  // ═══ FONCTIONS API (BACKEND) ═══

  // 🎯 REVOILÀ LA FONCTION DISPARUE : Déclarée proprement avec "async function"
  async function chargerDonnees() {
    loading.value = true;
    error.value = null;
    try {
      const token = localStorage.getItem('auth_token');
      
      // 1. Récupération des dépenses
      const resDepenses = await axios.get('/api/v1/depenses', {
        headers: { Authorization: `Bearer ${token}` }
      });
      depenses.value = resDepenses.data;

      // 2. Simulation ou récupération du fond de caisse (Modifie 500000 quand ton API Recettes sera prête)
      totalRecettes.value = 500000; 

    } catch (err) {
      console.error(err);
      error.value = "Impossible de charger les données des dépenses.";
    } finally {
      loading.value = false;
    }
  }

  // Enregistrer ou Modifier
  async function soumettreFormulaire() {
    loadingBtn.value = true;
    error.value = null;
    successMessage.value = null;

    form.value.montant = Math.round(parseFloat(form.value.montant));

    if (form.value.montant > soldeCaisse.value && !estEnModeEdition.value) {
      error.value = `Fonds insuffisants. Le solde actuel est de ${formatCurrency(soldeCaisse.value)}.`;
      loadingBtn.value = false;
      return;
    }

    try {
      const token = localStorage.getItem('auth_token');

      if (estEnModeEdition.value) {
        await axios.put(`/api/v1/depenses/${idDepenseEnCours.value}`, form.value, {
          headers: { Authorization: `Bearer ${token}` }
        });
        successMessage.value = "Dépense mise à jour avec succès !";
      } else {
        await axios.post('/api/v1/depenses', form.value, {
          headers: { Authorization: `Bearer ${token}` }
        });
        successMessage.value = "Nouvelle dépense enregistrée !";
      }

      setTimeout(() => {
        successMessage.value = null;
      }, 8000);

      annulerEdition();
      await chargerDonnees();

    } catch (err) {
      console.error(err);
      error.value = err.response?.data?.message || "Une erreur est survenue.";
    } finally {
      loadingBtn.value = false;
    }
  }

  setTimeout(() => {
    error.value = null;
  }, 5000);

  // Supprimer
  // 1. Déclencher l'ouverture du popup
  function confirmerSuppression(id) {
    idA_Supprimer.value = id;
    montrePopupSuppression.value = true;
  }

  // 2. Annuler et fermer le popup
  function annulerSuppression() {
    idA_Supprimer.value = null;
    montrePopupSuppression.value = false;
  }

  // 3. Exécuter la suppression réelle après validation dans le popup
  async function supprimerDepense() {
    if (!idA_Supprimer.value) return;

    error.value = null;
    successMessage.value = null;
    loadingBtn.value = true; // On réutilise le loader pour le bouton du popup

    try {
      const token = localStorage.getItem('auth_token');
      await axios.delete(`/api/v1/depenses/${idA_Supprimer.value}`, {
        headers: { Authorization: `Bearer ${token}` }
      });

      successMessage.value = "Dépense supprimée avec succès.";
      
      setTimeout(() => {
        successMessage.value = null;
      }, 4000);

      // Fermeture du popup et rechargement
      annulerSuppression();
      await chargerDonnees();

    } catch (err) {
      console.error(err);
      error.value = "Impossible de supprimer cette dépense.";
      
      setTimeout(() => {
        error.value = null;
      }, 5000);
    } finally {
      loadingBtn.value = false;
    }
  }

  // ═══ INTERACTION INTERFACE (UX) ═══

  const activerEdition = (depense) => {
    estEnModeEdition.value = true;
    idDepenseEnCours.value = depense.id;
    
    form.value = {
      categorie: depense.categorie,
      montant: depense.montant,
      date: depense.date,
      description: depense.description
    };
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const annulerEdition = () => {
    estEnModeEdition.value = false;
    idDepenseEnCours.value = null;
    form.value = {
      categorie: 'fournitures',
      montant: '',
      date: new Date().toISOString().split('T')[0],
      description: ''
    };
  };

  const formatCurrency = (valeur) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF', minimumFractionDigits: 0 }).format(valeur);
  };

  // ═══ CE QUE LE COMPOSABLE REND ACCESSIBLE ═══
  return {
    depenses,
    totalRecettes,
    loading,
    loadingBtn,
    error,
    successMessage,
    estEnModeEdition,
    form,
    totalDepenses,
    soldeCaisse,
    chargerDonnees,
    soumettreFormulaire,
    montrePopupSuppression,
    confirmerSuppression,
    annulerSuppression,
    supprimerDepense,
    activerEdition,
    annulerEdition,
    formatCurrency
  };
}