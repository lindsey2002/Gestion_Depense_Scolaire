<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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
    });
});
