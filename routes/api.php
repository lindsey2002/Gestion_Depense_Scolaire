<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PaiementController;
use App\Http\Controllers\Api\V1\EleveController;
use App\Http\Controllers\Api\V1\ClasseController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DepenseController;
use App\Http\Controllers\Api\V1\VagueController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Models\Annonce;


Route::prefix('v1')->group(function(){
    
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function(){

        Route::get('/classes', [ClasseController::class, 'index']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('dashboard/stats', [DashboardController::class, 'index']);
        Route::get('/annonces', function() { return response()->json(Annonce::latest()->get()); });

        Route::middleware('role:admin')->group(function(){
            Route::get('/admin-dashboard', function(){
                return response()->json(['message' => 'Bienvenue dans l\'espace Admin !']);
            });

            // seul l'admin peut configurer ou supprimer les classe de l'ecole
            Route::post('/register', [AuthController::class, 'register']);
            
            Route::post('/classes', [ClasseController::class, 'store']);
            Route::put('/classes/{id}', [ClasseController::class, 'update']);
            Route::delete('/classes/{id}', [ClasseController::class, 'destroy']);

            Route::get('/vagues', [VagueController::class, 'index']);
            Route::post('/vagues', [VagueController::class, 'store']);

            Route::get('/admin/dashboard/kpi', [DashboardController::class, 'getKpis']);
            Route::get('/admin/dashboard/charts', [DashboardController::class, 'getCharts']);
            Route::get('/admin/dashboard/classes', [DashboardController::class, 'getClasses']);

            Route::get('/dashboard/criteres-recherche', [DashboardController::class, 'getCriteresRecherche']);
            Route::get('/dashboard/detail-classe', [DashboardController::class, 'getDetailClasse']);

        });
            
        // zone commune: admin et comptable
        Route::middleware('role:admin,comptable')->group(function (){
            Route::get('/compta-data', function(){
                return response()->json(['message' => 'Voici les données financières.']);
            });

            Route::get('/eleves', [EleveController::class, 'index']);
            Route::get('/eleves/{id}', [EleveController::class, 'show']);
            Route::put('/eleves/{id}', [EleveController::class, 'update']);
            Route::delete('/eleves/{id}', [EleveController::class, 'destroy']);
            Route::get('/eleves/{id}/historique', [EleveController::class, 'historique']);

            Route::get('/paiements', [PaiementController::class, 'index']);
            Route::post('/paiements', [PaiementController::class, 'store']);

            Route::get('/depenses/export', [DepenseController::class, 'exportExcel']);
            Route::apiResource('depenses', DepenseController::class)->only(['index', 'store', 'update','destroy']);

            Route::get('/compta/exporter-excel', [PaiementController::class, 'exportExcel']);

            
        });
        // 4. ESPACE  GESTIONNAIRE
        Route::middleware('role:gestionnaire')->group(function () {
            Route::post('/eleves', [EleveController::class, 'store']);
            Route::get('/annonces', function() {
                return response()->json(\App\Models\Annonce::latest()->get());
            });
    
            Route::post('/annonces', function(\Illuminate\Http\Request $request) {
                // Validation manuelle rapide pour éviter le crash automatique 500
                if (!$request->has('titre') || !$request->has('contenu')) {
                    return response()->json(['error' => 'Champs obligatoires manquants'], 400);
                }

                $path = null;
                
                // Déplacement physique du fichier directement dans le dossier public
                if ($request->hasFile('fichier')) {
                    $file = $request->file('fichier');
                    $name = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads'), $name);
                    $path = 'uploads/' . $name;
                }

                // Création de l'annonce avec le chemin complet du modèle
                $annonce = \App\Models\Annonce::create([
                    'titre' => $request->input('titre'),
                    'contenu' => $request->input('contenu'),
                    'fichier' => $path
                ]);

                return response()->json($annonce, 201);
            });
        });



    });

});