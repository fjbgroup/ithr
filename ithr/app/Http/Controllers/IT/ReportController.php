<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\EwasteItem;
use App\Models\IT\InventoryItem;
use App\Models\IT\NonItAsset;
use Illuminate\Http\Request;

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

        return view('it.reports.it', compact('totalAssets', 'activeAssets', 'ewastePending', 'ewasteCollected', 'classes', 'statusRows'));
    }

    public function nonIt(Request $request)
    {
        $nitTotal    = NonItAsset::count();
        $nitActive   = NonItAsset::where('item_status', 'Active')->count();
        $nitDisposed = NonItAsset::where('item_status', 'Disposed')->count();
        $nitRepair   = NonItAsset::where('item_status', 'In Repair')->count();
        $nitClasses  = NonItAsset::selectRaw('asset_class, count(*) as c')
                         ->groupBy('asset_class')->orderByDesc('c')->get();
        $nitRecent   = NonItAsset::latest()->limit(20)->get();

        return view('it.reports.non-it', compact('nitTotal', 'nitActive', 'nitDisposed', 'nitRepair', 'nitClasses', 'nitRecent'));
    }
}

