<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RapportAcademique extends Model
{
    protected $table = 'rapports_academiques';

    protected $fillable = [
        'dossier_id', 'etudiant_id', 'convention_id', 'semestre',
        'annee_universitaire', 'moyenne', 'credits_valides', 'credits_total',
        'taux_assiduite', 'statut_academique', 'renouvellement_recommande', 'observations',
    ];

    protected function casts(): array
    {
        return [
            'moyenne' => 'decimal:2',
            'taux_assiduite' => 'decimal:2',
            'renouvellement_recommande' => 'boolean',
        ];
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'etudiant_id');
    }

    public function convention(): BelongsTo
    {
        return $this->belongsTo(Convention::class);
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut_academique) {
            'bon' => 'Bon',
            'acceptable' => 'Acceptable',
            'insuffisant' => 'Insuffisant',
            'exclus' => 'Exclus',
            default => $this->statut_academique,
        };
    }

    public function getStatutColorAttribute(): string
    {
        return match ($this->statut_academique) {
            'bon' => 'green',
            'acceptable' => 'blue',
            'insuffisant' => 'yellow',
            'exclus' => 'red',
            default => 'gray',
        };
    }
}
