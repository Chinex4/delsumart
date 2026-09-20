@extends('layouts.dashboard')
@section('dashboard-mode', 'admin')
@section('workspace-name', 'Administration')
@section('sidebar-navigation')
    <a class="side-link {{ request()->routeIs('admin.dashboard', 'admin.dashboard.*') ? 'active' : '' }}"
        href="{{ route('admin.dashboard') }}" title="Overview" @if (request()->routeIs('admin.dashboard', 'admin.dashboard.*')) aria-current="page" @endif>
        <x-icon name="grid" />
        <span class="side-label">Overview</span>
    </a>
    <a class="side-link {{ request()->routeIs('admin.students', 'admin.students.*') ? 'active' : '' }}"
        href="{{ route('admin.students') }}" title="Students" @if (request()->routeIs('admin.students', 'admin.students.*')) aria-current="page" @endif>
        <x-icon name="users" />
        <span class="side-label">Students</span>
    </a>
    <a class="side-link {{ request()->routeIs('admin.verifications', 'admin.verifications.*') ? 'active' : '' }}"
        href="{{ route('admin.verifications') }}" title="KYC reviews"
        @if (request()->routeIs('admin.verifications', 'admin.verifications.*')) aria-current="page" @endif>
        <x-icon name="shield" />
        <span class="side-label">KYC
            reviews</span>
    </a>
    <a class="side-link {{ request()->routeIs('admin.listings', 'admin.listings.*') ? 'active' : '' }}"
        href="{{ route('admin.listings') }}" title="Listings" @if (request()->routeIs('admin.listings', 'admin.listings.*')) aria-current="page" @endif>
        <x-icon name="bag" />
        <span class="side-label">Listings</span>
    </a>
    <a class="side-link {{ request()->routeIs('admin.transactions', 'admin.transactions.*') ? 'active' : '' }}"
        href="{{ route('admin.transactions') }}" title="Transactions"
        @if (request()->routeIs('admin.transactions', 'admin.transactions.*')) aria-current="page" @endif>
        <x-icon name="wallet" />
        <span class="side-label">Transactions</span>
    </a>
    <a class="side-link {{ request()->routeIs('admin.disputes', 'admin.disputes.*') ? 'active' : '' }}"
        href="{{ route('admin.disputes') }}" title="Disputes" @if (request()->routeIs('admin.disputes', 'admin.disputes.*')) aria-current="page" @endif>
        <x-icon name="message" />
        <span class="side-label">Disputes</span>
    </a>
    <a class="side-link {{ request()->routeIs('admin.flags', 'admin.flags.*') ? 'active' : '' }}"
        href="{{ route('admin.flags') }}" title="Fraud monitoring"
        @if (request()->routeIs('admin.flags', 'admin.flags.*')) aria-current="page" @endif>
        <x-icon name="flag" />
        <span class="side-label">Fraud
            monitoring</span>
    </a>
    <a class="side-link {{ request()->routeIs('admin.audits', 'admin.audits.*') ? 'active' : '' }}"
        href="{{ route('admin.audits') }}" title="Audit logs" @if (request()->routeIs('admin.audits', 'admin.audits.*')) aria-current="page" @endif>
        <x-icon name="file" />
        <span class="side-label">Audit
            logs</span>
    </a>
    <a class="side-link {{ request()->routeIs('admin.payouts') ? 'active' : '' }}" href="{{ route('admin.payouts') }}"
        title="Payouts" @if (request()->routeIs('admin.payouts')) aria-current="page" @endif>
        <x-icon name="wallet" />
        <span class="side-label">Payouts</span>
    </a>
@endsection
