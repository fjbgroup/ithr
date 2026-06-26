<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\AddAssetRequest;
use App\Models\IT\DeleteRequest;
use App\Models\IT\EditAssetRequest;
use App\Models\IT\EwasteItem;
use App\Models\IT\EwasteRequest;
use App\Models\IT\InventoryItem;
use App\Services\IT\ActivityLogService;
use App\Services\IT\NotificationService;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    // â”€â”€ Add requests â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    public function approveAdd(int $id)
    {
        $req = AddAssetRequest::findOrFail($id);
        if ($req->status !== 'Pending') return back()->with('error', 'Request is no longer pending.');

        $item = InventoryItem::create([
            'asset_number'  => $req->asset_number,
            'asset_class'   => $req->asset_class,
            'description'   => $req->description,
            'serial_number' => $req->serial_number,
            'brand'         => $req->brand,
            'model'         => $req->model,
            'location'      => $req->location,
            'notes'         => $req->notes,
            'item_status'   => 'Active',
            'condition_status' => 'Good',
            'created_by'    => $req->requested_by,
        ]);

        $req->update(['status' => 'Approved', 'reviewed_by' => Auth::guard('it')->id(), 'reviewed_at' => now()]);
        NotificationService::notifyUserWithEmail($req->requested_by, 'request_approved', 'Add Request Approved', 'Your asset add request has been approved.', route('it.inventory.index'));
        ActivityLogService::log('APPROVE_ADD', 'inventory', $item->id, 'Approved add request for: '.$item->description);

        return redirect()->route('it.inventory.index', ['view' => 'pending_requests'])->with('success', 'Add request approved.');
    }

    public function rejectAdd(int $id)
    {
        $req = AddAssetRequest::findOrFail($id);
        if ($req->status !== 'Pending') return back()->with('error', 'Request is no longer pending.');

        $req->update(['status' => 'Rejected', 'reviewed_by' => Auth::guard('it')->id(), 'reviewed_at' => now()]);
        NotificationService::notifyUserWithEmail($req->requested_by, 'request_rejected', 'Add Request Rejected', 'Your asset add request has been rejected.', route('it.inventory.index'));
        ActivityLogService::log('REJECT_ADD', 'inventory', 0, 'Rejected add request for: '.$req->description);

        return redirect()->route('it.inventory.index', ['view' => 'pending_requests'])->with('success', 'Add request rejected.');
    }

    // â”€â”€ Edit requests â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    public function approveEdit(int $id)
    {
        $req = EditAssetRequest::findOrFail($id);
        if ($req->status !== 'Pending') return back()->with('error', 'Request is no longer pending.');

        if ($req->asset_type === 'it') {
            $item = InventoryItem::findOrFail($req->asset_id);
            $item->update([
                'asset_number'     => $req->asset_number,
                'asset_class'      => $req->asset_class,
                'fa_code'          => $req->fa_code,
                'description'      => $req->description,
                'serial_number'    => $req->serial_number,
                'brand'            => $req->brand,
                'model'            => $req->model,
                'location'         => $req->location,
                'condition_status' => $req->condition_status,
                'purchase_date'    => $req->purchase_date,
                'purchase_price'   => $req->purchase_price,
                'years_purchase'   => $req->years_purchase,
                'total_cost'       => $req->total_cost,
                'accumulated'      => $req->accumulated,
                'nbv_at'           => $req->nbv_at,
                'notes'            => $req->notes,
            ]);
            $route = route('it.inventory.index', ['view' => 'pending_requests']);
            $logType = 'inventory';
            $logId = $item->id;
        } else {
            $item = \App\Models\IT\NonItAsset::findOrFail($req->asset_id);
            $item->update([
                'asset_number'     => $req->asset_number,
                'asset_class'      => $req->asset_class,
                'fa_code'          => $req->fa_code,
                'description'      => $req->description,
                'location'         => $req->location,
                'condition_status' => $req->condition_status,
                'years_purchase'   => $req->years_purchase,
                'total_cost'       => $req->total_cost,
                'accumulated'      => $req->accumulated,
                'nbv_at'           => $req->nbv_at,
                'notes'            => $req->notes,
            ]);
            $route = route('it.inventory.index', ['view' => 'pending_requests']);
            $logType = 'non_it_asset';
            $logId = $item->id;
        }

        $req->update(['status' => 'Approved', 'reviewed_by' => Auth::guard('it')->id(), 'reviewed_at' => now()]);
        NotificationService::notifyUserWithEmail($req->requested_by, 'request_approved', 'Edit Request Approved', 'Your asset edit request has been approved.', $route);
        ActivityLogService::log('APPROVE_EDIT', $logType, $logId, 'Approved edit request for: '.$req->description);

        return redirect($route)->with('success', 'Edit request approved.');
    }

    public function rejectEdit(int $id)
    {
        $req = EditAssetRequest::findOrFail($id);
        if ($req->status !== 'Pending') return back()->with('error', 'Request is no longer pending.');

        $route = route('it.inventory.index', ['view' => 'pending_requests']);

        $req->update(['status' => 'Rejected', 'reviewed_by' => Auth::guard('it')->id(), 'reviewed_at' => now()]);
        NotificationService::notifyUserWithEmail($req->requested_by, 'request_rejected', 'Edit Request Rejected', 'Your asset edit request has been rejected.', $route);
        ActivityLogService::log('REJECT_EDIT', 'inventory', 0, 'Rejected edit request for: '.$req->description);

        return redirect($route)->with('success', 'Edit request rejected.');
    }

    // â”€â”€ Delete requests â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    public function approveDelete(int $id)
    {
        $req = DeleteRequest::findOrFail($id);
        if ($req->status !== 'Pending') return back()->with('error', 'Request is no longer pending.');

        if ($req->inventory_id) {
            EwasteItem::where('original_inventory_id', $req->inventory_id)->delete();
            InventoryItem::where('id', $req->inventory_id)->delete();
        }

        $req->update(['status' => 'Approved', 'reviewed_by' => Auth::guard('it')->id(), 'reviewed_at' => now()]);
        NotificationService::notifyUserWithEmail($req->requested_by, 'request_approved', 'Delete Request Approved', 'Your asset delete request has been approved.', route('it.inventory.index'));
        ActivityLogService::log('APPROVE_DELETE', 'inventory', (int)$req->inventory_id, 'Approved delete request for: '.$req->asset_description);

        return redirect()->route('it.inventory.index', ['view' => 'pending_requests'])->with('success', 'Delete request approved.');
    }

    public function rejectDelete(int $id)
    {
        $req = DeleteRequest::findOrFail($id);
        if ($req->status !== 'Pending') return back()->with('error', 'Request is no longer pending.');

        $req->update(['status' => 'Rejected', 'reviewed_by' => Auth::guard('it')->id(), 'reviewed_at' => now()]);
        NotificationService::notifyUserWithEmail($req->requested_by, 'request_rejected', 'Delete Request Rejected', 'Your asset delete request has been rejected.', route('it.inventory.index'));
        ActivityLogService::log('REJECT_DELETE', 'inventory', (int)$req->inventory_id, 'Rejected delete request for: '.$req->asset_description);

        return redirect()->route('it.inventory.index', ['view' => 'pending_requests'])->with('success', 'Delete request rejected.');
    }

    // â”€â”€ E-Waste requests â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    public function approveEwaste(int $id)
    {
        $req = EwasteRequest::findOrFail($id);
        if ($req->status !== 'Pending') return back()->with('error', 'Request is no longer pending.');

        $req->update(['status' => 'Approved', 'reviewed_by' => Auth::guard('it')->id(), 'reviewed_at' => now()]);

        NotificationService::notifyUserWithEmail($req->requested_by, 'request_approved', 'E-Waste Request Approved', 'Your e-waste request has been approved.', route('it.inventory.index'));
        ActivityLogService::log('APPROVE_EWASTE', 'ewaste', (int)$req->inventory_id, 'Approved e-waste request: '.$req->description);

        return redirect()->route('it.inventory.index', ['view' => 'pending_requests'])->with('success', 'E-waste request approved.');
    }

    public function rejectEwaste(int $id)
    {
        $req = EwasteRequest::findOrFail($id);
        if ($req->status !== 'Pending') return back()->with('error', 'Request is no longer pending.');

        $req->update(['status' => 'Rejected', 'reviewed_by' => Auth::guard('it')->id(), 'reviewed_at' => now()]);
        NotificationService::notifyUserWithEmail($req->requested_by, 'request_rejected', 'E-Waste Request Rejected', 'Your e-waste request has been rejected.', route('it.inventory.index'));
        ActivityLogService::log('REJECT_EWASTE', 'ewaste', (int)$req->inventory_id, 'Rejected e-waste request: '.$req->description);

        return redirect()->route('it.inventory.index', ['view' => 'pending_requests'])->with('success', 'E-waste request rejected.');
    }

    // â”€â”€ Retract own pending requests (staff) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    public function retractAdd(int $id)
    {
        $req = AddAssetRequest::where('id', $id)->where('requested_by', Auth::guard('it')->id())->where('status', 'Pending')->firstOrFail();
        ActivityLogService::log('RETRACT_ADD', 'inventory', 0, 'Retracted add request: '.$req->description);
        $req->delete();
        return redirect()->route('it.inventory.index', ['view' => 'my_requests'])->with('success', 'Request retracted.');
    }

    public function retractEwaste(int $id)
    {
        $req = EwasteRequest::where('id', $id)->where('requested_by', Auth::guard('it')->id())->where('status', 'Pending')->firstOrFail();
        ActivityLogService::log('RETRACT_EWASTE', 'ewaste', 0, 'Retracted e-waste request: '.$req->description);
        $req->delete();
        return redirect()->route('it.inventory.index', ['view' => 'my_requests'])->with('success', 'Request retracted.');
    }

    public function retractDelete(int $id)
    {
        $req = DeleteRequest::where('id', $id)->where('requested_by', Auth::guard('it')->id())->where('status', 'Pending')->firstOrFail();
        ActivityLogService::log('RETRACT_DELETE', 'inventory', 0, 'Retracted delete request');
        $req->delete();
        return redirect()->route('it.inventory.index', ['view' => 'my_requests'])->with('success', 'Request retracted.');
    }
}

