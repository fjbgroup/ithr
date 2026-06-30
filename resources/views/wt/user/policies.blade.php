@extends(request()->routeIs('wt.admin.*') ? 'wt.layouts.admin' : 'wt.layouts.user')

@section('title', 'Role Permissions Matrix')
@section('page_title', 'Role Permissions Matrix')

@section('content')

<style>
  .role-matrix-page {
    display: grid;
    gap: 18px;
  }

  .role-matrix-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 4px;
  }

  .role-matrix-title {
    margin: 0;
    color: var(--text);
    font-size: 22px;
    font-weight: 900;
    line-height: 1.2;
    letter-spacing: -0.01em;
  }

  .role-matrix-subtitle {
    margin: 8px 0 0;
    color: var(--muted);
    font-size: 13px;
    font-weight: 600;
    line-height: 1.55;
  }

  .role-matrix-legend {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 8px;
    min-width: 220px;
  }

  .legend-chip,
  .role-pill,
  .permission-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 900;
    line-height: 1;
    text-transform: uppercase;
    white-space: nowrap;
  }

  .legend-chip {
    min-height: 28px;
    padding: 0 10px;
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--muted);
  }

  .role-matrix-card,
  .policy-note-card {
    overflow: hidden;
    border: 1px solid var(--border);
    border-radius: 16px;
    background: var(--surface);
    box-shadow: var(--shadow);
  }

  .role-matrix-scroll {
    overflow-x: auto;
  }

  .role-matrix-table {
    width: 100%;
    min-width: 860px;
    border-collapse: collapse;
  }

  .role-matrix-table th,
  .role-matrix-table td {
    padding: 16px 18px;
    border-bottom: 1px solid var(--border);
    text-align: center;
    vertical-align: middle;
  }

  .role-matrix-table th:first-child,
  .role-matrix-table td:first-child {
    width: 38%;
    text-align: left;
  }

  .role-matrix-table thead th {
    background: var(--soft-surface);
    color: var(--muted);
    font-size: 11px;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }

  .role-matrix-table tbody tr:last-child td {
    border-bottom: 0;
  }

  .feature-name {
    color: var(--text);
    font-size: 12.5px;
    font-weight: 800;
    line-height: 1.35;
  }

  .role-pill {
    min-height: 28px;
    padding: 0 12px;
  }

  .role-pill-ict {
    background: #dbeafe;
    color: #1d4ed8;
  }

  .role-pill-executive {
    background: #fef3c7;
    color: #b45309;
  }

  .permission-badge {
    min-height: 30px;
    padding: 0 11px;
    border: 1px solid transparent;
  }

  .permission-full {
    border-color: rgba(34, 197, 94, 0.28);
    background: rgba(34, 197, 94, 0.10);
    color: #15803d;
  }

  .permission-partial {
    border-color: rgba(245, 158, 11, 0.30);
    background: rgba(245, 158, 11, 0.12);
    color: #b45309;
  }

  .permission-none {
    border-color: rgba(148, 163, 184, 0.30);
    background: rgba(148, 163, 184, 0.12);
    color: #64748b;
  }

  .policy-note-card {
    padding: 22px;
  }

  .policy-note-title {
    margin: 0 0 12px;
    color: var(--text);
    font-size: 14px;
    font-weight: 900;
    letter-spacing: 0.06em;
    text-transform: uppercase;
  }

  html.dark .role-pill-ict {
    background: rgba(59, 130, 246, 0.18);
    color: #bfdbfe;
  }

  html.dark .role-pill-executive {
    background: rgba(245, 158, 11, 0.16);
    color: #fde68a;
  }

  html.dark .permission-full {
    color: #bbf7d0;
  }

  html.dark .permission-partial {
    color: #fde68a;
  }

  html.dark .permission-none {
    color: #cbd5e1;
  }

  @media (max-width: 768px) {
    .role-matrix-header {
      flex-direction: column;
    }

    .role-matrix-legend {
      justify-content: flex-start;
      min-width: 0;
    }

    .role-matrix-title {
      font-size: 19px;
    }
  }
</style>

<div class="role-matrix-page">
  <div class="role-matrix-header">
    <div>
      <h1 class="role-matrix-title">Role Permissions Matrix</h1>
      <p class="role-matrix-subtitle">A detailed breakdown of WT System access for ICT and Executive users.</p>
    </div>
    <div class="role-matrix-legend" aria-label="Permission legend">
      <span class="legend-chip"><i class="fas fa-check"></i> Full / Manage</span>
      <span class="legend-chip"><i class="fas fa-eye"></i> View / Own</span>
      <span class="legend-chip"><i class="fas fa-minus"></i> No Access</span>
    </div>
  </div>

  @include('wt.partials.role-permissions-matrix')

  <div class="policy-note-card">
    <h2 class="policy-note-title">Walkie Talkie Usage Policies</h2>
    @include('wt.partials.walkie-policy-content')
  </div>
</div>

@endsection
