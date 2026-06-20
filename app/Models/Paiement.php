<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends Model
{
    protected $fillable = [
        'dossier_id', 'etudiant_id', 'reference', 'montant', 'statut',
        'mode_paiement', 'reference_bancaire', 'banque', 'numero_compte',
        'date_prevue', 'date_versement', 'periode', 'commentaire', 'valide_par',
    ];

    protected function casts(): array
    {
        return [
            'montant' => 'decimal:2',
            'date_prevue' => 'date',
            'date_versement' => 'date',
        ];
    }

    public static function genererReference(): string
    {
        $annee = date('Y');
        $count = self::whereYear('created_at', $annee)->count() + 1;
        return 'PAY-' . $annee . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'etudiant_id');
    }

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'en_attente' => 'En attente',
            'valide' => 'Validé',
            'verse' => 'Versé',
            'annule' => 'Annulé',
            default => $this->statut,
        };
    }

    public function getStatutColorAttribute(): string
    {
        return match ($this->statut) {
            'en_attente' => 'yellow',
            'valide' => 'blue',
            'verse' => 'green',
            'annule' => 'red',
            default => 'gray',
        };
    }
}
