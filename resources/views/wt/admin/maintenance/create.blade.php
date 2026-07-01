@extends('wt.layouts.admin')

@section('title', isset($editRecord) ? 'Edit Repair Record' : 'Add Repair Record')

@section('content')
<div class="page-header-block flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div>
        <h1 class="page-title-standard text-slate-100">{{ isset($editRecord) ? 'Edit Repair Record' : 'Add Repair Record' }}</h1>
        <p class="page-subtitle-standard text-slate-400">{{ isset($editRecord) ? 'Update this maintenance record in a full page form.' : 'Register a maintenance or breakdown entry in a full page form.' }}</p>
    </div>
    <a href="{{ route('wt.admin.maintenance.index') }}" class="wt-btn wt-btn-soft">
        <i class="fas fa-arrow-left text-[13px]"></i>
        Back
    </a>
</div>

@if($errors instanceof \Illuminate\Support\ViewErrorBag && $errors->any())
<div class="alert-error mb-6">
    <ul class="list-disc pl-5 mt-1">
        @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="repair-create-shell">
    <form action="{{ isset($editRecord) ? route('wt.admin.maintenance.update', $editRecord) : route('wt.admin.maintenance.store') }}" method="POST">
        @csrf
        @if(isset($editRecord))
        @method('PATCH')
        @endif
        <input type="hidden" name="return_route" value="admin.maintenance.index">
        <div class="repair-form-header">
            <div>
                <h2 class="repair-form-title">{{ isset($editRecord) ? 'Update Repair Record' : 'New Repair Record' }}</h2>
                <p class="repair-form-subtitle">{{ isset($editRecord) ? 'Review the current repair details and save your changes below.' : 'Choose the unit and complete the repair details below.' }}</p>
            </div>
        </div>

        <div class="repair-form-body">
            @if(isset($editRecord))
            <div class="mb-5 rounded-2xl border border-slate-700 bg-slate-900/80 px-4 py-3">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Editing Record</p>
                <p class="mt-2 text-sm font-black text-slate-100">Unit {{ $editRecord->radio_id ?: '-' }} / {{ $editRecord->serial_number ?: '-' }}</p>
                <p class="mt-1 text-[10px] font-bold uppercase tracking-widest text-slate-500">Maintenance ID #{{ str_pad($editRecord->maintenance_id, 4, '0', STR_PAD_LEFT) }}</p>
            </div>
            @endif
            <div class="repair-form-grid">
                @if(!isset($editRecord))
                <div class="repair-form-group repair-form-group-full">
                    <label class="repair-form-label">Unit To Repair (Radio ID) <span class="required">*</span></label>
                    <select name="walkie_id" class="repair-form-input repair-smart-select" data-placeholder="Select unit..." required>
                        <option value="" disabled {{ old('walkie_id') ? '' : 'selected' }}>Select unit...</option>
                        @foreach($walkies as $w)
                        <option value="{{ $w->walkie_id }}" {{ (string) old('walkie_id') === (string) $w->walkie_id ? 'selected' : '' }}>
                            {{ $w->radio_id }} / {{ $w->serial_number }} ({{ $w->department }})
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="repair-form-section repair-form-group-full">Repair Timeline</div>

                <div class="repair-form-group">
                    <label class="repair-form-label">Received Date <span class="required">*</span></label>
                    <input type="date" name="received_date" class="repair-form-input" required value="{{ old('received_date', $editRecord->received_date ?? date('Y-m-d')) }}">
                </div>

                <div class="repair-form-group">
                    <label class="repair-form-label">Repair Date</label>
                    <input type="date" name="repair_date" class="repair-form-input" value="{{ old('repair_date', $editRecord->repair_date ?? '') }}">
                </div>

                <div class="repair-form-group">
                    <label class="repair-form-label">Status <span class="required">*</span></label>
                    <select name="status" class="repair-form-input repair-smart-select" required>
                        @foreach(['UNDER REPAIR', 'FAULTY', 'B.E.R', 'READY TO COLLECT', 'ALREADY FIXED', 'DONE'] as $status)
                        <option value="{{ $status }}" {{ old('status', $editRecord->status ?? 'UNDER REPAIR') === $status ? 'selected' : '' }}>
                            {{ $status === 'DONE' ? 'DONE / RESOLVED' : $status }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="repair-form-group">
                    <label class="repair-form-label">Done?</label>
                    <select name="done" class="repair-form-input repair-smart-select">
                        <option value="0" {{ (string) old('done', isset($editRecord) ? ((int) $editRecord->done) : '0') === '0' ? 'selected' : '' }}>NO (Pending)</option>
                        <option value="1" {{ (string) old('done', isset($editRecord) ? ((int) $editRecord->done) : '0') === '1' ? 'selected' : '' }}>YES (Finished)</option>
                    </select>
                </div>

                @if(isset($editRecord))
                <div class="repair-form-group">
                    <label class="repair-form-label">Finish Date</label>
                    <input type="date" name="finish_date" class="repair-form-input" value="{{ old('finish_date', $editRecord->finish_date ?? '') }}">
                </div>
                @endif

                <div class="repair-form-section repair-form-group-full">Repair Notes</div>

                <div class="repair-form-group repair-form-note-group">
                    <label class="repair-form-label">Issue Description <span class="required">*</span></label>
                    <textarea name="issue" class="repair-form-input repair-textarea" placeholder="e.g. Broken PTT" required>{{ old('issue', $editRecord->issue ?? $editRecord->issue_description ?? '') }}</textarea>
                </div>

                <div class="repair-form-group repair-form-note-group">
                    <label class="repair-form-label">Remarks</label>
                    <textarea name="remarks" class="repair-form-input repair-textarea" placeholder="Notes...">{{ old('remarks', $editRecord->remarks ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="repair-form-footer">
            <a href="{{ route('wt.admin.maintenance.index') }}" class="btn-cancel">Back</a>
            <button type="submit" class="btn-submit">{{ isset($editRecord) ? 'Save Changes' : 'Submit Record' }}</button>
        </div>
    </form>
</div>

<style>
    .repair-create-shell {
        background: #111827;
        border: 1px solid #334155;
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 20px 44px rgba(0, 0, 0, 0.42);
    }

    .repair-form-header {
        padding: 22px 30px 18px;
        border-bottom: 1px solid #243041;
        background: linear-gradient(180deg, #172033 0%, #111827 100%);
    }

    .repair-form-title {
        margin: 0;
        font-size: 16px;
        line-height: 1.2;
        font-weight: 900;
        letter-spacing: -0.02em;
        color: #f8fafc;
        text-transform: uppercase;
    }

    .repair-form-subtitle {
        margin-top: 6px;
        font-size: 11px;
        line-height: 1.5;
        color: #cbd5e1;
        font-weight: 600;
    }

    .repair-form-body {
        padding: 22px 30px;
        background: #111827;
    }

    .repair-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        align-items: flex-start;
        gap: 14px 18px;
    }

    .repair-form-group {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .repair-form-group-full {
        grid-column: 1 / -1;
    }

    .repair-form-section {
        margin-top: 0;
        padding: 7px 10px;
        border-left: 4px solid #38bdf8;
        border-radius: 11px;
        background: rgba(14, 165, 233, 0.12);
        color: #bae6fd;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .repair-form-note-group {
        align-self: stretch;
    }

    .repair-form-label {
        margin: 0;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #dbe4f0;
    }

    .required {
        color: #dc2626;
    }

    .repair-form-input {
        width: 100%;
        height: 46px;
        border-radius: 16px;
        border: 1px solid #334155;
        background: #0f172a;
        padding: 12px 14px;
        font-size: 12px;
        font-weight: 700;
        color: #f8fafc;
        outline: none;
        transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
    }

    .repair-form-input:focus {
        border-color: #60a5fa;
        background: #111827;
        box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.16);
    }

    .repair-textarea {
        height: 96px;
        min-height: 96px;
        resize: vertical;
        line-height: 1.45;
    }

    .repair-form-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 30px 22px;
        border-top: 1px solid #243041;
        background: #0f172a;
    }

    .alert-error {
        display: flex;
        align-items: flex-start;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        background: #3b0d0d;
        color: #fecaca;
        border: 1px solid #7f1d1d;
    }

    .dark body .content-surface .page-header-block {
        background: #111827 !important;
        border-color: #263244 !important;
    }

    .dark body .content-surface .page-header-block .page-title-standard {
        color: #f8fafc !important;
    }

    .dark body .content-surface .page-header-block .page-subtitle-standard {
        color: #aab5c7 !important;
    }

    .dark body .content-surface .repair-form-title {
        color: #f8fafc !important;
    }

    html:not(.dark) body .content-surface .page-header-block {
        border: 1px solid #d8e1ed !important;
        border-radius: 14px !important;
        background: #f5f8fc !important;
        box-shadow: none !important;
    }

    html:not(.dark) body .content-surface .page-header-block .page-title-standard {
        color: #172033 !important;
    }

    html:not(.dark) body .content-surface .page-header-block .page-subtitle-standard {
        color: #64748b !important;
    }

    html:not(.dark) body .content-surface .repair-create-shell {
        background: #ffffff !important;
        border-color: #d8e1ed !important;
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.08) !important;
    }

    html:not(.dark) body .content-surface .repair-form-header {
        background: #f8fafc !important;
        border-bottom-color: #d8e1ed !important;
    }

    html:not(.dark) body .content-surface .repair-form-title {
        color: #172033 !important;
    }

    html:not(.dark) body .content-surface .repair-form-subtitle {
        color: #475569 !important;
    }

    html:not(.dark) body .content-surface .repair-form-body {
        background: #ffffff !important;
    }

    html:not(.dark) body .content-surface .repair-form-label {
        color: #334155 !important;
    }

    html:not(.dark) body .content-surface .repair-form-section {
        background: #eaf6fc !important;
        color: #0369a1 !important;
    }

    html:not(.dark) body .content-surface .repair-form-input {
        background: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #172033 !important;
    }

    html:not(.dark) body .content-surface .repair-form-input::placeholder {
        color: #94a3b8 !important;
    }

    html:not(.dark) body .content-surface .repair-form-input:focus {
        background: #ffffff !important;
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.16) !important;
    }

    html:not(.dark) body .content-surface .repair-form-footer {
        background: #f8fafc !important;
        border-top-color: #d8e1ed !important;
    }

    html:not(.dark) body .content-surface .repair-create-shell .select2-container--default .select2-selection--single {
        background: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #172033 !important;
    }

    html:not(.dark) body .content-surface .repair-create-shell .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #172033 !important;
    }

    html:not(.dark) body .content-surface .repair-create-shell .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #64748b !important;
    }

    @media (max-width: 768px) {
        .repair-form-grid {
            grid-template-columns: 1fr;
        }

        .repair-form-header,
        .repair-form-body,
        .repair-form-footer {
            padding-left: 18px;
            padding-right: 18px;
        }

        .repair-form-footer {
            flex-direction: column-reverse;
        }

        .repair-form-footer > * {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection
