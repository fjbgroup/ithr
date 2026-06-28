@extends('it.layouts.app')

@section('title', 'Saved Drafts')
@section('page_title', 'Saved Drafts')

@section('content')

{{-- Page header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px">
  <div>
    <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;text-transform:uppercase;margin-bottom:6px">
      Request Form &rsaquo; IT Request Form &rsaquo; <span style="color:var(--accent)">Saved Drafts</span>
    </div>
    <h4 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:800;color:var(--text);margin:0 0 4px">Saved Drafts</h4>
    <p style="font-size:13px;color:var(--muted);margin:0">Your unsubmitted IT request forms. Resume editing or delete drafts you no longer need.</p>
  </div>
  <a href="{{ route('it.it-request-form') }}"
    style="display:inline-flex;align-items:center;gap:7px;font-size:13px;font-weight:700;background:var(--accent);color:#fff;border-radius:10px;padding:9px 18px;text-decoration:none;transition:opacity .15s;flex-shrink:0"
    onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
    <i class="bi bi-plus-circle"></i> New Request
  </a>
</div>

{{-- Flash messages --}}
@if(session('success'))
<div style="display:flex;align-items:center;gap:10px;background:rgba(22,163,74,.1);border:1.5px solid rgba(22,163,74,.3);color:#16a34a;border-radius:10px;padding:12px 16px;font-size:13.5px;font-weight:600;margin-bottom:18px">
  <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="display:flex;align-items:center;gap:10px;background:rgba(220,38,38,.1);border:1.5px solid rgba(220,38,38,.3);color:#dc2626;border-radius:10px;padding:12px 16px;font-size:13.5px;font-weight:600;margin-bottom:18px">
  <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
</div>
@endif

@if($drafts->isEmpty())

{{-- Empty state --}}
<div style="text-align:center;padding:64px 20px;background:var(--surface);border:1.5px dashed var(--border);border-radius:16px">
  <i class="bi bi-floppy" style="font-size:48px;color:var(--muted);display:block;margin-bottom:14px"></i>
  <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:6px">No saved drafts</div>
  <div style="font-size:13px;color:var(--muted);margin-bottom:20px">When you save an IT request form as a draft, it will appear here.</div>
  <a href="{{ route('it.it-request-form') }}"
    style="display:inline-flex;align-items:center;gap:7px;font-size:13px;font-weight:700;background:var(--accent);color:#fff;border-radius:10px;padding:9px 18px;text-decoration:none;transition:opacity .15s"
    onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
    <i class="bi bi-plus-circle"></i> Start a New Request
  </a>
</div>

@else

{{-- Draft count summary --}}
<div style="font-size:13px;color:var(--muted);margin-bottom:14px">
  {{ $drafts->total() }} saved {{ \Illuminate\Support\Str::plural('draft', $drafts->total()) }}
</div>

{{-- Draft rows --}}
<div style="display:flex;flex-direction:column;gap:10px">
  @foreach($drafts as $draft)
  @php
    $typeMap = [
      'hardware' => ['label'=>'Hardware','color'=>'#3b82f6','bg'=>'rgba(59,130,246,.1)','icon'=>'bi-laptop'],
      'software' => ['label'=>'Software','color'=>'#8b5cf6','bg'=>'rgba(139,92,246,.1)','icon'=>'bi-code-slash'],
      'system'   => ['label'=>'System',  'color'=>'#10b981','bg'=>'rgba(16,185,129,.1)','icon'=>'bi-hdd-network'],
      'service'  => ['label'=>'Service', 'color'=>'#0284c7','bg'=>'rgba(2,132,199,.1)', 'icon'=>'bi-wifi'],
    ];
    $t = $typeMap[$draft->request_type] ?? ['label'=>ucfirst($draft->request_type),'color'=>'#64748b','bg'=>'rgba(100,116,139,.1)','icon'=>'bi-question-circle'];
  @endphp
  <div style="background:var(--surface);border:1.5px solid var(--border);border-radius:12px;padding:14px 18px;display:flex;align-items:center;gap:14px;flex-wrap:wrap;transition:border-color .15s"
    onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--border)'">

    {{-- Type badge --}}
    <span style="display:inline-flex;align-items:center;gap:6px;background:{{ $t['bg'] }};color:{{ $t['color'] }};border-radius:20px;padding:4px 12px;font-size:12px;font-weight:700;flex-shrink:0">
      <i class="bi {{ $t['icon'] }}"></i>{{ $t['label'] }}
    </span>

    {{-- Subject & last saved --}}
    <div style="flex:1;min-width:0">
      <div style="font-size:13.5px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
        {{ $draft->subject ?? '— Untitled —' }}
      </div>
      <div style="font-size:11.5px;color:var(--muted);margin-top:2px">
        Last saved {{ $draft->updated_at->format('d M Y, H:i') }}
      </div>
    </div>

    {{-- Resume button --}}
    <a href="{{ route('it.it-request-form.edit', $draft->id) }}"
      style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;color:var(--accent);border:1.5px solid var(--accent);border-radius:20px;padding:4px 12px;text-decoration:none;flex-shrink:0;transition:all .15s;background:transparent"
      onmouseover="this.style.background='rgba(2,132,199,.08)'"
      onmouseout="this.style.background='transparent'">
      <i class="bi bi-pencil-square"></i> Resume
    </a>

    {{-- Delete button --}}
    <form method="POST" action="{{ route('it.it-request-form.draft.destroy', $draft->id) }}"
      onsubmit="return confirm('Delete this draft? This cannot be undone.')">
      @csrf
      @method('DELETE')
      <button type="submit"
        style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;color:#dc2626;border:1.5px solid rgba(220,38,38,.35);border-radius:20px;padding:4px 12px;background:transparent;cursor:pointer;font-family:inherit;transition:all .15s"
        onmouseover="this.style.background='rgba(220,38,38,.07)'"
        onmouseout="this.style.background='transparent'">
        <i class="bi bi-trash3"></i> Delete
      </button>
    </form>

  </div>
  @endforeach
</div>

{{-- Pagination --}}
@if($drafts->hasPages())
<div style="margin-top:20px">
  {{ $drafts->links() }}
</div>
@endif

@endif

@endsection

