<?php
namespace App\Notifications;
use App\Models\Transaction; use Illuminate\Notifications\Messages\MailMessage; use Illuminate\Notifications\Notification;
class PaymentReceivedNotification extends Notification {
 public function __construct(private Transaction $transaction,private string $audience){}
 public function via(object $notifiable):array{return ['mail'];}
 public function toMail(object $notifiable):MailMessage{$text=$this->audience==='buyer'?'Your payment was verified successfully.':'Payment for your listing has been verified.';return (new MailMessage)->subject('DelsuMart protected payment')->line($text)->line('Transaction #'.$this->transaction->id.' is now in protected hold.')->line('Funds are not represented as a generic Paystack escrow account; DelsuMart controls the internal protected transaction state.');}
}