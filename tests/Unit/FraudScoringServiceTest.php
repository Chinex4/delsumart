<?php
namespace Tests\Unit;
use App\Models\User; use App\Services\FraudScoringService; use Illuminate\Foundation\Testing\RefreshDatabase; use Illuminate\Support\Facades\Hash; use Tests\TestCase;
class FraudScoringServiceTest extends TestCase {use RefreshDatabase; public function test_normal_account_starts_low_risk():void{$u=User::create(['name'=>'Normal','matric_no'=>'N/1','email'=>'n@example.test','programme'=>'CS','level'=>'100','password'=>Hash::make('Password123!')]);$r=app(FraudScoringService::class)->scoreUser($u);$this->assertSame('low',$r['level']);$this->assertSame(0,$r['score']);}}
