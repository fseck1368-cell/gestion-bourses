<?php

namespace App\Notifications;

use App\Models\Dossier;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DossierStatutChange extends Notification
{
    use Queueable;

    public function __construct(
        private Dossier $dossier,
        private string $ancienStatut,
        private string $nouveauStatut
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Mise à jour de votre dossier ' . $this->dossier->reference)
            ->greeting('Bonjour ' . $notifiable->prenom . ' ' . $notifiable->nom . ',')
            ->line('Votre dossier de demande de bourse **' . $this->dossier->reference . '** a changé de statut.')
            ->line('Nouveau statut : **' . $this->dossier->statut_label . '**');

        if ($this->nouveauStatut === 'accepte') {
            $message->line('Félicitations ! Votre demande de bourse a été acceptée.');
        } elseif ($this->nouveauStatut === 'rejete') {
            $message->line('Nous sommes désolés, votre demande a été rejetée.');
            if ($this->dossier->commentaire_instructeur) {
                $message->line('Motif : ' . $this->dossier->commentaire_instructeur);
            }
        } elseif ($this->nouveauStatut === 'en_cours_instruction') {
            $message->line('Votre dossier est maintenant en cours d\'examen par un instructeur.');
        }

        return $message->action('Voir mon dossier', url('/etudiant/dossiers/' . $this->dossier->id))
                       ->line('Merci de votre confiance.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'dossier_id' => $this->dossier->id,
            'reference' => $this->dossier->reference,
            'ancien_statut' => $this->ancienStatut,
            'nouveau_statut' => $this->nouveauStatut,
            'message' => 'Votre dossier ' . $this->dossier->reference . ' est passé au statut : ' . $this->dossier->statut_label,
        ];
    }
}
