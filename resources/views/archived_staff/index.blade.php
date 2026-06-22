@extends('layouts.app')

@section('title', 'Archived Staff')

@section('content')
<div class="page-header">
    <div>
        <h2>Archived Staff</h2>
        <p class="page-subtitle">{{ $archivedStaff->count() }} deactivated staff member(s)</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar" style="flex-wrap:wrap;gap:.5rem;">
    <form method="GET" action="{{ route('archived-staff.index') }}" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;width:100%;">
        <div class="app-search" style="min-width:220px;">
            <svg class="app-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" value="{{ $search }}" placeholder="Search name, staff no, department…" autocomplete="off">
        </div>
        <select name="filter" class="filter-select" onchange="this.form.submit()">
            <option value=""     {{ $filter === ''         ? 'selected' : '' }}>All Archived</option>
            <option value="disabled"  {{ $filter === 'disabled'  ? 'selected' : '' }}>Account Disabled</option>
            <option value="inactive"  {{ $filter === 'inactive'  ? 'selected' : '' }}>HR Inactive</option>
        </select>
        <button type="submit" class="btn btn-outline btn-sm">Search</button>
        <a href="{{ route('archived-staff.index') }}" class="btn btn-ghost btn-sm">Clear</a>
    </form>
</div>

<div class="card card-sticky">
    @if ($archivedStaff->isEmpty())
    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M20 9v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9"/><path d="M9 22V12h6v10M2 10.6L12 2l10 8.6"/></svg>
        <p>No archived staff found.</p>
        @if($search || $filter)
        <a href="{{ route('archived-staff.index') }}" class="btn btn-ghost btn-sm" style="margin-top:.5rem;">Clear filters</a>
        @endif
    </div>
    @else
    <div class="table-wrap table-wrap-sticky">
        <table class="table table-sticky">
            <thead>
                <tr>
                    <th>Staff No</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Reason</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($archivedStaff as $s)
            @php
                $userDisabled  = $s->user && !$s->user->is_active;
                $hrInactive    = !$s->is_active;
            @endphp
            <tr>
                <td><code style="color:#6366f1;font-size:.82rem;">{{ $s->staff_no }}</code></td>
                <td>
                    <strong>{{ $s->name }}</strong>
                    @if($s->user)
                    <div style="font-size:.75rem;color:var(--muted);">{{ $s->user->email }}</div>
                    @endif
                </td>
                <td><span class="dept-badge" style="font-size:.75rem;">{{ $s->department->name ?? '—' }}</span></td>
                <td style="font-size:.85rem;color:var(--muted);">{{ $s->position ?? '—' }}</td>
                <td style="white-space:nowrap;">
                    @if($userDisabled)
                    <span class="status-badge" style="background:#fee2e2;color:#991b1b;font-size:.72rem;">Account Disabled</span>
                    @endif
                    @if($hrInactive)
                    <span class="status-badge" style="background:#fef3c7;color:#92400e;font-size:.72rem;margin-top:.2rem;display:block;">HR Inactive</span>
                    @endif
                </td>
                <td class="td-actions">
                    <a href="{{ route('staff.show', $s->id) }}" class="btn btn-sm btn-ghost">View</a>
                    @canwrite
                    @if($userDisabled && $s->user)
                    <form method="POST" action="{{ route('users.toggle_active', $s->user->id) }}" style="display:inline;">
                        @csrf @method('PATCH')
                        <input type="hidden" name="is_active" value="1">
                        <button type="submit" class="btn btn-sm" style="background:#dcfce7;color:#166534;">Enable Account</button>
                    </form>
                    @endif
                    @if($hrInactive && $s->user)
                    <form method="POST" action="{{ route('users.toggle_staff_status', $s->user->id) }}" style="display:inline;">
                        @csrf @method('PATCH')
                        <input type="hidden" name="is_active" value="1">
                        <button type="submit" class="btn btn-sm" style="background:#d1fae5;color:#065f46;">Set Active</button>
                    </form>
                    @endif
                    @endcanwrite
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
