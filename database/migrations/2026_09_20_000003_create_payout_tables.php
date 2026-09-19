<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->string('bank_code', 20);
            $t->string('bank_name');
            $t->string('account_number', 20);
            $t->string('account_name');
            $t->string('paystack_recipient_code')->nullable()->index();
            $t->timestamp('verified_at');
            $t->timestamps();
        });

        Schema::create('payout_requests', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->foreignId('bank_account_id')->constrained()->restrictOnDelete();
            $t->decimal('amount', 12, 2);
            $t->enum('status', ['pending', 'processing', 'paid', 'rejected'])->default('pending')->index();
            $t->string('reference')->unique();
            $t->text('admin_note')->nullable();
            $t->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('processed_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_requests');
        Schema::dropIfExists('bank_accounts');
    }
};
