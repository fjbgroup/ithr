@props([
    'id',
    'title' => 'View Details',
    'subtitle' => null,
    'fields' => [],
    'columns' => 3,
])

@php
    $gridClass = $columns === 2 ? 'ui-detail-grid--2' : 'ui-detail-grid--3';
@endphp

@once
<style>
  .ui-modal {
    position: fixed;
    inset: 0;
    z-index: 9000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 18px;
    background: rgba(15, 23, 42, .52);
    backdrop-filter: blur(2px);
  }
  .ui-modal.is-open { display: flex; }
  .ui-modal-panel {
    width: min(980px, 100%);
    max-height: min(82vh, 760px);
    overflow: hidden;
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 10px;
    background: var(--surface, #fff);
    box-shadow: 0 24px 70px rgba(15, 23, 42, .26);
    color: var(--text, #1e293b);
  }
  .ui-modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 18px;
    border-bottom: 1px solid var(--border, #e2e8f0);
  }
  .ui-modal-title {
    margin: 0;
    font-size: 16px;
    font-weight: 900;
    line-height: 1.2;
  }
  .ui-modal-subtitle {
    margin: 3px 0 0;
    color: var(--muted, #64748b);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
  }
  .ui-modal-close {
    width: 30px;
    height: 30px;
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 7px;
    background: transparent;
    color: var(--muted, #64748b);
    cursor: pointer;
    font-size: 18px;
    line-height: 1;
  }
  .ui-modal-body {
    max-height: calc(min(82vh, 760px) - 64px);
    overflow-y: auto;
    padding: 16px 18px 18px;
  }
  .ui-detail-grid {
    display: grid;
    gap: 10px;
  }
  .ui-detail-grid--2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .ui-detail-grid--3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
  .ui-detail-item {
    min-width: 0;
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 8px;
    background: var(--body-bg, #f8fafc);
    padding: 9px 10px;
  }
  .ui-detail-label {
    margin: 0 0 4px;
    color: var(--muted, #64748b);
    font-size: 9px;
    font-weight: 900;
    letter-spacing: .1em;
    line-height: 1.2;
    text-transform: uppercase;
  }
  .ui-detail-value {
    margin: 0;
    overflow-wrap: anywhere;
    color: var(--text, #1e293b);
    font-size: 12px;
    font-weight: 800;
    line-height: 1.35;
  }
  @media (max-width: 760px) {
    .ui-detail-grid--2,
    .ui-detail-grid--3 { grid-template-columns: 1fr; }
    .ui-modal { padding: 10px; }
  }
</style>
<script>
  document.addEventListener('click', function (event) {
    const opener = event.target.closest('[data-ui-modal-target]');
    if (opener) {
      const modal = document.getElementById(opener.getAttribute('data-ui-modal-target'));
      if (modal) {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
      }
    }

    const closer = event.target.closest('[data-ui-modal-close]');
    if (closer || event.target.classList.contains('ui-modal')) {
      const modal = event.target.closest('.ui-modal') || document.getElementById(closer?.getAttribute('data-ui-modal-close'));
      if (modal) {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
      }
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key !== 'Escape') return;
    document.querySelectorAll('.ui-modal.is-open').forEach(function (modal) {
      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
    });
  });
</script>
@endonce

<div id="{{ $id }}" class="ui-modal" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="ui-modal-panel">
        <div class="ui-modal-header">
            <div>
                <h2 class="ui-modal-title">{{ $title }}</h2>
                @if($subtitle)
                    <p class="ui-modal-subtitle">{{ $subtitle }}</p>
                @endif
            </div>
            <button type="button" class="ui-modal-close" data-ui-modal-close="{{ $id }}" aria-label="Close">&times;</button>
        </div>

        <div class="ui-modal-body">
            @if(count($fields))
                <dl class="ui-detail-grid {{ $gridClass }}">
                    @foreach($fields as $label => $value)
                        <div class="ui-detail-item">
                            <dt class="ui-detail-label">{{ is_int($label) ? ($value['label'] ?? '') : $label }}</dt>
                            <dd class="ui-detail-value">
                                @php($fieldValue = is_array($value) ? ($value['value'] ?? null) : $value)
                                {{ filled($fieldValue) ? $fieldValue : '-' }}
                            </dd>
                        </div>
                    @endforeach
                </dl>
            @else
                {{ $slot }}
            @endif
        </div>
    </div>
</div>
