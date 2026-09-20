@extends('layouts.admin')
@section('title', 'Transactions')
@section('content')
    <x-page-header title="Transactions" eyebrow="PAYMENT OPERATIONS"
        description="Follow verified payments and protected transaction states across the marketplace." />
    <form method="GET" class="filter-bar">
        <x-field name="reference" label="Reference" :value="request('reference')" />
        <x-field name="status" label="State" type="select">
            <option value="">All states</option>
            @foreach (config('ui.transaction_states') as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </x-field>
        <x-field name="buyer" label="Buyer name" :value="request('buyer')" />
        <x-field name="seller" label="Seller name" :value="request('seller')" />
        <x-field name="from" label="From date" type="date" :value="request('from')" />
        <x-field name="to" label="To date" type="date" :value="request('to')" />
        <x-button variant="secondary">Filter</x-button>
        <a class="text-link mb-3" href="{{ route('admin.transactions') }}">Reset</a>
    </form>
    <x-table label="Marketplace transactions">
        <thead>
            <tr>
                <th>Listing / reference</th>
                <th>Buyer / seller</th>
                <th>Amount</th>
                <th>State</th>
                <th>Date</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
                <tr>
                    <td>
                        <strong>{{ $tx->listing->title }}</strong>
                        <p class="field-hint font-mono">{{ $tx->paystack_reference }}</p>
                    </td>
                    <td>{{ $tx->buyer->name }}<p class="field-hint">to {{ $tx->seller->name }}</p>
                    </td>
                    <td class="font-semibold">₦{{ number_format($tx->amount, 2) }}</td>
                    <td>
                        <x-badge :status="$tx->status" />
                    </td>
                    <td>{{ $tx->created_at->format('d M Y') }}</td>
                    <td>
                        <x-button type="button" variant="secondary"
                            data-dialog="transaction-{{ $tx->id }}">Details</x-button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <x-empty title="No matching transactions"
                            description="Try another reference or reset your filters." />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-table>
    @foreach ($transactions as $tx)
        <x-modal :id="'transaction-' . $tx->id" :title="$tx->listing->title">
            <dl class="detail-list">
                @foreach (['Reference' => $tx->paystack_reference, 'Buyer' => $tx->buyer->name, 'Seller' => $tx->seller->name, 'Amount' => '₦' . number_format($tx->amount, 2), 'Created' => $tx->created_at->format('d M Y, H:i'), 'Payment verified' => $tx->paid_at?->format('d M Y, H:i') ?? 'Not yet verified', 'Completed' => $tx->completed_at?->format('d M Y, H:i') ?? 'Not completed'] as $label => $value)
                    <div>
                        <dt>{{ $label }}</dt>
                        <dd class="break-all">{{ $value }}</dd>
                    </div>
                @endforeach
                <div>
                    <dt>State</dt>
                    <dd>
                        <x-badge :status="$tx->status" />
                    </dd>
                </div>
            </dl>
            @if ($tx->status === 'disputed')
                <x-alert tone="warning" class="mt-5">Normal completion is paused pending dispute review.</x-alert>
            @endif
        </x-modal>
    @endforeach
    <div class="pagination">{{ $transactions->links() }}</div>
@endsection
