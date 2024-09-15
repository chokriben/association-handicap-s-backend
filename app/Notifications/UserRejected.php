<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRejected extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Votre compte a été rejeté')
                    ->greeting('Bonjour ' . $notifiable->name . ',')
                    ->line('Nous regrettons de vous informer que votre inscription a été rejetée.')
                    ->line('Pour plus d\'informations, veuillez nous contacter.');
    }
}
