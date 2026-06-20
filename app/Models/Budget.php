<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $fillable = [
        'campagne_id', 'libelle', 'montant_alloue',
        'montant_consomme', 'annee_universitaire',
        'source_financement', 'observations',
    ];

    protected function casts(): array
    {
        return [
            'montant_alloue' => 'decimal:2',
            'montant_consomme' => 'decimal:2',
        ];
    }

    public function campagne(): BelongsTo
    {
        return $this->belongsTo(Campagne::class);
    }

    public function getMontantRestantAttribute(): float
    {
        return $this->montant_alloue - $this->montant_consomme;
    }

    public function getTauxConsommationAttribute(): float
    {
        if ($this->montant_alloue == 0) return 0;
        return round(($this->montant_consomme / $this->montant_alloue) * 100, 2);
    }
}
