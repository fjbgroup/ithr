<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\AssetClass;
use App\Models\IT\Brand;
use App\Models\IT\EditAssetRequest;
use App\Models\IT\Location;
use App\Models\IT\NonItAsset;
use App\Services\IT\ActivityLogService;
use App\Services\IT\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NonItAssetController extends Controller
{
    public function index(Request $request)
    {
        $search   = trim($request->nit_search   ?? '');
        $class    = trim($request->nit_class    ?? '');
        $status   = trim($request->nit_status   ?? '');
        $location = trim($request->nit_location ?? '');

        $query = NonItAsset::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('asset_number', 'like', "%$search%")
                  ->orWhere('description',  'like', "%$search%")
                  ->orWhere('asset_class',  'like', "%$search%")
                  ->orWhere('location',     'like', "%$search%");
            });
        }
        if ($class)    $query->where('asset_class', $class);
        if ($status)   $query->where('item_status', $status);
        if ($location) $query->where('location', $location);

        $items       = $query->orderByDesc('created_at')->get();
        $nit_total      = NonItAsset::count();
        $nit_active     = NonItAsset::where('item_status', 'Active')->count();
        $nit_repair     = NonItAsset::where('item_status', 'In Repair')->count();
        $nit_disp       = NonItAsset::where('item_status', 'Disposed')->count();
        $nit_pending_wo     = NonItAsset::where('item_status', 'Pending for Write-Off')->count();
        $nit_pending_ewaste = NonItAsset::where('item_status', 'Pending to E-Waste/Disposal')->count();

        $filtered_total = $items->count();

        $assetClasses   = AssetClass::where('type', 'non_it')->orderBy('sort_order')->orderBy('name')->get();
        $brands         = Brand::orderBy('sort_order')->orderBy('name')->get();
        $locations      = Location::orderBy('sort_order')->orderBy('name')->get();
        $nitClassesUsed = NonItAsset::distinct()->orderBy('asset_class')->pluck('asset_class')
                            ->filter()->values();
        $allLocations   = $locations->pluck('name');

        $pendingEditIds = EditAssetRequest::where('asset_type', 'non_it')
                            ->where('status', 'Pending')
                            ->pluck('asset_id')
                            ->mapWithKeys(fn($id) => [$id => true])
                            ->all();

        if ($request->boolean('partial')) {
            $user = Auth::guard('it')->user();
            return response(view('it.non-it-assets.partials.live-table', compact('items', 'user', 'pendingEditIds', 'nit_total', 'search', 'class', 'status'))->render());
        }

        return view('it.non-it-assets.index', compact(
            'items', 'assetClasses', 'brands', 'locations',
            'nit_total', 'nit_active', 'nit_repair', 'nit_disp', 'nit_pending_wo', 'nit_pending_ewaste', 'filtered_total',
            'nitClassesUsed', 'pendingEditIds', 'allLocations',
            'search', 'class', 'status', 'location'
        ));
    }

    public function suggestions(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 1) return response()->json([]);

        $like    = "%$q%";
        $results = [];

        foreach (['asset_number', 'description', 'asset_class', 'location'] as $col) {
            $vals = \App\Models\NonItAsset::whereNotNull($col)
                ->where($col, 'like', $like)
                ->orderBy($col)
                ->distinct()
                ->limit(4)
                ->pluck($col);
            foreach ($vals as $v) {
                $results[] = trim($v);
            }
        }

        $results = array_values(array_unique(array_filter($results)));
        sort($results);

        return response()->json(array_slice($results, 0, 10));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('it')->user();
        $data = $request->validate([
            'asset_number'     => 'nullable|string|max:50',
            'asset_class'      => 'required|string|max:100',
            'fa_code'          => 'nullable|string|max:100',
            'description'      => 'required|string|max:255',
            'brand'            => 'nullable|string|max:100',
            'location'         => 'nullable|string|max:100',
            'item_status'      => 'nullable|in:Active,In Repair,Disposed,Reserved',
            'condition_status' => 'nullable|string|max:50',
            'date_registered'  => 'nullable|date',
            'years_purchase'   => 'nullable|integer',
            'total_cost'       => 'nullable|numeric',
            'accumulated'      => 'nullable|numeric',
            'nbv_at'           => 'nullable|numeric',
            'warranty_date'    => 'nullable|date',
            'notes'            => 'nullable|string',
        ]);

        if ($user->isAdminOrFinance()) {
            $data['created_by'] = $user->id;
            $item = NonItAsset::create($data);
            ActivityLogService::log('CREATE', 'non_it_asset', $item->id, 'Added: '.$item->description);
            return redirect()->route('it.non-it.index')->with('success', 'Asset added successfully.');
        }

        EditAssetRequest::create(array_merge($data, [
            'asset_type'   => 'non_it',
            'asset_id'     => 0,
            'requested_by' => $user->id,
        ]));
        return redirect()->route('it.non-it.index')->with('success', 'Edit request submitted. Awaiting approval.');
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::guard('it')->user();
        $item = NonItAsset::findOrFail($id);

        $data = $request->validate([
            'asset_number'     => 'nullable|string|max:50',
            'asset_class'      => 'required|string|max:100',
            'fa_code'          => 'nullable|string|max:100',
            'description'      => 'required|string|max:255',
            'brand'            => 'nullable|string|max:100',
            'location'         => 'nullable|string|max:100',
            'item_status'      => 'nullable|in:Active,In Repair,Disposed,Reserved',
            'condition_status' => 'nullable|string|max:50',
            'date_registered'  => 'nullable|date',
            'years_purchase'   => 'nullable|integer',
            'total_cost'       => 'nullable|numeric',
            'accumulated'      => 'nullable|numeric',
            'nbv_at'           => 'nullable|numeric',
            'warranty_date'    => 'nullable|date',
            'notes'            => 'nullable|string',
        ]);

        if ($user->isAdminOrFinance()) {
            $item->update($data);
            ActivityLogService::log('UPDATE', 'non_it_asset', $id, 'Updated: '.$item->description);
            return redirect()->route('it.non-it.index')->with('success', 'Asset updated successfully.');
        }

        $dup = EditAssetRequest::where('asset_id', $id)
            ->where('asset_type', 'non_it')
            ->where('requested_by', $user->id)
            ->where('status', 'Pending')
            ->exists();

        if ($dup) {
            return redirect()->route('it.non-it.index')->with('error', 'You already have a pending edit request for this asset.');
        }

        EditAssetRequest::create(array_merge($data, [
            'asset_type'   => 'non_it',
            'asset_id'     => $id,
            'requested_by' => $user->id,
        ]));
        return redirect()->route('it.non-it.index')->with('success', 'Edit request submitted. Awaiting approval.');
    }

    public function destroy(int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdminOrFinance()) abort(403);
        $item = NonItAsset::findOrFail($id);
        ActivityLogService::log('DELETE', 'non_it_asset', $id, 'Deleted: '.$item->description);
        $item->delete();
        return redirect()->route('it.non-it.index')->with('success', 'Asset deleted.');
    }

    public function bulkDestroy(Request $request)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdminOrFinance()) abort(403);
        $ids   = array_map('intval', $request->selected_ids ?? []);
        $count = 0;
        foreach ($ids as $id) {
            $item = NonItAsset::find($id);
            if ($item) {
                ActivityLogService::log('DELETE', 'non_it_asset', $id, 'Bulk deleted: '.$item->description);
                $item->delete();
                $count++;
            }
        }
        return redirect()->route('it.non-it.index')->with('success', "$count asset(s) deleted.");
    }

    public function importTemplate()
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdminOrFinance()) abort(403);

        $csv  = "Asset Number,Asset Class,F/A Code,Description,Location,Status,Condition,Date Registered,Years Purchase,Total Cost,Accumulated,NBV AT,Notes\n";
        $csv .= "NIT-001,FURNITURE,4100000047,Ergonomic Office Chair,Meeting Room A,Active,Good,2024-01-15,2018,5000.00,2000.00,3000.00,Sample row\n";

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="non_it_assets_template.csv"',
        ]);
    }

    public function importExcel(Request $request)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdminOrFinance()) abort(403);

        $rows     = $request->json()->all() ?? [];
        $inserted = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($rows as $i => $row) {
            $rn               = $i + 2;
            $asset_number     = trim($row['asset_number']     ?? '');
            $asset_class      = strtoupper(trim($row['asset_class'] ?? ''));
            $fa_code          = trim($row['fa_code']          ?? '');
            $description      = trim($row['description']      ?? '');
            $location         = trim($row['location']         ?? '');
            $item_status      = trim($row['item_status']      ?? 'Active');
            $condition_status = trim($row['condition_status'] ?? 'Good');
            $date_registered  = trim($row['date_registered']  ?? '') ?: null;
            $notes            = trim($row['notes']            ?? '');
            $years_purchase   = !empty($row['years_purchase']) ? (int)$row['years_purchase']   : null;
            $total_cost       = !empty($row['total_cost'])     ? (float)$row['total_cost']     : null;
            $accumulated      = !empty($row['accumulated'])    ? (float)$row['accumulated']    : null;
            $nbv_at           = !empty($row['nbv_at'])         ? (float)$row['nbv_at']         : null;

            if (empty($description) && empty($asset_class) && empty($asset_number) && empty($fa_code)) {
                $skipped++; continue;
            }
            if ($asset_number && NonItAsset::where('asset_number', $asset_number)->exists()) {
                $errors[] = "Row $rn: Asset No. '$asset_number' already exists â€” skipped.";
                $skipped++; continue;
            }

            NonItAsset::create([
                'asset_number'     => $asset_number ?: null,
                'asset_class'      => $asset_class,
                'fa_code'          => $fa_code ?: null,
                'description'      => $description,
                'location'         => $location ?: null,
                'item_status'      => $item_status,
                'condition_status' => $condition_status,
                'date_registered'  => $date_registered,
                'years_purchase'   => $years_purchase,
                'total_cost'       => $total_cost,
                'accumulated'      => $accumulated,
                'nbv_at'           => $nbv_at,
                'notes'            => $notes ?: null,
                'created_by'       => $user->id,
            ]);
            $inserted++;
        }

        if ($inserted > 0) {
            ActivityLogService::log('CREATE', 'non_it_asset', 0, "Excel import: $inserted assets added");
        }

        return response()->json(['inserted' => $inserted, 'skipped' => $skipped, 'errors' => $errors]);
    }
}

