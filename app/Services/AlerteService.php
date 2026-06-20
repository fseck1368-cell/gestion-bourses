<?php

namespace App\Services;

use App\Models\Alerte;
use App\Models\Budget;
use App\Models\Convention;
use App\Models\Dossier;
use App\Models\Parametre;
use App\Models\RapportAcademique;
use App\Models\User;
use App\Models\Campagne;

class AlerteService
{
    public function genererAlertes(): array
    {
        $alertes = [];

        $alertes = array_merge($alertes, $this->alertesBudget());
        $alertes = array_merge($alertes, $this->alertesConventions());
        $alertes = array_merge($alertes, $this->alertesCampagnes());
        $alertes = array_merge($alertes, $this->alertesDossiersEnRetard());

        return $alertes;
    }

    public function alertesBudget(): array
    {
        $seuil = (int) Parametre::get('seuil_alerte_budget', 80);
        $alertes = [];

        $budgets = Budget::with('campagne')->get();
        foreach ($budgets as $budget) {
            if ($budget->taux_consommation >= $seuil) {
                $alerte = Alerte::firstOrCreate(
                    ['type' => 'budget_seuil', 'lien' => route('admin.budgets.index')],
                    [
                        'titre' => 'Budget critique : ' . $budget->libelle,
                        'message' => "Le budget \"{$budget->libelle}\" a atteint {$budget->taux_consommation}% de consommation.",
                        'niveau' => $budget->taux_consommation >= 95 ? 'danger' : 'warning',
                        'lien' => route('admin.budgets.index'),
                    ]
                );
                $alertes[] = $alerte;
            }
        }

        return $alertes;
    }

    public function alertesConventions(): array
    {
        $alertes = [];

        $conventions = Convention::where('statut', 'active')
            ->where('date_fin', '<=', now()->addDays(30))
            ->with('etudiant')->get();

        foreach ($conventions as $conv) {
            $alerte = Alerte::firstOrCreate(
                ['type' => 'convention_expiration', 'lien' => route('admin.conventions.show', $conv)],
                [
                    'titre' => 'Convention expire bientôt',
                    'message' => "La convention {$conv->reference} de {$conv->etudiant->name} expire le {$conv->date_fin->format('d/m/Y')}.",
                    'niveau' => $conv->date_fin->isPast() ? 'danger' : 'warning',
                    'lien' => route('admin.conventions.show', $conv),
                ]
            );
            $alertes[] = $alerte;
        }

        return $alertes;
    }

    public function alertesCampagnes(): array
    {
        $alertes = [];

        $campagnes = Campagne::where('active', true)
            ->where('date_cloture', '<=', now()->addDays(7))
            ->where('date_cloture', '>=', now())
            ->get();

        foreach ($campagnes as $camp) {
            $jours = now()->diffInDays($camp->date_cloture);
            $alerte = Alerte::firstOrCreate(
                ['type' => 'campagne_cloture', 'lien' => route('admin.campagnes.index')],
                [
                    'titre' => 'Campagne ferme bientôt',
                    'message' => "La campagne \"{$camp->nom}\" ferme dans {$jours} jour(s).",
                    'niveau' => $jours <= 2 ? 'danger' : 'warning',
                    'lien' => route('admin.campagnes.index'),
                ]
            );
            $alertes[] = $alerte;
        }

        return $alertes;
    }

    public function alertesDossiersEnRetard(): array
    {
        $alertes = [];
        $delai = (int) Parametre::get('delai_instruction', 15);

        $dossiers = Dossier::where('statut', 'en_cours_instruction')
            ->where('date_instruction', '<=', now()->subDays($delai))
            ->count();

        if ($dossiers > 0) {
            $alerte = Alerte::firstOrCreate(
                ['type' => 'dossiers_retard'],
                [
                    'titre' => "Dossiers en retard d'instruction",
                    'message' => "{$dossiers} dossier(s) dépassent le délai de {$delai} jours d'instruction.",
                    'niveau' => 'warning',
                    'lien' => route('admin.dossiers.index'),
                ]
            );
            $alertes[] = $alerte;
        }

        return $alertes;
    }
}
