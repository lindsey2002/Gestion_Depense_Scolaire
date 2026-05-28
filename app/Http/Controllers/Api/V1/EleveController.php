<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use Illuminate\Http\Request;

class EleveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $eleves = Eleve::with('classe')->get();
        return response()->json($eleves, 200);
    }

    /**
     * Inscrire un nouvel eleve.
     */
    public function store(Request $request)
    {
        $request->validate([
            'matricule' => 'required|string|unique:eleves,matricule',
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_naissance' => 'required|date',
            'classe_id' => 'required|exists:classes,id',
        ]);

        $eleve = Eleve::create($request->all());

        return response()->json([
            'message' => 'Elève inscrit avec succès !',
            'eleve' => $eleve->load('classe')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $eleve = Eleve::findOrFail($id);

        $request->validate([
            'matricule' => 'sometimes|requires|string|unique:eleves,matricule,' . $id,
            'nom' => 'sometimes|required|string|max:255',
            'prenom' => 'sometimes|required|string|max:255',
            'date_naissance' => 'sometimes|required|date',
            'classe_id' => 'sometimes|required|exists:classes,id',
        ]);

        $eleve->update($request->all());

        return response()->json([
            'message' => 'Elève modifié avec succès !',
            'eleve' => $eleve->load('classe')
        ], 200);
    }

    public function destroy($id)
    {
        $eleve = Eleve::findOrFail($id);
        $eleve->delete();

        return response()->json([
            'message' => 'Elève supprimé avec succès (archivé).'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

}
