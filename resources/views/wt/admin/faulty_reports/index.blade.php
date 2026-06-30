@extends('wt.layouts.admin')

@section('title', 'User Faulty Reports')

@push('styles')
<style>
    .faulty-report-card {
        border-radius: 18px;
        border: 1px solid rgba(148, 163, 184, 0.2);
        background: #ffffff;
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.08);
    }
    .dark .faulty-report-card {
        background: #1e293b;
        border-color: #334155;
    }
    .faulty-report-count-pill {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        border-radius: 999px;
        border: 1px solid #bbf7d0;
        background: #dcfce7;
        color: #166534;
        padding: 6px 12px;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .faulty-report-search-panel {
        border: 1px solid #dbe3ef;
        border-radius: 8px;
        background: #ffffff;
        padding: 12px;
    }
    .faulty-report-search {
        width: 100%;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        padding: 10px 12px;
        color: #1f2937;
        font-size: 12px;
        font-weight: 700;
        outline: none;
    }
    .faulty-report-search:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.16);
    }
    .dark .faulty-report-search-panel {
        border-color: #263244;
        background: #111827;
    }
    .dark .faulty-report-search {
        border-color: #334155;
        background: #0f172a;
        color: #e2e8f0;
    }
    .faulty-report-field {
        border: 1px solid rgba(203, 213, 225, 0.9);
        border-radius: 10px;
        background: #f8fafc;
        color: #1f2937;
        font-size: 11px;
        font-weight: 800;
        padding: 8px 10px;
        width: 100%;
        outline: none;
    }
    .faulty-report-field:focus {
        border-color: #0284c7;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(139, 94, 60, 0.12);
    }
    .dark .faulty-report-field {
        background: #0f172a;
        border-color: #334155;
        color: #e2e8f0;
    }
    .faulty-report-label {
        display: block;
        margin-bottom: 5px;
        color: #64748b;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .dark .faulty-report-label {
        color: #94a3b8;
    }
    .faulty-report-readable {
        color: #334155;
        font-size: 12px;
        font-weight: 650;
        line-height: 1.75;
        overflow-wrap: anywhere;
        white-space: pre-line;
    }
    .dark .faulty-report-readable {
        color: #cbd5e1;
    }
    .faulty-report-note-box {
        border-radius: 10px;
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        padding: 12px 14px;
    }
    .dark .faulty-report-note-box {
        border-color: rgba(59, 130, 246, 0.45);
        background: rgba(30, 64, 175, 0.2);
    }
    .faulty-report-note-title {
        color: #0369a1;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .dark .faulty-report-note-title {
        color: #7dd3fc;
    }
    .faulty-report-field:is(textarea) {
        min-height: 86px;
        font-size: 12px;
        font-weight: 650;
        line-height: 1.6;
        resize: vertical;
    }
    .faulty-report-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border-top: 1px solid #263244;
        padding: 14px 16px;
        background: #111827;
    }
    .faulty-report-page-info {
        color: #7f9cc6;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .faulty-report-page-controls {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .faulty-report-page-btn,
    .faulty-report-page-number {
        min-width: 34px;
        height: 30px;
        border: 1px solid #2f4d74;
        border-radius: 6px;
        background: #111827;
        color: #93b7e8;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        transition: border-color 0.15s ease, background 0.15s ease, color 0.15s ease, opacity 0.15s ease;
    }
    .faulty-report-page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 58px;
        padding: 0 11px;
        color: #c7d7ee;
        font-size: 9px;
    }
    .faulty-report-page-btn:hover:not(:disabled),
    .faulty-report-page-number:hover {
        border-color: #3b82f6;
        background: #172033;
        color: #ffffff;
    }
    .faulty-report-page-btn:disabled {
        cursor: not-allowed;
        border-color: #223047;
        color: #536477;
        opacity: 0.55;
    }
    .faulty-report-page-number.is-active {
        border-color: #3b82f6;
        background: #172b49;
        color: #ffffff;
    }
    @media (max-width: 640px) {
        .faulty-report-pagination {
            align-items: stretch;
            flex-direction: column;
        }
        .faulty-report-page-controls {
            justify-content: space-between;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header-block flex flex-col md:flex-row md:items-start md:justify-between gap-4">
    <div>
        <h3 class="page-title-standard">User Faulty Reports</h3>
        <p class="page-subtitle-standard">ICT review page for faulty reports submitted by users and executives.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <span class="faulty-report-count-pill">{{ $summary['total'] }} Reports</span>
        <a href="{{ route('wt.admin.maintenance.index') }}" class="wt-btn wt-btn-soft">
            <i class="fa-solid fa-screwdriver-wrench text-[13px]"></i>
            Repair Records
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[11px] font-black text-emerald-700">
    <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-[11px] font-black text-red-700">
    <i class="fa-solid fa-circle-exclamation mr-2"></i>{{ $errors->first() }}
</div>
@endif

<div class="faulty-report-search-panel mb-5">
    <input id="faultyReportSearchInput" type="search" class="faulty-report-search" placeholder="Search any row or column value...">
</div>

<div class="faulty-report-card overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between gap-3">
        <h4 class="text-[11px] font-black uppercase tracking-widest text-slate-800 dark:text-slate-100">
            Reports From Users
        </h4>
        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Update here to notify user</span>
    </div>

    @if($records->isEmpty())
        <div class="px-5 py-12 text-center text-[11px] font-bold text-slate-400">
            No user faulty reports found.
        </div>
    @else
        <div class="divide-y divide-slate-100 dark:divide-slate-700">
            @foreach($records as $record)
            @php
                $status = strtoupper((string) $record->status);
                $isDone = (bool) $record->done || $status === 'DONE';
                $canMarkReceived = ! $isDone && ! $record->ict_received_at && in_array($status, ['UNDER REPAIR', 'FAULTY', 'B.E.R'], true);
                $canReturnOriginal = ! $isDone && $record->ict_received_at;
                $canMarkAlreadyFixed = ! $isDone && ! in_array($status, ['ALREADY FIXED', 'READY TO COLLECT'], true);
                $canMarkReadyToCollect = ! $isDone && $status === 'ALREADY FIXED';
                $statusClass = match($status) {
                    'DONE' => 'bg-emerald-100 text-emerald-700',
                    'ALREADY FIXED' => 'bg-teal-100 text-teal-700',
                    'READY TO COLLECT' => 'bg-lime-100 text-lime-700',
                    'UNDER REPAIR' => 'bg-sky-100 text-sky-700',
                    'B.E.R' => 'bg-red-100 text-red-700',
                    'FAULTY' => 'bg-orange-100 text-orange-700',
                    default => 'bg-amber-100 text-amber-700',
                };
                $evidencePaths = is_array($record->evidence_paths) ? $record->evidence_paths : [];
            @endphp
            <div class="faulty-report-row p-5 grid grid-cols-1 xl:grid-cols-[1.15fr_0.85fr] gap-5">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-[11px] font-black text-[#0284c7]">#{{ str_pad($record->maintenance_id, 4, '0', STR_PAD_LEFT) }}</span>
                        <span class="px-2 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $statusClass }}">
                            {{ $record->status ?: 'PENDING' }}
                        </span>
                        @if($isDone)
                        <span class="px-2 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-600 text-white">User Notified</span>
                        @endif
                        @if($record->ict_received_at)
                        <span class="px-2 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-sky-600 text-white">
                            WT Received {{ \Carbon\Carbon::parse($record->ict_received_at)->format('d M Y') }}
                        </span>
                        @endif
                        @if($record->temporary_spare_requested && ! $record->temporary_spare_assigned_at)
                        <span class="px-2 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-sky-100 text-sky-700">
                            Temporary WT Requested
                        </span>
                        @endif
                    </div>

                    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Reporter</p>
                            <p class="mt-1 text-[12px] font-black text-slate-800 dark:text-slate-100 uppercase">{{ $record->reporter_name ?: '-' }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $record->reporter_staff_id ?: '-' }} | {{ $record->department_name ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Unit</p>
                            <p class="mt-1 text-[12px] font-black text-slate-800 dark:text-slate-100 uppercase">{{ $record->radio_id ?: 'NO RADIO ID' }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $record->model ?: 'NO MODEL' }} | {{ $record->serial_number ?: 'NO SERIAL' }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Problem Reported</p>
                        <p class="faulty-report-readable mt-1">
                            {!! nl2br(e($record->problem_possible ?: ($record->issue ?: $record->issue_description ?: '-'))) !!}
                        </p>
                    </div>

                    @if($record->remarks)
                    <div class="faulty-report-note-box mt-4">
                        <p class="faulty-report-note-title">Latest Note Visible To User</p>
                        <p class="faulty-report-readable mt-1">{!! nl2br(e($record->remarks)) !!}</p>
                    </div>
                    @endif

                    @if($record->temporary_spare_requested && ! $record->temporarySpareWalkie)
                    <div class="faulty-report-note-box mt-4">
                        <p class="text-[9px] font-black uppercase tracking-widest text-sky-700 dark:text-sky-300">User Requested Temporary WT</p>
                        <p class="faulty-report-readable mt-1">
                            Please assign a SPARE / NEW WT below if stock is available.
                            @if($record->temporary_spare_request_note)
                                <br>Note: {!! nl2br(e($record->temporary_spare_request_note)) !!}
                            @endif
                        </p>
                    </div>
                    @elseif(! is_null($record->temporary_spare_requested) && ! $record->temporary_spare_requested)
                    <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-900/50">
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-500">Temporary WT Response</p>
                        <p class="mt-1 text-[11px] font-bold leading-5 text-slate-600 dark:text-slate-300">User does not need a temporary WT.</p>
                    </div>
                    @endif

                    <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 dark:border-emerald-900/60 dark:bg-emerald-950/30">
                        <p class="text-[9px] font-black uppercase tracking-widest text-emerald-700 dark:text-emerald-300">Pickup & Handover</p>
                        <p class="mt-1 text-[11px] font-black leading-5 text-emerald-900 dark:text-emerald-100">
                            Handover: {{ strtoupper($record->handover_person ?: '-') }}
                            <span class="font-bold text-emerald-700 dark:text-emerald-300">| {{ $record->handover_at ? \Carbon\Carbon::parse($record->handover_at)->format('d M Y, h:i A') : '-' }}</span>
                        </p>
                        <p class="mt-1 text-[11px] font-black leading-5 text-emerald-900 dark:text-emerald-100">
                            Pickup: {{ strtoupper($record->pickup_person ?: '-') }}
                            <span class="font-bold text-emerald-700 dark:text-emerald-300">| {{ $record->pickup_at ? \Carbon\Carbon::parse($record->pickup_at)->format('d M Y, h:i A') : '-' }}</span>
                        </p>
                        <p class="mt-1 text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-300">
                            Location: ICT Department Sejurumus after ICT approval
                        </p>
                    </div>

                    @if($record->temporarySpareWalkie)
                    <div class="mt-4 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 dark:border-sky-900/60 dark:bg-sky-950/30">
                        <p class="text-[9px] font-black uppercase tracking-widest text-sky-700 dark:text-sky-300">Temporary Spare / New WT In Use</p>
                        <p class="mt-1 text-[11px] font-black leading-5 text-sky-900 dark:text-sky-100">
                            Radio {{ $record->temporarySpareWalkie->radio_id ?: '-' }}
                            <span class="font-bold text-sky-600 dark:text-sky-300">| {{ $record->temporarySpareWalkie->model ?: 'NO MODEL' }} | {{ $record->temporarySpareWalkie->serial_number ?: 'NO SERIAL' }}</span>
                        </p>
                        <p class="mt-1 text-[10px] font-bold uppercase tracking-wider text-sky-600 dark:text-sky-300">
                            Assigned: {{ $record->temporary_spare_assigned_at ? \Carbon\Carbon::parse($record->temporary_spare_assigned_at)->format('d M Y') : '-' }}
                        </p>
                    </div>
                    @endif

                    @if(!empty($evidencePaths))
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach($evidencePaths as $path)
                        <a href="{{ asset('storage/' . $path) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50">
                            <i class="fa-solid fa-paperclip"></i> Evidence
                        </a>
                        @endforeach
                    </div>
                    @endif

                    <p class="mt-4 text-[10px] font-bold text-slate-400">
                        Submitted: {{ $record->received_date ? \Carbon\Carbon::parse($record->received_date)->format('d M Y') : '-' }}
                        @if($record->ict_received_at)
                            | ICT Received: {{ \Carbon\Carbon::parse($record->ict_received_at)->format('d M Y') }}
                        @endif
                        @if($record->finish_date)
                            | Finished: {{ \Carbon\Carbon::parse($record->finish_date)->format('d M Y') }}
                        @endif
                    </p>
                </div>

                <form method="POST" action="{{ route('wt.admin.faultyReports.update', $record->maintenance_id) }}" class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 p-4">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="faulty-report-label">Status</label>
                            <select name="status" class="faulty-report-field" required>
                                @foreach(array_merge(['PENDING ADMIN IT', 'UNDER REPAIR', 'FAULTY', 'B.E.R', 'ALREADY FIXED'], in_array($status, ['ALREADY FIXED', 'READY TO COLLECT'], true) ? ['READY TO COLLECT'] : [], ['DONE']) as $option)
                                <option value="{{ $option }}" @selected($status === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="faulty-report-label">Repair Date</label>
                            <input type="date" name="repair_date" value="{{ $record->repair_date }}" class="faulty-report-field">
                        </div>
                        <div>
                            <label class="faulty-report-label">Finish Date</label>
                            <input type="date" name="finish_date" value="{{ $record->finish_date }}" class="faulty-report-field">
                        </div>
                        <div>
                            <label class="faulty-report-label">Visible Status</label>
                            <input type="text" value="{{ $status === 'READY TO COLLECT' ? 'READY TO COLLECT' : ($status === 'ALREADY FIXED' ? 'ALREADY FIXED' : ($isDone ? 'DONE' : ($record->ict_received_at ? 'WT RECEIVED BY ICT' : 'WAITING FOR WT HANDOVER'))) }}" class="faulty-report-field" readonly>
                        </div>
                    </div>

                    <div class="mt-3 rounded-xl border border-sky-100 bg-sky-50/70 p-3 dark:border-sky-900/50 dark:bg-sky-950/20">
                        <label class="faulty-report-label">Temporary Spare / New WT While Repairing</label>
                        <select name="temporary_spare_walkie_id" class="faulty-report-field">
                            <option value="">No temporary spare / new WT</option>
                            @if($record->temporarySpareWalkie)
                            <option value="{{ $record->temporarySpareWalkie->walkie_id }}" selected>
                                CURRENT: Radio {{ $record->temporarySpareWalkie->radio_id }} | {{ $record->temporarySpareWalkie->model }} | {{ $record->temporarySpareWalkie->serial_number }}
                            </option>
                            @endif
                            @foreach($availableSpareWalkies as $spare)
                                @continue($record->temporary_spare_walkie_id && (int) $record->temporary_spare_walkie_id === (int) $spare->walkie_id)
                                <option value="{{ $spare->walkie_id }}" @selected((string) old('temporary_spare_walkie_id') === (string) $spare->walkie_id)>
                                    Radio {{ $spare->radio_id ?: '-' }} | {{ $spare->model ?: 'NO MODEL' }} | {{ $spare->serial_number ?: 'NO SERIAL' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-[10px] font-bold leading-5 text-sky-700 dark:text-sky-300">
                            {{ $record->temporary_spare_requested && ! $record->temporarySpareWalkie ? 'User requested a temporary WT. ' : '' }}Use this to assign a SPARE or NEW/UNALLOCATED unit temporarily while the reported unit is being repaired. When repair is done, use the return button below to release it and hand the original unit back.
                        </p>
                    </div>

                    <div class="mt-3">
                        <label class="faulty-report-label">Issue Information</label>
                        <textarea name="issue" rows="2" class="faulty-report-field" placeholder="Update issue details if ICT needs to correct the report.">{{ $record->issue ?: $record->issue_description }}</textarea>
                    </div>

                    <div class="mt-3">
                        <label class="faulty-report-label">ICT Update Note To User</label>
                        <textarea name="remarks" rows="3" class="faulty-report-field" placeholder="Example: Unit received by ICT. Checking battery and PTT button.">{{ $record->remarks }}</textarea>
                    </div>

                    <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:justify-end">
                        @if($canMarkReceived)
                        <button type="submit" form="receiveWtForm-{{ $record->maintenance_id }}" class="inline-flex w-full items-center justify-center gap-2 px-4 py-2 rounded-xl bg-sky-700 text-white text-[10px] font-black uppercase tracking-widest hover:bg-sky-800 sm:w-auto">
                            WT Received
                        </button>
                        @endif
                        @if($canMarkAlreadyFixed)
                        <button type="submit" form="alreadyFixedForm-{{ $record->maintenance_id }}" class="inline-flex w-full items-center justify-center gap-2 px-4 py-2 rounded-xl bg-teal-700 text-white text-[10px] font-black uppercase tracking-widest hover:bg-teal-800 sm:w-auto">
                            Already Fixed
                        </button>
                        @endif
                        @if($canMarkReadyToCollect)
                        <button type="submit" form="readyToCollectForm-{{ $record->maintenance_id }}" class="inline-flex w-full items-center justify-center gap-2 px-4 py-2 rounded-xl bg-lime-700 text-white text-[10px] font-black uppercase tracking-widest hover:bg-lime-800 sm:w-auto">
                            Ready To Collect
                        </button>
                        @endif
                        @if($canReturnOriginal)
                        <button type="submit" form="returnOriginalForm-{{ $record->maintenance_id }}" class="inline-flex w-full items-center justify-center gap-2 px-4 py-2 rounded-xl bg-emerald-700 text-white text-[10px] font-black uppercase tracking-widest hover:bg-emerald-800 sm:w-auto">
                            Return Spare/New & Original WT
                        </button>
                        @endif
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#0284c7] text-white text-[10px] font-black uppercase tracking-widest hover:bg-[#734C2F]">
                            <i class="fa-solid fa-bell"></i>
                            Update User
                        </button>
                    </div>
                </form>
                @if($canMarkReceived)
                <form id="receiveWtForm-{{ $record->maintenance_id }}" method="POST" action="{{ route('wt.admin.faultyReports.receiveWt', $record->maintenance_id) }}" onsubmit="return confirm('Confirm ICT has received this walkie talkie?');" class="hidden">
                    @csrf
                </form>
                @endif
                @if($canMarkAlreadyFixed)
                <form id="alreadyFixedForm-{{ $record->maintenance_id }}" method="POST" action="{{ route('wt.admin.faultyReports.update', $record->maintenance_id) }}" onsubmit="return confirm('Mark this report as already fixed?');" class="hidden">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="ALREADY FIXED">
                    <input type="hidden" name="repair_date" value="{{ $record->repair_date }}">
                    <input type="hidden" name="finish_date" value="{{ $record->finish_date }}">
                    <input type="hidden" name="issue" value="{{ $record->issue ?: $record->issue_description }}">
                    <input type="hidden" name="remarks" value="{{ $record->remarks }}">
                    <input type="hidden" name="temporary_spare_walkie_id" value="{{ $record->temporary_spare_walkie_id }}">
                </form>
                @endif
                @if($canMarkReadyToCollect)
                <form id="readyToCollectForm-{{ $record->maintenance_id }}" method="POST" action="{{ route('wt.admin.faultyReports.update', $record->maintenance_id) }}" onsubmit="return confirm('Mark this report as ready to collect?');" class="hidden">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="READY TO COLLECT">
                    <input type="hidden" name="repair_date" value="{{ $record->repair_date }}">
                    <input type="hidden" name="finish_date" value="{{ $record->finish_date }}">
                    <input type="hidden" name="issue" value="{{ $record->issue ?: $record->issue_description }}">
                    <input type="hidden" name="remarks" value="{{ $record->remarks }}">
                    <input type="hidden" name="temporary_spare_walkie_id" value="{{ $record->temporary_spare_walkie_id }}">
                </form>
                @endif
                @if($canReturnOriginal)
                <form id="returnOriginalForm-{{ $record->maintenance_id }}" method="POST" action="{{ route('wt.admin.faultyReports.returnOriginal', $record->maintenance_id) }}" onsubmit="return confirm('Confirm spare/new unit has been returned and original WT has been handed back?');" class="hidden">
                    @csrf
                </form>
                @endif
            </div>
            @endforeach
        </div>
        <div id="faultyReportPagination" class="faulty-report-pagination">
            <div id="faultyReportPageInfo" class="faulty-report-page-info">Showing 0 to 0 of 0 reports</div>
            <div class="faulty-report-page-controls">
                <button type="button" id="faultyReportPrev" class="faulty-report-page-btn">
                    <i class="fa-solid fa-chevron-left text-[9px]"></i>
                    Previous
                </button>
                <div id="faultyReportPages" class="faulty-report-page-controls"></div>
                <button type="button" id="faultyReportNext" class="faulty-report-page-btn">
                    Next
                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                </button>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('faultyReportSearchInput');
        const rows = Array.from(document.querySelectorAll('.faulty-report-row'));
        const pagination = document.getElementById('faultyReportPagination');
        const pageInfo = document.getElementById('faultyReportPageInfo');
        const prevBtn = document.getElementById('faultyReportPrev');
        const nextBtn = document.getElementById('faultyReportNext');
        const pagesHost = document.getElementById('faultyReportPages');
        const perPage = 10;
        let currentPage = 1;
        let filteredRows = rows;

        if (!pagination || !pageInfo || !prevBtn || !nextBtn || !pagesHost) {
            return;
        }

        function renderPages(totalPages) {
            pagesHost.innerHTML = '';
            if (totalPages <= 1) return;

            const maxButtons = 10;
            let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);

            if (endPage - startPage + 1 < maxButtons) {
                startPage = Math.max(1, endPage - maxButtons + 1);
            }

            for (let page = startPage; page <= endPage; page += 1) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = `faulty-report-page-number${page === currentPage ? ' is-active' : ''}`;
                button.textContent = page;
                button.addEventListener('click', function () {
                    currentPage = page;
                    render();
                });
                pagesHost.appendChild(button);
            }
        }

        function render() {
            const totalItems = filteredRows.length;
            const totalPages = Math.max(1, Math.ceil(totalItems / perPage));
            currentPage = Math.min(currentPage, totalPages);
            const startIndex = totalItems === 0 ? 0 : (currentPage - 1) * perPage;
            const endIndex = Math.min(startIndex + perPage, totalItems);

            rows.forEach((row) => {
                row.classList.add('hidden');
            });

            filteredRows.slice(startIndex, endIndex).forEach((row) => {
                row.classList.remove('hidden');
            });

            pageInfo.textContent = `Showing ${totalItems === 0 ? 0 : startIndex + 1} to ${endIndex} of ${totalItems} items`;
            prevBtn.disabled = currentPage === 1 || totalItems === 0;
            nextBtn.disabled = currentPage === totalPages || totalItems === 0;
            pagination.classList.toggle('hidden', totalItems <= perPage && !searchInput?.value.trim());
            renderPages(totalPages);
        }

        prevBtn.addEventListener('click', function () {
            if (currentPage > 1) {
                currentPage -= 1;
                render();
            }
        });

        nextBtn.addEventListener('click', function () {
            const totalPages = Math.ceil(rows.length / perPage);
            if (currentPage < totalPages) {
                currentPage += 1;
                render();
            }
        });

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const needle = this.value.trim().toLowerCase();
                filteredRows = rows.filter((row) => row.textContent.toLowerCase().includes(needle));
                currentPage = 1;
                render();
            });
        }

        render();
    });
</script>
@endpush
