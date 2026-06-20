<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RappelNotification extends Notification
{
    use Queueable;

    public function __construct(public string $titre, public string $contenu, public ?string $lien = null) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->titre)
            ->greeting('Bonjour ' . $notifiable->prenom . ',')
            ->line($this->contenu);

        if ($this->lien) {
            $mail->action('Voir les détails', $this->lien);
        }

        return $mail;
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'rappel',
            'message' => $this->contenu,
            'lien' => $this->lien,
        ];
    }
}
