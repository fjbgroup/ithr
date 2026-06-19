<?php

namespace App\Exports;

use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StaffExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return Department::leftJoin('staff', function($join) {
                $join->on('staff.department_id', '=', 'departments.id')
                     ->where('staff.is_active', '=', 1);
            })
            ->select('departments.name as dept', 'departments.company', DB::raw('COUNT(staff.id) as headcount'))
            ->groupBy('departments.id', 'departments.name', 'departments.company')
            ->orderBy('departments.company')
            ->orderBy('headcount', 'DESC')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Department',
            'Company',
            'Headcount',
        ];
    }

    public function title(): string
    {
        return 'Department Distribution';
    }
}
