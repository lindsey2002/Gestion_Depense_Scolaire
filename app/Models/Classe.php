<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    //
    protected $fillable = ['nom', 'niveau', 'diminutif', 'cursus', 'tarif_mensuel', 'frais_inscription', 'vague_id'];

    public function vague(){
        return $this->belongsTo(Vague::class);
    }
}


