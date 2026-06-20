<?php

namespace App\Console\Commands;

use App\Models\Dossier;
use App\Models\Historique;
use App\Notifications\RappelNotification;
use App\Services\WorkflowService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RotationAssignation extends Command
{
    protected $signature = 'bourses:rotation';
    protected $description = 'Assigner automatiquement les dossiers soumis aux instructeurs par rotation';

    public function handle(WorkflowService $workflowService): int
    {
        $dossiers = Dossier::where('statut', 'soumis')
            ->whereNull('instructeur_id')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($dossiers->isEmpty()) {
            $this->info('Aucun dossier à assigner.');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($dossiers as $dossier) {
            $instructeur = $workflowService->instructeurOptimal();

            if (!$instructeur) {
                $this->warn('Aucun instructeur actif disponible.');
                break;
            }

            $ancienStatut = $dossier->statut;

            $dossier->update([
                'instructeur_id' => $instructeur->id,
                'statut' => 'en_cours_instruction',
                'date_instruction' => now(),
            ]);

            Historique::create([
                'dossier_id' => $dossier->id,
                'user_id' => null,
                'action' => 'assignation_automatique',
                'ancien_statut' => $ancienStatut,
                'nouveau_statut' => 'en_cours_instruction',
                'commentaire' => "Assignation automatique à {$instructeur->name} par rotation.",
            ]);

            $instructeur->notify(new RappelNotification(
                'Nouveau dossier assigné',
                "Le dossier {$dossier->reference} vous a été assigné automatiquement pour instruction.",
                '/dossiers/' . $dossier->id
            ));

            Log::info('Assignation automatique par rotation', [
                'dossier_id' => $dossier->id,
                'reference' => $dossier->reference,
                'instructeur_id' => $instructeur->id,
                'instructeur_nom' => $instructeur->name,
            ]);

            $count++;
        }

        $this->info("{$count} dossier(s) assigné(s) par rotation.");

        return Command::SUCCESS;
    }
}
