@extends('wt.layouts.admin')

@section('title', 'Report Summary')

@section('content')
<div class="page-header-block flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h3 class="page-title-standard">Report Summary</h3>
        <p class="page-subtitle-standard">Walkie Talkie Distribution by Status</p>
    </div>
    <div id="reportExportActions" class="admin-table-export-actions"></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
    <!-- Chart Section -->
    <div class="bg-white p-8 rounded-[32px] shadow-sm border border-stone-100 flex flex-col items-center justify-center">
        <h4 class="text-stone-800 font-bold mb-8 w-full border-b border-stone-50 pb-4 text-xs uppercase tracking-widest text-center">Status Distribution</h4>
        <div class="w-full max-w-[280px]">
            <canvas id="summaryChart"></canvas>
        </div>
    </div>

    <!-- Table Section next to it, spanning 2 cols -->
    <div class="lg:col-span-2 bg-white rounded-[32px] shadow-sm border border-stone-100 overflow-hidden p-6">
        <h4 class="text-stone-800 font-bold text-xs uppercase tracking-widest mb-6">Inventory Summary Details</h4>
        <div class="overflow-x-auto">
            <table id="reportTable" class="w-full text-left display nowrap">
                <thead class="bg-stone-50 text-stone-400 text-[10px] uppercase font-black tracking-[0.15em]">
                    <tr>
                        <th class="px-4 py-3 rounded-tl-lg">Radio ID</th>
                        <th class="px-4 py-3">Model</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Ownership</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50">
                    @foreach($walkies as $w)
                    <tr class="hover:bg-stone-50 transition text-sm">
                        <td class="px-4 py-3 font-bold text-stone-800">{{ $w->radio_id }}</td>
                        <td class="px-4 py-3 flex flex-col">
                            <span class="font-bold text-stone-700 uppercase">{{ $w->model }}</span>
                            <span class="text-[9px] text-stone-400 font-mono">{{ $w->serial_number }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $st = strtolower($w->status);
                                $bg = 'bg-stone-100'; $text = 'text-stone-500';
                                if(str_contains($st, 'in use') || str_contains($st, 'inuse')) { $bg = 'bg-blue-50'; $text = 'text-blue-600'; }
                                elseif(str_contains($st, 'unused')) { $bg = 'bg-gray-100'; $text = 'text-gray-500'; }
                                elseif(str_contains($st, 'faulty') || str_contains($st, 'ber')) { $bg = 'bg-red-50'; $text = 'text-red-600'; }
                                elseif(str_contains($st, 'repair')) { $bg = 'bg-orange-50'; $text = 'text-orange-600'; }
                                elseif(str_contains($st, 'spare')) { $bg = 'bg-emerald-50'; $text = 'text-emerald-600'; }
                            @endphp
                            <span class="px-2 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter {{ $bg }} {{ $text }}">
                                {{ $w->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-stone-600">{{ $w->ownership ?: '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const reportTable = $('#reportTable').DataTable({
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'All']],
            scrollX: true,
            language: { search: "", searchPlaceholder: "Search report..." },
            dom: 'Blfrtip',
            buttons: getAdminTableExportButtons('Report Summary')
        });
        mountAdminTableFooter(reportTable);
        mountAdminTableExports(reportTable, '#reportExportActions');
    });

    const ctx = document.getElementById('summaryChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($originalLabels) !!},
            datasets: [{
                label: 'Total Units',
                data: {!! json_encode($originalValues) !!},
                backgroundColor: ['#4cc9f0', '#aacc00', '#f9c74f', '#f94144', '#9d4edd', '#ff00ff', '#43aa8b'],
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });
</script>
@endpush

