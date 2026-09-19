@extends('layouts.admin') @section('content')
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
        <p class="text-sm font-bold uppercase tracking-widest text-blue-700">Administration</p>
        <h1 class="mt-2 text-3xl font-black text-blue-950">Trust & safety overview</h1>
        <div class="mt-8 grid gap-px overflow-hidden border bg-slate-200 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($stats as $label => $value)
                <div class="bg-white p-5">
                    <p class="text-xs font-bold uppercase text-slate-500">{{ str_replace('_', ' ', $label) }}</p>
                    <p class="mt-2 text-3xl font-black">{{ $value }}</p>
                </div>
            @endforeach
        </div>
        <div class="mt-8 flex flex-wrap gap-3"><a href="{{ route('admin.verifications') }}"
                class="rounded-lg bg-blue-950 px-4 py-2 text-sm font-bold text-white">KYC requests</a><a
                href="{{ route('admin.flags') }}" class="rounded-lg border bg-white px-4 py-2 text-sm font-bold">Fraud
                flags</a><a href="{{ route('admin.audits') }}"
                class="rounded-lg border bg-white px-4 py-2 text-sm font-bold">Audit logs</a></div>
        <section class="mt-10 overflow-hidden border bg-white">
            <div class="border-b p-5">
                <h2 class="font-bold">Pending verification</h2>
            </div>
            @forelse($pending as $v)
                <div class="flex items-center justify-between gap-4 border-b p-5 last:border-0">
                    <div>
                        <p class="font-semibold">{{ $v->user->name }}</p>
                        <p class="text-sm text-slate-500">{{ $v->matric_no }} · {{ $v->programme }}</p>
                    </div><a href="{{ route('admin.verifications') }}" class="text-sm font-bold text-blue-800">Review</a>
            </div>@empty<div class="p-8 text-sm text-slate-500">No pending verification requests.</div>
            @endforelse
        </section>
    </div>
@endsection
