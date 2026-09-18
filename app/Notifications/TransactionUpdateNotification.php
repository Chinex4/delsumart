<?php
namespace App\Notifications;
use App\Models\Transaction; use Illuminate\Bus\Queueable; use Illuminate\Notifications\Messages\MailMessage; use Illuminate\Notifications\Notification;
class TransactionUpdateNotification extends Notification {use Queueable; public function __construct(private Transaction $transaction,private string $message){} public function via(object $n):array{return ['mail'];} public function toMail(object $n):MailMessage{return (new MailMessage)->subject('DelsuMart transaction update')->line($this->message)->line('Transaction: #'.$this->transaction->id)->line('Status: '.str_replace('_',' ',strtoupper($this->transaction->status)));}}
