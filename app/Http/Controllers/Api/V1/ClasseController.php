<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class ClasseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = Classe::with('vague')->get();
        return response()->json($classes, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'nom' => [
                'required', 'string',
                Rule::unique('classes')->where(function ($query) use ($request){
                    return $query->where('niveau', $request->niveau)
                                 ->where('vague_id', $request->vague_id);
                })
            ],
            'niveau' => 'required|string',
            'tarif_mensuel' => 'required|numeric|min:0',
            'frais_inscription' => 'required|numeric|min:0',
            'vague_id' => 'nullable|exists:vagues,id',
        ], [
            'nom.unique' => "Cette classe existe déjà. Veuillez supprimer ou modifier la classe existante.",
        ]
        );

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
        $classe = Classe::findOrFail($id);

        $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'niveau' => 'sometimes|required|string',
            'tarif_mensuel' => 'sometimes|required|numeric|min:0',
            'frais_inscription' => 'sometimes|required|numeric|min:0',
            'vague_id' => 'nullable|exists:vagues,id',
        ]);

        $classe->update($request->all());

        return response()->json([
            'message' => 'Classe modifiée avec succès !',
            'classe' => $classe
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $classe = Classe::findOrFail($id);
        $classe->delete(); // Supprime la classe (ou l'archive si Soft Delete est activé dessus)

        return response()->json([
            'message' => 'Classe supprimée avec succès.'
        ], 200);
    }
}
