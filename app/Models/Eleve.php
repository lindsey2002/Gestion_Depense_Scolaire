<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Eleve extends Model
{
    use SoftDeletes;

    protected $fillable = ['nom', 'prenom', 'classe_id', 'parent_id'];
}
