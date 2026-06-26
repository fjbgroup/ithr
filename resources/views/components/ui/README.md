# UI Table Standard

Use these anonymous Blade components for every data table across HR, IT, and WT modules.

## Compact Table

```blade
@php
    $columns = [
        ['label' => '#', 'width' => '5%', 'type' => 'number'],
        ['label' => 'Radio ID', 'width' => '12%'],
        ['label' => 'Owner', 'width' => '22%'],
        ['label' => 'Status', 'width' => '12%', 'type' => 'status'],
        ['label' => 'Date', 'width' => '14%', 'type' => 'date'],
        ['label' => 'Action', 'width' => '15%', 'type' => 'actions'],
    ];
@endphp

<x-ui.table id="inventoryTable" :columns="$columns">
    @forelse($records as $record)
        <tr>
            <x-ui.cell type="number">{{ $loop->iteration }}</x-ui.cell>
            <x-ui.cell>{{ $record->radio_id }}</x-ui.cell>
            <x-ui.cell :title="$record->ownership">{{ $record->ownership }}</x-ui.cell>
            <x-ui.cell type="status">{{ $record->status }}</x-ui.cell>
            <x-ui.cell type="date">{{ optional($record->created_at)->format('d/m/Y') }}</x-ui.cell>
            <x-ui.cell type="actions">
                <x-ui.action-group>
                    <x-ui.action-button type="view" modal="record-{{ $record->id }}-details" />
                    <x-ui.action-button type="edit" :href="route('example.edit', $record)" />
                    <x-ui.action-button
                        type="delete"
                        method="DELETE"
                        :action="route('example.destroy', $record)"
                        confirm="Delete this record?"
                    />
                </x-ui.action-group>
            </x-ui.cell>
        </tr>

        <x-ui.detail-modal
            id="record-{{ $record->id }}-details"
            title="Record Details"
            :subtitle="$record->radio_id"
            :fields="$record->getAttributes()"
        />
    @empty
        <tr>
            <td colspan="{{ count($columns) }}" class="ui-table-empty">No records found.</td>
        </tr>
    @endforelse
</x-ui.table>
```

## Rules

- Always define column widths.
- Use `<x-ui.cell>` so long text truncates with a tooltip.
- Action column should use `<x-ui.action-group>`.
- View details must pass all available fields, preferably `$model->getAttributes()` plus relationship summaries where needed.
