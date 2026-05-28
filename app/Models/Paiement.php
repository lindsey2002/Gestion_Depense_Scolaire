<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'eleve_id', 'montant', 'date_paiement',
        'mode_paiement', 'type_paiement', 'mois'
    ];

    /* Un paiement appartien a un eleve. */
    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }
}
