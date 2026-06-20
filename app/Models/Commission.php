<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commission extends Model
{
    protected $fillable = ['nom', 'date_deliberation', 'statut'];
    protected function casts(): array { return ['date_deliberation' => 'date']; }
    public function membres(): BelongsToMany { return $this->belongsToMany(User::class, 'commission_membres'); }
    public function dossiers(): BelongsToMany { return $this->belongsToMany(Dossier::class, 'commission_dossiers')->withPivot('decision_finale')->withTimestamps(); }
    public function votes(): HasMany { return $this->hasMany(Vote::class); }
}
