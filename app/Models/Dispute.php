<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class Dispute extends Model {protected $fillable=['transaction_id','complainant_id','category','details','evidence_path','status','resolution','resolved_by','resolved_at']; protected function casts():array{return ['resolved_at'=>'datetime'];} public function transaction(){return $this->belongsTo(Transaction::class);} public function complainant(){return $this->belongsTo(User::class,'complainant_id');}}
