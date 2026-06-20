<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alerte extends Model
{
    protected $fillable = [
        'user_id', 'type', 'titre', 'message', 'niveau', 'lien', 'lue', 'date_expiration',
    ];

    protected function casts(): array
    {
        return [
            'lue' => 'boolean',
            'date_expiration' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeNonLues($query)
    {
        return $query->where('lue', false);
    }

    public function scopeActives($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('date_expiration')->orWhere('date_expiration', '>', now());
        });
    }

    public function getNiveauColorAttribute(): string
    {
        return match ($this->niveau) {
            'danger' => 'red',
            'warning' => 'yellow',
            'success' => 'green',
            default => 'blue',
        };
    }
}
