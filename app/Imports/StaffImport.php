<?php

namespace App\Imports;

use App\Models\Staff;
use App\Models\User;
use App\Models\Department;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class StaffImport
{
    public array $skipped = [];
    public int $imported = 0;

    public function __construct(private array $mapping = []) {}

    private function col(string $field): string
    {
        return $this->mapping[$field] ?? $field;
    }

    public function processRows($rows): void
    {
        foreach ($rows as $i => $row) {
            $staffNo    = trim($row[$this->col('staff_no')] ?? '');
            $name       = trim($row[$this->col('name')] ?? '');
            $dob        = $this->parseDate($row[$this->col('date_of_birth')] ?? null);
            $dateJoined = $this->parseDate($row[$this->col('date_joined')] ?? null);

            if (!$staffNo || !$name || !$dob || !$dateJoined) {
                $this->skipped[] = 'Row ' . ($i + 2) . ': missing required field(s) — Employee ID, Legal Full Name, Date of Birth, and Hire Date are all required';
                continue;
            }

            $companyCode = trim($row[$this->col('company')] ?? '');
            $deptName    = trim($row[$this->col('department')] ?? '');

            $dept = null;
            if ($deptName) {
                $dept = Department::firstOrCreate(
                    ['name' => $deptName, 'company' => $companyCode ?: 'N/A'],
                    ['name' => $deptName, 'company' => $companyCode ?: 'N/A']
                );
            }

            $company          = Company::where('code', $companyCode)->orWhere('name', $companyCode)->first();
            $criticalPosition = in_array(strtolower(trim($row[$this->col('critical_position')] ?? '')), ['yes', '1', 'true']) ? 1 : 0;

            $staff = Staff::updateOrCreate(
                ['staff_no' => $staffNo],
                [
                    'name'                => $name,
                    'position'            => trim($row[$this->col('position')] ?? ''),
                    'department_id'       => $dept?->id,
                    'company'             => $company?->code ?? mb_substr($companyCode, 0, 200),
                    'company_id'          => $company?->id,
                    'email'               => trim($row[$this->col('email')] ?? ''),
                    'date_joined'         => $dateJoined,
                    'date_of_birth'       => $dob,
                    'gender'              => trim($row[$this->col('gender')] ?? ''),
                    'operation_support'   => trim($row[$this->col('operation_support')] ?? ''),
                    'location'            => trim($row[$this->col('location')] ?? ''),
                    'phone_number'        => trim($row[$this->col('phone_number')] ?? ''),
                    'compensation_grade'  => trim($row[$this->col('compensation_grade')] ?? ''),
                    'management_level'    => trim($row[$this->col('management_level')] ?? ''),
                    'job_level'           => trim($row[$this->col('job_level')] ?? ''),
                    'job_category'        => trim($row[$this->col('job_category')] ?? ''),
                    'job_family'          => trim($row[$this->col('job_family')] ?? ''),
                    'job_classification'  => trim($row[$this->col('job_classification')] ?? ''),
                    'critical_position'   => $criticalPosition,
                    'is_active'           => 1,
                ]
            );

            $email = trim($row[$this->col('email')] ?? '');
            User::updateOrCreate(
                ['staff_no' => $staffNo],
                [
                    'name'      => $name,
                    'email'     => $email ?: $staffNo . '@staff.local',
                    'password'  => Hash::make($staffNo),
                    'role'      => 'staff',
                    'is_active' => 1,
                    'staff_id'  => $staff->id,
                ]
            );

            $this->imported++;
        }
    }

    private function parseDate($value): ?string
    {
        if (!$value) return null;
        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
