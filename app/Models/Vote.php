<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    protected $fillable = ['commission_id', 'dossier_id', 'user_id', 'vote', 'commentaire'];
    public function commission(): BelongsTo { return $this->belongsTo(Commission::class); }
    public function dossier(): BelongsTo { return $this->belongsTo(Dossier::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
