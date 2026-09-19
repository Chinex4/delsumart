@extends('layouts.dashboard')
@section('title','Account')
@section('content')
<div class="mx-auto max-w-5xl">
    <p class="text-xs font-black uppercase tracking-[.2em] text-blue-600">ACCOUNT</p>
    <h1 class="mt-2 text-3xl font-black text-slate-950">Your student profile</h1>
    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <section class="rounded-2xl border bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="text-lg font-black">Profile information</h2>
            <dl class="mt-6 grid gap-5 sm:grid-cols-2">
                @foreach(['Full name'=>$user->name,'Email'=>$user->email,'Matric number'=>$user->matric_no,'Programme'=>$user->programme,'Level'=>$user->level,'Account status'=>ucfirst($user->account_status)] as $label=>$value)
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ $label }}</dt><dd class="mt-1 font-semibold">{{ $value }}</dd></div>
                @endforeach
            </dl>
        </section>
        <aside class="rounded-2xl bg-slate-950 p-6 text-white">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-300">Verification</p>
            <p class="mt-3 text-2xl font-black">{{ ucfirst($user->verification?->verification_status ?? 'Not submitted') }}</p>
            <p class="mt-3 text-sm leading-6 text-slate-300">Trading access depends on an active account and approved DELSU verification.</p>
            <a href="{{ route('kyc.show') }}" class="mt-6 inline-flex rounded-xl bg-white px-4 py-3 text-sm font-bold text-slate-950">Manage verification</a>
        </aside>
    </div>
</div>
@endsection