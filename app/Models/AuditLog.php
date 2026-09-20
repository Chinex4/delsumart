<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $updated_at = false;

    protected $fillable = [
        'admin_id',
        'action_type',
        'target_type',
        'target_id',
        'notes',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
