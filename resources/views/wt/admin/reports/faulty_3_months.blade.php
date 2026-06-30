@extends('wt.layouts.admin')

@section('title', 'Faulty Monthly Report')

@section('content')
@php
    $statusMeta = function ($status) {
        $status = strtoupper((string) $status);

        return match (true) {
            str_contains($status, 'DONE') || str_contains($status, 'FIXED') => ['bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-300', 'ring' => 'ring-emerald-400/20'],
            str_contains($status, 'REPAIR') || str_contains($status, 'FAULTY') || str_contains($status, 'B.E.R') => ['bg' => 'bg-amber-500/10', 'text' => 'text-amber-200', 'ring' => 'ring-amber-300/20'],
            str_contains($status, 'PENDING') || str_contains($status, 'WAITING') => ['bg' => 'bg-sky-500/10', 'text' => 'text-sky-200', 'ring' => 'ring-sky-300/20'],
            default => ['bg' => 'bg-slate-500/10', 'text' => 'text-slate-200', 'ring' => 'ring-slate-300/20'],
        };
    };

    $kpis = [
        ['label' => 'Total Reports', 'value' => $summary['total'], 'hint' => 'Monthly report', 'icon' => 'fa-clipboard-list'],
        ['label' => 'Pending Review', 'value' => $summary['pending'], 'hint' => 'Waiting action', 'icon' => 'fa-hourglass-half'],
        ['label' => 'Active Faulty', 'value' => $summary['active'], 'hint' => 'Repair or faulty status', 'icon' => 'fa-screwdriver-wrench'],
        ['label' => 'Resolved', 'value' => $summary['done'], 'hint' => 'Fixed or done', 'icon' => 'fa-circle-check'],
    ];
@endphp

<div class="faulty-report-page">
    <div class="faulty-report-header">
        <div>
            <div class="faulty-report-eyebrow">Dashboard / Faulty Report</div>
            <h3 class="faulty-report-title">Faulty Monthly Report</h3>
            <p class="faulty-report-subtitle">
                User faulty records for {{ $selectedPeriodLabel }} ({{ $startDate->format('d M Y') }} to {{ $endDate->format('d M Y') }}).
            </p>
        </div>
        <div class="faulty-header-actions">
            <form method="GET" action="{{ route('wt.admin.reports.faulty3Months') }}" class="faulty-period-filter">
                <label>
                    <span>Month</span>
                    <select name="month" aria-label="Select report month">
                        @foreach($months as $month)
                            <option value="{{ $month['value'] }}" @selected($selectedMonth === $month['value'])>
                                {{ $month['label'] }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>Year</span>
                    <select name="year" aria-label="Select report year">
                        @foreach($years as $year)
                            <option value="{{ $year }}" @selected($selectedYear === $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </label>
                <button type="submit">
                    <i class="fa-solid fa-filter"></i>
                    View
                </button>
            </form>
            <div id="faultyReportExportActions" class="admin-table-export-actions"></div>
        </div>
    </div>

    <div class="faulty-kpi-grid">
        @foreach($kpis as $kpi)
        <div class="faulty-kpi-card">
            <div class="faulty-kpi-icon"><i class="fa-solid {{ $kpi['icon'] }}"></i></div>
            <div>
                <p>{{ $kpi['label'] }}</p>
                <strong>{{ $kpi['value'] }}</strong>
                <span>{{ $kpi['hint'] }}</span>
            </div>
        </div>
        @endforeach
    </div>

    <div class="faulty-report-grid">
        <section class="faulty-panel">
            <div class="faulty-panel-head">
                <h4>Monthly Movement</h4>
                <span>Grouped by report date</span>
            </div>
            <div class="faulty-chart-box">
                <canvas id="faultyMonthlyChart"></canvas>
            </div>
        </section>

        <section class="faulty-panel">
            <div class="faulty-panel-head">
                <h4>Status Breakdown</h4>
                <span>Current repair state</span>
            </div>
            <div class="faulty-chart-box">
                <canvas id="faultyStatusChart"></canvas>
            </div>
        </section>
    </div>

    <section class="faulty-table-panel">
        <div class="faulty-panel-head">
            <h4>Faulty Details</h4>
            <span>{{ $records->count() }} record{{ $records->count() === 1 ? '' : 's' }}</span>
        </div>

        @if($records->isEmpty())
        <div class="faulty-empty-state">
            No faulty report found for {{ $selectedPeriodLabel }}.
        </div>
        @endif

        <div class="overflow-x-auto">
            <table id="faultyReportTable" class="display nowrap w-full text-left">
                <thead>
                    <tr>
                        <th>Ref</th>
                        <th>Radio / Serial</th>
                        <th>Reporter</th>
                        <th>Department</th>
                        <th>Issue</th>
                        <th>Status</th>
                        <th>Report Date</th>
                        <th>Repair Date</th>
                        <th>Finish Date</th>
                        <th>Spare Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $record)
                    @php($meta = $statusMeta($record->status_label))
                    <tr>
                        <td class="font-black text-slate-100">#{{ $record->maintenance_id }}</td>
                        <td>
                            <div class="faulty-main-cell">
                                <strong>{{ $record->radio_id ?: '-' }}</strong>
                                <span>{{ $record->serial_number ?: $record->model ?: 'No serial' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="faulty-main-cell">
                                <strong>{{ $record->reporter_name ?: $record->current_ownership ?: '-' }}</strong>
                                <span>{{ $record->reporter_staff_id ?: $record->phone_no ?: 'No staff ID' }}</span>
                            </div>
                        </td>
                        <td>{{ $record->department_name ?: $record->sector ?: '-' }}</td>
                        <td class="max-w-[280px] whitespace-normal">
                            {{ \Illuminate\Support\Str::limit($record->issue_description ?: $record->issue ?: $record->problem_possible ?: '-', 120) }}
                        </td>
                        <td>
                            <span class="faulty-status-pill {{ $meta['bg'] }} {{ $meta['text'] }} {{ $meta['ring'] }}">
                                {{ $record->status_label }}
                            </span>
                        </td>
                        <td data-order="{{ optional($record->report_date)->timestamp ?? 0 }}">{{ $record->report_date_label }}</td>
                        <td>{{ $record->repair_date ? \Carbon\Carbon::parse($record->repair_date)->format('d M Y') : '-' }}</td>
                        <td>{{ $record->finish_date ? \Carbon\Carbon::parse($record->finish_date)->format('d M Y') : '-' }}</td>
                        <td>
                            @if($record->temporarySpareWalkie)
                                {{ $record->temporarySpareWalkie->radio_id ?: $record->temporarySpareWalkie->serial_number }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    .faulty-report-page {
        color: var(--text);
    }

    .faulty-report-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .faulty-report-eyebrow {
        color: var(--muted);
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        margin-bottom: 0.35rem;
    }

    .faulty-report-title {
        color: var(--text);
        font-size: 1.35rem;
        font-weight: 900;
        letter-spacing: 0;
        line-height: 1.15;
    }

    .faulty-report-subtitle {
        color: var(--muted);
        font-size: 0.78rem;
        font-weight: 700;
        margin-top: 0.25rem;
    }

    .faulty-header-actions {
        display: flex;
        align-items: flex-start;
        justify-content: flex-end;
        gap: 0.65rem;
        flex-wrap: wrap;
    }

    .faulty-period-filter {
        display: flex;
        align-items: flex-end;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .faulty-period-filter label {
        display: grid;
        gap: 0.25rem;
    }

    .faulty-period-filter span {
        color: var(--muted);
        font-size: 0.58rem;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .faulty-period-filter select {
        min-width: 7.5rem;
        height: 2.35rem;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--form-input-bg);
        color: var(--form-input-color);
        font-size: 0.76rem;
        font-weight: 800;
        padding: 0 0.75rem;
        outline: none;
    }

    .faulty-period-filter select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(var(--accent-rgb), 0.16);
    }

    .faulty-period-filter button {
        height: 2.35rem;
        display: inline-flex;
        align-items: center;
        gap: 0.42rem;
        border-radius: 8px;
        border: 1px solid rgba(124, 197, 223, 0.25);
        background: #1680ad;
        color: #ffffff;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 0 0.9rem;
    }

    .faulty-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.75rem;
        margin-bottom: 0.9rem;
    }

    .faulty-kpi-card,
    .faulty-panel,
    .faulty-table-panel {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 8px;
        box-shadow: var(--shadow);
    }

    .faulty-kpi-card {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 0.95rem;
    }

    .faulty-kpi-icon {
        width: 2.35rem;
        height: 2.35rem;
        border-radius: 8px;
        display: grid;
        place-items: center;
        color: var(--accent);
        background: rgba(var(--accent-rgb), 0.16);
        border: 1px solid rgba(var(--accent-rgb), 0.18);
    }

    .faulty-kpi-card p,
    .faulty-panel-head span {
        color: var(--muted);
        font-size: 0.62rem;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .faulty-kpi-card strong {
        display: block;
        color: var(--text);
        font-size: 1.45rem;
        font-weight: 900;
        line-height: 1.1;
        margin-top: 0.2rem;
    }

    .faulty-kpi-card span {
        color: var(--muted);
        font-size: 0.72rem;
        font-weight: 700;
    }

    .faulty-report-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(280px, 0.85fr);
        gap: 0.9rem;
        margin-bottom: 0.9rem;
    }

    .faulty-panel,
    .faulty-table-panel {
        padding: 1rem;
    }

    .faulty-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.85rem;
    }

    .faulty-panel-head h4 {
        color: var(--text);
        font-size: 0.9rem;
        font-weight: 900;
        letter-spacing: 0;
    }

    .faulty-chart-box {
        height: 260px;
        min-height: 260px;
    }

    .faulty-empty-state {
        margin-bottom: 0.8rem;
        padding: 0.9rem;
        border: 1px dashed var(--border);
        border-radius: 8px;
        color: var(--muted);
        font-size: 0.78rem;
        font-weight: 800;
        text-align: center;
    }

    #faultyReportTable thead th {
        background: var(--table-head-bg);
        color: var(--table-head-color);
        border-bottom: 1px solid var(--border);
        font-size: 0.62rem;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 0.8rem;
    }

    #faultyReportTable tbody td {
        color: var(--text);
        border-bottom: 1px solid var(--border);
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.8rem;
        vertical-align: top;
    }

    .faulty-main-cell strong {
        display: block;
        color: var(--text);
        font-size: 0.8rem;
        font-weight: 900;
    }

    .faulty-main-cell span {
        display: block;
        color: var(--muted);
        font-size: 0.67rem;
        font-weight: 800;
        margin-top: 0.12rem;
    }

    .faulty-status-pill {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 0.32rem 0.55rem;
        font-size: 0.62rem;
        font-weight: 900;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        white-space: nowrap;
        ring-width: 1px;
    }

    @media (max-width: 1024px) {
        .faulty-kpi-grid,
        .faulty-report-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .faulty-report-header,
        .faulty-panel-head {
            flex-direction: column;
            align-items: flex-start;
        }

        .faulty-header-actions,
        .faulty-period-filter,
        .faulty-period-filter label,
        .faulty-period-filter select,
        .faulty-period-filter button {
            width: 100%;
        }

        .faulty-kpi-grid,
        .faulty-report-grid {
            grid-template-columns: 1fr;
        }

        .faulty-chart-box {
            height: 230px;
            min-height: 230px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        const faultyReportTable = $('#faultyReportTable').DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
            scrollX: true,
            order: [[6, 'desc']],
            language: { search: "", searchPlaceholder: "Search faulty report..." },
            dom: 'Blfrtip',
            buttons: getAdminTableExportButtons('Faulty Monthly Report')
        });

        mountAdminTableFooter(faultyReportTable);
        mountAdminTableExports(faultyReportTable, '#faultyReportExportActions');
    });

    const monthlyRows = @json($monthlyBreakdown->values());
    const statusRows = @json($statusBreakdown->map(fn ($value, $label) => ['label' => $label, 'value' => $value])->values());
    const gridColor = 'rgba(148, 163, 184, 0.16)';
    const labelColor = '#9fb0c9';

    new Chart(document.getElementById('faultyMonthlyChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: monthlyRows.map(row => row.label),
            datasets: [
                {
                    label: 'Total',
                    data: monthlyRows.map(row => row.total),
                    backgroundColor: '#2b91a8',
                    borderRadius: 6
                },
                {
                    label: 'Active',
                    data: monthlyRows.map(row => row.active),
                    backgroundColor: '#d6a04d',
                    borderRadius: 6
                },
                {
                    label: 'Resolved',
                    data: monthlyRows.map(row => row.done),
                    backgroundColor: '#4bb37d',
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: labelColor, boxWidth: 10, font: { weight: 'bold' } } }
            },
            scales: {
                x: { ticks: { color: labelColor, font: { weight: 'bold' } }, grid: { display: false } },
                y: { beginAtZero: true, ticks: { color: labelColor, precision: 0 }, grid: { color: gridColor } }
            }
        }
    });

    new Chart(document.getElementById('faultyStatusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: statusRows.map(row => row.label),
            datasets: [{
                data: statusRows.map(row => row.value),
                backgroundColor: ['#2b91a8', '#d6a04d', '#4bb37d', '#b85b5b', '#7e86c7', '#8fa3bf'],
                borderColor: '#111b2c',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: labelColor, boxWidth: 10, font: { weight: 'bold' } } }
            },
            cutout: '68%'
        }
    });
</script>
@endpush

