@extends('layouts.student')
@section('title', 'Your disputes')
@section('content')
    <x-page-header title="Support when a trade needs attention."
        description="Track issues and decisions for transactions you're part of." eyebrow="DISPUTES">
        <x-button :href="route('account.transactions')" variant="secondary">View transactions</x-button>
    </x-page-header>
    <div class="stack">
        @forelse($disputes as $dispute)
            <x-card>
                <x-dispute-summary :dispute="$dispute" />
            </x-card>
        @empty
            <x-card>
                <x-empty title="No disputes to show" icon="shield"
                    description="If something isn't right, open the transaction and raise a dispute before confirming receipt." />
            </x-card>
        @endforelse
    </div>
    <div class="pagination">{{ $disputes->links() }}</div>
@endsection
