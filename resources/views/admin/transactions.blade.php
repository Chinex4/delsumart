@extends('layouts.app') @section('content')
    <div class="mx-auto max-w-7xl px-4 py-10">
        <h1 class="text-3xl font-black text-blue-950">Transactions</h1>
        <form class="mt-6 flex flex-wrap gap-2"><input name="reference" value="{{ request('reference') }}"
                placeholder="Payment reference" class="border bg-white px-3 py-2"><select name="status"
                class="border bg-white px-3 py-2">
                <option value="">All states</option>
                @foreach (['pending_payment', 'paid_held', 'release_pending', 'released', 'disputed', 'refunded', 'cancelled'] as $s)
                    <option @selected(request('status') === $s) value="{{ $s }}">{{ str_replace('_', ' ', $s) }}</option>
                @endforeach
            </select>
            <button class="bg-blue-950 px-4 py-2 font-bold text-white">Filter</button>
        </form>
        <div class="mt-6 overflow-x-auto border bg-white">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100 text-left">
                    <tr>
                        <th class="p-3">Reference</th>
                        <th class="p-3">Listing</th>
                        <th class="p-3">Buyer → Seller</th>
                        <th class="p-3">Amount</th>
                        <th class="p-3">State</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($transactions as $t)
                        <tr>
                            <td class="p-3 font-mono text-xs">{{ $t->paystack_reference }}</td>
                            <td class="p-3">{{ $t->listing->title }}</td>
                            <td class="p-3">{{ $t->buyer->name }} → {{ $t->seller->name }}</td>
                            <td class="p-3">₦{{ number_format($t->amount, 2) }}</td>
                            <td class="p-3 font-bold uppercase">{{ str_replace('_', ' ', $t->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $transactions->links() }}</div>
    </div>
@endsection
