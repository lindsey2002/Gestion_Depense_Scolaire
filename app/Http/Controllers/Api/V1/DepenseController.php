<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Depense;
use Illuminate\Http\Request;

class DepenseController extends Controller
{
    // afficher l'historique de toutes les depenses
    public function index()
    {
        $depenses = Depense::latest()->get();
        return response()->json($depenses, 200);
    }

    // on enregistre une nouvelle depense
    public function store(Request $request)
    {
        $request->validate([
            'categorie' => 'required|in:fournitures,salaires,entretien',
            'montant' => 'required|integer|min:1',
            'date' => 'nullable|date',
            'description' => 'required|string|max:700',
        ]);

        $donnees = $request->all();

        if(! isset($donnees['date']) || empty($donnees['date'])){
            $donnees['date'] = date('Y-m-d');
        }

        $totalRecettes = Paiement::sum('montant');
        $totalDepenses = Depense::sum('montant');
        $soldeActuelCaisse = $totalRecettes - $totalDepenses;

        // si la depense depasse ce qu'on a en caisse, on bloque
        if($request->montant > $soldeActuelCaisse){
            return response()->json([
                'error' => 'Fonds insuffisants',
                'message' => "Impossible d'enregistrer cette dépense. Le solde actuel de la caisse est de {$soldeActuelCaisse} FCFA, ce qui est insuffisant pour couvrir cette depense",
            ], 422);
        }

        $depense = Depense::create($donnees);

        return response()->json([
            'message' => 'Nouvelle dépense enregistrée',
            'depense' => $depense,
            'solde_restant' => $soldeActuelCaisse - $request->montant
        ], 201);
    }
}
