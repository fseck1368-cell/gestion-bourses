<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Approbation extends Model
{
    protected $fillable = [
        'dossier_id', 'approbateur_id', 'ordre', 'statut', 'commentaire', 'date_decision',
    ];

    protected function casts(): array
    {
        return [
            'date_decision' => 'datetime',
        ];
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function approbateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approbateur_id');
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'en_attente' => 'En attente',
            'approuve' => 'Approuvé',
            'rejete' => 'Rejeté',
            default => $this->statut,
        };
    }

    public function getStatutColorAttribute(): string
    {
        return match ($this->statut) {
            'en_attente' => 'yellow',
            'approuve' => 'green',
            'rejete' => 'red',
            default => 'gray',
        };
    }
}
