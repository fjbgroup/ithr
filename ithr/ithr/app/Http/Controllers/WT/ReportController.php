<?php

namespace App\Http\Controllers\WT;

use Illuminate\Http\Request;
use App\Models\WT\WalkieTalkie;
use App\Models\WT\MaintenanceRecord;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportController extends Controller
{
    private const FAULTY_REPORT_DATES = [
        'received_date',
        'ict_received_at',
        'repair_date',
        'finish_date',
        'maintenance_date',
        'handover_at',
        'pickup_at',
    ];

    public function summary()
    {
        $statusCountArray = [];
        $originalLabels = [];
        $originalValues = [];

        $statusQuery = WalkieTalkie::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get();

        foreach ($statusQuery as $row) {
            $statusKey = strtolower(str_replace(' ', '', $row->status));
            $statusCountArray[$statusKey] = $row->total;
            $originalLabels[] = $row->status;
            $originalValues[] = $row->total;
        }

        $walkies = WalkieTalkie::orderBy('walkie_id', 'asc')->get();

        return view('wt.admin.reports.summary', compact('walkies', 'originalLabels', 'originalValues'));
    }

    private function reportDateFor(MaintenanceRecord $record): ?Carbon
    {
        foreach (self::FAULTY_REPORT_DATES as $column) {
            if (! empty($record->{$column})) {
                try {
                    return Carbon::parse($record->{$column});
                } catch (\Throwable $exception) {
                    continue;
                }
            }
        }

        return null;
    }

    private function normalizedStatus(?string $status): string
    {
        $status = strtoupper(trim((string) $status));

        return $status === '' ? 'UNKNOWN' : $status;
    }

    private function faultyReportSummary(Collection $records): array
    {
        return [
            'total' => $records->count(),
            'pending' => $records->filter(fn (MaintenanceRecord $record) => in_array($this->normalizedStatus($record->status), ['WAITING FOR ADMIN', 'PENDING ADMIN IT'], true))->count(),
            'active' => $records->filter(fn (MaintenanceRecord $record) => in_array($this->normalizedStatus($record->status), ['UNDER REPAIR', 'FAULTY', 'B.E.R', 'READY TO COLLECT'], true))->count(),
            'done' => $records->filter(fn (MaintenanceRecord $record) => (bool) $record->done || in_array($this->normalizedStatus($record->status), ['ALREADY FIXED', 'DONE'], true))->count(),
        ];
    }

    public function faultyThreeMonths(Request $request)
    {
        $currentYear = (int) now()->year;
        $selectedMonth = (int) $request->query('month', now()->month);
        $selectedYear = (int) $request->query('year', $currentYear);

        if ($selectedMonth < 1 || $selectedMonth > 12) {
            $selectedMonth = (int) now()->month;
        }

        if ($selectedYear < 2000 || $selectedYear > $currentYear + 1) {
            $selectedYear = $currentYear;
        }

        $startDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfDay();
        $endDate = (clone $startDate)->endOfMonth()->endOfDay();

        $records = MaintenanceRecord::query()
            ->with(['temporarySpareWalkie', 'handler', 'submitToAdmin'])
            ->where('request_source', 'user')
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhereRaw("UPPER(status) <> 'DRAFT'");
            })
            ->where(function ($query) use ($startDate, $endDate) {
                foreach (self::FAULTY_REPORT_DATES as $column) {
                    $query->orWhereBetween($column, [$startDate, $endDate]);
                }
            })
            ->orderByDesc('received_date')
            ->orderByDesc('ict_received_at')
            ->orderByDesc('repair_date')
            ->orderByDesc('finish_date')
            ->orderByDesc('maintenance_date')
            ->orderByDesc('maintenance_id')
            ->get()
            ->map(function (MaintenanceRecord $record) {
                $reportDate = $this->reportDateFor($record);
                $record->report_date = $reportDate;
                $record->report_date_label = $reportDate ? $reportDate->format('d M Y') : '-';
                $record->status_label = $this->normalizedStatus($record->status);

                return $record;
            });

        $summary = $this->faultyReportSummary($records);

        $statusBreakdown = $records
            ->groupBy(fn (MaintenanceRecord $record) => $record->status_label)
            ->map(fn (Collection $items) => $items->count())
            ->sortDesc();

        $monthStarts = collect([$startDate->copy()->startOfMonth()]);
        $monthlyBreakdown = $monthStarts->map(function (Carbon $month) use ($records) {
            $monthRecords = $records->filter(function (MaintenanceRecord $record) use ($month) {
                return $record->report_date instanceof Carbon
                    && $record->report_date->isSameMonth($month);
            });

            return [
                'label' => $month->format('M Y'),
                'total' => $monthRecords->count(),
                'active' => $monthRecords->filter(fn (MaintenanceRecord $record) => in_array($record->status_label, ['UNDER REPAIR', 'FAULTY', 'B.E.R', 'READY TO COLLECT'], true))->count(),
                'done' => $monthRecords->filter(fn (MaintenanceRecord $record) => (bool) $record->done || in_array($record->status_label, ['ALREADY FIXED', 'DONE'], true))->count(),
            ];
        });

        $months = collect(range(1, 12))->map(fn (int $month) => [
            'value' => $month,
            'label' => Carbon::create(null, $month, 1)->format('F'),
        ]);
        $years = collect(range($currentYear + 1, 2000));
        $selectedPeriodLabel = $startDate->format('F Y');

        return view('wt.admin.reports.faulty_3_months', compact(
            'records',
            'summary',
            'statusBreakdown',
            'monthlyBreakdown',
            'startDate',
            'endDate',
            'selectedMonth',
            'selectedYear',
            'selectedPeriodLabel',
            'months',
            'years'
        ));
    }
}


