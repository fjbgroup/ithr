@extends('wt.layouts.admin')

@section('title', 'My Profile')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .profile-page-shell { max-width:980px; }
    .profile-summary { border:1px solid var(--border);border-radius:12px;background:var(--surface);padding:10px; }
    .profile-avatar-clean { display:flex;width:34px;height:34px;align-items:center;justify-content:center;border-radius:9px;background:var(--accent);color:#fff;font-size:14px;font-weight:900; }
    .profile-summary-name { color:var(--text);font-size:11px;font-weight:900;text-transform:uppercase; }
    .profile-summary-role { margin-top:2px;color:var(--muted);font-size:7px;font-weight:900;letter-spacing:.16em;text-transform:uppercase; }
    .profile-pill { display:inline-flex;align-items:center;gap:5px;border-radius:999px;border:1px solid var(--border);background:var(--body-bg);padding:5px 8px;color:var(--text);font-size:8px;font-weight:900;text-transform:uppercase; }
    .profile-form-card { margin-top:8px;border:1px solid var(--border);border-radius:12px;background:var(--surface);padding:12px; }
    .profile-form-title { margin-bottom:10px;color:var(--text);font-size:9px;font-weight:900;letter-spacing:.1em;text-transform:uppercase; }
    .profile-label-clean { margin-bottom:4px;display:block;color:var(--muted);font-size:7px;font-weight:900;letter-spacing:.14em;text-transform:uppercase; }
    .profile-input-clean { width:100%;min-height:30px;border-radius:8px;border:1px solid var(--border);background:var(--surface);padding:6px 9px;color:var(--text);font-size:9px;font-weight:800;outline:none;transition:border-color .16s ease,box-shadow .16s ease; }
    .profile-input-clean:focus { border-color:var(--accent) !important;box-shadow:0 0 0 3px rgba(2,132,199,.12) !important; }
    .profile-input-clean[disabled] { background:var(--body-bg);color:var(--muted);cursor:not-allowed; }
    .profile-save-clean { display:inline-flex;min-height:30px;align-items:center;justify-content:center;gap:6px;border-radius:8px;background:var(--navy);padding:7px 11px;color:#fff;font-size:8px;font-weight:900;letter-spacing:.12em;text-transform:uppercase;border:none;cursor:pointer; }
    .profile-save-clean:hover { background:var(--accent); }
    .profile-select + .select2-container { width:100% !important; }
    .profile-select + .select2-container .select2-selection--single { min-height:30px;border-radius:8px;border:1px solid var(--border);background:var(--surface);padding:2px 9px;display:flex;align-items:center; }
    .profile-select + .select2-container .select2-selection__rendered { line-height:1.4 !important;padding-left:0 !important;padding-right:26px !important;color:var(--text) !important;font-size:9px !important;font-weight:800 !important;text-transform:uppercase !important; }
    .profile-select + .select2-container .select2-selection__placeholder { color:var(--muted) !important; }
    .profile-select + .select2-container .select2-selection__arrow { height:100% !important;right:8px !important; }
    .select2-dropdown { border:1px solid var(--border) !important;border-radius:10px !important;overflow:hidden;box-shadow:0 18px 38px rgba(15,23,42,.16);background:var(--surface) !important; }
    .select2-search--dropdown { padding:6px !important;background:var(--body-bg) !important;border-bottom:1px solid var(--border); }
    .select2-search--dropdown .select2-search__field { border:1px solid var(--border) !important;border-radius:7px !important;padding:5px 7px !important;font-size:9px !important;font-weight:800 !important;background:var(--surface) !important;color:var(--text) !important;text-transform:uppercase !important; }
    .select2-results__option { padding:7px 9px;font-size:9px;font-weight:800;background:var(--surface) !important;color:var(--text) !important;text-transform:uppercase !important; }
    .select2-container--default .select2-results__option--selected,
    .select2-container--default .select2-results__option[aria-selected=true] { background:var(--body-bg) !important;color:var(--text) !important; }
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable { background:var(--accent) !important;color:#fff !important; }
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
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-2">
                <div class="profile-avatar-clean">{{ strtoupper(substr(Auth::guard('wt')->user()->username, 0, 1)) }}</div>
                <div>
                    <h4 class="profile-summary-name">{{ strtoupper(Auth::guard('wt')->user()->full_name ?: Auth::guard('wt')->user()->username) }}</h4>
                    <p class="profile-summary-role">{{ Auth::guard('wt')->user()->wt_role === 'admin_it' ? 'ICT Department' : 'Executive Account' }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-1.5">
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
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div>
                    <label class="profile-label-clean">Staff ID</label>
                    <input type="text" value="{{ Auth::guard('wt')->user()->staff_id }}" class="profile-input-clean" readonly disabled>
                </div>
                <div>
                    <label class="profile-label-clean">Username</label>
                    <input type="text" value="{{ Auth::guard('wt')->user()->username }}" class="profile-input-clean" readonly disabled>
                </div>
                <div class="md:col-span-2">
                    <label class="profile-label-clean">Full Name</label>
                    <input type="text" name="full_name" value="{{ strtoupper((string) old('full_name', Auth::guard('wt')->user()->full_name)) }}" placeholder="Enter your full name" class="uppercase-input profile-input-clean" required>
                </div>
                <div class="md:col-span-2">
                    <label class="profile-label-clean">Phone No</label>
                    <input type="text" name="phone_no" value="{{ old('phone_no', Auth::guard('wt')->user()->phone_no) }}" placeholder="Enter phone number" class="profile-input-clean">
                </div>
                <div>
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
                <div>
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


