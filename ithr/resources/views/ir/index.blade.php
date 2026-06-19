@extends('layouts.app')

@section('title', 'IR Records')

@section('content')
<div class="page-header">
    <div>
        <h2>IR Records</h2>
        <p class="page-subtitle">
            {{ count($records) }} record(s)
            @if ($totalVerbal) · Verbal: {{ $totalVerbal }}@endif
            @if ($totalWritten) · Written: {{ $totalWritten }}@endif
        </p>
    </div>
    @canwrite
    <button class="btn btn-primary" onclick="openAddIR()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add IR Record
    </button>
    @endcanwrite
</div>

<!-- Filter Bar -->
<div class="filter-bar" style="flex-wrap:wrap;gap:.5rem;">
    <form id="ir-filter-form" method="GET" action="{{ route('ir.index') }}" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;width:100%;">
        @if ($staff_filter)
        <input type="hidden" name="staff_id" value="{{ $staff_filter }}">
        @endif
        <div class="app-search">
            <svg class="app-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" value="{{ $search }}" placeholder="Search staff name, staff no, title…" autocomplete="off">
        </div>
        <select name="type" class="filter-select">
            <option value="">All Types</option>
            <option value="Verbal"  {{ $type_filter === 'Verbal'  ? 'selected' : '' }}>Verbal</option>
            <option value="Written" {{ $type_filter === 'Written' ? 'selected' : '' }}>Written</option>
        </select>
        <button type="submit" class="btn btn-outline btn-sm">Filter</button>
        <a href="{{ route('ir.index', $staff_filter ? ['staff_id' => $staff_filter] : []) }}" class="btn btn-ghost btn-sm">Clear</a>
        @if ($staff_filter && $preloadStaff)
        <a href="{{ route('staff.show', $staff_filter) }}" class="btn btn-ghost btn-sm">← Back to {{ $preloadStaff->name }}</a>
        @endif
    </form>
</div>

<!-- Records Table -->
<div class="card" id="ir-results">
    @if ($records->isEmpty())
    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        <p>No IR records found.</p>
    </div>
    @else
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Staff No</th>
                    <th>Staff Name</th>
                    <th>Department</th>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($records as $r)
            <tr>
                <td><code style="color:#6366f1;font-size:.82rem;">{{ $r->staff?->staff_no ?? '—' }}</code></td>
                <td style="font-weight:500;">{{ $r->staff?->name ?? '—' }}</td>
                <td><span class="dept-badge" style="font-size:.75rem;">{{ $r->staff?->department->name ?? '—' }}</span></td>
                <td style="font-size:.88rem;">{{ $r->title }}</td>
                <td style="font-size:.85rem;white-space:nowrap;">{{ \Carbon\Carbon::parse($r->date)->format('d M Y') }}</td>
                <td>
                    @if ($r->type === 'Verbal')
                    <span style="background:#fef3c7;color:#92400e;font-size:.75rem;font-weight:600;padding:.2rem .65rem;border-radius:20px;">Verbal</span>
                    @else
                    <span style="background:#fee2e2;color:#991b1b;font-size:.75rem;font-weight:600;padding:.2rem .65rem;border-radius:20px;">Written</span>
                    @endif
                </td>
                <td class="td-actions">
                    @canwrite
                    <button class="btn btn-sm btn-outline" onclick="editIR({{ $r->toJson() }})">Edit</button>
                    <form action="{{ route('ir.destroy', $r->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this IR record for {{ $r->staff?->name ?? 'this staff' }}? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                    @endcanwrite
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<!-- Add / Edit Modal -->
<div class="modal" id="irModal">
    <div class="modal-box" style="max-width:540px;">
        <div class="modal-header">
            <h3 id="irModalTitle">Add IR Record</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form method="POST" action="{{ route('ir.store') }}" id="irForm">
            @csrf
            <div id="methodField"></div>
            <input type="hidden" name="id" id="irId" value="">
            <div class="form-grid">
                <div class="form-group form-full" id="staffSearchGroup">
                    <label>Staff <span style="color:var(--danger)">*</span></label>
                    <input type="text" id="irStaffSearch" placeholder="Type name or staff no…" autocomplete="off">
                    <input type="hidden" name="staff_id" id="irStaffId">
                    <div id="irStaffDropdown" style="display:none;position:absolute;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);max-height:220px;overflow-y:auto;z-index:1000;width:100%;"></div>
                    <div id="irStaffSelected" style="display:none;margin-top:.35rem;font-size:.85rem;color:var(--text-muted);"></div>
                </div>
                <div class="form-group form-full">
                    <label>Title <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="title" id="irTitle" placeholder="e.g. Late Attendance Warning" required>
                </div>
                <div class="form-group">
                    <label>Date <span style="color:var(--danger)">*</span></label>
                    <input type="date" name="date" id="irDate" required>
                </div>
                <div class="form-group">
                    <label>Type <span style="color:var(--danger)">*</span></label>
                    <select name="type" id="irType" required>
                        <option value="Verbal">Verbal</option>
                        <option value="Written">Written</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
const _preloadStaff = @json($preloadStaff);

function openAddIR() {
    document.getElementById('irModalTitle').textContent = 'Add IR Record';
    document.getElementById('irForm').action = "{{ route('ir.store') }}";
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('irId').value     = '';
    document.getElementById('irTitle').value  = '';
    document.getElementById('irDate').value   = '';
    document.getElementById('irType').value   = 'Verbal';
    document.getElementById('irStaffId').value = '';
    document.getElementById('irStaffSearch').value = '';
    document.getElementById('irStaffSelected').style.display = 'none';
    document.getElementById('staffSearchGroup').style.display = '';

    if (_preloadStaff) {
        document.getElementById('irStaffId').value    = _preloadStaff.id;
        document.getElementById('irStaffSearch').value = _preloadStaff.staff_no + ' — ' + _preloadStaff.name;
        showStaffSelected(_preloadStaff.staff_no + ' — ' + _preloadStaff.name);
    }
    openModal('irModal');
}

function editIR(data) {
    document.getElementById('irModalTitle').textContent = 'Edit IR Record';
    document.getElementById('irForm').action = "{{ url('ir') }}/" + data.id;
    document.getElementById('methodField').innerHTML = '@method("PUT")';
    document.getElementById('irId').value     = data.id;
    document.getElementById('irTitle').value  = data.title;
    document.getElementById('irDate').value   = data.date;
    document.getElementById('irType').value   = data.type;
    document.getElementById('irStaffId').value = data.staff_id;
    const staffLabel = (data.staff ? data.staff.staff_no + ' — ' + data.staff.name : 'Unknown Staff');
    document.getElementById('irStaffSearch').value = staffLabel;
    showStaffSelected(staffLabel);
    document.getElementById('staffSearchGroup').style.display = 'none';
    openModal('irModal');
}

function showStaffSelected(label) {
    const el = document.getElementById('irStaffSelected');
    el.textContent = '✓ ' + label;
    el.style.display = 'block';
}

let irSearchTimer;
document.getElementById('irStaffSearch').addEventListener('input', function () {
    clearTimeout(irSearchTimer);
    const q = this.value.trim();
    if (q.length < 2) { document.getElementById('irStaffDropdown').style.display = 'none'; return; }
    irSearchTimer = setTimeout(() => {
        fetch("{{ route('users.search_staff') }}?search_staff=" + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                const dd = document.getElementById('irStaffDropdown');
                if (!data.length) { dd.style.display = 'none'; return; }
                dd.innerHTML = data.map(s =>
                    `<div style="padding:.55rem .9rem;cursor:pointer;font-size:.88rem;border-bottom:1px solid var(--border);"
                          onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
                        <strong style="color:var(--primary)">${s.staff_no}</strong> — ${s.name}
                        <span style="color:var(--text-muted);font-size:.78rem;margin-left:.3rem;">${s.dept_name || ''}</span>
                     </div>`
                ).join('');
                dd.querySelectorAll('div').forEach((el, i) => {
                    el.addEventListener('click', () => selectIRStaff(data[i].id, data[i].staff_no, data[i].name));
                });
                dd.style.display = 'block';
            });
    }, 250);
});

document.getElementById('irStaffSearch').addEventListener('blur', function () {
    setTimeout(() => { document.getElementById('irStaffDropdown').style.display = 'none'; }, 200);
});

document.querySelector('#irModal form').addEventListener('submit', function(e) {
    if (!document.getElementById('irStaffId').value) {
        e.preventDefault();
        const s = document.getElementById('irStaffSearch');
        s.style.borderColor = '#dc2626';
        s.placeholder = 'Search and select a staff member first';
        s.focus();
    }
});

function selectIRStaff(id, staffNo, name) {
    document.getElementById('irStaffId').value    = id;
    document.getElementById('irStaffSearch').value = staffNo + ' — ' + name;
    document.getElementById('irStaffSearch').style.borderColor = '';
    document.getElementById('irStaffDropdown').style.display = 'none';
    showStaffSelected(staffNo + ' — ' + name);
}

function openModal(id) {
    document.getElementById(id).classList.add('active');
}

function closeModal() {
    document.querySelectorAll('.modal').forEach(m => m.classList.remove('active'));
}

liveSearch(document.getElementById('ir-filter-form'), 'ir-results');
</script>
@endsection
