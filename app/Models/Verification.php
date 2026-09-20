<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    protected $fillable = [
        'user_id',
        'matric_no',
        'full_name',
        'programme',
        'level',
        'id_card_image',
        'fee_receipt_image',
        'verification_status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'resubmission_count',
    ];

    protected function casts(): array
    {
        return ['verified_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
