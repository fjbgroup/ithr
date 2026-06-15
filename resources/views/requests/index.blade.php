@extends('layouts.app')

@section('title', 'Update Requests')

@section('content')
<div class="page-header">
    <div>
        <h2>Update Requests Inbox</h2>
        <p class="page-subtitle">Review and respond to staff update requests</p>
    </div>
</div>

<div class="filter-bar">
    <span class="filter-label">Filter:</span>
    <div class="filter-tabs">
        @foreach ($validFilters as $f)
        <a href="{{ route('requests.index', ['filter' => $f]) }}" class="filter-tab {{ $filter === $f ? 'active' : '' }}">
            {{ $f }}
            <span class="filter-count">{{ $counts[$f] }}</span>
        </a>
        @endforeach
    </div>
</div>

@if ($requests->isEmpty())
<div class="card">
    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path></svg>
        <p>No {{ strtolower($filter) }} requests.</p>
    </div>
</div>
@else
<div class="requests-list">
    @foreach ($requests as $req)
    <div class="request-card {{ strtolower($req->status) }}">
        <div class="request-header">
            <div class="request-meta">
                <div class="req-avatar">{{ strtoupper(substr($req->requester_name, 0, 1)) }}</div>
                <div>
                    <strong>{{ $req->requester_name }}</strong>
                    <span class="req-type-badge">{{ $req->record_type }}</span>
                </div>
            </div>
            <div class="request-status-wrap">
                <span class="status-pill status-{{ strtolower($req->status) }}">{{ $req->status }}</span>
                <span class="req-time">{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y, H:i') }}</span>
            </div>
        </div>
        
        <div class="request-body">
            <div class="req-reference">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg>
                Record: <strong>{{ $req->record_reference }}</strong>
            </div>
            
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
        
        @if ($req->status === 'Pending')
        <div class="request-footer" style="flex-direction:column; align-items:stretch; gap:.75rem;">
            <div>
                <label style="font-size:.78rem;font-weight:600;color:var(--text-muted);display:block;margin-bottom:.35rem;">Admin Note <span style="font-weight:400;color:var(--text-muted);">(optional — visible to requester)</span></label>
                <textarea id="note-{{ $req->id }}" rows="2" placeholder="e.g. Updated. Please verify in the system." style="width:100%;padding:.5rem .65rem;border:1px solid var(--border);border-radius:6px;font-size:.85rem;resize:vertical;font-family:inherit;background:var(--surface);color:var(--text);"></textarea>
            </div>
            <div style="display:flex;gap:.5rem;">
                <form method="POST" action="{{ route('requests.resolve', $req->id) }}" style="display:inline" onsubmit="document.getElementById('admin_note_resolve_{{ $req->id }}').value = document.getElementById('note-{{ $req->id }}').value;">
                    @csrf
                    <input type="hidden" name="admin_note" id="admin_note_resolve_{{ $req->id }}" value="">
                    <button type="submit" class="btn btn-primary btn-sm">Mark Resolved</button>
                </form>
                
                <form method="POST" action="{{ route('requests.dismiss', $req->id) }}" style="display:inline" onsubmit="document.getElementById('admin_note_dismiss_{{ $req->id }}').value = document.getElementById('note-{{ $req->id }}').value; return confirm('Are you sure you want to dismiss this request?');">
                    @csrf
                    <input type="hidden" name="admin_note" id="admin_note_dismiss_{{ $req->id }}" value="">
                    <button type="submit" class="btn btn-light btn-sm" style="color:var(--danger);">Dismiss</button>
                </form>
            </div>
        </div>
        @else
            @if ($req->admin_note)
            <div style="padding:.75rem 1.25rem; background:#f8fafc; border-top:1px dashed var(--border); font-size:.82rem;">
                <strong style="color:var(--text);font-weight:600;">Admin Note:</strong>
                <span style="color:var(--text-muted);">{{ $req->admin_note }}</span>
            </div>
            @endif
        @endif
    </div>
    @endforeach
</div>
@endif

@endsection
