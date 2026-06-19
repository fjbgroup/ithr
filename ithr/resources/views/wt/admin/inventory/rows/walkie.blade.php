@php
    $searchParts = [];
    foreach ($columns as $column) {
        $key = $column['key'] ?? null;
        if ($key && $key !== 'actions') {
            $searchParts[] = (string) data_get($record, $key, '');
        }
    }
    $rowStatus = strtoupper((string) data_get($record, 'status', ''));
@endphp

<tr
    data-status="{{ $rowStatus }}"
    data-search="{{ strtoupper(trim(implode(' ', $searchParts))) }}"
>
    @foreach($columns as $column)
        @php
            $key = $column['key'] ?? null;
            $value = $key ? data_get($record, $key) : null;
            $cellClass = $column['cellClass'] ?? '';
        @endphp

        <td class="h-[38px] truncate border border-slate-200 px-4 text-[12px] font-bold text-slate-800 dark:border-slate-700 dark:text-slate-200 {{ $cellClass }}">
            @if(($column['type'] ?? null) === 'boolean')
                {{ (int) $value === 1 ? 'YES' : 'NO' }}
            @elseif(($column['type'] ?? null) === 'badge')
                <span class="inline-flex rounded border border-slate-300 px-2 py-1 text-[10px] font-black uppercase dark:border-slate-600">
                    {{ filled($value) ? $value : '-' }}
                </span>
            @else
                {{ filled($value) ? $value : '-' }}
            @endif
        </td>
    @endforeach
</tr>

