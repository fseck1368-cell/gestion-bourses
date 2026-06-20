<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'dossier_id',
        'nom_fichier',
        'chemin',
        'type_document',
        'mime_type',
        'taille',
    ];

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type_document) {
            'releve_notes' => 'Relevé de notes',
            'certificat_scolarite' => 'Certificat de scolarité',
            'justificatif_revenu' => 'Justificatif de revenu',
            'piece_identite' => 'Pièce d\'identité',
            'attestation_sociale' => 'Attestation sociale',
            'autre' => 'Autre document',
            default => $this->type_document,
        };
    }
}
