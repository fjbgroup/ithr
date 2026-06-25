@extends('wt.layouts.user')

@section('title', 'Request Access')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .form-section-title {
        color: #0284c7;
        border-left: 4px solid #0284c7;
        padding-left: 10px;
        font-weight: 800;
        margin-bottom: 16px;
        margin-top: 24px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-size: 10px;
    }
    input:focus, select:focus, textarea:focus {
        border-color: var(--accent) !important;
        box-shadow: 0 0 0 2px rgba(2,132,199,.12) !important;
        outline: none;
    }

    .radio-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px; }
    .radio-item { position: relative; }
    .radio-item input { position: absolute; opacity: 0; }
    .radio-item label {
        display: block; background: var(--body-bg); border: 1px solid var(--border);
        padding: 8px 10px; border-radius: 10px; text-align: center; font-size: 11px;
        cursor: pointer; color: var(--muted); font-weight: 600; transition: all 0.2s;
    }
    .radio-item input:checked + label {
        background: var(--accent); border-color: var(--accent); color: #fff;
        box-shadow: 0 4px 10px rgba(2,132,199,.25);
    }
    .checkbox-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 12px; }
    .checkbox-item {
        display: flex; align-items: center; gap: 10px;
        background: var(--body-bg); border: 1px solid var(--border);
        padding: 12px 14px; border-radius: 12px; color: var(--muted); font-weight: 600;
    }
    .checkbox-item input { width: 16px; height: 16px; accent-color: var(--accent); }
    .select2-container--default .select2-selection--multiple {
        border-color: var(--border) !important;
        border-radius: 0.75rem !important;
        padding: 6px !important;
        background: var(--surface) !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: var(--accent) !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: var(--accent) !important;
        border: none !important;
        color: #fff !important;
        border-radius: 8px !important;
        padding: 4px 10px !important;
    }
    .smart-select + .select2-container,
    .dept-select + .select2-container { width: 100% !important; }
    .smart-select + .select2-container .select2-selection--single {
        min-height: 42px;
        border-radius: 0.75rem !important;
        border: 1px solid var(--border) !important;
        background: var(--surface) !important;
        padding: 6px 12px !important;
        display: flex !important;
        align-items: center !important;
    }
    .smart-select + .select2-container .select2-selection__rendered {
        color: var(--text) !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        padding-left: 0 !important;
        padding-right: 24px !important;
        line-height: 1.3 !important;
        text-transform: uppercase;
    }
    .smart-select + .select2-container .select2-selection__placeholder {
        color: var(--muted) !important;
    }
    .smart-select + .select2-container .select2-selection__arrow {
        height: 100% !important;
        right: 12px !important;
    }
    .select2-dropdown {
        border: 1px solid var(--border) !important;
        border-radius: 0 0 14px 14px !important;
        overflow: hidden;
        background: var(--surface) !important;
        box-shadow: 0 12px 24px rgba(15,23,42,.12);
    }
    .select2-search--dropdown,
    .select2-results,
    .select2-results > .select2-results__options {
        background: var(--surface) !important;
    }
    .select2-search--dropdown { padding: 10px !important; }
    .select2-search--dropdown .select2-search__field {
        border: 2px solid var(--accent) !important;
        border-radius: 11px !important;
        background: var(--body-bg) !important;
        color: var(--text) !important;
        padding: 8px 10px !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        text-transform: uppercase;
        outline: none !important;
    }
    .select2-results__option {
        background: var(--surface) !important;
        color: var(--text) !important;
        padding: 10px 16px !important;
        font-size: 11px !important;
        font-weight: 900 !important;
        text-transform: uppercase;
    }
    .select2-container--default .select2-results__option--selected,
    .select2-container--default .select2-results__option[aria-selected=true] {
        background: var(--body-bg) !important;
        color: var(--text) !important;
    }
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background: var(--accent) !important;
        color: #fff !important;
    }
    .request-compact-shell {
        max-width: 980px !important;
        margin-left: auto !important;
        margin-right: auto !important;
        font-size: 10px !important;
    }
    .request-compact-card {
        border-radius: 10px !important;
        padding: 10px 12px !important;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04) !important;
    }
    .request-compact-card form {
        gap: 9px !important;
    }
    .request-compact-card h2 {
        font-size: 11px !important;
        line-height: 1.2 !important;
    }
    .request-compact-card .form-section-title {
        margin-top: 8px !important;
        margin-bottom: 6px !important;
        padding-left: 8px !important;
        font-size: 9.5px !important;
        line-height: 1.2 !important;
        letter-spacing: 0.14em !important;
    }
    .request-compact-card .grid {
        gap: 7px !important;
    }
    .request-compact-card .mb-5,
    .request-compact-card .mb-4,
    .request-compact-card .mb-3 {
        margin-bottom: 7px !important;
    }
    .request-compact-card .mt-5,
    .request-compact-card .mt-4,
    .request-compact-card .mt-3,
    .request-compact-card .mt-2,
    .request-compact-card .mt-1 {
        margin-top: 4px !important;
    }
    .request-compact-card .p-5,
    .request-compact-card .p-4 {
        padding: 8px !important;
    }
    .request-compact-card .px-4,
    .request-compact-card .px-3 {
        padding-left: 8px !important;
        padding-right: 8px !important;
    }
    .request-compact-card .py-3,
    .request-compact-card .py-2,
    .request-compact-card .py-2\.5 {
        padding-top: 5px !important;
        padding-bottom: 5px !important;
    }
    .request-compact-card label,
    .request-compact-card p,
    .request-compact-card span {
        font-size: 9px !important;
        line-height: 1.25 !important;
    }
    .request-compact-card label {
        font-size: 8.5px !important;
        margin-bottom: 3px !important;
        letter-spacing: 0.08em !important;
    }
    .request-compact-card input:not([type="checkbox"]):not([type="radio"]),
    .request-compact-card select,
    .request-compact-card textarea {
        min-height: 28px !important;
        height: 28px !important;
        border-radius: 7px !important;
        padding: 5px 8px !important;
        font-size: 9.5px !important;
        line-height: 1.2 !important;
        border: 1px solid var(--border) !important;
        background: var(--surface) !important;
        color: var(--text) !important;
    }
    .request-compact-card textarea {
        min-height: 44px !important;
        height: auto !important;
    }
    .request-compact-card label,
    .request-compact-card p,
    .request-compact-card span { color: var(--text) !important; }
    .request-compact-card label { color: var(--muted) !important; }
    .request-compact-card .smart-select + .select2-container .select2-selection--single,
    .request-compact-card .dept-select + .select2-container .select2-selection--multiple {
        min-height: 28px !important;
        border-radius: 7px !important;
        padding: 2px 8px !important;
    }
    .request-compact-card .smart-select + .select2-container .select2-selection__rendered {
        font-size: 9.5px !important;
        line-height: 1.2 !important;
    }
    .request-compact-card .rounded-2xl,
    .request-compact-card .rounded-xl {
        border-radius: 8px !important;
    }
    .request-compact-card .flex.items-center.gap-2\.5 {
        gap: 8px !important;
        margin-bottom: 6px !important;
    }
    .request-compact-card .bg-\[\#0284c7\].text-white {
        padding: 6px !important;
        border-radius: 7px !important;
    }
    .request-compact-card button,
    .request-compact-card .radio-item label,
    .request-compact-card .checkbox-item {
        min-height: 28px !important;
        padding: 6px 9px !important;
        border-radius: 7px !important;
        font-size: 9px !important;
    }
    .request-compact-card input[type="checkbox"],
    .request-compact-card input[type="radio"] {
        width: 12px !important;
        height: 12px !important;
    }
    .signature-pad {
        border: 1px solid var(--border);
        border-radius: 8px;
        background: var(--surface);
        overflow: hidden;
    }
    .signature-pad canvas {
        display: block;
        width: 100%;
        height: 140px;
        background: #fff;
        touch-action: none;
    }
    .signature-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border-top: 1px solid var(--border);
        padding: 8px 10px;
        background: var(--body-bg);
    }
    .signature-clear {
        border: 1px solid var(--border);
        border-radius: 7px;
        background: var(--surface);
        color: var(--text);
        font-size: 9px;
        font-weight: 900;
        letter-spacing: .12em;
        text-transform: uppercase;
        padding: 6px 10px;
    }
</style>
@endpush

@section('content')
@php
    $currentUser = auth('wt')->user();
    $nameOptions = $formOptionLists['names'] ?? [];
    $departmentOptions = $formOptionLists['departments'] ?? [];
    $sectorOptions = $formOptionLists['sectors'] ?? [];
    $locationOptions = $formOptionLists['locations'] ?? [];
    $bayOptions = $formOptionLists['bays'] ?? [];
    $profileName = old('requestor_name', $currentUser->full_name ?: $currentUser->username);
    $profileStaffId = old('requestor_staff_id', $currentUser->staff_id);
    $profileDepartments = collect(old('requestor_dept', $currentUser->department ? [$currentUser->department] : []));
@endphp
<div class="request-compact-shell">
<div style="margin-bottom:18px">
    <div style="font-size:16px;font-weight:800;color:var(--text)">Request Access</div>
    <p style="margin-top:4px;font-size:12px;color:var(--muted)">Submit a formal application to borrow communication equipment.</p>
</div>

<div class="request-compact-card table-card" style="padding:20px 22px">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
        <div style="background:var(--accent);color:#fff;padding:8px;border-radius:8px;display:flex;align-items:center">
            <i class="fa-solid fa-hand-holding-hand"></i>
        </div>
        <h2 style="font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.1em;color:var(--text);margin:0">Walkie Talkie Request Form</h2>
    </div>

    <form action="{{ route('wt.user.requests.store') }}" method="POST" class="space-y-4" id="walkieRequestForm">
        @csrf
        <input type="hidden" name="event_name" value="{{ old('event_name', 'General Request') }}">

        <!-- 1. USER INFORMATION -->
        <h4 class="form-section-title">1. User Information</h4>
        <div style="border:1px solid rgba(2,132,199,.2);background:rgba(2,132,199,.06);border-radius:8px;padding:12px 14px;margin-bottom:12px">
            <p style="font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.1em;color:var(--accent);margin:0">Profile Note</p>
            <p style="margin-top:4px;font-size:10px;font-weight:700;line-height:1.6;color:var(--text);margin-bottom:0">
                Your personal details are filled automatically based on <span style="text-transform:uppercase">My Profile</span>. If you need to update your name, staff ID, or department, please update them in <span style="text-transform:uppercase">My Profile</span> first.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-bold text-stone-600 mb-1 uppercase tracking-widest">Full Name</label>
                <input type="text" name="requestor_name" value="{{ strtoupper((string) $profileName) }}" class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-100 text-[11px] font-bold uppercase" readonly required>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-stone-600 mb-1 uppercase tracking-widest">Staff ID</label>
                <input type="text" name="requestor_staff_id" value="{{ strtoupper((string) $profileStaffId) }}" class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-100 text-[11px] font-bold uppercase" readonly required>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-stone-600 mb-1 uppercase tracking-widest">Date</label>
                <input type="date" name="request_date" value="{{ old('request_date', date('Y-m-d')) }}" class="w-full px-3 py-2 rounded-lg border border-[#0284c7]/30 bg-[#FDFBF7]/50 text-[11px]" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold text-stone-600 mb-1 uppercase tracking-widest">Department</label>
                <select name="requestor_dept[]" class="dept-select w-full" multiple="multiple" required>
                    @foreach($departmentOptions as $department)
                    <option value="{{ $department }}" @selected($profileDepartments->contains($department))>{{ $department }}</option>
                    @endforeach
                    @foreach($profileDepartments as $department)
                        @if($department && !collect($departmentOptions)->contains($department))
                        <option value="{{ $department }}" selected>{{ $department }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>

        <!-- 2. OWNERSHIP TYPE -->
        <h4 class="form-section-title">2. Ownership Type</h4>
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="block text-[10px] font-bold text-stone-600 mb-2 uppercase tracking-widest">Select Ownership Type</label>
                <div class="radio-grid">
                    <div class="radio-item">
                        <input type="radio" name="ownership_type" id="own1" value="unallocated" {{ old('ownership_type') == 'unallocated' ? 'checked' : '' }}>
                        <label for="own1">Unallocated</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" name="ownership_type" id="own2" value="shared" {{ old('ownership_type') == 'shared' ? 'checked' : '' }}>
                        <label for="own2">Shared</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" name="ownership_type" id="own3" value="individual" {{ old('ownership_type', 'individual') == 'individual' ? 'checked' : '' }}>
                        <label for="own3">Individual</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" name="ownership_type" id="own4" value="spare" {{ old('ownership_type') == 'spare' ? 'checked' : '' }}>
                        <label for="own4">Spare</label>
                    </div>
                </div>
            </div>
            <div id="shared_with_section" class="{{ old('ownership_type') === 'shared' ? '' : 'hidden' }}">
                <label class="block text-[10px] font-bold text-stone-600 mb-1 uppercase tracking-widest">Shared With <span class="text-red-500">*</span></label>
                <input type="text" name="shared_with" id="shared_with" value="{{ strtoupper(old('shared_with', '')) }}" placeholder="E.G. USER / TEAM / DEPARTMENT" class="w-full px-3 py-2 rounded-lg border border-[#0284c7]/30 bg-[#FDFBF7]/50 text-[11px] font-bold uppercase">
                @error('shared_with')
                    <div class="text-red-600 text-xs font-bold mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- 3. REQUEST DETAILS -->
        <h4 class="form-section-title">3. Request Details</h4>
        
        <div class="mb-4">
            <label class="block text-[10px] font-bold text-stone-600 mb-2 uppercase tracking-widest">Sector</label>
            <select name="sector" class="smart-select w-full" data-placeholder="Type or select sector" required>
                <option value=""></option>
                @foreach($sectorOptions as $sector)
                <option value="{{ $sector }}" @selected(old('sector') === $sector)>{{ $sector }}</option>
                @endforeach
                @if(old('sector') && !in_array(old('sector'), $sectorOptions, true))
                <option value="{{ old('sector') }}" selected>{{ old('sector') }}</option>
                @endif
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div id="bay_section">
                <label class="block text-[10px] font-bold text-stone-600 mb-1 uppercase tracking-widest">Bay <span class="text-stone-400">(Optional)</span></label>
                <select name="bay_from" id="bay_from" class="smart-select w-full" data-placeholder="Type number only, e.g. 3">
                    <option value=""></option>
                    @foreach($bayOptions as $bay)
                    <option value="{{ $bay }}" @selected(old('bay_from') === $bay)>{{ $bay }}</option>
                    @endforeach
                    @if(old('bay_from') && !in_array(old('bay_from'), $bayOptions, true))
                    <option value="{{ old('bay_from') }}" selected>{{ old('bay_from') }}</option>
                    @endif
                </select>
                @error('bay_from')
                    <div class="text-red-600 text-xs font-bold mt-2">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="block text-[10px] font-bold text-stone-600 mb-2 uppercase tracking-widest">Location</label>
                <select name="location" class="smart-select w-full" data-placeholder="Type or select location" required>
                    <option value=""></option>
                    @foreach($locationOptions as $location)
                    <option value="{{ $location }}" @selected(old('location') === $location)>{{ $location }}</option>
                    @endforeach
                    @if(old('location') && !in_array(old('location'), $locationOptions, true))
                    <option value="{{ old('location') }}" selected>{{ old('location') }}</option>
                    @endif
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-stone-600 mb-2 uppercase tracking-widest">Justifications</label>
                <textarea name="justification" rows="1" placeholder="Reason for request..." class="w-full px-3 py-2 rounded-lg border border-[#0284c7]/30 bg-[#FDFBF7]/50 text-[11px]" required>{{ old('justification') }}</textarea>
            </div>
        </div>

        <!-- 4. SUBMIT TO -->
        <h4 class="form-section-title">4. Submit To</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold text-stone-600 mb-2 font-black uppercase tracking-widest">Submit request to Executive</label>
                <select name="submit_to_admin_id" id="submit_to_admin_id" class="admin-select w-full" required>
                    <option value="" disabled selected>Select an Executive...</option>
                    @foreach(($admins ?? collect()) as $admin)
                        <option value="{{ $admin->user_id }}" 
                                data-dept="{{ $admin->department ?? 'General' }}" 
                                {{ old('submit_to_admin_id') == $admin->user_id ? 'selected' : '' }}>
                            {{ strtoupper($admin->full_name ?: $admin->username) }} - {{ strtoupper($admin->department ?: 'NO DEPARTMENT') }}
                        </option>
                    @endforeach
                </select>
                
                <!-- Executive Info Display -->
                <div id="admin-details" style="display:none;margin-top:12px;padding:14px;background:var(--body-bg);border:1px solid var(--border);border-radius:10px">
                    <div class="flex items-center gap-4">
                        <div class="bg-white p-2.5 rounded-xl border border-[#0284c7]/10 text-[#0284c7]">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="grid grid-cols-1 gap-8 flex-1">
                            <div>
                                <p class="text-[9px] font-black text-stone-400 uppercase tracking-[0.2em] mb-1">Department</p>
                                <p id="admin-dept-text" class="text-xs font-bold text-[#142b47] uppercase">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                @error('submit_to_admin_id')
                    <div class="text-red-600 text-xs font-bold mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- 5. SIGNATURE -->
        <h4 class="form-section-title">5. Signature</h4>
        <div class="mb-4">
            <label class="block text-[10px] font-bold text-stone-600 mb-2 uppercase tracking-widest">Applicant Signature</label>
            <div class="signature-pad" data-signature-pad>
                <canvas></canvas>
                <div class="signature-actions">
                    <span style="font-size:9px;font-weight:800;color:var(--muted);text-transform:uppercase;letter-spacing:.12em">Sign inside the box</span>
                    <button type="button" class="signature-clear" data-signature-clear>Clear</button>
                </div>
            </div>
            <input type="hidden" name="request_signature" data-signature-input required>
            @error('request_signature')
                <div class="text-red-600 text-xs font-bold mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="pt-6 flex justify-end">
            <button type="submit" class="bg-[#0284c7] text-white px-8 py-3 rounded-xl font-black text-[11px] tracking-widest hover:bg-[#724D31] transition shadow-lg shadow-[#0284c7]/20 flex items-center gap-3 border border-[#A67B5B]">
                SUBMIT REQUEST <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.smart-select').select2({
            tags: true,
            width: '100%',
            allowClear: true,
            placeholder: function() {
                return $(this).data('placeholder') || 'Type or select option';
            },
            createTag: function(params) {
                const term = $.trim(params.term);
                if (term === '') return null;
                return { id: term.toUpperCase(), text: term.toUpperCase(), newTag: true };
            },
            insertTag: function(data, tag) {
                data.unshift(tag);
            }
        });
        $('.dept-select').select2({
            placeholder: "Select Department",
            tags: true,
            tokenSeparators: [','],
            width: '100%'
        });
        $('.admin-select').select2({ placeholder: "Select an Executive", allowClear: true });

        function setupSignaturePad(container) {
            const canvas = container.querySelector('canvas');
            const input = container.parentElement.querySelector('[data-signature-input]');
            const clearButton = container.querySelector('[data-signature-clear]');
            const context = canvas.getContext('2d');
            let drawing = false;
            let hasSignature = false;

            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const rect = canvas.getBoundingClientRect();
                const image = hasSignature ? canvas.toDataURL('image/png') : null;

                canvas.width = rect.width * ratio;
                canvas.height = rect.height * ratio;
                context.setTransform(ratio, 0, 0, ratio, 0, 0);
                context.lineWidth = 2;
                context.lineCap = 'round';
                context.lineJoin = 'round';
                context.strokeStyle = '#111827';

                if (image) {
                    const img = new Image();
                    img.onload = () => context.drawImage(img, 0, 0, rect.width, rect.height);
                    img.src = image;
                }
            }

            function point(event) {
                const rect = canvas.getBoundingClientRect();
                const source = event.touches ? event.touches[0] : event;
                return { x: source.clientX - rect.left, y: source.clientY - rect.top };
            }

            function updateInput() {
                input.value = hasSignature ? canvas.toDataURL('image/png') : '';
            }

            function start(event) {
                event.preventDefault();
                drawing = true;
                const pos = point(event);
                context.beginPath();
                context.moveTo(pos.x, pos.y);
            }

            function move(event) {
                if (!drawing) return;
                event.preventDefault();
                const pos = point(event);
                context.lineTo(pos.x, pos.y);
                context.stroke();
                hasSignature = true;
                updateInput();
            }

            function stop() {
                drawing = false;
                updateInput();
            }

            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);
            canvas.addEventListener('mousedown', start);
            canvas.addEventListener('mousemove', move);
            canvas.addEventListener('mouseup', stop);
            canvas.addEventListener('mouseleave', stop);
            canvas.addEventListener('touchstart', start, { passive: false });
            canvas.addEventListener('touchmove', move, { passive: false });
            canvas.addEventListener('touchend', stop);
            clearButton.addEventListener('click', function () {
                context.clearRect(0, 0, canvas.width, canvas.height);
                hasSignature = false;
                updateInput();
            });
        }

        document.querySelectorAll('[data-signature-pad]').forEach(setupSignaturePad);

        const walkieRequestForm = document.getElementById('walkieRequestForm');
        const requestSignatureInput = document.querySelector('[data-signature-input]');

        if (walkieRequestForm && requestSignatureInput) {
            walkieRequestForm.addEventListener('submit', function (event) {
                if (requestSignatureInput.value.trim() !== '') {
                    return;
                }

                event.preventDefault();
                alert('Please sign before submitting the request.');
                requestSignatureInput.closest('.mb-4')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        }

        function syncSharedWithVisibility() {
            const ownershipType = $('input[name="ownership_type"]:checked').val();
            const shouldShowSharedWith = ownershipType === 'shared';
            $('#shared_with_section').toggleClass('hidden', !shouldShowSharedWith);
            $('#shared_with').prop('required', shouldShowSharedWith);

            if (!shouldShowSharedWith) {
                $('#shared_with').val('');
            }
        }

        $('input[name="ownership_type"]').on('change', syncSharedWithVisibility);
        syncSharedWithVisibility();

        // Executive Details logic
        $('#submit_to_admin_id').on('change', function() {
            const selected = $(this).find(':selected');
            const dept = selected.data('dept');

            if (selected.val()) {
                $('#admin-dept-text').text(dept || 'NO DEPARTMENT');
                $('#admin-details').removeClass('hidden').addClass('animate-in fade-in slide-in-from-top-2 duration-300');
            } else {
                $('#admin-details').addClass('hidden');
            }
        });
    });
</script>
@endpush
