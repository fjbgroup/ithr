<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\AddAssetRequest;
use App\Models\IT\AssetClass;
use App\Models\IT\DeleteRequest;
use App\Models\IT\EditAssetRequest;
use App\Models\IT\EwasteItem;
use App\Models\IT\EwasteRequest;
use App\Models\IT\InventoryItem;
use App\Services\IT\ActivityLogService;
use App\Services\IT\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $user   = Auth::guard('it')->user();
        $query  = InventoryItem::with(['ewasteItems' => fn($q) => $q->orderByDesc('id')]);

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

        $items       = $query->orderByDesc('created_at')->paginate(25)->withQueryString();
        $assetClasses = AssetClass::where('type', 'it')->orderBy('sort_order')->get();

        if ($request->boolean('partial')) {
            return response(view('it.inventory.partials.live-table', compact('items', 'user'))->render());
        }

        // Pending requests for admin view (separate per type)
        $pendingAdds    = collect();
        $pendingEw      = collect();
        $pendingDeletes = collect();
        $pendingEdits   = collect();
        $pendingAddCount = $pendingEwCount = $pendingDelCount = $pendingEditCount = $totalPending = 0;

        if ($user->isAdmin()) {
            $pendingAdds    = AddAssetRequest::where('status', 'Pending')->with('requester')->orderByDesc('created_at')->get();
            $pendingEw      = EwasteRequest::where('status', 'Pending')->with('requester')->orderByDesc('created_at')->get();
            $pendingDeletes = DeleteRequest::where('status', 'Pending')->with('requester', 'inventoryItem')->orderByDesc('created_at')->get();
            $pendingEdits   = EditAssetRequest::where('status', 'Pending')->where('asset_type', 'it')->with('requester', 'inventoryItem')->orderByDesc('created_at')->get();

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
            $myAdds    = AddAssetRequest::where('requested_by', $user->id)->orderByDesc('created_at')->get();
            $myEw      = EwasteRequest::where('requested_by', $user->id)->orderByDesc('created_at')->get();
            $myDeletes = DeleteRequest::where('requested_by', $user->id)->with('inventoryItem')->orderByDesc('created_at')->get();
            $myEdits   = EditAssetRequest::where('requested_by', $user->id)->where('asset_type', 'it')->with('inventoryItem')->orderByDesc('created_at')->get();
            $myDisposals = EwasteItem::where('created_by', $user->id)->orderByDesc('created_at')->get();

            $myPending = $myAdds->where('status', 'Pending')->count()
                       + $myEw->where('status', 'Pending')->count()
                       + $myDeletes->where('status', 'Pending')->count()
                       + $myEdits->where('status', 'Pending')->count();
            $totalMy   = $myAdds->count() + $myEw->count() + $myDeletes->count() + $myEdits->count() + $myDisposals->count();
        }

        return view('it.inventory.index', compact(
            'items', 'assetClasses',
            'pendingAdds', 'pendingEw', 'pendingDeletes', 'pendingEdits',
            'pendingAddCount', 'pendingEwCount', 'pendingDelCount', 'pendingEditCount', 'totalPending',
            'myAdds', 'myEw', 'myDeletes', 'myEdits', 'myDisposals', 'myPending', 'totalMy'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('it')->user();

        $data = $request->validate([
            'asset_number'     => 'nullable|string|max:50',
            'asset_class'      => 'required|string|max:50',
            'fa_code'          => 'nullable|string|max:100',
            'description'      => 'required|string|max:255',
            'serial_number'    => 'nullable|string|max:100',
            'brand'            => 'nullable|string|max:100',
            'model'            => 'nullable|string|max:100',
            'location'         => 'nullable|string|max:100',
            'condition_status' => 'nullable|in:Good,Fair,Poor,For Disposal',
            'item_status'      => 'nullable|in:Active,In Repair,Disposed,Reserved,Collected',
            'purchase_date'    => 'nullable|date',
            'purchase_price'   => 'nullable|numeric',
            'years_purchase'   => 'nullable|integer',
            'total_cost'       => 'nullable|numeric',
            'accumulated'      => 'nullable|numeric',
            'nbv_at'           => 'nullable|numeric',
            'notes'            => 'nullable|string',
        ]);

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
        $item = InventoryItem::findOrFail($id);

        $data = $request->validate([
            'asset_number'     => 'nullable|string|max:50',
            'asset_class'      => 'required|string|max:50',
            'fa_code'          => 'nullable|string|max:100',
            'description'      => 'required|string|max:255',
            'serial_number'    => 'nullable|string|max:100',
            'brand'            => 'nullable|string|max:100',
            'model'            => 'nullable|string|max:100',
            'location'         => 'nullable|string|max:100',
            'condition_status' => 'nullable|in:Good,Fair,Poor,For Disposal',
            'item_status'      => 'nullable|in:Active,In Repair,Disposed,Reserved,Collected',
            'purchase_date'    => 'nullable|date',
            'purchase_price'   => 'nullable|numeric',
            'years_purchase'   => 'nullable|integer',
            'total_cost'       => 'nullable|numeric',
            'accumulated'      => 'nullable|numeric',
            'nbv_at'           => 'nullable|numeric',
            'notes'            => 'nullable|string',
        ]);

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
        $item = InventoryItem::findOrFail($id);

        if ($user->isAdmin()) {
            ActivityLogService::log('DELETE', 'inventory', $id, 'Deleted asset: '.$item->description);
            $item->delete();
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
                ActivityLogService::log('DELETE', 'inventory', $id, 'Bulk deleted: '.$item->description);
                $item->delete();
                $count++;
            }
        }
        return back()->with('success', "$count asset(s) deleted.");
    }

    public function importTemplate()
    {
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

