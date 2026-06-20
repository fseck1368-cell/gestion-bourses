<?php

namespace App\Services;

use App\Models\Convention;
use App\Models\Dossier;
use App\Models\RapportAcademique;

class AcademiqueService
{
    /**
     * Évaluer si une convention devrait être renouvelée basé sur le dernier rapport académique.
     */
    public function evaluerRenouvellement(Convention $convention): array
    {
        $dernierRapport = RapportAcademique::where('convention_id', $convention->id)
            ->orderBy('annee_universitaire', 'desc')
            ->orderBy('semestre', 'desc')
            ->first();

        if (!$dernierRapport) {
            return [
                'recommande' => false,
                'motif' => 'Aucun rapport académique disponible pour évaluation.',
                'moyenne_actuelle' => 0.0,
            ];
        }

        $moyenneActuelle = (float) $dernierRapport->moyenne;

        // Critères de renouvellement
        $moyenneMinimale = 10.0;
        $assiduiteMinimale = 75.0;

        $recommande = true;
        $motifs = [];

        if ($moyenneActuelle < $moyenneMinimale) {
            $recommande = false;
            $motifs[] = 'Moyenne insuffisante (' . $moyenneActuelle . '/20, minimum requis: ' . $moyenneMinimale . ')';
        }

        if ($dernierRapport->taux_assiduite < $assiduiteMinimale) {
            $recommande = false;
            $motifs[] = 'Assiduité insuffisante (' . $dernierRapport->taux_assiduite . '%, minimum requis: ' . $assiduiteMinimale . '%)';
        }

        if ($dernierRapport->statut_academique === 'exclus') {
            $recommande = false;
            $motifs[] = 'Étudiant exclu du programme académique';
        }

        if ($recommande) {
            $motif = 'Résultats satisfaisants - renouvellement recommandé';
        } else {
            $motif = implode('. ', $motifs);
        }

        return [
            'recommande' => $recommande,
            'motif' => $motif,
            'moyenne_actuelle' => $moyenneActuelle,
        ];
    }

    /**
     * Analyser la progression académique d'un étudiant (comparaison des moyennes semestrielles).
     */
    public function analyserProgression(int $etudiantId): array
    {
        $rapports = RapportAcademique::where('etudiant_id', $etudiantId)
            ->orderBy('annee_universitaire', 'asc')
            ->orderBy('semestre', 'asc')
            ->get();

        $moyennes = $rapports->pluck('moyenne')->map(fn ($m) => (float) $m)->toArray();

        if (count($moyennes) < 2) {
            return [
                'tendance' => 'stable',
                'moyennes' => $moyennes,
                'variation' => 0.0,
            ];
        }

        $derniere = end($moyennes);
        $avantDerniere = $moyennes[count($moyennes) - 2];
        $variation = round($derniere - $avantDerniere, 2);

        if ($variation > 0.5) {
            $tendance = 'hausse';
        } elseif ($variation < -0.5) {
            $tendance = 'baisse';
        } else {
            $tendance = 'stable';
        }

        return [
            'tendance' => $tendance,
            'moyennes' => $moyennes,
            'variation' => $variation,
        ];
    }

    /**
     * Calculer un score de fiabilité multi-années basé sur l'historique académique.
     */
    public function scoresFiabilite(int $etudiantId): array
    {
        $rapports = RapportAcademique::where('etudiant_id', $etudiantId)
            ->orderBy('annee_universitaire', 'asc')
            ->orderBy('semestre', 'asc')
            ->get();

        if ($rapports->isEmpty()) {
            return [
                'score' => 0,
                'annees_evaluees' => 0,
                'details' => 'Aucun rapport académique disponible.',
            ];
        }

        $anneesDistinctes = $rapports->pluck('annee_universitaire')->unique()->count();
        $score = 0;
        $details = [];

        // Moyenne générale sur toutes les années (max 40 points)
        $moyenneGlobale = $rapports->avg('moyenne');
        $scoreMoyenne = min(40, round(($moyenneGlobale / 20) * 40));
        $score += $scoreMoyenne;
        $details[] = 'Moyenne globale: ' . round($moyenneGlobale, 2) . '/20 (' . $scoreMoyenne . '/40 pts)';

        // Assiduité moyenne (max 30 points)
        $assiduiteGlobale = $rapports->avg('taux_assiduite');
        $scoreAssiduite = min(30, round(($assiduiteGlobale / 100) * 30));
        $score += $scoreAssiduite;
        $details[] = 'Assiduité moyenne: ' . round($assiduiteGlobale, 1) . '% (' . $scoreAssiduite . '/30 pts)';

        // Régularité/constance (max 20 points) - basé sur l'écart-type des moyennes
        $moyennes = $rapports->pluck('moyenne')->map(fn ($m) => (float) $m);
        $ecartType = $this->ecartType($moyennes->toArray());
        $scoreRegularite = max(0, min(20, 20 - round($ecartType * 5)));
        $score += $scoreRegularite;
        $details[] = 'Régularité: écart-type ' . round($ecartType, 2) . ' (' . $scoreRegularite . '/20 pts)';

        // Bonus ancienneté (max 10 points) - 2 points par année évaluée
        $bonusAnciennete = min(10, $anneesDistinctes * 2);
        $score += $bonusAnciennete;
        $details[] = 'Ancienneté: ' . $anneesDistinctes . ' année(s) (' . $bonusAnciennete . '/10 pts)';

        $score = min(100, max(0, $score));

        return [
            'score' => $score,
            'annees_evaluees' => $anneesDistinctes,
            'details' => implode('. ', $details),
        ];
    }

    /**
     * Calculer l'écart-type d'un tableau de valeurs.
     */
    private function ecartType(array $values): float
    {
        $count = count($values);
        if ($count < 2) {
            return 0.0;
        }

        $moyenne = array_sum($values) / $count;
        $sommeCarres = 0;

        foreach ($values as $valeur) {
            $sommeCarres += pow($valeur - $moyenne, 2);
        }

        return sqrt($sommeCarres / $count);
    }
}
