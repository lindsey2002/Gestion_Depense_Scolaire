<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Vague;
use Illuminate\Http\Request;

class VagueController extends Controller
{
    // lister toutes les vagues de rentrée

    public function index(){
        $vagues = Vague::all();
        return response()->json($vagues, 200);
    }

    // cree une nouvelle vague
    public function store(Request $request){
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:vagues,nom',
            'date_debut' => 'required|date',
            'nombre_mois' => 'required|integer|min:1|max:12',
        ]);

        $vague = Vague::create($validated);

        return response()->json([
            'message' => 'Vague de rentrée créée avec succès',
            'vague' => $vague
        ], 201);
    }
}
