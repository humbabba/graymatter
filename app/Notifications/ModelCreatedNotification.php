<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ModelCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Model $model,
        public string $label,
        public string $url,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = $this->model->name ?? $this->model->label ?? "#{$this->model->id}";

        return (new MailMessage)
            ->subject("New {$this->label} created: {$name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new {$this->label} has been created: {$name}")
            ->action("View {$this->label}", url($this->url));
    }
}
