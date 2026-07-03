@php
    $isAdminRoute = request()->routeIs('wt.admin.*');
    $layout = $isAdminRoute ? 'wt.layouts.admin' : 'wt.layouts.user';
    $routePrefix = $isAdminRoute ? 'wt.admin' : 'wt.user';
@endphp

@extends($layout)

@section('title')
Handover WT
@endsection

@push('styles')
<style>
    .handover-language-toggle { display:inline-grid;grid-template-columns:repeat(2,minmax(0,1fr));align-items:center;padding:0;width:min(100%,280px);border-radius:999px;border:1px solid var(--border);background:var(--surface); }
    .handover-language-btn { border:0;border-radius:0;width:100%;padding:9px 12px;min-width:0;font-size:9px;font-weight:900;letter-spacing:.09em;text-transform:uppercase;color:var(--muted);background:transparent;line-height:1;transition:all .18s ease;cursor:pointer; }
    .handover-language-btn:first-child { border-top-left-radius:999px;border-bottom-left-radius:999px; }
    .handover-language-btn:last-child { border-top-right-radius:999px;border-bottom-right-radius:999px; }
    .handover-language-btn.is-active { color:#fff;background:var(--accent);box-shadow:0 4px 12px rgba(2,132,199,.25); }
    .handover-language-btn:not(.is-active):hover { color:var(--text);background:var(--body-bg); }
    .handover-terms-panel { border:1px solid var(--border);background:var(--body-bg); }
    .handover-terms-copy { display:none; }
    .handover-terms-copy.is-active { display:block; }
    .handover-terms-list { display:grid;gap:12px; }
    .handover-terms-item { border-radius:10px;border:1px solid var(--border);background:var(--surface);padding:14px 16px; }
    .handover-terms-item strong { color:var(--accent); }
    .handover-terms-sublist { margin-top:8px;padding-left:18px;list-style:lower-roman; }
    .handover-terms-sublist li + li { margin-top:4px; }
    .ho-card { background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px; }
    .ho-badge { display:inline-flex;padding:3px 10px;border-radius:20px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;border:1px solid; }
    .ho-meta { font-size:10px;font-weight:700;color:var(--muted);display:grid;gap:6px;margin-top:12px; }
    .ho-meta span { color:var(--text); }
    .ho-info-box { border:1px solid rgba(2,132,199,.2);background:rgba(2,132,199,.06);border-radius:8px;padding:12px 14px;font-size:11px;font-weight:700;color:var(--accent);margin-top:10px; }
    .ho-rep-panel, .ho-notyet-panel { display:none;border:1px solid var(--border);background:var(--body-bg);border-radius:8px;padding:12px;margin-top:8px; }
</style>
@endpush

@section('content')
@php
    $statusBadge = function ($request) {
        return match ($request->status) {
            'Pending Admin Approval', 'Pending IT Approval' => ['text' => 'Processing', 'bg' => 'rgba(245,158,11,.1)', 'color' => '#d97706', 'border' => 'rgba(245,158,11,.3)'],
            'Pending Executive Pickup', 'Approved' => ['text' => 'Ready To Collect', 'bg' => 'rgba(2,132,199,.1)', 'color' => '#0369a1', 'border' => 'rgba(2,132,199,.3)'],
            'Rejected' => ['text' => 'Rejected', 'bg' => 'rgba(239,68,68,.1)', 'color' => '#dc2626', 'border' => 'rgba(239,68,68,.3)'],
            default => ['text' => $request->status ?: 'Unknown', 'bg' => 'rgba(100,116,139,.1)', 'color' => '#475569', 'border' => 'rgba(100,116,139,.3)'],
        };
    };
@endphp

<div style="margin-bottom:18px">
    <div style="font-size:16px;font-weight:800;color:var(--text)">Handover WT</div>
    <p style="margin-top:4px;font-size:12px;color:var(--muted)">Respond to ICT pickup notifications for approved walkie talkie requests.</p>
</div>

@if(session('success'))
<div class="alert-success-custom mb-4"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-danger-custom mb-4"><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
@endif
@if($errors->any())
<div class="alert-danger-custom mb-4"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
@endif

<div class="table-card mb-4">
    <div class="table-card-header">
        <i class="fas fa-exchange-alt" style="color:var(--muted);font-size:15px"></i>
        <span class="table-card-title">Current Handover Status WT</span>
    </div>
    <div style="padding:16px 20px">
        <div class="row g-3">
            @forelse($statusRequests as $request)
            @php($badge = $statusBadge($request))
            <div class="col-md-6 col-xl-4">
                <div class="ho-card">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px">
                        <div style="min-width:0">
                            <p style="font-size:8px;font-weight:900;text-transform:uppercase;letter-spacing:.16em;color:var(--muted);margin:0">Request #{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</p>
                            <p style="margin-top:4px;font-size:12px;font-weight:900;color:var(--text);margin-bottom:0">{{ $request->event_name ?: 'Walkie Talkie Request' }}</p>
                        </div>
                        <span class="ho-badge" style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};border-color:{{ $badge['border'] }};flex-shrink:0">{{ $badge['text'] }}</span>
                    </div>
                    <div class="ho-meta">
                        <p>Request Date: <span>{{ $request->request_date ? \Carbon\Carbon::parse($request->request_date)->format('d M Y') : '-' }}</span></p>
                        <p>Radio ID: <span>{{ $request->radio_id ?: '-' }}</span></p>
                        <p>Serial No: <span>{{ $request->assigned_serial_number ?: '-' }}</span></p>
                        <p>Preferred Pickup: <span>{{ $request->requested_pickup_at ? \Carbon\Carbon::parse($request->requested_pickup_at)->format('d M Y H:i') : '-' }}</span></p>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12" style="text-align:center;padding:32px;color:var(--muted);font-size:11px;font-weight:700">No handover status records found.</div>
            @endforelse
        </div>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <i class="fas fa-bell" style="color:var(--muted);font-size:15px"></i>
        <span class="table-card-title">Pickup Notifications WT</span>
    </div>
    <div style="padding:8px 20px 16px">
        @forelse($pendingHandovers as $request)
        <div style="padding:16px 0;border-bottom:1px solid var(--border)">
            <div style="display:flex;flex-wrap:wrap;gap:16px;align-items:flex-start;justify-content:space-between">
                <div style="min-width:0;flex:1">
                    <p style="font-size:8px;font-weight:900;text-transform:uppercase;letter-spacing:.16em;color:var(--muted);margin:0">Request #{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</p>
                    <p style="margin-top:4px;font-size:13px;font-weight:900;color:var(--text);margin-bottom:0">{{ $request->full_name }}</p>
                    <div class="ho-meta">
                        <p>Radio ID: <span>{{ $request->radio_id ?: '-' }}</span></p>
                        <p>Serial No: <span>{{ $request->assigned_serial_number ?: '-' }}</span></p>
                        <p>Department: <span>{{ $request->department ?: '-' }}</span></p>
                        <p>Location: <span>{{ $request->location ?: '-' }}</span></p>
                        <p>Preferred Pickup: <span>{{ $request->requested_pickup_at ? \Carbon\Carbon::parse($request->requested_pickup_at)->format('d M Y H:i') : '-' }}</span></p>
                    </div>
                    <div class="ho-info-box">Pick up walkie talkie at ICT Department after ICT approval. Please sign the pickup form when the WT is handed over.</div>
                </div>

                <div style="display:flex;flex-direction:column;gap:8px;min-width:220px;flex-shrink:0">
                    <a href="{{ route($routePrefix . '.handover.pickup', $request->id) }}" class="btn-primary-custom" style="background:#16a34a;justify-content:center">
                        <i class="fa-solid fa-pen-to-square"></i> Pickup
                    </a>
                </div>

                <form action="{{ route($routePrefix . '.handover.store') }}" method="POST" style="display:none;flex-direction:column;gap:8px;min-width:220px;flex-shrink:0" data-pickup-form>
                    @csrf
                    <input type="hidden" name="access_request_id" value="{{ $request->id }}">
                    <button type="submit" name="pickup_response" value="yes" class="btn-primary-custom" style="background:#16a34a;justify-content:center">
                        <i class="fa-solid fa-check"></i> Ready To Pickup
                    </button>
                    <button type="button" class="btn-secondary-custom" style="justify-content:center" data-representative-toggle>
                        <i class="fa-solid fa-user"></i> Representative
                    </button>
                    <div class="ho-rep-panel" data-representative-panel>
                        <label class="form-label">Representative Pickup Name</label>
                        <input type="text" name="representative_name" class="form-control mb-2" placeholder="Name of person picking up" data-representative-input>
                        <button type="submit" name="pickup_response" value="representative" class="btn-primary-custom" style="width:100%;justify-content:center">Submit Representative</button>
                    </div>
                    <button type="button" class="btn-secondary-custom" style="justify-content:center" data-not-yet-toggle>
                        <i class="fa-solid fa-clock"></i> Not Yet
                    </button>
                    <div class="ho-notyet-panel" data-not-yet-panel>
                        <label class="form-label">When can you collect?</label>
                        <input type="text" name="pickup_collection_note" class="form-control mb-2" placeholder="E.g. Tomorrow 10 AM / 20 May after lunch" data-not-yet-input>
                        <button type="submit" name="pickup_response" value="not_yet" class="btn-secondary-custom" style="width:100%;justify-content:center">Submit Not Yet</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:40px;color:var(--muted);font-size:13px">No handover notifications are waiting for you right now.</div>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-pickup-form]').forEach(function (form) {
        const toggle = form.querySelector('[data-representative-toggle]');
        const panel = form.querySelector('[data-representative-panel]');
        const input = form.querySelector('[data-representative-input]');
        const notYetToggle = form.querySelector('[data-not-yet-toggle]');
        const notYetPanel = form.querySelector('[data-not-yet-panel]');
        const notYetInput = form.querySelector('[data-not-yet-input]');

        if (toggle && panel && input) {
            toggle.addEventListener('click', function () {
                panel.style.display = panel.style.display === 'block' ? 'none' : 'block';
                if (panel.style.display === 'block') { input.focus(); }
            });
        }

        if (notYetToggle && notYetPanel && notYetInput) {
            notYetToggle.addEventListener('click', function () {
                notYetPanel.style.display = notYetPanel.style.display === 'block' ? 'none' : 'block';
                if (notYetPanel.style.display === 'block') { notYetInput.focus(); }
            });
        }
    });
});
</script>
@endpush
