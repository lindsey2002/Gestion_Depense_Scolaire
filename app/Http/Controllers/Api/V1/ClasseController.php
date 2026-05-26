<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use Illuminate\Http\Request;

class ClasseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $classes = Classe::all();
        return response()->json($classes, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'nom' => 'required|string|max:255',
            'niveau' => 'required|string',
            'tarif_mensuel' => 'required|numeric|min:0',
            'frais_inscription' => 'required|numeric|min:0'
        ]);

        $classe = Classe::create($request->all());

        return response()->json([
            'message' => 'Nouvelle classe créée avec succès',
            'classe' => $classe,
        ], 201); // 201 succes lors de la creation
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
