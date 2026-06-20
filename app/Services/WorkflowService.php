<?php

namespace App\Services;

use App\Models\Dossier;
use App\Models\Parametre;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class WorkflowService
{
    /**
     * Calculer la priorité d'un dossier (1=highest, 5=lowest).
     * Basé sur: score_global, revenu_familial, nombre_freres_soeurs.
     */
    public function calculerPriorite(Dossier $dossier): int
    {
        $points = 0;

        // Score global (sur 20) - plus le score est élevé, plus la priorité est haute
        if ($dossier->score_global >= 16) {
            $points += 4;
        } elseif ($dossier->score_global >= 14) {
            $points += 3;
        } elseif ($dossier->score_global >= 12) {
            $points += 2;
        } elseif ($dossier->score_global >= 10) {
            $points += 1;
        }

        // Revenu familial - plus le revenu est bas, plus la priorité est haute
        if ($dossier->revenu_familial <= 2000) {
            $points += 4;
        } elseif ($dossier->revenu_familial <= 4000) {
            $points += 3;
        } elseif ($dossier->revenu_familial <= 6000) {
            $points += 2;
        } elseif ($dossier->revenu_familial <= 8000) {
            $points += 1;
        }

        // Nombre de frères/soeurs - plus la famille est grande, plus la priorité est haute
        if ($dossier->nombre_freres_soeurs >= 5) {
            $points += 4;
        } elseif ($dossier->nombre_freres_soeurs >= 4) {
            $points += 3;
        } elseif ($dossier->nombre_freres_soeurs >= 3) {
            $points += 2;
        } elseif ($dossier->nombre_freres_soeurs >= 2) {
            $points += 1;
        }

        // Convertir les points (0-12) en priorité (1-5)
        // Plus les points sont élevés, plus la priorité est haute (numéro bas)
        if ($points >= 10) {
            return 1;
        } elseif ($points >= 8) {
            return 2;
        } elseif ($points >= 6) {
            return 3;
        } elseif ($points >= 4) {
            return 4;
        }

        return 5;
    }

    /**
     * Trouver l'instructeur actif avec le moins de dossiers assignés en cours d'instruction.
     */
    public function instructeurOptimal(): ?User
    {
        return User::where('role', 'instructeur')
            ->where('actif', true)
            ->withCount(['dossiersAssignes' => function ($query) {
                $query->where('statut', 'en_cours_instruction');
            }])
            ->orderBy('dossiers_assignes_count', 'asc')
            ->first();
    }

    /**
     * Trouver les dossiers en instruction depuis plus de X jours.
     */
    public function dossiersAEscalader(int $joursMax = 15): Collection
    {
        return Dossier::where('statut', 'en_cours_instruction')
            ->where('date_instruction', '<=', now()->subDays($joursMax))
            ->with(['instructeur', 'etudiant'])
            ->get();
    }
}
