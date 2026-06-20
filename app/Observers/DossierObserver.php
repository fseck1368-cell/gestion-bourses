<?php

namespace App\Observers;

use App\Models\Dossier;
use App\Models\Alerte;
use App\Notifications\DossierStatutChange;

class DossierObserver
{
    public function updating(Dossier $dossier): void
    {
        if ($dossier->isDirty('statut')) {
            $oldStatut = $dossier->getOriginal('statut');
            $newStatut = $dossier->statut;

            $dossier->etudiant->notify(new DossierStatutChange($dossier, $oldStatut, $newStatut));

            if ($newStatut === 'en_cours_instruction' && $dossier->isDirty('instructeur_id')) {
                Alerte::create([
                    'user_id' => $dossier->etudiant_id,
                    'type' => 'statut_change',
                    'titre' => 'Dossier en cours d\'instruction',
                    'message' => 'Votre dossier ' . $dossier->reference . ' est maintenant en cours d\'instruction.',
                    'niveau' => 'info',
                    'lien' => '/etudiant/dossiers/' . $dossier->id,
                ]);
            }

            if (in_array($newStatut, ['accepte', 'rejete'])) {
                Alerte::create([
                    'user_id' => $dossier->etudiant_id,
                    'type' => 'decision',
                    'titre' => $newStatut === 'accepte' ? 'Dossier accepté !' : 'Dossier rejeté',
                    'message' => 'Une décision a été prise sur votre dossier ' . $dossier->reference . '.',
                    'niveau' => $newStatut === 'accepte' ? 'success' : 'danger',
                    'lien' => '/etudiant/dossiers/' . $dossier->id,
                ]);
            }
        }

        if ($dossier->isDirty('complement_requis') && $dossier->complement_requis) {
            Alerte::create([
                'user_id' => $dossier->etudiant_id,
                'type' => 'complement',
                'titre' => 'Complément requis',
                'message' => 'Un complément d\'information est demandé pour votre dossier ' . $dossier->reference . '.',
                'niveau' => 'warning',
                'lien' => '/etudiant/dossiers/' . $dossier->id,
            ]);
        }
    }
}
