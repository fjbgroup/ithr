@php
    $permissionRows = [
        ['module' => 'Main landing page', 'ict' => 'View system dashboard', 'executive' => 'My inventory page'],
        ['module' => 'Inventory list and unit timeline', 'ict' => 'Manage all units', 'executive' => 'View assigned units'],
        ['module' => 'Add, edit, import, and delete walkie records', 'ict' => 'Manage records', 'executive' => 'Not available'],
        ['module' => 'Under repair and faulty units', 'ict' => 'Manage repair records', 'executive' => 'Submit faulty report'],
        ['module' => 'Duplicated ID records', 'ict' => 'Manage ID changes', 'executive' => 'Not available'],
        ['module' => 'Special use, spare, and temporary units', 'ict' => 'Manage special units', 'executive' => 'Not available'],
        ['module' => 'Walkie talkie request', 'ict' => 'Submit request', 'executive' => 'Submit request'],
        ['module' => 'Return unit', 'ict' => 'Submit and process', 'executive' => 'Submit return'],
        ['module' => 'Approval inbox', 'ict' => 'Final ICT approval', 'executive' => 'Executive approval'],
        ['module' => 'Approval history', 'ict' => 'View all history', 'executive' => 'Not available'],
        ['module' => 'Handover and pickup records', 'ict' => 'Manage handover', 'executive' => 'Own records only'],
        ['module' => 'My inventory and request status', 'ict' => 'Own records only', 'executive' => 'Own records only'],
        ['module' => 'All status tracking and reports', 'ict' => 'View all records', 'executive' => 'Relevant records only'],
        ['module' => 'Users control', 'ict' => 'Manage users', 'executive' => 'Not available'],
        ['module' => 'Master data', 'ict' => 'Manage master data', 'executive' => 'Not available'],
        ['module' => 'System logs and audit trail', 'ict' => 'View audit logs', 'executive' => 'Not available'],
        ['module' => 'Profile, policy, and role matrix', 'ict' => 'Manage all access', 'executive' => 'Manage own profile'],
    ];

    $permissionTone = function ($value) {
        $value = strtolower($value);

        if (str_contains($value, 'not available')) {
            return 'none';
        }

        if (str_contains($value, 'view') || str_contains($value, 'own') || str_contains($value, 'relevant')) {
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
