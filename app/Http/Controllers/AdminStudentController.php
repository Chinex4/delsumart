<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Dispute;
use App\Models\User;
use App\Services\FraudScoringService;
use Illuminate\Http\Request;

class AdminStudentController extends Controller
{
    public function index(Request $r)
    {
        $q = User::where('role', 'student')
            ->with('verification')
            ->withCount(['listings', 'purchases', 'sales']);
        if ($s = $r->string('q')->trim()->value()) {
            $q->where(
                fn ($x) => $x
                    ->where('name', 'like', "%$s%")
                    ->orWhere('email', 'like', "%$s%")
                    ->orWhere('matric_no', 'like', "%$s%"),
            );
        }
        if ($status = $r->string('status')->value()) {
            $q->whereHas(
                'verification',
                fn ($v) => $v->where('verification_status', $status),
            );
        }

        if ($account = $r->string('account_status')->value()) {
            $q->where('account_status', $account);
        }

        return view('admin.students.index', [
            'students' => $q->latest()->paginate(20)->withQueryString(),
        ]);
    }

    public function show(User $student, FraudScoringService $fraud)
    {
        abort_unless($student->role === 'student', 404);
        $student->load([
            'verification',
            'listings' => fn ($q) => $q->latest()->limit(10),
            'purchases' => fn ($q) => $q->with('listing')->latest()->limit(10),
            'sales' => fn ($q) => $q->with('listing')->latest()->limit(10),
        ]);

        return view('admin.students.show', [
            'student' => $student,
            'risk' => $fraud->scoreUser($student),
            'disputes' => Dispute::with('transaction.listing')
                ->whereHas(
                    'transaction',
                    fn ($query) => $query
                        ->where('buyer_id', $student->id)
                        ->orWhere('seller_id', $student->id),
                )
                ->latest()
                ->take(10)
                ->get(),
            'auditLogs' => AuditLog::with('admin')
                ->where('target_type', 'user')
                ->where('target_id', $student->id)
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }

    public function status(Request $r, User $student)
    {
        abort_unless($student->role === 'student', 404);
        $d = $r->validate([
            'account_status' => 'required|in:active,suspended',
            'reason' => 'required|string|max:1000',
        ]);
        $student->update(['account_status' => $d['account_status']]);
        AuditLog::create([
            'admin_id' => $r->user()->id,
            'action_type' => 'student_'.$d['account_status'],
            'target_type' => 'user',
            'target_id' => $student->id,
            'notes' => $d['reason'],
        ]);

        return back()->with(
            'success',
            'Student account status updated and audited.',
        );
    }
}
