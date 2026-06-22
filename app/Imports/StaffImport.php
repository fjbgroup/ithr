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
            $dateJoined = $this->parseDate($row[$this->col('date_joined')] ?? null);
            $icNumber   = trim($row[$this->col('ic_number')] ?? '') ?: null;

            // Derive DOB from IC if available, otherwise from DOB column
            $dob = $icNumber
                ? ($this->parseDobFromIc($icNumber) ?? $this->parseDate($row[$this->col('date_of_birth')] ?? null))
                : $this->parseDate($row[$this->col('date_of_birth')] ?? null);

            if (!$staffNo || !$name || !$dateJoined) {
                $this->skipped[] = 'Row ' . ($i + 2) . ': missing required field(s) — Employee ID, Legal Full Name, and Hire Date are required';
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

            $company = Company::where('code', $companyCode)->orWhere('name', $companyCode)->first();

            // Always-update fields (required or essential)
            $updateData = [
                'name'       => $name,
                'date_joined' => $dateJoined,
                'is_active'  => 1,
            ];

            // Only overwrite optional fields when the imported value is non-empty,
            // so existing data (email, phone, etc.) is preserved for partial imports.
            $optional = [
                'position'            => trim($row[$this->col('position')] ?? ''),
                'department_id'       => $dept?->id,
                'company'             => $company?->code ?? (mb_substr($companyCode, 0, 200) ?: null),
                'company_id'          => $company?->id,
                'email'               => trim($row[$this->col('email')] ?? ''),
                'date_of_birth'       => $dob,
                'ic_number'           => $icNumber,
                'employment_status'   => trim($row[$this->col('employment_status')] ?? '') ?: null,
                'last_promotion_date' => $this->parseDate($row[$this->col('last_promotion_date')] ?? null),
                'gender'              => trim($row[$this->col('gender')] ?? ''),
                'location'            => trim($row[$this->col('location')] ?? ''),
                'phone_number'        => trim($row[$this->col('phone_number')] ?? ''),
                'compensation_grade'  => trim($row[$this->col('compensation_grade')] ?? ''),
                'management_level'    => trim($row[$this->col('management_level')] ?? ''),
                'job_level'           => trim($row[$this->col('job_level')] ?? ''),
                'job_category'        => trim($row[$this->col('job_category')] ?? ''),
            ];
            foreach ($optional as $k => $v) {
                if ($v !== null && $v !== '') {
                    $updateData[$k] = $v;
                }
            }

            $staff = Staff::updateOrCreate(['staff_no' => $staffNo], $updateData);

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

    private function parseDobFromIc(string $ic): ?string
    {
        $digits = preg_replace('/[^0-9]/', '', $ic);
        if (strlen($digits) < 6) return null;
        $yy   = (int) substr($digits, 0, 2);
        $mm   = (int) substr($digits, 2, 2);
        $dd   = (int) substr($digits, 4, 2);
        $year = $yy > 30 ? 1900 + $yy : 2000 + $yy;
        try {
            return \Carbon\Carbon::createFromDate($year, $mm, $dd)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseDate($value): ?string
    {
        if ($value === null || $value === '' || $value === 0) return null;

        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        $value = trim((string) $value);
        if (!$value) return null;

        // Try explicit formats common in Malaysian HR data (d/m/Y first)
        foreach (['d/m/Y', 'd-m-Y', 'd/m/y', 'd.m.Y', 'Y-m-d', 'm/d/Y'] as $fmt) {
            try {
                $dt = \Carbon\Carbon::createFromFormat($fmt, $value);
                if ($dt && $dt->format($fmt) === $value) return $dt->format('Y-m-d');
            } catch (\Exception $e) {}
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
