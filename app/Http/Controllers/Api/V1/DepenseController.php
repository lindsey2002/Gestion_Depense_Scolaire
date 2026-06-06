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

    // 1. MODIFIER UNE DÉPENSE EXISTANTE
    public function update(Request $request, $id)
    {
        $depense = Depense::find($id);

        if (!$depense) {
            return response()->json([
                'error' => 'Introuvable',
                'message' => 'Cette dépense n\'existe pas.'
            ], 404);
        }

        $request->validate([
            'categorie' => 'required|in:fournitures,salaires,entretien',
            'montant' => 'required|integer|min:1',
            'date' => 'required|date',
            'description' => 'required|string|max:700',
        ]);

        // Calcul du solde virtuel avant d'autoriser la modification
        // On récupère l'argent global, on remet virtuellement l'ancien montant en caisse, puis on teste le nouveau
        $totalRecettes = Paiement::sum('montant');
        $totalDepenses = Depense::sum('montant');
        $soldeCaisseSansCetteDepense = $totalRecettes - ($totalDepenses - $depense->montant);

        if ($request->montant > $soldeCaisseSansCetteDepense) {
            return response()->json([
                'error' => 'Fonds insuffisants',
                'message' => "Modification impossible. Le solde disponible serait insuffisant ({$soldeCaisseSansCetteDepense} FCFA).",
            ], 422);
        }

        $depense->update($request->all());

        return response()->json([
            'message' => 'Dépense mise à jour avec succès',
            'depense' => $depense
        ], 200);
    }

    // 2. SUPPRIMER UNE DÉPENSE
    public function destroy($id)
    {
        $depense = Depense::find($id);

        if (!$depense) {
            return response()->json([
                'error' => 'Introuvable',
                'message' => 'Cette dépense n\'existe pas.'
            ], 404);
        }

        $depense->delete();

        return response()->json([
            'message' => 'Dépense supprimée avec succès'
        ], 200);
    }

    // 3. EXPORT EXCEL BRUT (Génération de fichier CSV compatible Excel)
    public function exportExcel()
    {
        $depenses = Depense::latest()->get();
        
        // Nom du fichier avec la date du jour
        $filename = "depenses_pension_" . date('Y-m-d') . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($depenses) {
            $file = fopen('php://output', 'w');
            
            // Forcer l'encodage Excel pour les accents (BOM UTF-8)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes des colonnes Excel
            fputcsv($file, ['ID', 'Catégorie', 'Montant (FCFA)', 'Date', 'Description']);

            // 🎯 CORRECTION ICI : "as $depense" au singulier
            foreach ($depenses as $depense) {
                fputcsv($file, [
                    $depense->id,
                    ucfirst($depense->categorie),
                    $depense->montant,
                    $depense->date,
                    $depense->description
                ]);
            }

            fclose($file);
        }; // 🎯 CORRECTION ICI : Fermeture propre du bloc anonyme

        return response()->stream($callback, 200, $headers);
    }
}
