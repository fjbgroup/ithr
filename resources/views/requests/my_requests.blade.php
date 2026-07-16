@extends('layouts.app')

@section('title', 'My Requests')

@section('content')
<div class="page-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
    <div>
        <h2>My Requests</h2>
        <p class="page-subtitle">Track the status of your submitted update requests</p>
    </div>
    <button class="btn btn-primary" onclick="document.getElementById('addRequestModal').style.display='flex';" style="display:flex;align-items:center;gap:.5rem;height:fit-content;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Add Request
    </button>
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

<!-- Add Request Modal -->
<div class="modal" id="addRequestModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div class="modal-box" style="background:#fff; border-radius:14px; width:100%; max-width:520px; box-shadow:0 10px 25px rgba(0,0,0,0.1); max-height:90vh; overflow-y:auto;">
        <div class="modal-header ur-modal-header" style="background: var(--primary); padding: 1.25rem 1.5rem; border-radius: 14px 14px 0 0; display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex;align-items:center;gap:.75rem;">
                <div class="ur-header-icon" style="width: 34px; height: 34px; border-radius: 9px; background: rgba(255,255,255,.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </div>
                <div>
                    <h3 style="color:white;margin:0;font-size:.95rem;">New Update Request</h3>
                </div>
            </div>
            <button type="button" class="modal-close" onclick="document.getElementById('addRequestModal').style.display='none';" style="color:rgba(255,255,255,.6);font-size:1.4rem; background: none; border: none; cursor: pointer;">×</button>
        </div>

        <form action="{{ route('requests.store') }}" method="POST">
            @csrf
            <input type="hidden" name="record_id" value="{{ Auth::id() }}">
            
            <div class="modal-body" style="display: flex; flex-direction: column; gap: 1rem; padding: 1.5rem;">
                
                <div class="form-group">
                    <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.3rem;">Information Category *</label>
                    <select name="record_type" id="recordTypeSelect" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px; background: var(--form-input-bg); color: var(--form-input-color);" onchange="updateFieldOptions()">
                        <option value="">Select category...</option>
                        <option value="Staff Data">Profile Info</option>
                        <option value="Training Record">Training Info</option>
                        <option value="Family Information">Family Info</option>
                    </select>
                </div>

                <div class="form-group" id="fieldsSelectionGroup" style="display:none;">
                    <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.3rem;">Fields to Update (Select all that apply)</label>
                    <div id="dynamicFieldsContainer" style="display:flex; flex-wrap:wrap; gap:.5rem; margin-top:.3rem;">
                        <!-- Checkboxes inserted here via JS -->
                    </div>
                </div>

                <div class="form-group">
                    <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.3rem;">Update Details *</label>
                    <textarea name="message" class="form-control" required rows="4" placeholder="Describe the updates you need..." style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px; background: var(--form-input-bg); color: var(--form-input-color);"></textarea>
                </div>
            </div>

            <div class="modal-footer" style="padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.5rem; border-top: 1px solid var(--border);">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('addRequestModal').style.display='none';">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<script>
const categoryFields = {
    'Staff Data': ['Name', 'Email', 'Phone Number', 'Gender', 'Date of Birth', 'IC Number', 'Address', 'Profile Picture'],
    'Training Record': ['Course Name', 'Completion Date', 'Certification', 'Trainer Name', 'Institution', 'Results'],
    'Family Information': ['Spouse Details', 'Children Details', 'Emergency Contact', 'Beneficiary', 'Relationship']
};

function updateFieldOptions() {
    const type = document.getElementById('recordTypeSelect').value;
    const container = document.getElementById('dynamicFieldsContainer');
    const group = document.getElementById('fieldsSelectionGroup');
    
    container.innerHTML = '';
    
    if (type && categoryFields[type]) {
        group.style.display = 'block';
        categoryFields[type].forEach(field => {
            const label = document.createElement('label');
            label.style.display = 'flex';
            label.style.alignItems = 'center';
            label.style.gap = '0.35rem';
            label.style.fontSize = '0.78rem';
            label.style.background = 'var(--bg, #f3f4f6)';
            label.style.padding = '0.3rem 0.6rem';
            label.style.borderRadius = '6px';
            label.style.border = '1px solid var(--border, #e5e7eb)';
            label.style.cursor = 'pointer';
            label.style.userSelect = 'none';
            label.style.color = 'var(--text, #1f2937)';
            
            const cb = document.createElement('input');
            cb.type = 'checkbox';
            cb.name = 'fields[]';
            cb.value = field;
            cb.style.margin = '0';
            
            // Add a small hover effect for better UX
            label.onmouseover = function() { this.style.borderColor = 'var(--primary, #6366f1)'; };
            label.onmouseout = function() { if(!cb.checked) this.style.borderColor = 'var(--border, #e5e7eb)'; };
            cb.onchange = function() {
                label.style.borderColor = this.checked ? 'var(--primary, #6366f1)' : 'var(--border, #e5e7eb)';
                label.style.background = this.checked ? 'rgba(99, 102, 241, 0.05)' : 'var(--bg, #f3f4f6)';
            };

            label.appendChild(cb);
            label.appendChild(document.createTextNode(field));
            container.appendChild(label);
        });
    } else {
        group.style.display = 'none';
    }
}
</script>

@endsection
