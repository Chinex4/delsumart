@extends('layouts.dashboard') @section('title','Verification') @section('content')<div class="mx-auto max-w-3xl px-4 py-14">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-blue-700">Student verification</p>
                <h1 class="mt-2 text-3xl font-black text-blue-950">Confirm your DELSU identity</h1><p class="mt-2 text-sm text-slate-500">Upload your documents securely for administrator review.</p>
            </div>
            @if ($verification)
                <span
                    class="rounded-full px-3 py-1 text-xs font-bold uppercase {{ $verification->verification_status === 'verified' ? 'bg-emerald-100 text-emerald-800' : ($verification->verification_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">{{ $verification->verification_status }}</span>
            @endif
        </div>
        @if ($verification?->verification_status === 'verified')
            <div class="mt-8 border-l-4 border-emerald-600 bg-emerald-50 p-6">
                <h2 class="font-bold text-emerald-950">Verified student</h2>
                <p class="mt-1 text-sm text-emerald-800">Your trading access is unlocked.</p>
            </div>
        @else
            @if ($verification?->verification_status === 'rejected')
                <div class="mt-8 border-l-4 border-red-600 bg-red-50 p-5">
                    <p class="font-bold">Verification needs attention</p>
                    <p class="mt-1 text-sm">{{ $verification->rejection_reason }}</p>
                </div>
            @elseif($verification)
                <div class="mt-8 border-l-4 border-amber-500 bg-amber-50 p-5">
                    <p class="font-bold">Review in progress</p>
                    <p class="mt-1 text-sm">Your documents are private and awaiting administrator review. Trading remains
                        locked.</p>
                </div>
            @endif
            <form method="POST" enctype="multipart/form-data" action="{{ route('kyc.store') }}" class="mt-8 space-y-6">
                @csrf<div class="grid gap-4 border bg-white p-5 sm:grid-cols-2">
                    @foreach ([['Full name', auth()->user()->name], ['Matric number', auth()->user()->matric_no], ['Programme', auth()->user()->programme], ['Level', auth()->user()->level]] as $x)
                        <div>
                            <p class="text-xs font-bold uppercase text-slate-500">{{ $x[0] }}</p>
                            <p class="mt-1 font-semibold">{{ $x[1] }}</p>
                        </div>
                    @endforeach
                </div>
                <label class="block"><span class="font-semibold">Student ID card</span><span
                        class="mt-1 block text-sm text-slate-500">JPG, PNG or PDF. Maximum 4 MB.</span><input type="file"
                        name="id_card" accept=".jpg,.jpeg,.png,.pdf" required
                        class="mt-3 block w-full rounded-lg border bg-white p-3"></label><label class="block"><span
                        class="font-semibold">Current school-fee receipt / breakdown</span><span
                        class="mt-1 block text-sm text-slate-500">JPG, PNG or PDF. Maximum 4 MB.</span><input type="file"
                        name="fee_receipt" accept=".jpg,.jpeg,.png,.pdf" required
                        class="mt-3 block w-full rounded-lg border bg-white p-3"></label><button
                    class="rounded-lg bg-blue-950 px-6 py-3 font-bold text-white">{{ $verification ? 'Resubmit documents' : 'Submit for verification' }}</button>
            </form>
        @endif
</div>@endsection
