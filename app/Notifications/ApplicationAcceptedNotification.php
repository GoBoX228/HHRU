<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationAcceptedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $vacancyTitle,
        private readonly string $chatUrl,
    ) {
    }

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ваш отклик принят')
            ->greeting('Поздравляем!')
            ->line("Ваш отклик на вакансию «{$this->vacancyTitle}» был принят работодателем.")
            ->action('Открыть чат по проекту', $this->chatUrl)
            ->line('Это автоматическое уведомление платформы.');
    }
}