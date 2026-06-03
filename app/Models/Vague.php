<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vague extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'date_debut',
        'nombre_mois',
    ];

    /* Une vague possède plusieurs classes*/

    public function classes(){
        return $this->hasMany(Classe::class);
    }
}
