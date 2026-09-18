<?php
namespace App\Http\Controllers;
use Illuminate\Auth\Events\PasswordReset; use Illuminate\Http\Request; use Illuminate\Support\Facades\{Hash,Password}; use Illuminate\Support\Str; use Illuminate\Validation\Rules\Password as PasswordRule;
class PasswordResetController extends Controller {
 public function requestForm(){return view('auth.forgot-password');}
 public function email(Request $r){$r->validate(['email'=>'required|email']);$status=Password::sendResetLink($r->only('email'));return $status===Password::RESET_LINK_SENT?back()->with('success',__($status)):back()->withErrors(['email'=>__($status)]);}
 public function resetForm(Request $r,string $token){return view('auth.reset-password',['token'=>$token,'email'=>$r->string('email')->value()]);}
 public function reset(Request $r){$d=$r->validate(['token'=>'required','email'=>'required|email','password'=>['required','confirmed',PasswordRule::min(8)->mixedCase()->numbers()]]);$status=Password::reset($d,function($user,$password){$user->forceFill(['password'=>Hash::make($password),'remember_token'=>Str::random(60)])->save();event(new PasswordReset($user));});return $status===Password::PASSWORD_RESET?redirect()->route('login')->with('success',__($status)):back()->withErrors(['email'=>__($status)]);}
}