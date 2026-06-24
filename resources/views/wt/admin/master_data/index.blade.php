@extends('wt.layouts.admin')

@section('title', 'Walkie Talkie Master Data')

@section('content')
<style>
.wtmd-shell { display: grid; gap: 16px; }
.wtmd-header {
    display: flex; align-items: center; justify-content: space-between; gap: 16px;
    padding: 18px 22px; border: 1px solid var(--border); border-left: 4px solid #38bdf8;
    border-radius: 12px; background: var(--surface);
}
.wtmd-header h2 { margin: 0; font-size: 20px; font-weight: 900; color: var(--text); }
.wtmd-header p { margin: 4px 0 0; font-size: 11px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); }
.wtmd-tabs { display: flex; flex-wrap: wrap; gap: 8px; }
.wtmd-tab {
    display: inline-flex; align-items: center; gap: 8px; padding: 9px 16px;
    border: 1px solid var(--border); border-radius: 999px; background: var(--surface);
    color: var(--muted); font-size: 13px; font-weight: 800; text-decoration: none; transition: all .15s;
}
.wtmd-tab:hover { color: var(--text); border-color: #38bdf8; }
.wtmd-tab.active { background: #0f3a72; border-color: #3b82f6; color: #fff; }
.wtmd-tab-count {
    display: inline-flex; align-items: center; justify-content: center; min-width: 22px; height: 20px;
    padding: 0 6px; border-radius: 999px; background: rgba(148,163,184,.22); color: inherit;
    font-size: 11px; font-weight: 900;
}
.wtmd-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; }
.wtmd-search { position: relative; flex: 1 1 280px; max-width: 360px; }
.wtmd-search input {
    width: 100%; height: 40px; padding: 0 14px; border: 1px solid var(--border); border-radius: 8px;
    background: var(--form-input-bg); color: var(--form-input-color); font-size: 13px; outline: none;
}
.wtmd-card { border: 1px solid var(--border); border-radius: 12px; background: var(--surface); overflow: hidden; }
.wtmd-table { width: 100%; border-collapse: collapse; }
.wtmd-table thead th {
    padding: 12px 18px; text-align: left; background: var(--table-head-bg); color: var(--table-head-color);
    font-size: 10px; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; border-bottom: 1px solid var(--border);
}
.wtmd-table tbody td { padding: 12px 18px; border-bottom: 1px solid var(--border); color: var(--text); font-size: 13px; }
.wtmd-table tbody tr:hover td { background: var(--table-hover); }
.wtmd-table tbody td.wtmd-num { color: var(--muted); width: 48px; }
.wtmd-value { font-weight: 800; }
.wtmd-usage-pill {
    display: inline-flex; align-items: center; padding: 3px 12px; border-radius: 999px;
    font-size: 11px; font-weight: 800; background: rgba(56,189,248,.16); color: #0284c7;
    border: 1px solid transparent; cursor: pointer; transition: all .15s;
}
.wtmd-usage-pill:hover { background: rgba(56,189,248,.28); border-color: #38bdf8; }
.wtmd-usage-table { width: 100%; border-collapse: collapse; }
.wtmd-usage-table thead th {
    position: sticky; top: 0; padding: 9px 12px; text-align: left; background: var(--table-head-bg);
    color: var(--table-head-color); font-size: 10px; font-weight: 900; letter-spacing: .06em;
    text-transform: uppercase; border-bottom: 1px solid var(--border);
}
.wtmd-usage-table tbody td { padding: 9px 12px; border-bottom: 1px solid var(--border); color: var(--text); font-size: 12px; }
.wtmd-usage-table tbody tr:hover td { background: var(--table-hover); }
.wtmd-status-chip {
    display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 6px;
    font-size: 10px; font-weight: 800; border: 1px solid var(--border); background: var(--body-bg); color: var(--text);
}
.wtmd-usage-zero { color: var(--muted); }
.wtmd-actions { display: flex; gap: 6px; justify-content: flex-end; }
.wtmd-in-use { font-size: 11px; font-weight: 700; color: var(--muted); }
.wtmd-empty { text-align: center; padding: 40px 18px; color: var(--muted); font-size: 13px; }
.wtmd-btn {
    display: inline-flex; align-items: center; gap: 6px; height: 34px; padding: 0 14px; border-radius: 8px;
    border: 1px solid transparent; font-size: 12px; font-weight: 800; cursor: pointer; text-decoration: none; line-height: 1;
}
.wtmd-btn-primary { background: #0d6efd; color: #fff; }
.wtmd-btn-outline { background: transparent; border-color: var(--border); color: var(--text); }
.wtmd-btn-danger { background: #dc3545; color: #fff; }
.wtmd-btn-sm { height: 30px; padding: 0 11px; font-size: 11px; }
</style>

<div class="wtmd-shell">
    <div class="wtmd-header">
        <div>
            <h2>Walkie Talkie Master Data</h2>
            <p>Reference lists for walkie talkie records only</p>
        </div>
        <button type="button" class="wtmd-btn wtmd-btn-primary" onclick="wtmdOpenAdd()">
            <i class="fas fa-plus"></i> Add {{ $categories[$activeTab] }}
        </button>
    </div>

    <div class="wtmd-tabs">
        @foreach($categories as $key => $label)
        <a href="{{ route('wt.admin.masterData.index', ['tab' => $key]) }}" class="wtmd-tab {{ $activeTab === $key ? 'active' : '' }}">
            {{ \Illuminate\Support\Str::plural($label) }}
            <span class="wtmd-tab-count">{{ $counts[$key] }}</span>
        </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('wt.admin.masterData.index') }}" class="wtmd-toolbar">
        <input type="hidden" name="tab" value="{{ $activeTab }}">
        <div class="wtmd-search">
            <input type="text" name="q" value="{{ $search }}" placeholder="Search {{ strtolower($categories[$activeTab]) }}...">
        </div>
        <div style="display:flex;gap:8px">
            <button type="submit" class="wtmd-btn wtmd-btn-primary">Search</button>
            <a href="{{ route('wt.admin.masterData.index', ['tab' => $activeTab]) }}" class="wtmd-btn wtmd-btn-outline">Clear</a>
        </div>
    </form>

    <div class="wtmd-card">
        <table class="wtmd-table">
            <thead>
                <tr>
                    <th class="wtmd-num">#</th>
                    <th>{{ $categories[$activeTab] }}</th>
                    <th>In Use</th>
                    <th>Created</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $i => $row)
                <tr>
                    <td class="wtmd-num">{{ $i + 1 }}</td>
                    <td><span class="wtmd-value">{{ $row->value }}</span></td>
                    <td>
                        @if($row->usage_count > 0)
                        <button type="button" class="wtmd-usage-pill" title="View the {{ $row->usage_count }} walkie talkie(s) using this {{ strtolower($categories[$activeTab]) }}"
                            onclick="wtmdShowUsage(@js($row->value), {{ $row->usage_count }})">
                            {{ $row->usage_count }}
                        </button>
                        @else
                        <span class="wtmd-usage-zero">0</span>
                        @endif
                    </td>
                    <td style="color:var(--muted)">{{ optional($row->created_at)->format('d M Y') ?? '—' }}</td>
                    <td>
                        <div class="wtmd-actions">
                            <button type="button" class="wtmd-btn wtmd-btn-outline wtmd-btn-sm"
                                onclick="wtmdOpenEdit({{ $row->id }}, @js($row->value))">
                                <i class="fas fa-pen"></i> Edit
                            </button>
                            @if($row->usage_count > 0)
                            <span class="wtmd-in-use">In use</span>
                            @else
                            <form method="POST" action="{{ route('wt.admin.masterData.destroy', $row->id) }}"
                                data-modern-confirm="Delete &quot;{{ $row->value }}&quot;?"
                                data-modern-confirm-title="Delete {{ $categories[$activeTab] }}"
                                data-modern-confirm-remark="false" style="margin:0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="wtmd-btn wtmd-btn-danger wtmd-btn-sm">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="wtmd-empty">No {{ strtolower(\Illuminate\Support\Str::plural($categories[$activeTab])) }} found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Add / Edit Modal --}}
<div id="wtmdModal" class="modal-overlay" onclick="wtmdOutside(event)" aria-hidden="true">
    <div class="modal-box" style="max-width:460px">
        <div class="modal-header">
            <div>
                <h2 class="modal-title" id="wtmdModalTitle">Add {{ $categories[$activeTab] }}</h2>
                <p class="modal-subtitle">{{ $categories[$activeTab] }} values for walkie talkie records.</p>
            </div>
            <button type="button" class="modal-close-btn" onclick="wtmdClose()"><i class="fas fa-xmark"></i></button>
        </div>
        <form method="POST" id="wtmdForm" action="{{ route('wt.admin.masterData.store') }}">
            @csrf
            <input type="hidden" name="category" value="{{ $activeTab }}">
            <input type="hidden" name="_method" id="wtmdMethod" value="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">{{ $categories[$activeTab] }} Value <span class="required">*</span></label>
                    <input type="text" name="value" id="wtmdValue" class="form-input" required maxlength="255"
                        placeholder="e.g. {{ $activeTab === 'model' ? 'P8200' : ($activeTab === 'ownership_type' ? 'SHARED' : 'OPERATIONS') }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="wtmdClose()">Cancel</button>
                <button type="submit" class="btn-submit" id="wtmdSubmit">Add</button>
            </div>
        </form>
    </div>
</div>

{{-- "In Use" drill-down Modal --}}
<div id="wtmdUsageModal" class="modal-overlay" onclick="wtmdUsageOutside(event)" aria-hidden="true">
    <div class="modal-box" style="max-width:880px">
        <div class="modal-header">
            <div>
                <h2 class="modal-title" id="wtmdUsageTitle">In Use</h2>
                <p class="modal-subtitle" id="wtmdUsageSubtitle">Walkie talkies using this value.</p>
            </div>
            <button type="button" class="modal-close-btn" onclick="wtmdUsageClose()"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="modal-body" style="max-height:60vh;overflow:auto;padding:0">
            <table class="wtmd-usage-table">
                <thead>
                    <tr>
                        <th style="width:44px">#</th>
                        <th>Radio ID</th>
                        <th>Serial No.</th>
                        <th>Model</th>
                        <th>Status</th>
                        <th>Ownership Type</th>
                        <th>Ownership</th>
                        <th>Department</th>
                        <th>Position</th>
                    </tr>
                </thead>
                <tbody id="wtmdUsageBody">
                    <tr><td colspan="9" style="text-align:center;padding:32px;color:var(--muted)">Loading…</td></tr>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="wtmdUsageClose()">Close</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var modal = document.getElementById('wtmdModal');
    var form = document.getElementById('wtmdForm');
    var methodField = document.getElementById('wtmdMethod');
    var valueField = document.getElementById('wtmdValue');
    var title = document.getElementById('wtmdModalTitle');
    var submit = document.getElementById('wtmdSubmit');
    var label = @js($categories[$activeTab]);
    var storeUrl = @js(route('wt.admin.masterData.index'));
    var baseUrl = @js(url('wt/admin/master-data'));

    window.wtmdOpenAdd = function () {
        form.action = @js(route('wt.admin.masterData.store'));
        methodField.value = 'POST';
        valueField.value = '';
        title.textContent = 'Add ' + label;
        submit.textContent = 'Add';
        wtmdShow();
        setTimeout(function () { valueField.focus(); }, 60);
    };

    window.wtmdOpenEdit = function (id, value) {
        form.action = baseUrl + '/' + id;
        methodField.value = 'PUT';
        valueField.value = value;
        title.textContent = 'Edit ' + label;
        submit.textContent = 'Save Changes';
        wtmdShow();
        setTimeout(function () { valueField.focus(); valueField.select(); }, 60);
    };

    function wtmdShow() {
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }
    window.wtmdClose = function () {
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    };
    window.wtmdOutside = function (event) {
        if (event.target === modal) wtmdClose();
    };

    // ── "In Use" drill-down ──
    var usageModal = document.getElementById('wtmdUsageModal');
    var usageBody = document.getElementById('wtmdUsageBody');
    var usageTitle = document.getElementById('wtmdUsageTitle');
    var usageSubtitle = document.getElementById('wtmdUsageSubtitle');
    var category = @js($activeTab);
    var usageUrl = @js(route('wt.admin.masterData.usage'));

    function esc(value) {
        return String(value == null ? '' : value).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c];
        });
    }

    window.wtmdShowUsage = function (value, count) {
        usageTitle.textContent = label + ': ' + value;
        usageSubtitle.textContent = count + ' walkie talkie' + (count === 1 ? '' : 's') + ' using this ' + label.toLowerCase() + '.';
        usageBody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:32px;color:var(--muted)">Loading…</td></tr>';
        usageModal.classList.add('active');
        usageModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        fetch(usageUrl + '?category=' + encodeURIComponent(category) + '&value=' + encodeURIComponent(value), {
            headers: { 'Accept': 'application/json' }
        })
            .then(function (r) { if (!r.ok) throw new Error('Failed to load.'); return r.json(); })
            .then(function (data) {
                var rows = data.records || [];
                if (!rows.length) {
                    usageBody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:32px;color:var(--muted)">No matching walkie talkies.</td></tr>';
                    return;
                }
                usageBody.innerHTML = rows.map(function (w, i) {
                    return '<tr>' +
                        '<td style="color:var(--muted)">' + (i + 1) + '</td>' +
                        '<td style="font-weight:800">' + esc(w.radio_id) + '</td>' +
                        '<td>' + esc(w.serial_number) + '</td>' +
                        '<td>' + esc(w.model) + '</td>' +
                        '<td><span class="wtmd-status-chip">' + esc(w.status) + '</span></td>' +
                        '<td>' + esc(w.ownership_type) + '</td>' +
                        '<td>' + esc(w.ownership) + '</td>' +
                        '<td>' + esc(w.department) + '</td>' +
                        '<td>' + esc(w.position) + '</td>' +
                        '</tr>';
                }).join('');
            })
            .catch(function () {
                usageBody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:32px;color:#dc3545">Unable to load details.</td></tr>';
            });
    };

    window.wtmdUsageClose = function () {
        usageModal.classList.remove('active');
        usageModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    };
    window.wtmdUsageOutside = function (event) {
        if (event.target === usageModal) wtmdUsageClose();
    };

    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { wtmdClose(); wtmdUsageClose(); } });
})();
</script>
@endpush
