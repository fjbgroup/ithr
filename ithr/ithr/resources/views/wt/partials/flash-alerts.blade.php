@if(session('success') || session('error') || session('info'))
@php
    $flashType = session('success') ? 'success' : (session('error') ? 'error' : 'info');
    $flashMessage = session($flashType);
    $flashClasses = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-800/60 dark:bg-emerald-950/30 dark:text-emerald-200',
        'error' => 'border-red-200 bg-red-50 text-red-800 dark:border-red-800/60 dark:bg-red-950/30 dark:text-red-200',
        'info' => 'border-sky-200 bg-sky-50 text-sky-800 dark:border-sky-800/60 dark:bg-sky-950/30 dark:text-sky-200',
    ][$flashType];
    $flashIcons = [
        'success' => 'fa-circle-check',
        'error' => 'fa-circle-exclamation',
        'info' => 'fa-circle-info',
    ][$flashType];
@endphp
<div class="mb-4 rounded-xl border px-4 py-3 text-[11px] font-black uppercase tracking-[0.08em] shadow-sm {{ $flashClasses }}">
    <div class="flex items-center gap-2">
        <i class="fas {{ $flashIcons }}"></i>
        <span>{{ $flashMessage }}</span>
    </div>
</div>
@endif

