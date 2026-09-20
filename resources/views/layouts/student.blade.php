@extends('layouts.dashboard')
@section('dashboard-mode', 'student')
@section('workspace-name', 'Student workspace')
@section('sidebar-navigation')
    <a class="side-link {{ request()->routeIs('dashboard', 'dashboard.*') ? 'active' : '' }}" href="{{ route('dashboard') }}"
        title="Overview" @if (request()->routeIs('dashboard', 'dashboard.*')) aria-current="page" @endif>
        <x-icon name="grid" />
        <span class="side-label">Overview</span>
    </a>
    <a class="side-link {{ request()->routeIs('listings.index', 'listings.index.*') ? 'active' : '' }}"
        href="{{ route('listings.index') }}" title="Marketplace" @if (request()->routeIs('listings.index', 'listings.index.*')) aria-current="page" @endif>
        <x-icon name="search" />
        <span class="side-label">Marketplace</span>
    </a>
    <a class="side-link {{ request()->routeIs('account.listings', 'account.listings.*') ? 'active' : '' }}"
        href="{{ route('account.listings') }}" title="My listings"
        @if (request()->routeIs('account.listings', 'account.listings.*')) aria-current="page" @endif>
        <x-icon name="bag" />
        <span class="side-label">My
            listings</span>
    </a>
    <a class="side-link {{ request()->routeIs('listings.create', 'listings.create.*') ? 'active' : '' }}"
        href="{{ route('listings.create') }}" title="Create listing"
        @if (request()->routeIs('listings.create', 'listings.create.*')) aria-current="page" @endif>
        <x-icon name="plus" />
        <span class="side-label">Create listing</span>
    </a>
    <a class="side-link {{ request()->routeIs('account.purchases', 'account.purchases.*') ? 'active' : '' }}"
        href="{{ route('account.purchases') }}" title="Purchases"
        @if (request()->routeIs('account.purchases', 'account.purchases.*')) aria-current="page" @endif>
        <x-icon name="wallet" />
        <span class="side-label">Purchases</span>
    </a>
    <a class="side-link {{ request()->routeIs('account.sales', 'account.sales.*') ? 'active' : '' }}"
        href="{{ route('account.sales') }}" title="Sales" @if (request()->routeIs('account.sales', 'account.sales.*')) aria-current="page" @endif>
        <x-icon name="bag" />
        <span class="side-label">Sales</span>
    </a>
    <a class="side-link {{ request()->routeIs('account.transactions', 'account.transactions.*') ? 'active' : '' }}"
        href="{{ route('account.transactions') }}" title="Transactions"
        @if (request()->routeIs('account.transactions', 'account.transactions.*')) aria-current="page" @endif>
        <x-icon name="file" />
        <span class="side-label">Transactions</span>
    </a>
    <a class="side-link {{ request()->routeIs('kyc.show', 'kyc.show.*') ? 'active' : '' }}" href="{{ route('kyc.show') }}"
        title="Verification" @if (request()->routeIs('kyc.show', 'kyc.show.*')) aria-current="page" @endif>
        <x-icon name="shield" />
        <span class="side-label">Verification</span>
    </a>
    <a class="side-link {{ request()->routeIs('account.disputes', 'account.disputes.*') ? 'active' : '' }}"
        href="{{ route('account.disputes') }}" title="Disputes"
        @if (request()->routeIs('account.disputes', 'account.disputes.*')) aria-current="page" @endif>
        <x-icon name="message" />
        <span class="side-label">Disputes</span>
    </a>
    <a class="side-link {{ request()->routeIs('account', 'account.*') ? 'active' : '' }}" href="{{ route('account') }}"
        title="My account" @if (request()->routeIs('account', 'account.*')) aria-current="page" @endif>
        <x-icon name="user" />
        <span class="side-label">My
            account</span>
    </a>
    <a class="side-link {{ request()->routeIs('payouts.index') ? 'active' : '' }}" href="{{ route('payouts.index') }}"
        title="Payouts" @if (request()->routeIs('payouts.index')) aria-current="page" @endif>
        <x-icon name="wallet" />
        <span class="side-label">Payouts</span>
    </a>
@endsection
