@extends('layouts.student')
@section('title', 'Payouts')
@section('content')
    <x-page-header title="Your earnings" eyebrow="PAYOUTS"
        description="Track completed sales, set your verified bank account and request a withdrawal." />
    <div class="stats-grid">
        <x-stat label="Completed sales" :value="'₦' . number_format($totalSales, 2)" icon="bag" />
        <x-stat label="Available to request" :value="'₦' . number_format($available, 2)" icon="wallet" />
        <x-stat label="Paid out" :value="'₦' . number_format($totalPaid, 2)" icon="check" />
    </div>
    <div class="detail-grid mb-7" x-data="payoutBankSetup"
        data-bank-authorized="{{ (int) session('payout_bank_verified_at', 0) >= now()->subMinutes(15)->timestamp ? 'true' : 'false' }}"
        data-banks-url="{{ route('payouts.banks') }}" data-resolve-url="{{ route('payouts.bank.resolve') }}">
        <x-card title="Payout account" description="Confirm your email before adding or changing your bank details.">
            @if ($bankAccount)
                <div class="rounded-lg bg-slate-50 p-5 mb-5">
                    <x-badge status="verified" />
                    <p class="font-semibold mt-3">{{ $bankAccount->bank_name }}</p>
                    <p class="text-sm mt-1">{{ $bankAccount->account_name }}</p>
                    <p class="field-hint">••••••{{ substr($bankAccount->account_number, -4) }}</p>
                </div>
            @else
                <x-alert title="Set up your bank account">We'll verify your Nigerian bank account through Paystack before
                    you can request a payout.</x-alert>
            @endif
            <form method="POST" action="{{ route('payouts.bank.otp') }}">
                @csrf
                <x-button variant="secondary"
                    icon="lock">{{ $bankAccount ? 'Change bank account securely' : 'Send security code' }}</x-button>
            </form>
            <form method="POST" action="{{ route('payouts.bank.otp.verify') }}" class="form-stack mt-5">
                @csrf
                <x-field name="otp" label="Email security code" inputmode="numeric" autocomplete="one-time-code"
                    pattern="[0-9]{6}" maxlength="6" required
                    hint="Enter the 6-digit code sent to your registered email." />
                <x-button>Confirm code</x-button>
            </form>
            <div x-show="authorized" x-cloak class="form-stack mt-7 border-t border-slate-100 pt-6">
                <h3 class="text-lg">Add bank account</h3>
                <div class="field">
                    <label for="bank-code">Nigerian bank</label>
                    <select id="bank-code" x-model="bankCode" @change="maybeResolve" class="input">
                        <option value="">Select a bank</option>
                        <template x-for="bank in banks" :key="bank.code">
                            <option :value="bank.code" x-text="bank.name">
                            </option>
                        </template>
                    </select>
                </div>
                <div class="field">
                    <label for="account-number">Account number</label>
                    <input id="account-number" x-model="accountNumber" @input.debounce.500ms="maybeResolve"
                        inputmode="numeric" maxlength="10" class="input" aria-describedby="bank-help">
                    <p id="bank-help" class="field-hint">Enter your 10-digit Nigerian account number.</p>
                </div>
                <p x-show="loading" role="status" class="muted text-sm">Checking account with Paystack…</p>
                <div x-show="resolved" class="alert alert-success">
                    <div>
                        <strong>Account confirmed</strong>
                        <p x-text="resolvedName">
                        </p>
                    </div>
                </div>
                <form method="POST" action="{{ route('payouts.bank.store') }}" x-show="resolved">
                    @csrf
                    <input type="hidden" name="bank_code" :value="bankCode">
                    <input type="hidden" name="bank_name" :value="selectedBankName">
                    <input type="hidden" name="account_number" :value="accountNumber">
                    <x-button class="w-full">Save verified bank account</x-button>
                </form>
            </div>
            <p x-show="error" x-text="error" role="alert" class="field-error mt-4">
            </p>
        </x-card>
        <aside class="stack content-start">
            <x-card title="Request a withdrawal"
                description="Requests are reviewed by an administrator. Only completed sales contribute to your available balance.">
                <form method="POST" action="{{ route('payouts.store') }}" class="form-stack">
                    @csrf
                    <x-field name="amount" label="Amount (₦)" type="number" min="5000" max="200000" step="0.01"
                        required hint="Between ₦5,000 and ₦200,000 per request." />
                    <x-button :disabled="!$bankAccount || $available < 5000">Request payout</x-button>
                    @if (!$bankAccount || $available < 5000)
                        <p class="field-hint">You'll need a verified bank account and at least ₦5,000 available to request a
                            payout.</p>
                    @endif
                </form>
            </x-card>
            <x-alert title="Your balance, explained">Pending and processing requests reserve part of your sales balance
                until a decision is recorded.</x-alert>
        </aside>
    </div>
    <div class="section-heading">
        <h2>Payout history</h2>
    </div>
    <x-table label="Your payout requests">
        <thead>
            <tr>
                <th>Reference</th>
                <th>Amount</th>
                <th>Bank</th>
                <th>Status</th>
                <th>Requested</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payouts as $payout)
                <tr>
                    <td class="font-mono text-xs">{{ $payout->reference }}</td>
                    <td class="font-semibold">₦{{ number_format($payout->amount, 2) }}</td>
                    <td>{{ $payout->bankAccount->bank_name }}<p class="field-hint">
                            ••••{{ substr($payout->bankAccount->account_number, -4) }}</p>
                    </td>
                    <td>
                        <x-badge :status="$payout->status" />
                    </td>
                    <td>{{ $payout->created_at->format('d M Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty title="No withdrawals yet"
                            description="Your payout requests and their progress will appear here." icon="wallet" />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-table>
    <div class="pagination">{{ $payouts->links() }}</div>
@endsection
