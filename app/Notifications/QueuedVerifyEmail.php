<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class QueuedVerifyEmail extends VerifyEmail implements ShouldQueue
{
    use Queueable; // adiciona todas as propriedades necessárias, incluindo $connection

    public function via($notifiable)
    {
        return parent::via($notifiable);
    }

    public function toMail($notifiable)
    {
        return parent::toMail($notifiable);
    }
}
