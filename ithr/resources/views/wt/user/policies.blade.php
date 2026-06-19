@extends(request()->routeIs('wt.admin.*') ? 'wt.layouts.admin' : 'wt.layouts.user')

@section('title', 'Policies')
@section('page_title', 'Policies')

@section('content')

<div style="margin-bottom:18px">
  <div style="font-size:16px;font-weight:800;color:var(--text)">Policies</div>
  <p style="margin-top:4px;font-size:12px;color:var(--muted)">Walkie Talkie Usage Policies &amp; Terms.</p>
</div>

<div class="table-card">
  <div style="padding:48px 24px;text-align:center">
    <i class="fas fa-file-contract" style="font-size:32px;color:var(--border);margin-bottom:16px"></i>
    <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text);margin-bottom:8px">Content Under Development</div>
    <p style="font-size:11px;color:var(--muted);text-transform:uppercase;font-weight:600;letter-spacing:.1em">This page will be updated with the latest policies soon.</p>
  </div>
</div>

@endsection

