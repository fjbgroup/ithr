@extends('layouts.app')

@section('title', 'Audit Log')

@section('content')
<div class="page-header">
    <div>
        <h2>Audit Log</h2>
        <p class="page-subtitle">System-wide activity trail &mdash; {{ number_format($logs->total()) }} record(s)</p>
    </div>
</div>

<div class="filter-bar" style="flex-wrap:wrap;gap:.5rem;">
    <form method="GET" action="{{ route('audit-log.index') }}" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;width:100%;">
        <input
            type="text"
            name="user_name"
            value="{{ request('user_name') }}"
            placeholder="Search by user name&hellip;"
            class="filter-search"
            style="flex:2;min-width:160px;"
            autocomplete="off"
        >
        <select name="module" class="filter-select">
            <option value="">All Modules</option>
            @foreach ($modules as $key => $label)
            <option value="{{ $key }}" {{ request('module') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="action" class="filter-select">
            <option value="">All Actions</option>
            @foreach ($actions as $key => $label)
            <option value="{{ $key }}" {{ request('action') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-select" style="min-width:140px;">
        <input type="date" name="date_to"   value="{{ request('date_to') }}"   class="filter-select" style="min-width:140px;">
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        <a href="{{ route('audit-log.index') }}" class="btn btn-outline btn-sm">Clear</a>
    </form>
</div>

<div class="card">
    @if ($logs->isEmpty())
    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <p>No activity logs found.</p>
    </div>
    @else
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th style="white-space:nowrap;">Timestamp</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Module</th>
                    <th>Description</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                <tr>
                    <td style="white-space:nowrap;font-size:.82rem;color:var(--muted);">
                        {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i:s') }}
                    </td>
                    <td style="min-width:120px;">
                        @if ($log->user_name)
                        <div style="font-weight:500;font-size:.875rem;">{{ $log->user_name }}</div>
                        @php
                            $roleClass = match($log->user_role) {
                                'admin_it' => 'admin-it',
                                'admin_hr' => 'admin-hr',
                                default    => 'staff',
                            };
                            $roleLabel = match($log->user_role) {
                                'admin_it' => 'Admin (IT)',
                                'admin_hr' => 'Admin (HR)',
                                'staff'    => 'Staff',
                                default    => $log->user_role ?? '—',
                            };
                        @endphp
                        <span class="role-badge {{ $roleClass }}" style="font-size:.7rem;">{{ $roleLabel }}</span>
                        @else
                        <span style="color:var(--muted);font-size:.82rem;font-style:italic;">Guest</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $actionStyle = match($log->action) {
                                'login', 'create', 'import'              => 'background:#dcfce7;color:#166534;',
                                'update', 'toggle', 'approve', 'resolve' => 'background:#dbeafe;color:#1e40af;',
                                'delete', 'reject', 'dismiss'            => 'background:#fee2e2;color:#991b1b;',
                                default                                  => 'background:#f1f5f9;color:#64748b;',
                            };
                        @endphp
                        <span class="status-badge" style="{{ $actionStyle }}font-size:.75rem;white-space:nowrap;">
                            {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td>
                        <span style="font-size:.78rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;white-space:nowrap;">
                            {{ $modules[$log->module] ?? $log->module }}
                        </span>
                    </td>
                    <td style="font-size:.875rem;max-width:340px;">{{ $log->description }}</td>
                    <td style="font-size:.78rem;color:var(--muted);font-family:monospace;white-space:nowrap;">
                        {{ $log->ip_address ?? '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($logs->hasPages())
    <div style="padding:1rem 1.25rem;border-top:1px solid var(--border);">
        {{ $logs->links() }}
    </div>
    @endif
    @endif
</div>
@endsection
