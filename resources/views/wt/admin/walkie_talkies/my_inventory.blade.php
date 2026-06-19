@extends('wt.layouts.admin')

@section('title', 'My Inventory')

@push('styles')
<style>
    .my-inventory-owner-card {
        border: 1px solid rgba(139, 94, 60, 0.14);
        background: rgba(139, 94, 60, 0.06);
    }

    .dark .my-inventory-owner-card {
        border-color: rgba(148, 163, 184, 0.18);
        background: rgba(15, 23, 42, 0.34);
    }

    .my-inventory-view-btn {
        display: inline-flex;
        min-height: 26px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: 6px;
        border: 1px solid rgba(139, 94, 60, 0.24);
        background: #0284c7;
        padding: 5px 8px;
        color: #fff;
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        transition: transform 0.16s ease, background 0.16s ease;
        white-space: nowrap;
    }

    .my-inventory-view-btn:hover {
        transform: translateY(-1px);
        background: #724D31;
    }

    .my-inventory-view-btn[disabled] {
        cursor: not-allowed;
        opacity: 0.45;
        transform: none;
    }

    .my-inventory-secondary-btn {
        display: inline-flex;
        min-height: 30px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border-radius: 9px;
        border: 1px solid rgba(139, 94, 60, 0.24);
        background: #ffffff;
        padding: 7px 10px;
        color: #0284c7;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        transition: transform 0.16s ease, background 0.16s ease;
        white-space: nowrap;
    }

    .my-inventory-secondary-btn:hover {
        transform: translateY(-1px);
        background: rgba(139, 94, 60, 0.08);
    }

    .dark .my-inventory-secondary-btn {
        border-color: rgba(148, 163, 184, 0.22);
        background: rgba(15, 23, 42, 0.48);
        color: #38bdf8;
    }

    .my-inventory-modal-panel {
        max-height: min(760px, calc(100vh - 48px));
        overflow-y: auto;
        border: 1px solid #e7e5e4;
        background: #ffffff;
    }

    .dark .my-inventory-modal-panel {
        border-color: rgba(148, 163, 184, 0.22);
        background: #152033;
    }
</style>
@endpush

@section('content')
<div class="page-header-block mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    <div>
        <h3 class="page-title-standard">{{ $viewMode === 'history' ? 'My Inventory History' : 'My Inventory' }}</h3>
        <p class="page-subtitle-standard">
            {{ $viewMode === 'history' ? 'Returned walkie talkie history kept up to ' . ($historyRetentionYears ?? 5) . ' years.' : 'List of walkie talkies currently under your care.' }}
        </p>
    </div>
    <div class="flex items-center gap-2">
        @if($viewMode === 'history')
            <a href="{{ route('wt.admin.walkies.myInventory') }}" class="my-inventory-secondary-btn">
                <i class="fa-solid fa-arrow-left"></i>
                Active Rentals
            </a>
        @else
            <a href="{{ route('wt.admin.walkies.myInventory', ['view' => 'history']) }}" class="my-inventory-secondary-btn">
                <i class="fa-solid fa-clock-rotate-left"></i>
                History
            </a>
        @endif
    </div>
</div>

@if($viewMode === 'history')
<div class="bg-white dark:bg-slate-800/50 rounded-2xl shadow-sm border border-stone-100 dark:border-slate-700/50 overflow-hidden transition-all">
    <div class="px-6 py-4 border-b border-stone-50 dark:border-slate-700/50 flex justify-between items-center">
        <h4 class="card-title text-stone-800 dark:text-slate-200">Returned Units History</h4>
        <span class="text-[9px] font-black uppercase tracking-widest text-stone-400">Max {{ $historyRetentionYears ?? 5 }} Years</span>
    </div>
    <div class="p-4">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left table-body min-w-[900px]">
                <thead class="table-head text-stone-400 dark:text-slate-500 border-b border-stone-50 dark:border-slate-700">
                    <tr>
                        <th class="px-4 py-4 text-[9px] uppercase tracking-widest font-black">Request</th>
                        <th class="px-4 py-4 text-[9px] uppercase tracking-widest font-black">Type</th>
                        <th class="px-4 py-4 text-[9px] uppercase tracking-widest font-black">Assigned Units</th>
                        <th class="px-4 py-4 text-[9px] uppercase tracking-widest font-black">Period</th>
                        <th class="px-4 py-4 text-[9px] uppercase tracking-widest font-black">Return Date</th>
                        <th class="px-4 py-4 text-[9px] uppercase tracking-widest font-black">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50 dark:divide-slate-700/30">
                    @forelse($historyRequests as $history)
                    @php
                        $historyUnits = collect($history->assigned_radio_ids ?? [])->filter()->values();
                        if ($historyUnits->isEmpty() && $history->radio_id) {
                            $historyUnits = collect(explode(',', $history->radio_id))->map(fn ($unit) => trim($unit))->filter()->values();
                        }
                        $isTemporaryHistory = $history->request_type === 'temporary_walkie_talkie';
                    @endphp
                    <tr class="hover:bg-stone-50/50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-4 py-4">
                            <div class="font-black text-stone-800 dark:text-slate-100 text-[11px]">Request #{{ str_pad($history->id, 5, '0', STR_PAD_LEFT) }}</div>
                            <div class="mt-1 text-[9px] font-bold uppercase tracking-widest text-stone-400">{{ $history->event_name ?: 'General Request' }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-[9px] font-black uppercase tracking-widest {{ $isTemporaryHistory ? 'text-violet-600 dark:text-violet-300' : 'text-stone-500 dark:text-slate-400' }}">
                                {{ $isTemporaryHistory ? 'Temporary' : 'Standard' }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="font-mono text-[11px] text-stone-600 dark:text-slate-300 font-bold">{{ $historyUnits->isNotEmpty() ? $historyUnits->implode(', ') : '-' }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-[10px] font-bold text-stone-500 dark:text-slate-400">
                                {{ $history->request_date ? \Carbon\Carbon::parse($history->request_date)->format('d M Y') : '-' }}
                                @if($isTemporaryHistory)
                                    - {{ $history->end_date ? \Carbon\Carbon::parse($history->end_date)->format('d M Y') : '-' }}
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-[10px] font-bold text-stone-500 dark:text-slate-400">{{ $history->return_date ? \Carbon\Carbon::parse($history->return_date)->format('d M Y') : '-' }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex rounded-full border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-[8px] font-black uppercase tracking-widest text-indigo-700">Returned / History</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-stone-50 dark:bg-slate-700 flex items-center justify-center text-stone-200 dark:text-slate-600">
                                    <i class="fas fa-clock-rotate-left text-xl"></i>
                                </div>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-stone-300 dark:text-slate-600">No returned history found</p>
                                @if($records->isNotEmpty())
                                <p class="max-w-[280px] text-[9px] font-bold uppercase tracking-wider text-stone-400 dark:text-slate-500">
                                    You still have {{ $records->count() }} active rental {{ \Illuminate\Support\Str::plural('unit', $records->count()) }}. Open Active Rentals to view current assigned walkie talkies.
                                </p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="bg-white dark:bg-slate-800/50 rounded-2xl shadow-sm border border-stone-100 dark:border-slate-700/50 overflow-hidden transition-all">
    <div class="px-6 py-4 border-b border-stone-50 dark:border-slate-700/50 flex justify-between items-center">
        <h4 class="card-title text-stone-800 dark:text-slate-200">Assigned Units</h4>
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-[9px] font-black uppercase tracking-widest text-stone-400">Live Status</span>
        </div>
    </div>
    <div class="p-4">
        <div class="space-y-3">
            @forelse($records as $record)
                    @php
                        $activeRequest = $record->active_request ?? null;
                        $ownerName = $record->ownership
                            ?: ($activeRequest->full_name ?? null)
                            ?: ($activeRequest && $activeRequest->user ? ($activeRequest->user->full_name ?: $activeRequest->user->username) : null)
                            ?: '-';
                        $ownerDept = $record->department ?: ($activeRequest->department ?? null);
                        $picDetails = collect($activeRequest->pic_details ?? [])->filter(fn ($pic) => is_array($pic))->values();
                        $displayRemark = $activeRequest
                            ? ($activeRequest->approval_remark ?: $record->remark)
                            : $record->remark;
                        $reportFaultyUrl = route('wt.admin.damages.form', array_filter([
                            'mode' => 'staff',
                            'walkie_id' => $record->walkie_id,
                            'owner' => $ownerName !== '-' ? $ownerName : null,
                            'department' => $ownerDept,
                            'ownership_type' => $record->ownership_type,
                            'shared_with' => $record->shared_with,
                            'sector' => $activeRequest->sector ?? null,
                            'bay_from' => $activeRequest->bay_from ?? null,
                            'location' => $activeRequest->location ?? null,
                        ], fn ($value) => filled($value)));
                        $requestUnits = collect($activeRequest->assigned_radio_ids ?? [])->filter()->values();
                        if ($requestUnits->isEmpty() && $activeRequest && $activeRequest->radio_id) {
                            $requestUnits = collect(explode(',', $activeRequest->radio_id))->map(fn ($unit) => trim($unit))->filter()->values();
                        }
                    @endphp
                    <article class="rounded-md border border-slate-200 bg-white shadow-sm dark:border-slate-700/70 dark:bg-slate-900/40">
                        <div class="flex flex-col gap-2 border-b border-slate-100 px-4 py-3 dark:border-slate-700/70 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-[8px] font-black uppercase tracking-[0.16em] text-slate-400">Walkie Talkie Unit</p>
                                <h5 class="mt-0.5 text-sm font-black text-slate-900 dark:text-slate-100">Radio ID {{ $record->radio_id }}</h5>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 rounded-md border border-emerald-200 bg-emerald-50 px-2 py-1 text-[8px] font-black uppercase tracking-widest text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $record->status === 'IN USE' ? 'bg-emerald-500' : 'bg-stone-300' }}"></span>
                                    {{ $record->status }}
                                </span>
                                @if($activeRequest)
                                    <button type="button" class="my-inventory-view-btn" onclick="openInventoryRequestModal('inventoryRequestModal-{{ $record->walkie_id }}')">
                                        <i class="fa-solid fa-eye"></i>
                                        View Form
                                    </button>
                                @else
                                    <button type="button" class="my-inventory-view-btn" disabled>
                                        <i class="fa-solid fa-eye-slash"></i>
                                        No Form
                                    </button>
                                @endif
                                <a href="{{ $reportFaultyUrl }}" class="my-inventory-view-btn">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    Report Faulty
                                </a>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-px bg-slate-100 dark:bg-slate-700/70 md:grid-cols-5">
                            <div class="bg-slate-50 px-4 py-2.5 dark:bg-slate-900/50">
                                <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Model</p>
                                <p class="mt-0.5 text-[11px] font-black text-slate-900 dark:text-slate-100">{{ $record->model ?: '-' }}</p>
                            </div>
                            <div class="bg-slate-50 px-4 py-2.5 dark:bg-slate-900/50">
                                <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Serial No</p>
                                <p class="mt-0.5 font-mono text-[11px] font-black text-slate-900 dark:text-slate-100">{{ $record->serial_number ?: '-' }}</p>
                            </div>
                            <div class="bg-slate-50 px-4 py-2.5 dark:bg-slate-900/50">
                                <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Ownership Type</p>
                                <p class="mt-0.5 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ $record->ownership_type ?: '-' }}</p>
                            </div>
                            <div class="bg-white px-4 py-2.5 dark:bg-slate-900/30">
                                <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Owner</p>
                                <p class="mt-0.5 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ $ownerName }}</p>
                            </div>
                            <div class="bg-white px-4 py-2.5 dark:bg-slate-900/30">
                                <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Department</p>
                                <p class="mt-0.5 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ $ownerDept ?: '-' }}</p>
                            </div>
                        </div>

                        <div class="border-t border-slate-100 bg-white px-4 py-2.5 dark:border-slate-700/70 dark:bg-slate-900/30">
                            <p class="text-[8px] font-black uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">ICT Remarks</p>
                            <p class="mt-0.5 whitespace-pre-line text-[11px] font-bold leading-4 text-slate-900 dark:text-slate-100">{{ $displayRemark ?: 'No ICT remarks yet.' }}</p>
                        </div>
                    </article>
                    @if($activeRequest)
                            <div id="inventoryRequestModal-{{ $record->walkie_id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-stone-900/60 p-4 backdrop-blur-sm" onclick="if (event.target === this) closeInventoryRequestModal('inventoryRequestModal-{{ $record->walkie_id }}')">
                                <div class="my-inventory-modal-panel w-full max-w-3xl rounded-2xl shadow-xl">
                                    <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-6 py-5 dark:border-slate-700/70">
                                        <div>
                                            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-[#0284c7] dark:text-[#38bdf8]">View Form</p>
                                            <h3 class="mt-2 text-base font-black text-stone-900 dark:text-slate-100">Request #{{ str_pad($activeRequest->id, 5, '0', STR_PAD_LEFT) }}</h3>
                                            <p class="mt-1 text-[10px] font-bold uppercase tracking-widest text-stone-400 dark:text-slate-500">{{ $activeRequest->request_type === 'temporary_walkie_talkie' ? 'Temporary Request' : 'Walkie Talkie Request' }}</p>
                                        </div>
                                        <button type="button" class="rounded-xl border border-stone-200 px-3 py-2 text-stone-500 transition hover:bg-stone-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800" onclick="closeInventoryRequestModal('inventoryRequestModal-{{ $record->walkie_id }}')">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>

                                    <div class="space-y-4 px-6 py-5">
                                        <section>
                                            <p class="mb-2 text-[9px] font-black uppercase tracking-[0.18em] text-slate-400">Ownership Information</p>
                                            <div class="space-y-3">
                                                @forelse($picDetails as $picIndex => $pic)
                                                <div class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900/40">
                                                    <div class="flex items-center gap-2 border-b border-slate-100 px-3 py-2 dark:border-slate-700">
                                                        <span class="h-5 w-1 rounded-full bg-[#0284c7]"></span>
                                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-800 dark:text-slate-100">{{ $picIndex + 1 }}. Ownership Information</p>
                                                    </div>
                                                    <div class="grid grid-cols-1 gap-3 p-3 md:grid-cols-2">
                                                        <div>
                                                            <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Ownership Name</p>
                                                            <p class="mt-1 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ !empty($pic['name']) ? $pic['name'] : '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Ownership Phone No</p>
                                                            <p class="mt-1 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ !empty($pic['phone_no']) ? $pic['phone_no'] : '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Department</p>
                                                            <p class="mt-1 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ !empty($pic['department']) ? $pic['department'] : '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Ownership Type</p>
                                                            <p class="mt-1 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ !empty($pic['ownership_type']) ? $pic['ownership_type'] : '-' }}</p>
                                                        </div>
                                                        @if(strtoupper((string) ($pic['ownership_type'] ?? '')) === 'SHARED')
                                                        <div>
                                                            <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Shared With</p>
                                                            <p class="mt-1 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ !empty($pic['shared_with']) ? $pic['shared_with'] : '-' }}</p>
                                                        </div>
                                                        @endif
                                                        <div>
                                                            <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Sector</p>
                                                            <p class="mt-1 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ !empty($pic['sector']) ? $pic['sector'] : '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Bay From</p>
                                                            <p class="mt-1 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ !empty($pic['bay_from']) ? $pic['bay_from'] : '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Location</p>
                                                            <p class="mt-1 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ !empty($pic['location']) ? $pic['location'] : '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="border-t border-slate-100 bg-slate-50 px-3 py-3 dark:border-slate-700 dark:bg-slate-800/60">
                                                        <p class="text-[8px] font-black uppercase tracking-widest text-[#0284c7] dark:text-[#38bdf8]">Pickup Info</p>
                                                        <p class="mt-1 text-[10px] font-bold text-slate-600 dark:text-slate-300">This unit is for the ownership name entered above. Pick up the approved walkie talkie at ICT Department after ICT approves this request.</p>
                                                        <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-2">
                                                            <div>
                                                                <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Who Will Pick Up This Walkie Talkie?</p>
                                                                <p class="mt-1 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ !empty($pic['pickup_person']) ? $pic['pickup_person'] : '-' }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Pickup Phone No</p>
                                                                <p class="mt-1 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ !empty($pic['pickup_phone_no']) ? $pic['pickup_phone_no'] : '-' }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @empty
                                                <div class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900/40">
                                                    <div class="flex items-center gap-2 border-b border-slate-100 px-3 py-2 dark:border-slate-700">
                                                        <span class="h-5 w-1 rounded-full bg-[#0284c7]"></span>
                                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-800 dark:text-slate-100">1. Ownership Information</p>
                                                    </div>
                                                    <div class="grid grid-cols-1 gap-3 p-3 md:grid-cols-2">
                                                        <div><p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Ownership Name</p><p class="mt-1 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ $activeRequest->full_name ?: $ownerName }}</p></div>
                                                        <div><p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Ownership Phone No</p><p class="mt-1 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">-</p></div>
                                                        <div><p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Department</p><p class="mt-1 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ $ownerDept ?: '-' }}</p></div>
                                                        <div><p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Ownership Type</p><p class="mt-1 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ $activeRequest->ownership_type ?: $record->ownership_type ?: '-' }}</p></div>
                                                        <div><p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Sector</p><p class="mt-1 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ $activeRequest->sector ?: '-' }}</p></div>
                                                        <div><p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Bay From</p><p class="mt-1 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ $activeRequest->bay_from ?: '-' }}</p></div>
                                                        <div><p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Location</p><p class="mt-1 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ $activeRequest->location ?: '-' }}</p></div>
                                                    </div>
                                                    <div class="border-t border-slate-100 bg-slate-50 px-3 py-3 dark:border-slate-700 dark:bg-slate-800/60">
                                                        <p class="text-[8px] font-black uppercase tracking-widest text-[#0284c7] dark:text-[#38bdf8]">Pickup Info</p>
                                                        <p class="mt-1 text-[10px] font-bold text-slate-600 dark:text-slate-300">This unit is for the ownership name entered above. Pick up the approved walkie talkie at ICT Department after ICT approves this request.</p>
                                                        <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-2">
                                                            <div><p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Who Will Pick Up This Walkie Talkie?</p><p class="mt-1 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ $activeRequest->pickup_representative_name ?: $activeRequest->full_name ?: '-' }}</p></div>
                                                            <div><p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Pickup Phone No</p><p class="mt-1 text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">-</p></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforelse
                                            </div>
                                        </section>

                                        <section class="grid grid-cols-1 gap-3">
                                            <div class="rounded-lg border border-slate-200 p-3 dark:border-slate-700">
                                                <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Justification / Notes</p>
                                                <p class="mt-1 whitespace-pre-line text-[11px] font-bold leading-5 text-slate-900 dark:text-slate-100">{{ $activeRequest->justifications ?: 'No notes.' }}</p>
                                            </div>

                                            <div class="rounded-lg border border-slate-200 p-3 dark:border-slate-700">
                                                <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">ICT Remarks / Update</p>
                                                <p class="mt-1 whitespace-pre-line text-[11px] font-bold leading-5 text-slate-900 dark:text-slate-100">{{ $activeRequest->approval_remark ?: 'No ICT remarks yet.' }}</p>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </div>
                    @endif
                    @empty
                    <div class="px-4 py-20 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-stone-50 text-stone-200 dark:bg-slate-700 dark:text-slate-600">
                                <i class="fas fa-box-open text-xl"></i>
                            </div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-stone-300 dark:text-slate-600">No walkie talkies assigned to you</p>
                            <p class="mt-1 max-w-[200px] text-[9px] leading-relaxed text-stone-400 dark:text-slate-500">If you should have a device assigned, please contact the ICT department for updates.</p>
                        </div>
                    </div>
                    @endforelse
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    function openInventoryRequestModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeInventoryRequestModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
</script>
@endpush

