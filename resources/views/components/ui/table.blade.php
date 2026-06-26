@props([
    'id' => 'uiTable',
    'columns' => [],
    'emptyText' => 'No records found.',
    'compact' => true,
])

@once
<style>
  .ui-table-shell {
    width: 100%;
    overflow: hidden;
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 8px;
    background: var(--surface, #fff);
  }
  .ui-table-scroll {
    width: 100%;
    overflow-x: auto;
  }
  @media (min-width: 1024px) {
    .ui-table-scroll { overflow-x: hidden; }
  }
  .ui-table {
    width: 100%;
    table-layout: fixed;
    border-collapse: collapse;
    font-size: 12px;
    color: var(--text, #1e293b);
  }
  .ui-table th,
  .ui-table td {
    border-bottom: 1px solid var(--border, #e2e8f0);
    vertical-align: middle;
  }
  .ui-table th {
    height: 34px;
    padding: 7px 10px;
    background: var(--table-head-bg, #f8fafc);
    color: var(--table-head-color, var(--muted, #64748b));
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .08em;
    line-height: 1.1;
    text-transform: uppercase;
    white-space: nowrap;
  }
  .ui-table td {
    height: 36px;
    padding: 7px 10px;
    font-weight: 600;
    line-height: 1.25;
  }
  .ui-table tr:hover td {
    background: var(--table-hover, #f0f9ff);
  }
  .ui-table .ui-col-number,
  .ui-table .ui-col-date,
  .ui-table .ui-col-status,
  .ui-table .ui-col-actions {
    text-align: center;
  }
  .ui-table .ui-col-actions {
    width: 112px;
    white-space: nowrap;
  }
  .ui-truncate {
    display: block;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .ui-table-empty {
    height: 96px !important;
    text-align: center !important;
    color: var(--muted, #64748b);
    font-weight: 700;
  }
  html.dark .ui-table-shell,
  html[data-theme="dark"] .ui-table-shell {
    border-color: var(--border, #334155);
    background: var(--surface, #1e293b);
  }
</style>
@endonce

<div {{ $attributes->merge(['class' => 'ui-table-shell']) }}>
    <div class="ui-table-scroll">
        <table id="{{ $id }}" class="ui-table">
            @if(count($columns))
                <colgroup>
                    @foreach($columns as $column)
                        <col style="width: {{ $column['width'] ?? 'auto' }}">
                    @endforeach
                </colgroup>
            @endif
            <thead>
                <tr>
                    @foreach($columns as $column)
                        @php($alignClass = isset($column['type']) ? 'ui-col-' . $column['type'] : '')
                        <th class="{{ $alignClass }}">{{ $column['label'] ?? '' }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
