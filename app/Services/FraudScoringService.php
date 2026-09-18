<?php

namespace App\Services;

use App\Models\Dispute;
use App\Models\FraudFlag;
use App\Models\Transaction;
use App\Models\User;

class FraudScoringService
{
    public function scoreUser(User $user): array
    {
        $score = 0;
        $reasons = [];
        $add = function (int $points, string $reason) use (&$score, &$reasons) {
            $score += $points;
            $reasons[] = $reason." (+{$points})";
        };
        $v = $user->verification;
        if (($v?->resubmission_count ?? 0) >= 3) {
            $add(20, 'Three or more KYC resubmissions');
        }
        if ($v?->verification_status === 'rejected') {
            $add(10, 'Current KYC submission is rejected');
        }
        $rapid = $user->listings()->where('created_at', '>=', now()->subMinutes(10))->count();
        if ($rapid >= 5) {
            $add(25, 'Five or more listings created within 10 minutes');
        }
        $daily = $user->listings()->where('created_at', '>=', now()->subDay())->count();
        if ($daily >= 12) {
            $add(15, 'Twelve or more listings created within 24 hours');
        }
        $sales = $user->sales()->count();
        $sellerDisputes = Dispute::whereIn('transaction_id', $user->sales()->select('id'))->count();
        if ($sellerDisputes >= 3) {
            $add(30, 'Three or more disputes on seller transactions');
        } elseif ($sellerDisputes >= 1 && $sales >= 1 && ($sellerDisputes / $sales) >= 0.5) {
            $add(20, 'At least half of seller transactions have disputes');
        }
        $cancelled = Transaction::where(fn ($q) => $q->where('buyer_id', $user->id)->orWhere('seller_id', $user->id))->where('status', 'cancelled')->count();
        if ($cancelled >= 3) {
            $add(15, 'Three or more cancelled transactions');
        }
        $highValue = $user->listings()->where('price', '>=', 1000000)->where('created_at', '>=', now()->subDay())->count();
        if ($highValue >= 3) {
            $add(10, 'Three or more high-value listings created within 24 hours');
        }
        $score = min($score, 100);

        return ['score' => $score, 'level' => $score >= 60 ? 'high' : ($score >= 30 ? 'medium' : 'low'), 'reasons' => $reasons];
    }

    public function evaluate(User $user): array
    {
        $r = $this->scoreUser($user);
        if ($r['score'] >= 30) {
            $flag = FraudFlag::firstOrNew(['related_type' => 'account', 'related_id' => $user->id, 'status' => 'open']);
            $flag->fill(['flag_reason' => implode('; ', $r['reasons']), 'risk_score' => $r['score']])->save();
        }

        return $r;
    }
}
