@props([
    'rows' => collect(),
    'title' => 'Special Use Walkie Talkies',
    'subtitle' => 'Manage Unit Maintenance, Repair Logs, and Faulty Equipment.',
    'tableId' => 'coreDatatable',
    'searchId' => null,
    'statusId' => null,
    'resetId' => null,
    'statusOptions' => [],
    'showActions' => false,
    'actionPartial' => null,
    'showFilters' => true,
])

@php
    $isPaginator = $rows instanceof \Illuminate\Contracts\Pagination\Paginator;
    $items = $isPaginator
        ? collect($rows->items())
        : ($rows instanceof \Illuminate\Support\Collection ? $rows : collect($rows));

    $searchId = $searchId ?: "{$tableId}Search";
    $statusId = $statusId ?: "{$tableId}Status";
    $resetId = $resetId ?: "{$tableId}Reset";

    $columns = $showActions && $actionPartial ? [
        ['key' => 'radio_id', 'label' => 'Radio ID', 'width' => '9%'],
        ['key' => 'status', 'label' => 'Status', 'width' => '9%'],
        ['key' => 'serial_no', 'label' => 'Serial No.', 'width' => '11%'],
        ['key' => 'model', 'label' => 'Model', 'width' => '10%'],
        ['key' => 'ownership_type', 'label' => 'Ownership Type', 'width' => '11%'],
        ['key' => 'current_ownership', 'label' => 'Current Ownership', 'width' => '12%'],
        ['key' => 'department', 'label' => 'Department', 'width' => '10%'],
        ['key' => 'received_date', 'label' => 'Received Date', 'width' => '10%'],
        ['key' => 'repair_date', 'label' => 'Repair Date', 'width' => '8%'],
        ['key' => '__actions', 'label' => 'Action', 'width' => '10%'],
    ] : [
        ['key' => 'radio_id', 'label' => 'Radio ID', 'width' => '10%'],
        ['key' => 'status', 'label' => 'Status', 'width' => '10%'],
        ['key' => 'serial_no', 'label' => 'Serial No.', 'width' => '12%'],
        ['key' => 'model', 'label' => 'Model', 'width' => '11%'],
        ['key' => 'ownership_type', 'label' => 'Ownership Type', 'width' => '12%'],
        ['key' => 'current_ownership', 'label' => 'Current Ownership', 'width' => '13%'],
        ['key' => 'department', 'label' => 'Department', 'width' => '11%'],
        ['key' => 'received_date', 'label' => 'Received Date', 'width' => '11%'],
        ['key' => 'repair_date', 'label' => 'Repair Date', 'width' => '10%'],
    ];

    $valueFor = function ($row, string $key) {
        $fallbackKey = match ($key) {
            'serial_no' => 'serial_number',
            'ownership_type' => 'current_ownership_type',
            'current_ownership' => 'ownership',
            default => $key,
        };

        return data_get($row, $key) ?? data_get($row, $fallbackKey);
    };

    $showingFrom = $isPaginator ? ($rows->firstItem() ?? 0) : ($items->isEmpty() ? 0 : 1);
    $showingTo = $isPaginator ? ($rows->lastItem() ?? 0) : $items->count();
    $showingTotal = $isPaginator ? $rows->total() : $items->count();
@endphp

<div {{ $attributes->merge(['class' => 'bg-slate-100 p-6 min-h-screen']) }}>
    <div class="mx-auto flex min-h-[700px] w-full max-w-7xl flex-col rounded-2xl bg-white p-6 shadow-lg dark:bg-slate-900">
        <div class="mb-6 flex items-start justify-between gap-4 border-b border-gray-100 pb-4 dark:border-slate-800">
            <div class="flex min-w-0 items-center gap-3">
                <div class="h-14 w-1 shrink-0 rounded-full bg-amber-600"></div>
                <div class="min-w-0">
                    <h1 class="truncate text-3xl font-extrabold text-slate-800 dark:text-slate-50">{{ $title }}</h1>
                    <p class="mt-1 truncate text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-300">{{ $subtitle }}</p>
                </div>
            </div>

            @isset($actions)
                <div class="flex shrink-0 items-center gap-2">
                    {{ $actions }}
                </div>
            @endisset
        </div>

        @if($showFilters)
            <div class="mb-6 flex gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 max-[800px]:flex-col">
                <div class="flex-1">
                    <label for="{{ $searchId }}" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-300">Search</label>
                    <input id="{{ $searchId }}" type="text" placeholder="Keywords" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-300 focus:ring-1 focus:ring-slate-300 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                </div>
                <div class="flex-1">
                    <label for="{{ $statusId }}" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-300">Status</label>
                    <select id="{{ $statusId }}" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-slate-800 focus:border-slate-300 focus:ring-1 focus:ring-slate-300 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                        <option value="">All Status</option>
                        @foreach($statusOptions as $value => $label)
                            @php($optionValue = is_int($value) ? $label : $value)
                            <option value="{{ $optionValue }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button id="{{ $resetId }}" type="button" class="rounded-lg border border-gray-200 bg-white px-6 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-gray-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                        Reset
                    </button>
                </div>
            </div>
        @endif

        <div class="flex flex-grow flex-col overflow-hidden rounded-t-xl border border-gray-100 bg-white shadow-inner dark:border-slate-800 dark:bg-slate-900">
            <table class="w-full table-fixed text-left text-sm">
                <colgroup>
                    @foreach($columns as $column)
                        <col style="width: {{ $column['width'] }}">
                    @endforeach
                </colgroup>
                <thead class="border-b border-gray-100 bg-slate-50 dark:border-slate-800 dark:bg-slate-900">
                    <tr class="h-12">
                        @foreach($columns as $column)
                            <th class="truncate px-4 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                {{ $column['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
            </table>

            <div class="w-full flex-grow overflow-y-auto bg-white dark:bg-slate-900" style="min-height: 450px;">
                <table id="{{ $tableId }}" class="w-full table-fixed text-left text-sm">
                    <colgroup>
                        @foreach($columns as $column)
                            <col style="width: {{ $column['width'] }}">
                        @endforeach
                    </colgroup>
                    <tbody>
                        @forelse($items as $row)
                            @php
                                $rowStatus = strtoupper((string) ($valueFor($row, 'status') ?? ''));
                                $rowSearch = strtoupper(trim(collect($columns)->map(fn ($column) => (string) $valueFor($row, $column['key']))->implode(' ')));
                            @endphp
                            <tr class="h-12 border-b border-gray-100 transition hover:bg-slate-50/50 dark:border-slate-800 dark:hover:bg-slate-800/60" data-core-row data-status="{{ $rowStatus }}" data-search="{{ $rowSearch }}">
                                @foreach($columns as $column)
                                    @php($value = $valueFor($row, $column['key']))
                                    <td class="truncate px-4 py-3 text-sm text-slate-800 dark:text-slate-200">
                                        @if($column['key'] === 'status')
                                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                                {{ filled($value) ? $value : '-' }}
                                            </span>
                                        @elseif($column['key'] === '__actions')
                                            <div class="flex items-center justify-center gap-1">
                                                @include($actionPartial, ['row' => $row])
                                            </div>
                                        @else
                                            {{ filled($value) ? $value : ($column['key'] === 'repair_date' ? '--' : '-') }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr class="h-12" data-core-empty-row>
                                <td colspan="{{ count($columns) }}" class="px-4 py-3 text-center italic text-slate-500">
                                    No data available in this view.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-auto flex items-center justify-between border-t border-gray-100 bg-white px-4 py-3 text-sm text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 max-[700px]:flex-col max-[700px]:items-stretch max-[700px]:gap-3">
                <div id="{{ $tableId }}Showing">
                    Showing {{ $showingFrom }} to {{ $showingTo }} of {{ $showingTotal }} items
                </div>
                <div id="{{ $tableId }}Pagination" class="flex items-center gap-2">
                    @if($isPaginator)
                        <a
                            href="{{ $rows->previousPageUrl() ?: '#' }}"
                            @class([
                                'flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-gray-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100',
                                'pointer-events-none opacity-50' => !$rows->previousPageUrl(),
                            ])
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            Previous
                        </a>
                        <a
                            href="{{ $rows->nextPageUrl() ?: '#' }}"
                            @class([
                                'flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-gray-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100',
                                'pointer-events-none opacity-50' => !$rows->nextPageUrl(),
                            ])
                        >
                            Next
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    @else
                        <button type="button" class="flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 opacity-50 shadow-sm dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" disabled>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            Previous
                        </button>
                        <button type="button" class="flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 opacity-50 shadow-sm dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" disabled>
                            Next
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            window.mountCoreDatatableFilters = function mountCoreDatatableFilters(config) {
                const table = document.getElementById(config.tableId);
                if (!table) return;

                const searchInput = document.getElementById(config.searchId);
                const statusSelect = document.getElementById(config.statusId);
                const resetButton = document.getElementById(config.resetId);
                const rows = Array.from(table.querySelectorAll('[data-core-row]'));
                const emptyRow = table.querySelector('[data-core-empty-row]');
                const showing = document.getElementById(`${config.tableId}Showing`);

                function applyFilters() {
                    const searchValue = (searchInput?.value || '').trim().toUpperCase();
                    const statusValue = (statusSelect?.value || '').trim().toUpperCase();
                    let visible = 0;

                    rows.forEach((row) => {
                        const matchesSearch = !searchValue || (row.dataset.search || '').includes(searchValue);
                        const matchesStatus = !statusValue || (row.dataset.status || '') === statusValue;
                        const show = matchesSearch && matchesStatus;
                        row.classList.toggle('hidden', !show);
                        if (show) visible += 1;
                    });

                    if (emptyRow) emptyRow.classList.toggle('hidden', visible > 0);
                    if (showing) showing.textContent = `Showing ${visible === 0 ? 0 : 1} to ${visible} of ${visible} items`;
                }

                searchInput?.addEventListener('input', applyFilters);
                statusSelect?.addEventListener('change', applyFilters);
                resetButton?.addEventListener('click', () => {
                    if (searchInput) searchInput.value = '';
                    if (statusSelect) statusSelect.value = '';
                    applyFilters();
                });
            };
        </script>
    @endpush
@endonce

@if(!$isPaginator && $showFilters)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.mountCoreDatatableFilters({
                    tableId: @json($tableId),
                    searchId: @json($searchId),
                    statusId: @json($statusId),
                    resetId: @json($resetId),
                });
            });
        </script>
    @endpush
@endif

