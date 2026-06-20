<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
        'telephone',
        'numero_etudiant',
        'actif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'actif' => 'boolean',
        ];
    }

    public function getNameAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function isEtudiant(): bool
    {
        return $this->role === 'etudiant';
    }

    public function isInstructeur(): bool
    {
        return $this->role === 'instructeur';
    }

    public function isAdministrateur(): bool
    {
        return $this->role === 'administrateur';
    }

    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class, 'etudiant_id');
    }

    public function dossiersAssignes(): HasMany
    {
        return $this->hasMany(Dossier::class, 'instructeur_id');
    }

    public function getProfilCompletionAttribute(): int
    {
        $fields = ['nom', 'prenom', 'email', 'telephone', 'numero_etudiant'];
        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $filled++;
            }
        }

        $hasDossier = $this->dossiers()->exists();
        if ($hasDossier) $filled++;

        $total = count($fields) + 1;
        return (int) round(($filled / $total) * 100);
    }
}
