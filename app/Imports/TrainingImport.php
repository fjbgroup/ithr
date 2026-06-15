<?php

namespace App\Imports;

use App\Models\TrainingCourse;
use App\Models\TrainingAttendance;
use App\Models\Staff;
use Illuminate\Support\Str;

class TrainingImport
{
    public array $skipped = [];
    public int $imported = 0;

    public function __construct(private array $mapping = [], private ?string $fixedType = null) {}

    private function col(string $field): string
    {
        return $this->mapping[$field] ?? $field;
    }

    public function processRows($rows): void
    {
        foreach ($rows as $i => $row) {
            $courseCode = strtoupper(trim($row[$this->col('course_code')] ?? ''));
            $staffNo    = trim($row[$this->col('staff_no')] ?? '');

            // Auto-generate course_code from title when not provided
            if (!$courseCode) {
                $title = trim($row[$this->col('course_title')] ?? '');
                if (!$title) {
                    $this->skipped[] = 'Row ' . ($i + 2) . ': missing course_code and course_title';
                    continue;
                }
                $courseCode = strtoupper(substr(Str::slug($title, ''), 0, 100));
            }

            $trainingDateRaw = $row[$this->col('start_date')] ?? null;
            $parsed = $this->parseDateRange($trainingDateRaw);
            $startDate = $parsed['start_date'];
            $endDate   = $parsed['end_date'];
            $calculatedDuration = $parsed['duration'];

            $courseTitle  = trim($row[$this->col('course_title')] ?? $courseCode);

            $rowType = $this->fixedType ?: (in_array(trim($row[$this->col('training_type')] ?? ''), ['External', 'Internal'])
                                        ? trim($row[$this->col('training_type')]) : 'External');

            $excelDuration = trim($row[$this->col('duration')] ?? '');
            $finalDuration = $excelDuration ?: $calculatedDuration;

            $course = TrainingCourse::updateOrCreate(
                ['code' => $courseCode],
                [
                    'title'         => $courseTitle ?: $courseCode,
                    'training_type' => $rowType,
                    'company'       => trim($row[$this->col('company')] ?? ''),
                    'start_date'    => $startDate,
                    'end_date'      => $endDate,
                    'venue'         => trim($row[$this->col('venue')] ?? ''),
                    'duration'      => $finalDuration,
                ]
            );

            if (!$staffNo) continue;

            $staff = Staff::where('staff_no', $staffNo)->first();
            if (!$staff) {
                $this->skipped[] = 'Row ' . ($i + 2) . ": staff_no '{$staffNo}' not found";
                continue;
            }

            TrainingAttendance::updateOrCreate(
                ['staff_id' => $staff->id, 'course_id' => $course->id],
                [
                    'status'        => trim($row[$this->col('status')] ?? 'Completed') ?: 'Completed',
                    'training_type' => $course->training_type,
                    'remarks'       => trim($row[$this->col('remarks')] ?? ''),
                ]
            );

            $this->imported++;
        }
    }

    private function parseDateRange($value): array
    {
        $result = ['start_date' => null, 'end_date' => null, 'duration' => null];
        if (!$value) return $result;

        if (is_numeric($value)) {
            try {
                $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                $dstr = $dt->format('Y-m-d');
                return ['start_date' => $dstr, 'end_date' => $dstr, 'duration' => '1 day'];
            } catch (\Exception $e) {
                return $result;
            }
        }

        $str = trim((string)$value);
        if (!$str) return $result;

        // Pattern for "12-13/1/2026" or "12-13 Jan 2026" or "12 - 13 Jan 2026"
        if (preg_match('/^(\d+)\s*[-–]\s*(\d+)\s*[\/\s](.+)$/i', $str, $m)) {
            $startDay = (int)$m[1];
            $endDay   = (int)$m[2];
            $rest     = trim($m[3]);

            try {
                $rest = trim(str_replace(['/', '\\'], ' ', $rest));
                $baseDateStr = $endDay . ' ' . $rest;
                
                $dtEnd = null;
                if (preg_match('/^\d+\s+\d+\s+\d+$/', $baseDateStr)) {
                    try { $dtEnd = \Carbon\Carbon::createFromFormat('j n Y', $baseDateStr); } catch (\Exception $e) {}
                }
                
                if (!$dtEnd) {
                    try { $dtEnd = \Carbon\Carbon::parse($baseDateStr); } catch (\Exception $e) {}
                }
                
                if ($dtEnd) {
                    $days = ($endDay - $startDay) + 1;
                    if ($days > 0) {
                        $dtStart = $dtEnd->copy()->day($startDay);
                        return [
                            'start_date' => $dtStart->format('Y-m-d'),
                            'end_date'   => $dtEnd->format('Y-m-d'),
                            'duration'   => $days . ($days > 1 ? ' days' : ' day')
                        ];
                    }
                }
            } catch (\Exception $e) {}
        }

        // Try d/m/Y or d-m-Y directly for single dates
        if (preg_match('/^\d+[\/\-]\d+[\/\-]\d+$/', $str)) {
            try {
                $val = str_replace('-', '/', $str);
                $dstr = \Carbon\Carbon::createFromFormat('j/n/Y', $val)->format('Y-m-d');
                return ['start_date' => $dstr, 'end_date' => $dstr, 'duration' => '1 day'];
            } catch (\Exception $e) {}
        }

        try {
            $dstr = \Carbon\Carbon::parse($str)->format('Y-m-d');
            return ['start_date' => $dstr, 'end_date' => $dstr, 'duration' => '1 day'];
        } catch (\Exception $e) {}

        return $result;
    }
}
