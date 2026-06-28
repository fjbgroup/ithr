@extends('wt.layouts.user')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@push('styles')
<style>
.ud-stat{display:flex;align-items:center;gap:18px;text-decoration:none;background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:22px 24px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 14px rgba(0,0,0,.07);transition:box-shadow .2s,transform .2s;height:100%}
.ud-stat:hover{box-shadow:0 6px 24px rgba(0,0,0,.15);transform:translateY(-2px)}
.ud-stat-icon{width:46px;height:46px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
.ud-stat-val{font-size:22px;font-weight:800;color:var(--text);line-height:1;font-family:'Inter',sans-serif}
.ud-stat-lbl{font-size:12px;color:var(--muted);margin-top:4px;font-weight:500}
</style>
@endpush

@section('content')

<div style="margin-bottom:24px">
  <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:4px">Welcome back</div>
  <div style="font-size:20px;font-weight:800;color:var(--text)">{{ Auth::guard('wt')->user()->username ?? 'User' }}</div>
  <div style="font-size:12px;color:var(--muted);margin-top:2px">WT System &middot; {{ strtoupper(str_replace('_', ' ', Auth::guard('wt')->user()->wt_role ?? 'user')) }}</div>
</div>

<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3">
    <a href="{{ route('wt.user.returns.create') }}" class="ud-stat">
      <div class="ud-stat-icon" style="background:rgba(2,132,199,.12);color:#0284c7"><i class="fa-solid fa-rotate-left"></i></div>
      <div>
        <div class="ud-stat-val">Return</div>
        <div class="ud-stat-lbl">Return Unit</div>
      </div>
    </a>
  </div>
  <div class="col-sm-6 col-xl-3">
    <a href="{{ route('wt.user.damages.create') }}" class="ud-stat">
      <div class="ud-stat-icon" style="background:rgba(239,68,68,.12);color:#dc2626"><i class="fa-solid fa-triangle-exclamation"></i></div>
      <div>
        <div class="ud-stat-val">Faulty</div>
        <div class="ud-stat-lbl">Report Unit</div>
      </div>
    </a>
  </div>
  <div class="col-sm-6 col-xl-3">
    <a href="{{ route('wt.user.requests.status') }}" class="ud-stat">
      <div class="ud-stat-icon" style="background:rgba(34,197,94,.12);color:#16a34a"><i class="fa-solid fa-list-ul"></i></div>
      <div>
        <div class="ud-stat-val">Status</div>
        <div class="ud-stat-lbl">Request Tracking</div>
      </div>
    </a>
  </div>
  <div class="col-sm-6 col-xl-3">
    <a href="{{ route('wt.user.profile') }}" class="ud-stat">
      <div class="ud-stat-icon" style="background:rgba(245,158,11,.12);color:#d97706"><i class="fa-solid fa-user-circle"></i></div>
      <div>
        <div class="ud-stat-val">Profile</div>
        <div class="ud-stat-lbl">My Account</div>
      </div>
    </a>
  </div>
</div>

<div class="table-card">
  <div class="table-card-header">
    <i class="fas fa-box" style="color:var(--muted);font-size:15px"></i>
    <span class="table-card-title">Inventory Access</span>
  </div>
  <div style="padding:20px 22px">
    <p style="font-size:13px;color:var(--muted);line-height:1.6">Inventory listing is restricted to ICT. Use the menu on the left to create requests, return units, and report faulty units.</p>
  </div>
</div>

@endsection
