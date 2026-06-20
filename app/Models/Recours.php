<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recours extends Model
{
    protected $table = 'recours';

    protected $fillable = [
        'reference', 'dossier_id', 'etudiant_id', 'traite_par',
        'motif', 'justification', 'statut', 'decision_motif',
        'date_soumission', 'date_traitement',
    ];

    protected function casts(): array
    {
        return [
            'date_soumission' => 'datetime',
            'date_traitement' => 'datetime',
        ];
    }

    public static function genererReference(): string
    {
        $annee = date('Y');
        $count = self::whereYear('created_at', $annee)->count() + 1;
        return 'REC-' . $annee . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'etudiant_id');
    }

    public function traitePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traite_par');
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'soumis' => 'Soumis',
            'en_examen' => 'En examen',
            'accepte' => 'Accepté',
            'rejete' => 'Rejeté',
            default => $this->statut,
        };
    }

    public function getStatutColorAttribute(): string
    {
        return match ($this->statut) {
            'soumis' => 'yellow',
            'en_examen' => 'blue',
            'accepte' => 'green',
            'rejete' => 'red',
            default => 'gray',
        };
    }
}
