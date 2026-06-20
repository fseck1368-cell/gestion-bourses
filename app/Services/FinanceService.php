<?php

namespace App\Services;

use App\Models\Alerte;
use App\Models\Convention;
use App\Models\Paiement;
use App\Models\Parametre;

class FinanceService
{
    /**
     * Vérifier si un étudiant a dépassé le plafond de cumul des bourses.
     */
    public function verifierPlafondCumul(int $etudiantId): array
    {
        $plafond = (float) Parametre::get('plafond_cumul_bourse', 50000);

        $totalRecu = (float) Paiement::where('etudiant_id', $etudiantId)
            ->whereIn('statut', ['valide', 'verse'])
            ->sum('montant');

        $depasse = $totalRecu >= $plafond;
        $reste = max(0, $plafond - $totalRecu);

        return [
            'depasse' => $depasse,
            'total_recu' => $totalRecu,
            'plafond' => $plafond,
            'reste' => $reste,
        ];
    }

    /**
     * Détecter les anomalies pour un étudiant (paiements sur 2+ conventions actives simultanément).
     */
    public function detecterAnomalies(int $etudiantId): array
    {
        $anomalies = [];

        // Vérifier si l'étudiant a des paiements sur 2+ conventions actives simultanément
        $conventionsActives = Convention::where('etudiant_id', $etudiantId)
            ->where('statut', 'active')
            ->get();

        if ($conventionsActives->count() >= 2) {
            $conventionsAvecPaiements = $conventionsActives->filter(function ($convention) use ($etudiantId) {
                return Paiement::where('etudiant_id', $etudiantId)
                    ->where('dossier_id', $convention->dossier_id)
                    ->whereIn('statut', ['en_attente', 'valide', 'verse'])
                    ->exists();
            });

            if ($conventionsAvecPaiements->count() >= 2) {
                $anomalies[] = [
                    'type' => 'cumul_conventions',
                    'message' => 'L\'étudiant a des paiements sur ' . $conventionsAvecPaiements->count() . ' conventions actives simultanément.',
                    'conventions' => $conventionsAvecPaiements->pluck('reference')->toArray(),
                ];
            }
        }

        return $anomalies;
    }

    /**
     * Vérifier si un montant nécessite une double validation.
     */
    public function necessiteDoubleValidation(float $montant): bool
    {
        $seuil = (float) Parametre::get('seuil_double_validation', 5000);

        return $montant > $seuil;
    }
}
