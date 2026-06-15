@extends('wt.layouts.admin')

@section('title', 'Handover WT')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .handover-search-select + .select2-container {
        width: 100% !important;
    }
    .handover-search-select + .select2-container .select2-selection--single {
        min-height: 42px;
        border-radius: 10px;
        border: 1px solid rgb(226 232 240);
        background: rgb(248 250 252);
        padding: 6px 12px;
        display: flex;
        align-items: center;
    }
    .handover-search-select + .select2-container .select2-selection__rendered {
        color: #334155 !important;
        font-size: 10px;
        font-weight: 700;
        line-height: 1.4 !important;
        padding-left: 0 !important;
        padding-right: 24px !important;
        text-transform: uppercase;
    }
    .handover-search-select + .select2-container .select2-selection__placeholder {
        color: #64748b !important;
    }
    .handover-search-select + .select2-container .select2-selection__arrow {
        height: 100% !important;
        right: 10px !important;
    }
    .select2-dropdown {
        border: 1px solid rgb(226 232 240) !important;
        border-radius: 12px !important;
        overflow: hidden;
    }
    .select2-search--dropdown {
        padding: 8px !important;
        background: #fff;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid rgb(203 213 225) !important;
        border-radius: 8px !important;
        padding: 8px 10px !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        text-transform: uppercase;
    }
    .select2-results__option {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 10px 12px;
    }
    .select2-results__option--highlighted.select2-results__option--selectable {
        background: #0ea5e9 !important;
        color: #fff !important;
    }
    .dark .handover-search-select + .select2-container .select2-selection--single {
        background: #020617;
        border-color: #334155;
    }
    .dark .handover-search-select + .select2-container .select2-selection__rendered {
        color: #e2e8f0 !important;
    }
    .dark .select2-dropdown,
    .dark .select2-search--dropdown,
    .dark .select2-results__options,
    .dark .select2-results__option {
        background: #020617 !important;
        color: #e2e8f0 !important;
        border-color: #334155 !important;
    }
    .dark .select2-search--dropdown .select2-search__field {
        background: #0f172a !important;
        color: #e2e8f0 !important;
        border-color: #475569 !important;
    }
    .handover-language-toggle {
        display: inline-grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        align-items: center;
        gap: 0;
        padding: 0;
        width: min(100%, 280px);
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.22);
        background: rgba(255, 255, 255, 0.78);
        backdrop-filter: blur(8px);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.72);
    }
    .handover-language-btn {
        border: 0;
        border-radius: 0;
        width: 100%;
        padding: 9px 12px;
        min-width: 0;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.09em;
        text-transform: uppercase;
        color: #64748b;
        background: transparent;
        line-height: 1;
        transition: all 0.18s ease;
    }
    .handover-language-btn:first-child {
        border-top-left-radius: 999px;
        border-bottom-left-radius: 999px;
    }
    .handover-language-btn:last-child {
        border-top-right-radius: 999px;
        border-bottom-right-radius: 999px;
    }
    .handover-language-btn.is-active {
        color: #ffffff;
        background: linear-gradient(135deg, #8B5E3C, #6f4a31);
        box-shadow: 0 8px 18px rgba(111, 74, 49, 0.22);
    }
    .handover-language-btn:not(.is-active):hover {
        color: #5b3f2b;
        background: rgba(255, 250, 245, 0.96);
    }
    .handover-terms-panel {
        border: 1px solid rgba(139, 94, 60, 0.12);
        background:
            radial-gradient(circle at top right, rgba(251, 191, 36, 0.10), transparent 26%),
            linear-gradient(135deg, rgba(255, 250, 245, 0.98), rgba(248, 250, 252, 0.98));
    }
    .handover-terms-copy {
        display: none;
    }
    .handover-terms-copy.is-active {
        display: block;
    }
    .handover-terms-list {
        display: grid;
        gap: 12px;
    }
    .handover-terms-item {
        border-radius: 16px;
        border: 1px solid rgba(148, 163, 184, 0.16);
        background: rgba(255, 255, 255, 0.86);
        padding: 14px 16px;
    }
    .handover-terms-item strong {
        color: #8B5E3C;
    }
    .handover-terms-sublist {
        margin-top: 8px;
        padding-left: 18px;
        list-style: lower-roman;
    }
    .handover-terms-sublist li + li {
        margin-top: 4px;
    }
    .dark .handover-language-toggle {
        background: rgba(15, 23, 42, 0.78);
        border-color: rgba(71, 85, 105, 0.7);
        box-shadow: inset 0 1px 0 rgba(148, 163, 184, 0.08);
    }
    .dark .handover-language-btn {
        color: #94a3b8;
    }
    .dark .handover-language-btn.is-active {
        color: #ffffff;
        background: linear-gradient(135deg, #8B5E3C, #a16207);
        box-shadow: none;
    }
    .dark .handover-language-btn:not(.is-active):hover {
        color: #e2e8f0;
        background: rgba(30, 41, 59, 0.9);
    }
    .dark .handover-terms-panel {
        border-color: #334155;
        background:
            radial-gradient(circle at top right, rgba(34, 197, 94, 0.12), transparent 26%),
            linear-gradient(135deg, rgba(15, 23, 42, 0.98), rgba(30, 41, 59, 0.98));
    }
    .dark .handover-terms-item {
        border-color: rgba(148, 163, 184, 0.16);
        background: rgba(15, 23, 42, 0.9);
    }
    .dark .handover-terms-item strong {
        color: #fbbf24;
    }
</style>
@endpush

@section('content')
@php
    $directHandoverScope = $directHandoverScope ?? 'self';
    $approvedRequest = $approvedRequest ?? null;
    $approvedRequestWalkies = $approvedRequestWalkies ?? collect();
    $isSelfIssue = $directHandoverScope === 'self';
    $currentUser = auth('wt')->user();
    $approvedName = strtoupper((string) ($approvedRequest->full_name ?? ''));
    $approvedStaffId = strtoupper((string) ($approvedRequest->staff_id ?? ''));
    $approvedPosition = strtoupper((string) ($approvedRequest->position ?? ''));
    $approvedDepartment = strtoupper((string) ($approvedRequest->department ?? ''));
    $selfName = strtoupper($currentUser->full_name ?: $currentUser->username ?: '');
    $selfStaffId = strtoupper($currentUser->staff_id ?: '');
    $selfPosition = strtoupper($currentUser->position ?: ($currentUser->role === 'admin' ? 'EXECUTIVE' : 'ICT'));
    $selfDepartment = strtoupper($currentUser->department ?: '');
@endphp
<div class="space-y-4">
    <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
        <div>
            <h3 class="page-title-standard">{{ $approvedRequest ? 'Complete Approved Request Handover' : ($isSelfIssue ? 'Self-Issue Handover WT' : 'Handover On Behalf WT') }}</h3>
            <p class="page-subtitle-standard">{{ $approvedRequest ? 'Fill in the handover details for the request that was just approved.' : ($isSelfIssue ? 'Issue an available walkie talkie directly to your ICT account.' : 'Issue an available walkie talkie directly to another executive or ICT account.') }}</p>
        </div>
        <span class="inline-flex w-fit items-center gap-2 rounded-md border border-sky-200 bg-sky-50 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-sky-700">
            <i class="fa-solid fa-user-check"></i>
            {{ $approvedRequest ? 'Approved Request #' . str_pad($approvedRequest->id, 5, '0', STR_PAD_LEFT) : ($isSelfIssue ? 'Self-Issue Handover WT' : 'On Behalf Handover WT') }}
        </span>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-[10px] font-bold text-emerald-700">
            <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-[10px] font-bold text-red-700">
            <i class="fa-solid fa-circle-exclamation mr-2"></i>{{ $errors->first() }}
        </div>
    @endif

    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <form action="{{ route('wt.admin.handover.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="handover_mode" value="ict_direct">
            <input type="hidden" name="direct_handover_scope" value="{{ $directHandoverScope }}">
            @if($approvedRequest)
                <input type="hidden" name="access_request_id" value="{{ $approvedRequest->id }}">
                <input type="hidden" name="user_id" value="{{ $approvedRequest->user_id ?: $currentUser->user_id }}">
            @endif
            @if($isSelfIssue)
                <input type="hidden" name="user_id" value="{{ $currentUser->user_id }}">
            @endif

            <div>
                <h4 class="mb-2 text-[10px] font-black uppercase tracking-widest text-slate-700 dark:text-slate-200">{{ $approvedRequest ? '1. Approved Request Details' : ($isSelfIssue ? '1. Self-Issue Details' : '1. Executive Information') }}</h4>
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    @if($approvedRequest)
                        <div class="md:col-span-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[10px] font-black uppercase tracking-widest text-emerald-800 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-200">
                            <i class="fa-solid fa-circle-check mr-2"></i>Request #{{ str_pad($approvedRequest->id, 5, '0', STR_PAD_LEFT) }} approved for {{ $approvedName ?: '-' }}
                        </div>
                    @elseif($isSelfIssue)
                        <div class="md:col-span-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-[10px] font-black uppercase tracking-widest text-sky-800 dark:border-sky-900/60 dark:bg-sky-950/40 dark:text-sky-200">
                            <i class="fa-solid fa-user-shield mr-2"></i>Self-issue to {{ $selfName ?: $currentUser->username }}
                        </div>
                    @else
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500">Executive Account</label>
                            <select id="handover_user_select" name="user_id" class="handover-search-select w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-[10px] font-bold text-slate-700 outline-none focus:border-sky-400 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" required>
                                <option value="">Select executive account</option>
                                @foreach($users as $user)
                                    @php
                                        $displayName = strtoupper($user->full_name ?: $user->username ?: '-');
                                        $displayDepartment = strtoupper($user->department ?: 'NO DEPARTMENT');
                                    @endphp
                                    <option value="{{ $user->user_id }}"
                                        data-name="{{ $displayName }}"
                                        data-staff-id="{{ strtoupper($user->staff_id ?: '-') }}"
                                        data-position="{{ strtoupper($user->position ?: ($user->role === 'admin' ? 'EXECUTIVE' : 'ICT')) }}"
                                        data-department="{{ $displayDepartment }}"
                                        @selected((string) old('user_id') === (string) $user->user_id)>
                                        {{ $displayName }} - {{ $displayDepartment }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500">Executive Name</label>
                            <input id="executive_name" type="text" class="w-full rounded-md border border-slate-200 bg-slate-100 px-3 py-2 text-[10px] font-bold text-slate-600 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300" readonly>
                        </div>
                        <div>
                            <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500">Executive ID</label>
                            <input id="executive_staff_no" type="text" class="w-full rounded-md border border-slate-200 bg-slate-100 px-3 py-2 text-[10px] font-bold text-slate-600 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300" readonly>
                        </div>
                        <div>
                            <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500">Executive Position</label>
                            <input id="executive_position" type="text" class="w-full rounded-md border border-slate-200 bg-slate-100 px-3 py-2 text-[10px] font-bold text-slate-600 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300" readonly>
                        </div>
                        <div>
                            <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500">Executive Department</label>
                            <input id="executive_department" type="text" class="w-full rounded-md border border-slate-200 bg-slate-100 px-3 py-2 text-[10px] font-bold text-slate-600 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300" readonly>
                        </div>
                        <div class="md:col-span-2 pt-2">
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-700 dark:text-slate-200">2. Owner Being Requested For</h4>
                        </div>
                    @endif

                    <div>
                        <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500">{{ $isSelfIssue ? 'Full Name' : 'Owner Full Name' }}</label>
                        <input id="owner_staff_name" name="staff_name" type="text" value="{{ old('staff_name', $approvedRequest ? $approvedName : ($isSelfIssue ? $selfName : '')) }}" list="name-options" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-[10px] font-bold text-slate-700 outline-none focus:border-sky-400 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" required>
                    </div>

                    <div>
                        <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500">Ownership Type</label>
                        <select name="ownership_type" id="ownership_type" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-[10px] font-bold text-slate-700 outline-none focus:border-sky-400 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" required>
                            <option value="INDIVIDUAL" @selected(old('ownership_type', 'INDIVIDUAL') === 'INDIVIDUAL')>Individual</option>
                            <option value="SHARED" @selected(old('ownership_type') === 'SHARED')>Shared</option>
                            <option value="SPARE" @selected(old('ownership_type') === 'SPARE')>Spare</option>
                        </select>
                    </div>

                    <div id="shared_with_section" class="{{ old('ownership_type') === 'SHARED' ? '' : 'hidden' }}">
                        <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500">Shared With <span class="text-red-500">*</span></label>
                        <input id="shared_with" name="shared_with" type="text" value="{{ strtoupper(old('shared_with', '')) }}" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-[10px] font-bold uppercase text-slate-700 outline-none focus:border-sky-400 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" placeholder="E.G. USER / TEAM / DEPARTMENT">
                    </div>

                    <div>
                        <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500">Position</label>
                        <input id="owner_position" name="position" type="text" value="{{ old('position', $approvedRequest ? $approvedPosition : ($isSelfIssue ? $selfPosition : '')) }}" list="position-options" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-[10px] font-bold text-slate-700 outline-none focus:border-sky-400 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" required>
                    </div>

                    <div>
                        <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500">Department</label>
                        <input id="owner_department" name="department" type="text" value="{{ old('department', $approvedRequest ? $approvedDepartment : ($isSelfIssue ? $selfDepartment : '')) }}" list="department-options" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-[10px] font-bold text-slate-700 outline-none focus:border-sky-400 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                    </div>
                </div>
            </div>

            <div>
                <h4 class="mb-2 text-[10px] font-black uppercase tracking-widest text-slate-700 dark:text-slate-200">{{ $isSelfIssue ? '2. Walkie Talkie Unit' : '3. Walkie Talkie Unit' }}</h4>
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500">Available Unit</label>
                        @if($approvedRequest)
                            <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-[10px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                                @forelse($approvedRequestWalkies as $radio)
                                    <div>RADIO ID: {{ $radio->radio_id }} | SERIAL: {{ $radio->serial_number }} | MODEL: {{ $radio->model }}</div>
                                @empty
                                    <div>No assigned unit found for this approved request.</div>
                                @endforelse
                            </div>
                        @else
                            <select name="walkie_inventory_id" class="w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-[10px] font-bold text-slate-700 outline-none focus:border-sky-400 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" required>
                                <option value="">Select unused walkie talkie</option>
                                @foreach($availableRadios as $radio)
                                    <option value="{{ $radio->walkie_id }}" @selected((string) old('walkie_inventory_id') === (string) $radio->walkie_id)>
                                        RADIO ID: {{ $radio->radio_id }} | SERIAL: {{ $radio->serial_number }} | MODEL: {{ $radio->model }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @if(!$approvedRequest && $availableRadios->isEmpty())
                            <p class="mt-2 text-[10px] font-bold text-red-600">No UNUSED walkie talkies are available for handover.</p>
                        @endif
                    </div>

                    <div>
                        <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500">Issued Date</label>
                        <input name="issued_at" type="date" value="{{ old('issued_at', date('Y-m-d')) }}" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-[10px] font-bold text-slate-700 outline-none focus:border-sky-400 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500">Handover Notes</label>
                        <textarea name="notes" rows="2" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-[10px] font-bold text-slate-700 outline-none focus:border-sky-400 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" placeholder="{{ $isSelfIssue ? 'Example: Self-issue by ICT for duty use.' : 'Example: Direct handover by ICT on behalf of recipient.' }}">{{ old('notes', $approvedRequest ? 'ICT handover for approved request #' . str_pad($approvedRequest->id, 5, '0', STR_PAD_LEFT) . '.' : '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end border-t border-slate-100 pt-3 dark:border-slate-700">
                <button type="submit" class="wt-btn" {{ (!$approvedRequest && $availableRadios->isEmpty()) || ($approvedRequest && $approvedRequestWalkies->isEmpty()) ? 'disabled' : '' }}>
                    Submit Handover
                </button>
            </div>
        </form>
    </div>

    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <div class="mb-3 flex items-center justify-between gap-3">
            <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-700 dark:text-slate-200">{{ $isSelfIssue ? 'Recent Self-Issue Handovers' : 'Recent On Behalf Handovers' }}</h4>
            <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400">{{ $recentHandovers->count() }} records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[8px] font-black uppercase tracking-widest text-slate-400 dark:bg-slate-950">
                    <tr>
                        <th class="px-3 py-2">Date</th>
                        <th class="px-3 py-2">Radio ID</th>
                        <th class="px-3 py-2">Recipient</th>
                        <th class="px-3 py-2">Ownership Type</th>
                        <th class="px-3 py-2">Department</th>
                        <th class="px-3 py-2">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[10px] dark:divide-slate-700">
                    @forelse($recentHandovers as $handover)
                        <tr>
                            <td class="px-3 py-2 font-bold text-slate-600 dark:text-slate-300">{{ $handover->issued_at ? \Carbon\Carbon::parse($handover->issued_at)->format('d M Y') : '-' }}</td>
                            <td class="px-3 py-2 font-black text-sky-700 dark:text-sky-300">{{ $handover->radio_id ?: $handover->walkie_talkie_id ?: '-' }}</td>
                            <td class="px-3 py-2 font-bold text-slate-700 dark:text-slate-100">{{ $handover->staff_name }}</td>
                            <td class="px-3 py-2 text-slate-500 dark:text-slate-300">{{ strtoupper($handover->walkieTalkie->ownership_type ?? '-') }}</td>
                            <td class="px-3 py-2 text-slate-500 dark:text-slate-300">{{ $handover->department ?: '-' }}</td>
                            <td class="px-3 py-2 text-slate-500 dark:text-slate-300">{{ $handover->notes ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-[10px] font-bold uppercase tracking-widest text-slate-400">No handover records yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const userSelect = document.getElementById('handover_user_select');
        const ownerInputs = [
            document.getElementById('owner_staff_name'),
            document.getElementById('owner_position'),
            document.getElementById('owner_department')
        ];
        const executiveNameInput = document.getElementById('executive_name');
        const executiveStaffNoInput = document.getElementById('executive_staff_no');
        const executivePositionInput = document.getElementById('executive_position');
        const executiveDepartmentInput = document.getElementById('executive_department');
        const ownershipTypeInput = document.getElementById('ownership_type');
        const sharedWithSection = document.getElementById('shared_with_section');
        const sharedWithInput = document.getElementById('shared_with');

        function syncSharedWithVisibility() {
            const shouldShowSharedWith = (ownershipTypeInput?.value || '').toUpperCase() === 'SHARED';
            sharedWithSection?.classList.toggle('hidden', !shouldShowSharedWith);
            if (sharedWithInput) {
                sharedWithInput.required = shouldShowSharedWith;
                if (!shouldShowSharedWith) sharedWithInput.value = '';
            }
        }

        function syncSelectedUser() {
            if (!userSelect) return;
            const option = userSelect.options[userSelect.selectedIndex];
            if (!option || !option.value) return;

            if (executiveNameInput) executiveNameInput.value = option.dataset.name || '';
            if (executiveStaffNoInput) executiveStaffNoInput.value = option.dataset.staffId || '';
            if (executivePositionInput) executivePositionInput.value = option.dataset.position || '';
            if (executiveDepartmentInput) executiveDepartmentInput.value = option.dataset.department || '';
        }

        if (userSelect) {
            const handleUserChange = function () {
                if (executiveNameInput) executiveNameInput.value = '';
                if (executiveStaffNoInput) executiveStaffNoInput.value = '';
                if (executivePositionInput) executivePositionInput.value = '';
                if (executiveDepartmentInput) executiveDepartmentInput.value = '';
                ownerInputs.forEach((input) => {
                    if (input) input.value = '';
                });
                syncSelectedUser();
            };

            if (window.jQuery && $(userSelect).select2) {
                $(userSelect).select2({
                    width: '100%',
                    placeholder: 'Select executive account',
                    allowClear: true
                }).on('change select2:select', handleUserChange);
            } else {
                userSelect.addEventListener('change', handleUserChange);
            }
            syncSelectedUser();
            window.setTimeout(syncSelectedUser, 0);
        }

        ownershipTypeInput?.addEventListener('change', syncSharedWithVisibility);
        syncSharedWithVisibility();
    });
</script>
@endpush


