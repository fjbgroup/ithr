<?php

namespace App\Exports;

use App\Models\IT\InventoryItem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class ItAssetExport implements FromQuery, WithHeadings, WithTitle, ShouldAutoSize, WithMapping
{
    public function __construct(protected array $filters = []) {}

    public function query()
    {
        $q = InventoryItem::query();
        if (!empty($this->filters['status']))     $q->where('item_status', $this->filters['status']);
        if (!empty($this->filters['class']))      $q->where('asset_class', $this->filters['class']);
        if (!empty($this->filters['date_from']))  $q->whereDate('purchase_date', '>=', $this->filters['date_from']);
        if (!empty($this->filters['date_to']))    $q->whereDate('purchase_date', '<=', $this->filters['date_to']);
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
            $item->domain,
            $item->ip_address,
            $item->os_code,
            $item->sp,
            $item->description,
            $item->asset_type,
            $item->asset_name,
            $item->fqdn,
            $item->mac_address,
            $item->memory_mb,
            $item->nr_processors,
            $item->processor,
            $item->state,
            $item->purchase_date?->format('d/m/Y'),
            $item->warranty_date?->format('d/m/Y'),
            $item->last_patched,
            $item->last_full_backup,
            $item->last_full_image,
            $item->order_number,
            $item->comments,
            $item->location,
            $item->building,
            $item->department,
            $item->branch_office,
            $item->bar_code,
            $item->manufacturer,
            $item->contact,
            $item->model,
            $item->serial_number,
            $item->scan_server,
            $item->chrome_os_device_id,
            $item->system_sku,
        ];
    }

    public function headings(): array
    {
        return [
            'Domain', 'IPAddress', 'OScode', 'SP', 'Description', 'Assettype', 'AssetName',
            'FQDN', 'Mac', 'Memory (GB)', 'NrProcessors', 'Processor', 'State',
            'PurchaseDate', 'Warrantydate', 'LastPatched', 'LastFullbackup', 'LastFullimage',
            'OrderNumber', 'Comments', 'Location', 'Building', 'Department', 'Branchoffice',
            'BarCode', 'Manufacturer', 'Contact', 'Model', 'Serialnumber', 'Scanserver',
            'Chrome OS Device ID', 'System SKU',
        ];
    }

    public function title(): string
    {
        return 'IT Assets';
    }
}
