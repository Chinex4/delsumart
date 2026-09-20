<?php

namespace Tests\Feature;

use App\Models\FraudFlag;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspacePresentationTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $name, string $role = 'student'): User
    {
        return User::create([
            'name' => $name,
            'email' => strtolower($name).'@example.test',
            'matric_no' => 'DELSU/'.$name,
            'programme' => 'Computer Science',
            'level' => '300',
            'password' => 'Password123!',
            'role' => $role,
            'account_status' => 'active',
        ]);
    }

    public function test_transaction_filters_preserve_student_scope_and_admin_date_and_party_filters(): void
    {
        $buyer = $this->user('Buyer');
        $seller = $this->user('Seller');
        $other = $this->user('Other');
        $admin = $this->user('Admin', 'admin');
        $listing = $seller->listings()->create([
            'title' => 'Study laptop', 'description' => 'Campus laptop',
            'category' => 'Laptops & Computers', 'price' => 90000,
        ]);
        foreach ([['DM-MATCH', $buyer, 'paid_held'], ['DM-RELEASED', $buyer, 'released'], ['DM-PRIVATE', $other, 'paid_held']] as [$reference, $owner, $status]) {
            Transaction::create([
                'listing_id' => $listing->id, 'buyer_id' => $owner->id, 'seller_id' => $seller->id,
                'amount' => 90000, 'status' => $status, 'paystack_reference' => $reference,
            ]);
        }
        $this->actingAs($buyer)->get('/account/transactions?status=paid_held')
            ->assertOk()->assertSee('DM-MATCH')->assertDontSee('DM-RELEASED')->assertDontSee('DM-PRIVATE');
        $this->get('/account/purchases?status=released')->assertOk()->assertSee('DM-RELEASED')->assertDontSee('DM-MATCH');
        $this->actingAs($admin)->get('/admin/transactions?buyer=Buyer&seller=Seller&status=paid_held&from='.now()->toDateString())
            ->assertOk()->assertSee('DM-MATCH')->assertDontSee('DM-RELEASED')->assertDontSee('DM-PRIVATE');
        $this->get('/admin/transactions?to='.now()->subDay()->toDateString())->assertOk()->assertDontSee('DM-MATCH');
    }

    public function test_risk_filters_use_distinct_ranges_and_identify_flagged_student(): void
    {
        $admin = $this->user('Admin', 'admin');
        $student = $this->user('Student');
        foreach ([20, 40, 70] as $score) {
            FraudFlag::create(['related_type' => 'user', 'related_id' => $student->id, 'risk_score' => $score, 'flag_reason' => 'Signal-'.$score, 'status' => 'open']);
        }
        $this->actingAs($admin)->get('/admin/fraud-flags?risk=low')->assertOk()
            ->assertSee('Student')->assertSee('Signal-20')->assertDontSee('Signal-40')->assertDontSee('Signal-70');
        $this->get('/admin/fraud-flags?risk=medium')->assertSee('Signal-40')->assertDontSee('Signal-20')->assertDontSee('Signal-70');
        $this->get('/admin/fraud-flags?risk=high')->assertSee('Signal-70')->assertDontSee('Signal-20')->assertDontSee('Signal-40');
    }

    public function test_payout_pages_render_scoped_data_and_mask_student_account_number(): void
    {
        $student = $this->user('Student');
        $other = $this->user('Other');
        $admin = $this->user('Admin', 'admin');
        foreach ([$student, $other] as $user) {
            $bank = $user->bankAccount()->create([
                'bank_code' => '058', 'bank_name' => 'Test bank', 'account_number' => '0123456789',
                'account_name' => $user->name, 'verified_at' => now(),
            ]);
            $user->payoutRequests()->create([
                'bank_account_id' => $bank->id, 'amount' => 5000, 'status' => 'pending',
                'reference' => 'PO-'.$user->name,
            ]);
        }
        $this->actingAs($student)->get('/account/payouts')->assertOk()->assertSee('PO-Student')
            ->assertDontSee('PO-Other')->assertDontSee('0123456789')->assertSee('Confirm code');
        $this->get('/admin/payouts')->assertForbidden();
        $this->actingAs($admin)->get('/admin/payouts')->assertOk()->assertSee('PO-Student')->assertSee('PO-Other');
    }
}
