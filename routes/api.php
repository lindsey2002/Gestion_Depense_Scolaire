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


Route::prefix('v1')->group(function(){
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function(){
       
        Route::middleware('role:admin')->group(function(){
            Route::get('/admin-dashboard', function(){
                return response()->json(['message' => 'Bienvenue dans l\'espace Admin !']);
            });

            // seul l'admin peut configurer ou supprimer les classe de l'ecole
            Route::post('/classes', [ClasseController::class, 'store']);
            Route::put('/classes/{id}', [ClasseController::class, 'update']);
            Route::delete('/classes/{id}', [ClasseController::class, 'destroy']);

            Route::get('/vagues', [VagueController::class, 'index']);
            Route::post('/vagues', [VagueController::class, 'store']);

        });
            
        // zone commune: admin et comptable
        Route::middleware('role:admin,comptable')->group(function (){
            Route::get('/compta-data', function(){
                return response()->json(['message' => 'Voici les données financières.']);
            });

            Route::get('/classes', [ClasseController::class, 'index']);

            Route::get('/eleves', [EleveController::class, 'index']);
            Route::post('/eleves', [EleveController::class, 'store']);
            Route::put('/eleves/{id}', [EleveController::class, 'update']);
            Route::delete('/eleves/{id}', [EleveController::class, 'destroy']);
            Route::get('/eleves/{id}/historique', [EleveController::class, 'historique']);

            Route::get('/paiements', [PaiementController::class, 'index']);
            Route::post('/paiements', [PaiementController::class, 'store']);

            Route::apiResource('depenses', DepenseController::class)->only(['index', 'store']);

            Route::get('dashboard/stats', [DashboardController::class, 'index']);
        });
        
    });
});
