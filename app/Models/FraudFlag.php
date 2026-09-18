<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class FraudFlag extends Model {protected $fillable=['related_type','related_id','flag_reason','risk_score','status','reviewed_by','reviewed_at']; protected function casts():array{return ['reviewed_at'=>'datetime'];}}
