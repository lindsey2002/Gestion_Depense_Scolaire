<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Eleve;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    /* Enregistrer un nouveau paiement (Inscription ou mensualités groupées) */
    public function store(Request $request)
    {
        // 1. Validation des données reçues
        $request->validate([
          'eleve_id' => 'required|exists:eleves,id',
          'date_paiement' => 'nullable|date',
          'montant' => 'required|numeric|min:0',
          'mode_paiement' => 'required|in:especes,orange_money,virement',
          'type_paiement' => 'required|in:inscription,mensualite',
          'mois' => 'required_if:type_paiement,mensualite|nullable|array',
          'mois.*' => 'string',
        ]);

        $donnees = $request->all();
        $eleve = Eleve::with('classe')->findOrFail($request->eleve_id);

        if(empty($donnees['date_paiement'])){
            $donnees['date_paiement'] = date('Y-m-d H:i:s');
        }

       // 2. Génération du numéro de reçu unique (Placé ici, utilisable par les deux blocs)
        $moisActuel = (int)date('n');
        $anneeActuel = (int)date('Y');
        $anneeAcademique = ($moisActuel >= 10) ? ($anneeActuel) . '-' . ($anneeActuel + 1) : ($anneeActuel - 1) . '-' . $anneeActuel;

        // Sécurisation de TA structure sans rien changer à ton format :
        do {
            // Ta formule exacte d'origine
            $numeroRecuGenere = 'REC-' . $anneeAcademique . '-' . strtoupper(substr(uniqid(), -5));
            
            // Vérification instantanée en base de données
            $existeDeja = \App\Models\Paiement::where('numero_recu', $numeroRecuGenere)->exists();

        } while ($existeDeja); // Si le code extrait existe déjà, la boucle recommence instantanément

        // Variable qui contiendra l'enregistrement de référence à renvoyer au Front-end
        $paiementFinal = null;

        // 3. BLOC MENSUALITÉ
        if($request->type_paiement === 'mensualite')
        {
            // Impossible de payer une mensualité si pas encore inscrit
            if($eleve->statut !== 'inscrit'){
                return response()->json([
                    'error' => 'Action impossible',
                    'message' => "Cet élève doit d'abord payer ses frais d'inscription."
                ], 422);
            }
            
            // Vérification des doublons pour chaque mois sélectionné
            foreach ($request->mois as $unMois) {
                $moisDejaPaye = Paiement::where('eleve_id', $request->eleve_id)
                    ->where('type_paiement', 'mensualite')
                    ->where('mois', $unMois)
                    ->exists();
            
                if($moisDejaPaye){
                    return response()->json([
                        'error' => 'Doublon détecté',
                        'message' => "La mensualité du mois de {$unMois} a déjà été réglée pour cet élève."
                    ], 422);
                }
            }
            
            // Insertion en base de données : une ligne par mois partageant le même numéro de reçu généré
            foreach ($request->mois as $unMois) {
                $paiementFinal = Paiement::create([
                    'eleve_id' => $request->eleve_id,
                    'date_paiement' => $donnees['date_paiement'],
                    'montant' => $request->montant / count($request->mois), // Montant divisé par le nombre de mois
                    'mode_paiement' => $request->mode_paiement,
                    'type_paiement' => 'mensualite',
                    'mois' => $unMois,
                    'numero_recu' => $numeroRecuGenere // Partage du même reçu unique pour le groupe
                ]);
            }
        }

            // Petite astuce pour que le reçu Front-end connaisse tous les mois payés d'un coup
            $paiementFinal->liste_mois_payes = $request->mois;
            $paiementFinal->montant_total_recu = $request->montant;
        

        // 4. BLOC INSCRIPTION
        if($request->type_paiement === 'inscription')
        {
            $donnees['mois'] = null; // pas de mois pour une inscription
            $donnees['numero_recu'] = $numeroRecuGenere; // On applique le numéro généré
            
            // On crée l'unique ligne d'inscription
            $paiementFinal = Paiement::create($donnees);

            // Si c'est l'inscription on officialise l'élève et génère son matricule
            if($eleve->statut === 'en_attente'){
                // Extraction du niveau pour le matricule
                $chiffreNiveau = preg_replace('/[^0-9]/', '', $eleve->classe->niveau); 
                $lettresNiveau = ucfirst(strtolower(substr($eleve->classe->niveau, 0, 3)));
                $prefixeNiveau = $lettresNiveau . $chiffreNiveau;

                // Récupération des diminutifs configurés sur la classe
                $diminutifFiliere = strtolower($eleve->classe->diminutif);
                $cursus = strtolower($eleve->classe->cursus);

                // Génération du matricule selon tes règles exactes
                $anneeCourante = date('y');
                $chiffresUnique = rand(1000, 9999);
                $matriculeGenere = $prefixeNiveau . $diminutifFiliere . $cursus . '-' . $anneeCourante . '-' . $chiffresUnique;

                // Mise à jour de l'élève
                $eleve->update([
                    'matricule' => $matriculeGenere,
                    'statut' => 'inscrit'
                ]);
            }
            
            $paiementFinal->liste_mois_payes = [];
            $paiementFinal->montant_total_recu = $paiementFinal->montant;
        }

        // 5. RÉPONSE UNIQUE API
        return response()->json([
            'message' => 'Paiement enregistré avec succès !',
            'paiement' => $paiementFinal->load('eleve.classe')
        ], 201);
    }

    // Affichage de l'historique de tous les paiements pour le comptable/admin
    public function index()
    {
        $paiements = Paiement::with('eleve.classe')->latest()->get();
        return response()->json($paiements, 200);
    }
}