<?php

namespace App\Services;

use App\Models\MfaCode;
use App\Models\User;
use App\Notifications\MfaCodeNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MfaService
{
    public function issue(User $user): void
    {
        $latest = MfaCode::where('user_id', $user->id)->latest()->first();
        if ($latest?->last_sent_at?->gt(now()->subMinute())) {
            throw ValidationException::withMessages(['code' => 'Please wait before requesting another code.']);
        }$code = (string) random_int(100000, 999999);
        MfaCode::where('user_id', $user->id)->whereNull('used_at')->update(['used_at' => now()]);
        MfaCode::create(['user_id' => $user->id, 'code_hash' => Hash::make($code), 'expires_at' => now()->addMinutes(10), 'last_sent_at' => now()]);
        $user->notify(new MfaCodeNotification($code));
    }

    public function verify(User $user, string $code): bool
    {
        $record = MfaCode::where('user_id', $user->id)->whereNull('used_at')->latest()->first();
        if (! $record || $record->expires_at->isPast() || $record->attempts >= 5) {
            return false;
        }$record->increment('attempts');
        if (! Hash::check($code, $record->code_hash)) {
            return false;
        }$record->update(['used_at' => now()]);

        return true;
    }
}
