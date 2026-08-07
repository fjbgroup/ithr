<?php

namespace App\Exports;

use App\Models\IT\ItPeripheralLoan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class ItPeripheralLoanExport implements FromQuery, WithHeadings, WithTitle, ShouldAutoSize, WithMapping
{
    public function __construct(protected array $filters = []) {}

    public function query()
    {
        $q = ItPeripheralLoan::with(['staff.department', 'inventoryItem'])->orderBy('created_at', 'desc');

        if (!empty($this->filters['year'])) {
            $q->whereYear('created_at', $this->filters['year']);
        }
        if (!empty($this->filters['month'])) {
            $q->whereMonth('created_at', $this->filters['month']);
        }
        if (!empty($this->filters['company_code'])) {
            $q->whereHas('staff.department', function ($sq) {
                $sq->where('company', $this->filters['company_code']);
            });
        }
        if (!empty($this->filters['department_id'])) {
            $q->whereHas('staff', function ($sq) {
                $sq->where('department_id', $this->filters['department_id']);
            });
        }
        if (!empty($this->filters['asset_class'])) {
            $q->whereHas('inventoryItem', function ($sq) {
                $sq->where('asset_class', $this->filters['asset_class']);
            });
        }
        return $q;
    }

    public function map($loan): array
    {
        return [
            $loan->created_at->format('d/m/Y'),
            $loan->referral_code,
            $loan->staff->name ?? 'Unknown',
            $loan->staff->department->name ?? 'Unknown',
            $loan->inventoryItem->description ?? 'Unknown',
            $loan->inventoryItem->asset_class ?? 'Unknown',
            $loan->inventoryItem->asset_number ?? 'Unknown',
            $loan->loan_start_date ? $loan->loan_start_date->format('d/m/Y') : '',
            $loan->loan_end_date ? $loan->loan_end_date->format('d/m/Y') : '',
            $loan->status,
        ];
    }

    public function headings(): array
    {
        return [
            'Date Requested', 'Referral Code', 'Staff Name', 'Department',
            'Item Description', 'Asset Class', 'Asset Number',
            'Loan Start', 'Loan End', 'Status',
        ];
    }

    public function title(): string
    {
        return 'IT Peripheral Loans';
    }
}
