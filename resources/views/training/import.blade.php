@extends('layouts.app')

@section('title', 'Import Training Records')

@section('content')
<div style="padding:2rem 2rem 3rem;background:#f1f5f9;min-height:100vh;">

    {{-- Page header --}}
    <div style="margin-bottom:1.75rem;">
        <a href="{{ route('training.index') }}"
           style="display:inline-flex;align-items:center;gap:.4rem;font-size:.8rem;font-weight:700;color:#64748b;text-decoration:none;margin-bottom:.75rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
            Back to Training Records
        </a>
        <h1 style="font-size:1.6rem;font-weight:800;color:#142b47;margin:0 0 .25rem;">Import Training Records</h1>
        <p style="color:#64748b;font-size:.875rem;margin:0;">Upload a CSV or Excel file (.csv, .xlsx, .xls) to bulk-add training data.</p>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:.85rem 1.1rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:.6rem;color:#15803d;font-size:.84rem;font-weight:600;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:.85rem 1.1rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:.6rem;color:#dc2626;font-size:.84rem;font-weight:600;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Main two-column import cards --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">

        {{-- INTERNAL --}}
        <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;border-top:4px solid #22c55e;box-shadow:0 1px 3px rgba(0,0,0,.06);overflow:hidden;">
            <div style="padding:1.5rem;">
                {{-- Card header --}}
                <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
                    <div style="width:42px;height:42px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <div>
                        <div style="font-size:1rem;font-weight:800;color:#142b47;">Internal Training</div>
                        <div style="font-size:.75rem;color:#94a3b8;margin-top:.1rem;">FJB Doc 213 — Internal format</div>
                    </div>
                </div>

                {{-- Format info --}}
                <div style="background:#f8fafc;border-radius:8px;padding:.85rem 1rem;margin-bottom:1.1rem;font-size:.78rem;color:#64748b;line-height:1.7;">
                    Expected columns:
                    <div style="display:flex;flex-wrap:wrap;gap:.35rem;margin-top:.5rem;">
                        @foreach(['DATE TRAINING','VENUE','NAME OF COURSE','ID STAFF'] as $col)
                        <span style="background:#e0f2fe;color:#0369a1;padding:.15rem .5rem;border-radius:4px;font-family:monospace;font-size:.72rem;font-weight:700;">{{ $col }}</span>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('training.import-template', ['type' => 'internal']) }}"
                   style="display:inline-flex;align-items:center;gap:.4rem;font-size:.75rem;font-weight:700;color:#22c55e;text-decoration:none;margin-bottom:1.1rem;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download Internal Template
                </a>

                <form action="{{ route('training.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="csv_type" value="internal">

                    <label for="int_file"
                           style="display:flex;flex-direction:column;align-items:center;gap:.6rem;padding:1.4rem 1rem;border:2px dashed #bbf7d0;border-radius:10px;cursor:pointer;background:#f0fdf4;margin-bottom:.9rem;transition:.15s;"
                           onmouseover="this.style.borderColor='#22c55e'" onmouseout="this.style.borderColor='#bbf7d0'">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="1.8"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <span id="int-label" style="font-size:.8rem;font-weight:700;color:#15803d;">Click to choose file</span>
                        <span style="font-size:.7rem;color:#94a3b8;">CSV, XLSX or XLS</span>
                    </label>
                    <input id="int_file" type="file" name="csv_file" accept=".csv,.xlsx,.xls" required style="display:none;"
                           onchange="document.getElementById('int-label').textContent=this.files[0]?.name||'Click to choose file'">

                    <button type="submit"
                            style="width:100%;padding:.65rem;background:#22c55e;border:none;border-radius:8px;font-size:.85rem;font-weight:700;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.4rem;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Import Internal
                    </button>
                </form>
            </div>
        </div>

        {{-- EXTERNAL --}}
        <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;border-top:4px solid #f97316;box-shadow:0 1px 3px rgba(0,0,0,.06);overflow:hidden;">
            <div style="padding:1.5rem;">
                {{-- Card header --}}
                <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
                    <div style="width:42px;height:42px;border-radius:10px;background:#fff7ed;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    </div>
                    <div>
                        <div style="font-size:1rem;font-weight:800;color:#142b47;">External Training</div>
                        <div style="font-size:.75rem;color:#94a3b8;margin-top:.1rem;">FJB Doc 213 — External format</div>
                    </div>
                </div>

                {{-- Format info --}}
                <div style="background:#f8fafc;border-radius:8px;padding:.85rem 1rem;margin-bottom:1.1rem;font-size:.78rem;color:#64748b;line-height:1.7;">
                    Expected columns:
                    <div style="display:flex;flex-wrap:wrap;gap:.35rem;margin-top:.5rem;">
                        @foreach(['REF NO','NAME OF COURSE','VENUE','TRAINING DATE','ID PETUGAS'] as $col)
                        <span style="background:#fff7ed;color:#c2410c;padding:.15rem .5rem;border-radius:4px;font-family:monospace;font-size:.72rem;font-weight:700;">{{ $col }}</span>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('training.import-template', ['type' => 'external']) }}"
                   style="display:inline-flex;align-items:center;gap:.4rem;font-size:.75rem;font-weight:700;color:#f97316;text-decoration:none;margin-bottom:1.1rem;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download External Template
                </a>

                <form action="{{ route('training.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="csv_type" value="external">

                    <label for="ext_file"
                           style="display:flex;flex-direction:column;align-items:center;gap:.6rem;padding:1.4rem 1rem;border:2px dashed #fed7aa;border-radius:10px;cursor:pointer;background:#fff7ed;margin-bottom:.9rem;transition:.15s;"
                           onmouseover="this.style.borderColor='#f97316'" onmouseout="this.style.borderColor='#fed7aa'">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="1.8"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <span id="ext-label" style="font-size:.8rem;font-weight:700;color:#c2410c;">Click to choose file</span>
                        <span style="font-size:.7rem;color:#94a3b8;">CSV, XLSX or XLS</span>
                    </label>
                    <input id="ext_file" type="file" name="csv_file" accept=".csv,.xlsx,.xls" required style="display:none;"
                           onchange="document.getElementById('ext-label').textContent=this.files[0]?.name||'Click to choose file'">

                    <button type="submit"
                            style="width:100%;padding:.65rem;background:#f97316;border:none;border-radius:8px;font-size:.85rem;font-weight:700;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.4rem;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Import External
                    </button>
                </form>
            </div>
        </div>

    </div>

    {{-- Generic / Simple format — secondary card --}}
    <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <div style="padding:1.25rem 1.5rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <div style="width:38px;height:38px;border-radius:9px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                    </div>
                    <div>
                        <div style="font-size:.9rem;font-weight:700;color:#142b47;">Generic / Simple Format</div>
                        <div style="display:flex;flex-wrap:wrap;gap:.3rem;margin-top:.4rem;">
                            @foreach(['staff_no','course_title','training_type','start_date','venue','status'] as $col)
                            <span style="background:#f1f5f9;color:#475569;padding:.1rem .45rem;border-radius:4px;font-family:monospace;font-size:.68rem;font-weight:700;">{{ $col }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
                    <a href="{{ route('training.import-template', ['type' => 'simple']) }}"
                       style="display:inline-flex;align-items:center;gap:.35rem;font-size:.75rem;font-weight:700;color:#6366f1;text-decoration:none;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download Template
                    </a>
                    <form action="{{ route('training.import') }}" method="POST" enctype="multipart/form-data" style="display:flex;align-items:center;gap:.5rem;">
                        @csrf
                        <input type="hidden" name="csv_type" value="simple">
                        <label for="simple_file"
                               style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem .9rem;border:1.5px solid #e2e8f0;border-radius:8px;cursor:pointer;font-size:.78rem;font-weight:700;color:#475569;background:#f8fafc;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <span id="simple-label">Choose file</span>
                        </label>
                        <input id="simple_file" type="file" name="csv_file" accept=".csv,.xlsx,.xls" required style="display:none;"
                               onchange="document.getElementById('simple-label').textContent=this.files[0]?.name||'Choose file'">
                        <button type="submit"
                                style="padding:.45rem 1rem;background:#142b47;border:none;border-radius:8px;font-size:.78rem;font-weight:700;color:#fff;cursor:pointer;">
                            Import
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Danger Zone --}}
    <div style="margin-top:1.25rem;background:#fff;border-radius:14px;border:1px solid #fecaca;box-shadow:0 1px 3px rgba(0,0,0,.05);">
        <div style="padding:1rem 1.5rem;border-bottom:1px solid #fee2e2;display:flex;align-items:center;gap:.5rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <span style="font-size:.8rem;font-weight:800;color:#dc2626;text-transform:uppercase;letter-spacing:.04em;">Danger Zone</span>
        </div>
        <div style="padding:1rem 1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
            <p style="font-size:.8rem;color:#64748b;margin:0;">
                Permanently delete all training records for a specific type. This cannot be undone.
            </p>
            <div style="display:flex;gap:.6rem;flex-wrap:wrap;">
                {{-- Delete Internal --}}
                <form method="POST" action="{{ route('training.delete-by-type') }}"
                      onsubmit="return confirm('Delete ALL Internal training records? This cannot be undone.')">
                    @csrf
                    <input type="hidden" name="type" value="Internal">
                    <button type="submit"
                            style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1rem;background:#fff;border:1.5px solid #22c55e;border-radius:8px;font-size:.78rem;font-weight:700;color:#15803d;cursor:pointer;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        Delete All Internal
                    </button>
                </form>
                {{-- Delete External --}}
                <form method="POST" action="{{ route('training.delete-by-type') }}"
                      onsubmit="return confirm('Delete ALL External training records? This cannot be undone.')">
                    @csrf
                    <input type="hidden" name="type" value="External">
                    <button type="submit"
                            style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1rem;background:#fff;border:1.5px solid #f97316;border-radius:8px;font-size:.78rem;font-weight:700;color:#c2410c;cursor:pointer;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        Delete All External
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
