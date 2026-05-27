<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Eleve extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['matricule', 'nom', 'prenom', 'classe_id', 'parent_id'];

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }
}


