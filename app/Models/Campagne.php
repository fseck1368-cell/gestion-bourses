<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campagne extends Model
{
    protected $fillable = ['nom', 'description', 'annee_universitaire', 'date_ouverture', 'date_cloture', 'active'];
    protected function casts(): array { return ['date_ouverture' => 'date', 'date_cloture' => 'date', 'active' => 'boolean']; }
    public function dossiers(): HasMany { return $this->hasMany(Dossier::class); }
    public function criteres(): HasMany { return $this->hasMany(CritereEligibilite::class); }
    public function budgets(): HasMany { return $this->hasMany(Budget::class); }
    public function estOuverte(): bool { return $this->active && now()->between($this->date_ouverture, $this->date_cloture); }
}
