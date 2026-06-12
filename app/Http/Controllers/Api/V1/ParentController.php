<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\Annonce;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{
    public function getDashboardData(): JsonResponse
    {
        try {
            $parentId = Auth::id();

            // 1. On récupère les enfants SANS AUCUNE RELATION pour éviter le crash
            $enfants = Eleve::where('parent_id', $parentId)->get();

            $enfantsStats = [];
            foreach ($enfants as $enfant) {
                $enfantsStats[] = [
                    'id' => $enfant->id,
                    'matricule' => $enfant->matricule ?? 'En attente',
                    'nom' => $enfant->prenom . ' ' . $enfant->nom,
                    'classe' => 'Classe Test', // Écrit en dur temporairement
                    'statut' => $enfant->statut,
                    'details_financiers' => [
                        'paye_inscription' => 0,
                        'paye_mensualites' => 0,
                        'total_depense' => 0,
                        'reste_a_payer' => 0,
                        'mois_payes' => []
                    ]
                ];
            }

            // 2. On récupère les annonces de manière sécurisée
            $annonces = Annonce::latest()->take(5)->get();

            return response()->json([
                'kpis_globaux' => [
                    'nombre_enfants' => $enfants->count(),
                    'total_general_depense' => 0,
                    'total_general_restant' => 0,
                ],
                'enfants' => $enfantsStats,
                'annonces' => $annonces
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}