@extends('wt.layouts.admin')

@section('title', 'My Profile')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .profile-page-shell {
        max-width: 980px;
    }
    .profile-summary {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        padding: 10px;
    }
    .profile-avatar-clean {
        display: flex;
        width: 34px;
        height: 34px;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: #0ea5e9;
        color: #ffffff;
        font-size: 14px;
        font-weight: 900;
    }
    .profile-summary-name {
        color: #0f172a;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
    }
    .profile-summary-role {
        margin-top: 2px;
        color: #64748b;
        font-size: 7px;
        font-weight: 900;
        letter-spacing: 0.16em;
        text-transform: uppercase;
    }
    .profile-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        padding: 5px 8px;
        color: #334155;
        font-size: 8px;
        font-weight: 900;
        text-transform: uppercase;
    }
    .profile-form-card {
        margin-top: 8px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        padding: 12px;
    }
    .profile-form-title {
        margin-bottom: 10px;
        color: #0f172a;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }
    .profile-label-clean {
        margin-bottom: 4px;
        display: block;
        color: #64748b;
        font-size: 7px;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }
    .profile-input-clean {
        width: 100%;
        min-height: 30px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        padding: 6px 9px;
        color: #0f172a;
        font-size: 9px;
        font-weight: 800;
        outline: none;
        transition: border-color 0.16s ease, box-shadow 0.16s ease, background 0.16s ease;
    }
    .profile-input-clean:focus {
        border-color: #0ea5e9 !important;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.12) !important;
    }
    .profile-input-clean[disabled] {
        background: #f1f5f9;
        color: #64748b;
        cursor: not-allowed;
    }
    .profile-save-clean {
        display: inline-flex;
        min-height: 30px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: 8px;
        background: #0f172a;
        padding: 7px 11px;
        color: #ffffff;
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .profile-save-clean:hover {
        background: #0369a1;
    }
    .profile-select + .select2-container {
        width: 100% !important;
    }
    .profile-select + .select2-container .select2-selection--single {
        min-height: 30px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        padding: 2px 9px;
        display: flex;
        align-items: center;
    }
    .profile-select + .select2-container .select2-selection__rendered {
        line-height: 1.4 !important;
        padding-left: 0 !important;
        padding-right: 26px !important;
        color: #0f172a !important;
        font-size: 9px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
    }
    .profile-select + .select2-container .select2-selection__placeholder {
        color: #64748b !important;
    }
    .profile-select + .select2-container .select2-selection__arrow {
        height: 100% !important;
        right: 8px !important;
    }
    .select2-dropdown {
        border: 1px solid #cbd5e1 !important;
        border-radius: 10px !important;
        overflow: hidden;
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.16);
        background: #ffffff !important;
    }
    .select2-search--dropdown {
        padding: 6px !important;
        background: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #cbd5e1 !important;
        border-radius: 7px !important;
        padding: 5px 7px !important;
        font-size: 9px !important;
        font-weight: 800 !important;
        background: #ffffff !important;
        color: #0f172a !important;
        text-transform: uppercase !important;
    }
    .select2-search--dropdown .select2-search__field::placeholder {
        color: #64748b !important;
        opacity: 1 !important;
    }
    .select2-results__option {
        padding: 7px 9px;
        font-size: 9px;
        font-weight: 800;
        background: #ffffff !important;
        color: #334155 !important;
        text-transform: uppercase !important;
    }
    .select2-container--default .select2-results__option--selected,
    .select2-container--default .select2-results__option[aria-selected=true] {
        background: #e0f2fe !important;
        color: #075985 !important;
    }
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background: #0284c7 !important;
        color: #ffffff !important;
    }
    html.dark .profile-summary,
    html.dark .profile-form-card {
        border-color: rgba(148, 163, 184, 0.18);
        background: #162236;
    }
    html.dark .profile-summary-name,
    html.dark .profile-form-title {
        color: #f8fafc;
    }
    html.dark .profile-summary-role,
    html.dark .profile-label-clean {
        color: #94a3b8;
    }
    html.dark .profile-pill {
        border-color: rgba(148, 163, 184, 0.18);
        background: rgba(15, 23, 42, 0.42);
        color: #cbd5e1;
    }
    html.dark .profile-input-clean {
        border-color: rgba(148, 163, 184, 0.22);
        background: #0f172a;
        color: #f8fafc;
    }
    html.dark .profile-input-clean[disabled] {
        background: rgba(15, 23, 42, 0.52);
        color: #94a3b8;
    }
    html.dark .profile-save-clean {
        border: 1px solid rgba(56, 189, 248, 0.34);
        background: rgba(14, 165, 233, 0.14);
        color: #bae6fd;
    }
    html.dark .profile-save-clean:hover {
        background: rgba(14, 165, 233, 0.24);
    }
    html.dark .profile-select + .select2-container .select2-selection--single {
        border-color: rgba(148, 163, 184, 0.22);
        background: #0f172a;
    }
    html.dark .profile-select + .select2-container .select2-selection__rendered {
        color: #f8fafc !important;
    }
    html.dark .profile-select + .select2-container .select2-selection__placeholder {
        color: #94a3b8 !important;
    }
    html.dark .select2-dropdown {
        border-color: #334155 !important;
        background: #0f172a !important;
        box-shadow: 0 18px 38px rgba(0, 0, 0, 0.32);
    }
    html.dark .select2-search--dropdown {
        background: #111827 !important;
        border-bottom-color: rgba(51, 65, 85, 0.7);
    }
    html.dark .select2-search--dropdown .select2-search__field {
        border-color: #475569 !important;
        background: #0f172a !important;
        color: #e2e8f0 !important;
    }
    html.dark .select2-results__option {
        background: #0f172a !important;
        color: #cbd5e1 !important;
    }
    html.dark .select2-container--default .select2-results__option--selected,
    html.dark .select2-container--default .select2-results__option[aria-selected=true] {
        background: rgba(14, 165, 233, 0.2) !important;
        color: #bae6fd !important;
    }
</style>
@endpush

@section('content')
@php
    $departmentOptions = $formOptionLists['departments'] ?? [];
    $positionOptions = $formOptionLists['positions'] ?? [];
@endphp
<div class="page-header-block">
    <h3 class="page-title-standard">My Profile</h3>
    <p class="page-subtitle-standard">
        Manage your personal information and account details.
    </p>
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
                    <p class="profile-summary-role">{{ Auth::guard('wt')->user()->role === 'admin_it' ? 'ICT Department' : 'Executive Account' }}</p>
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


