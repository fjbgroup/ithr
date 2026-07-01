@extends('wt.layouts.admin')

@section('title', 'Activity Logs')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<style>
    .activity-table-shell {
        background: var(--surface) !important;
        border-color: var(--border) !important;
        color: var(--text) !important;
    }
    .activity-table-shell table.dataTable,
    .activity-table-shell .dataTables_wrapper {
        width: 100% !important;
        color: var(--text) !important;
    }
    .activity-table-shell table.dataTable {
        table-layout: auto;
    }
    .activity-table-shell table.dataTable thead th {
        white-space: nowrap;
        color: var(--table-head-color) !important;
        background: var(--table-head-bg) !important;
        border-color: var(--border) !important;
    }
    .activity-table-shell .activity-details-cell {
        min-width: 320px;
        max-width: 520px;
        white-space: normal;
        word-break: break-word;
        line-height: 1.5;
        color: var(--text) !important;
    }
    .activity-table-shell .dataTables_wrapper .dataTables_length label,
    .activity-table-shell .dataTables_wrapper .dataTables_filter label,
    .activity-table-shell .dataTables_wrapper .dataTables_info,
    .activity-table-shell .dataTables_wrapper .dataTables_paginate {
        color: var(--muted) !important;
    }
    .activity-table-shell .dataTables_wrapper .dataTables_length select,
    .activity-table-shell .dataTables_wrapper .dataTables_filter input {
        color: var(--form-input-color) !important;
        background: var(--form-input-bg) !important;
        border: 1px solid var(--form-input-border) !important;
    }
    .activity-table-shell .dataTables_wrapper table.dataTable tbody tr {
        background: var(--row-surface) !important;
    }
    .activity-table-shell .dataTables_wrapper table.dataTable tbody tr:nth-child(even) {
        background: var(--row-alt) !important;
    }
    .activity-table-shell .dataTables_wrapper table.dataTable tbody tr:hover {
        background: var(--table-hover) !important;
    }
    .activity-table-shell .dataTables_wrapper table.dataTable tbody td {
        color: var(--text) !important;
        border-color: var(--border) !important;
        background: transparent !important;
    }
    .activity-table-shell .dataTables_wrapper table.dataTable tbody td:first-child .font-bold {
        color: var(--text) !important;
    }
    .activity-table-shell .dataTables_wrapper table.dataTable tbody td:first-child .font-mono {
        color: var(--muted) !important;
    }
    .activity-table-shell .activity-account-cell {
        color: var(--accent) !important;
    }
    .activity-table-shell .activity-module-badge {
        background: rgba(2, 132, 199, 0.10) !important;
        color: #0369a1 !important;
        border: 1px solid rgba(2, 132, 199, 0.18);
    }
    html.dark .activity-table-shell .activity-module-badge {
        background: rgba(56, 189, 248, 0.14) !important;
        color: #7dd3fc !important;
        border-color: rgba(125, 211, 252, 0.22);
    }
    .activity-action-badge {
        border: 1px solid transparent;
    }
    .activity-action-neutral {
        background: var(--soft-surface) !important;
        color: var(--muted) !important;
        border-color: var(--border);
    }
    .activity-action-success {
        background: #dcfce7 !important;
        color: #166534 !important;
        border-color: #bbf7d0;
    }
    .activity-action-info {
        background: #dbeafe !important;
        color: #1d4ed8 !important;
        border-color: #bfdbfe;
    }
    .activity-action-danger {
        background: #fee2e2 !important;
        color: #991b1b !important;
        border-color: #fecaca;
    }
    html.dark .activity-action-success {
        background: rgba(34, 197, 94, 0.18) !important;
        color: #86efac !important;
        border-color: rgba(134, 239, 172, 0.26);
    }
    html.dark .activity-action-info {
        background: rgba(59, 130, 246, 0.20) !important;
        color: #93c5fd !important;
        border-color: rgba(147, 197, 253, 0.28);
    }
    html.dark .activity-action-danger {
        background: rgba(239, 68, 68, 0.18) !important;
        color: #fca5a5 !important;
        border-color: rgba(252, 165, 165, 0.28);
    }
    .activity-table-shell .dataTables_wrapper .paginate_button {
        color: var(--muted) !important;
    }
    .activity-table-shell .dataTables_wrapper .paginate_button.current,
    .activity-table-shell .dataTables_wrapper .paginate_button.current:hover {
        color: #ffffff !important;
        background: var(--accent) !important;
        border-color: transparent !important;
    }
    .activity-table-shell .dataTables_wrapper .paginate_button:hover {
        background: var(--table-hover) !important;
        border-color: var(--border) !important;
        color: var(--text) !important;
    }
</style>
@endpush

@section('content')
<div class="page-header-block flex flex-col md:flex-row md:items-end md:justify-between gap-3">
    <div>
        <h3 class="page-title-standard">System Activity Logs</h3>
        <p class="page-subtitle-standard">
            Comprehensive audit trail of all user interactions and data modifications.
        </p>
    </div>

</div>

<div class="activity-table-shell bg-white rounded-[30px] shadow-sm border border-stone-100 overflow-hidden">
    <div class="p-6">
        <table id="activityTable" class="w-full text-left display">
            <thead class="bg-stone-50 text-stone-400 text-[10px] uppercase font-black tracking-[0.15em] border-b border-stone-100">
                <tr>
                    <th class="px-4 py-4 rounded-tl-xl">Timestamp</th>
                    <th class="px-4 py-4">Account</th>
                    <th class="px-4 py-4">Module</th>
                    <th class="px-4 py-4 text-center">Action</th>
                    <th class="px-4 py-4">Details</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50">
                @forelse($logs as $log)
                <tr class="hover:bg-[#FDFBF7] transition text-sm">
                    <td class="px-4 py-4">
                        <span class="font-bold text-stone-800">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y') }}</span><br>
                        <span class="text-[10px] text-stone-400 font-mono">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }}</span>
                    </td>
                    <td class="activity-account-cell px-4 py-4 font-bold">
                        {{ $log->username }}
                    </td>
                    <td class="px-4 py-4">
                        <span class="activity-module-badge px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter">
                            {{ str_replace('_', ' ', $log->event_type) }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-center">
                        @php
                            $action = strtolower($log->event_action);
                            $actionClass = 'activity-action-neutral';
                            if($action == 'insert' || $action == 'add' || $action == 'login') { $actionClass = 'activity-action-success'; }
                            elseif($action == 'update' || $action == 'edit') { $actionClass = 'activity-action-info'; }
                            elseif($action == 'delete' || $action == 'remove' || $action == 'logout') { $actionClass = 'activity-action-danger'; }
                        @endphp
                        <span class="activity-action-badge px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter {{ $actionClass }}">
                            {{ $log->event_action }}
                        </span>
                    </td>
                    <td class="activity-details-cell px-4 py-4 text-xs text-stone-600" title="{{ $log->event_details }}">
                        {{ $log->event_details }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-stone-500 text-sm">No recent activity found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script>
    $(document).ready(function() {
        const activityTable = $('#activityTable').DataTable({
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'All']],
            ordering: true,
            order: [[0, 'desc']], // Order by Timestamp descending by default
            dom: 'lfrtip',

            language: { search: "", searchPlaceholder: "Search logs..." },
            responsive: true,
            autoWidth: false
        });
        mountAdminTableFooter(activityTable);


    });
</script>
@endpush
