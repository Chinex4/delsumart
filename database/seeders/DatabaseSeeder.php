<?php

namespace Database\Seeders;

use App\Models\Dispute;
use App\Models\FraudFlag;
use App\Models\Listing;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'DelsuMart Administrator',
            'matric_no' => 'ADMIN001',
            'email' => 'admin@delsumart.test',
            'programme' => 'Administration',
            'level' => 'Staff',
            'password' => Hash::make('DemoAdmin2026!'),
            'role' => 'admin',
        ]);
        $seller = User::create([
            'name' => 'Ada Okafor',
            'matric_no' => 'DEMO/CSC/001',
            'email' => 'ada@delsumart.test',
            'programme' => 'Computer Science',
            'level' => '400',
            'password' => Hash::make('DemoStudent2026!'),
        ]);
        $seller
            ->verification()
            ->create([
                'matric_no' => $seller->matric_no,
                'full_name' => $seller->name,
                'programme' => $seller->programme,
                'level' => $seller->level,
                'id_card_image' => 'demo/private-id',
                'fee_receipt_image' => 'demo/private-fee',
                'verification_status' => 'verified',
                'verified_by' => $admin->id,
                'verified_at' => now(),
            ]);
        $buyer = User::create([
            'name' => 'Emeka Obi',
            'matric_no' => 'DEMO/ENG/004',
            'email' => 'emeka@delsumart.test',
            'programme' => 'Electrical Engineering',
            'level' => '300',
            'password' => Hash::make('DemoStudent2026!'),
        ]);
        $buyer
            ->verification()
            ->create([
                'matric_no' => $buyer->matric_no,
                'full_name' => $buyer->name,
                'programme' => $buyer->programme,
                'level' => $buyer->level,
                'id_card_image' => 'demo/private-id',
                'fee_receipt_image' => 'demo/private-fee',
                'verification_status' => 'verified',
                'verified_by' => $admin->id,
                'verified_at' => now(),
            ]);
        $pending = User::create([
            'name' => 'Tega Efe',
            'matric_no' => 'DEMO/ACC/002',
            'email' => 'tega@delsumart.test',
            'programme' => 'Accounting',
            'level' => '300',
            'password' => Hash::make('DemoStudent2026!'),
        ]);
        $pending
            ->verification()
            ->create([
                'matric_no' => $pending->matric_no,
                'full_name' => $pending->name,
                'programme' => $pending->programme,
                'level' => $pending->level,
                'id_card_image' => 'demo/private-id',
                'fee_receipt_image' => 'demo/private-fee',
                'verification_status' => 'pending',
            ]);
        $rejected = User::create([
            'name' => 'Ese James',
            'matric_no' => 'DEMO/ECO/003',
            'email' => 'ese@delsumart.test',
            'programme' => 'Economics',
            'level' => '200',
            'password' => Hash::make('DemoStudent2026!'),
        ]);
        $rejected
            ->verification()
            ->create([
                'matric_no' => $rejected->matric_no,
                'full_name' => $rejected->name,
                'programme' => $rejected->programme,
                'level' => $rejected->level,
                'id_card_image' => 'demo/private-id',
                'fee_receipt_image' => 'demo/private-fee',
                'verification_status' => 'rejected',
                'verified_by' => $admin->id,
                'verified_at' => now(),
                'rejection_reason' => 'Fee receipt is not readable. Please upload a clear current-session copy.',
                'resubmission_count' => 3,
            ]);
        $active = Listing::create([
            'user_id' => $seller->id,
            'title' => 'HP EliteBook 840 G7',
            'description' => 'Demo listing created for project evaluation and interface testing.',
            'category' => 'Laptops & Computers',
            'price' => 385000,
            'status' => 'active',
        ]);
        Listing::create([
            'user_id' => $seller->id,
            'title' => 'Calculus Textbook Bundle',
            'description' => 'Clean academic textbook bundle.',
            'category' => 'Books & Academic Materials',
            'price' => 12000,
            'status' => 'active',
        ]);
        $sold = Listing::create([
            'user_id' => $seller->id,
            'title' => 'Scientific Calculator',
            'description' => 'Demo sold listing.',
            'category' => 'Books & Academic Materials',
            'price' => 18000,
            'status' => 'sold',
        ]);
        $released = Transaction::create([
            'listing_id' => $sold->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'amount' => 18000,
            'status' => 'released',
            'paystack_reference' => 'DM-DEMO-RELEASED',
            'paid_at' => now()->subDays(3),
            'completed_at' => now()->subDays(2),
        ]);
        $heldListing = Listing::create([
            'user_id' => $seller->id,
            'title' => 'Hostel Reading Lamp',
            'description' => 'Demo protected transaction listing.',
            'category' => 'Hostel/Home Items',
            'price' => 15000,
            'status' => 'sold',
        ]);
        Transaction::create([
            'listing_id' => $heldListing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'amount' => 15000,
            'status' => 'paid_held',
            'paystack_reference' => 'DM-DEMO-HELD',
            'paid_at' => now()->subDay(),
        ]);
        $disputedListing = Listing::create([
            'user_id' => $seller->id,
            'title' => 'Wireless Keyboard',
            'description' => 'Demo disputed transaction listing.',
            'category' => 'Laptops & Computers',
            'price' => 22000,
            'status' => 'sold',
        ]);
        $disputed = Transaction::create([
            'listing_id' => $disputedListing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'amount' => 22000,
            'status' => 'disputed',
            'paystack_reference' => 'DM-DEMO-DISPUTED',
            'paid_at' => now()->subDays(2),
        ]);
        Dispute::create([
            'transaction_id' => $disputed->id,
            'complainant_id' => $buyer->id,
            'category' => 'Item condition',
            'details' => 'Demo dispute for administrator review during project presentation.',
            'status' => 'open',
        ]);
        FraudFlag::create([
            'related_type' => 'account',
            'related_id' => $rejected->id,
            'flag_reason' => 'Three or more KYC resubmissions (+20); Current KYC submission is rejected (+10)',
            'risk_score' => 30,
            'status' => 'open',
        ]);
    }
}
