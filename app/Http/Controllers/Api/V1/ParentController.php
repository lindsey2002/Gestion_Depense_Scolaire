<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\Paiement;
use App\Models\Annonce;

class ParentController extends Controller
{
   public function getDashboardData(): JsonResponse
{
    try {
        $parentId = Auth::id();

        // Charge les relations nécessaires d'un coup
        $enfants = Eleve::where('parent_id', $parentId)
            ->with(['classe.vague', 'paiements'])
            ->get();

        $totalDepense = 0;
        $totalRestant = 0;

        $enfantsStats = [];
        foreach ($enfants as $enfant) {

            // --- CLASSE ---
            $classe = $enfant->classe; // objet complet avec vague

            // --- CALCUL FINANCIER ---
            $payeInscription = $enfant->paiements
                ->where('type_paiement', 'inscription')
                ->sum('montant');

            $payeMensualites = $enfant->paiements
                ->where('type_paiement', 'mensualite')
                ->sum('montant');

            $moisPayes = $enfant->paiements
                ->where('type_paiement', 'mensualite')
                ->pluck('mois')
                ->toArray();

            // Reste à payer = (nb_mois * tarif_mensuel) - déjà payé en mensualités
            $nbrMois   = $classe?->vague?->nombre_mois ?? 9;
            $tarifMois = $classe?->tarif_mensuel ?? 0;
            $resteAPayer = ($nbrMois * $tarifMois) - $payeMensualites;

            $totalDepense += $payeInscription + $payeMensualites;
            $totalRestant += $resteAPayer;

            $enfantsStats[] = [
                'id'        => $enfant->id,
                'matricule' => $enfant->matricule ?? 'En attente',
                'nom'       => $enfant->prenom . ' ' . $enfant->nom,
                'statut'    => $enfant->statut,
                // On renvoie l'objet classe entier (avec vague) pour le Vue
                'classe'    => $classe ? [
                    'libelle' => $classe->niveau . ' ' . $classe->diminutif,
                    'vague'   => $classe->vague ? [
                        'nom'        => $classe->vague->nom,
                        'nombre_mois'=> $classe->vague->nombre_mois,
                    ] : null,
                ] : null,
                'details_financiers' => [
                    'paye_inscription'  => $payeInscription,
                    'paye_mensualites'  => $payeMensualites,
                    'total_depense'     => $payeInscription + $payeMensualites,
                    'reste_a_payer'     => max(0, $resteAPayer),
                    'mois_payes'        => array_map('strtolower', $moisPayes),
                ]
            ];
        }

        $annonces = Annonce::latest()->take(5)->get();

        return response()->json([
            'kpis_globaux' => [
                'nombre_enfants'        => $enfants->count(),
                'total_general_depense' => $totalDepense,
                'total_general_restant' => $totalRestant,
            ],
            'enfants'  => $enfantsStats,
            'annonces' => $annonces,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => $e->getMessage(),
            'line'    => $e->getLine()
        ], 500);
    }
}
}