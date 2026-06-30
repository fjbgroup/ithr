@extends('layouts.app')

@section('title', 'Role Permissions & Capabilities')

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
    text-align: left;
    border-bottom: 1px solid var(--border);
    font-size: 0.85rem;
}
.role-matrix-table th {
    background: var(--table-head-bg);
    color: var(--table-head-color);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-size: 0.75rem;
}
.role-matrix-table tr:last-child td {
    border-bottom: none;
}
.role-matrix-table td.role-name {
    font-weight: 600;
    color: var(--text);
    width: 200px;
    vertical-align: top;
}
.role-matrix-table td ul {
    margin: 0;
    padding-left: 1.2rem;
    color: var(--text);
}
.role-matrix-table td ul li {
    margin-bottom: 0.4rem;
    line-height: 1.4;
}
.role-matrix-table td ul li:last-child {
    margin-bottom: 0;
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
</style>
@endsection

@section('content')
<div class="role-matrix-container">
    <div class="role-matrix-header">
        <h1>System Role Capabilities</h1>
        <p>A breakdown of what each user role can view, edit, and manage within the HR system.</p>
    </div>

    <div class="role-matrix-card">
        <div class="table-responsive">
            <table class="role-matrix-table">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Permissions & Capabilities</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="role-name">
                            <span class="role-badge-custom role-admin">Admin (IT & HR)</span>
                        </td>
                        <td>
                            <ul>
                                <li><strong>Full Data Access:</strong> Can view, add, edit, and delete all staff and family records.</li>
                                <li><strong>Master Data Management:</strong> Can manage departments, positions, and core system options.</li>
                                <li><strong>User Accounts:</strong> Manage user roles, passwords, 2FA settings, and system access blocks.</li>
                                <li><strong>Training & IR:</strong> Full management of training records, QR attendance, and Incident Reports (IR).</li>
                                <li><strong>Approvals:</strong> Can bypass standard approval workflows (e.g., meeting rooms) and resolve user update requests.</li>
                                <li><strong>Audit Log:</strong> IT Admins have access to the system audit trail.</li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td class="role-name">
                            <span class="role-badge-custom role-exec">CEO / GM / Head of Unit</span>
                        </td>
                        <td>
                            <ul>
                                <li><strong>Oversight Access:</strong> Can view all staff registry profiles, family details, and training/travel records across the company.</li>
                                <li><strong>Reporting:</strong> Can generate and export company-wide reports.</li>
                                <li><strong>Approvals (PIC):</strong> Can approve or reject meeting room bookings if designated as the Person-In-Charge for specific rooms.</li>
                                <li><strong>Read-Only Bounds:</strong> Cannot edit staff data directly or access system administration (Master Data / User Accounts).</li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td class="role-name">
                            <span class="role-badge-custom role-staff">Staff</span>
                        </td>
                        <td>
                            <ul>
                                <li><strong>Self-Service Profile:</strong> Can view their own profile and family records.</li>
                                <li><strong>Update Requests:</strong> Cannot edit their own data directly, but can submit <em>Update Requests</em> for HR to review and approve.</li>
                                <li><strong>Meeting Rooms:</strong> Can book available meeting rooms (subject to PIC approval).</li>
                                <li><strong>My Travel & Training:</strong> Can view their own travel requests and attend training sessions.</li>
                                <li><strong>Restricted View:</strong> Cannot view other staff members' data or access administrative modules.</li>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
