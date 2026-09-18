<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\MfaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $r)
    {
        $d = $r->validate(['name' => 'required|string|max:100', 'matric_no' => 'required|string|max:30|unique:users,matric_no', 'email' => 'required|email|max:255|unique:users,email', 'programme' => 'required|string|max:100', 'level' => 'required|string|max:20', 'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()]]);
        $d['matric_no'] = strtoupper(preg_replace('/\s+/', '', trim($d['matric_no'])));
        $u = User::create($d);
        Auth::login($u);
        $r->session()->regenerate();

        return redirect()->route('kyc.show');
    }

    public function login(Request $r, MfaService $mfa)
    {
        $d = $r->validate(['login' => 'required|string', 'password' => 'required|string']);
        $field = filter_var($d['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'matric_no';
        $value = $field === 'matric_no' ? strtoupper(preg_replace('/\s+/', '', trim($d['login']))) : strtolower(trim($d['login']));
        $u = User::where($field, $value)->first();
        if (! $u || ! Hash::check($d['password'], $u->password)) {
            return back()->withErrors(['login' => 'The supplied credentials are invalid.']);
        }
        $mfa->issue($u);
        $r->session()->put('mfa_user_id', $u->id);

        return redirect()->route('mfa.show');
    }

    public function verifyMfa(Request $r, MfaService $mfa)
    {
        $d = $r->validate(['code' => 'required|digits:6']);
        $u = User::findOrFail($r->session()->get('mfa_user_id'));
        if (! $mfa->verify($u, $d['code'])) {
            return back()->withErrors(['code' => 'The code is invalid, expired, or has exceeded its attempt limit.']);
        }
        Auth::login($u);
        $r->session()->forget('mfa_user_id');
        $r->session()->regenerate();

        return redirect()->intended($u->isAdmin() ? route('admin.dashboard') : route('dashboard'));
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();

        return redirect('/');
    }
}
