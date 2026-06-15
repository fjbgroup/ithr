<?php

namespace App\Exports;

use App\Models\TrainingAttendance;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class TrainingExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = TrainingAttendance::join('staff', 'training_attendances.staff_id', '=', 'staff.id')
            ->join('training_courses', 'training_attendances.course_id', '=', 'training_courses.id')
            ->leftJoin('departments', 'staff.department_id', '=', 'departments.id');

        if (!empty($this->filters['year'])) {
            $query->whereYear('training_courses.start_date', $this->filters['year']);
        }
        if (!empty($this->filters['dept'])) {
            $query->where('staff.department_id', $this->filters['dept']);
        }
        if (!empty($this->filters['month'])) {
            $query->whereMonth('training_courses.start_date', $this->filters['month']);
        }
        if (!empty($this->filters['type'])) {
            $type_filter = $this->filters['type'];
            $query->where(function($q) use ($type_filter) {
                $q->where('training_attendances.training_type', $type_filter)
                  ->orWhere(function($sq) use ($type_filter) {
                      $sq->whereNull('training_attendances.training_type')
                         ->where('training_courses.training_type', $type_filter);
                  });
            });
        }
        if (!empty($this->filters['company'])) {
            $query->where('departments.company', $this->filters['company']);
        }

        return $query->select(
                'training_courses.start_date',
                'training_attendances.training_type',
                'training_courses.training_type as course_type',
                'training_courses.code as course_code',
                'training_courses.title as course_title',
                'staff.name as staff_name',
                'staff.staff_no',
                'departments.name as dept_name',
                'departments.company',
                'training_attendances.status'
            )
            ->orderBy('training_courses.start_date', 'DESC')
            ->get();
    }

    public function map($row): array
    {
        return [
            $row->start_date,
            $row->training_type ?: ($row->course_type ?: 'External'),
            $row->course_code,
            $row->course_title,
            $row->staff_name,
            $row->staff_no,
            $row->dept_name,
            $row->company,
            $row->status,
        ];
    }

    public function headings(): array
    {
        return [
            'Date',
            'Type',
            'Course Code',
            'Course Title',
            'Staff Name',
            'Staff No',
            'Department',
            'Company',
            'Status',
        ];
    }

    public function title(): string
    {
        return 'Training Records';
    }
}
