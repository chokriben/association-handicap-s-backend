<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotifySuperAdmin extends Notification
{
    use Queueable;

    protected $newUser;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($newUser)
    {
        $this->newUser = $newUser;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail']; // Add more channels if needed like 'database', 'slack'
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $approveUrl = url('/api/approve-registration/' . $this->newUser->id);
        $rejectUrl = url('/api/reject-registration/' . $this->newUser->id);

        return (new MailMessage)
                    ->subject('New User Registration')
                    ->line('A new user has registered and is awaiting approval.')
                    ->line('Name: ' . $this->newUser->name)
                    ->line('Email: ' . $this->newUser->email)
                    ->line('Role: ' . $this->newUser->role)
                    ->action('Approve User', $approveUrl)   // Approve action
                    ->action('Reject User', $rejectUrl)     // Reject action
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->newUser->id,
            'name' => $this->newUser->name,
            'email' => $this->newUser->email,
        ];
    }
}
