@extends(request()->routeIs('wt.admin.*') ? 'wt.layouts.admin' : 'wt.layouts.user')

@section('title', 'Policies')
@section('page_title', 'Policies')

@section('content')

<div style="margin-bottom:18px">
  <div style="font-size:16px;font-weight:800;color:var(--text)">Policies</div>
  <p style="margin-top:4px;font-size:12px;color:var(--muted)">Walkie Talkie Usage Policies &amp; Terms.</p>
</div>

<div class="table-card">
  <div style="padding:24px">
    @include('wt.partials.walkie-policy-content')
  </div>
</div>

@endsection
