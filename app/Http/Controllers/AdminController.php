<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Dispute;
use App\Models\FraudFlag;
use App\Models\Listing;
use App\Models\SystemSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Verification;
use App\Notifications\KycStatusNotification;
use App\Services\FraudScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'emailLoginOtpEnabled' => SystemSetting::boolean('email_login_otp', true),
            'stats' => [
                'students' => User::where('role', 'student')->count(),
                'verified' => Verification::where(
                    'verification_status',
                    'verified',
                )->count(),
                'pending' => Verification::where(
                    'verification_status',
                    'pending',
                )->count(),
                'rejected' => Verification::where(
                    'verification_status',
                    'rejected',
                )->count(),
                'listings' => Listing::where('status', 'active')->count(),
                'held' => Transaction::where('status', 'paid_held')->count(),
                'disputes' => Dispute::whereIn('status', [
                    'open',
                    'under_review',
                ])->count(),
                'flags' => FraudFlag::where('status', 'open')->count(),
            ],
            'recentTransactions' => Transaction::with([
                'listing',
                'buyer',
                'seller',
            ])
                ->latest()
                ->take(5)
                ->get(),
            'recentAudits' => AuditLog::with('admin')->latest()->take(5)->get(),
            'pending' => Verification::with('user')
                ->where('verification_status', 'pending')
                ->latest()
                ->take(8)
                ->get(),
        ]);
    }

    public function verifications(Request $request)
    {
        return view('admin.verifications', [
            'verifications' => Verification::with('user')
                ->when(
                    $request->filled('status'),
                    fn ($query) => $query->where(
                        'verification_status',
                        $request->string('status')->value(),
                    ),
                )
                ->latest()
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function verification(Verification $verification)
    {
        $verification->load(['user', 'reviewer']);

        $history = AuditLog::with('admin')
            ->where('target_type', 'verification')
            ->where('target_id', $verification->id)
            ->latest()
            ->get();

        return view(
            'admin.verifications.show',
            compact('verification', 'history'),
        );
    }

    public function decide(
        Request $r,
        Verification $verification,
        FraudScoringService $fraud,
    ) {
        $d = $r->validate([
            'decision' => 'required|in:verified,rejected',
            'reason' => 'nullable|required_if:decision,rejected|string|max:1000',
        ]);
        DB::transaction(function () use ($r, $verification, $d) {
            $verification->update([
                'verification_status' => $d['decision'],
                'verified_by' => $r->user()->id,
                'verified_at' => now(),
                'rejection_reason' => $d['decision'] === 'rejected' ? $d['reason'] : null,
            ]);
            AuditLog::create([
                'admin_id' => $r->user()->id,
                'action_type' => 'kyc_'.$d['decision'],
                'target_type' => 'verification',
                'target_id' => $verification->id,
                'notes' => $d['reason'] ?? 'Student identity documents reviewed.',
            ]);
        });
        $verification->user->notify(
            new KycStatusNotification($d['decision'], $d['reason'] ?? null),
        );
        $fraud->evaluate($verification->user->fresh());

        return back()->with('success', 'Verification decision recorded.');
    }

    public function flags(Request $r)
    {
        $q = FraudFlag::query();
        if ($status = $r->string('status')->value()) {
            $q->where('status', $status);
        }
        if ($level = $r->string('risk')->value()) {
            $level === 'high'
                ? $q->where('risk_score', '>=', 60)
                : ($level === 'medium'
                    ? $q->whereBetween('risk_score', [30, 59])
                    : ($level === 'low'
                        ? $q->where('risk_score', '<', 30)
                        : null));
        }

        $flags = $q->latest()->paginate(20)->withQueryString();
        $accounts = User::whereIn(
            'id',
            $flags->where('related_type', 'user')->pluck('related_id'),
        )
            ->get()
            ->keyBy('id');

        return view('admin.flags', compact('flags', 'accounts'));
    }

    public function reviewFlag(Request $r, FraudFlag $flag)
    {
        $d = $r->validate(['status' => 'required|in:reviewed,dismissed']);
        $flag->update([
            'status' => $d['status'],
            'reviewed_by' => $r->user()->id,
            'reviewed_at' => now(),
        ]);
        AuditLog::create([
            'admin_id' => $r->user()->id,
            'action_type' => 'fraud_'.$d['status'],
            'target_type' => 'fraud_flag',
            'target_id' => $flag->id,
            'notes' => 'Risk score '.$flag->risk_score.': '.$flag->flag_reason,
        ]);

        return back()->with('success', 'Fraud flag updated.');
    }

    public function audits(Request $r)
    {
        $q = AuditLog::with('admin');
        if ($action = $r->string('action')->trim()->value()) {
            $q->where('action_type', 'like', "%$action%");
        }

        return view('admin.audits', [
            'logs' => $q->latest()->paginate(30)->withQueryString(),
        ]);
    }
}
