@extends('layouts.app')

@section('title', 'Role Permissions Matrix')

@section('styles')
<style>
.role-matrix-container {
    max-width: 1000px;
    margin: 0 auto;
}
.role-matrix-header {
    margin-bottom: 1.5rem;
}
.role-matrix-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 0.5rem;
}
.role-matrix-header p {
    font-size: 0.85rem;
    color: var(--muted);
}
.role-matrix-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}
.role-matrix-table {
    width: 100%;
    border-collapse: collapse;
}
.role-matrix-table th, .role-matrix-table td {
    padding: 1rem 1.25rem;
    text-align: center;
    border-bottom: 1px solid var(--border);
    font-size: 0.85rem;
}
.role-matrix-table th:first-child,
.role-matrix-table td:first-child {
    text-align: left;
}
.role-matrix-table th {
    background: var(--table-head-bg);
    color: var(--table-head-color);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-size: 0.75rem;
    white-space: nowrap;
}
.role-matrix-table td.feature-name {
    font-weight: 600;
    color: var(--text);
}
.role-matrix-table td.feature-desc {
    display: block;
    font-weight: 400;
    font-size: 0.75rem;
    color: var(--muted);
    margin-top: 0.2rem;
}
.role-matrix-table tr:last-child td {
    border-bottom: none;
}
.role-matrix-table tr:hover td {
    background: var(--row-alt);
}
.icon-check {
    color: #10b981; /* Emerald */
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.icon-cross {
    color: #ef4444; /* Red */
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.icon-partial {
    color: #f59e0b; /* Amber */
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.role-badge-custom {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 700;
    background: #eff6ff;
    color: #3b82f6;
    margin-bottom: 0.5rem;
}
.role-admin { background: #fef2f2; color: #ef4444; }
.role-exec { background: #fffbeb; color: #f59e0b; }
.role-staff { background: #f0fdf4; color: #22c55e; }

html.dark .role-admin { background: rgba(239, 68, 68, 0.15); color: #fca5a5; }
html.dark .role-exec { background: rgba(245, 158, 11, 0.15); color: #fcd34d; }
html.dark .role-staff { background: rgba(34, 197, 94, 0.15); color: #86efac; }
html.dark .role-matrix-table tr:hover td { background: rgba(255, 255, 255, 0.02); }

/* Tooltip for partial permissions */
.partial-tooltip {
    position: relative;
    cursor: help;
    border-bottom: 1px dotted #f59e0b;
}
</style>
@endsection

@section('content')
<div class="role-matrix-container">
    <div class="role-matrix-header">
        <h1>Role Permissions Matrix</h1>
        <p>A detailed breakdown of feature access and administrative capabilities for each user role.</p>
    </div>

    <div class="role-matrix-card">
        <div class="table-responsive">
            <table class="role-matrix-table">
                <thead>
                    <tr>
                        <th style="width: 40%">Module / Feature</th>
                        <th style="width: 20%">
                            <span class="role-badge-custom role-admin" style="margin-bottom:0">Admin</span>
                        </th>
                        <th style="width: 20%">
                            <span class="role-badge-custom role-exec" style="margin-bottom:0">Executive (CEO/GM/HOU)</span>
                        </th>
                        <th style="width: 20%">
                            <span class="role-badge-custom role-staff" style="margin-bottom:0">Staff</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span class="feature-name">View Own Profile</span>
                            <span class="feature-desc">Access to personal information, travel, and training records.</span>
                        </td>
                        <td><span class="icon-check"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span></td>
                        <td><span class="icon-check"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span></td>
                        <td><span class="icon-check"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="feature-name">View Global Staff Directory</span>
                            <span class="feature-desc">Can view profiles and contact info of all other staff members.</span>
                        </td>
                        <td><span class="icon-check"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span></td>
                        <td><span class="icon-check"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span></td>
                        <td><span class="icon-cross"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="feature-name">Edit Staff Data</span>
                            <span class="feature-desc">Directly modify any staff profile, family information, or positions.</span>
                        </td>
                        <td><span class="icon-check"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span></td>
                        <td><span class="icon-cross"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span></td>
                        <td><span class="icon-cross"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="feature-name">Submit Update Requests</span>
                            <span class="feature-desc">Request HR to update personal information (requires approval).</span>
                        </td>
                        <td><span class="icon-check"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span></td>
                        <td><span class="icon-check"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span></td>
                        <td><span class="icon-check"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="feature-name">Resolve Update Requests</span>
                            <span class="feature-desc">Approve or reject data modification requests from staff.</span>
                        </td>
                        <td><span class="icon-check"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span></td>
                        <td><span class="icon-cross"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span></td>
                        <td><span class="icon-cross"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="feature-name">Book Meeting Rooms</span>
                            <span class="feature-desc">Submit a reservation for any available meeting room.</span>
                        </td>
                        <td><span class="icon-check"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span></td>
                        <td><span class="icon-check"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span></td>
                        <td><span class="icon-check"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="feature-name">Approve Room Bookings</span>
                            <span class="feature-desc">Approve or reject pending meeting room reservations.</span>
                        </td>
                        <td><span class="icon-check"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span></td>
                        <td>
                            <span class="partial-tooltip icon-partial" title="Only if designated as the Person-In-Charge (PIC) for a specific room.">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                            </span>
                        </td>
                        <td>
                            <span class="partial-tooltip icon-partial" title="Only if designated as the Person-In-Charge (PIC) for a specific room.">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="feature-name">Manage User Accounts</span>
                            <span class="feature-desc">Change roles, reset passwords, or block system access.</span>
                        </td>
                        <td><span class="icon-check"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span></td>
                        <td><span class="icon-cross"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span></td>
                        <td><span class="icon-cross"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="feature-name">Manage Master Data</span>
                            <span class="feature-desc">Configure departments, settings, and structural master data.</span>
                        </td>
                        <td><span class="icon-check"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span></td>
                        <td><span class="icon-cross"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span></td>
                        <td><span class="icon-cross"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="feature-name">System Audit Logs</span>
                            <span class="feature-desc">View the security and activity logs of the entire system.</span>
                        </td>
                        <td><span class="icon-check"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span></td>
                        <td><span class="icon-cross"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span></td>
                        <td><span class="icon-cross"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="padding: 1rem 1.25rem; background: var(--row-alt); border-top: 1px solid var(--border); font-size: 0.75rem; color: var(--muted); display: flex; gap: 1.5rem; justify-content: center;">
            <span style="display: flex; align-items: center; gap: 0.35rem;">
                <span class="icon-check" style="width: 14px; height: 14px;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span> Full Access
            </span>
            <span style="display: flex; align-items: center; gap: 0.35rem;">
                <span class="icon-partial" style="width: 14px; height: 14px;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="12" x2="16" y2="12"></line></svg></span> Conditional Access
            </span>
            <span style="display: flex; align-items: center; gap: 0.35rem;">
                <span class="icon-cross" style="width: 14px; height: 14px;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span> No Access
            </span>
        </div>
    </div>
</div>
@endsection
