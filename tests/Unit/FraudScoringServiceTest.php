<?php

namespace Tests\Unit;

use App\Models\Listing;
use App\Models\User;
use App\Services\FraudScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FraudScoringServiceTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $suffix = '1'): User
    {
        return User::create([
            'name' => 'Student '.$suffix,
            'matric_no' => 'N/'.$suffix,
            'email' => 'n'.$suffix.'@example.test',
            'programme' => 'CS',
            'level' => '100',
            'password' => Hash::make('Password123!'),
            'account_status' => 'active',
        ]);
    }

    public function test_normal_account_starts_low_risk(): void
    {
        $r = app(FraudScoringService::class)->scoreUser($this->user());
        $this->assertSame('low', $r['level']);
        $this->assertSame(0, $r['score']);
    }

    public function test_rapid_listing_rule_is_explainable_and_creates_flag(): void
    {
        $u = $this->user();
        for ($i = 0; $i < 5; $i++) {
            Listing::create([
                'user_id' => $u->id,
                'title' => 'Item '.$i,
                'description' => 'Test',
                'category' => 'Other',
                'price' => 1000 + $i,
                'status' => 'active',
            ]);
        }
        $r = app(FraudScoringService::class)->evaluate($u);
        $this->assertSame(25, $r['score']);
        $this->assertSame('low', $r['level']);
        $this->assertDatabaseCount('fraud_flags', 0);
        for ($i = 5; $i < 12; $i++) {
            Listing::create([
                'user_id' => $u->id,
                'title' => 'Item '.$i,
                'description' => 'Test',
                'category' => 'Other',
                'price' => 1000 + $i,
                'status' => 'active',
            ]);
        }
        $r = app(FraudScoringService::class)->evaluate($u);
        $this->assertGreaterThanOrEqual(30, $r['score']);
        $this->assertNotEmpty($r['reasons']);
        $this->assertDatabaseHas('fraud_flags', [
            'related_type' => 'account',
            'related_id' => $u->id,
            'status' => 'open',
        ]);
    }
}
