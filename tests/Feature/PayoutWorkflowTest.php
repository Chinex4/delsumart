<?php

namespace Tests\Feature;

use App\Models\PayoutRequest;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PayoutWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function student(string $name): User
    {
        $user = User::create([
            'name' => $name,
            'matric_no' => 'DELSU/'.$name,
            'email' => strtolower($name).'@test.local',
            'programme' => 'Computer Science',
            'level' => '300',
            'password' => 'Password123!',
            'account_status' => 'active',
        ]);
        $user
            ->verification()
            ->create([
                'matric_no' => $user->matric_no,
                'full_name' => $name,
                'programme' => $user->programme,
                'level' => $user->level,
                'id_card_image' => 'id',
                'fee_receipt_image' => 'receipt',
                'verification_status' => 'verified',
            ]);

        return $user;
    }

    public function test_payout_balance_uses_only_released_sales_and_prevents_overdraw(): void
    {
        $seller = $this->student('Seller');
        $buyer = $this->student('Buyer');
        $listing = $seller
            ->listings()
            ->create([
                'title' => 'Laptop',
                'description' => 'Good',
                'category' => 'Laptops & Computers',
                'price' => 10000,
                'status' => 'sold',
            ]);
        Transaction::create([
            'listing_id' => $listing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'amount' => 10000,
            'status' => 'released',
            'paystack_reference' => 'TX-1',
        ]);
        $bank = $seller
            ->bankAccount()
            ->create([
                'bank_code' => '058',
                'bank_name' => 'GTBank',
                'account_number' => '0123456789',
                'account_name' => 'Seller Test',
                'paystack_recipient_code' => 'RCP_test',
                'verified_at' => now(),
            ]);
        $this->actingAs($seller)
            ->post('/account/payouts', ['amount' => 5000])
            ->assertRedirect();
        $this->actingAs($seller)
            ->post('/account/payouts', ['amount' => 6000])
            ->assertSessionHasErrors('amount');
        $this->assertDatabaseHas('payout_requests', [
            'user_id' => $seller->id,
            'bank_account_id' => $bank->id,
            'amount' => 5000,
            'status' => 'pending',
        ]);
    }

    public function test_payout_enforces_minimum_maximum_and_bank_requirement(): void
    {
        $seller = $this->student('Seller');

        $this->actingAs($seller)
            ->post('/account/payouts', ['amount' => 4999])
            ->assertSessionHasErrors('amount');
        $this->actingAs($seller)
            ->post('/account/payouts', ['amount' => 200001])
            ->assertSessionHasErrors('amount');
        $this->actingAs($seller)
            ->post('/account/payouts', ['amount' => 5000])
            ->assertStatus(422);
    }

    public function test_bank_setup_requires_otp_and_reverifies_account_server_side(): void
    {
        $student = $this->student('Seller');
        $this->actingAs($student)
            ->get('/account/payouts/banks')
            ->assertForbidden();
        Cache::put(
            'payout-bank-otp:'.$student->id,
            [
                'hash' => Hash::make('123456'),
                'sent_at' => now()->timestamp,
                'attempts' => 0,
            ],
            now()->addMinutes(10),
        );
        $this->post('/account/payouts/bank/otp/verify', [
            'otp' => '123456',
        ])->assertRedirect();
        Http::fake([
            'api.paystack.co/bank/resolve*' => Http::response([
                'status' => true,
                'data' => [
                    'account_number' => '0123456789',
                    'account_name' => 'SELLER TEST',
                ],
            ]),
            'api.paystack.co/transferrecipient' => Http::response([
                'status' => true,
                'data' => ['recipient_code' => 'RCP_secure'],
            ]),
        ]);
        $this->post('/account/payouts/bank', [
            'bank_code' => '058',
            'bank_name' => 'Guaranty Trust Bank',
            'account_number' => '0123456789',
        ])->assertRedirect();
        $this->assertDatabaseHas('bank_accounts', [
            'user_id' => $student->id,
            'account_name' => 'SELLER TEST',
            'paystack_recipient_code' => 'RCP_secure',
        ]);
    }

    public function test_admin_can_process_payout_and_action_is_audited(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'matric_no' => 'ADMIN/1',
            'email' => 'admin@test.local',
            'programme' => 'Administration',
            'level' => 'Staff',
            'password' => 'Password123!',
            'role' => 'admin',
        ]);
        $seller = $this->student('Seller');
        $bank = $seller
            ->bankAccount()
            ->create([
                'bank_code' => '058',
                'bank_name' => 'GTBank',
                'account_number' => '0123456789',
                'account_name' => 'Seller',
                'verified_at' => now(),
            ]);
        $payout = PayoutRequest::create([
            'user_id' => $seller->id,
            'bank_account_id' => $bank->id,
            'amount' => 5000,
            'status' => 'pending',
            'reference' => 'PO-TEST',
        ]);
        $this->actingAs($admin)
            ->patch('/admin/payouts/'.$payout->id, ['status' => 'paid'])
            ->assertRedirect();
        $this->assertSame('paid', $payout->fresh()->status);
        $this->assertDatabaseHas('audit_logs', [
            'target_type' => 'payout_request',
            'target_id' => $payout->id,
            'action_type' => 'payout_paid',
        ]);
    }
}
