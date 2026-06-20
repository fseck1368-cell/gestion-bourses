<?php

namespace App\Services;

use App\Models\Campagne;
use App\Models\Dossier;
use App\Models\Parametre;
use App\Models\User;

class EligibiliteService
{
    public function verifierEligibilite(User $etudiant, ?int $campagneId = null): array
    {
        $erreurs = [];
        $avertissements = [];

        // Vérifier si le compte est actif
        if (!$etudiant->actif) {
            $erreurs[] = 'Votre compte est désactivé.';
        }

        // Vérifier si l'étudiant a déjà un dossier cette année (doublon)
        $annee = date('Y') . '-' . (date('Y') + 1);
        $dossierExistant = Dossier::where('etudiant_id', $etudiant->id)
            ->where('annee_universitaire', $annee)
            ->whereNotIn('statut', ['rejete'])
            ->first();

        if ($dossierExistant) {
            $erreurs[] = 'Vous avez déjà un dossier pour l\'année ' . $annee . ' (Réf: ' . $dossierExistant->reference . ').';
        }

        // Vérifier si une campagne est ouverte
        if ($campagneId) {
            $campagne = Campagne::find($campagneId);
            if ($campagne && !$campagne->estOuverte()) {
                $erreurs[] = 'La campagne "' . $campagne->nom . '" n\'est pas ouverte.';
            }
        } else {
            $campagneOuverte = Campagne::where('active', true)
                ->where('date_ouverture', '<=', now())
                ->where('date_cloture', '>=', now())
                ->exists();

            if (!$campagneOuverte) {
                $avertissements[] = 'Aucune campagne de bourse n\'est actuellement ouverte.';
            }
        }

        // Vérifier le profil complétude
        if (empty($etudiant->numero_etudiant)) {
            $avertissements[] = 'Votre numéro étudiant n\'est pas renseigné.';
        }

        return [
            'eligible' => empty($erreurs),
            'erreurs' => $erreurs,
            'avertissements' => $avertissements,
        ];
    }

    public function verifierDoublon(int $etudiantId, string $anneeUniversitaire): ?Dossier
    {
        return Dossier::where('etudiant_id', $etudiantId)
            ->where('annee_universitaire', $anneeUniversitaire)
            ->whereNotIn('statut', ['rejete'])
            ->first();
    }

    public function verifierQuotaFiliere(string $filiere, ?int $campagneId = null): array
    {
        $quotaMax = (int) Parametre::get('quota_filiere_' . strtolower($filiere), 0);

        if ($quotaMax === 0) {
            $quotaMax = (int) Parametre::get('quota_filiere_defaut', 50);
        }

        $query = Dossier::where('filiere', $filiere)
            ->where('statut', 'accepte');

        if ($campagneId) {
            $query->where('campagne_id', $campagneId);
        } else {
            $query->whereYear('created_at', now()->year);
        }

        $count = $query->count();

        return [
            'filiere' => $filiere,
            'acceptes' => $count,
            'quota_max' => $quotaMax,
            'places_restantes' => max(0, $quotaMax - $count),
            'quota_atteint' => $count >= $quotaMax,
        ];
    }
}
