<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewChatMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $senderName,
        private readonly string $preview,
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
            ->subject('Новое сообщение в чате')
            ->greeting('Здравствуйте!')
            ->line("Новое сообщение от {$this->senderName}: {$this->preview}")
            ->action('Перейти в чат', $this->chatUrl)
            ->line('Это автоматическое уведомление платформы.');
    }
}