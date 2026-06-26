@props([
    'type' => 'view',
    'href' => null,
    'modal' => null,
    'action' => null,
    'method' => 'POST',
    'confirm' => null,
    'label' => null,
    'title' => null,
])

@php
    $label = $label ?? ucfirst($type);
    $title = $title ?? $label;
    $variantClass = 'ui-action-btn--' . $type;
    $icon = match ($type) {
        'edit' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"/><path d="m16.5 3.5 4 4L7 21H3v-4L16.5 3.5z"/></svg>',
        'delete' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v5"/><path d="M14 11v5"/></svg>',
        'download' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>',
        default => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>',
    };
@endphp

@once
<style>
  .ui-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    height: 28px;
    min-width: 28px;
    padding: 0 8px;
    border: 1px solid transparent;
    border-radius: 6px;
    background: transparent;
    font-size: 11px;
    font-weight: 800;
    line-height: 1;
    text-decoration: none;
    cursor: pointer;
    transition: background .14s ease, border-color .14s ease, color .14s ease;
  }
  .ui-action-btn svg {
    width: 13px;
    height: 13px;
    fill: none;
    stroke: currentColor;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 2;
    flex-shrink: 0;
  }
  .ui-action-btn--view { color: #0369a1; background: rgba(2,132,199,.08); border-color: rgba(2,132,199,.16); }
  .ui-action-btn--view:hover { background: rgba(2,132,199,.16); color: #075985; }
  .ui-action-btn--edit { color: #4f46e5; background: rgba(79,70,229,.08); border-color: rgba(79,70,229,.16); }
  .ui-action-btn--edit:hover { background: rgba(79,70,229,.16); color: #3730a3; }
  .ui-action-btn--delete { color: #dc2626; background: rgba(220,38,38,.08); border-color: rgba(220,38,38,.16); }
  .ui-action-btn--delete:hover { background: rgba(220,38,38,.16); color: #991b1b; }
  .ui-action-btn--download { color: #0f766e; background: rgba(15,118,110,.08); border-color: rgba(15,118,110,.16); }
  .ui-action-btn--download:hover { background: rgba(15,118,110,.16); color: #115e59; }
  html.dark .ui-action-btn--view,
  html[data-theme="dark"] .ui-action-btn--view { color: #7dd3fc; }
  html.dark .ui-action-btn--edit,
  html[data-theme="dark"] .ui-action-btn--edit { color: #a5b4fc; }
  html.dark .ui-action-btn--delete,
  html[data-theme="dark"] .ui-action-btn--delete { color: #fca5a5; }
</style>
@endonce

@if($action)
    <form method="POST" action="{{ $action }}" style="display:inline" @if($confirm) onsubmit="return confirm(@js($confirm))" @endif>
        @csrf
        @if(!in_array(strtoupper($method), ['GET', 'POST'], true))
            @method($method)
        @endif
        <button type="submit" class="ui-action-btn {{ $variantClass }}" title="{{ $title }}" aria-label="{{ $title }}">
            {!! $icon !!}<span>{{ $label }}</span>
        </button>
    </form>
@elseif($href)
    <a href="{{ $href }}" class="ui-action-btn {{ $variantClass }}" title="{{ $title }}" aria-label="{{ $title }}">
        {!! $icon !!}<span>{{ $label }}</span>
    </a>
@else
    <button
        type="button"
        class="ui-action-btn {{ $variantClass }}"
        title="{{ $title }}"
        aria-label="{{ $title }}"
        @if($modal) data-ui-modal-target="{{ $modal }}" @endif
    >
        {!! $icon !!}<span>{{ $label }}</span>
    </button>
@endif
