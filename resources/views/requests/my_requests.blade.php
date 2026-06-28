@extends('layouts.app')

@section('title', 'My Requests')

@section('content')
<div class="page-header">
    <div>
        <h2>My Requests</h2>
        <p class="page-subtitle">Track the status of your submitted update requests</p>
    </div>
</div>

<div class="filter-bar">
    <span class="filter-label">Filter:</span>
    <div class="filter-tabs">
        @foreach ($validFilters as $f)
        <a href="{{ route('my-requests', ['filter' => $f]) }}" class="filter-tab {{ $filter === $f ? 'active' : '' }}">
            {{ $f }}
            <span class="filter-count">{{ $counts[$f] }}</span>
        </a>
        @endforeach
    </div>
</div>

@if ($requests->isEmpty())
<div class="card">
    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        <p>You have no {{ $filter !== 'All' ? strtolower($filter) : '' }} requests.</p>
    </div>
</div>
@else
<div class="requests-list">
    @foreach ($requests as $req)
    <div class="request-card {{ strtolower($req->status) }}">
        <div class="request-header">
            <div class="request-meta">
                <div class="req-avatar">{{ strtoupper(substr($req->record_type, 0, 1)) }}</div>
                <div>
                    <strong>{{ $req->record_type }}</strong>
                    <span class="req-type-badge">{{ $req->record_reference }}</span>
                </div>
            </div>
            <div class="request-status-wrap">
                <span class="status-pill status-{{ strtolower($req->status) }}">{{ $req->status }}</span>
                <span class="req-time">{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y, H:i') }}</span>
            </div>
        </div>

        <div class="request-body">
            @php
                $msgBody = $req->message;
                $fieldLine = '';
                if (str_starts_with($msgBody, 'Fields to update:')) {
                    $lines = explode("\n", $msgBody, 3);
                    $fieldLine = trim(str_replace('Fields to update:', '', $lines[0]));
                    $msgBody = trim($lines[2] ?? '');
                }
            @endphp
            
            @if ($fieldLine)
            <div style="margin:.5rem 0 .6rem; display:flex; flex-wrap:wrap; gap:.35rem; align-items:center;">
                <span style="font-size:.75rem;color:var(--text-muted);font-weight:600;">Fields:</span>
                @foreach (explode(',', $fieldLine) as $f)
                <span style="display:inline-block;background:var(--primary-soft,#ede9fe);color:var(--primary);font-size:.75rem;font-weight:600;padding:.15rem .55rem;border-radius:999px;">{{ trim($f) }}</span>
                @endforeach
            </div>
            @endif
            
            <div class="req-message">{!! nl2br(e($msgBody)) !!}</div>
        </div>

        <!-- Status timeline -->
        <div style="padding:.75rem 1.25rem; border-top:1px solid var(--border); display:flex; align-items:center; gap:1rem; font-size:.8rem; flex-wrap:wrap;">
            <!-- Submitted -->
            <div style="display:flex;align-items:center;gap:.4rem;">
                <span style="width:20px;height:20px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </span>
                <span style="color:var(--text-muted);">Submitted <strong style="color:var(--text);">{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y') }}</strong></span>
            </div>
            
            @if ($req->status !== 'Pending')
            <!-- Line -->
            <div style="flex-grow:1;height:2px;background:var(--border);border-radius:2px;"></div>
            
            <!-- Resolved/Dismissed -->
            <div style="display:flex;align-items:center;gap:.4rem;">
                @if ($req->status === 'Resolved')
                <span style="width:20px;height:20px;border-radius:50%;background:var(--success);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </span>
                <span style="color:var(--text-muted);">Resolved <strong style="color:var(--text);">{{ \Carbon\Carbon::parse($req->updated_at)->format('d M Y') }}</strong></span>
                @else
                <span style="width:20px;height:20px;border-radius:50%;background:var(--danger);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </span>
                <span style="color:var(--text-muted);">Dismissed <strong style="color:var(--text);">{{ \Carbon\Carbon::parse($req->updated_at)->format('d M Y') }}</strong></span>
                @endif
            </div>
            @endif
        </div>

        @if ($req->status !== 'Pending' && $req->admin_note)
        <div style="padding:.75rem 1.25rem; background:var(--bg); border-top:1px dashed var(--border); font-size:.82rem;">
            <strong style="color:var(--text);font-weight:600;">Admin Note:</strong>
            <span style="color:var(--text-muted);">{{ $req->admin_note }}</span>
        </div>
        @endif
    </div>
    @endforeach
</div>
@endif

@endsection
