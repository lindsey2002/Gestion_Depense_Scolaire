<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use App\Mail\AgentCreatedMail;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if(! $user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'message' => 'Identifiants incorrects.'
            ], 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        // Révoque le token qui a servi à la requête actuelle
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie avec succès.'
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email_perso' => 'required|email|unique:users,email',
            'role' => 'required|in:comptable,gestionnaire',
        ]);

        $prenomNettoye = Str::slug(strtolower($request->prenom), '');
        $nomNettoye = Str::slug(strtolower($request->nom), '');

        $premiereLettre = substr($prenomClean, 0, 3);
        $baseEmail = $premiereLettre . $nomClean;
        $emailPro = $baseEmail.'@isi.com';
        $passwordTemporaire = Str::random(6);
        
        $compteur = 1;
        while (\App\Models\User::where('email', $emailPro)->exists()) {
            $emailPro = $baseEmail . $compteur . '@isi.com';
            $compteur++;
        }
        $user = User::create([
            'name' => $request->prenom.' '.$request->nom,
            'email' => $emailPro,
            'password' => Hash::make($passwordTemporaire),
            'role' => $request->role,
        ]);

        try{
            Mail::to($request->email_perso)->send(new AgentCreatedMail($user, $passwordTemporaire));
        }catch (Exception $e){
            Log::error("Echec envoi mail agent : ".$e->getMessage());
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Agent cree avec succes et notifie par mail',
            'data' => [
                'email_professionnel' => $emailPro,
                'mot_de_passe_temporaire' => $passwordTemporaire
            ]
        ], 201);
    }
}
