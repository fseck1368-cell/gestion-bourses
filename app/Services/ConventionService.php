<?php

namespace App\Services;

use App\Models\Convention;
use App\Models\Paiement;
use App\Models\RapportAcademique;

class ConventionService
{
    public function genererEcheancier(Convention $convention): array
    {
        $paiementsCrees = [];

        for ($i = 0; $i < $convention->duree_mois; $i++) {
            $datePrevue = $convention->date_debut->copy()->addMonths($i);

            $existe = Paiement::where('dossier_id', $convention->dossier_id)
                ->where('etudiant_id', $convention->etudiant_id)
                ->where('periode', $datePrevue->format('m/Y'))
                ->exists();

            if ($existe) continue;

            $paiement = Paiement::create([
                'dossier_id' => $convention->dossier_id,
                'etudiant_id' => $convention->etudiant_id,
                'montant' => $convention->montant_mensuel,
                'statut' => 'en_attente',
                'mode_paiement' => 'virement',
                'date_prevue' => $datePrevue,
                'periode' => $datePrevue->format('m/Y'),
                'commentaire' => 'Paiement mensuel - Convention ' . $convention->reference,
            ]);

            $paiement->genererReference();
            $paiement->save();

            $paiementsCrees[] = $paiement;
        }

        return $paiementsCrees;
    }

    public function verifierSuspensionAutomatique(Convention $convention): bool
    {
        $dernierRapport = RapportAcademique::where('convention_id', $convention->id)
            ->latest()
            ->first();

        if (!$dernierRapport) return false;

        if ($dernierRapport->statut_academique === 'insuffisant' || $dernierRapport->statut_academique === 'exclus') {
            $convention->update(['statut' => 'suspendue']);

            \App\Models\Alerte::create([
                'user_id' => $convention->etudiant_id,
                'type' => 'convention_suspendue',
                'titre' => 'Convention suspendue',
                'message' => 'Votre convention ' . $convention->reference . ' a été suspendue en raison de résultats académiques insuffisants.',
                'niveau' => 'danger',
                'lien' => '/etudiant/dossiers/' . $convention->dossier_id,
            ]);

            \App\Models\Historique::create([
                'dossier_id' => $convention->dossier_id,
                'user_id' => null,
                'action' => 'Suspension automatique de convention - Résultats insuffisants',
                'commentaire' => 'Statut académique: ' . $dernierRapport->statut_academique . ', Moyenne: ' . $dernierRapport->moyenne,
            ]);

            return true;
        }

        return false;
    }
}
