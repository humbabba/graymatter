<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuthCodeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $code
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Login Code')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your login code is:')
            ->line('**' . $this->code . '**')
            ->line('This code expires in 10 minutes.')
            ->line('If you did not request this code, no action is needed.');
    }
}
