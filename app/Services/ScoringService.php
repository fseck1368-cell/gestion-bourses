<?php

namespace App\Services;

use App\Models\Dossier;
use App\Models\CritereEligibilite;
use App\Models\Parametre;

class ScoringService
{
    public function calculerScore(Dossier $dossier): array
    {
        $score = 0;
        $details = [];

        // Score basé sur la moyenne générale (max 30 points)
        $scoreMoyenne = $this->scoreMoyenne($dossier->moyenne_generale);
        $score += $scoreMoyenne;
        $details[] = ['critere' => 'Moyenne générale', 'valeur' => $dossier->moyenne_generale . '/20', 'points' => $scoreMoyenne, 'max' => 30];

        // Score basé sur le revenu familial (max 30 points)
        $scoreRevenu = $this->scoreRevenu($dossier->revenu_familial);
        $score += $scoreRevenu;
        $details[] = ['critere' => 'Revenu familial', 'valeur' => number_format($dossier->revenu_familial, 0, ',', ' ') . ' DH', 'points' => $scoreRevenu, 'max' => 30];

        // Score basé sur la situation sociale (max 20 points)
        $scoreSociale = $this->scoreSituation($dossier->situation_sociale);
        $score += $scoreSociale;
        $details[] = ['critere' => 'Situation sociale', 'valeur' => $dossier->situation_sociale, 'points' => $scoreSociale, 'max' => 20];

        // Score basé sur le nombre de frères/soeurs (max 10 points)
        $scoreFamille = min($dossier->nombre_freres_soeurs * 2, 10);
        $score += $scoreFamille;
        $details[] = ['critere' => 'Charge familiale', 'valeur' => $dossier->nombre_freres_soeurs . ' frère(s)/soeur(s)', 'points' => $scoreFamille, 'max' => 10];

        // Score basé sur le niveau d'étude (max 10 points)
        $scoreNiveau = $this->scoreNiveau($dossier->niveau_etude);
        $score += $scoreNiveau;
        $details[] = ['critere' => 'Niveau d\'étude', 'valeur' => $dossier->niveau_etude, 'points' => $scoreNiveau, 'max' => 10];

        $scoreGlobal = round(($score / 100) * 20, 2);

        $dossier->update(['score_global' => $scoreGlobal]);

        $seuilAcceptation = (float) Parametre::get('seuil_score_acceptation', 12);

        return [
            'score_global' => $scoreGlobal,
            'score_brut' => $score,
            'max_possible' => 100,
            'details' => $details,
            'recommandation' => $scoreGlobal >= $seuilAcceptation ? 'favorable' : 'defavorable',
            'seuil' => $seuilAcceptation,
        ];
    }

    private function scoreMoyenne(float $moyenne): int
    {
        if ($moyenne >= 16) return 30;
        if ($moyenne >= 14) return 25;
        if ($moyenne >= 12) return 20;
        if ($moyenne >= 10) return 15;
        return 5;
    }

    private function scoreRevenu(float $revenu): int
    {
        if ($revenu <= 2000) return 30;
        if ($revenu <= 4000) return 25;
        if ($revenu <= 6000) return 20;
        if ($revenu <= 8000) return 15;
        if ($revenu <= 10000) return 10;
        return 5;
    }

    private function scoreSituation(string $situation): int
    {
        return match (strtolower($situation)) {
            'orphelin', 'orpheline' => 20,
            'handicape', 'handicapé', 'handicapée' => 18,
            'famille_monoparentale', 'monoparentale' => 16,
            'famille_nombreuse', 'nombreuse' => 14,
            'precaire', 'précaire' => 12,
            'modeste' => 10,
            default => 5,
        };
    }

    private function scoreNiveau(string $niveau): int
    {
        return match (strtolower($niveau)) {
            'doctorat' => 10,
            'master', 'master 2', 'master 1' => 8,
            'licence', 'licence 3' => 6,
            'licence 2', 'l2' => 4,
            'licence 1', 'l1' => 3,
            default => 2,
        };
    }
}
