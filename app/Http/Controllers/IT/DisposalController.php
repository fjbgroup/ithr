<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\DisposalItem;
use App\Models\IT\EwasteItem;
use App\Services\IT\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisposalController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $search = trim($request->di_search ?? '');
        $class  = trim($request->di_class  ?? '');
        $status = trim($request->di_status ?? '');

        $query = DisposalItem::with('creator')->orderByDesc('created_at');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('asset_number',  'like', "%$search%")
                  ->orWhere('description',   'like', "%$search%")
                  ->orWhere('serial_number', 'like', "%$search%")
                  ->orWhere('asset_class',   'like', "%$search%");
            });
        }
        if ($class)  $query->where('asset_class',      $class);
        if ($status) $query->where('disposal_status',  $status);

        $items        = $query->paginate(25)->withQueryString();
        $total        = DisposalItem::count();
        $approved     = DisposalItem::where('disposal_status', 'Approved')->count();
        $disposed     = DisposalItem::where('disposal_status', 'Disposed')->count();
        $assetClasses = \App\Models\IT\AssetClass::where('type', 'it')->orderBy('sort_order')->pluck('name');

        if ($request->boolean('partial')) {
            $user = Auth::guard('it')->user();
            return response(view('it.disposal.partials.live-table', compact('items', 'user', 'total', 'search', 'class', 'status'))->render());
        }

        return view('it.disposal.index', compact('items', 'total', 'approved', 'disposed', 'assetClasses', 'search', 'class', 'status'));
    }

    public function autocomplete(Request $request)
    {
        $q = trim($request->q ?? '');
        if (strlen($q) < 2) return response()->json([]);

        $rows = DisposalItem::where(function ($query) use ($q) {
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

    public function restore(int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdmin()) abort(403);
        $item = DisposalItem::findOrFail($id);
        $item->update(['disposal_status' => 'Approved']);
        ActivityLogService::log('RESTORE', 'disposal', $id, 'Restored disposal item: '.$item->description);
        return redirect()->route('it.disposal.index')->with('success', 'Asset restored to Active successfully.');
    }

    public function destroy(int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdmin()) abort(403);
        $item = DisposalItem::findOrFail($id);
        $desc = $item->description;
        $item->delete();
        ActivityLogService::log('DELETE', 'disposal', $id, 'Permanently deleted: '.$desc);
        return redirect()->route('it.disposal.index')->with('success', 'Asset permanently deleted.');
    }

    public function proofs()
    {
        $items = DisposalItem::with('creator')
            ->where('disposal_status', 'Disposed')
            ->orderByDesc('date_disposed')
            ->orderByDesc('updated_at')
            ->get();

        $dpCount     = $items->count();
        $dpThisMonth = $items->filter(fn($r) =>
            $r->date_disposed && $r->date_disposed->format('Y-m') === now()->format('Y-m')
        )->count();

        return view('it.disposal.proofs', compact('items', 'dpCount', 'dpThisMonth'));
    }

    public function collected()
    {
        $items = EwasteItem::with('creator')
            ->where('disposal_status', 'Collected')
            ->orderByDesc('date_disposed')
            ->orderByDesc('updated_at')
            ->get();

        $cpCount     = $items->count();
        $cpThisMonth = $items->filter(fn($r) =>
            $r->date_disposed && $r->date_disposed->format('Y-m') === now()->format('Y-m')
        )->count();

        return view('it.disposal.collected', compact('items', 'cpCount', 'cpThisMonth'));
    }

    public function markDisposed(Request $request, int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdminOrFinance()) abort(403);
        $item = DisposalItem::findOrFail($id);
        $item->update(['disposal_status' => 'Disposed', 'date_disposed' => now()->toDateString()]);
        ActivityLogService::log('DISPOSED', 'inventory', $id, 'Marked as Disposed: '.$item->description);
        return back()->with('success', 'Item marked as disposed.');
    }

    public function store(Request $request)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdminOrFinance()) abort(403);

        $data = $request->validate([
            'asset_class'        => 'required|string|max:50',
            'description'        => 'required|string|max:255',
            'asset_number'       => 'nullable|string|max:50',
            'serial_number'      => 'nullable|string|max:100',
            'disposal_status'    => 'nullable|string|max:50',
            'disposal_method'    => 'nullable|string|max:100',
            'vendor_collector'   => 'nullable|string|max:100',
            'certificate_number' => 'nullable|string|max:100',
            'notes'              => 'nullable|string',
            'date_flagged'       => 'nullable|date',
            'date_disposed'      => 'nullable|date',
        ]);

        $data['created_by']     = $user->id;
        $data['disposal_status'] = $data['disposal_status'] ?? 'Approved';

        $item = DisposalItem::create($data);
        ActivityLogService::log('CREATE', 'disposal', $item->id, 'Added disposal item: ' . $item->description);
        return back()->with('success', 'Disposal item added successfully.');
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdminOrFinance()) abort(403);
        $item = DisposalItem::findOrFail($id);

        $data = $request->validate([
            'asset_class'        => 'required|string|max:50',
            'description'        => 'required|string|max:255',
            'asset_number'       => 'nullable|string|max:50',
            'serial_number'      => 'nullable|string|max:100',
            'disposal_status'    => 'nullable|string|max:50',
            'disposal_method'    => 'nullable|string|max:100',
            'vendor_collector'   => 'nullable|string|max:100',
            'certificate_number' => 'nullable|string|max:100',
            'notes'              => 'nullable|string',
            'date_flagged'       => 'nullable|date',
            'date_disposed'      => 'nullable|date',
        ]);

        $item->update($data);
        ActivityLogService::log('UPDATE', 'disposal', $id, 'Updated disposal item: ' . $item->description);
        return back()->with('success', 'Disposal item updated successfully.');
    }

    public function importTemplate()
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdminOrFinance()) abort(403);

        $csv  = "Asset Number,Asset Class,Description,Serial Number,Status,Disposal Method,Vendor/Collector,Certificate Number,Date Flagged,Date Disposed,Notes\n";
        $csv .= "OEPC1401,COMPUTER,HP EliteBook 840 G3,SGH629QBBY,Approved,Recycle,ABC Vendor,CERT-001,2024-01-15,2024-02-01,Sample row\n";

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="disposal_template.csv"',
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
            $rn                 = $i + 2;
            $asset_number       = trim($row['asset_number']       ?? '');
            $asset_class        = strtoupper(trim($row['asset_class'] ?? ''));
            $description        = trim($row['description']        ?? '');
            $serial_number      = trim($row['serial_number']      ?? '');
            $disposal_status    = trim($row['disposal_status']    ?? 'Approved');
            $disposal_method    = trim($row['disposal_method']    ?? '');
            $vendor_collector   = trim($row['vendor_collector']   ?? '');
            $certificate_number = trim($row['certificate_number'] ?? '');
            $date_flagged       = trim($row['date_flagged']       ?? '') ?: null;
            $date_disposed      = trim($row['date_disposed']      ?? '') ?: null;
            $notes              = trim($row['notes']              ?? '');

            if (empty($description) && empty($asset_class)) {
                $skipped++; continue;
            }

            \App\Models\IT\DisposalItem::create([
                'asset_number'       => $asset_number       ?: null,
                'asset_class'        => $asset_class,
                'description'        => $description,
                'serial_number'      => $serial_number      ?: null,
                'disposal_status'    => in_array($disposal_status, ['Approved', 'Disposed']) ? $disposal_status : 'Approved',
                'disposal_method'    => $disposal_method    ?: null,
                'vendor_collector'   => $vendor_collector   ?: null,
                'certificate_number' => $certificate_number ?: null,
                'date_flagged'       => $date_flagged,
                'date_disposed'      => $date_disposed,
                'notes'              => $notes              ?: null,
                'created_by'         => $user->id,
            ]);
            $inserted++;
        }

        if ($inserted > 0) {
            ActivityLogService::log('CREATE', 'disposal', 0, "Excel import: $inserted disposal items added");
        }

        return response()->json(['inserted' => $inserted, 'skipped' => $skipped, 'errors' => $errors]);
    }

    public function collectionInvoice(Request $request)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdminOrFinance()) abort(403);

        $from = $request->from ?: now()->startOfMonth()->toDateString();
        $to   = $request->to   ?: now()->toDateString();
        $ref  = 'COL-' . now()->format('Ymd') . '-' . strtoupper(substr(md5($from . $to), 0, 6));

        $items = EwasteItem::with(['creator'])
            ->where('disposal_status', 'Collected')
            ->whereBetween('date_disposed', [$from, $to])
            ->orderBy('date_disposed')
            ->get();

        return view('it.ewaste.collection-invoice', compact('items', 'from', 'to', 'ref'));
    }
}

