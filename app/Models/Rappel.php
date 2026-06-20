<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rappel extends Model
{
    protected $fillable = ['dossier_id', 'destinataire_id', 'type', 'message', 'envoye', 'date_envoi'];
    protected function casts(): array { return ['envoye' => 'boolean', 'date_envoi' => 'datetime']; }
    public function dossier(): BelongsTo { return $this->belongsTo(Dossier::class); }
    public function destinataire(): BelongsTo { return $this->belongsTo(User::class, 'destinataire_id'); }
}
