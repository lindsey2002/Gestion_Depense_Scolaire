<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Depense;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $admin = User::create([
            'name' => 'Directeur',
            'email' => 'admin@ecole.com',
            'password' => Hash::make('passer123'),
            'role' => 'admin',
        ]);

        $comptable = User::create([
            'name' => 'Comptable',
            'email' => 'comptable@ecole.com',
            'password' => Hash::make('passer123'),
            'role' => 'comptable',
        ]);

        $parent = User::create([
            'name' => 'Parent',
            'email' => 'parent@ecole.com',
            'password' => Hash::make('passer123'),
            'role' => 'parent',
        ]);

        $classe1 = Classe::create([
            'nom' => 'Informatique',
            'niveau' => 'licence1',
            'tarif_mensuel' => 50000,
            'frais_inscription' => 120000,
        ]);

        $classe2 = Classe::create([
            'nom' => 'Informatique',
            'niveau' => 'licence2',
            'tarif_mensuel' => 55000,
            'frais_inscription' => 125000,
        ]);

        $eleve1 = Eleve::create([
            'nom' => 'Diop',
            'prenom' => 'Moussa',
            'classe_id' => $classe1->id,
            'parent_id' => $parent->id,
        ]);

        $eleve2 = Eleve::create([
            'nom' => 'Diop',
            'prenom' => 'Awa',
            'classe_id' => $classe2->id,
            'parent_id' => $parent->id,
        ]);

        Depense::create([
            'categorie' => 'Fournitures',
            'montant' => 15000,
            'date' => now()->format('Y-m-d'),
            'description' => 'Achat de marqueurs et de rames de papier',
        ]);

        Depense::create([
            'categorie' => 'Entretien',
            'montant' => 30000,
            'date' => now()->format('Y-m-d'),
            'description' => 'Réparation de la climatisation de la salle informatique',
        ]);
    }
}
