<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\Classe;
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
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_naissance' => 'required|date',
            'classe_id' => 'required|exists:classes,id',
        ]);

        // on recupere la classe
        $classe = Classe::findOrFail($request->classe_id);
        
        // extraction du niveau: ex "licence 2" -> on prend "lic" + le chiffre "2" = "lic3"
        // on extrait le premier chiffre du niveau
        $chiffreNiveau = preg_replace('/[^0-9]/', '', $classe->niveau);

        // on prend les 3premieres lettres du niveau en minuscules ou majuscules
        $lettreNiveau = ucfirst(strtolower(substr($classe->niveau, 0, 3)));
        $prefixeNiveau = $lettreNiveau . $chiffreNiveau;

        // recuperation directe des configurations de la classe
        $diminutifFiliere = strtolower($classe->diminutif);
        $cursus = strtolower($classe->cursus);

        // les deux premieres lettres du prenom..

        // compteur de chiffres aleatoires pour l'unicite
        $anneeCourante = date('y');
        $chiffresUnique = rand(1000, 9999);

        // assemblage final
            $matriculeGenere = $prefixeNiveau . $diminutifFiliere . $cursus . '-' . $anneeCourante . '-' . $chiffresUnique;

        // enregistrement
        $donnees = $request->all();
        $donnees['matricule'] = $matriculeGenere;

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
