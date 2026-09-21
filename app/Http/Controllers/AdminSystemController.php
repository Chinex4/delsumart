<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AdminSystemController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'email_login_otp' => ['required', 'boolean'],
        ]);

        SystemSetting::putBoolean('email_login_otp', (bool) $data['email_login_otp']);

        AuditLog::create([
            'admin_id' => $request->user()->id,
            'action_type' => 'system_setting_updated',
            'target_type' => 'system_setting',
            'target_id' => 0,
            'notes' => 'Email login OTP '.($data['email_login_otp'] ? 'enabled' : 'disabled').'.',
        ]);

        return back()->with('success', 'System settings updated.');
    }

    public function clearCaches(Request $request)
    {
        Artisan::call('optimize:clear');
        Artisan::call('config:clear');

        AuditLog::create([
            'admin_id' => $request->user()->id,
            'action_type' => 'application_cache_cleared',
            'target_type' => 'system',
            'target_id' => 0,
            'notes' => 'Laravel optimization and configuration caches were cleared from the admin dashboard.',
        ]);

        return back()->with('success', 'Application and configuration caches cleared.');
    }
}
