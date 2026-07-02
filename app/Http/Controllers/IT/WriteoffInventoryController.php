<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\DisposalItem;
use App\Models\IT\EwasteItem;
use App\Models\IT\InventoryItem;
use App\Services\IT\ActivityLogService;
use App\Services\IT\NotificationService;
use Illuminate\Support\Facades\Auth;

class WriteoffInventoryController extends Controller
{
    public function index()
    {
        if (!Auth::guard('it')->user()->isFinanceAdmin()) abort(403);

        $pendingQueue = EwasteItem::where('ceo_status', 'Approved')
            ->where('finance_status', 'Pending')
            ->orderByDesc('ceo_signed_at')
            ->orderByDesc('created_at')
            ->get();

        $processedQueue = EwasteItem::where('ceo_status', 'Approved')
            ->whereIn('finance_status', ['EWaste', 'Disposal'])
            ->orderByDesc('updated_at')
            ->limit(50)
            ->get();

        $pendingGroups  = $pendingQueue->groupBy(fn($i) => $i->batch_id ?: 'solo_'.$i->id);
        $processedGroups = $processedQueue->groupBy(fn($i) => $i->batch_id ?: 'solo_'.$i->id);

        return view('it.writeoff_inventory.index', [
            'pendingGroups'   => $pendingGroups,
            'processedGroups' => $processedGroups,
            'pendingCount'    => $pendingQueue->count(),
        ]);
    }

    public function routeEwasteBatch($batchId)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isFinanceAdmin()) abort(403);

        $items = EwasteItem::where('batch_id', $batchId)
            ->where('ceo_status', 'Approved')
            ->where('finance_status', 'Pending')
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('it.writeoff-inventory.index')->with('error', 'Batch not found or already processed.');
        }

        foreach ($items as $item) {
            $item->update(['finance_status' => 'EWaste', 'disposal_status' => 'Approved']);

            if ($item->original_inventory_id) {
                InventoryItem::where('id', $item->original_inventory_id)
                    ->update(['location' => 'E-Waste']);
            }

            ActivityLogService::log('FINANCE_EWASTE', 'ewaste', $item->id, 'Finance routed write-off to E-Waste: '.$item->description);

            if ($item->created_by) {
                NotificationService::notifyUser(
                    $item->created_by, 'writeoff',
                    'Write-Off Routed to E-Waste',
                    'The write-off for "'.$item->description.'" has been reviewed by Finance and routed to E-Waste.',
                    route('it.ewaste.index')
                );
            }
        }

        return redirect()->route('it.writeoff-inventory.index')->with('success', $items->count().' items routed to E-Waste successfully.');
    }

    public function routeDisposalBatch($batchId)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isFinanceAdmin()) abort(403);

        $items = EwasteItem::where('batch_id', $batchId)
            ->where('ceo_status', 'Approved')
            ->where('finance_status', 'Pending')
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('it.writeoff-inventory.index')->with('error', 'Batch not found or already processed.');
        }

        foreach ($items as $item) {
            DisposalItem::create([
                'asset_number'    => $item->asset_number,
                'asset_class'     => $item->asset_class,
                'description'     => $item->description,
                'serial_number'   => $item->serial_number,
                'disposal_status' => 'Approved',
                'date_flagged'    => now()->toDateString(),
                'created_by'      => $user->id,
            ]);

            $item->update(['finance_status' => 'Disposal', 'disposal_status' => 'Approved']);

            if ($item->original_inventory_id) {
                InventoryItem::where('id', $item->original_inventory_id)
                    ->update(['location' => 'Disposal']);
            }

            ActivityLogService::log('FINANCE_DISPOSAL', 'ewaste', $item->id, 'Finance routed write-off to Disposal: '.$item->description);

            if ($item->created_by) {
                NotificationService::notifyUser(
                    $item->created_by, 'writeoff',
                    'Write-Off Routed to Disposal',
                    'The write-off for "'.$item->description.'" has been reviewed by Finance and routed to Disposal.',
                    route('it.disposal.index')
                );
            }
        }

        return redirect()->route('it.writeoff-inventory.index')->with('success', $items->count().' items routed to Disposal successfully.');
    }

    public function routeEwaste($id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isFinanceAdmin()) abort(403);

        $item = EwasteItem::where('id', $id)
            ->where('ceo_status', 'Approved')
            ->where('finance_status', 'Pending')
            ->first();

        if ($item) {
            $item->update(['finance_status' => 'EWaste', 'disposal_status' => 'Approved']);

            if ($item->original_inventory_id) {
                InventoryItem::where('id', $item->original_inventory_id)
                    ->update(['location' => 'E-Waste']);
            }

            ActivityLogService::log('FINANCE_EWASTE', 'ewaste', $item->id, 'Finance routed write-off to E-Waste: ' . $item->description);

            if ($item->created_by) {
                NotificationService::notifyUser(
                    $item->created_by, 'writeoff',
                    'Write-Off Routed to E-Waste',
                    'The write-off for "' . $item->description . '" has been reviewed by Finance and routed to E-Waste.',
                    route('it.ewaste.index')
                );
            }

            return redirect()->route('it.writeoff-inventory.index')->with('success', 'Item routed to E-Waste successfully.');
        }

        return redirect()->route('it.writeoff-inventory.index')->with('error', 'Item not found or already processed.');
    }

    public function routeDisposal($id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isFinanceAdmin()) abort(403);

        $item = EwasteItem::where('id', $id)
            ->where('ceo_status', 'Approved')
            ->where('finance_status', 'Pending')
            ->first();

        if ($item) {
            DisposalItem::create([
                'asset_number'   => $item->asset_number,
                'asset_class'    => $item->asset_class,
                'description'    => $item->description,
                'serial_number'  => $item->serial_number,
                'disposal_status'=> 'Approved',
                'date_flagged'   => now()->toDateString(),
                'created_by'     => $user->id,
            ]);

            $item->update(['finance_status' => 'Disposal', 'disposal_status' => 'Approved']);

            if ($item->original_inventory_id) {
                InventoryItem::where('id', $item->original_inventory_id)
                    ->update(['location' => 'Disposal']);
            }

            ActivityLogService::log('FINANCE_DISPOSAL', 'ewaste', $item->id, 'Finance routed write-off to Disposal: ' . $item->description);

            if ($item->created_by) {
                NotificationService::notifyUser(
                    $item->created_by, 'writeoff',
                    'Write-Off Routed to Disposal',
                    'The write-off for "' . $item->description . '" has been reviewed by Finance and routed to Disposal.',
                    route('it.disposal.index')
                );
            }

            return redirect()->route('it.writeoff-inventory.index')->with('success', 'Item routed to Disposal successfully.');
        }

        return redirect()->route('it.writeoff-inventory.index')->with('error', 'Item not found or already processed.');
    }
}
