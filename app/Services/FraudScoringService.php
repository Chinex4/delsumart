<?php
namespace App\Services;
use App\Models\{FraudFlag,User};
class FraudScoringService {
 public function scoreUser(User $user):array{$score=0;$reasons=[];$v=$user->verification;if(($v?->resubmission_count??0)>=3){$score+=20;$reasons[]='Three or more KYC resubmissions (+20)';}$rapid=$user->listings()->where('created_at','>=',now()->subMinutes(10))->count();if($rapid>=5){$score+=25;$reasons[]='Five or more listings within 10 minutes (+25)';}$disputes=\App\Models\Dispute::whereIn('transaction_id',$user->sales()->pluck('id'))->count();if($disputes>=3){$score+=30;$reasons[]='Three or more seller disputes (+30)';}$score=min($score,100);return ['score'=>$score,'level'=>$score>=60?'high':($score>=30?'medium':'low'),'reasons'=>$reasons];}
 public function evaluate(User $user):array{$r=$this->scoreUser($user);if($r['score']>=30)FraudFlag::firstOrCreate(['related_type'=>'account','related_id'=>$user->id,'status'=>'open'],['flag_reason'=>implode('; ',$r['reasons']),'risk_score'=>$r['score']]);return $r;}
}