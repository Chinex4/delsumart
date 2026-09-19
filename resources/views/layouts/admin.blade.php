<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration') — DelsuMart</title>@vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
<div x-data="{ sidebar:false, collapsed:false }" class="min-h-screen lg:flex">
    <div x-cloak x-show="sidebar" @click="sidebar=false" class="fixed inset-0 z-40 bg-slate-950/60 lg:hidden"></div>
    <aside :class="collapsed ? 'lg:w-20' : 'lg:w-72'" class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full bg-[#07152f] text-white transition-all duration-300 lg:static lg:translate-x-0" :class="{ 'translate-x-0': sidebar }">
        <div class="flex h-20 items-center justify-between border-b border-white/10 px-5"><a href="{{ route('admin.dashboard') }}" class="font-black">delsu<span class="text-blue-400">mart</span> <span x-show="!collapsed" class="ml-1 text-xs text-slate-400">ADMIN</span></a><button @click="sidebar=false" class="lg:hidden">✕</button></div>
        <nav class="space-y-1 p-4 text-sm font-semibold">
            @foreach ([['admin.dashboard','Overview','⌂'],['admin.students','Students','◎'],['admin.verifications','KYC reviews','✓'],['admin.listings','Listings','▦'],['admin.transactions','Transactions','↔'],['admin.disputes','Disputes','!'],['admin.flags','Fraud monitoring','△'],['admin.audits','Audit logs','≡']] as [$route,$label,$icon])
                <a title="{{ $label }}" href="{{ route($route) }}" class="flex items-center gap-3 rounded-xl px-3 py-3 {{ request()->routeIs($route) || request()->routeIs($route.'.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-white/10">{{ $icon }}</span><span x-show="!collapsed">{{ $label }}</span></a>
            @endforeach
        </nav>
        <div class="absolute bottom-0 w-full border-t border-white/10 p-4"><a href="{{ route('home') }}" class="mb-2 block rounded-xl px-3 py-2 text-sm text-slate-300 hover:bg-white/10">← Public site</a><form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full rounded-xl px-3 py-2 text-left text-sm text-slate-300 hover:bg-white/10">Sign out</button></form></div>
    </aside>
    <div class="min-w-0 flex-1">
        <header class="sticky top-0 z-30 flex h-20 items-center border-b bg-white/90 px-4 backdrop-blur sm:px-6"><button @click="sidebar=true" class="rounded-xl border p-2.5 lg:hidden" aria-label="Open admin sidebar">☰</button><button @click="collapsed=!collapsed" class="hidden rounded-xl border p-2.5 lg:block" aria-label="Toggle admin sidebar">☰</button><div class="ml-auto text-right"><p class="text-sm font-bold">{{ auth()->user()->name }}</p><p class="text-xs text-slate-500">Administrator</p></div></header>
        @if(session('success'))<div class="mx-4 mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900">{{ session('success') }}</div>@endif
        <main class="p-4 sm:p-6 lg:p-8">@yield('content')</main>
    </div>
</div>
</body></html>
