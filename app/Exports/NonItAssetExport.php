<?php

namespace App\Exports;

use App\Models\IT\NonItAsset;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class NonItAssetExport implements FromQuery, WithHeadings, WithTitle, ShouldAutoSize, WithMapping
{
    public function __construct(protected array $filters = []) {}

    public function query()
    {
        $q = NonItAsset::query();
        if (!empty($this->filters['status']))     $q->where('item_status', $this->filters['status']);
        if (!empty($this->filters['class']))      $q->where('asset_class', $this->filters['class']);
        if (!empty($this->filters['date_from']))  $q->whereDate('date_registered', '>=', $this->filters['date_from']);
        if (!empty($this->filters['date_to']))    $q->whereDate('date_registered', '<=', $this->filters['date_to']);
        if (!empty($this->filters['search']))     $q->where(function($sq) {
            $s = $this->filters['search'];
            $sq->where('asset_number', 'like', "%$s%")
               ->orWhere('description', 'like', "%$s%")
               ->orWhere('serial_number', 'like', "%$s%")
               ->orWhere('brand', 'like', "%$s%");
        });
        return $q->orderBy('created_at', 'desc');
    }

    public function map($item): array
    {
        return [
            $item->asset_number,
            $item->asset_class,
            $item->description,
            $item->serial_number,
            $item->brand,
            $item->model,
            $item->location,
            $item->item_status,
            $item->condition_status,
            $item->date_registered?->format('d/m/Y'),
            $item->fa_code,
            $item->total_cost,
            $item->accumulated,
            $item->nbv_at,
        ];
    }

    public function headings(): array
    {
        return [
            'Asset Number', 'Asset Class', 'Description', 'Serial Number',
            'Brand', 'Model', 'Location', 'Status', 'Condition',
            'Date Registered', 'FA Code',
            'Total Cost', 'Accumulated', 'NBV',
        ];
    }

    public function title(): string
    {
        return 'Non-IT Assets';
    }
}
