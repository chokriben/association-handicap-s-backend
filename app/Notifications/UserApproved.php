<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Property; // Import the Property model

class UserApproved extends Notification
{
    use Queueable;

    // Constructor with Property and data parameters
    public function __construct()
    {
        //
    }

    // Define the channels the notification will be sent through
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    // Build the mail representation of the notification
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Votre compte a été approuvé')
                    ->greeting('Bonjour ' . $notifiable->name . ',')
                    ->line('Nous sommes heureux de vous informer que votre compte a été approuvé.')
                    ->action('Connectez-vous', url('/login'))
                    ->line('Merci de faire partie de notre communauté!');
    }

    // Convert the notification to an array format (optional)
    public function toArray(object $notifiable): array
    {
        return [
            // You can include any relevant data here
        ];
    }
}
