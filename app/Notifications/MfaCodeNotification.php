<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MfaCodeNotification extends Notification
{
    use Queueable;

    public function __construct(private string $code) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->subject('Your DelsuMart security code')->greeting('Confirm your sign in')->line('Use this one-time code to finish signing in:')->line($this->code)->line('This code expires in 10 minutes. If you did not sign in, ignore this email.');
    }
}
