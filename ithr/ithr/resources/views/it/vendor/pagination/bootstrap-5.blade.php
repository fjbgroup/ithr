@if ($paginator->hasPages())
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;font-family:'Inter',sans-serif">

  {{-- Showing X–Y of Z items --}}
  <span style="font-size:12px;color:var(--muted)">
    Showing <strong style="color:var(--text)">{{ number_format($paginator->firstItem()) }}</strong>–<strong style="color:var(--text)">{{ number_format($paginator->lastItem()) }}</strong>
    of <strong style="color:var(--text)">{{ number_format($paginator->total()) }}</strong> items
  </span>

  {{-- Navigation --}}
  <div style="display:flex;align-items:center;gap:3px">

    {{-- Previous --}}
    @if ($paginator->onFirstPage())
      <span style="display:inline-flex;align-items:center;gap:3px;padding:3px 10px;font-size:12px;font-weight:500;color:var(--muted);background:var(--surface);border:1px solid var(--border);border-radius:6px;opacity:.45;cursor:not-allowed;white-space:nowrap;user-select:none">
        &larr; Previous
      </span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" style="display:inline-flex;align-items:center;gap:3px;padding:3px 10px;font-size:12px;font-weight:500;color:var(--text);background:var(--surface);border:1px solid var(--border);border-radius:6px;text-decoration:none;white-space:nowrap">
        &larr; Previous
      </a>
    @endif

    {{-- Page numbers --}}
    @foreach ($elements as $element)
      @if (is_string($element))
        <span style="display:inline-flex;align-items:center;justify-content:center;min-width:28px;height:28px;font-size:12px;color:var(--muted);padding:0 3px">{{ $element }}</span>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span style="display:inline-flex;align-items:center;justify-content:center;min-width:28px;height:28px;font-size:12px;font-weight:600;color:#fff;background:#1e2d40;border:1px solid #1e2d40;border-radius:6px;cursor:default">{{ $page }}</span>
          @else
            <a href="{{ $url }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:28px;height:28px;font-size:12px;font-weight:500;color:var(--text);background:var(--surface);border:1px solid var(--border);border-radius:6px;text-decoration:none">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" style="display:inline-flex;align-items:center;gap:3px;padding:3px 10px;font-size:12px;font-weight:500;color:var(--text);background:var(--surface);border:1px solid var(--border);border-radius:6px;text-decoration:none;white-space:nowrap">
        Next &rarr;
      </a>
    @else
      <span style="display:inline-flex;align-items:center;gap:3px;padding:3px 10px;font-size:12px;font-weight:500;color:var(--muted);background:var(--surface);border:1px solid var(--border);border-radius:6px;opacity:.45;cursor:not-allowed;white-space:nowrap;user-select:none">
        Next &rarr;
      </span>
    @endif

  </div>
</div>
@endif

