<?php

namespace App\Exports;

use App\Models\Paiement;
use App\Models\Depense;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JournalComptableExport implements WithMultipleSheets
{
    protected $annee;

    public function __construct($annee = '2025-2026')
    {
        $this->annee = $annee;
    }

    public function sheets(): array
    {
        return [
            new InstanceFeuilleEncaissements(),
            new InstanceFeuilleDepenses()
        ];
    }
}

class InstanceFeuilleEncaissements implements FromCollection, WithTitle, WithHeadings
{
    public function title(): string { return 'Encaissements'; }

    public function headings(): array {
        return ["N° Reçu", "Matricule", "Élève", "Classe", "Mois", "Montant Perçu", "Mode Paiement"];
    }

    public function collection() {
        return Paiement::with('eleve.classe')
            ->get()
            ->map(function($p) {
                return [
                    $p->numero_recu,
                    $p->eleve->matricule ?? 'N/A',
                    ($p->eleve->prenom ?? '') . ' ' . ($p->eleve->nom ?? ''),
                    $p->eleve->classe->nom ?? 'N/A',
                    $p->mois,
                    $p->montant,
                    str_replace('_', ' ', $p->mode_paiement)
                ];
            });
    }
}

// 🔴 Sous-classe pour l'onglet Dépenses
class InstanceFeuilleDepenses implements FromCollection, WithTitle, WithHeadings
{
    public function title(): string { return 'Dépenses'; }

    public function headings(): array {
        return ["ID", "Libellé", "Catégorie", "Mois", "Montant Payé", "Bénéficiaire"];
    }

    public function collection() {
        return Depense::get()->map(function($d) {
            return [
                'DEP-' . $d->id,
                $d->libelle,
                $d->categorie,
                $d->mois,
                $d->montant,
                $d->beneficiaire
            ];
        });
    }
}