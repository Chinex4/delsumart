<?php

namespace Tests\Feature;

use App\Models\Dispute;
use App\Models\Listing;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FraudScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RedesignedWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    private function student(
        string $name = 'Student',
        string $status = 'verified',
        bool $admin = false,
    ): User {
        $user = User::create([
            'name' => $name,
            'matric_no' => 'DELSU/'.$name,
            'email' => strtolower($name).'@example.test',
            'programme' => 'Computer Science',
            'level' => '300',
            'password' => 'Password123!',
            'role' => $admin ? 'admin' : 'student',
            'account_status' => 'active',
        ]);
        if (! $admin) {
            $user->verification()->create([
                'matric_no' => $user->matric_no,
                'full_name' => $user->name,
                'programme' => $user->programme,
                'level' => $user->level,
                'id_card_image' => 'kyc/'.$user->id.'/id.png',
                'fee_receipt_image' => 'kyc/'.$user->id.'/receipt.pdf',
                'verification_status' => $status,
            ]);
        }

        return $user;
    }

    private function listing(
        User $seller,
        string $title = 'Campus laptop',
        int $price = 120000,
        string $status = 'active',
    ): Listing {
        return $seller->listings()->create([
            'title' => $title,
            'description' => 'A useful campus essential.',
            'category' => 'Laptops & Computers',
            'price' => $price,
            'status' => $status,
        ]);
    }

    private function transaction(
        User $seller,
        User $buyer,
        string $status = 'paid_held',
    ): Transaction {
        return Transaction::create([
            'listing_id' => $this->listing($seller)->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'amount' => 120000,
            'status' => $status,
            'paystack_reference' => 'DM-'.uniqid(),
        ]);
    }

    public function test_guest_pages_render_real_listings_and_filter_and_sort_correctly(): void
    {
        $seller = $this->student();
        $this->listing($seller, 'Affordable laptop', 50000);
        $this->listing($seller, 'Premium laptop', 250000);
        $this->listing($seller, 'Hidden sold item', 10000, 'sold');
        $this->get('/')
            ->assertOk()
            ->assertSee('Affordable laptop')
            ->assertDontSee('Hidden sold item');
        $this->get('/marketplace?sort=price_asc')
            ->assertOk()
            ->assertSeeInOrder(['Affordable laptop', 'Premium laptop']);
        $this->get('/marketplace?q=Premium&min_price=100000&max_price=300000')
            ->assertOk()
            ->assertSee('Premium laptop')
            ->assertDontSee('Affordable laptop');
        $this->get('/marketplace?category=Other')
            ->assertOk()
            ->assertSee('No finds this time');
        foreach (
            [
                '/login',
                '/register',
                '/mfa',
                '/forgot-password',
                '/reset-password/test-token?email=a@example.test',
            ] as $path
        ) {
            $this->get($path)->assertOk();
        }
    }

    public function test_all_student_screens_render_and_show_only_their_own_activity(): void
    {
        $buyer = $this->student('Buyer');
        $seller = $this->student('Seller');
        $other = $this->student('Other');
        $transaction = $this->transaction($seller, $buyer);
        $privateListing = $this->listing(
            $other,
            'Unrelated private dashboard listing',
        );
        $otherTransaction = $this->transaction($other, $seller);
        Dispute::create([
            'transaction_id' => $transaction->id,
            'complainant_id' => $buyer->id,
            'category' => 'Condition',
            'details' => 'Buyer concern',
        ]);
        Dispute::create([
            'transaction_id' => $otherTransaction->id,
            'complainant_id' => $other->id,
            'category' => 'Condition',
            'details' => 'Unrelated private dispute',
        ]);
        $this->actingAs($buyer);
        foreach (
            [
                '/dashboard',
                '/account',
                '/account/listings',
                '/account/listings/create',
                '/account/purchases',
                '/account/sales',
                '/account/transactions',
                '/account/disputes',
                '/verification',
            ] as $path
        ) {
            $this->get($path)
                ->assertOk()
                ->assertDontSee($privateListing->title)
                ->assertDontSee('Unrelated private dispute');
        }
        $this->get('/account/purchases')
            ->assertSee($transaction->paystack_reference)
            ->assertDontSee($otherTransaction->paystack_reference);
        $this->get('/account/disputes')->assertSee('Buyer concern');
        $this->actingAs($seller)
            ->get('/account/disputes')
            ->assertSee('Buyer concern');
    }

    public function test_all_admin_screens_render_with_review_data(): void
    {
        $admin = $this->student('Admin', admin: true);
        $student = $this->student('Pending', 'pending');
        $buyer = $this->student('Buyer');
        $tx = $this->transaction($student, $buyer, 'disputed');
        Dispute::create([
            'transaction_id' => $tx->id,
            'complainant_id' => $buyer->id,
            'category' => 'Condition',
            'details' => 'Review this issue',
        ]);
        app(FraudScoringService::class)->evaluate($student);
        $this->actingAs($admin);
        foreach (
            [
                '/admin',
                '/admin/students',
                '/admin/students/'.$student->id,
                '/admin/verifications',
                '/admin/verifications/'.$student->verification->id,
                '/admin/listings',
                '/admin/transactions',
                '/admin/disputes',
                '/admin/fraud-flags',
                '/admin/audit-logs',
            ] as $path
        ) {
            $this->get($path)->assertOk();
        }
        $this->get('/admin/verifications/'.$student->verification->id)
            ->assertSee(
                route('kyc.documents.show', [
                    $student->verification,
                    'id-card',
                ]),
                false,
            )
            ->assertSee('View PDF / document')
            ->assertDontSee($student->verification->id_card_image)
            ->assertDontSee($student->verification->fee_receipt_image);
    }

    public function test_document_access_remains_private_for_both_document_types(): void
    {
        Storage::fake('local');
        $owner = $this->student('Owner');
        $other = $this->student('Other');
        $admin = $this->student('Admin', admin: true);
        $verification = $owner->verification;
        Storage::disk('local')->put(
            $verification->id_card_image,
            'private-image',
        );
        Storage::disk('local')->put(
            $verification->fee_receipt_image,
            '%PDF-private',
        );
        foreach (['id-card', 'fee-receipt'] as $type) {
            $url = route('kyc.documents.show', [$verification, $type]);
            $this->get($url)->assertRedirect(route('login'));
            $this->actingAs($other)->get($url)->assertForbidden();
            $this->actingAs($owner)->get($url)->assertOk();
            $this->actingAs($admin)->get($url)->assertOk();
            auth()->forgetGuards();
        }
        $this->actingAs($owner)
            ->get('/verification/'.$verification->id.'/documents/unknown')
            ->assertNotFound();
        $this->actingAs($other)
            ->get('/admin/verifications/'.$verification->id)
            ->assertForbidden();
        $this->actingAs($owner)
            ->get(
                '/verification/'.
                    $verification->id.
                    '/documents/..%2F..%2F.env',
            )
            ->assertNotFound();
    }

    public function test_kyc_submission_resubmission_and_admin_decisions_keep_audit_and_validation(): void
    {
        Storage::fake('local');
        Notification::fake();
        $student = $this->student('Pending', 'rejected');
        $admin = $this->student('Admin', admin: true);
        $this->actingAs($student->fresh())
            ->post('/verification', [
                'id_card' => UploadedFile::fake()->create(
                    'id.pdf',
                    50,
                    'application/pdf',
                ),
                'fee_receipt' => UploadedFile::fake()->create(
                    'fees.pdf',
                    50,
                    'application/pdf',
                ),
            ])
            ->assertRedirect('/verification');
        $verification = $student->verification->fresh();
        $this->assertSame('pending', $verification->verification_status);
        $this->assertSame(1, $verification->resubmission_count);
        Storage::disk('local')->assertExists($verification->id_card_image);
        $this->actingAs($admin)
            ->patch('/admin/verifications/'.$verification->id, [
                'decision' => 'rejected',
            ])
            ->assertSessionHasErrors('reason');
        $this->actingAs($admin)
            ->patch('/admin/verifications/'.$verification->id, [
                'decision' => 'rejected',
                'reason' => 'Please upload a clearer receipt.',
            ])
            ->assertRedirect();
        $this->assertDatabaseHas('audit_logs', [
            'target_id' => $verification->id,
            'action_type' => 'kyc_rejected',
        ]);
        $this->actingAs($student->fresh())
            ->get('/verification')
            ->assertSee('Please upload a clearer receipt.');
        $this->actingAs($admin)
            ->patch('/admin/verifications/'.$verification->id, [
                'decision' => 'verified',
            ])
            ->assertRedirect();
        $this->assertDatabaseHas('audit_logs', [
            'target_id' => $verification->id,
            'action_type' => 'kyc_verified',
        ]);
        $this->actingAs($student->fresh())
            ->post('/verification', [
                'id_card' => UploadedFile::fake()->create(
                    'id.pdf',
                    50,
                    'application/pdf',
                ),
                'fee_receipt' => UploadedFile::fake()->create(
                    'fees.pdf',
                    50,
                    'application/pdf',
                ),
            ])
            ->assertStatus(409);
    }

    public function test_suspended_or_unverified_students_cannot_reach_listing_creation(): void
    {
        $student = $this->student('Pending', 'pending');
        $this->actingAs($student)
            ->get('/account/listings/create')
            ->assertRedirect('/verification');
        $student->verification->update(['verification_status' => 'verified']);
        $student->update(['account_status' => 'suspended']);
        $this->actingAs($student->fresh())
            ->get('/account/listings/create')
            ->assertRedirect('/verification');
    }

    public function test_transaction_actions_are_owner_and_state_scoped(): void
    {
        $buyer = $this->student('Buyer');
        $seller = $this->student('Seller');
        $other = $this->student('Other');
        $tx = $this->transaction($seller, $buyer);
        $this->actingAs($buyer)
            ->get('/account/purchases')
            ->assertSee('Confirm receipt');
        $this->actingAs($seller)
            ->get('/account/sales')
            ->assertDontSee('Confirm receipt');
        $this->actingAs($other)
            ->post('/transactions/'.$tx->id.'/confirm')
            ->assertForbidden();
        $tx->update(['status' => 'disputed']);
        $this->actingAs($buyer)
            ->get('/account/purchases')
            ->assertDontSee('Confirm receipt')
            ->assertSee('Normal completion is paused');
        $this->actingAs($buyer)
            ->post('/transactions/'.$tx->id.'/confirm')
            ->assertStatus(409);
    }

    public function test_checkout_and_callback_keep_server_authoritative_amount_checks(): void
    {
        Notification::fake();
        $buyer = $this->student('Buyer');
        $seller = $this->student('Seller');
        $listing = $this->listing($seller);
        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/test',
                ],
            ]),
        ]);
        $this->actingAs($buyer)
            ->post('/checkout/'.$listing->id, ['amount' => 1])
            ->assertRedirect('https://checkout.paystack.com/test');
        Http::assertSent(fn ($request) => $request['amount'] === 12000000);
        $tx = Transaction::first();
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::sequence()
                ->push([
                    'status' => true,
                    'data' => [
                        'status' => 'success',
                        'amount' => 1,
                        'currency' => 'NGN',
                    ],
                ])
                ->push([
                    'status' => true,
                    'data' => [
                        'status' => 'success',
                        'amount' => 12000000,
                        'currency' => 'NGN',
                    ],
                ]),
        ]);
        $this->get(
            '/payments/callback?reference='.$tx->paystack_reference,
        )->assertStatus(422);
        $this->assertSame('pending_payment', $tx->fresh()->status);
        $this->get(
            '/payments/callback?reference='.$tx->paystack_reference,
        )->assertRedirect('/dashboard');
        $this->assertSame('paid_held', $tx->fresh()->status);
    }

    public function test_webhook_signature_and_idempotency_are_preserved(): void
    {
        config(['services.paystack.secret_key' => 'test-secret']);
        $payload = json_encode([
            'event' => 'charge.success',
            'data' => ['reference' => 'DM-TEST'],
        ]);
        $this->call(
            'POST',
            '/paystack/webhook',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $payload,
        )->assertUnauthorized();
        $headers = [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_PAYSTACK_SIGNATURE' => hash_hmac(
                'sha512',
                $payload,
                'test-secret',
            ),
        ];
        $this->call(
            'POST',
            '/paystack/webhook',
            [],
            [],
            [],
            $headers,
            $payload,
        )->assertOk();
        $this->call(
            'POST',
            '/paystack/webhook',
            [],
            [],
            [],
            $headers,
            $payload,
        )->assertOk();
        $this->assertDatabaseCount('payment_events', 1);
    }
}
