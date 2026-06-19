@extends('wt.layouts.user')

@section('title', 'Request Status')

@section('content')
@php
    $activeStatusFilter = $activeStatusFilter ?? null;
    $statusFilterLabels = [
        'draft' => 'Draft',
        'processing' => 'Processing',
        'ready' => 'Ready To Collect',
        'approved' => 'Approved',
        'history' => 'Return History',
        'rejected' => 'Rejected',
    ];
    $statusBadge = function ($request) {
        if (($request->source_type ?? 'request') === 'repair') {
            return ($request->status_group ?? 'processing') === 'ready'
                ? ['text' => 'Already Fixed / Ready To Collect', 'class' => 'border-sky-200 bg-sky-50 text-sky-700']
                : ['text' => 'Processing', 'class' => 'border-amber-200 bg-amber-50 text-amber-700'];
        }

        return match ($request->status) {
            'Draft' => ['text' => 'Draft', 'class' => 'border-stone-200 bg-stone-50 text-stone-700'],
            'Pending Admin Approval', 'Pending IT Approval' => [
                'text' => !empty($request->return_status) ? 'Return Processing' : 'Processing',
                'class' => 'border-amber-200 bg-amber-50 text-amber-700'
            ],
            'Pending Staff Pickup' => ['text' => 'Ready To Collect', 'class' => 'border-sky-200 bg-sky-50 text-sky-700'],
            'Approved' => ['text' => 'Approved', 'class' => 'border-emerald-200 bg-emerald-50 text-emerald-700'],
            'Returned' => ['text' => 'Returned / History', 'class' => 'border-indigo-200 bg-indigo-50 text-indigo-700'],
            'Rejected' => ['text' => 'Rejected', 'class' => 'border-red-200 bg-red-50 text-red-700'],
            default => ['text' => $request->status ?: 'Unknown', 'class' => 'border-stone-200 bg-stone-50 text-stone-700'],
        };
    };
@endphp

<div class="px-2">
    <div class="mb-4">
        <h3 class="text-sm font-extrabold text-[#142b47] tracking-tight">Request Status</h3>
        <p class="text-stone-400 font-medium mt-0.5 text-[9px] tracking-widest uppercase">
            Check the latest status and return history of all your requests. Returned history is kept up to {{ $historyRetentionYears ?? 5 }} years.
        </p>
    </div>

    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-6 mb-4">
        <a href="{{ route('wt.user.requests.status', ['status' => 'draft']) }}" class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm transition hover:border-stone-300 {{ $activeStatusFilter === 'draft' ? 'ring-2 ring-stone-300' : '' }}">
            <p class="text-[9px] font-black uppercase tracking-[0.18em] text-stone-600">Draft</p>
            <p class="mt-2 text-2xl font-black text-slate-800">{{ $statusSummary['draft'] }}</p>
        </a>
        <a href="{{ route('wt.user.requests.status', ['status' => 'processing']) }}" class="rounded-2xl border border-amber-100 bg-white p-4 shadow-sm transition hover:border-amber-200 {{ $activeStatusFilter === 'processing' ? 'ring-2 ring-amber-200' : '' }}">
            <p class="text-[9px] font-black uppercase tracking-[0.18em] text-amber-600">Processing</p>
            <p class="mt-2 text-2xl font-black text-slate-800">{{ $statusSummary['processing'] }}</p>
        </a>
        <a href="{{ route('wt.user.requests.status', ['status' => 'ready']) }}" class="rounded-2xl border border-sky-100 bg-white p-4 shadow-sm transition hover:border-sky-200 {{ $activeStatusFilter === 'ready' ? 'ring-2 ring-sky-200' : '' }}">
            <p class="text-[9px] font-black uppercase tracking-[0.18em] text-sky-600">Ready To Collect</p>
            <p class="mt-2 text-2xl font-black text-slate-800">{{ $statusSummary['ready'] }}</p>
        </a>
        <a href="{{ route('wt.user.requests.status', ['status' => 'approved']) }}" class="rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm transition hover:border-emerald-200 {{ $activeStatusFilter === 'approved' ? 'ring-2 ring-emerald-200' : '' }}">
            <p class="text-[9px] font-black uppercase tracking-[0.18em] text-emerald-600">Approved</p>
            <p class="mt-2 text-2xl font-black text-slate-800">{{ $statusSummary['approved'] }}</p>
        </a>
        <a href="{{ route('wt.user.requests.status', ['status' => 'history']) }}" class="rounded-2xl border border-indigo-100 bg-white p-4 shadow-sm transition hover:border-indigo-200 {{ $activeStatusFilter === 'history' ? 'ring-2 ring-indigo-200' : '' }}">
            <p class="text-[9px] font-black uppercase tracking-[0.18em] text-indigo-600">History</p>
            <p class="mt-2 text-2xl font-black text-slate-800">{{ $statusSummary['history'] ?? 0 }}</p>
        </a>
        <a href="{{ route('wt.user.requests.status', ['status' => 'rejected']) }}" class="rounded-2xl border border-red-100 bg-white p-4 shadow-sm transition hover:border-red-200 {{ $activeStatusFilter === 'rejected' ? 'ring-2 ring-red-200' : '' }}">
            <p class="text-[9px] font-black uppercase tracking-[0.18em] text-red-600">Rejected</p>
            <p class="mt-2 text-2xl font-black text-slate-800">{{ $statusSummary['rejected'] }}</p>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-[#0284c7]/10 overflow-hidden">
        <div class="p-3 border-b border-stone-100 bg-stone-50/70">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h4 class="text-[11px] font-black text-[#142b47] uppercase tracking-wider">{{ $activeStatusFilter ? ($statusFilterLabels[$activeStatusFilter] ?? 'My Request Status') : 'My Request Status' }}</h4>
                @if($activeStatusFilter)
                    <a href="{{ route('wt.user.requests.status') }}" class="text-[9px] font-black uppercase tracking-widest text-stone-500 hover:text-stone-800">View All</a>
                @endif
            </div>
        </div>

        <div class="p-4">
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @forelse($requestStatuses as $request)
                    @php
                        $badge = $statusBadge($request);
                        $isTemporaryRequest = $request->is_temporary ?? false;
                        $isRepairRequest = ($request->source_type ?? 'request') === 'repair';
                    @endphp
                    <div class="rounded-2xl border border-stone-200 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-[8px] uppercase tracking-widest text-stone-400 font-black">{{ $isRepairRequest ? 'Repair Request' : 'Request' }} #{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</p>
                                <p class="mt-1 text-[12px] font-black text-[#142b47]">{{ $request->title ?: 'Walkie Talkie Request' }}</p>
                                @if($isRepairRequest)
                                <p class="mt-1 text-[8px] font-black uppercase tracking-widest text-orange-600">Faulty Walkie Talkie</p>
                                @endif
                                @if($isTemporaryRequest)
                                <p class="mt-1 text-[8px] font-black uppercase tracking-widest text-violet-600">Temporary Request x{{ $request->quantity ?: 1 }}</p>
                                @endif
                            </div>
                            <span class="inline-flex rounded-full border px-2.5 py-1 text-[8px] font-black uppercase tracking-widest {{ $badge['class'] }}">
                                {{ $badge['text'] }}
                            </span>
                        </div>

                        <div class="mt-3 grid grid-cols-1 gap-2 text-[10px] font-bold text-stone-500">
                            @if(!$isRepairRequest)
                            <div class="mb-1">
                                @include('wt.partials.approval-flow', ['request' => $request, 'compact' => true])
                            </div>
                            @endif
                            <p>Request Date: <span class="text-stone-800">{{ $request->request_date ? \Carbon\Carbon::parse($request->request_date)->format('d M Y') : '-' }}</span></p>
                            @if($isTemporaryRequest)
                            <p>Period: <span class="text-stone-800">{{ $request->request_date ? \Carbon\Carbon::parse($request->request_date)->format('d M Y') : '-' }} - {{ $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d M Y') : '-' }}</span></p>
                            @php $userStatusDays = max(1, (int) ($request->duration_days ?: 1)); @endphp
                            <p>Duration: <span class="text-stone-800">{{ $userStatusDays }} {{ \Illuminate\Support\Str::plural('day', $userStatusDays) }}</span></p>
                            @endif
                            <p>Department: <span class="text-stone-800">{{ $request->department ?: '-' }}</span></p>
                            <p>Position: <span class="text-stone-800">{{ $request->position ?: '-' }}</span></p>
                            <p>{{ $isRepairRequest ? 'Unit Info' : 'Radio ID' }}: <span class="text-stone-800">{{ $request->radio_id ?: '-' }}</span></p>
                            @if(!empty($request->approval_remark))
                            <p>Remark by Approval: <span class="text-stone-800">{{ $request->approval_remark }}</span></p>
                            @endif
                            @if($request->note)
                            <p>Note: <span class="text-stone-800">{{ $request->note }}</span></p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8 text-stone-400 text-[11px] font-bold">
                        No request status records found.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

