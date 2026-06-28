@extends('wt.layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<style>
    .dashboard-shell {
        color: var(--text);
    }
    .dashboard-shell .profile-name {
        font-size: 1rem;
        line-height: 1.2;
        font-weight: 800;
        letter-spacing: -0.02em;
    }
    .dashboard-shell .profile-pill {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .dashboard-shell .section-title {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .dashboard-shell .section-subtitle {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .dashboard-shell .card-title {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .dashboard-shell .card-meta {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .dashboard-shell .table-head {
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .dashboard-shell .table-body {
        font-size: 10px;
    }
    .dashboard-shell .table-model {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .dashboard-shell .table-status {
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .dashboard-shell .profile-btn {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .dashboard-shell #recentAssetsTable,
    .dashboard-shell #recentAssetsTable_wrapper {
        width: 100% !important;
    }
    .dashboard-shell #recentAssetsTable {
        table-layout: fixed;
    }
    .dashboard-shell #recentAssetsTable th,
    .dashboard-shell #recentAssetsTable td {
        width: 33.333% !important;
    }
    .dashboard-shell #recentAssetsTable td.dataTables_empty {
        height: 72px !important;
        padding: 24px 12px !important;
        text-align: center !important;
        color: #94a3b8 !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        font-size: 11px !important;
        font-weight: 700 !important;
    }
    .dark .dashboard-shell #recentAssetsTable td.dataTables_empty {
        color: #94a3b8 !important;
        background: #1e293b !important;
        border-color: #334155 !important;
    }
    .dashboard-shell #recentAssetsTable_wrapper .adminit-table-footer {
        margin-top: 0 !important;
    }
    .dashboard-shell .total-wt-badge {
        border-color: rgba(179, 138, 90, 0.3) !important;
        background: rgba(179, 138, 90, 0.12) !important;
    }
    .dashboard-shell .total-wt-badge i,
    .dashboard-shell .total-wt-badge .card-meta,
    .dashboard-shell .total-wt-badge .total-wt-value {
        color: #0284c7 !important;
    }
    html:not(.dark) .dashboard-shell .total-wt-badge {
        border-color: #e7d7c3 !important;
        background: #f7efe6 !important;
    }
    html:not(.dark) .dashboard-shell .total-wt-badge i,
    html:not(.dark) .dashboard-shell .total-wt-badge .card-meta,
    html:not(.dark) .dashboard-shell .total-wt-badge .total-wt-value {
        color: #142b47 !important;
    }
    .dashboard-shell .activity-list {
        display: grid;
        gap: 10px;
    }
    .dashboard-shell .activity-item {
        display: flex;
        gap: 10px;
        padding: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
    }
    .dashboard-shell .activity-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: #e0f2fe;
        color: #0284c7;
        flex-shrink: 0;
        font-size: 11px;
    }
    .dashboard-shell .activity-user {
        font-size: 11px;
        font-weight: 900;
        color: #142b47;
        line-height: 1.2;
        text-transform: uppercase;
    }
    .dashboard-shell .activity-details {
        margin-top: 3px;
        color: #64748b;
        font-size: 10px;
        font-weight: 700;
        line-height: 1.35;
    }
    .dashboard-shell .activity-time {
        margin-top: 5px;
        color: #94a3b8;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }
    html.dark .dashboard-shell .activity-item {
        border-color: #334155;
        background: #0f172a;
    }
    html.dark .dashboard-shell .activity-icon {
        background: rgba(14, 165, 233, .15);
        color: #38bdf8;
    }
    html.dark .dashboard-shell .activity-user {
        color: #e5edf7;
    }
    html.dark .dashboard-shell .activity-details {
        color: #94a3b8;
    }
    @media (max-width: 768px) {
        .dashboard-shell .profile-name {
            font-size: 0.95rem;
        }
        .dashboard-shell .page-title-standard {
            font-size: 1.125rem;
        }
        .dashboard-shell .page-subtitle-standard,
        .dashboard-shell .profile-pill,
        .dashboard-shell .card-meta,
        .dashboard-shell .table-head {
            font-size: 8px;
        }
        .dashboard-shell .card-title,
        .dashboard-shell .section-title,
        .dashboard-shell .section-subtitle,
        .dashboard-shell .table-body,
        .dashboard-shell .table-model {
            font-size: 9px;
        }
    }
</style>
@endpush

@section('content')
@php
    $hasStatusData = collect($originalValues ?? [])->filter(fn ($value) => (int) $value > 0)->isNotEmpty();
    $profileCompletionFields = [
        'Full Name' => Auth::guard('wt')->user()->full_name,
        'ID No.' => Auth::guard('wt')->user()->staff_id,
        'Department' => Auth::guard('wt')->user()->department,
        'Position' => Auth::guard('wt')->user()->position,
    ];
    $missingProfileFields = collect($profileCompletionFields)
        ->filter(fn ($value) => blank($value))
        ->keys()
        ->values();
@endphp
<div class="dashboard-shell">
<!-- Admin Profile Section -->
<div class="mb-6 bg-gradient-to-r from-[#1F2937] to-[#334155] rounded-2xl p-4 md:p-5 text-white shadow-xl relative overflow-hidden dark:border dark:border-slate-700/50">
    <div class="absolute right-0 top-0 w-48 h-48 bg-white/5 rounded-full -mr-16 -mt-16 blur-2xl"></div>
    <div class="relative flex flex-col md:flex-row items-start md:items-center gap-4 md:gap-6">
        <div class="w-14 h-14 md:w-16 md:h-16 rounded-xl bg-gradient-to-br from-[#0EA5E9] to-[#075985] flex items-center justify-center text-xl md:text-2xl font-black shadow-lg border border-white/10">
            {{ strtoupper(substr(Auth::guard('wt')->user()->full_name ?? Auth::guard('wt')->user()->username, 0, 1)) }}
        </div>
        <div class="text-left flex-1 w-full">
            <div class="profile-name mb-0.5 !text-white">{{ Auth::guard('wt')->user()->full_name ?: strtoupper(Auth::guard('wt')->user()->username) }}</div>
            <div class="flex flex-wrap justify-start gap-2 mt-1.5">
                <div class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-full border border-white/10">
                    <i class="fas fa-id-card text-[#38bdf8] text-[10px]"></i>
                    <span class="profile-pill !text-white">{{ Auth::guard('wt')->user()->staff_id ?: '-' }}</span>
                </div>
                <div class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-full border border-white/10">
                    <i class="fas fa-building text-[#38bdf8] text-[10px]"></i>
                    <span class="profile-pill !text-white">{{ Auth::guard('wt')->user()->department ?: (Auth::guard('wt')->user()->wt_role === 'admin_it' ? 'ADMINISTRATOR' : 'GENERAL') }}</span>
                </div>
                <div class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-full border border-white/10">
                    <i class="fas fa-user-tie text-[#38bdf8] text-[10px]"></i>
                    <span class="profile-pill !text-white">{{ Auth::guard('wt')->user()->position ?: (Auth::guard('wt')->user()->wt_role === 'admin_it' ? 'ICT' : 'ADMINISTRATOR') }}</span>
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-2 w-full md:w-auto">
            <button onclick="window.location.href='{{ route('wt.admin.profile') }}'" class="profile-btn bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition border border-white/10 flex items-center justify-center gap-2 w-full md:w-auto">
                <i class="fas fa-edit"></i> Profile
            </button>
        </div>
    </div>
</div>

@if($missingProfileFields->isNotEmpty())
<div class="mb-5 rounded-2xl border border-amber-200/80 bg-amber-50 px-4 py-3 shadow-sm dark:border-amber-800/70 dark:bg-amber-900/20">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                <i class="fas fa-exclamation-triangle text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.14em] text-amber-800 dark:text-amber-300">Profile Reminder</p>
                <p class="mt-1 text-[11px] font-semibold leading-5 text-amber-700 dark:text-amber-200">
                    Your profile is not complete yet. Please update:
                    {{ $missingProfileFields->implode(', ') }}.
                </p>
            </div>
        </div>
        <button onclick="window.location.href='{{ route('wt.admin.profile') }}'" class="shrink-0 rounded-xl border border-amber-300 bg-white px-4 py-2 text-[9px] font-black uppercase tracking-[0.14em] text-amber-700 transition hover:bg-amber-100 dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-200 dark:hover:bg-amber-900/40">
            Complete Profile
        </button>
    </div>
</div>
@endif

<div class="grid grid-cols-1 gap-4 xl:grid-cols-[minmax(0,2fr)_minmax(320px,0.85fr)]">
    <div class="bg-white dark:bg-slate-800/50 p-6 rounded-2xl shadow-sm border border-stone-100 dark:border-slate-700/50 transition-colors duration-300">
        <div class="flex flex-col gap-3 mb-6 border-b border-stone-50 dark:border-slate-700/50 pb-3 lg:flex-row lg:items-center lg:justify-between">
            <h4 class="card-title text-stone-800 dark:text-slate-200">Walkie Talkie by Status</h4>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end">
                <div class="total-wt-badge inline-flex items-center gap-2 rounded-lg border px-3 py-2">
                    <i class="fas fa-walkie-talkie text-[11px]"></i>
                    <span class="card-meta">Total Walkie Talkie</span>
                    <span class="total-wt-value text-sm font-black leading-none">{{ number_format($totalWalkie ?? 0) }}</span>
                </div>
            </div>
        </div>
        @if($hasStatusData)
        <div class="w-full h-[300px] md:h-[350px]">
            <canvas id="statusChart"></canvas>
        </div>
        @else
        <div class="flex h-[120px] items-center justify-center rounded-lg border border-dashed border-slate-200 bg-slate-50 text-[10px] font-black uppercase tracking-[0.14em] text-slate-400 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-500">
            No status data found.
        </div>
        @endif
    </div>

    <div class="bg-white dark:bg-slate-800/50 p-6 rounded-2xl shadow-sm border border-stone-100 dark:border-slate-700/50 transition-colors duration-300">
        <div class="mb-5 border-b border-stone-50 pb-3 dark:border-slate-700/50">
            <h4 class="card-title text-stone-800 dark:text-slate-200">Recent Activities by User</h4>
            <p class="mt-1 text-[10px] font-bold text-slate-400">Latest user actions recorded in the system.</p>
        </div>

        @if(($recentActivities ?? collect())->isNotEmpty())
            <div class="activity-list">
                @foreach($recentActivities as $activity)
                    @php
                        $activityUser = $activity->user;
                        $displayUser = $activityUser
                            ? ($activityUser->full_name ?: $activityUser->username)
                            : ($activity->username ?: 'System User');
                    @endphp
                    <div class="activity-item">
                        <span class="activity-icon">
                            <i class="fas fa-user-clock"></i>
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="activity-user">{{ $displayUser }}</div>
                            <div class="activity-details">{{ $activity->event_details ?: str_replace('_', ' ', $activity->event_action ?: 'Activity recorded') }}</div>
                            <div class="activity-time">
                                {{ $activity->created_at ? \Carbon\Carbon::parse($activity->created_at)->format('d M Y H:i') : '-' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex h-[120px] items-center justify-center rounded-lg border border-dashed border-slate-200 bg-slate-50 text-[10px] font-black uppercase tracking-[0.14em] text-slate-400 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-500">
                No recent activities found.
            </div>
        @endif
    </div>
</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const statusChartCanvas = document.getElementById('statusChart');
    const statusLabels = {!! json_encode($originalLabels ?? []) !!};
    const statusValues = {!! json_encode($originalValues ?? []) !!};
    const statusColors = {!! json_encode($originalColors ?? []) !!};

    const isMobile = window.innerWidth < 768;

    if (statusChartCanvas) {
        const ctx = statusChartCanvas.getContext('2d');

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusValues,
                    backgroundColor: statusColors,
                    hoverOffset: 20,
                    borderWidth: 2,
                    borderColor: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: 6
                },
                plugins: {
                    legend: {
                        position: isMobile ? 'bottom' : 'right',
                        labels: {
                            font: { size: isMobile ? 9 : 10, family: 'DM Sans', weight: '700' },
                            color: document.documentElement.classList.contains('dark') ? '#94a3b8' : '#64748b',
                            usePointStyle: true,
                            padding: isMobile ? 10 : 12,
                            boxWidth: isMobile ? 9 : 11
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1F2937',
                        padding: 12,
                        titleFont: { size: 13, family: 'DM Sans', weight: '700' },
                        bodyFont: { size: 12, family: 'DM Sans' },
                        cornerRadius: 12,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((sum, value) => sum + Number(value), 0);
                                const value = Number(context.parsed);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';

                                return `${context.label}: ${value} unit (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    $(document).ready(function() {
        // DataTable initialization removed
    });
</script>
@endpush
