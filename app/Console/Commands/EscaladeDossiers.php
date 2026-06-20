<?php

namespace App\Console\Commands;

use App\Models\Alerte;
use App\Models\Parametre;
use App\Models\User;
use App\Services\WorkflowService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EscaladeDossiers extends Command
{
    protected $signature = 'bourses:escalade';
    protected $description = 'Escalader les dossiers en instruction depuis trop longtemps';

    public function handle(WorkflowService $workflowService): int
    {
        $delai = (int) Parametre::get('delai_escalade', 15);

        $dossiers = $workflowService->dossiersAEscalader($delai);

        if ($dossiers->isEmpty()) {
            $this->info('Aucun dossier à escalader.');
            return Command::SUCCESS;
        }

        $admins = User::where('role', 'administrateur')->where('actif', true)->get();

        $count = 0;
        foreach ($dossiers as $dossier) {
            $joursAttente = now()->diffInDays($dossier->date_instruction);

            foreach ($admins as $admin) {
                Alerte::create([
                    'user_id' => $admin->id,
                    'type' => 'escalade',
                    'titre' => 'Dossier en retard d\'instruction',
                    'message' => "Le dossier {$dossier->reference} est en instruction depuis {$joursAttente} jours (instructeur: " . ($dossier->instructeur ? $dossier->instructeur->name : 'non assigné') . ").",
                    'niveau' => 'warning',
                    'lien' => '/dossiers/' . $dossier->id,
                    'lue' => false,
                ]);
            }

            Log::info('Escalade dossier', [
                'dossier_id' => $dossier->id,
                'reference' => $dossier->reference,
                'jours_attente' => $joursAttente,
                'instructeur_id' => $dossier->instructeur_id,
            ]);

            $count++;
        }

        $this->info("{$count} dossier(s) escaladé(s) auprès de " . $admins->count() . " administrateur(s).");

        return Command::SUCCESS;
    }
}
