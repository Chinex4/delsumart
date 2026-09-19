<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = ['user_id','bank_code','bank_name','account_number','account_name','paystack_recipient_code','verified_at'];

    protected function casts(): array
    {
        return ['verified_at' => 'datetime'];
    }

    public function user() { return $this->belongsTo(User::class); }
}
