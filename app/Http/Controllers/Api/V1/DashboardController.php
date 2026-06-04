<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Eleve;
use App\Models\Vague;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse{
        $totalRecettesGlobales = Paiement::sum('montant');
        $totalInscriptions = Paiement::where('type_paiement', 'inscription')->sum('montant');
        $totalPensions = Paiement::where('type_paiement', 'mensualite')->sum('montant');

        $totalScolariteAttendu = 0;

        //$eleves = Eleve::whereRaw('LOWER(statut) = ?', ['actif'])->with('classe.vague')->get();
        $eleves = Eleve::with('classe.vague')->get();
        //$eleves = Eleve::where('statut', 'actif')->with('classe.vague')->get();

        foreach($eleves as $eleve){
            if($eleve->classe && $eleve->classe->vague){
                $nombreMoisDus = $eleve->classe->vague->nombre_mois;
                $montantMensuel = $eleve->classe->tarif_mensuel;

                $fraisInscription = $eleve->classe->frais_inscription;

                $totalScolariteAttendu += (($nombreMoisDus * $montantMensuel) + $fraisInscription);
            }
        }

        $resteAPercevoirGlobal = $totalScolariteAttendu - $totalRecettesGlobales;

        $vaguesStats = [];
        $vagues = Vague::with('classes.eleves.paiements')->get();

        foreach ($vagues as $vague) {
            $recettesVague = 0;
            $nombreElevesVague = 0;

            if($vague->classes){
                foreach ($vague->classes as $classe) {
                    if ($classe->eleves) {
                       $nombreElevesVague += $classe->eleves->count();

                       foreach ($classe->eleves as $eleve) {
                            if ($eleve->paiements) {
                                $recettesVague += $eleve->paiements->sum('montant');
                            }
                        }
                    }
                }
            }

            $vaguesStats[] = [
                'id' => $vague->id,
                'nom' => $vague->nom,
                'nombre_eleves' => $nombreElevesVague,
                'total_encaisse' => $recettesVague,
            ];
        }

        return response()->json([
            'caisse_generale' => [
                'total_scolarite_attendue' => $totalScolariteAttendu,
                'total_recettes' => $totalRecettesGlobales,
                'part_inscriptions' => $totalInscriptions,
                'part_pensions' => $totalPensions,
                'reste_a_percevoir' => max(0, $resteAPercevoirGlobal)
            ],
            'statistiques_vagues' => $vaguesStats
        ], 200);


    }
}
