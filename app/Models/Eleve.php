<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Paiement;
use App\Models\Eleve;

class Eleve extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['matricule', 'nom', 'prenom', 'date_naissance', 'classe_id', 'statut', 'parent_id'];

    protected $attributes = ['statut' => 'en_attente', ];
    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }
    /*  Obtenir les paiements de l'eleve. */
    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }
}


