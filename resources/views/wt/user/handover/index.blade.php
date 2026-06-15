@php
    $isAdminRoute = request()->routeIs('admin.*');
    $layout = $isAdminRoute ? 'layouts.admin' : 'layouts.user';
    $routePrefix = $isAdminRoute ? 'admin' : 'user';
@endphp

@extends($layout)

@section('title')
Handover WT
@endsection

@push('styles')
<style>
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
    $statusBadge = function ($request) {
        return match ($request->status) {
            'Pending Admin Approval', 'Pending IT Approval' => ['text' => 'Processing', 'class' => 'border-amber-200 bg-amber-50 text-amber-700'],
            'Approved' => ['text' => 'Ready To Collect', 'class' => 'border-sky-200 bg-sky-50 text-sky-700'],
            'Rejected' => ['text' => 'Rejected', 'class' => 'border-red-200 bg-red-50 text-red-700'],
            default => ['text' => $request->status ?: 'Unknown', 'class' => 'border-stone-200 bg-stone-50 text-stone-700'],
        };
    };
@endphp
<div class="px-2">
    <div class="mb-3">
        <h3 class="text-sm font-extrabold text-[#3D2B1F] tracking-tight">Handover WT</h3>
        <p class="text-stone-400 font-medium mt-0.5 text-[9px] tracking-widest uppercase">
            Respond to ICT pickup notifications for approved walkie talkie requests.
        </p>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[11px] font-bold text-emerald-800">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-[11px] font-bold text-red-700">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-[11px] font-bold text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="mb-4 bg-white rounded-2xl shadow-sm border border-[#8B5E3C]/10 overflow-hidden dark:bg-slate-800 dark:border-slate-700">
        <div class="p-3 border-b border-stone-100 bg-stone-50/70 dark:bg-slate-900 dark:border-slate-700">
            <h4 class="text-[11px] font-black text-[#3D2B1F] uppercase tracking-wider dark:text-slate-100">Current Handover Status WT</h4>
            <p class="text-[9px] text-stone-500 mt-0.5 uppercase tracking-wide dark:text-slate-400">Check whether your request is still processing or already ready to collect.</p>
        </div>

        <div class="p-4">
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @forelse($statusRequests as $request)
                    @php($badge = $statusBadge($request))
                    <div class="rounded-2xl border border-stone-200 p-4 dark:border-slate-700 dark:bg-slate-900/50">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-[8px] uppercase tracking-widest text-stone-400 font-black">Request #{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</p>
                                <p class="mt-1 text-[12px] font-black text-[#3D2B1F] dark:text-slate-100">{{ $request->event_name ?: 'Walkie Talkie Request' }}</p>
                            </div>
                            <span class="inline-flex rounded-full border px-2.5 py-1 text-[8px] font-black uppercase tracking-widest {{ $badge['class'] }}">
                                {{ $badge['text'] }}
                            </span>
                        </div>

                        <div class="mt-3 grid grid-cols-1 gap-2 text-[10px] font-bold text-stone-500 dark:text-slate-400">
                            <p>Request Date: <span class="text-stone-800 dark:text-slate-100">{{ $request->request_date ? \Carbon\Carbon::parse($request->request_date)->format('d M Y') : '-' }}</span></p>
                            <p>Radio ID: <span class="text-stone-800 dark:text-slate-100">{{ $request->radio_id ?: '-' }}</span></p>
                            <p>Serial No: <span class="text-stone-800 dark:text-slate-100">{{ $request->assigned_serial_number ?: '-' }}</span></p>
                            <p>Preferred Pickup: <span class="text-stone-800 dark:text-slate-100">{{ $request->requested_pickup_at ? \Carbon\Carbon::parse($request->requested_pickup_at)->format('d M Y H:i') : '-' }}</span></p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8 text-stone-400 text-[11px] font-bold">
                        No handover status records found.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-[#8B5E3C]/10 overflow-hidden dark:bg-slate-800 dark:border-slate-700">
        <div class="p-3 border-b border-stone-100 bg-stone-50/70 dark:bg-slate-900 dark:border-slate-700">
            <h4 class="text-[11px] font-black text-[#3D2B1F] uppercase tracking-wider dark:text-slate-100">Pickup Notifications WT</h4>
            <p class="text-[9px] text-stone-500 mt-0.5 uppercase tracking-wide dark:text-slate-400">Pick up walkie talkie at ICT Department.</p>
        </div>

        <div class="p-4 space-y-4">
            @forelse($pendingHandovers as $request)
                <div class="rounded-2xl border border-stone-200 p-4 dark:border-slate-700 dark:bg-slate-900/50">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-2">
                            <p class="text-[8px] uppercase tracking-widest text-stone-400 font-black">Request #{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</p>
                            <p class="text-[12px] font-black text-[#3D2B1F] dark:text-slate-100">{{ $request->full_name }}</p>
                            <div class="grid grid-cols-1 gap-2 text-[10px] font-bold text-stone-500 md:grid-cols-2 dark:text-slate-400">
                                <p>Radio ID: <span class="text-stone-800 dark:text-slate-100">{{ $request->radio_id ?: '-' }}</span></p>
                                <p>Serial No: <span class="text-stone-800 dark:text-slate-100">{{ $request->assigned_serial_number ?: '-' }}</span></p>
                                <p>Department: <span class="text-stone-800 dark:text-slate-100">{{ $request->department ?: '-' }}</span></p>
                                <p>Location: <span class="text-stone-800 dark:text-slate-100">{{ $request->location ?: '-' }}</span></p>
                                <p>Walkie Talkie For: <span class="text-stone-800 dark:text-slate-100">{{ $request->full_name ?: '-' }}</span></p>
                                <p>Preferred Pickup: <span class="text-stone-800 dark:text-slate-100">{{ $request->requested_pickup_at ? \Carbon\Carbon::parse($request->requested_pickup_at)->format('d M Y H:i') : '-' }}</span></p>
                            </div>
                            <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-[11px] font-black text-sky-800 dark:border-sky-900/60 dark:bg-sky-950/40 dark:text-sky-200">
                                Pick up walkie talkie at ICT Department after ICT approval.
                            </div>
                        </div>

                        <form action="{{ route($routePrefix . '.handover.store') }}" method="POST" class="flex w-full flex-col gap-2 sm:w-auto lg:min-w-[230px]" data-pickup-form>
                            @csrf
                            <input type="hidden" name="access_request_id" value="{{ $request->id }}">
                            <button type="submit" name="pickup_response" value="yes" class="rounded-lg bg-emerald-600 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white hover:bg-emerald-700">
                                Ready To Pickup
                            </button>
                            <button type="button" class="rounded-lg bg-sky-700 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white hover:bg-sky-800" data-representative-toggle>
                                Representative
                            </button>
                            <div class="hidden rounded-xl border border-sky-200 bg-sky-50 p-3 dark:border-sky-900/60 dark:bg-sky-950/40" data-representative-panel>
                                <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-sky-800 dark:text-sky-200">Representative Pickup Name</label>
                                <input type="text" name="representative_name" class="w-full rounded-lg border border-sky-200 bg-white px-3 py-2 text-[11px] font-bold text-slate-800 outline-none focus:border-sky-500 dark:border-sky-800 dark:bg-slate-900 dark:text-slate-100" placeholder="Name of person picking up" data-representative-input>
                                <button type="submit" name="pickup_response" value="representative" class="mt-2 w-full rounded-lg bg-sky-700 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white hover:bg-sky-800">
                                    Submit Representative
                                </button>
                            </div>
                            <button type="button" class="rounded-lg bg-slate-600 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white hover:bg-slate-700" data-not-yet-toggle>
                                Not Yet
                            </button>
                            <div class="hidden rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900" data-not-yet-panel>
                                <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-700 dark:text-slate-200">When can you collect?</label>
                                <input type="text" name="pickup_collection_note" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px] font-bold text-slate-800 outline-none focus:border-slate-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" placeholder="Example: Tomorrow 10 AM / 20 May after lunch" data-not-yet-input>
                                <button type="submit" name="pickup_response" value="not_yet" class="mt-2 w-full rounded-lg bg-slate-600 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white hover:bg-slate-700">
                                    Submit Not Yet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-stone-400">No handover notifications are waiting for you right now.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-pickup-form]').forEach(function (form) {
        const toggle = form.querySelector('[data-representative-toggle]');
        const panel = form.querySelector('[data-representative-panel]');
        const input = form.querySelector('[data-representative-input]');
        const notYetToggle = form.querySelector('[data-not-yet-toggle]');
        const notYetPanel = form.querySelector('[data-not-yet-panel]');
        const notYetInput = form.querySelector('[data-not-yet-input]');

        if (toggle && panel && input) {
            toggle.addEventListener('click', function () {
                panel.classList.toggle('hidden');
                if (!panel.classList.contains('hidden')) {
                    input.focus();
                }
            });
        }

        if (notYetToggle && notYetPanel && notYetInput) {
            notYetToggle.addEventListener('click', function () {
                notYetPanel.classList.toggle('hidden');
                if (!notYetPanel.classList.contains('hidden')) {
                    notYetInput.focus();
                }
            });
        }
    });
});
</script>
@endpush

