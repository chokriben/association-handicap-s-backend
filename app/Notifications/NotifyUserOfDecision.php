<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifyUserOfDecision extends Notification implements ShouldQueue
{
    use Queueable;

    public $decision;

    /**
     * Create a new notification instance.
     *
     * @param string $decision
     */
    public function __construct($decision)
    {
        $this->decision = $decision;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $message = $this->decision === 'approved'
            ? 'Your membership has been accepted.'
            : 'Your membership has been rejected.';

        return (new MailMessage)
            ->subject('Membership Decision')
            ->line($message)
            ->action('View Your Profile', url('/profile')) // Adjust the link as needed
            ->line('Thank you for your registration!');
    }
}
