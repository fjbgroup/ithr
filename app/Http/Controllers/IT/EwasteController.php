<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\AssetClass;
use App\Models\IT\EwasteItem;
use App\Models\IT\InventoryItem;
use App\Services\IT\ActivityLogService;
use App\Services\IT\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EwasteController extends Controller
{
    public function index(Request $request)
    {
        $query = EwasteItem::query()->where('disposal_status', '!=', 'Pending');

        if ($s = $request->ew_search) {
            $query->where(function ($q) use ($s) {
                $q->where('asset_number', 'like', "%$s%")
                  ->orWhere('description', 'like', "%$s%")
                  ->orWhere('serial_number', 'like', "%$s%");
            });
        }
        if ($c = $request->ew_class)  $query->where('asset_class', $c);
        if ($st = $request->ew_status) {
            if ($st === 'E-Waste') {
                $query->where('disposal_status', '!=', 'Collected');
            } else {
                $query->where('disposal_status', $st);
            }
        }

        $items        = $query->orderByDesc('created_at')->paginate(25)->withQueryString();
        $assetClasses = AssetClass::where('type', 'it')->orderBy('sort_order')->pluck('name');

        if ($request->boolean('partial')) {
            $user = Auth::guard('it')->user();
            return response(view('it.ewaste.partials.live-table', compact('items', 'user'))->render());
        }

        return view('it.ewaste.index', compact('items', 'assetClasses'));
    }

    public function autocomplete(Request $request)
    {
        $q = trim($request->q ?? '');
        if (strlen($q) < 2) return response()->json([]);

        $rows = EwasteItem::where('disposal_status', '!=', 'Pending')
            ->where(function ($query) use ($q) {
                $query->where('asset_number',  'like', "%$q%")
                      ->orWhere('description',  'like', "%$q%")
                      ->orWhere('serial_number','like', "%$q%");
            })
            ->limit(20)
            ->get(['asset_number', 'description', 'serial_number']);

        $ql = strtolower($q);
        $suggestions = collect();
        foreach ($rows as $row) {
            foreach (['asset_number', 'description', 'serial_number'] as $field) {
                $val = $row->$field;
                if ($val && str_contains(strtolower($val), $ql)) {
                    $suggestions->push($val);
                }
            }
        }

        return response()->json($suggestions->unique()->values()->take(8));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdminOrFinance()) abort(403);

        $data = $request->validate([
            'asset_number'         => 'nullable|string|max:50',
            'asset_class'          => 'required|string|max:50',
            'description'          => 'required|string|max:255',
            'serial_number'        => 'nullable|string|max:100',
            'original_inventory_id'=> 'nullable|integer|exists:inventory_items,id',
            'condition_on_disposal'=> 'nullable|string',
            'disposal_method'      => 'nullable|string',
            'date_flagged'         => 'nullable|date',
            'notes'                => 'nullable|string',
        ]);

        $data['created_by']     = $user->id;
        $data['disposal_status']= 'Approved';
        $data['hou_status']     = 'Approved';

        $item = EwasteItem::create($data);

        if (!empty($data['original_inventory_id'])) {
            InventoryItem::where('id', $data['original_inventory_id'])->update(['item_status' => 'Disposed']);
        } else {
            $invItem = InventoryItem::create([
                'asset_number'     => $item->asset_number,
                'asset_class'      => $item->asset_class,
                'description'      => $item->description,
                'serial_number'    => $item->serial_number,
                'item_status'      => 'Disposed',
                'condition_status' => $item->condition_on_disposal ?: 'For Disposal',
                'notes'            => $item->notes,
                'created_by'       => $user->id,
            ]);
            $item->update(['original_inventory_id' => $invItem->id]);
        }

        ActivityLogService::log('CREATE', 'ewaste', $item->id, 'Added E-Waste item: '.$item->description);
        return back()->with('success', 'E-Waste item added.');
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdminOrFinance()) abort(403);
        $item = EwasteItem::findOrFail($id);

        $data = $request->validate([
            'condition_on_disposal' => 'nullable|string',
            'disposal_status'       => 'nullable|string',
            'disposal_method'       => 'nullable|string',
            'weight_kg'             => 'nullable|numeric',
            'vendor_collector'      => 'nullable|string|max:100',
            'certificate_number'    => 'nullable|string|max:100',
            'date_disposed'         => 'nullable|date',
            'notes'                 => 'nullable|string',
        ]);

        $item->update($data);
        ActivityLogService::log('UPDATE', 'ewaste', $id, 'Updated E-Waste item: '.$item->description);
        return back()->with('success', 'E-Waste item updated.');
    }

    public function destroy(int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdminOrFinance()) abort(403);
        $item = EwasteItem::findOrFail($id);

        if ($item->original_inventory_id) {
            InventoryItem::where('id', $item->original_inventory_id)
                ->whereIn('item_status', ['Active', 'Collected'])
                ->update(['item_status' => 'Active']);
        }

        ActivityLogService::log('DELETE', 'ewaste', $id, 'Deleted E-Waste item: '.$item->description);
        $item->delete();
        return back()->with('success', 'E-Waste item deleted.');
    }

    public function collect(int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdminOrFinance()) abort(403);
        $item = EwasteItem::findOrFail($id);

        $item->update(['disposal_status' => 'Collected', 'date_disposed' => now()->toDateString()]);
        if ($item->original_inventory_id) {
            InventoryItem::where('id', $item->original_inventory_id)->update(['item_status' => 'Collected']);
        }
        ActivityLogService::log('COLLECTED', 'ewaste', $id, 'Marked as collected: '.$item->description);
        return back()->with('success', 'Marked as collected.');
    }

    public function restore(int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdminOrFinance()) abort(403);
        $ew = EwasteItem::findOrFail($id);

        if ($ew->original_inventory_id) {
            InventoryItem::where('id', $ew->original_inventory_id)->update(['item_status' => 'Active', 'location' => '']);
        } else {
            InventoryItem::create([
                'asset_number'    => $ew->asset_number,
                'asset_class'     => $ew->asset_class,
                'description'     => $ew->description,
                'serial_number'   => $ew->serial_number,
                'item_status'     => 'Active',
                'condition_status'=> 'Good',
                'created_by'      => $user->id,
            ]);
        }
        ActivityLogService::log('RESTORE', 'ewaste', $id, 'Restored to IT Assets: '.$ew->description);
        $ew->delete();
        return back()->with('success', 'Item restored to IT Assets.');
    }

    public function bulk(Request $request)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdminOrFinance()) abort(403);

        $action = $request->bulk_action;
        $ids    = array_map('intval', $request->input('selected_ids', []));
        $count  = 0;

        foreach ($ids as $bid) {
            $ew = EwasteItem::find($bid);
            if (!$ew) continue;

            if ($action === 'bulk_collect' && $ew->disposal_status === 'Approved') {
                $ew->update(['disposal_status' => 'Collected', 'date_disposed' => now()->toDateString()]);
                if ($ew->original_inventory_id) {
                    InventoryItem::where('id', $ew->original_inventory_id)->update(['item_status' => 'Collected']);
                }
                ActivityLogService::log('COLLECTED', 'ewaste', $bid, 'Bulk collected: '.$ew->description);
                $count++;
            } elseif ($action === 'bulk_restore') {
                if ($ew->original_inventory_id) {
                    InventoryItem::where('id', $ew->original_inventory_id)->update(['item_status' => 'Active', 'location' => '']);
                } else {
                    InventoryItem::create([
                        'asset_number'    => $ew->asset_number,
                        'asset_class'     => $ew->asset_class,
                        'description'     => $ew->description,
                        'serial_number'   => $ew->serial_number,
                        'item_status'     => 'Active',
                        'condition_status'=> 'Good',
                        'created_by'      => $user->id,
                    ]);
                }
                ActivityLogService::log('RESTORE', 'ewaste', $bid, 'Bulk restored to IT Assets: '.$ew->description);
                $ew->delete();
                $count++;
            } elseif ($action === 'bulk_delete') {
                if ($ew->original_inventory_id) {
                    InventoryItem::where('id', $ew->original_inventory_id)
                        ->whereIn('item_status', ['Active', 'Collected'])
                        ->update(['item_status' => 'Active']);
                }
                ActivityLogService::log('DELETE', 'ewaste', $bid, 'Bulk deleted: '.$ew->description);
                $ew->delete();
                $count++;
            }
        }

        $messages = [
            'bulk_collect'  => $count.' item(s) marked as collected.',
            'bulk_restore'  => $count.' item(s) restored to IT Assets.',
            'bulk_delete'   => $count.' item(s) deleted.',
        ];
        return redirect()->route('it.ewaste.index')->with('success', $messages[$action] ?? 'Bulk action applied.');
    }

    public function importTemplate()
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdminOrFinance()) abort(403);

        $csv  = "Asset Number,Asset Class,Description,Serial Number,Date Flagged,Weight (kg),Notes\n";
        $csv .= "OEPC1401,COMPUTER,HP EliteBook 840 G3,SGH629QBBY,2024-01-15,1.50,Sample row\n";

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ewaste_template.csv"',
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
            $rn            = $i + 2;
            $asset_number  = trim($row['asset_number']  ?? '');
            $asset_class   = strtoupper(trim($row['asset_class'] ?? ''));
            $description   = trim($row['description']   ?? '');
            $serial_number = trim($row['serial_number']  ?? '');
            $date_flagged  = trim($row['date_flagged']   ?? '') ?: null;
            $weight_kg     = !empty($row['weight_kg'])   ? (float)$row['weight_kg'] : null;
            $notes         = trim($row['notes']          ?? '');

            if (empty($description) && empty($asset_class)) {
                $skipped++; continue;
            }

            EwasteItem::create([
                'asset_number'    => $asset_number  ?: null,
                'asset_class'     => $asset_class,
                'description'     => $description,
                'serial_number'   => $serial_number ?: null,
                'date_flagged'    => $date_flagged,
                'weight_kg'       => $weight_kg,
                'notes'           => $notes         ?: null,
                'disposal_status' => 'Approved',
                'hou_status'      => 'Approved',
                'created_by'      => $user->id,
            ]);
            $inserted++;
        }

        if ($inserted > 0) {
            ActivityLogService::log('CREATE', 'ewaste', 0, "Excel import: $inserted e-waste items added");
        }

        return response()->json(['inserted' => $inserted, 'skipped' => $skipped, 'errors' => $errors]);
    }

    public function uncollect(int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdminOrFinance()) abort(403);

        $item = EwasteItem::findOrFail($id);
        $item->update(['disposal_status' => 'Approved', 'date_disposed' => null]);

        if ($item->original_inventory_id) {
            InventoryItem::where('id', $item->original_inventory_id)
                ->update(['item_status' => 'Active']);
        }

        ActivityLogService::log('UPDATE', 'ewaste', $id, 'Reverted collection: ' . $item->description);
        return redirect()->route('ewaste.collected')->with('success', 'Item reverted back to Approved.');
    }

    // â”€â”€ GM timeout auto-reassignment (called from writeoff flow) â”€â”€
    public function reassignExpiredGMs(): void
    {
        $expired = EwasteItem::where('disposal_status', 'Pending')
            ->where('gm_status', 'Pending')
            ->whereNotNull('current_gm_user_id')
            ->where('gm_assigned_at', '<', now()->subDays(3))
            ->get();

        foreach ($expired as $ew) {
            $nextGm = ($ew->current_gm_user_id == $ew->gm1_user_id) ? $ew->gm2_user_id : $ew->gm1_user_id;
            if ($nextGm) {
                $ew->update(['current_gm_user_id' => $nextGm, 'gm_assigned_at' => now()]);
                NotificationService::notifyUser($nextGm, 'writeoff', 'âœï¸ Write-Off Awaiting Your GM Signature',
                    'A write-off for "'.$ew->description.'" has been reassigned to you (previous GM did not respond within 3 days).',
                    route('it.writeoff.index'));
            }
        }
    }
}

