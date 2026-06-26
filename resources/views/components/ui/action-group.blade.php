@once
<style>
  .ui-action-group {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    width: 100%;
  }
</style>
@endonce

<div {{ $attributes->merge(['class' => 'ui-action-group']) }}>
    {{ $slot }}
</div>
