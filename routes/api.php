<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PaiementController;
use App\Http\Controllers\Api\V1\EleveController;
use App\Http\Controllers\Api\V1\ClasseController;
use App\Http\Controllers\Api\V1\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function(){
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function(){
        Route::middleware('role:admin')->get('/admin-dashboard', function(){
            return response()->json(['message' => 'Bienvenue dans l\'espace Admin !']);
        });

        Route::middleware('role:admin,comptable')->get('/compta-data', function(){
            return response()->json(['message' => 'Voici les données financières.']);
        });

        Route::middleware('role:admin')->group(function(){

            Route::get('/classes', [ClasseController::class, 'index']);
            Route::post('/classes', [ClasseController::class, 'store']);
            Route::put('/classes/{id}', [ClasseController::class, 'update']);
            Route::delete('/classes/{id}', [ClasseController::class, 'destroy']);

            Route::get('/eleves', [EleveController::class, 'index']);
            Route::post('/eleves', [EleveController::class, 'store']);
            Route::put('/eleves/{id}', [EleveController::class, 'update']);
            Route::delete('/eleves/{id}', [EleveController::class, 'destroy']);

            Route::get('/paiements', [PaiementController::class, 'index']);
            Route::post('/paiements', [PaiementController::class, 'store']);
        });
    });
});
