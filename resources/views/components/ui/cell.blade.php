@props([
    'type' => 'text',
    'title' => null,
])

@php
    $class = match ($type) {
        'number', 'date', 'status', 'actions' => 'ui-col-' . $type,
        default => '',
    };
@endphp

<td {{ $attributes->merge(['class' => $class]) }}>
    @if($type === 'actions')
        {{ $slot }}
    @else
        <span class="ui-truncate" title="{{ $title ?? trim(strip_tags((string) $slot)) }}">
            {{ $slot }}
        </span>
    @endif
</td>
