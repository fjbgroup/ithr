@if ($paginator->hasPages())
<nav style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;font-family:'DM Sans',sans-serif">

  {{-- Result count --}}
  <div style="font-size:12.5px;color:var(--muted);white-space:nowrap">
    Showing
    <strong style="color:var(--text)">{{ $paginator->firstItem() }}</strong>
    –
    <strong style="color:var(--text)">{{ $paginator->lastItem() }}</strong>
    of
    <strong style="color:var(--text)">{{ number_format($paginator->total()) }}</strong>
  </div>

  {{-- Page buttons --}}
  <div style="display:flex;align-items:center;gap:4px">

    {{-- Previous --}}
    @if ($paginator->onFirstPage())
    <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:1.5px solid var(--border);background:transparent;color:var(--muted);font-size:13px;opacity:.4;cursor:default">
      <i class="bi bi-chevron-left"></i>
    </span>
    @else
    <a href="{{ $paginator->previousPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:1.5px solid var(--border);background:var(--surface);color:var(--text);font-size:13px;text-decoration:none;transition:all .15s"
       onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text)'">
      <i class="bi bi-chevron-left"></i>
    </a>
    @endif

    {{-- Page numbers --}}
    @foreach ($elements as $element)
      {{-- Dots --}}
      @if (is_string($element))
      <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;font-size:13px;color:var(--muted)">
        {{ $element }}
      </span>
      @endif

      {{-- Pages --}}
      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
          <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:1.5px solid var(--accent);background:var(--accent);color:#fff;font-size:13px;font-weight:700;cursor:default">
            {{ $page }}
          </span>
          @else
          <a href="{{ $url }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:1.5px solid var(--border);background:var(--surface);color:var(--text);font-size:13px;font-weight:500;text-decoration:none;transition:all .15s"
             onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text)'">
            {{ $page }}
          </a>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:1.5px solid var(--border);background:var(--surface);color:var(--text);font-size:13px;text-decoration:none;transition:all .15s"
       onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text)'">
      <i class="bi bi-chevron-right"></i>
    </a>
    @else
    <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:1.5px solid var(--border);background:transparent;color:var(--muted);font-size:13px;opacity:.4;cursor:default">
      <i class="bi bi-chevron-right"></i>
    </span>
    @endif

  </div>
</nav>
@endif
