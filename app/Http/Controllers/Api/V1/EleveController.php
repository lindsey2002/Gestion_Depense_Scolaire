<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\Classe;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
     * Inscrire un nouvel eleve et lier/creer son parent.
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
            // Validations du parent tuteur
            'parent_email' => 'required|email',
            'parent_prenom' => 'required|string|max:255',
            'parent_nom' => 'required|string|max:255',
        ], [
            'nom.unique' => "Un eleve avec ce nom, prenom et date naissance est deja inscrit. Si vous devez corriger une information, utilisez la modification.",
        ]);

        // 1. GESTION DU COMPTE PARENT (COMPARAISON)
        $parentUser = \App\Models\User::where('email', $request->parent_email)
                                      ->where('role', 'parent')
                                      ->first();

        // Si le parent n'existe pas du tout, on lui crée son compte d'accès
        if (!$parentUser) {
            // Génération d'un mot de passe initial
            $motDePasseBrut = 'ISI-' . date('Y') . '-' . rand(1000, 9999);

            $parentUser = \App\Models\User::create([
                'name' => $request->parent_prenom . ' ' . $request->parent_nom,
                'email' => $request->parent_email,
                'password' => bcrypt($motDePasseBrut),
                'role' => 'parent',
            ]);
        }

        // 2. LOGIQUE DE GÉNÉRATION DU MATRICULE AUTOMATIQUE
        $classe = Classe::findOrFail($request->classe_id);
        $chiffreNiveau = preg_replace('/[^0-9]/', '', $classe->niveau);
        $lettreNiveau = ucfirst(strtolower(substr($classe->niveau, 0, 3)));
        $prefixeNiveau = $lettreNiveau . $chiffreNiveau;

        $diminutifFiliere = strtolower($classe->diminutif);
        $cursus = strtolower($classe->cursus);

        $anneeCourante = date('y');
        $chiffresUnique = rand(1000, 9999);

        $matriculeGenere = $prefixeNiveau . $diminutifFiliere . $cursus . '-' . $anneeCourante . '-' . $chiffresUnique;

        // 3. INSERER LES DONNÉES EN BD
        $donnees = $request->all();
        $donnees['matricule'] = $matriculeGenere;
        $donnees['parent_id'] = $parentUser->id; // Stockage de la liaison de l'ID parent

        // Correction effectuée : on passe le tableau contenant le matricule et parent_id
        $eleve = Eleve::create($donnees);

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

    public function historique($id)
    {
        $eleve = Eleve::with(['classe.vague', 'paiements'])->find($id);

        if(! $eleve){
            return response()->json(['message' => "Eleve introuvable"], 404);
        }

        $vague = $eleve->classe->vague;
        if(! $vague){
            return response()->json(['message' => "Impossible de generer l'echeancier: cette classe n'est rattachée a aucune vague de rentree."], 422);
        }

        $dateDebut = \Carbon\Carbon::parse($vague->date_debut);
        $nombreMois = $vague->nombre_mois;

        \Carbon\Carbon::setLocale('fr');        
        $moisAnneeScolaire = [];

        for($i = 0; $i < $nombreMois; $i++){
            $moisAnneeScolaire[] = $dateDebut->copy()->addMonths($i)->translatedFormat('F');
        }

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
                'classe' => $eleve->classe->nom . ' ' . $eleve->classe->niveau,
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

    public function show(Request $request, $id)
    {
        $query = Eleve::query();

        if ($request->has('include')) {
            $relations = explode(',', $request->input('include'));
            $query->with($relations);
        }

        $eleve = $query->find($id);

        if (!$eleve) {
            return response()->json(['message' => 'Élève introuvable'], 404);
        }

        return response()->json($eleve);
    }
}