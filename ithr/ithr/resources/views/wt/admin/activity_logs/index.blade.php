@extends('wt.layouts.admin')

@section('title', 'Activity Logs')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<style>
    .activity-table-shell {
        background: #243047;
        border-color: rgba(148, 163, 184, 0.18);
    }
    .activity-table-shell table.dataTable,
    .activity-table-shell .dataTables_wrapper {
        width: 100% !important;
        color: #dbe7ff;
    }
    .activity-table-shell table.dataTable {
        table-layout: auto;
    }
    .activity-table-shell table.dataTable thead th {
        white-space: nowrap;
        color: #a9bddf !important;
        background: #25324a !important;
        border-bottom-color: rgba(148, 163, 184, 0.12) !important;
    }
    .activity-table-shell .activity-details-cell {
        min-width: 320px;
        max-width: 520px;
        white-space: normal;
        word-break: break-word;
        line-height: 1.5;
        color: #bfd0ec !important;
    }
    .activity-table-shell .dataTables_wrapper .dataTables_length label,
    .activity-table-shell .dataTables_wrapper .dataTables_filter label,
    .activity-table-shell .dataTables_wrapper .dataTables_info,
    .activity-table-shell .dataTables_wrapper .dataTables_paginate {
        color: #dbe7ff !important;
    }
    .activity-table-shell .dataTables_wrapper .dataTables_length select,
    .activity-table-shell .dataTables_wrapper .dataTables_filter input {
        color: #f8fbff !important;
        background: #182238 !important;
        border: 1px solid rgba(96, 165, 250, 0.18) !important;
    }
    .activity-table-shell .dataTables_wrapper table.dataTable tbody tr {
        background: #2c3951 !important;
    }
    .activity-table-shell .dataTables_wrapper table.dataTable tbody tr:nth-child(even) {
        background: #26334a !important;
    }
    .activity-table-shell .dataTables_wrapper table.dataTable tbody tr:hover {
        background: #32425d !important;
    }
    .activity-table-shell .dataTables_wrapper table.dataTable tbody td {
        color: #e6efff !important;
        border-bottom-color: rgba(148, 163, 184, 0.08) !important;
    }
    .activity-table-shell .dataTables_wrapper table.dataTable tbody td:first-child .font-bold {
        color: #f6d39a !important;
    }
    .activity-table-shell .dataTables_wrapper table.dataTable tbody td:first-child .font-mono {
        color: #9fb4d7 !important;
    }
    .activity-table-shell .dataTables_wrapper .paginate_button {
        color: #dbe7ff !important;
    }
    .activity-table-shell .dataTables_wrapper .paginate_button.current,
    .activity-table-shell .dataTables_wrapper .paginate_button.current:hover {
        color: #ffffff !important;
        background: #35507b !important;
        border-color: transparent !important;
    }
    .activity-table-shell .dataTables_wrapper .paginate_button:hover {
        background: #2d4264 !important;
        border-color: transparent !important;
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
                    <td class="px-4 py-4 font-bold text-[#A67B5B]">
                        {{ $log->username }}
                    </td>
                    <td class="px-4 py-4">
                        <span class="px-3 py-1 bg-stone-100 text-stone-600 rounded-full text-[9px] font-black uppercase tracking-tighter">
                            {{ str_replace('_', ' ', $log->event_type) }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-center">
                        @php
                            $action = strtolower($log->event_action);
                            $bg = 'bg-stone-100'; $text = 'text-stone-500';
                            if($action == 'insert' || $action == 'add' || $action == 'login') { $bg = 'bg-emerald-50'; $text = 'text-emerald-600'; }
                            elseif($action == 'update' || $action == 'edit') { $bg = 'bg-blue-50'; $text = 'text-blue-600'; }
                            elseif($action == 'delete' || $action == 'remove' || $action == 'logout') { $bg = 'bg-red-50'; $text = 'text-red-600'; }
                        @endphp
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter {{ $bg }} {{ $text }}">
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

