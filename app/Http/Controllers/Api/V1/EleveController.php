<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\Classe;
use Illuminate\Validation\Rule;
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
            'nom' => [
                'required', 'string', 'max:255',
                Rule::unique('eleves')->where(function ($query) use ($request){
                    return  $query->where('prenom', $request->prenom)
                                  ->where('date_naissance', $request->date_naissance);
                })
            ],
            'prenom' => 'required|string|max:255',
            'date_naissance' => 'required|date',
            'classe_id' => 'required|exists:classes,id',
        ], [
            'nom.unique' => "Un eleve avec ce nom, prenom et date naissance est deja inscrit. Si vous devez corriger une information, utilisez la modification.",
        ]
        );

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
            'matricule' => 'sometimes|required|string|unique:eleves,matricule,' . $id,
            'nom' => 'sometimes|required|string|max:255',
            'prenom' => 'sometimes|required|string|max:255',
            'date_naissance' => 'sometimes|required|date',
            'classe_id' => 'sometimes|required|exists:classes,id',
            'statut'  => 'sometimes|required|string|in:en attente,inscrit,actif,abandon,suspendu',
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
    public function historique($id)
    {
        $eleve = Eleve::with(['classe.vague', 'paiements'])->find($id);

        if(! $eleve){
            return response()->json([
                'message' => "Eleve introuvable"
            ], 404);
        }

        $vague = $eleve->classe->vague;
        if(! $vague){
            return response()->json([
                'message' => "Impossible de generer l'echeancier: cette classe n'est rattachée a aucune vague de rentree."
            ], 422);
        }

        $dateDebut = \Carbon\Carbon::parse($vague->date_debut);
        $nombreMois = $vague->nombre_mois;

        \Carbon\Carbon::setLocale('fr');        

        $moisAnneeScolaire = [];

        for($i = 0; $i < $nombreMois; $i++){
            $moisAnneeScolaire[] = $dateDebut->copy()->addMonths($i)->translatedFormat('F');
        }

        /* ici on extrait la colonne 'mois' de la collection de ses
         paiements, pluck('mois') va donner un tableau du genre: ['octobre',
         'novembre', ..] */
        $moisPayes = $eleve->paiements
            ->where('type_paiement', 'mensualite')
            ->whereNotNull('mois')
            ->pluck('mois')
            ->map(function($mois){
                return strtolower(trim($mois));
        })->toArray();

        $echeancierMensuel = [];
        
        foreach ($moisAnneeScolaire as $mois){
            $moisLower = strtolower($mois);
            $echeancierMensuel[] = [
                'mois' => ucfirst($mois),
                'statut' => in_array($moisLower, $moisPayes) ? 'Payé' : 'Impayé'
            ];
        }

        $totalVerse = $eleve->paiements->sum('montant');
        $totalPensionVerse = $eleve->paiements->where('type_paiement', 'mensualite')->sum('montant');

        return response()->json([
            'eleve' => [
                'id' => $eleve->id,
                'matricule' => $eleve->matricule,
                'prenom' => $eleve->prenom,
                'nom' => $eleve->nom,
                'classe' => $eleve->classe->nom . '' . $eleve->classe->niveau,
                'statut_actuel' => $eleve->statut,
                'vague_rentree' => $vague->nom
            ],
            'statistiques' => [
                'total_general_verse' => $totalVerse,
                'total_pension_verse' => $totalPensionVerse,
                'nombre_mois_payes' => count($moisPayes),
                'nombre_mois_dus' => $nombreMois,
            ],
            'echeancier_mensuel' => $echeancierMensuel,
            'liste_recus' => $eleve->paiements
        ], 200);
    }

}
