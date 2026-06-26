<?php

namespace App\Http\Controllers\IT;

use App\Exports\ItAssetExport;
use App\Exports\NonItAssetExport;
use App\Models\IT\EwasteItem;
use App\Models\IT\InventoryItem;
use App\Models\IT\NonItAsset;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function it(Request $request)
    {
        $totalAssets     = InventoryItem::count();
        $activeAssets    = InventoryItem::where('item_status', 'Active')->count();
        $ewastePending   = EwasteItem::where('disposal_status', 'Pending')->count();
        $ewasteCollected = EwasteItem::where('disposal_status', 'Disposed')->count();
        $classes         = InventoryItem::selectRaw('asset_class, count(*) as c')
                             ->groupBy('asset_class')->orderByDesc('c')->get();
        $statusRows      = InventoryItem::selectRaw('item_status, count(*) as c')
                             ->groupBy('item_status')->orderByDesc('c')->get();
        $allClasses      = InventoryItem::distinct()->orderBy('asset_class')->pluck('asset_class');

        $q = InventoryItem::query();
        if ($request->filled('status'))    $q->where('item_status', $request->status);
        if ($request->filled('class'))     $q->where('asset_class', $request->class);
        if ($request->filled('date_from')) $q->whereDate('purchase_date', '>=', $request->date_from);
        if ($request->filled('date_to'))   $q->whereDate('purchase_date', '<=', $request->date_to);
        if ($request->filled('search'))    $q->where(function($sq) use ($request) {
            $s = $request->search;
            $sq->where('asset_number', 'like', "%$s%")
               ->orWhere('description', 'like', "%$s%")
               ->orWhere('serial_number', 'like', "%$s%")
               ->orWhere('brand', 'like', "%$s%");
        });
        $items = $q->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        return view('it.reports.it', compact(
            'totalAssets', 'activeAssets', 'ewastePending', 'ewasteCollected',
            'classes', 'statusRows', 'allClasses', 'items'
        ));
    }

    public function exportIt(Request $request)
    {
        $filters = $request->only(['status', 'class', 'date_from', 'date_to', 'search']);
        return Excel::download(new ItAssetExport($filters), 'it-assets-' . now()->format('Ymd') . '.xlsx');
    }

    public function nonIt(Request $request)
    {
        $nitTotal    = NonItAsset::count();
        $nitActive   = NonItAsset::where('item_status', 'Active')->count();
        $nitDisposed = NonItAsset::where('item_status', 'Disposed')->count();
        $nitRepair   = NonItAsset::where('item_status', 'In Repair')->count();
        $nitClasses  = NonItAsset::selectRaw('asset_class, count(*) as c')
                         ->groupBy('asset_class')->orderByDesc('c')->get();
        $allNitClasses = NonItAsset::distinct()->orderBy('asset_class')->pluck('asset_class');

        $q = NonItAsset::query();
        if ($request->filled('status'))    $q->where('item_status', $request->status);
        if ($request->filled('class'))     $q->where('asset_class', $request->class);
        if ($request->filled('date_from')) $q->whereDate('date_registered', '>=', $request->date_from);
        if ($request->filled('date_to'))   $q->whereDate('date_registered', '<=', $request->date_to);
        if ($request->filled('search'))    $q->where(function($sq) use ($request) {
            $s = $request->search;
            $sq->where('asset_number', 'like', "%$s%")
               ->orWhere('description', 'like', "%$s%")
               ->orWhere('serial_number', 'like', "%$s%")
               ->orWhere('brand', 'like', "%$s%");
        });
        $nitItems = $q->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        return view('it.reports.non-it', compact(
            'nitTotal', 'nitActive', 'nitDisposed', 'nitRepair',
            'nitClasses', 'allNitClasses', 'nitItems'
        ));
    }

    public function exportNonIt(Request $request)
    {
        $filters = $request->only(['status', 'class', 'date_from', 'date_to', 'search']);
        return Excel::download(new NonItAssetExport($filters), 'non-it-assets-' . now()->format('Ymd') . '.xlsx');
    }
}
