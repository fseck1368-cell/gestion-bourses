<?php

namespace App\Notifications;

use App\Models\Dossier;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RappelInstruction extends Notification
{
    use Queueable;

    public function __construct(private Dossier $dossier, private int $joursAttente) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Rappel : Dossier en attente d\'instruction')
            ->greeting('Bonjour ' . $notifiable->prenom . ',')
            ->line('Le dossier **' . $this->dossier->reference . '** vous est assigné depuis ' . $this->joursAttente . ' jours.')
            ->line('Étudiant : ' . $this->dossier->etudiant->prenom . ' ' . $this->dossier->etudiant->nom)
            ->action('Examiner le dossier', url('/instructeur/dossiers/' . $this->dossier->id))
            ->line('Merci de traiter ce dossier dans les meilleurs délais.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'dossier_id' => $this->dossier->id,
            'reference' => $this->dossier->reference,
            'jours_attente' => $this->joursAttente,
            'message' => 'Rappel : le dossier ' . $this->dossier->reference . ' attend votre instruction depuis ' . $this->joursAttente . ' jours.',
        ];
    }
}
