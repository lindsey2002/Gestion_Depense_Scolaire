<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Eleve;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    /* Enregistrer un nouveau paiement (Inscription ou mensualite) */

    public function store(Request $request)
    {

    // 1. Validation des données recues
        $request->validate([
          'eleve_id' => 'required|exists:eleves,id',
          'date_paiement' => 'nullable|date',
          'montant' => 'required|numeric|min:0',
          'mode_paiement' => 'required|in:especes,orange_money,virement',
          'type_paiement' => 'required|in:inscription,mensualite',
          'mois' => 'required_if:type_paiement,mensualite|nullable|string',
        ]);

        $donnees = $request->all();
        $eleve = Eleve::with('classe')->findOrFail($request->eleve_id);

        if(empty($donnees['date_paiement'])){
            $donnees['date_paiement'] = date('Y-m-d H:i:s');
        }

        if($request->type_paiement === 'mensualite')
        {
            // impossibble de payer une mensualite si pas encore inscrit
            if($eleve->statut !== 'inscrit'){
                return response()->json([
                    'error' => 'Action impossible',
                    'message' => "Cet eleve doit d\'abord payer ses frais d\'inscription."
                ], 422);
            }
            
            $moisDejaPaye = Paiement::where('eleve_id', $request->eleve_id)
                ->where('type_paiement', 'mensualite')
                ->where('mois', $request->mois)
                ->exists();
            
            if($moisDejaPaye){
                return response()->json([
                    'error' => 'Doublon détecté',
                    'message' => "La mensualité du mois de {$request->mois} a déjà été réglée pour cet élève."
                ], 422);
            }
        }

        if($request->type_paiement === 'inscription')
        {
            $donnees['mois'] = null; // pas de mois pour une inscription
        }
        $moisActuel = (int)date('n');
        $anneeActuel = (int)date('Y');
        $anneeAcademique = ($moisActuel >= 10) ? ($anneeActuel) . '-' . ($anneeActuel + 1) : ($anneeActuel - 1) . '-' . $anneeActuel;

        $donnees['numero_recu'] = 'REC-' . $anneeAcademique . '-' . strtoupper(substr(uniqid(), -5));
        $paiement = Paiement::create($donnees);

        // si c'est l'inscription on officialise l'eleve
        if($paiement->type_paiement === 'inscription' && $eleve->statut === 'en_attente'){
            // extraction du niveau pour le matricule
            $chiffreNiveau = preg_replace('/[^0-9]/', '', $eleve->classe->niveau); 
            $lettresNiveau = ucfirst(strtolower(substr($eleve->classe->niveau, 0, 3)));
            $prefixeNiveau = $lettresNiveau . $chiffreNiveau;

            // Récupération des diminutifs configurés sur la classe
            $diminutifFiliere = strtolower($eleve->classe->diminutif);
            $cursus = strtolower($eleve->classe->cursus);

            // generation du matricule selon tes regles exactes
            $anneeCourante = date('y');
            $chiffresUnique = rand(1000, 9999);
            $matriculeGenere = $prefixeNiveau . $diminutifFiliere . $cursus . '-' . $anneeCourante . '-' . $chiffresUnique;

            // mise a jour de l'eleve
            $eleve->update([
                'matricule' => $matriculeGenere,
                'statut' => 'inscrit'
            ]);
        }

        return response()->json([
            'message' => 'Paiement enregistré avec succès !',
            'paiement' => $paiement->load('eleve.classe')
        ], 201);
    }
// Affichage de l'historique de tous les paiements pour le comptable/admin
    public function index()
    {
        $paiements = Paiement::with('eleve')->latest()->get();
        
        return response()->json($paiements, 200);
    }
}
