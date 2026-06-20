<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CritereEligibilite extends Model
{
    protected $table = 'criteres_eligibilite';

    protected $fillable = [
        'campagne_id', 'nom', 'description', 'type',
        'valeur_min', 'valeur_max', 'valeurs_acceptees',
        'poids', 'obligatoire', 'actif',
    ];

    protected function casts(): array
    {
        return [
            'valeur_min' => 'decimal:2',
            'valeur_max' => 'decimal:2',
            'valeurs_acceptees' => 'array',
            'obligatoire' => 'boolean',
            'actif' => 'boolean',
        ];
    }

    public function campagne(): BelongsTo
    {
        return $this->belongsTo(Campagne::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'critere_id');
    }
}
