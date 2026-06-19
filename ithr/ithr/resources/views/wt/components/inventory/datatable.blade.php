@props([
    'pageTitle',
    'pageSubtitle' => null,
    'tableId' => 'inventoryDatatable',
    'searchId' => null,
    'statusId' => null,
    'resetId' => null,
    'columns' => [],
    'records' => collect(),
    'rowPartial' => null,
    'statusOptions' => [],
    'itemsPerPage' => 10,
    'emptyText' => 'NO ITEMS FOUND',
    'minWidth' => '1180px',
    'showStatusFilter' => true,
])

@php
    $records = $records instanceof \Illuminate\Support\Collection ? $records : collect($records);
    $columnCount = count($columns);
    $searchId = $searchId ?: "{$tableId}Search";
    $statusId = $statusId ?: "{$tableId}Status";
    $resetId = $resetId ?: "{$tableId}Reset";
    $safeTableId = \Illuminate\Support\Str::slug($tableId, '-');
@endphp

<section
    {{ $attributes->merge(['class' => 'inventory-datatable grid gap-3']) }}
    data-inventory-datatable="{{ $safeTableId }}"
>
    <div class="flex min-h-[72px] w-full items-center justify-between gap-4 rounded-lg border border-slate-300 border-l-[6px] border-l-[#c28a48] bg-white px-5 py-3 dark:border-slate-700 dark:border-l-[#f2c48d] dark:bg-slate-800">
        <div class="min-w-0">
            <h1 class="truncate text-[20px] font-black leading-tight tracking-normal text-slate-950 dark:text-slate-50">
                {{ $pageTitle }}
            </h1>
            @if($pageSubtitle)
                <p class="mt-1 truncate text-[10px] font-black uppercase leading-tight tracking-[0.24em] text-slate-500 dark:text-slate-300">
                    {{ $pageSubtitle }}
                </p>
            @endif
        </div>

        @isset($actions)
            <div class="flex shrink-0 items-center gap-2">
                {{ $actions }}
            </div>
        @endisset
    </div>

    <div class="grid min-h-[86px] grid-cols-[minmax(260px,1fr)_minmax(220px,30%)_92px] items-end gap-3 rounded-lg border border-slate-300 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900 max-[900px]:grid-cols-1">
        <div>
            <label for="{{ $searchId }}" class="mb-2 block text-[11px] font-extrabold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-300">Search</label>
            <input
                id="{{ $searchId }}"
                type="search"
                placeholder="Keywords"
                class="h-[38px] w-full rounded-md border border-slate-300 bg-white px-3 text-[13px] font-bold text-slate-950 outline-none focus:border-sky-400 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
            >
        </div>

        @if($showStatusFilter)
            <div>
                <label for="{{ $statusId }}" class="mb-2 block text-[11px] font-extrabold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-300">Status</label>
                <select
                    id="{{ $statusId }}"
                    class="h-[38px] w-full rounded-md border border-slate-300 bg-white px-3 text-[13px] font-bold text-slate-950 outline-none focus:border-sky-400 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                >
                    <option value="">All Status</option>
                    @foreach($statusOptions as $value => $label)
                        @php($optionValue = is_int($value) ? $label : $value)
                        <option value="{{ $optionValue }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @else
            <div class="hidden max-[900px]:block"></div>
        @endif

        <button
            id="{{ $resetId }}"
            type="button"
            class="h-[38px] rounded-md border border-slate-300 bg-white px-3 text-[12px] font-black text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
        >
            Reset
        </button>
    </div>

    <div class="flex min-h-[400px] flex-col overflow-hidden rounded-lg border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-900">
        <div class="min-h-0 flex-1 overflow-x-auto">
            <table id="{{ $tableId }}" class="w-full table-fixed border-collapse" style="min-width: {{ $minWidth }}">
                <colgroup>
                    @foreach($columns as $column)
                        <col style="width: {{ $column['width'] ?? floor(100 / max($columnCount, 1)) . '%' }}">
                    @endforeach
                </colgroup>
                <thead>
                    <tr>
                        @foreach($columns as $column)
                            <th class="h-[46px] border border-slate-200 bg-slate-50 px-4 text-left text-[13px] font-black uppercase tracking-[0.06em] text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                                {{ $column['label'] ?? strtoupper(str_replace('_', ' ', $column['key'] ?? '')) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        @if($rowPartial)
                            @include($rowPartial, [
                                'record' => $record,
                                'columns' => $columns,
                                'tableId' => $tableId,
                            ])
                        @else
                            {{ $slot }}
                        @endif
                    @empty
                        <tr data-empty-row="true">
                            <td colspan="{{ $columnCount }}" class="h-[300px] border border-slate-200 text-center text-[12px] font-black uppercase tracking-[0.14em] text-slate-400 dark:border-slate-700 dark:text-slate-500">
                                {{ $emptyText }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="h-[10px] shrink-0 border-t border-slate-200 bg-slate-200 dark:border-slate-700 dark:bg-slate-800">
            <div class="h-[10px] w-[39%] rounded-full bg-slate-500"></div>
        </div>

        <div class="flex min-h-[54px] shrink-0 items-center justify-between gap-4 border-t border-slate-200 bg-white px-4 py-2 dark:border-slate-700 dark:bg-slate-800 max-[700px]:flex-col max-[700px]:items-stretch">
            <div id="{{ $tableId }}Showing" class="text-[13px] font-black text-slate-950 dark:text-slate-100">
                Total: 0 items
            </div>
        </div>
    </div>
</section>

@once
    @push('scripts')
        <script>
            window.mountInventoryDatatable = function mountInventoryDatatable(config) {
                const table = document.getElementById(config.tableId);
                if (!table) return;

                const searchInput = document.getElementById(config.searchId);
                const statusSelect = document.getElementById(config.statusId);
                const resetButton = document.getElementById(config.resetId);
                const showing = document.getElementById(`${config.tableId}Showing`);
                const rows = Array.from(table.querySelectorAll('tbody tr:not([data-empty-row])'));
                const emptyRow = table.querySelector('tbody tr[data-empty-row]');
                const perPage = Number(config.itemsPerPage || 10);
                let currentPage = 1;
                let filteredRows = rows;

                function makeButton(label, disabled, handler, isNav = false, isActive = false) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.innerHTML = label;
                    button.disabled = disabled;
                    button.className = [
                        'inline-flex h-[34px] items-center justify-center rounded-md border border-slate-300 bg-white px-3 text-[13px] font-black text-slate-800 disabled:text-slate-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:disabled:text-slate-400',
                        isNav ? 'min-w-[92px]' : 'min-w-[42px]',
                        isActive ? 'border-sky-400 bg-sky-100 text-sky-900 dark:bg-sky-900 dark:text-white' : '',
                    ].join(' ');
                    button.addEventListener('click', handler);
                    return button;
                }

                function pages(totalPages) {
                    if (totalPages <= 6) return Array.from({ length: totalPages }, (_, index) => index + 1);
                    if (currentPage <= 3) return [1, 2, 3, 4, '...', totalPages];
                    if (currentPage >= totalPages - 2) return [1, '...', totalPages - 3, totalPages - 2, totalPages - 1, totalPages];
                    return [1, '...', currentPage - 1, currentPage, currentPage + 1, '...', totalPages];
                }

                function render() {
                    const totalItems = filteredRows.length;

                    rows.forEach((row) => row.classList.add('hidden'));
                    filteredRows.forEach((row) => row.classList.remove('hidden'));
                    if (emptyRow) emptyRow.classList.toggle('hidden', totalItems > 0);

                    if (showing) {
                        showing.textContent = `Total: ${totalItems} items`;
                    }
                }

                function applyFilters() {
                    const searchValue = (searchInput?.value || '').trim().toUpperCase();
                    const statusValue = (statusSelect?.value || '').trim().toUpperCase();

                    filteredRows = rows.filter((row) => {
                        const rowSearch = (row.dataset.search || row.textContent || '').toUpperCase();
                        const rowStatus = (row.dataset.status || '').toUpperCase();
                        return (!searchValue || rowSearch.includes(searchValue))
                            && (!statusValue || rowStatus === statusValue);
                    });

                    currentPage = 1;
                    render();
                }

                searchInput?.addEventListener('input', applyFilters);
                statusSelect?.addEventListener('change', applyFilters);
                resetButton?.addEventListener('click', () => {
                    if (searchInput) searchInput.value = '';
                    if (statusSelect) statusSelect.value = '';
                    applyFilters();
                });

                applyFilters();
            };
        </script>
    @endpush
@endonce

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.mountInventoryDatatable({
                tableId: @json($tableId),
                searchId: @json($searchId),
                statusId: @json($statusId),
                resetId: @json($resetId),
                itemsPerPage: @json($itemsPerPage),
            });
        });
    </script>
@endpush

