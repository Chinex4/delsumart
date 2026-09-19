<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutOtpNotification extends Notification
{
    use Queueable;

    public function __construct(public string $code)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Confirm your DelsuMart bank account')
            ->greeting('Bank account security check')
            ->line('Use this one-time code to continue setting or changing your payout bank account:')
            ->line($this->code)
            ->line('This code expires in 10 minutes. Do not share it with anyone.');
    }
}
