@extends('wt.layouts.user')

@section('title', 'Dashboard')

@section('content')
<div class="hd-banner">
    <div>
        <div class="hd-greeting">Welcome, {{ Auth::guard('wt')->user()->username ?? 'User' }}</div>
        <div class="hd-date">WT System &middot; {{ strtoupper(str_replace('_', ' ', Auth::guard('wt')->user()->role)) }}</div>
    </div>
    <div class="hd-clock">{{ now()->format('H:i') }}</div>
</div>

<div class="page-header">
    <div>
        <h2>User Workspace</h2>
        <p class="page-subtitle">Create requests, return units, and report faulty walkie talkie assets.</p>
    </div>
</div>

<div class="hd-kpi-row">
    <a href="{{ route('wt.user.returns.create') }}" class="hd-kpi hd-kpi-blue">
        <div class="hd-kpi-icon">
            <i class="fa-solid fa-rotate-left"></i>
        </div>
        <div class="hd-kpi-body">
            <div class="hd-kpi-val">Return</div>
            <div class="hd-kpi-lbl">Return Unit</div>
        </div>
        <span class="hd-kpi-link">Open &rarr;</span>
    </a>

    <a href="{{ route('wt.user.damages.create') }}" class="hd-kpi hd-kpi-red">
        <div class="hd-kpi-icon">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="hd-kpi-body">
            <div class="hd-kpi-val">Faulty</div>
            <div class="hd-kpi-lbl">Report Unit</div>
        </div>
        <span class="hd-kpi-link">Open &rarr;</span>
    </a>

    <a href="{{ route('wt.user.requests.status') }}" class="hd-kpi hd-kpi-green">
        <div class="hd-kpi-icon">
            <i class="fa-solid fa-list-ul"></i>
        </div>
        <div class="hd-kpi-body">
            <div class="hd-kpi-val">Status</div>
            <div class="hd-kpi-lbl">Request Tracking</div>
        </div>
        <span class="hd-kpi-link">View &rarr;</span>
    </a>

    <a href="{{ route('wt.user.profile') }}" class="hd-kpi hd-kpi-amber">
        <div class="hd-kpi-icon">
            <i class="fa-solid fa-user-circle"></i>
        </div>
        <div class="hd-kpi-body">
            <div class="hd-kpi-val">Profile</div>
            <div class="hd-kpi-lbl">My Account</div>
        </div>
        <span class="hd-kpi-link">View &rarr;</span>
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3>Inventory Access</h3>
    </div>
    <div class="p-5">
        <p class="text-sm font-medium leading-6 text-slate-500 dark:text-slate-300">
            Inventory listing is restricted to ICT. Use the menu on the left to create requests, return units, and report faulty units.
        </p>
    </div>
</div>
@endsection

