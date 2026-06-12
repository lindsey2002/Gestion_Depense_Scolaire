<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Eleve;
use Illuminate\Http\Request;
use App\Exports\JournalComptableExport;
use Maatwebsite\Excel\Facades\Excel;

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
        $moisActuel = (int)date('n');
        $anneeActuel = (int)date('Y');
        $anneeAcademique = ($moisActuel >= 10) ? ($anneeActuel) . '-' . ($anneeActuel + 1) : ($anneeActuel - 1) . '-' . $anneeActuel;

        do {
            $numeroRecuGenere = 'REC-' . $anneeAcademique . '-' . strtoupper(substr(uniqid(), -5));
            
            $existeDeja = \App\Models\Paiement::where('numero_recu', $numeroRecuGenere)->exists();

        } while ($existeDeja); 
        $paiementFinal = null;

        if($request->type_paiement === 'mensualite')
        {
            if($eleve->statut !== 'inscrit'){
                return response()->json([
                    'error' => 'Action impossible',
                    'message' => "Cet élève doit d'abord payer ses frais d'inscription."
                ], 422);
            }
            
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
            
            foreach ($request->mois as $unMois) {
                $paiementFinal = Paiement::create([
                    'eleve_id' => $request->eleve_id,
                    'date_paiement' => $donnees['date_paiement'],
                    'montant' => $request->montant / count($request->mois), 
                    'mode_paiement' => $request->mode_paiement,
                    'type_paiement' => 'mensualite',
                    'mois' => $unMois,
                    'numero_recu' => $numeroRecuGenere 
                ]);
            }
            if ($paiementFinal) {
                $paiementFinal->liste_mois_payes = $request->mois;
                $paiementFinal->montant_total_recu = $request->montant;
            }
        }
        
        if($request->type_paiement === 'inscription')
        {
            $donnees['mois'] = null; 
            $donnees['numero_recu'] = $numeroRecuGenere; 
            
            $paiementFinal = Paiement::create($donnees);

            if($eleve->statut === 'en_attente'){
                
                $chiffreNiveau = preg_replace('/[^0-9]/', '', $eleve->classe->niveau); 
                $lettresNiveau = ucfirst(strtolower(substr($eleve->classe->niveau, 0, 3)));
                $prefixeNiveau = $lettresNiveau . $chiffreNiveau;

                $diminutifFiliere = strtolower($eleve->classe->diminutif);
                $cursus = strtolower($eleve->classe->cursus);

                $anneeCourante = date('y');
                $chiffresUnique = rand(1000, 9999);
                $matriculeGenere = $prefixeNiveau . $diminutifFiliere . $cursus . '-' . $anneeCourante . '-' . $chiffresUnique;

                $eleve->update([
                    'matricule' => $matriculeGenere,
                    'statut' => 'inscrit'
                ]);
            }
            
            $paiementFinal->liste_mois_payes = [];
            $paiementFinal->montant_total_recu = $paiementFinal->montant;
        }

        return response()->json([
            'message' => 'Paiement enregistré avec succès !',
            'paiement' => $paiementFinal->load('eleve.classe')
        ], 201);
    }

    public function index()
    {
        $paiements = Paiement::with('eleve.classe')->latest()->get();
        return response()->json($paiements, 200);
    }

    public function exportExcel()
    {
        return Excel::download(new JournalComptableExport, 'ISI_Suivi_Comptable.xlsx');
    }
}