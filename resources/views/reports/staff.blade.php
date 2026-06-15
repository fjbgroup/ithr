@extends('layouts.app')

@section('title', 'Staff Registry Report')

@section('content')
<div class="page-header">
    <div>
        <h2>Staff Registry Report</h2>
        <p class="page-subtitle">Employee headcount and distribution across departments</p>
    </div>
    <div class="header-actions" style="display:flex;gap:.5rem;">
        <a href="{{ route('report.export') }}" class="btn btn-outline">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            Export Excel
        </a>
        <button class="btn btn-outline" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print Report
        </button>
    </div>
</div>

<div class="stats-grid" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(56,189,248,.12);color:#0284c7;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $totalStaff }}</div>
            <div class="stat-label">Total Active Staff</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(22,163,74,.1);color:#16a34a;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $totalDepts }}</div>
            <div class="stat-label">Departments</div>
        </div>
    </div>
    <div class="stat-card clickable-stat" onclick="openCompanyStaffModal('FJB', 'FGV Johor Bulkers')">
        <div class="stat-icon" style="background:rgba(56,189,248,.1);color:#0284c7;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $fjbCount }}</div>
            <div class="stat-label">FJB Staff</div>
        </div>
    </div>
    <div class="stat-card clickable-stat" onclick="openCompanyStaffModal('FBSB', 'FGV Bulkers Sdn Bhd')">
        <div class="stat-icon" style="background:rgba(22,163,74,.1);color:#16a34a;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $fbsbCount }}</div>
            <div class="stat-label">FBSB Staff</div>
        </div>
    </div>
    <div class="stat-card clickable-stat" onclick="openCompanyStaffModal('LBSB', 'Langsat Bulkers Sdn Bhd')">
        <div class="stat-icon" style="background:rgba(245,158,11,.1);color:#d97706;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $lbsbCount }}</div>
            <div class="stat-label">LBSB Staff</div>
        </div>
    </div>
    <div class="stat-card clickable-stat" onclick="openCompanyStaffModal('FGT', 'FGV Transport')">
        <div class="stat-icon" style="background:rgba(99,102,241,.1);color:#4f46e5;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $fgtCount }}</div>
            <div class="stat-label">FGT Staff</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
    <div class="card">
        <div class="card-header"><h3 class="card-title">Headcount by Company</h3></div>
        <div style="padding:1.25rem;">
            <div style="display:grid;grid-template-columns:repeat(2, 1fr);gap:1rem;margin-bottom:1.5rem;">
                <div class="co-stat-box" onclick="openCompanyStaffModal('FJB', 'FGV Johor Bulkers')" style="background:rgba(56,189,248,.08);border:1px solid rgba(56,189,248,.2);border-radius:8px;padding:1rem;text-align:center;cursor:pointer;">
                    <div style="font-size:2rem;font-weight:700;color:#0284c7;">{{ $fjbCount }}</div>
                    <div style="font-size:.8rem;color:var(--muted);font-weight:600;margin-top:.25rem;">FJB</div>
                </div>
                <div class="co-stat-box" onclick="openCompanyStaffModal('FBSB', 'FGV Bulkers Sdn Bhd')" style="background:rgba(22,163,74,.07);border:1px solid rgba(22,163,74,.2);border-radius:8px;padding:1rem;text-align:center;cursor:pointer;">
                    <div style="font-size:2rem;font-weight:700;color:#16a34a;">{{ $fbsbCount }}</div>
                    <div style="font-size:.8rem;color:var(--muted);font-weight:600;margin-top:.25rem;">FBSB</div>
                </div>
                <div class="co-stat-box" onclick="openCompanyStaffModal('LBSB', 'Langsat Bulkers Sdn Bhd')" style="background:rgba(245,158,11,.07);border:1px solid rgba(245,158,11,.2);border-radius:8px;padding:1rem;text-align:center;cursor:pointer;">
                    <div style="font-size:2rem;font-weight:700;color:#d97706;">{{ $lbsbCount }}</div>
                    <div style="font-size:.8rem;color:var(--muted);font-weight:600;margin-top:.25rem;">LBSB</div>
                </div>
                <div class="co-stat-box" onclick="openCompanyStaffModal('FGT', 'FGV Transport')" style="background:rgba(99,102,241,.07);border:1px solid rgba(99,102,241,.2);border-radius:8px;padding:1rem;text-align:center;cursor:pointer;">
                    <div style="font-size:2rem;font-weight:700;color:#4f46e5;">{{ $fgtCount }}</div>
                    <div style="font-size:.8rem;color:var(--muted);font-weight:600;margin-top:.25rem;">FGT</div>
                </div>
            </div>
            
            <h4 style="font-size:.85rem;font-weight:700;margin-bottom:.75rem;color:var(--text);">Top 8 Positions</h4>
            <div style="display:flex;flex-direction:column;gap:.5rem;">
                @foreach($posRows as $pos)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:.5rem .75rem;background:#f8fafc;border-radius:6px;border:1px solid var(--border);">
                    <span style="font-size:.8rem;font-weight:600;color:var(--text-muted);">{{ $pos->position }}</span>
                    <span style="background:var(--navy);color:#fff;font-size:.7rem;font-weight:700;padding:.1rem .5rem;border-radius:10px;">{{ $pos->total }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Department Distribution</h3></div>
        <div class="table-responsive">
            <table class="table" style="font-size:.85rem;">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th style="text-align:center;">Co.</th>
                        <th style="text-align:right;">Headcount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deptRows as $row)
                    <tr>
                        <td><strong>{{ $row->dept }}</strong></td>
                        <td style="text-align:center;"><span class="badge badge-outline" style="font-size:.65rem;">{{ $row->company }}</span></td>
                        <td style="text-align:right;">
                            @if($row->headcount > 0)
                            <button class="btn-link-count" onclick="openDeptStaffModal({{ $row->id }}, '{{ addslashes($row->dept) }}')">
                                {{ $row->headcount }}
                            </button>
                            @else
                            <span style="font-weight:700;color:#94a3b8;padding-right:.75rem;">0</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Company Staff Modal -->
<div class="modal" id="companyStaffModal">
    <div class="modal-box" style="max-width:600px;">
        <div class="modal-header">
            <h3 id="companyStaffModalTitle">Staff List</h3>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <div id="companyStaffBody" style="max-height:450px;overflow-y:auto;">
            <div style="padding:2.5rem;text-align:center;color:var(--muted);">
                <div class="spinner" style="margin-bottom:1rem;"></div>
                Loading staff registry…
            </div>
        </div>
    </div>
</div>

<style>
.clickable-stat { cursor: pointer; transition: transform .15s ease, box-shadow .15s ease; }
.clickable-stat:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,.1); }
.co-stat-box:hover { opacity: 0.8; }
.btn-link-count {
    background: none; border: none; color: var(--navy); font-weight: 700; font-family: inherit;
    cursor: pointer; padding: .2rem .75rem; border-radius: 4px; transition: .15s; text-align: right; width: 100%;
}
.btn-link-count:hover { background: rgba(20,43,71,.05); color: var(--primary); }
</style>
@endsection

@section('scripts')
<script>
function openCompanyStaffModal(code, name) {
    document.getElementById('companyStaffModalTitle').textContent = 'Active Staff: ' + name;
    document.getElementById('companyStaffBody').innerHTML = 
        '<div style="padding:2.5rem;text-align:center;color:var(--muted);"><div class="spinner" style="margin-bottom:1rem;"></div>Loading staff registry…</div>';
    
    openModal('companyStaffModal');
    
    fetch("{{ url('report/company-staff') }}/" + code)
        .then(r => r.json())
        .then(data => {
            renderStaffTable(data);
        })
        .catch(err => {
            showError();
        });
}

function openDeptStaffModal(id, name) {
    document.getElementById('companyStaffModalTitle').textContent = 'Staff in ' + name;
    document.getElementById('companyStaffBody').innerHTML = 
        '<div style="padding:2.5rem;text-align:center;color:var(--muted);"><div class="spinner" style="margin-bottom:1rem;"></div>Loading staff registry…</div>';
    
    openModal('companyStaffModal');
    
    fetch("{{ url('master-data/staff-list') }}/" + id)
        .then(r => r.json())
        .then(data => {
            renderStaffTable(data);
        })
        .catch(err => {
            showError();
        });
}

function renderStaffTable(data) {
    if (!data.length) {
        document.getElementById('companyStaffBody').innerHTML = 
            '<div style="padding:3rem;text-align:center;color:var(--muted);">No active staff records found.</div>';
        return;
    }
    
    let html = '<table class="table" style="font-size:.85rem;"><thead><tr><th>#</th><th>Name</th><th>Staff No</th><th>Position</th></tr></thead><tbody>';
    data.forEach((s, i) => {
        html += `<tr>
            <td class="td-num">${i + 1}</td>
            <td><strong>${s.name}</strong></td>
            <td class="td-muted"><code>${s.staff_no}</code></td>
            <td class="td-muted">${s.position || '—'}</td>
        </tr>`;
    });
    html += '</tbody></table>';
    document.getElementById('companyStaffBody').innerHTML = html;
}

function showError() {
    document.getElementById('companyStaffBody').innerHTML = 
        '<div style="padding:3rem;text-align:center;color:var(--danger);">Failed to load staff list. Please try again.</div>';
}
</script>
@endsection

