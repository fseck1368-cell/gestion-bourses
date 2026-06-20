<?php

namespace App\Console\Commands;

use App\Models\Dossier;
use App\Models\Rappel;
use App\Notifications\RappelInstruction;
use Illuminate\Console\Command;

class EnvoyerRappels extends Command
{
    protected $signature = 'bourses:rappels {--jours=7 : Nombre de jours sans traitement avant rappel}';
    protected $description = 'Envoyer des rappels aux instructeurs pour les dossiers en attente';

    public function handle(): int
    {
        $jours = (int) $this->option('jours');

        $dossiers = Dossier::where('statut', 'en_cours_instruction')
            ->whereNotNull('instructeur_id')
            ->where('date_instruction', '<=', now()->subDays($jours))
            ->with(['instructeur', 'etudiant'])
            ->get();

        $count = 0;
        foreach ($dossiers as $dossier) {
            $dejaEnvoye = Rappel::where('dossier_id', $dossier->id)
                ->where('type', 'instruction')
                ->where('created_at', '>=', now()->subDays(3))
                ->exists();

            if ($dejaEnvoye) continue;

            $joursAttente = now()->diffInDays($dossier->date_instruction);

            $dossier->instructeur->notify(new RappelInstruction($dossier, $joursAttente));

            Rappel::create([
                'dossier_id' => $dossier->id,
                'destinataire_id' => $dossier->instructeur_id,
                'type' => 'instruction',
                'message' => "Rappel envoyé après $joursAttente jours d'attente",
                'envoye' => true,
                'date_envoi' => now(),
            ]);

            $count++;
        }

        $this->info("$count rappel(s) envoyé(s).");
        return Command::SUCCESS;
    }
}
