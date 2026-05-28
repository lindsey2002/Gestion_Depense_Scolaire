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
          'date_paiement' => 'required|date',
          'montant' => 'required|numeric|min:0',
          'mode_paiement' => 'required|in:especes,orange_money,virement',
          'type_paiement' => 'required|in:inscription,mensualite',
          'mois' => 'required_if:type_paiement,mensualite|nullable|string',
        ]);

        if($request->type_paiement === 'mensualite')
        {
            $inscriptionPayee = Paiement::where('eleve_id', $request->eleve_id)
                ->where('type_paiement', 'inscription')
                ->exists();

            if(! $inscriptionPayee){
                return response()->json([
                    'error' => 'Action impossible',
                    'message' => 'Cet eleve ne peut pas payer de mensualite car ses frais d\'inscription n\'ont pas encore ete regles.'
                ], 422);
            }
        }

        $donnees = $request->all();
        if($request->type_paiement === 'inscription')
        {
            $donnees['mois'] = null;
        }

        $paiement = Paiement::create($donnees);

        return response()->json([
            'message' => 'Paiement enregistré avec succès !',
            'paiement' => $paiement->load('eleve')
        ], 201);
    }
// Affichage de l'historique de tous les paiements pour le comptable/admin
    public function index()
    {
        $paiements = Paiement::with('eleve')->latest()->get();
        
        return response()->json($paiements, 200);
    }
}
