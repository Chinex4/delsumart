@extends('layouts.admin') @section('content')
    <div class="mx-auto max-w-6xl px-4 py-10">
        <a href="{{ route('admin.students') }}" class="text-sm font-bold text-blue-800">← Students</a>
        <div class="mt-5 grid gap-6 lg:grid-cols-[1fr_340px]">
            <section class="border bg-white p-6">
                <h1 class="text-2xl font-black text-blue-950">{{ $student->name }}</h1>
                <p class="mt-1 text-slate-500">{{ $student->matric_no }} · {{ $student->programme }} · {{ $student->level }}
                </p>
                <div class="mt-6 grid gap-4 sm:grid-cols-3">
                    <div>
                        <p class="text-xs uppercase text-slate-500">KYC</p>
                        <p class="font-bold">{{ $student->verification?->verification_status ?? 'Not submitted' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-slate-500">Account</p>
                        <p class="font-bold">{{ $student->account_status }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-slate-500">Joined</p>
                        <p class="font-bold">{{ $student->created_at->format('d M Y') }}</p>
                    </div>
                </div>
                <h2 class="mt-8 font-black">Recent listings</h2>
                <div class="mt-3 divide-y border">
                    @forelse($student->listings as $l)
                        <div class="flex justify-between p-3"><span>{{ $l->title }}</span><span
                                class="font-semibold">₦{{ number_format($l->price, 2) }} · {{ $l->status }}</span></div>
                    @empty<p class="p-4 text-slate-500">No listings.</p>
                    @endforelse
                </div>
            </section>
            <aside class="space-y-5">
                <div class="border bg-white p-5">
                    <p class="text-xs font-bold uppercase text-slate-500">Fraud risk</p>
                    <p class="mt-2 text-3xl font-black">{{ $risk['score'] }}/100</p>
                    <p class="font-bold uppercase">{{ $risk['level'] }} risk</p>
                    <ul class="mt-3 space-y-2 text-sm text-slate-600">
                        @forelse($risk['reasons'] as $reason)
                        <li>{{ $reason }}</li>@empty<li>No active risk rules triggered.</li>
                        @endforelse
                    </ul>
                </div>
                <form method="POST" action="{{ route('admin.students.status', $student) }}" class="border bg-white p-5">
                    @csrf @method('PATCH')<h2 class="font-black">Account control</h2><select name="account_status"
                        class="mt-3 w-full border p-2">
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                    </select>
                    <textarea required name="reason" rows="3" placeholder="Administrative reason" class="mt-3 w-full border p-2"></textarea><button class="mt-3 w-full bg-blue-950 px-4 py-2 font-bold text-white">Apply
                        and audit</button>
                </form>
            </aside>
        </div>
    </div>
@endsection
