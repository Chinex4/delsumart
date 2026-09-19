<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayoutRequest extends Model
{
    protected $fillable = ['user_id','bank_account_id','amount','status','reference','admin_note','processed_by','processed_at'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'processed_at' => 'datetime'];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function bankAccount() { return $this->belongsTo(BankAccount::class); }
    public function processor() { return $this->belongsTo(User::class, 'processed_by'); }
}
