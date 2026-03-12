<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $vacancyTitle,
        private readonly string $freelancerName,
        private readonly string $applicationsUrl,
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
            ->subject('Новый отклик на вакансию')
            ->greeting('Здравствуйте!')
            ->line("На вакансию «{$this->vacancyTitle}» пришел новый отклик от {$this->freelancerName}.")
            ->action('Открыть отклики', $this->applicationsUrl)
            ->line('Это автоматическое уведомление платформы.');
    }
}