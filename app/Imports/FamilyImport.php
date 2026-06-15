<?php

namespace App\Imports;

use App\Models\FamilyMember;
use App\Models\Staff;

class FamilyImport
{
    public array $skipped = [];
    public int $imported = 0;

    public function __construct(private array $mapping = []) {}

    private function col(string $field): string
    {
        return $this->mapping[$field] ?? $field;
    }

    private function resolve(array|object $row, string ...$candidates): ?string
    {
        foreach ($candidates as $key) {
            $mapped = $this->col($key);
            $val = $row[$mapped] ?? $row[$key] ?? null;
            if ($val !== null && trim((string) $val) !== '') return trim((string) $val);
        }
        return null;
    }

    public function processRows($rows): void
    {
        foreach ($rows as $i => $row) {
            $staffNo      = $this->resolve($row, 'staff_no', 'employee_id') ?? '';
            $memberName   = $this->resolve($row, 'name', 'legal_name_first_name') ?? '';
            $relationship = $this->resolve($row, 'relationship', 'related_person_relationship') ?? '';

            if (!$staffNo || !$memberName || !$relationship) {
                $this->skipped[] = 'Row ' . ($i + 2) . ': missing staff_no/employee_id, name, or relationship';
                continue;
            }

            $staff = Staff::where('staff_no', $staffNo)->first();
            if (!$staff) {
                $this->skipped[] = 'Row ' . ($i + 2) . ": staff_no '{$staffNo}' not found";
                continue;
            }

            $relationship     = $this->normalizeRelationship($relationship);
            $dob              = $this->parseDate($this->resolve($row, 'date_of_birth'));
            $emergencyContact = in_array(strtolower(trim($this->resolve($row, 'emergency_contact') ?? '')), ['yes', '1', 'true']) ? 'Yes' : 'No';

            FamilyMember::updateOrCreate(
                [
                    'staff_id'           => $staff->id,
                    'family_member_name' => $memberName,
                    'relationship'       => $relationship,
                ],
                [
                    'date_of_birth'            => $dob,
                    'effective_date'           => $this->parseDate($this->resolve($row, 'effective_date')),
                    'nric_no'                  => $this->resolve($row, 'nric_no', 'dependent_nric_no'),
                    'dependent_id'             => $this->resolve($row, 'dependent_id'),
                    'gender'                   => $this->normalizeGender($this->resolve($row, 'gender') ?? ''),
                    'city_of_birth'            => $this->resolve($row, 'city_of_birth'),
                    'country_of_birth'         => $this->resolve($row, 'country_of_birth'),
                    'nationality'              => $this->resolve($row, 'nationality', 'primary_nationality_dependent'),
                    'citizenship_status'       => $this->resolve($row, 'citizenship_status', 'citizenship_status_locale_sensitive'),
                    'region_of_birth'          => $this->resolve($row, 'region_of_birth'),
                    'use_employee_address'     => $this->normalizeBool($this->resolve($row, 'use_employee_address') ?? 'No'),
                    'use_employee_phone'       => $this->normalizeBool($this->resolve($row, 'use_employee_phone') ?? 'No'),
                    'phone_country_code'       => $this->resolve($row, 'phone_country_code', 'cf_ss_country_phone'),
                    'phone_number'             => $this->resolve($row, 'phone_number'),
                    'phone_device_type'        => $this->resolve($row, 'phone_device_type', 'cf_lrv_phone_device_type'),
                    'is_fulltime_student'      => $this->normalizeBool($this->resolve($row, 'is_fulltime_student', 'full_time_student') ?? 'No'),
                    'student_start_date'       => $this->parseDate($this->resolve($row, 'student_start_date', 'student_status_start_date')),
                    'student_end_date'         => $this->parseDate($this->resolve($row, 'student_end_date', 'student_status_end_date')),
                    'occupation'               => $this->resolve($row, 'occupation', 'dependent_occupation'),
                    'occupation_effective_date'=> $this->parseDate($this->resolve($row, 'occupation_effective_date')),
                    'is_disabled'              => $this->normalizeBool($this->resolve($row, 'is_disabled', 'disabled') ?? 'No'),
                    'is_terminated'            => $this->normalizeBool($this->resolve($row, 'is_terminated', 'terminated') ?? 'No'),
                    'emergency_contact'        => $emergencyContact,
                    'company_code'             => $this->resolve($row, 'company_code'),
                    'company_name'             => $this->resolve($row, 'company_name'),
                    'region_name'              => $this->resolve($row, 'region_name'),
                ]
            );

            $this->imported++;
        }
    }

    private function normalizeRelationship(string $value): string
    {
        $v = strtolower($value);
        if (in_array(ucfirst($v), ['Spouse', 'Child', 'Parent', 'Sibling'])) return ucfirst($v);
        if (str_contains($v, 'spouse') || str_contains($v, 'husband') || str_contains($v, 'wife') || str_contains($v, 'domestic partner')) return 'Spouse';
        if (str_contains($v, 'child') || str_contains($v, 'legitimate') || str_contains($v, 'adopted') || str_contains($v, 'son') || str_contains($v, 'daughter')) return 'Child';
        if (str_contains($v, 'parent') || str_contains($v, 'father') || str_contains($v, 'mother')) return 'Parent';
        if (str_contains($v, 'sibling') || str_contains($v, 'brother') || str_contains($v, 'sister')) return 'Sibling';
        return $value;
    }

    private function normalizeGender(string $value): ?string
    {
        $v = strtolower($value);
        if (str_contains($v, 'female') || $v === 'f') return 'Female';
        if (str_contains($v, 'male') || $v === 'm') return 'Male';
        return null;
    }

    private function normalizeBool(mixed $value): int
    {
        return in_array(strtolower(trim((string) $value)), ['yes', '1', 'true']) ? 1 : 0;
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
