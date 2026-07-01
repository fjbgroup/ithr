@extends('wt.layouts.admin')

@section('title', 'My Profile')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .profile-page-shell { width:100%;max-width:1280px;margin:0 auto; }
    .profile-summary { border:1px solid var(--border);border-radius:14px;background:var(--surface);padding:18px 20px;box-shadow:0 14px 36px rgba(15,23,42,.08); }
    .profile-summary-inline { display:flex;align-items:center;justify-content:flex-start;gap:14px;flex-wrap:wrap;text-align:left; }
    .profile-summary-name { color:var(--text);font-size:15px;font-weight:900;text-transform:uppercase;line-height:1.2; }
    .profile-summary-role { margin-top:5px;color:var(--muted);font-size:9px;font-weight:900;letter-spacing:.16em;text-transform:uppercase; }
    .profile-summary-identity { display:flex;min-width:220px;flex-direction:column;justify-content:center;align-self:center;text-align:left; }
    .profile-pill-row { display:flex;align-items:center;justify-content:flex-start;gap:6px;flex-wrap:wrap;min-width:0; }
    .profile-pill { display:inline-flex;min-height:32px;align-items:center;gap:7px;border-radius:999px;border:1px solid var(--border);background:var(--body-bg);padding:7px 12px;color:var(--text);font-size:10px;font-weight:900;text-transform:uppercase; }
    .profile-form-card { margin-top:12px;border:1px solid var(--border);border-radius:14px;background:var(--surface);padding:20px;box-shadow:0 14px 36px rgba(15,23,42,.08); }
    .profile-form-title { margin-bottom:16px;color:var(--text);font-size:12px;font-weight:900;letter-spacing:.1em;text-transform:uppercase; }
    .profile-details-grid { display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:16px 18px;align-items:end; }
    .profile-field { min-width:0; }
    .profile-field-sm { grid-column:span 2; }
    .profile-field-md { grid-column:span 3; }
    .profile-label-clean { margin-bottom:7px;display:block;color:var(--muted);font-size:9px;font-weight:900;letter-spacing:.14em;text-transform:uppercase; }
    .profile-input-clean { width:100%;min-height:42px;border-radius:10px;border:1px solid var(--border);background:var(--form-input-bg);padding:9px 12px;color:var(--text);font-size:12px;font-weight:800;outline:none;transition:border-color .16s ease,box-shadow .16s ease; }
    .profile-input-clean:focus { border-color:var(--accent) !important;box-shadow:0 0 0 3px rgba(2,132,199,.12) !important; }
    .profile-input-clean[disabled] { background:var(--body-bg);color:var(--muted);cursor:not-allowed; }
    .profile-save-clean { display:inline-flex;min-height:40px;align-items:center;justify-content:center;gap:8px;border-radius:10px;background:var(--navy);padding:10px 16px;color:#fff;font-size:10px;font-weight:900;letter-spacing:.12em;text-transform:uppercase;border:none;cursor:pointer; }
    .profile-save-clean:hover { background:var(--accent); }
    .profile-select + .select2-container { width:100% !important; }
    .profile-select + .select2-container .select2-selection--single { min-height:42px;border-radius:10px;border:1px solid var(--border);background:var(--form-input-bg);padding:5px 12px;display:flex;align-items:center; }
    .profile-select + .select2-container .select2-selection__rendered { line-height:1.4 !important;padding-left:0 !important;padding-right:28px !important;color:var(--text) !important;font-size:12px !important;font-weight:800 !important;text-transform:uppercase !important; }
    .profile-select + .select2-container .select2-selection__placeholder { color:var(--muted) !important; }
    .profile-select + .select2-container .select2-selection__arrow { height:100% !important;right:8px !important; }
    .select2-dropdown { border:1px solid var(--border) !important;border-radius:10px !important;overflow:hidden;box-shadow:0 18px 38px rgba(15,23,42,.16);background:var(--surface) !important; }
    .select2-search--dropdown { padding:6px !important;background:var(--body-bg) !important;border-bottom:1px solid var(--border); }
    .select2-search--dropdown .select2-search__field { border:1px solid var(--border) !important;border-radius:7px !important;padding:7px 9px !important;font-size:11px !important;font-weight:800 !important;background:var(--surface) !important;color:var(--text) !important;text-transform:uppercase !important; }
    .select2-results__option { padding:8px 10px;font-size:11px;font-weight:800;background:var(--surface) !important;color:var(--text) !important;text-transform:uppercase !important; }
    .select2-container--default .select2-results__option--selected,
    .select2-container--default .select2-results__option[aria-selected=true] { background:var(--body-bg) !important;color:var(--text) !important; }
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable { background:var(--accent) !important;color:#fff !important; }
    @media (max-width: 768px) {
        .profile-summary,
        .profile-form-card { padding:16px; }
        .profile-summary-inline { align-items:flex-start;gap:10px; }
        .profile-summary-identity { min-width:100%; }
        .profile-summary-name { font-size:13px; }
        .profile-details-grid { grid-template-columns:1fr; }
        .profile-field-sm,
        .profile-field-md { grid-column:span 1; }
        .profile-pill { width:100%;justify-content:flex-start; }
        .profile-save-clean { width:100%; }
    }
</style>
@endpush

@section('content')
@php
    $departmentOptions = $formOptionLists['departments'] ?? [];
    $positionOptions = $formOptionLists['positions'] ?? [];
@endphp
<div style="margin-bottom:18px">
    <div style="font-size:16px;font-weight:800;color:var(--text)">My Profile</div>
    <p style="margin-top:4px;font-size:12px;color:var(--muted)">Manage your personal information and account details.</p>
</div>

<div class="profile-page-shell w-full">
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <span class="font-bold text-sm">{{ session('success') }}</span>
    </div>
    @endif

    <div class="profile-summary">
        <div class="profile-summary-inline">
            <div class="profile-summary-identity">
                <h4 class="profile-summary-name">{{ strtoupper(Auth::guard('wt')->user()->full_name ?: Auth::guard('wt')->user()->username) }}</h4>
                <p class="profile-summary-role">{{ Auth::guard('wt')->user()->wt_role === 'admin_it' ? 'ICT Department' : 'Executive Account' }}</p>
            </div>
            <div class="profile-pill-row">
                <span class="profile-pill"><i class="fas fa-id-badge"></i>{{ Auth::guard('wt')->user()->staff_id ?: '-' }}</span>
                <span class="profile-pill"><i class="fas fa-user"></i>{{ Auth::guard('wt')->user()->username ?: '-' }}</span>
                <span class="profile-pill"><i class="fas fa-building"></i>{{ Auth::guard('wt')->user()->department ?: 'No Department' }}</span>
            </div>
        </div>
    </div>

    <div class="profile-form-card">
        <form action="{{ route('wt.admin.profile.update') }}" method="POST">
            @csrf
            <h4 class="profile-form-title">Personal Details</h4>
            <div class="profile-details-grid">
                <div class="profile-field profile-field-sm">
                    <label class="profile-label-clean">Staff ID</label>
                    <input type="text" value="{{ Auth::guard('wt')->user()->staff_id }}" class="profile-input-clean" readonly disabled>
                </div>
                <div class="profile-field profile-field-sm">
                    <label class="profile-label-clean">Username</label>
                    <input type="text" value="{{ Auth::guard('wt')->user()->username }}" class="profile-input-clean" readonly disabled>
                </div>
                <div class="profile-field profile-field-sm">
                    <label class="profile-label-clean">Phone No</label>
                    <input type="text" name="phone_no" value="{{ old('phone_no', Auth::guard('wt')->user()->phone_no) }}" placeholder="Enter phone number" class="profile-input-clean">
                </div>
                <div class="profile-field profile-field-md">
                    <label class="profile-label-clean">Full Name</label>
                    <input type="text" name="full_name" value="{{ strtoupper((string) old('full_name', Auth::guard('wt')->user()->full_name)) }}" placeholder="Enter your full name" class="uppercase-input profile-input-clean" required>
                </div>
                <div class="profile-field profile-field-md">
                    <label class="profile-label-clean">Department</label>
                    <select name="department" class="profile-select w-full" data-placeholder="Type or select department" required>
                        <option value=""></option>
                        @foreach($departmentOptions as $department)
                        <option value="{{ strtoupper((string) $department) }}" @selected(strtoupper(trim((string) old('department', Auth::guard('wt')->user()->department))) === strtoupper(trim((string) $department)))>{{ strtoupper((string) $department) }}</option>
                        @endforeach
                        @if(old('department', Auth::guard('wt')->user()->department) && !collect($departmentOptions)->contains(fn ($department) => strtoupper(trim((string) $department)) === strtoupper(trim((string) old('department', Auth::guard('wt')->user()->department)))))
                        <option value="{{ strtoupper((string) old('department', Auth::guard('wt')->user()->department)) }}" selected>{{ strtoupper((string) old('department', Auth::guard('wt')->user()->department)) }}</option>
                        @endif
                    </select>
                </div>
                <div class="profile-field profile-field-md">
                    <label class="profile-label-clean">Position</label>
                    <select name="position" class="profile-select w-full" data-placeholder="Type or select position" required>
                        <option value=""></option>
                        @foreach($positionOptions as $position)
                        <option value="{{ strtoupper((string) $position) }}" @selected(strtoupper(trim((string) old('position', Auth::guard('wt')->user()->position))) === strtoupper(trim((string) $position)))>{{ strtoupper((string) $position) }}</option>
                        @endforeach
                        @if(old('position', Auth::guard('wt')->user()->position) && !collect($positionOptions)->contains(fn ($position) => strtoupper(trim((string) $position)) === strtoupper(trim((string) old('position', Auth::guard('wt')->user()->position)))))
                        <option value="{{ strtoupper((string) old('position', Auth::guard('wt')->user()->position)) }}" selected>{{ strtoupper((string) old('position', Auth::guard('wt')->user()->position)) }}</option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button type="submit" class="profile-save-clean">
                    Save Changes <i class="fas fa-save"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.uppercase-input').on('input', function () {
            this.value = this.value.toUpperCase();
        });

        $('.profile-select').select2({
            width: '100%',
            tags: true,
            placeholder: function () {
                return $(this).data('placeholder') || 'Type or select option';
            },
            allowClear: true,
            createTag: function (params) {
                const term = $.trim(params.term);
                if (term === '') return null;
                return {
                    id: term.toUpperCase(),
                    text: term.toUpperCase(),
                    newTag: true
                };
            },
            insertTag: function (data, tag) {
                data.unshift(tag);
            }
        });

        $(document).on('input', '.select2-search__field', function () {
            this.value = this.value.toUpperCase();
        });
    });
</script>
@endpush
