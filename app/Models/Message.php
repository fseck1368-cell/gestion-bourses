<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = ['dossier_id', 'user_id', 'contenu', 'demande_complement', 'lu'];
    protected function casts(): array { return ['demande_complement' => 'boolean', 'lu' => 'boolean']; }
    public function dossier(): BelongsTo { return $this->belongsTo(Dossier::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
