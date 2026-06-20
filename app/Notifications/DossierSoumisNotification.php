<?php

namespace App\Notifications;

use App\Models\Dossier;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DossierSoumisNotification extends Notification
{
    use Queueable;

    public function __construct(public Dossier $dossier) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Dossier de bourse soumis - ' . $this->dossier->reference)
            ->greeting('Bonjour ' . $notifiable->prenom . ',')
            ->line('Votre demande de bourse a été soumise avec succès.')
            ->line('Référence : ' . $this->dossier->reference)
            ->line('Filière : ' . $this->dossier->filiere)
            ->action('Voir mon dossier', url('/etudiant/dossiers/' . $this->dossier->id))
            ->line('Vous serez notifié de l\'avancement de votre dossier.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'dossier_soumis',
            'dossier_id' => $this->dossier->id,
            'message' => 'Votre dossier ' . $this->dossier->reference . ' a été soumis avec succès.',
        ];
    }
}
