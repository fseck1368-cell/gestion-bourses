<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RendezVous extends Model
{
    protected $table = 'rendez_vous';

    protected $fillable = [
        'dossier_id', 'etudiant_id', 'instructeur_id',
        'date_heure', 'lieu', 'motif', 'statut',
        'note_instructeur', 'commentaire_refus',
    ];

    protected function casts(): array
    {
        return [
            'date_heure' => 'datetime',
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

    public function instructeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructeur_id');
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'demande' => 'Demandé',
            'confirme' => 'Confirmé',
            'refuse' => 'Refusé',
            'termine' => 'Terminé',
            'annule' => 'Annulé',
            default => $this->statut,
        };
    }

    public function getStatutColorAttribute(): string
    {
        return match ($this->statut) {
            'demande' => 'yellow',
            'confirme' => 'green',
            'refuse' => 'red',
            'termine' => 'blue',
            'annule' => 'gray',
            default => 'gray',
        };
    }
}
