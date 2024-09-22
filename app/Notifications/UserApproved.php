<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UserApproved extends Notification
{
    use Queueable;

    protected $user;
    protected $password;
    protected $superAdminName;

    public function __construct($user, $password, $superAdminName)
    {
        $this->user = $user;
        $this->password = $password;  // Le mot de passe de l'utilisateur
        $this->superAdminName = $superAdminName;  // Le nom du super administrateur
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Votre inscription a été acceptée')
            ->line('Bonjour ' . $this->user->name . ',')
            ->line('Votre inscription a été acceptée avec succès !')
            ->line('Voici vos informations de connexion :')
            ->line('**Email :** ' . $this->user->email)
            ->line('**Mot de passe :** ' . $this->password)  // Envoyer le mot de passe (assurez-vous de le sécuriser)
            ->line('Si vous avez des questions, n\'hésitez pas à nous contacter.')
            ->line('Merci pour votre compréhension.')
            ->line('---')
            ->line('Cordialement,')
            ->line($this->superAdminName);  // Le nom du super administrateur
    }

    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'status' => 'approved',
        ];
    }
}
