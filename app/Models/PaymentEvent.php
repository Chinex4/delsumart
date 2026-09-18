<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class PaymentEvent extends Model {protected $fillable=['event_key','event_type','reference','payload','processed_at']; protected function casts():array{return ['payload'=>'array','processed_at'=>'datetime'];}}
