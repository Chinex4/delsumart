@extends('layouts.app') @section('content')
    <div class="mx-auto max-w-7xl px-4 py-12">
        <h1 class="text-3xl font-black text-blue-950">Transaction disputes</h1>
        <p class="mt-2 text-slate-600">Review participant claims before changing the protected transaction state.</p>
        <div class="mt-8 space-y-5">
            @forelse($disputes as $d)
                <article class="border bg-white p-5">
                    <div class="grid gap-5 lg:grid-cols-[1fr_420px]">
                        <div>
                            <div class="flex items-center gap-3">
                                <h2 class="font-bold">Dispute #{{ $d->id }} · {{ $d->transaction->listing->title }}
                                </h2><span class="text-xs font-bold uppercase text-red-700">{{ $d->status }}</span>
                            </div>
                            <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                                <div>
                                    <dt class="text-slate-500">Buyer</dt>
                                    <dd class="font-semibold">{{ $d->transaction->buyer->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Seller</dt>
                                    <dd class="font-semibold">{{ $d->transaction->seller->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Amount</dt>
                                    <dd class="font-semibold">₦{{ number_format($d->transaction->amount, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Payment reference</dt>
                                    <dd class="font-mono text-xs">{{ $d->transaction->paystack_reference }}</dd>
                                </div>
                            </dl>
                            <div class="mt-5 border-l-4 border-red-600 bg-red-50 p-4">
                                <p class="text-xs font-bold uppercase">{{ $d->category }}</p>
                                <p class="mt-2 text-sm">{{ $d->details }}</p>
                            </div>
                        </div>
                        @if (in_array($d->status, ['open', 'under_review']))
                            <form method="POST" action="{{ route('admin.disputes.resolve', $d) }}"
                                class="space-y-3 border bg-slate-50 p-4">@csrf @method('PATCH')<label
                                    class="block text-sm font-semibold">Decision<select name="decision"
                                        class="mt-1 w-full border bg-white p-2">
                                        <option value="resolved">Resolve</option>
                                        <option value="rejected">Reject dispute</option>
                                    </select></label><label class="block text-sm font-semibold">Transaction action<select
                                        name="transaction_action" class="mt-1 w-full border bg-white p-2">
                                        <option value="hold">Return to protected hold</option>
                                        <option value="release">Release/complete</option>
                                        <option value="refund">Mark refunded</option>
                                    </select></label><label class="block text-sm font-semibold">Resolution notes
                                    <textarea name="resolution" required rows="4" class="mt-1 w-full border bg-white p-2"></textarea>
                                </label><button class="rounded bg-blue-950 px-4 py-2 text-sm font-bold text-white">Record
                                decision</button></form>@else<div class="border bg-slate-50 p-4 text-sm">
                                <p class="font-bold">Decision recorded</p>
                                <p class="mt-2 text-slate-600">{{ $d->resolution }}</p>
                            </div>
                        @endif
                    </div>
            </article>@empty<div class="border border-dashed bg-white p-10 text-center text-slate-500">No disputes have
                    been submitted.</div>
            @endforelse
        </div>
        <div class="mt-8">{{ $disputes->links() }}</div>
    </div>
@endsection
