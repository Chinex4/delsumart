<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('matric_no', 30)->unique();
            $t->string('email')->unique();
            $t->string('programme');
            $t->string('level', 20);
            $t->string('password');
            $t->enum('role', ['student', 'admin'])->default('student')->index();
            $t->enum('account_status', ['active', 'suspended'])->default('active');
            $t->rememberToken();
            $t->timestamps();
        });
        Schema::create('verifications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->string('matric_no', 30)->unique();
            $t->string('full_name');
            $t->string('programme');
            $t->string('level', 20);
            $t->string('id_card_image');
            $t->string('fee_receipt_image');
            $t->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending')->index();
            $t->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('verified_at')->nullable();
            $t->text('rejection_reason')->nullable();
            $t->unsignedInteger('resubmission_count')->default(0);
            $t->timestamps();
        });
        Schema::create('listings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('title')->index();
            $t->text('description');
            $t->string('category')->index();
            $t->decimal('price', 12, 2);
            $t->enum('status', ['active', 'sold', 'removed'])->default('active')->index();
            $t->timestamps();
        });
        Schema::create('listing_images', function (Blueprint $t) {
            $t->id();
            $t->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $t->string('path');
            $t->unsignedTinyInteger('sort_order')->default(0);
            $t->timestamps();
        });
        Schema::create('transactions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('listing_id')->constrained()->restrictOnDelete();
            $t->foreignId('buyer_id')->constrained('users')->restrictOnDelete();
            $t->foreignId('seller_id')->constrained('users')->restrictOnDelete();
            $t->decimal('amount', 12, 2);
            $t->enum('status', ['pending_payment', 'paid_held', 'release_pending', 'released', 'disputed', 'refunded', 'cancelled'])->default('pending_payment')->index();
            $t->string('paystack_reference')->unique();
            $t->timestamp('paid_at')->nullable();
            $t->timestamp('completed_at')->nullable();
            $t->timestamps();
        });
        Schema::create('disputes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $t->foreignId('complainant_id')->constrained('users')->restrictOnDelete();
            $t->string('category');
            $t->text('details');
            $t->string('evidence_path')->nullable();
            $t->enum('status', ['open', 'under_review', 'resolved', 'rejected'])->default('open')->index();
            $t->text('resolution')->nullable();
            $t->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('resolved_at')->nullable();
            $t->timestamps();
        });
        Schema::create('fraud_flags', function (Blueprint $t) {
            $t->id();
            $t->string('related_type')->index();
            $t->unsignedBigInteger('related_id')->index();
            $t->text('flag_reason');
            $t->unsignedTinyInteger('risk_score');
            $t->enum('status', ['open', 'reviewed', 'dismissed'])->default('open')->index();
            $t->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('reviewed_at')->nullable();
            $t->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('action_type')->index();
            $t->string('target_type');
            $t->unsignedBigInteger('target_id');
            $t->text('notes')->nullable();
            $t->timestamp('created_at')->useCurrent();
        });
        Schema::create('mfa_codes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('code_hash');
            $t->timestamp('expires_at')->index();
            $t->timestamp('used_at')->nullable();
            $t->unsignedTinyInteger('attempts')->default(0);
            $t->timestamp('last_sent_at')->nullable();
            $t->timestamps();
        });
        Schema::create('payment_events', function (Blueprint $t) {
            $t->id();
            $t->string('event_key')->unique();
            $t->string('event_type')->index();
            $t->string('reference')->nullable()->index();
            $t->json('payload');
            $t->timestamp('processed_at')->nullable();
            $t->timestamps();
        });
        Schema::create('sessions', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->foreignId('user_id')->nullable()->index();
            $t->string('ip_address', 45)->nullable();
            $t->text('user_agent')->nullable();
            $t->longText('payload');
            $t->integer('last_activity')->index();
        });
        Schema::create('cache', function (Blueprint $t) {
            $t->string('key')->primary();
            $t->mediumText('value');
            $t->integer('expiration');
        });
        Schema::create('jobs', function (Blueprint $t) {
            $t->id();
            $t->string('queue')->index();
            $t->longText('payload');
            $t->unsignedTinyInteger('attempts');
            $t->unsignedInteger('reserved_at')->nullable();
            $t->unsignedInteger('available_at');
            $t->unsignedInteger('created_at');
        });
    }

    public function down(): void
    {
        foreach (['jobs', 'cache', 'sessions', 'payment_events', 'mfa_codes', 'audit_logs', 'fraud_flags', 'disputes', 'transactions', 'listing_images', 'listings', 'verifications', 'users'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
