<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\AddAssetRequest;
use App\Models\IT\ActivityLog;
use App\Models\IT\AssetClass;
use App\Models\IT\DeleteRequest;
use App\Models\IT\DisposalItem;
use App\Models\IT\EditAssetRequest;
use App\Models\IT\EwasteItem;
use App\Models\IT\EwasteRequest;
use App\Models\IT\InventoryItem;
use App\Models\IT\NonItAsset;
use App\Models\IT\PasswordResetRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('it')->user();

        $this->syncInventoryAssetState();

        // Total counts
        $totalIT    = InventoryItem::count();
        $activeIT   = InventoryItem::where('item_status', 'Active')->count();
        $totalNIT   = NonItAsset::count();
        $activeNIT  = NonItAsset::where('item_status', 'Active')->count();
        $ewastePending = EwasteItem::where('disposal_status', 'Pending')->count();

        // Pending approvals count (admin sees all)
        $pendingApprovals = 0;
        if ($user->isAdmin()) {
            $pendingApprovals = AddAssetRequest::where('status', 'Pending')->count()
                + DeleteRequest::where('status', 'Pending')->count()
                + EditAssetRequest::where('status', 'Pending')->count()
                + EwasteRequest::where('status', 'Pending')->count()
                + PasswordResetRequest::where('status', 'pending')->count();
        }

        // Combined totals
        $totalAll  = $totalIT + $totalNIT;
        $activeAll = $activeIT + $activeNIT;
        $pendingAll = $ewastePending + AddAssetRequest::where('status', 'Pending')->count();

        // IT asset class distribution for chart (top 8 by count)
        $itChartRaw = DB::table('inventory_items')
            ->where(fn($q) => $q->whereNull('item_status')->orWhere('item_status', '!=', 'Disposed'))
            ->selectRaw('asset_class, COUNT(*) as cnt')
            ->groupBy('asset_class')
            ->orderByDesc('cnt')
            ->limit(8)
            ->get();
        $itChartData = $itChartRaw->map(fn($r) => ['label' => $r->asset_class, 'value' => (int)$r->cnt])->values();
        $itTotal     = $totalIT;

        // Non-IT class distribution
        $nitChartRaw = DB::table('non_it_assets')
            ->selectRaw('asset_class, COUNT(*) as cnt')
            ->groupBy('asset_class')
            ->orderByDesc('cnt')
            ->limit(8)
            ->get();
        $nitChartData = $nitChartRaw->map(fn($r) => ['label' => $r->asset_class, 'value' => (int)$r->cnt])->values();
        $nitTotal     = $totalNIT;

        // Recent activity
        $recentActivity = ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(function ($a) {
                $a->full_name = $a->user?->full_name ?? 'System';
                return $a;
            });

        // Recently added assets
        $recentAssets = InventoryItem::orderByDesc('created_at')->limit(6)->get();

        // User-specific stats
        $myEwastePending  = EwasteItem::where('created_by', $user->id)->where('disposal_status', 'Pending')->count();
        $myEwasteApproved = EwasteItem::where('created_by', $user->id)->where('disposal_status', 'Approved')->count();
        $myItReq          = AddAssetRequest::where('requested_by', $user->id)->where('status', 'Pending')->count();

        // Disposal items count (finance)
        $disposalCount = DisposalItem::count();

        return view('it.dashboard.index', compact(
            'totalIT', 'activeIT', 'totalNIT', 'ewastePending', 'pendingApprovals',
            'totalAll', 'activeAll', 'pendingAll',
            'itChartData', 'nitChartData', 'itTotal', 'nitTotal',
            'recentActivity', 'recentAssets',
            'myEwastePending', 'myEwasteApproved', 'myItReq', 'disposalCount'
        ));
    }

    private function syncInventoryAssetState(): void
    {
        $orphans = EwasteItem::whereNull('original_inventory_id')
            ->where(fn($q) => $q->whereNull('asset_source')->orWhere('asset_source', 'IT'))
            ->get();

        foreach ($orphans as $ew) {
            $inv = InventoryItem::create([
                'asset_number'     => $ew->asset_number,
                'asset_class'      => $ew->asset_class,
                'description'      => $ew->description,
                'serial_number'    => $ew->serial_number,
                'item_status'      => 'Disposed',
                'condition_status' => $ew->condition_on_disposal ?: 'For Disposal',
                'notes'            => $ew->notes,
                'created_by'       => $ew->created_by,
            ]);
            $ew->update(['original_inventory_id' => $inv->id]);
        }

        $selectLocationItems = InventoryItem::where(function ($q) {
                $q->whereNull('location')->orWhere('location', '');
            })
            ->where(function ($q) {
                $q->where('item_status', '!=', 'Active')
                  ->orWhereHas('ewasteItems', fn($ew) => $ew->whereIn('disposal_status', ['Approved', 'Collected', 'Rejected']));
            })
            ->get();

        foreach ($selectLocationItems as $item) {
            $item->ewasteItems()
                ->whereIn('disposal_status', ['Approved', 'Collected', 'Rejected'])
                ->delete();

            if ($item->item_status !== 'Active') {
                $item->update(['item_status' => 'Active']);
            }
        }
    }
}
