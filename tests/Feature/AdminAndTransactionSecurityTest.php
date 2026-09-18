<?php

namespace Tests\Feature;

use App\Models\Dispute;
use App\Models\Listing;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminAndTransactionSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $id, bool $admin = false): User
    {
        $u = User::create(['name' => 'User '.$id, 'matric_no' => 'M/'.$id, 'email' => 'u'.$id.'@example.test', 'programme' => 'CS', 'level' => '400', 'password' => Hash::make('Password123!'), 'role' => $admin ? 'admin' : 'student', 'account_status' => 'active']);
        if (! $admin) {
            $u->verification()->create(['matric_no' => $u->matric_no, 'full_name' => $u->name, 'programme' => $u->programme, 'level' => $u->level, 'id_card_image' => 'kyc/'.$id.'/id.pdf', 'fee_receipt_image' => 'kyc/'.$id.'/fee.pdf', 'verification_status' => 'verified']);
        }

return $u;
    }

    public function test_admin_can_suspend_student_and_action_is_audited(): void
    {
        $admin = $this->user('admin', true);
        $student = $this->user('student');
        $this->actingAs($admin)->patch('/admin/students/'.$student->id.'/status', ['account_status' => 'suspended', 'reason' => 'Security review'])->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $student->id, 'account_status' => 'suspended']);
        $this->assertDatabaseHas('audit_logs', ['admin_id' => $admin->id, 'target_id' => $student->id, 'action_type' => 'student_suspended']);
    }

    public function test_unrelated_student_cannot_view_private_kyc_document(): void
    {
        Storage::fake('local');
        $owner = $this->user('owner');
        $other = $this->user('other');
        Storage::disk('local')->put($owner->verification->id_card_image, 'secret');
        $this->actingAs($other)->get('/verification/'.$owner->verification->id.'/documents/id-card')->assertForbidden();
    }

    public function test_dispute_blocks_normal_release_and_admin_resolution_is_audited(): void
    {
        $admin = $this->user('admin', true);
        $seller = $this->user('seller');
        $buyer = $this->user('buyer');
        $listing = Listing::create(['user_id' => $seller->id, 'title' => 'Phone', 'description' => 'Test', 'category' => 'Phones & Electronics', 'price' => 100000, 'status' => 'sold']);
        $tx = Transaction::create(['listing_id' => $listing->id, 'buyer_id' => $buyer->id, 'seller_id' => $seller->id, 'amount' => 100000, 'status' => 'disputed', 'paystack_reference' => 'DM-TEST']);
        $d = Dispute::create(['transaction_id' => $tx->id, 'complainant_id' => $buyer->id, 'category' => 'Condition', 'details' => 'Not as described', 'status' => 'open']);
        $this->actingAs($buyer)->post('/transactions/'.$tx->id.'/confirm')->assertStatus(409);
        $this->actingAs($admin)->patch('/admin/disputes/'.$d->id, ['decision' => 'resolved', 'resolution' => 'Refund approved after review', 'transaction_action' => 'refund'])->assertRedirect();
        $this->assertDatabaseHas('transactions', ['id' => $tx->id, 'status' => 'refunded']);
        $this->assertDatabaseHas('audit_logs', ['admin_id' => $admin->id, 'target_type' => 'dispute', 'target_id' => $d->id]);
    }
}
