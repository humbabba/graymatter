<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserRegisteredNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $newUser
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New user registered: ' . $this->newUser->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->newUser->name . ' (' . $this->newUser->email . ') has registered an account.')
            ->action('View user', url('/users/' . $this->newUser->id));
    }
}
