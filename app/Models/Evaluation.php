<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    protected $fillable = [
        'dossier_id', 'critere_id', 'evaluateur_id',
        'note', 'critere_rempli', 'commentaire',
    ];

    protected function casts(): array
    {
        return [
            'note' => 'decimal:2',
            'critere_rempli' => 'boolean',
        ];
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function critere(): BelongsTo
    {
        return $this->belongsTo(CritereEligibilite::class, 'critere_id');
    }

    public function evaluateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluateur_id');
    }
}
