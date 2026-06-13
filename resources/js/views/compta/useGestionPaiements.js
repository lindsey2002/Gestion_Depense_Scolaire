import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

// 1. EXPORTATION DE LA FONCTION POUR LE COMPOSANT VISUEL
export function useGestionPaiements() {

    // ==========================================
    // ÉTATS RÉACTIFS (VARIABLES DE CONTRÔLE)
    // ==========================================
    const eleves = ref([]);                 // Liste brute de tous les élèves récupérés du Back-end
    const searchQuery = ref('');            // Texte tapé par le comptable dans la barre de recherche
    const selectedEleveId = ref('');       // ID de l'élève actuellement sélectionné
    const currentEleve = ref(null);         // Objet complet de l'élève sélectionné (avec classe, vague, paiements)

    const loadingBtn = ref(false);          // État de chargement du bouton de soumission
    const error = ref(null);                // Message d'erreur de l'API
    const success = ref(null);              // Message de succès de l'API

    // Tableau des mois sélectionnés dans la grille par le comptable
    const moisSelectionnes = ref([]);

    // Formulaire structuré prêt à être envoyé à ton PaiementController::store
    const form = ref({
        eleve_id: '',
        montant: 0,
        mode_paiement: 'especes',
        type_paiement: 'inscription',
        mois: [] // Tableau envoyé au contrôleur modifié
    });

    // Référence fixe de l'ordre des mois pour les calculs de calendrier académique
    const ordreMoisAnnee = [
        'septembre', 'octobre', 'novembre', 'décembre', 
        'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août'
    ];

    // ==========================================
    // 2. BARRE DE RECHERCHE FILTRANTE (COMPUTED)
    // ==========================================
    const elevesFiltres = computed(() => {
        // Si la barre de recherche est vide, on ne propose aucun choix (évite une surcharge à l'écran)
        if (!searchQuery.value.trim()) {
            return [];
        }
        
        // Nettoyage du texte recherché (minuscules et suppression des espaces superflus)
        const recherche = searchQuery.value.toLowerCase().trim();
        
        return eleves.value.filter(eleve => {
            const prenom = eleve.prenom ? eleve.prenom.toLowerCase() : '';
            const nom = eleve.nom ? eleve.nom.toLowerCase() : '';
            const nomComplet = `${prenom} ${nom}`;
            const matricule = eleve.matricule ? eleve.matricule.toLowerCase() : '';
            
            // On vérifie si la saisie correspond au prénom, au nom, au nom complet ou au matricule
            return prenom.includes(recherche) || 
                   nom.includes(recherche) || 
                   nomComplet.includes(recherche) || 
                   matricule.includes(recherche);
        });
    });

    // ==========================================
    // 3. CALCULETTE DE CALENDRIER SELON LA VAGUE
    // ==========================================
    const moisDeLaVague = computed(() => {
        // Si aucun élève n'est sélectionné ou s'il n'a pas de classe/vague, on renvoie un tableau vide
        if (!currentEleve.value || !currentEleve.value.classe?.vague) return [];
        
        const vague = currentEleve.value.classe.vague;
        const moisDebutNom = vague.nom.toLowerCase().trim(); // Ex: "vague octobre" -> on cherche "octobre"
        
        // On isole le nom du mois dans le nom de la vague (ex: "vague octobre" contient "octobre")
        const nomMoisTrouve = ordreMoisAnnee.find(m => moisDebutNom.includes(m)) || 'octobre';
        
        // On trouve la position de départ dans notre calendrier de référence
        let indexDebut = ordreMoisAnnee.indexOf(nomMoisTrouve);
        
        const listeMoisGeneres = [];
        const totalMois = vague.nombre_mois || 9; // Par défaut 9 mois si non renseigné
        
        // On boucle autant de fois qu'il y a de mois de scolarité dans la vague
        for (let i = 0; i < totalMois; i++) {
            // Le modulo 12 permet de revenir à Janvier (index 4) après Décembre (index 3)
            const indexActuel = (indexDebut + i) % 12;
            listeMoisGeneres.push(ordreMoisAnnee[indexActuel]);
        }
        
        return listeMoisGeneres;
    });

    // ==========================================
    // 4. DÉTERMINATION DE L'ÉTAT DU MOIS (TRICOLORE)
    // ==========================================
    const getStatutMois = (nomMois) => {
        if (!currentEleve.value) return 'bloque';

        // ÉTAPE A : Vérifier si le mois est déjà enregistré dans l'historique de l'élève
        const dejaPayeEnBase = currentEleve.value.paiements?.some(
            p => p.type_paiement === 'mensualite' && p.mois === nomMois
        );
        
        if (dejaPayeEnBase) {
            return 'paye'; 
        }

        // ÉTAPE B : Règle du "Pas de saut" (Analyse chronologique)
        const indexMoisDansVague = moisDeLaVague.value.indexOf(nomMois);
        
        // On vérifie si tous les mois précédents de la vague sont validés (soit en BDD, soit cochés dans le formulaire)
        for (let i = 0; i < indexMoisDansVague; i++) {
            const moisPrecedent = moisDeLaVague.value[i];
            
            const payeEnBase = currentEleve.value.paiements?.some(
                p => p.type_paiement === 'mensualite' && p.mois === moisPrecedent
            );
            const cocheDansFormulaire = moisSelectionnes.value.includes(moisPrecedent);
            
            // S'il y a un seul mois précédent qui n'est ni payé ni coché, le mois actuel se bloque
            if (!payeEnBase && !cocheDansFormulaire) {
                return 'bloque'; // ➔ Couleur BLEU DÉSACTIVÉ / OPAQUE
            }
        }


        return 'payable'; 
    };

    // ==========================================
    // 5. GESTION DU CLIC SUR UN BADGE DE MOIS
    // ==========================================
    const toggleMois = (nomMois) => {
        const statut = getStatutMois(nomMois);
        
        if (statut === 'paye' || statut === 'bloque') return;

        const index = moisSelectionnes.value.indexOf(nomMois);

        if (index > -1) {
            const indexDansVague = moisDeLaVague.value.indexOf(nomMois);
            moisSelectionnes.value = moisSelectionnes.value.filter(m => {
                return moisDeLaVague.value.indexOf(m) < indexDansVague;
            });
        } else {
            // Sinon, on ajoute le mois coché à la liste
            moisSelectionnes.value.push(nomMois);
        }

        form.value.mois = moisSelectionnes.value;
        form.value.montant = moisSelectionnes.value.length * (currentEleve.value.classe?.tarif_mensuel || 0);
    };

    // ==========================================
    // 6. SÉLECTION D'UN ÉLÈVE DEPUIS LA RECHERCHE
    // ==========================================
    const selectEleve = (eleve) => {
        currentEleve.value = eleve;
        selectedEleveId.value = eleve.id;
        form.value.eleve_id = eleve.id;
        
        searchQuery.value = `${eleve.prenom} ${eleve.nom}`; // Remplit l'input avec le choix cliqué
        moisSelectionnes.value = [];
        form.value.mois = [];

        if (eleve.statut === 'en_attente') {
            form.value.type_paiement = 'inscription';
            form.value.montant = eleve.classe?.frais_inscription || 0;
        } else {
            form.value.type_paiement = 'mensualite';
            form.value.montant = 0; // Sera calculé dynamiquement au clic sur les badges de mois
        }
    };

    const adaptAmount = () => {
        if (!currentEleve.value) return;
        
        if (form.value.type_paiement === 'inscription') {
            form.value.montant = currentEleve.value.classe?.frais_inscription || 0;
            moisSelectionnes.value = [];
            form.value.mois = [];
        } else {
            form.value.montant = moisSelectionnes.value.length * (currentEleve.value.classe?.tarif_mensuel || 0);
        }
    };

    // ==========================================
    // 7. COMMUNICATIONS API (GET & POST)
    // ==========================================
    const fetchEleves = async () => {
        try {
            const token = localStorage.getItem('auth_token');
            // Remplacement par la bonne route API qui charge l'historique complet pour la grille
            const response = await axios.get('/api/v1/eleves?include=paiements,classe.vague', {
                headers: { Authorization: `Bearer ${token}` }
            });
            eleves.value = response.data;
        } catch (err) {
            console.error(err);
            error.value = "Impossible de récupérer les fiches élèves.";
        }
    };

    const handleProcessPayment = async () => {
        try {
            loadingBtn.value = true;
            error.value = null;
            success.value = null;

            // Sécurité Front : Interdire la validation si aucun mois n'est coché pour une mensualité
            if (form.value.type_paiement === 'mensualite' && form.value.mois.length === 0) {
                error.value = "Veuillez cocher au moins un mois à encaisser.";
                loadingBtn.value = false;
                return;
            }

            const token = localStorage.getItem('auth_token');
            const response = await axios.post('/api/v1/paiements', form.value, {
                headers: { Authorization: `Bearer ${token}` }
            });

            success.value = "Encaissement validé avec succès !";
            console.log("Données pour le reçu A4 :", response.data.paiement);

            // Nettoyage complet de l'interface après écriture réussie
            searchQuery.value = '';
            selectedEleveId.value = '';
            currentEleve.value = null;
            moisSelectionnes.value = [];
            form.value = { eleve_id: '', montant: 0, mode_paiement: 'especes', type_paiement: 'inscription', mois: [] };
            
            // Rafraîchissement global des données élèves pour mettre à jour les grises de la caisse
            await fetchEleves();
        } catch (err) {
            console.error(err);
            error.value = err.response?.data?.message || "Erreur lors du traitement de l'écriture comptable.";
        } finally {
            loadingBtn.value = false;
        }
    };

    const formatCurrency = (val) => {
        if (!val) return '0 FCFA';
        return new Intl.NumberFormat('fr-FR').format(val) + ' FCFA';
    };

    onMounted(() => {
        fetchEleves();
    });

    // ==========================================
    // 8. EXPÉDITION DU PACK LOGIQUE POUR LE VISUEL
    // ==========================================
    return {
        searchQuery,
        elevesFiltres,
        currentEleve,
        form,
        moisDeLaVague,
        moisSelectionnes,
        loadingBtn,
        error,
        success,
        selectEleve,
        toggleMois,
        adaptAmount,
        handleProcessPayment,
        getStatutMois,
        formatCurrency
    };
}