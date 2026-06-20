<?php

namespace App\Notifications;

use App\Models\Paiement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaiementVerseNotification extends Notification
{
    use Queueable;

    public function __construct(public Paiement $paiement) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Paiement de bourse effectué - ' . $this->paiement->reference)
            ->greeting('Bonjour ' . $notifiable->prenom . ',')
            ->line('Un paiement de bourse a été effectué en votre faveur.')
            ->line('Montant : ' . number_format($this->paiement->montant, 2, ',', ' ') . ' DH')
            ->line('Référence : ' . $this->paiement->reference)
            ->line('Mode : ' . ucfirst($this->paiement->mode_paiement))
            ->line('Date : ' . $this->paiement->date_versement?->format('d/m/Y'))
            ->action('Voir mes paiements', url('/dashboard'));
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'paiement_verse',
            'paiement_id' => $this->paiement->id,
            'message' => 'Paiement de ' . number_format($this->paiement->montant, 0, ',', ' ') . ' DH effectué (' . $this->paiement->reference . ').',
        ];
    }
}
