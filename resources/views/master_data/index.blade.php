@extends('layouts.app')

@section('title', 'Master Data')

@section('content')
<div class="page-header">
    <div>
        <h2>Master Data</h2>
        <p class="page-subtitle">Centralized reference data for the entire system</p>
    </div>
    @if(Auth::user()->isAdminIT())
    <button class="btn btn-primary" onclick="openAddModal()">
        + Add {{ $tabLabels[$activeTab] }}
    </button>
    @endif
</div>

<!-- Tab navigation -->
<div class="md-tabs">
    @foreach($tabLabels as $key => $label)
    <a href="{{ route('master-data.index', ['tab' => $key]) }}" class="md-tab {{ $activeTab === $key ? 'active' : '' }}">
        {{ Str::plural($label) }}
        <span class="md-tab-count">{{ $counts[$key] }}</span>
    </a>
    @endforeach
</div>

<!-- Filter bar -->
<form id="md-filter-form" method="GET" action="{{ route('master-data.index') }}" class="md-filter-bar">
    <input type="hidden" name="tab" value="{{ $activeTab }}">
    <div class="md-filter-search">
        <input type="text" name="q" class="form-control" placeholder="Search..." value="{{ $search }}">
    </div>
    @if(in_array($activeTab, ['departments', 'courses']))
    <div class="md-filter-select">
        <select name="company" class="form-control">
            <option value="">All Companies</option>
            @foreach($allCompanies as $co)
            <option value="{{ $co->code }}" {{ $cFilter === $co->code ? 'selected' : '' }}>
                {{ $co->code }} — {{ $co->name }}
            </option>
            @endforeach
        </select>
    </div>
    @endif
    <div class="md-filter-actions">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('master-data.index', ['tab' => $activeTab]) }}" class="btn btn-outline">Clear</a>
    </div>
</form>

<div id="md-results">
@if($activeTab === 'departments')
    <div class="stats-grid" style="margin-bottom:1.25rem;">
        <div class="stat-card stat-blue">
            <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></div>    
            <div><div class="stat-label">Total Departments</div><div class="stat-value">{{ array_sum($data['totals']) }}</div></div>
        </div>
        @foreach(['FJB' => 'stat-green', 'FBSB' => 'stat-amber', 'LBSB' => 'stat-blue', 'FGT' => 'stat-purple'] as $coCode => $class)
        <div class="stat-card {{ $class }}">
            <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg></div>
            <div><div class="stat-label">{{ $coCode }}</div><div class="stat-value">{{ $data['totals'][$coCode] ?? 0 }}</div></div>
        </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Departments <span class="table-count">{{ count($data['rows']) }} shown</span></h3>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Department Name</th>
                        <th>Company</th>
                        <th>Active Staff</th>
                        <th>Created</th>
                        @if(Auth::user()->isAdminIT())<th class="td-actions">Actions</th>@endif
                    </tr>
                </thead>
                <tbody>
                @forelse($data['rows'] as $i => $d)
                <tr>
                    <td class="td-num">{{ $i + 1 }}</td>
                    <td><strong>{{ $d->name }}</strong></td>
                    <td>
                        @php
                            $badgeClass = [
                                'FJB'  => 'admin-it',
                                'FBSB' => 'admin-hr',
                                'LBSB' => 'dept-blue',
                                'FGT'  => 'dept-purple'
                            ][strtoupper($d->company)] ?? 'badge-outline';
                        @endphp
                        <span class="badge-role {{ $badgeClass }}">
                            {{ $d->company }}
                        </span>
                    </td>
                    <td>
                        @if($d->staff_count > 0)
                        <button class="btn-link-count" onclick="openStaffListModal({{ $d->id }}, '{{ addslashes($d->name) }}')">
                            {{ $d->staff_count }}
                        </button>
                        @else<span class="td-zero">0</span>@endif
                    </td>
                    <td class="td-muted">{{ $d->created_at->format('d M Y') }}</td>
                    @if(Auth::user()->isAdminIT())
                    <td class="td-actions">
                        <button class="btn btn-outline btn-sm" onclick='openEditModal({id:{{ $d->id }}, name:"{{ addslashes($d->name) }}", company:"{{ $d->company }}"})'>Edit</button>
                        @if($d->staff_count == 0)
                        <button class="btn btn-sm btn-danger-soft" onclick="confirmDelete({{ $d->id }}, '{{ addslashes($d->name) }}')">Delete</button>
                        @else<span class="td-in-use">In use</span>@endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="6" class="td-muted" style="text-align:center;padding:2.5rem;">No departments found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@elseif($activeTab === 'companies')
    <div class="card">
        <div class="card-header">
            <h3>Companies <span class="table-count">{{ count($data['rows']) }} shown</span></h3>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Code</th>
                        <th>Full Name</th>
                        <th>Departments</th>
                        <th>Active Staff</th>
                        @if(Auth::user()->isAdminIT())<th class="td-actions">Actions</th>@endif
                    </tr>
                </thead>
                <tbody>
                @forelse($data['rows'] as $i => $c)
                <tr>
                    <td class="td-num">{{ $i + 1 }}</td>
                    <td><strong>{{ $c->code }}</strong></td>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->dept_count }}</td>
                    <td>{{ $c->staff_count }}</td>
                    @if(Auth::user()->isAdminIT())
                    <td class="td-actions">
                        <button class="btn btn-outline btn-sm" onclick='openEditModal({id:{{ $c->id }}, code:"{{ $c->code }}", name:"{{ addslashes($c->name) }}"})'>Edit</button>
                        @if($c->staff_count == 0 && $c->dept_count == 0)
                        <button class="btn btn-sm btn-danger-soft" onclick="confirmDelete({{ $c->id }}, '{{ $c->code }}')">Delete</button>
                        @else<span class="td-in-use">In use</span>@endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="6" class="td-muted" style="text-align:center;padding:2.5rem;">No companies found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@elseif($activeTab === 'courses')
    <div class="card">
        <div class="card-header">
            <h3>Training Courses <span class="table-count">{{ count($data['rows']) }} shown</span></h3>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Code</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Company</th>
                        <th>Date</th>
                        <th>Attendances</th>
                        @if(Auth::user()->isAdminIT())<th class="td-actions">Actions</th>@endif
                    </tr>
                </thead>
                <tbody>
                @forelse($data['rows'] as $i => $c)
                <tr>
                    <td class="td-num">{{ $i + 1 }}</td>
                    <td><strong>{{ $c->code }}</strong></td>
                    <td>{{ $c->title }}</td>
                    <td>
                        <span class="badge-role {{ $c->training_type === 'Internal' ? 'admin-hr' : 'admin-it' }}">
                            {{ $c->training_type ?? 'External' }}
                        </span>
                    </td>
                    <td>{{ $c->company }}</td>
                    <td class="td-muted">{{ $c->start_date ? \Carbon\Carbon::parse($c->start_date)->format('d M Y') : '—' }}</td>
                    <td>{{ $c->att_count }}</td>
                    @if(Auth::user()->isAdminIT())
                    <td class="td-actions">
                        <button class="btn btn-outline btn-sm" onclick='openEditModal({
                            id:{{ $c->id }},
                            code:"{{ $c->code }}",
                            title:"{{ addslashes($c->title) }}",
                            training_type:"{{ $c->training_type ?? 'External' }}",
                            company:"{{ $c->company }}",
                            start_date:"{{ $c->start_date ?? '' }}"
                        })'>Edit</button>
                        @if($c->att_count == 0)
                        <button class="btn btn-sm btn-danger-soft" onclick="confirmDelete({{ $c->id }}, '{{ $c->code }}')">Delete</button>
                        @else<span class="td-in-use">In use</span>@endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="8" class="td-muted" style="text-align:center;padding:2.5rem;">No training courses found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@elseif($activeTab === 'positions')
    <div class="card">
        <div class="card-header">
            <h3>Job Positions <span class="table-count">{{ count($data['rows']) }} shown</span></h3>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Position Title</th>
                        <th>Staff Using</th>
                        @if(Auth::user()->isAdminIT())<th class="td-actions">Actions</th>@endif
                    </tr>
                </thead>
                <tbody>
                @forelse($data['rows'] as $i => $p)
                <tr>
                    <td class="td-num">{{ $i + 1 }}</td>
                    <td>{{ $p->title }}</td>
                    <td>{{ $p->staff_count }}</td>
                    @if(Auth::user()->isAdminIT())
                    <td class="td-actions">
                        <button class="btn btn-outline btn-sm" onclick='openEditModal({id:{{ $p->id }}, title:"{{ addslashes($p->title) }}"})'>Edit</button>
                        @if($p->staff_count == 0)
                        <button class="btn btn-sm btn-danger-soft" onclick="confirmDelete({{ $p->id }}, '{{ addslashes($p->title) }}')">Delete</button>
                        @else<span class="td-in-use">In use</span>@endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="4" class="td-muted" style="text-align:center;padding:2.5rem;">No positions found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@elseif($activeTab === 'transport')
    <div class="card">
        <div class="card-header">
            <h3>Transport Modes <span class="table-count">{{ count($data['rows']) }} shown</span></h3>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Transport Mode</th>
                        <th>Times Used</th>
                        @if(Auth::user()->isAdminIT())<th class="td-actions">Actions</th>@endif
                    </tr>
                </thead>
                <tbody>
                @forelse($data['rows'] as $i => $t)
                <tr>
                    <td class="td-num">{{ $i + 1 }}</td>
                    <td>{{ $t->name }}</td>
                    <td>{{ $t->usage_count }}</td>
                    @if(Auth::user()->isAdminIT())
                    <td class="td-actions">
                        <button class="btn btn-outline btn-sm" onclick='openEditModal({id:{{ $t->id }}, name:"{{ addslashes($t->name) }}"})'>Edit</button>
                        @if($t->usage_count == 0)
                        <button class="btn btn-sm btn-danger-soft" onclick="confirmDelete({{ $t->id }}, '{{ addslashes($t->name) }}')">Delete</button>
                        @else<span class="td-in-use">In use</span>@endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="4" class="td-muted" style="text-align:center;padding:2.5rem;">No transport modes found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@elseif($activeTab === 'settings')
    <div class="card">
        <div class="card-header">
            <h3>System Settings <span class="table-count">{{ count($data['rows']) }} shown</span></h3>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Setting Key</th>
                        <th>Value</th>
                        @if(Auth::user()->isAdminIT())<th class="td-actions">Actions</th>@endif
                    </tr>
                </thead>
                <tbody>
                @forelse($data['rows'] as $i => $s)
                <tr>
                    <td class="td-num">{{ $i + 1 }}</td>
                    <td><code>{{ $s->setting_key }}</code></td>
                    <td>{{ $s->setting_value }}</td>
                    @if(Auth::user()->isAdminIT())
                    <td class="td-actions">
                        <button class="btn btn-outline btn-sm" onclick='openEditModal({id:{{ $s->id }}, setting_key:"{{ $s->setting_key }}", setting_value:"{{ addslashes($s->setting_value) }}"})'>Edit</button>
                        <button class="btn btn-sm btn-danger-soft" onclick="confirmDelete({{ $s->id }}, '{{ $s->setting_key }}')">Delete</button>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="4" class="td-muted" style="text-align:center;padding:2.5rem;">No settings found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
</div>

<!-- Staff List Modal -->
<div class="modal" id="staffListModal">
    <div class="modal-box" style="max-width:580px;">
        <div class="modal-header">
            <h3 id="staffListModalTitle">Staff in Department</h3>
            <button class="modal-close" onclick="closeStaffListModal()">✕</button>
        </div>
        <div id="staffListBody" style="max-height:420px;overflow-y:auto;">
            <div style="padding:2rem;text-align:center;color:var(--muted);">Loading…</div>
        </div>
    </div>
</div>

@if(Auth::user()->isAdminIT())
<!-- Main Add/Edit Modal -->
<div class="modal" id="mainModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="mainModalTitle">Add Record</h3>
            <button class="modal-close" onclick="closeMainModal()">✕</button>
        </div>
        <form method="POST" action="{{ route('master-data.store') }}">
            @csrf
            <input type="hidden" name="tab" value="{{ $activeTab }}">

            <div class="modal-body">
                @if($activeTab === 'departments')
                <div class="form-group">
                    <label class="form-label">Department Name <span class="req">*</span></label>
                    <input type="text" name="name" id="f_name" class="form-control" required placeholder="e.g. HUMAN RESOURCE/ADMIN">
                </div>
                <div class="form-group">
                    <label class="form-label">Company <span class="req">*</span></label>
                    <select name="company" id="f_company" class="form-control" required>
                        @foreach($allCompanies as $co)
                        <option value="{{ $co->code }}">{{ $co->code }} — {{ $co->name }}</option>
                        @endforeach
                    </select>
                </div>
                @elseif($activeTab === 'companies')
                <div class="form-group">
                    <label class="form-label">Company Code <span class="req">*</span></label>
                    <input type="text" name="code" id="f_code" class="form-control" required placeholder="e.g. FJB" style="text-transform:uppercase;">
                </div>
                <div class="form-group">
                    <label class="form-label">Full Name <span class="req">*</span></label>
                    <input type="text" name="name" id="f_name" class="form-control" required placeholder="e.g. FGV Johor Bulkers Sdn Bhd">    
                </div>
                @elseif($activeTab === 'courses')
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Course Code <span class="req">*</span></label>
                        <input type="text" name="code" id="f_code" class="form-control" required placeholder="e.g. HR-001" style="text-transform:uppercase;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Training Type</label>
                        <select name="training_type" id="f_training_type" class="form-control">
                            <option value="External">External</option>
                            <option value="Internal">Internal</option>
                        </select>
                    </div>
                    <div class="form-group form-full">
                        <label class="form-label">Course Title <span class="req">*</span></label>
                        <input type="text" name="title" id="f_title" class="form-control" required placeholder="Full course title">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Company</label>
                        <select name="company" id="f_company" class="form-control">
                            @foreach($allCompanies as $co)
                            <option value="{{ $co->code }}">{{ $co->code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Training Date</label>
                        <input type="date" name="start_date" id="f_start_date" class="form-control">
                    </div>
                </div>
                @elseif($activeTab === 'positions')
                <div class="form-group">
                    <label class="form-label">Position Title <span class="req">*</span></label>
                    <input type="text" name="title" id="f_title" class="form-control" required placeholder="e.g. Senior Executive">
                </div>
                @elseif($activeTab === 'transport')
                <div class="form-group">
                    <label class="form-label">Transport Mode <span class="req">*</span></label>
                    <input type="text" name="name" id="f_name" class="form-control" required placeholder="e.g. Flight">
                </div>
                @elseif($activeTab === 'settings')
                <div class="form-group">
                    <label class="form-label">Setting Key <span class="req">*</span></label>
                    <input type="text" name="setting_key" id="f_setting_key" class="form-control" required placeholder="e.g. app_name">
                </div>
                <div class="form-group">
                    <label class="form-label">Value <span class="req">*</span></label>
                    <textarea name="setting_value" id="f_setting_value" class="form-control" required rows="3"></textarea>
                </div>
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeMainModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="formSubmitBtn">Add</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal" id="deleteModal">
    <div class="modal-box modal-sm">
        <div class="modal-header">
            <h3>Confirm Delete</h3>
            <button class="modal-close" onclick="closeDeleteModal()">✕</button>
        </div>
        <form method="POST" action="">
            @csrf
            @method('DELETE')
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            <div class="modal-body">
                <p>Delete <strong id="deleteName"></strong>?</p>
                <p style="color:var(--danger);font-size:.85rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeDeleteModal()">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
const TAB = '{{ $activeTab }}';
const TAB_LABEL = '{{ $tabLabels[$activeTab] }}';

function openAddModal() {
    const form = document.querySelector('#mainModal form');
    form.action = "{{ route('master-data.store') }}";
    
    let methodInput = form.querySelector('input[name="_method"]');
    if (methodInput) methodInput.remove();
    
    document.getElementById('mainModalTitle').textContent = 'Add ' + TAB_LABEL;
    document.getElementById('formSubmitBtn').textContent = 'Add ' + TAB_LABEL;
    
    clearFormFields();
    openModal('mainModal');
}

function openEditModal(data) {
    const form = document.querySelector('#mainModal form');
    form.action = "{{ url('master-data') }}/" + data.id;
    
    let methodInput = form.querySelector('input[name="_method"]');
    if (!methodInput) {
        methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        form.appendChild(methodInput);
    }
    methodInput.value = 'PUT';
    
    document.getElementById('mainModalTitle').textContent = 'Edit ' + TAB_LABEL;
    document.getElementById('formSubmitBtn').textContent = 'Save Changes';
    
    if (TAB === 'departments') { 
        setVal('f_name', data.name); 
        setVal('f_company', data.company); 
    }
    else if (TAB === 'companies') { 
        setVal('f_code', data.code); 
        setVal('f_name', data.name); 
    }
    else if (TAB === 'courses') { 
        setVal('f_code', data.code); 
        setVal('f_title', data.title); 
        setVal('f_training_type', data.training_type); 
        setVal('f_company', data.company); 
        setVal('f_start_date', data.start_date); 
    }
    else if (TAB === 'positions') { 
        setVal('f_title', data.title); 
    }
    else if (TAB === 'transport') { 
        setVal('f_name', data.name); 
    }
    else if (TAB === 'settings') { 
        setVal('f_setting_key', data.setting_key); 
        setVal('f_setting_value', data.setting_value); 
    }
    
    openModal('mainModal');
}

function confirmDelete(id, label) {
    const form = document.querySelector('#deleteModal form');
    form.action = "{{ url('master-data') }}/" + id;
    document.getElementById('deleteName').textContent = label;
    openModal('deleteModal');
}

function closeMainModal() { closeModal(); }
function closeDeleteModal() { closeModal(); }

function setVal(id, val) { 
    const el = document.getElementById(id); 
    if (el) el.value = val ?? ''; 
}

function clearFormFields() {
    ['f_name', 'f_code', 'f_title', 'f_start_date', 'f_setting_key', 'f_setting_value'].forEach(id => setVal(id, ''));
    setVal('f_training_type', 'External');
}

// Staff list modal functions
function openStaffListModal(deptId, deptName) {
    document.getElementById('staffListModalTitle').textContent = deptName;
    document.getElementById('staffListBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--muted);">Loading…</div>';  
    openModal('staffListModal');
    
    fetch("{{ url('master-data/staff-list') }}/" + deptId)
        .then(r => r.json())
        .then(data => {
            if (!data.length) {
                document.getElementById('staffListBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--muted);">No active staff in this department.</div>';
                return;
            }
            const rows = data.map((s, i) => `<tr>
                <td class="td-num">${i + 1}</td>
                <td><strong>${s.name}</strong></td>
                <td class="td-muted">${s.staff_no}</td>
                <td class="td-muted">${s.position || '—'}</td>
            </tr>`).join('');
            document.getElementById('staffListBody').innerHTML =
                `<table class="table"><thead><tr><th>#</th><th>Name</th><th>Staff No.</th><th>Position</th></tr></thead><tbody>${rows}</tbody></table>`;
        })
        .catch(() => {
            document.getElementById('staffListBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--danger);">Failed to load staff.</div>';
        });
}

function closeStaffListModal() {
    closeModal();
}


liveSearch(document.getElementById('md-filter-form'), 'md-results');
</script>
@endsection
