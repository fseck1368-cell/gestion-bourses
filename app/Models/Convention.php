<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Convention extends Model
{
    protected $fillable = [
        'reference', 'dossier_id', 'etudiant_id', 'date_debut', 'date_fin',
        'montant_mensuel', 'duree_mois', 'conditions', 'obligations_etudiant',
        'statut', 'date_signature', 'motif_resiliation',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'date_signature' => 'date',
            'montant_mensuel' => 'decimal:2',
        ];
    }

    public static function genererReference(): string
    {
        $annee = date('Y');
        $count = self::whereYear('created_at', $annee)->count() + 1;
        return 'CONV-' . $annee . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'etudiant_id');
    }

    public function rapportsAcademiques(): HasMany
    {
        return $this->hasMany(RapportAcademique::class);
    }

    public function getMontantTotalAttribute(): float
    {
        return $this->montant_mensuel * $this->duree_mois;
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'brouillon' => 'Brouillon',
            'active' => 'Active',
            'suspendue' => 'Suspendue',
            'terminee' => 'Terminée',
            'resiliee' => 'Résiliée',
            default => $this->statut,
        };
    }

    public function getStatutColorAttribute(): string
    {
        return match ($this->statut) {
            'brouillon' => 'gray',
            'active' => 'green',
            'suspendue' => 'yellow',
            'terminee' => 'blue',
            'resiliee' => 'red',
            default => 'gray',
        };
    }
}
