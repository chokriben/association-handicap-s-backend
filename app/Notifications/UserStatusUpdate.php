<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserStatusUpdate extends Notification
{
    use Queueable;

    protected $user;
    protected $status;

    public function __construct($user, $status)
    {
        $this->user = $user;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Mise à jour de votre statut d\'enregistrement')
            ->line('Votre enregistrement a été ' . $this->status . '.')
            ->line('Nom: ' . $this->user->name)
            ->line('Email: ' . $this->user->email)
            ->line('Merci d\'utiliser notre application!');
    }

    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'status' => $this->status,
        ];
    }
}
