<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\AddAssetRequest;
use App\Models\IT\AssetClass;
use App\Models\IT\Brand;
use App\Models\IT\DeleteRequest;
use App\Models\IT\EditAssetRequest;
use App\Models\IT\EwasteItem;
use App\Models\IT\EwasteRequest;
use App\Models\IT\InventoryItem;
use App\Models\IT\Location;
use App\Services\IT\ActivityLogService;
use App\Services\IT\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $user   = Auth::guard('it')->user();

        // Backfill: create InventoryItems for any EwasteItems that have no linked inventory record
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

        $globalYear = session('global_year', 'all');
        $applyYear = function ($query) use ($globalYear) {
            if ($globalYear !== 'all') {
                $query->whereYear('created_at', $globalYear);
            }
        };

        $query  = InventoryItem::with(['ewasteItems' => fn($q) => $q->orderByDesc('id')])->where($applyYear);

        if ($s = $request->search) {
            $query->where(function ($q) use ($s) {
                $q->where('asset_number', 'like', "%$s%")
                  ->orWhere('description', 'like', "%$s%")
                  ->orWhere('serial_number', 'like', "%$s%")
                  ->orWhere('brand', 'like', "%$s%");
            });
        }
        if ($c = $request->get('class')) $query->where('asset_class', $c);
        if ($st = $request->get('status')) {
            match ($st) {
                'Active'    => $query->where('item_status', 'Active')
                                     ->whereDoesntHave('ewasteItems'),
                'E-Waste'   => $query->whereHas('ewasteItems', fn($q) => $q->where('disposal_status', 'Approved')),
                'Pending'   => $query->whereHas('ewasteItems', fn($q) => $q->where('disposal_status', 'Pending')),
                'Collected' => $query->whereHas('ewasteItems', fn($q) => $q->where('disposal_status', 'Collected')),
                default     => $query->where('item_status', $st),
            };
        }
        if ($loc = $request->get('location')) $query->where('location', $loc);

        $items        = $query->orderByDesc('created_at')->paginate(25)->withQueryString();
        $assetClasses = AssetClass::where('type', 'it')->orderBy('sort_order')->get();
        $brands       = Brand::orderBy('sort_order')->orderBy('name')->get();
        $locations    = Location::orderBy('sort_order')->orderBy('name')->get();

        $pendingEwIds = EwasteRequest::where($applyYear)->where('status', 'Pending')
            ->whereNotNull('inventory_id')
            ->pluck('inventory_id')
            ->mapWithKeys(fn($id) => [(int) $id => true])
            ->all();

        $pendingEditIds = EditAssetRequest::where($applyYear)->where(function ($q) {
                $q->whereNull('asset_type')->orWhere('asset_type', 'it');
            })
            ->where('status', 'Pending')
            ->whereNotNull('asset_id')
            ->pluck('asset_id')
            ->mapWithKeys(fn($id) => [(int) $id => true])
            ->all();

        $pendingDeleteIds = DeleteRequest::where($applyYear)->where('status', 'Pending')
            ->whereNotNull('inventory_id')
            ->pluck('inventory_id')
            ->mapWithKeys(fn($id) => [(int) $id => true])
            ->all();

        if ($request->boolean('partial')) {
            return response(view('it.inventory.partials.live-table', compact('items', 'user', 'pendingEwIds', 'pendingEditIds', 'pendingDeleteIds'))->render());
        }

        // Pending requests for admin view (separate per type)
        $pendingAdds    = collect();
        $pendingEw      = collect();
        $pendingDeletes = collect();
        $pendingEdits   = collect();
        $pendingAddCount = $pendingEwCount = $pendingDelCount = $pendingEditCount = $totalPending = 0;

        if ($user->isAdmin()) {
            $pendingAdds    = AddAssetRequest::where($applyYear)->where('status', 'Pending')->with('requester')->orderByDesc('created_at')->get();
            $pendingEw      = EwasteRequest::where($applyYear)->where('status', 'Pending')->with('requester')->orderByDesc('created_at')->get();
            $pendingDeletes = DeleteRequest::where($applyYear)->where('status', 'Pending')->with('requester', 'inventoryItem')->orderByDesc('created_at')->get();
            $pendingEdits   = EditAssetRequest::where($applyYear)->where('status', 'Pending')->with('requester', 'inventoryItem')->orderByDesc('created_at')->get();

            $pendingAddCount  = $pendingAdds->count();
            $pendingEwCount   = $pendingEw->count();
            $pendingDelCount  = $pendingDeletes->count();
            $pendingEditCount = $pendingEdits->count();
            $totalPending     = $pendingAddCount + $pendingEwCount + $pendingDelCount + $pendingEditCount;
        }

        // My requests for non-admin (separate per type)
        $myAdds     = collect();
        $myEw       = collect();
        $myDeletes  = collect();
        $myEdits    = collect();
        $myDisposals = collect();
        $myPending  = 0;
        $totalMy    = 0;

        if (!$user->isAdmin() && !$user->isReadOnlyViewer()) {
            $myAdds    = AddAssetRequest::where($applyYear)->where('requested_by', $user->id)->orderByDesc('created_at')->get();
            $myEw      = EwasteRequest::where($applyYear)->where('requested_by', $user->id)->orderByDesc('created_at')->get();
            $myDeletes = DeleteRequest::where($applyYear)->where('requested_by', $user->id)->with('inventoryItem')->orderByDesc('created_at')->get();
            $myEdits   = EditAssetRequest::where($applyYear)->where('requested_by', $user->id)->with('inventoryItem')->orderByDesc('created_at')->get();
            $myDisposals = EwasteItem::where($applyYear)->where('created_by', $user->id)->orderByDesc('created_at')->get();

            $myPending = $myAdds->where('status', 'Pending')->count()
                       + $myEw->where('status', 'Pending')->count()
                       + $myDeletes->where('status', 'Pending')->count()
                       + $myEdits->where('status', 'Pending')->count();
            $totalMy   = $myAdds->count() + $myEw->count() + $myDeletes->count() + $myEdits->count() + $myDisposals->count();
        }

        return view('it.inventory.index', compact(
            'items', 'assetClasses', 'brands', 'locations',
            'pendingAdds', 'pendingEw', 'pendingDeletes', 'pendingEdits',
            'pendingAddCount', 'pendingEwCount', 'pendingDelCount', 'pendingEditCount', 'totalPending',
            'myAdds', 'myEw', 'myDeletes', 'myEdits', 'myDisposals', 'myPending', 'totalMy',
            'pendingEwIds', 'pendingEditIds', 'pendingDeleteIds'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('it')->user();
        if ($user->isReadOnlyViewer()) abort(403);

        $data = $request->validate([
            'asset_name'       => 'required|string|max:255',
            'asset_type'       => 'required|string|max:100',
            'model'            => 'required|string|max:100',
            'serial_number'    => 'required|string|max:100',
            'domain'           => 'nullable|string|max:100',
            'ip_address'       => 'nullable|string|max:100',
            'os_code'          => 'nullable|string|max:100',
            'sp'               => 'nullable|string|max:100',
            'description'      => 'required|string|max:255',
            'fqdn'             => 'nullable|string|max:255',
            'mac_address'      => 'nullable|string|max:100',
            'memory_mb'        => 'nullable|string|max:100',
            'nr_processors'    => 'nullable|string|max:100',
            'processor'        => 'nullable|string|max:100',
            'state'            => 'nullable|string|max:100',
            'purchase_date'    => 'nullable|date',
            'warranty_date'    => 'nullable|date',
            'last_patched'     => 'nullable|string|max:100',
            'last_full_backup' => 'nullable|string|max:100',
            'last_full_image'  => 'nullable|string|max:100',
            'order_number'     => 'nullable|string|max:100',
            'comments'         => 'nullable|string',
            'location'         => 'nullable|string|max:100',
            'building'         => 'nullable|string|max:100',
            'department'       => 'nullable|string|max:100',
            'branch_office'    => 'nullable|string|max:100',
            'bar_code'         => 'nullable|string|max:100',
            'manufacturer'     => 'nullable|string|max:100',
            'contact'          => 'nullable|string|max:100',
            'scan_server'      => 'nullable|string|max:100',
            'chrome_os_device_id' => 'nullable|string|max:255',
            'system_sku'       => 'nullable|string|max:100',
        ]);
        
        $data['asset_class'] = $data['asset_type'] ?? null;
        $data['asset_number'] = 'AST-' . date('ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
        $data['state'] = 'Active';
        $data['item_status'] = 'Active';

        if ($user->isAdmin()) {
            $data['created_by'] = $user->id;
            $item = InventoryItem::create($data);
            ActivityLogService::log('CREATE', 'inventory', $item->id, 'Added asset: '.$item->description);
            return back()->with('success', 'Asset added successfully.');
        }

        // Non-admin: submit request
        AddAssetRequest::create(array_merge($data, ['requested_by' => $user->id]));
        NotificationService::notifyAdmins('add_request', 'New Asset Add Request', $user->full_name.' requested to add a new asset: '.($data['description'] ?? ''), route('it.inventory.index'));
        ActivityLogService::log('REQUEST_ADD', 'inventory', 0, 'Submitted add asset request: '.($data['description'] ?? ''));
        return back()->with('success', 'Request submitted for admin approval.');
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::guard('it')->user();
        if ($user->isReadOnlyViewer()) abort(403);
        $item = InventoryItem::findOrFail($id);
        $ewStatus = $item->ewasteItems()->latest('id')->value('disposal_status');
        if (!$user->isAdminOrFinance() && ($item->item_status === 'Pending for Write-Off' || $ewStatus !== null)) abort(403);

        $data = $request->validate([
            'asset_name'       => 'required|string|max:255',
            'asset_type'       => 'required|string|max:100',
            'model'            => 'required|string|max:100',
            'serial_number'    => 'required|string|max:100',
            'domain'           => 'nullable|string|max:100',
            'ip_address'       => 'nullable|string|max:100',
            'os_code'          => 'nullable|string|max:100',
            'sp'               => 'nullable|string|max:100',
            'description'      => 'required|string|max:255',
            'fqdn'             => 'nullable|string|max:255',
            'mac_address'      => 'nullable|string|max:100',
            'memory_mb'        => 'nullable|string|max:100',
            'nr_processors'    => 'nullable|string|max:100',
            'processor'        => 'nullable|string|max:100',
            'state'            => 'nullable|string|max:100',
            'purchase_date'    => 'nullable|date',
            'warranty_date'    => 'nullable|date',
            'last_patched'     => 'nullable|string|max:100',
            'last_full_backup' => 'nullable|string|max:100',
            'last_full_image'  => 'nullable|string|max:100',
            'order_number'     => 'nullable|string|max:100',
            'comments'         => 'nullable|string',
            'location'         => 'nullable|string|max:100',
            'building'         => 'nullable|string|max:100',
            'department'       => 'nullable|string|max:100',
            'branch_office'    => 'nullable|string|max:100',
            'bar_code'         => 'nullable|string|max:100',
            'manufacturer'     => 'nullable|string|max:100',
            'contact'          => 'nullable|string|max:100',
            'scan_server'      => 'nullable|string|max:100',
            'chrome_os_device_id' => 'nullable|string|max:255',
            'system_sku'       => 'nullable|string|max:100',
        ]);
        
        $data['asset_class'] = $data['asset_type'] ?? null;

        if (array_key_exists('location', $data) && trim((string) $data['location']) === '') {
            $data['location'] = null;
            $data['item_status'] = 'Active';
            $item->ewasteItems()
                ->whereIn('disposal_status', ['Approved', 'Collected', 'Rejected'])
                ->delete();
        }

        if ($user->isAdmin()) {
            $item->update($data);
            ActivityLogService::log('UPDATE', 'inventory', $item->id, 'Updated asset: '.$item->description);
            return back()->with('success', 'Asset updated.');
        }

        // Non-admin: submit edit request
        EditAssetRequest::create(array_merge($data, [
            'asset_type'   => 'it',
            'asset_id'     => $id,
            'requested_by' => $user->id,
        ]));
        NotificationService::notifyAdmins('edit_request', 'Asset Edit Request', $user->full_name.' requested to edit: '.$item->description, route('it.inventory.index'));
        ActivityLogService::log('REQUEST_EDIT', 'inventory', $id, 'Submitted edit request for: '.$item->description);
        return back()->with('success', 'Edit request submitted for admin approval.');
    }

    public function destroy(int $id)
    {
        $user = Auth::guard('it')->user();
        if ($user->isReadOnlyViewer()) abort(403);
        $item = InventoryItem::findOrFail($id);
        $ewStatus = $item->ewasteItems()->latest('id')->value('disposal_status');
        if (!$user->isAdminOrFinance() && ($item->item_status === 'Pending for Write-Off' || $ewStatus !== null)) abort(403);

        if ($user->isAdmin()) {
            DB::transaction(function () use ($item, $id) {
                EwasteItem::where('original_inventory_id', $id)
                    ->where('disposal_status', 'Pending')
                    ->delete();
                EwasteRequest::where('inventory_id', $id)->where('status', 'Pending')->delete();
                EditAssetRequest::where(function ($q) {
                        $q->whereNull('asset_type')->orWhere('asset_type', 'it');
                    })
                    ->where('asset_id', $id)
                    ->where('status', 'Pending')
                    ->delete();
                DeleteRequest::where('inventory_id', $id)->where('status', 'Pending')->delete();
                ActivityLogService::log('DELETE', 'inventory', $id, 'Deleted asset: '.$item->description);
                $item->delete();
            });
            return back()->with('success', 'Asset deleted.');
        }

        DeleteRequest::create([
            'inventory_id'    => $id,
            'requested_by'    => $user->id,
            'reason'          => request('reason', ''),
            'asset_number'    => $item->asset_number,
            'asset_class'     => $item->asset_class,
            'asset_description' => $item->description,
        ]);
        NotificationService::notifyAdmins('delete_request', 'Asset Delete Request', $user->full_name.' requested to delete: '.$item->description, route('it.inventory.index'));
        ActivityLogService::log('REQUEST_DELETE', 'inventory', $id, 'Submitted delete request for: '.$item->description);
        return back()->with('success', 'Delete request submitted for admin approval.');
    }

    public function bulkDestroy(Request $request)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdmin()) abort(403);
        $ids   = array_map('intval', $request->selected_ids ?? []);
        $count = 0;
        foreach ($ids as $id) {
            $item = InventoryItem::find($id);
            if ($item) {
                DB::transaction(function () use ($item, $id) {
                    EwasteItem::where('original_inventory_id', $id)
                        ->where('disposal_status', 'Pending')
                        ->delete();
                    EwasteRequest::where('inventory_id', $id)->where('status', 'Pending')->delete();
                    EditAssetRequest::where(function ($q) {
                            $q->whereNull('asset_type')->orWhere('asset_type', 'it');
                        })
                        ->where('asset_id', $id)
                        ->where('status', 'Pending')
                        ->delete();
                    DeleteRequest::where('inventory_id', $id)->where('status', 'Pending')->delete();
                    ActivityLogService::log('DELETE', 'inventory', $id, 'Bulk deleted: '.$item->description);
                    $item->delete();
                });
                $count++;
            }
        }
        return back()->with('success', "$count asset(s) deleted.");
    }

    public function importTemplate()
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdminOrFinance()) abort(403);

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="it_assets_template.csv"'];
        $csv = "Asset Number,Asset Class,F/A Code,Description,Serial Number,Brand,Model,Location,Years Purchase,Total Cost,Accumulated,NBV AT,Notes\n";
        $csv .= "OEPC1401,PC,4100000047,HP EliteOne 800 G2 AIO,SGH629QBBY,HP,EliteOne 800 G2,Server Room 1,2018,5000.00,2000.00,3000.00,Sample row\n";
        return response($csv, 200, $headers);
    }

    public function import(Request $request)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdminOrFinance()) abort(403);

        $rows     = $request->json()->all();
        $inserted = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($rows as $i => $row) {
            $rn = $i + 2;
            $assetNumber  = trim($row['asset_number'] ?? '');
            $assetClass   = strtoupper(trim($row['asset_class'] ?? ''));
            $description  = trim($row['description'] ?? '');

            if (empty($description) && empty($assetClass) && empty($assetNumber)) {
                $skipped++; continue;
            }

            if ($assetNumber && InventoryItem::where('asset_number', $assetNumber)->exists()) {
                $errors[] = "Row $rn: Asset No. '$assetNumber' already exists â€” skipped."; $skipped++; continue;
            }
            $sn = trim($row['serial_number'] ?? '');
            if ($sn && InventoryItem::where('serial_number', $sn)->exists()) {
                $errors[] = "Row $rn: Serial '$sn' already registered â€” skipped."; $skipped++; continue;
            }

            InventoryItem::create([
                'asset_number'  => $assetNumber ?: null,
                'asset_class'   => $assetClass,
                'fa_code'       => trim($row['fa_code'] ?? '') ?: null,
                'description'   => $description,
                'serial_number' => $sn ?: null,
                'brand'         => trim($row['brand'] ?? '') ?: null,
                'model'         => trim($row['model'] ?? '') ?: null,
                'location'      => trim($row['location'] ?? '') ?: null,
                'years_purchase'=> !empty($row['years_purchase']) ? (int)$row['years_purchase'] : null,
                'total_cost'    => !empty($row['total_cost']) ? (float)$row['total_cost'] : null,
                'accumulated'   => !empty($row['accumulated']) ? (float)$row['accumulated'] : null,
                'nbv_at'        => !empty($row['nbv_at']) ? (float)$row['nbv_at'] : null,
                'notes'         => trim($row['notes'] ?? '') ?: null,
                'condition_status' => 'Good',
                'item_status'   => 'Active',
                'created_by'    => $user->id,
            ]);
            $inserted++;
        }

        if ($inserted > 0) {
            ActivityLogService::log('CREATE', 'inventory', 0, "Excel import: $inserted assets added");
        }

        return response()->json(['inserted' => $inserted, 'skipped' => $skipped, 'errors' => $errors]);
    }

    public function searchSuggestions(Request $request)
    {
        $q = trim($request->query('q', ''));
        if (strlen($q) < 2) return response()->json([]);

        $items = InventoryItem::where(function ($query) use ($q) {
                $query->where('asset_number', 'like', "%$q%")
                      ->orWhere('description', 'like', "%$q%")
                      ->orWhere('serial_number', 'like', "%$q%")
                      ->orWhere('brand', 'like', "%$q%");
            })
            ->orderByRaw("CASE WHEN asset_number LIKE ? THEN 0 ELSE 1 END", ["$q%"])
            ->limit(8)
            ->get(['asset_number', 'description', 'brand']);

        return response()->json($items->map(fn($i) => [
            'label' => trim($i->asset_number . ' â€” ' . $i->description . ($i->brand ? ' (' . $i->brand . ')' : '')),
            'value' => $i->asset_number ?: $i->description,
        ]));
    }

    public function descriptionSuggestions(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) return response()->json([]);
        return response()->json(
            InventoryItem::where('description', 'like', "%$q%")
                ->orderBy('description')->distinct()->limit(8)->pluck('description')
        );
    }

    public function brandSuggestions(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 1) return response()->json([]);
        return response()->json(
            InventoryItem::whereNotNull('brand')->where('brand', 'like', "%$q%")
                ->orderBy('brand')->distinct()->limit(8)->pluck('brand')
        );
    }

    public function modelSuggestions(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 1) return response()->json([]);
        return response()->json(
            InventoryItem::whereNotNull('model')->where('model', 'like', "%$q%")
                ->orderBy('model')->distinct()->limit(8)->pluck('model')
        );
    }
}
