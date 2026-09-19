<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student workspace') — DelsuMart</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">
<div x-data="{ sidebar: false, collapsed: false }" class="min-h-screen lg:flex">
    <div x-cloak x-show="sidebar" @click="sidebar=false" class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden"></div>
    <aside :class="collapsed ? 'lg:w-20' : 'lg:w-72'" class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full border-r border-white/10 bg-slate-950 text-white transition-all duration-300 lg:static lg:translate-x-0" :class="{ 'translate-x-0': sidebar }">
        <div class="flex h-20 items-center justify-between border-b border-white/10 px-5">
            <a href="{{ route('home') }}" class="text-xl font-black">delsu<span class="text-blue-400">mart</span></a>
            <button @click="sidebar=false" class="rounded-lg p-2 lg:hidden" aria-label="Close sidebar">✕</button>
        </div>
        <nav class="space-y-1 p-4 text-sm font-semibold">
            @foreach ([
                ['dashboard','Overview','⌂'], ['listings.index','Marketplace','⌕'], ['account.listings','My listings','▦'],
                ['listings.create','Create listing','＋'], ['account.purchases','Purchases','↓'], ['account.sales','Sales','↑'],
                ['account.transactions','Transactions','↔'], ['account.disputes','Disputes','!'], ['kyc.show','Verification','✓'],
                ['account','Account','○']
            ] as [$route,$label,$icon])
                <a href="{{ route($route) }}" title="{{ $label }}" class="flex items-center gap-3 rounded-xl px-3 py-3 transition {{ request()->routeIs($route) ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-white/10">{{ $icon }}</span>
                    <span x-show="!collapsed" class="truncate">{{ $label }}</span>
                </a>
            @endforeach
        </nav>
        <div class="absolute bottom-0 w-full border-t border-white/10 p-4">
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold text-slate-300 hover:bg-white/10 hover:text-white">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-white/10">↗</span><span x-show="!collapsed">Sign out</span>
                </button>
            </form>
        </div>
    </aside>
    <div id="dashboard-workspace" class="min-w-0 flex-1">
        <header class="sticky top-0 z-30 flex h-20 items-center gap-3 border-b bg-white/90 px-4 backdrop-blur sm:px-6">
            <button @click="sidebar=true" class="rounded-xl border border-slate-200 p-2.5 lg:hidden" aria-label="Open sidebar">☰</button>
            <button @click="collapsed=!collapsed" class="hidden rounded-xl border border-slate-200 p-2.5 lg:block" aria-label="Toggle sidebar">☰</button>
            <div class="ml-auto text-right">
                <p class="text-sm font-bold">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-500">{{ auth()->user()->matric_no }}</p>
            </div>
            <div class="grid h-10 w-10 place-items-center rounded-full bg-blue-100 font-black text-blue-800">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
        </header>
        @if(session('success'))<div class="mx-4 mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900 sm:mx-6">{{ session('success') }}</div>@endif
        @if(session('warning'))<div class="mx-4 mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 sm:mx-6">{{ session('warning') }}</div>@endif
        <main class="p-4 sm:p-6 lg:p-8">@yield('content')</main>
    </div>
</div>
</body>
</html>
