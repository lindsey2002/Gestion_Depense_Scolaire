<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Eleve;
use App\Models\Vague;
use App\Models\Classe; // Ajout de ton modèle Classe
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        // ==========================================
        // 1. TES CALCULS FINANCIERS (INCHANGÉS)
        // ==========================================
        $totalRecettesGlobales = Paiement::sum('montant');
        $totalInscriptions = Paiement::where('type_paiement', 'inscription')->sum('montant');
        $totalPensions = Paiement::where('type_paiement', 'mensualite')->sum('montant');

        $totalScolariteAttendu = 0;
        $eleves = Eleve::with('classe.vague')->get();

        foreach($eleves as $eleve){
            if($eleve->classe && $eleve->classe->vague){
                $nombreMoisDus = $eleve->classe->vague->nombre_mois;
                $montantMensuel = $eleve->classe->tarif_mensuel;
                $fraisInscription = $eleve->classe->frais_inscription;

                $totalScolariteAttendu += (($nombreMoisDus * $montantMensuel) + $fraisInscription);
            }
        }

        $resteAPercevoirGlobal = $totalScolariteAttendu - $totalRecettesGlobales;

        // ==========================================
        // 2. TES STATISTIQUES VAGUES (INCHANGÉES)
        // ==========================================
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

        // ==========================================
        // 3. AJOUTS POUR TON FRONT-END (KPI, GRAPH_MOIS, CLASSES)
        // ==========================================
        
        // Récupération propre des classes avec leur effectif et leur vague pour ton tableau
        $classesListe = Classe::withCount('eleves')
            ->with('vague')
            ->get()
            ->map(function($c) {
                return [
                    'id' => $c->id,
                    'nom' => $c->nom,
                    'vague_nom' => $c->vague ? $c->vague->nom : 'Sans Vague',
                    'eleves_count' => $c->eleves_count,
                    'capacite_max' => $c->capacite_max ?? 50 // Valeur par défaut si vide dans ta BD
                ];
            
            }
            );


        // Récupération des inscriptions groupées par mois pour ton graphique en barres
        $inscriptionsMensuelles = Eleve::select(
            DB::raw("DATE_FORMAT(created_at, '%b') as mois"),
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('mois')
        ->orderBy('created_at', 'asc')
        ->get();

        // Calcul rapide du taux d'élèves à jour (Simulé sur la base du reste à percevoir)
        $totalEleves = $eleves->count();
        $totalAJour = 0;
        $totalEnRetard = 0;

        foreach($eleves as $e) {
            $dejaPaye = $e->paiements ? $e->paiements->sum('montant') : 0;
            // Un élève est considéré en retard s'il doit de l'argent (Logique basique)
            if ($dejaPaye > 0) {
                $totalAJour++;
            } else {
                $totalEnRetard++;
            }
        }
        $tauxAJour = $totalEleves > 0 ? round(($totalAJour / $totalEleves) * 100) : 0;

        // ==========================================
        // 4. RÉPONSE DOUBLEMENT COMPATIBLE
        // ==========================================
        return response()->json([
            // Tes clés d'origine pour ne pas casser ton travail sur la compta
            'caisse_generale' => [
                'total_scolarite_attendue' => $totalScolariteAttendu,
                'total_recettes' => $totalRecettesGlobales,
                'part_inscriptions' => $totalInscriptions,
                'part_pensions' => $totalPensions,
                'reste_a_percevoir' => max(0, $resteAPercevoirGlobal)
            ],
            'statistiques_vagues' => $vaguesStats,

            // Les nouvelles clés que tes 3 fichiers Vue.js (Kpi, Charts, Table) demandent
            'kpis' => [
                'totalEtudiants' => $totalEleves,
                'totalClasses'   => Classe::count(),
                'totalVagues'    => Vague::count(),
                'totalRecettes'  => number_format($totalRecettesGlobales, 0, '.', ' ')
            ],
            'charts' => [
                'inscriptions' => $inscriptionsMensuelles,
                'paiements' => [
                    'tauxAJour' => $tauxAJour,
                    'totalAJour' => $totalAJour,
                    'totalEnRetard' => $totalEnRetard
                ]
            ],
            'classes' => $classesListe
        ], 200);
    }

    public function getCriteresRecherche()
{
    // 1. Récupérer tous les noms de classes uniques (ex: Génie Logiciel, Finance...)
    $formations = Classe::select('nom')
        ->groupBy('nom')
        ->pluck('nom');

    // 2. Récupérer tous les niveaux uniques (ex: L1, L2, L3, M1, M2...)
    // Si tu as un champ 'niveau' dans ta table classes :
    $niveaux = Classe::select('niveau')
        ->whereNotNull('niveau')
        ->groupBy('niveau')
        ->pluck('niveau');

    // 3. Récupérer toutes les vagues actives
    $vagues = \App\Models\Vague::select('id', 'nom')->get();

    return response()->json([
        'formations' => $formations,
        'niveaux' => $niveaux,
        'vagues' => $vagues
    ]);
}
public function getDetailClasse(Request $request)
{
    $formation = $request->query('formation');
    $niveau = $request->query('niveau');
    $vagueId = $request->query('vague');

    // 1. Trouver la classe exacte qui correspond aux 3 critères
    $classe = Classe::where('nom', $formation)
        ->where('niveau', $niveau)
        ->where('vague_id', $vagueId)
        ->first();

    if (!$classe) {
        return response()->json(['message' => 'Aucune classe trouvée pour ces critères.'], 404);
    }

    // 2. Charger les élèves de CETTE classe précise avec leurs paiements (Eager Loading pour éviter le problème N+1)
    $eleves = \App\Models\Eleve::where('classe_id', $classe->id)
        ->with('paiements')
        ->get()
        ->map(function($e) {
            $totalPaye = $e->paiements ? $e->paiements->sum('montant') : 0;
            return [
                'id' => $e->id,
                'matricule' => $e->matricule, // Adapte selon le nom de ton champ matricule
                'nom' => $e->nom,
                'prenom' => $e->prenom,
                'statut' => $e->statut ?? 'Actif',
                'total_paye' => $totalPaye,
                // On considère qu'un élève est à jour s'il a payé (tu pourras affiner selon tes mensualités)
                'est_a_jour' => $totalPaye > 0 
            ];
        });

    // 3. Calculer les KPIs financiers spécifiques à ce groupe
    $totalEncaisse = $eleves->sum('total_paye');
    // Exemple de calcul : si chaque classe a un montant de scolarité théorique par élève
    $fraisScolariteTheorique = $classe->frais_scolarite ?? 500000; 
    $attenduTheorique = $eleves->count() * $fraisScolariteTheorique;
    $resteAPercevoir = max(0, $attenduTheorique - $totalEncaisse);

    return response()->json([
        'classe_info' => [
            'id' => $classe->id,
            'nom' => $classe->nom,
            'niveau' => $classe->niveau,
            'vague' => $classe->vague ? $classe->vague->nom : 'N/A',
        ],
        'kpis' => [
            'effectif' => $eleves->count(),
            'capacite_max' => $classe->capacite_max ?? 50,
            'total_encaisse' => $totalEncaisse,
            'reste_a_percevoir' => $resteAPercevoir
        ],
        'eleves' => $eleves
    ]);
}
}