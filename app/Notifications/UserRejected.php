<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UserRejected extends Notification
{
    use Queueable;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Votre inscription a été rejetée')
            ->line('Bonjour ' . $this->user->name . ',')
            ->line('Nous sommes désolés de vous informer que votre inscription a été rejetée.')
            ->line('Si vous avez des questions, n\'hésitez pas à nous contacter.')
            ->line('Merci pour votre compréhension.');
    }

    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'status' => 'rejected',
        ];
    }
}
