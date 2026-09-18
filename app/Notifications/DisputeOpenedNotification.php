<?php
namespace App\Notifications;
use App\Models\Dispute; use Illuminate\Notifications\Messages\MailMessage; use Illuminate\Notifications\Notification;
class DisputeOpenedNotification extends Notification {
 public function __construct(private Dispute $dispute){}
 public function via(object $notifiable):array{return ['mail'];}
 public function toMail(object $notifiable):MailMessage{return (new MailMessage)->subject('DelsuMart dispute opened')->line('A dispute has been opened for transaction #'.$this->dispute->transaction_id.'.')->line('Protected release is paused while the dispute is reviewed.');}
}