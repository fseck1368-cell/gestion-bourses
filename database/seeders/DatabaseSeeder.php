<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nom' => 'Admin',
            'prenom' => 'System',
            'email' => 'admin@bourses.sn',
            'password' => Hash::make('password'),
            'role' => 'administrateur',
            'actif' => true,
        ]);

        User::create([
            'nom' => 'Diop',
            'prenom' => 'Moussa',
            'email' => 'instructeur@bourses.sn',
            'password' => Hash::make('password'),
            'role' => 'instructeur',
            'actif' => true,
        ]);

        User::create([
            'nom' => 'Seck',
            'prenom' => 'Fatou',
            'email' => 'etudiant@bourses.sn',
            'password' => Hash::make('password'),
            'role' => 'etudiant',
            'numero_etudiant' => 'ETU-2024-001',
            'actif' => true,
        ]);
    }
}
