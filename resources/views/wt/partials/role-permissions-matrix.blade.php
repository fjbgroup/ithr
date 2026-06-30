@php
    $permissionRows = [
        ['module' => 'Dashboard overview', 'ict' => 'Full', 'executive' => 'Full'],
        ['module' => 'Inventory list and unit timeline', 'ict' => 'Manage', 'executive' => 'View'],
        ['module' => 'Add, edit, import, and delete walkie records', 'ict' => 'Full', 'executive' => 'No'],
        ['module' => 'Under repair, faulty, duplicate ID, and special use tools', 'ict' => 'Manage', 'executive' => 'View / submit'],
        ['module' => 'Request walkie talkie', 'ict' => 'Submit', 'executive' => 'Submit'],
        ['module' => 'Return unit', 'ict' => 'Submit / process', 'executive' => 'Submit'],
        ['module' => 'Report faulty or damaged unit', 'ict' => 'Submit / process', 'executive' => 'Submit'],
        ['module' => 'Approval inbox', 'ict' => 'Final approval', 'executive' => 'Department approval'],
        ['module' => 'Approval history', 'ict' => 'Full', 'executive' => 'No'],
        ['module' => 'Handover and pickup records', 'ict' => 'Manage', 'executive' => 'Own records'],
        ['module' => 'My inventory and request status', 'ict' => 'Own records', 'executive' => 'Own records'],
        ['module' => 'All status tracking and reports', 'ict' => 'Full', 'executive' => 'Relevant records'],
        ['module' => 'Users control', 'ict' => 'Manage', 'executive' => 'No'],
        ['module' => 'Master data', 'ict' => 'Manage', 'executive' => 'No'],
        ['module' => 'System logs and audit trail', 'ict' => 'View', 'executive' => 'No'],
        ['module' => 'Profile, policy, and permission matrix', 'ict' => 'View', 'executive' => 'View'],
    ];

    $permissionTone = function ($value) {
        $value = strtolower($value);

        if (str_contains($value, 'no')) {
            return 'none';
        }

        if (str_contains($value, 'view') || str_contains($value, 'own') || str_contains($value, 'limited') || str_contains($value, 'relevant')) {
            return 'partial';
        }

        return 'full';
    };
@endphp

<div class="role-matrix-card">
    <div class="role-matrix-scroll">
        <table class="role-matrix-table">
            <thead>
                <tr>
                    <th>Module / Feature</th>
                    <th><span class="role-pill role-pill-ict">ICT</span></th>
                    <th><span class="role-pill role-pill-executive">Executive</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach($permissionRows as $row)
                <tr>
                    <td>
                        <div class="feature-name">{{ $row['module'] }}</div>
                    </td>
                    @foreach(['ict', 'executive'] as $role)
                        @php($tone = $permissionTone($row[$role]))
                        <td>
                            <span class="permission-badge permission-{{ $tone }}">
                                @if($tone === 'none')
                                    <i class="fas fa-minus"></i>
                                @elseif($tone === 'partial')
                                    <i class="fas fa-eye"></i>
                                @else
                                    <i class="fas fa-check"></i>
                                @endif
                                {{ $row[$role] }}
                            </span>
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
