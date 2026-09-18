<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable {
 use HasFactory,Notifiable;
 protected $fillable=['name','matric_no','email','programme','level','password','role','account_status'];
 protected $hidden=['password','remember_token'];
 protected function casts():array{return ['password'=>'hashed'];}
 public function verification(){return $this->hasOne(Verification::class);}
 public function listings(){return $this->hasMany(Listing::class);}
 public function purchases(){return $this->hasMany(Transaction::class,'buyer_id');}
 public function sales(){return $this->hasMany(Transaction::class,'seller_id');}
 public function isAdmin():bool{return $this->role==='admin';}
 public function isVerifiedStudent():bool{return $this->verification?->verification_status==='verified' && $this->account_status==='active';}
}