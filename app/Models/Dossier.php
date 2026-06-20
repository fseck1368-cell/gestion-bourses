<?php

namespace App\Models;

use App\Observers\DossierObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(DossierObserver::class)]
class Dossier extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'etudiant_id',
        'instructeur_id',
        'statut',
        'annee_universitaire',
        'niveau_etude',
        'filiere',
        'etablissement',
        'moyenne_generale',
        'situation_sociale',
        'revenu_familial',
        'nombre_freres_soeurs',
        'motif_demande',
        'commentaire_instructeur',
        'commentaire_admin',
        'score_global',
        'complement_requis',
        'complement_description',
        'complement_date_demande',
        'complement_date_reponse',
        'avis_instructeur',
        'date_avis_instructeur',
        'avis_transmis_admin',
        'date_soumission',
        'date_instruction',
        'date_decision',
    ];

    protected function casts(): array
    {
        return [
            'date_soumission' => 'datetime',
            'date_instruction' => 'datetime',
            'date_decision' => 'datetime',
            'complement_date_demande' => 'datetime',
            'complement_date_reponse' => 'datetime',
            'date_avis_instructeur' => 'datetime',
            'complement_requis' => 'boolean',
            'avis_transmis_admin' => 'boolean',
            'moyenne_generale' => 'decimal:2',
            'revenu_familial' => 'decimal:2',
        ];
    }

    public static function genererReference(): string
    {
        $annee = date('Y');
        $count = self::whereYear('created_at', $annee)->count() + 1;
        return 'BRS-' . $annee . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'etudiant_id');
    }

    public function instructeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructeur_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function historiques(): HasMany
    {
        return $this->hasMany(Historique::class)->orderBy('created_at', 'desc');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function campagne(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Campagne::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function recours(): HasMany
    {
        return $this->hasMany(Recours::class);
    }

    public function convention(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Convention::class);
    }

    public function rapportsAcademiques(): HasMany
    {
        return $this->hasMany(RapportAcademique::class);
    }

    public function rendezVous(): HasMany
    {
        return $this->hasMany(RendezVous::class);
    }

    public function approbations(): HasMany
    {
        return $this->hasMany(Approbation::class)->orderBy('ordre');
    }

    public function estModifiable(): bool
    {
        return $this->statut === 'soumis';
    }

    public function getAvisLabelAttribute(): ?string
    {
        return match ($this->avis_instructeur) {
            'favorable' => 'Favorable',
            'defavorable' => 'Défavorable',
            'reserve' => 'Réservé',
            default => null,
        };
    }

    public function getAvisColorAttribute(): string
    {
        return match ($this->avis_instructeur) {
            'favorable' => 'green',
            'defavorable' => 'red',
            'reserve' => 'yellow',
            default => 'gray',
        };
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'soumis' => 'Soumis',
            'en_cours_instruction' => 'En cours d\'instruction',
            'accepte' => 'Accepté',
            'rejete' => 'Rejeté',
            default => $this->statut,
        };
    }

    public function getStatutColorAttribute(): string
    {
        return match ($this->statut) {
            'soumis' => 'yellow',
            'en_cours_instruction' => 'blue',
            'accepte' => 'green',
            'rejete' => 'red',
            default => 'gray',
        };
    }
}
