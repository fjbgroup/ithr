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

    .my-inventory-action-stack {
        display: inline-flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 6px;
    }

    .my-inventory-return-btn {
        background: #16a34a;
    }

    .my-inventory-return-btn:hover {
        background: #15803d;
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

    .my-inventory-search {
        position: relative;
        width: min(100%, 360px);
    }

    .my-inventory-search i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 11px;
        pointer-events: none;
    }

    .my-inventory-search input {
        width: 100%;
        min-height: 36px;
        border-radius: 9px;
        border: 1px solid #dbe3ef;
        background: #ffffff;
        padding: 8px 12px 8px 34px;
        color: #0f172a;
        font-size: 11px;
        font-weight: 800;
        outline: none;
    }

    .my-inventory-search input:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
    }

    .dark .my-inventory-search input {
        border-color: rgba(148, 163, 184, 0.24);
        background: rgba(15, 23, 42, 0.48);
        color: #e2e8f0;
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
                        <th class="px-4 py-4 text-center text-[9px] uppercase tracking-widest font-black">Request</th>
                        <th class="px-4 py-4 text-center text-[9px] uppercase tracking-widest font-black">Type</th>
                        <th class="px-4 py-4 text-center text-[9px] uppercase tracking-widest font-black">Assigned Units</th>
                        <th class="px-4 py-4 text-center text-[9px] uppercase tracking-widest font-black">Period</th>
                        <th class="px-4 py-4 text-center text-[9px] uppercase tracking-widest font-black">Return Date</th>
                        <th class="px-4 py-4 text-center text-[9px] uppercase tracking-widest font-black">Status</th>
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
    <div class="px-6 py-4 border-b border-stone-50 dark:border-slate-700/50 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <h4 class="card-title text-stone-800 dark:text-slate-200">Assigned Units</h4>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <label class="my-inventory-search" for="myInventorySearch">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" id="myInventorySearch" placeholder="Search radio ID, serial, owner, department...">
            </label>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[9px] font-black uppercase tracking-widest text-stone-400">Live Status</span>
            </div>
        </div>
    </div>
    <div class="p-4">
        <div class="overflow-x-auto custom-scrollbar" id="myInventoryList">
            <table class="w-full table-body text-left">
                <thead class="table-head border-b border-stone-50 text-stone-400 dark:border-slate-700 dark:text-slate-500">
                    <tr>
                        <th class="px-4 py-4 text-center text-[9px] font-black uppercase tracking-widest">Radio ID</th>
                        <th class="px-4 py-4 text-center text-[9px] font-black uppercase tracking-widest">Status</th>
                        <th class="px-4 py-4 text-center text-[9px] font-black uppercase tracking-widest">Serial No</th>
                        <th class="px-4 py-4 text-center text-[9px] font-black uppercase tracking-widest">Model</th>
                        <th class="px-4 py-4 text-center text-[9px] font-black uppercase tracking-widest">Assigned To</th>
                        <th class="px-4 py-4 text-center text-[9px] font-black uppercase tracking-widest">Department</th>
                        <th class="px-4 py-4 text-center text-[9px] font-black uppercase tracking-widest">Ownership Type</th>
                        <th class="px-4 py-4 text-center text-[9px] font-black uppercase tracking-widest">Remarks</th>
                        <th class="px-4 py-4 text-center text-[9px] font-black uppercase tracking-widest">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50 dark:divide-slate-700/30">
                    @forelse($records as $record)
                    @php
                        $activeRequest = $record->active_request ?? null;
                        $ownerName = $record->ownership
                            ?: ($activeRequest->full_name ?? null)
                            ?: ($activeRequest && $activeRequest->user ? ($activeRequest->user->full_name ?: $activeRequest->user->username) : null)
                            ?: '-';
                        $ownerDept = $record->department ?: ($activeRequest->department ?? null);
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
                            'bay_from' => $activeRequest->bay_from ?? null,
                            'location' => $activeRequest->location ?? null,
                        ], fn ($value) => filled($value)));
                        $returnUrl = route('wt.admin.returns.create', array_filter([
                            'mode' => 'self',
                            'walkie_id' => $record->walkie_id,
                            'radio_id' => $record->radio_id,
                            'q' => $record->radio_id,
                        ], fn ($value) => filled($value)));
                    @endphp
                    <tr class="hover:bg-stone-50/50 dark:hover:bg-slate-700/30 transition-colors" data-my-inventory-item data-my-inventory-search="{{ strtoupper(implode(' ', [
                        $record->radio_id,
                        $record->model,
                        $record->serial_number,
                        $record->status,
                        $record->ownership_type,
                        $ownerName,
                        $ownerDept,
                        $displayRemark,
                        optional($activeRequest)->event_name,
                        optional($activeRequest)->staff_id,
                        optional($activeRequest)->request_type,
                    ])) }}">
                        <td class="px-4 py-4 text-center font-mono text-[11px] font-black text-slate-900 dark:text-slate-100">{{ $record->radio_id ?: '-' }}</td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 rounded-md border border-emerald-200 bg-emerald-50 px-2 py-1 text-[8px] font-black uppercase tracking-widest text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                                <span class="h-1.5 w-1.5 rounded-full {{ strtoupper((string) $record->status) === 'IN USE' ? 'bg-emerald-500' : 'bg-stone-300' }}"></span>
                                {{ $record->status ?: '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center font-mono text-[11px] font-bold text-slate-600 dark:text-slate-300">{{ $record->serial_number ?: '-' }}</td>
                        <td class="px-4 py-4 text-center text-[11px] font-bold text-slate-600 dark:text-slate-300">{{ $record->model ?: '-' }}</td>
                        <td class="px-4 py-4 text-center text-[11px] font-black uppercase text-slate-900 dark:text-slate-100">{{ $ownerName }}</td>
                        <td class="px-4 py-4 text-center text-[11px] font-bold uppercase text-slate-600 dark:text-slate-300">{{ $ownerDept ?: '-' }}</td>
                        <td class="px-4 py-4 text-center text-[11px] font-bold uppercase text-slate-600 dark:text-slate-300">{{ $record->ownership_type ?: '-' }}</td>
                        <td class="px-4 py-4 text-center text-[10px] font-bold text-slate-500 dark:text-slate-400">{{ $displayRemark ?: '-' }}</td>
                        <td class="px-4 py-4 text-center">
                            <div class="my-inventory-action-stack">
                                <a href="{{ $returnUrl }}" class="my-inventory-view-btn my-inventory-return-btn">
                                    <i class="fa-solid fa-rotate-left"></i>
                                    Return
                                </a>
                                <a href="{{ $reportFaultyUrl }}" class="my-inventory-view-btn">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    Report Faulty
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-0 text-center align-middle">
                            <div class="flex min-h-[340px] w-full items-center justify-center px-6 py-10">
                                <div class="mx-auto flex max-w-[520px] flex-col items-center justify-center gap-3 text-center">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-stone-50 text-stone-200 dark:bg-slate-700 dark:text-slate-600">
                                    <i class="fas fa-box-open text-xl"></i>
                                </div>
                                <p class="text-center text-[10px] font-black uppercase tracking-[0.2em] text-stone-300 dark:text-slate-600">No assets assigned to you yet</p>
                                <p class="mt-1 mx-auto w-full max-w-[420px] text-center text-[9px] leading-relaxed text-stone-400 dark:text-slate-500">Assigned inventory will appear here automatically once ICT assigns assets to your executive account.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="myInventorySearchEmpty" class="hidden px-4 py-16 text-center">
            <div class="flex flex-col items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-stone-50 text-stone-200 dark:bg-slate-700 dark:text-slate-600">
                    <i class="fas fa-magnifying-glass text-xl"></i>
                </div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-stone-300 dark:text-slate-600">No matching walkie talkie found</p>
            </div>
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

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('myInventorySearch');
        const emptyState = document.getElementById('myInventorySearchEmpty');
        const items = Array.from(document.querySelectorAll('[data-my-inventory-item]'));

        if (!searchInput) {
            return;
        }

        searchInput.addEventListener('input', function () {
            const query = searchInput.value.trim().toUpperCase();
            let visibleCount = 0;

            items.forEach((item) => {
                const isVisible = query === '' || (item.dataset.myInventorySearch || '').includes(query);
                item.style.display = isVisible ? '' : 'none';

                if (isVisible) {
                    visibleCount += 1;
                }
            });

            if (emptyState) {
                emptyState.classList.toggle('hidden', visibleCount > 0 || items.length === 0);
            }
        });
    });
</script>
@endpush
