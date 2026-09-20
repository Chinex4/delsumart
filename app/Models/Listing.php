<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'price',
        'status',
    ];

    protected function casts(): array
    {
        return ['price' => 'decimal:2'];
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images()
    {
        return $this->hasMany(ListingImage::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
