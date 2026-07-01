<?php

namespace App\Support;

use App\Models\Department;
use App\Models\Staff;
use App\Models\WT\AccessRequest;
use App\Models\WT\MasterData;
use App\Models\WT\MaintenanceRecord;
use App\Models\WT\User;
use App\Models\WT\WalkieTalkie;
use Illuminate\Support\Collection;

class WtFormOptions
{
    private ?array $lists = null;

    public function lists(): array
    {
        if ($this->lists !== null) {
            return $this->lists;
        }

        $users = User::query()
            ->select(['name', 'staff_no', 'dept_name', 'position', 'phone_no'])
            ->get();

        $staff = Staff::query()
            ->with('department:id,name')
            ->where('is_active', 1)
            ->select(['id', 'staff_no', 'name', 'position', 'department_id', 'phone_number'])
            ->get();

        $names = $this->optionValues(
            $users->pluck('name')
                ->merge($staff->pluck('name'))
                ->merge(AccessRequest::query()->pluck('full_name'))
                ->merge(MaintenanceRecord::query()->pluck('reporter_name'))
        );

        $departments = $this->optionValues(
            MasterData::valuesFor('department')
                ->merge(Department::query()->pluck('name'))
                ->merge($users->pluck('dept_name'))
                ->merge($staff->map(fn (Staff $row) => $row->department?->name))
                ->merge(WalkieTalkie::query()->pluck('department'))
                ->merge(AccessRequest::query()->pluck('department'))
                ->merge(MaintenanceRecord::query()->pluck('department_name'))
        );

        $positions = collect($this->optionValues(
            MasterData::valuesFor('position')
                ->merge($users->pluck('position'))
                ->merge($staff->pluck('position'))
                ->merge(WalkieTalkie::query()->pluck('position'))
                ->merge(AccessRequest::query()->pluck('position'))
                ->merge(MaintenanceRecord::query()->pluck('designation'))
        ))->reject(fn ($value) => MasterData::isBlockedValue('position', $value))->values()->all();

        $locations = $this->optionValues(
            MasterData::valuesFor('location')
                ->merge(WalkieTalkie::query()->pluck('location'))
                ->merge(AccessRequest::query()->pluck('location'))
                ->merge(MaintenanceRecord::query()->pluck('location'))
                ->merge(collect(['T4', 'T5', 'GT', 'LBSB']))
        );

        $bays = $this->optionValues(
            MasterData::valuesFor('bay')
                ->merge(AccessRequest::query()->pluck('bay_from'))
        );

        $phoneByName = $users
            ->mapWithKeys(fn (User $row) => [strtoupper(trim((string) $row->name)) => $row->phone_no])
            ->merge($staff->mapWithKeys(fn (Staff $row) => [strtoupper(trim((string) $row->name)) => $row->phone_number]))
            ->filter()
            ->all();

        $personMetaByName = $users
            ->mapWithKeys(fn (User $row) => [
                strtoupper(trim((string) $row->name)) => [
                    'staff_no' => $row->staff_no,
                    'department' => $row->dept_name,
                    'position' => $row->position,
                    'phone_no' => $row->phone_no,
                ],
            ])
            ->merge($staff->mapWithKeys(fn (Staff $row) => [
                strtoupper(trim((string) $row->name)) => [
                    'staff_no' => $row->staff_no,
                    'department' => $row->department?->name,
                    'position' => $row->position,
                    'phone_no' => $row->phone_number,
                ],
            ]))
            ->all();

        return $this->lists = [
            'names' => $names,
            'ownership_names' => $names,
            'pickup_person_names' => $names,
            'departments' => $departments,
            'positions' => $positions,
            'locations' => $locations,
            'bays' => $bays,
            'phone_by_name' => $phoneByName,
            'person_meta_by_name' => $personMetaByName,
        ];
    }

    private function optionValues(Collection $values): array
    {
        return $values
            ->map(fn ($value) => strtoupper(trim((string) $value)))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }
}
