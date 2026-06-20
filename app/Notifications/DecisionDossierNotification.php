<?php

namespace App\Notifications;

use App\Models\Dossier;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DecisionDossierNotification extends Notification
{
    use Queueable;

    public function __construct(public Dossier $dossier) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $accepte = $this->dossier->statut === 'accepte';

        $mail = (new MailMessage)
            ->subject('Décision sur votre dossier - ' . $this->dossier->reference)
            ->greeting('Bonjour ' . $notifiable->prenom . ',');

        if ($accepte) {
            $mail->line('Nous avons le plaisir de vous informer que votre demande de bourse a été ACCEPTÉE.')
                ->line('Référence : ' . $this->dossier->reference)
                ->line('Vous serez contacté pour les prochaines étapes (convention, paiement).');
        } else {
            $mail->line('Nous vous informons que votre demande de bourse a été REJETÉE.')
                ->line('Référence : ' . $this->dossier->reference);
            if ($this->dossier->commentaire_admin) {
                $mail->line('Motif : ' . $this->dossier->commentaire_admin);
            }
            $mail->line('Vous disposez d\'un délai pour soumettre un recours si vous le souhaitez.');
        }

        return $mail->action('Voir mon dossier', url('/etudiant/dossiers/' . $this->dossier->id));
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'decision_dossier',
            'dossier_id' => $this->dossier->id,
            'statut' => $this->dossier->statut,
            'message' => 'Votre dossier ' . $this->dossier->reference . ' a été ' . ($this->dossier->statut === 'accepte' ? 'accepté' : 'rejeté') . '.',
        ];
    }
}
