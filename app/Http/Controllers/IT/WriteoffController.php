<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\EwasteItem;
use App\Models\IT\EwasteRequest;
use App\Models\IT\InventoryItem;
use App\Models\IT\NonItAsset;
use App\Models\IT\User;
use App\Services\IT\ActivityLogService;
use App\Services\IT\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WriteoffController extends Controller
{
    public function index()
    {
        $user     = Auth::guard('it')->user();
        $houUsers = User::where('it_role', 'hou')->where('is_active', true)
                        ->orderBy('dept_name')->orderBy('name')->get();

        $savedSigUrl = ($user->signature_img && Storage::disk('public')->exists($user->signature_img))
            ? route('profile.signature.image')
            : '';

        // â”€â”€ HOU â”€â”€
        $myHouQueue = $myHouCount = $houHistory = $houHistoryCount = null;
        if ($user->isHOU()) {
            $myHouQueue     = EwasteItem::with(['creator', 'inventoryItem'])
                ->where('checked_by_user_id', $user->id)
                ->where('disposal_status', 'Pending')
                ->where('hou_status', 'Pending')
                ->orderBy('created_at')->get();
            $myHouCount     = $myHouQueue->count();
            $houHistory     = EwasteItem::with(['creator'])
                ->where('checked_by_user_id', $user->id)
                ->whereIn('hou_status', ['Checked', 'Rejected'])
                ->orderByDesc('hou_signed_at')->limit(50)->get();
            $houHistoryCount = $houHistory->count();
        }

        // â”€â”€ GM â”€â”€
        $myGmQueue = $myGmCount = $gmHistory = $gmHistoryCount = null;
        if ($user->isGM()) {
            $myGmQueue   = EwasteItem::with(['creator', 'houUser'])
                ->where('current_gm_user_id', $user->id)
                ->where('gm_status', 'Pending')
                ->where('disposal_status', 'Pending')
                ->orderBy('gm_assigned_at')->get();
            $myGmCount   = $myGmQueue->count();
            $gmHistory   = EwasteItem::with(['creator', 'houUser'])
                ->where(fn($q) => $q->where('gm1_user_id', $user->id)->orWhere('gm2_user_id', $user->id))
                ->whereIn('gm_status', ['Checked', 'Rejected'])
                ->orderByDesc('gm_signed_at')->limit(50)->get();
            $gmHistoryCount = $gmHistory->count();
        }

        // â”€â”€ CEO â”€â”€
        $myCeoQueue = $myCeoCount = $ceoHistory = $ceoHistoryCount = null;
        if ($user->isCEO()) {
            $myCeoQueue      = EwasteItem::with(['creator', 'houUser'])
                ->where('disposal_status', 'Pending')
                ->where('gm_status', 'Checked')
                ->where('ceo_status', 'Pending')
                ->orderByDesc('gm_signed_at')->orderByDesc('created_at')->get();
            $myCeoCount      = $myCeoQueue->count();
            $ceoHistory      = EwasteItem::with(['creator', 'houUser'])
                ->whereIn('ceo_status', ['Approved', 'Rejected'])
                ->orderByDesc('ceo_signed_at')->limit(50)->get();
            $ceoHistoryCount = $ceoHistory->count();
        }

        // â”€â”€ Finance Admin â”€â”€
        $faPending = collect(); $faPendingCount = 0; $faProcessed = collect();
        if ($user->isFinanceAdmin()) {
            $faPending      = EwasteItem::with(['creator', 'houUser'])
                ->where('ceo_status', 'Approved')
                ->where('finance_status', 'Pending')
                ->orderByRaw("COALESCE(batch_id,'') DESC")
                ->orderByDesc('ceo_signed_at')->get();
            $faPendingCount = $faPending->count();
            $faProcessed    = EwasteItem::with(['creator'])
                ->where('ceo_status', 'Approved')
                ->whereIn('finance_status', ['EWaste', 'Disposal'])
                ->orderByRaw("COALESCE(batch_id,'') DESC")
                ->orderByDesc('updated_at')->limit(100)->get();
        }

        // â”€â”€ Pending Final Approval (all roles) â”€â”€
        $woQueue = EwasteItem::with(['creator', 'houUser'])
            ->where('disposal_status', 'Pending')
            ->where(fn($q) => $q->whereNull('ceo_status')->orWhere('ceo_status', 'Pending'))
            ->where(fn($q) => $q->where('gm_status', 'Checked')
                ->orWhere(fn($q2) => $q2->where('hou_status', 'Checked')->whereNull('gm1_user_id')))
            ->orderByDesc('gm_signed_at')->orderByDesc('hou_signed_at')->orderByDesc('created_at')
            ->get();
        $woCount = $woQueue->count();

        // â”€â”€ My Write-Offs (non-HOU/GM/CEO) â”€â”€
        // Only show submissions from the past 3 months that haven't been dismissed
        $myWoItems = collect(); $myWoCount = 0;
        if (!$user->isHOU() && !$user->isGM() && !$user->isCEO()) {
            $myWoItems = EwasteItem::where('created_by', $user->id)
                ->whereNull('dismissed_at')
                ->where('created_at', '>=', now()->subMonths(3))
                ->orderByRaw("COALESCE(batch_id,'') DESC")->orderByDesc('created_at')->get();
            $myWoCount = $myWoItems->count();
        }

        // â”€â”€ Form display items (loaded from URL params when user comes from inventory page) â”€â”€
        $woItem = $nitItem = null;
        $bulkItems = $bulkNitItems = collect();
        $bulkIdsRaw = trim(request('bulk_ids', ''));
        $bulkNitIdsRaw = trim(request('bulk_nit_ids', ''));
        $userStaffId = $user->department ?? '';

        if ($itemId = (int)request('item_id')) {
            $woItem = InventoryItem::find($itemId);
        }
        if ($nitId = (int)request('nit_id')) {
            $nitItem = NonItAsset::find($nitId);
        }
        if ($bulkIdsRaw) {
            $safeIds = array_filter(array_map('intval', explode(',', $bulkIdsRaw)));
            if ($safeIds) $bulkItems = InventoryItem::whereIn('id', $safeIds)->get();
        }
        if ($bulkNitIdsRaw) {
            $safeNitIds = array_filter(array_map('intval', explode(',', $bulkNitIdsRaw)));
            if ($safeNitIds) $bulkNitItems = NonItAsset::whereIn('id', $safeNitIds)->get();
        }

        return view('it.writeoff.index', compact(
            'user', 'houUsers', 'savedSigUrl',
            'myHouQueue', 'myHouCount', 'houHistory', 'houHistoryCount',
            'myGmQueue', 'myGmCount', 'gmHistory', 'gmHistoryCount',
            'myCeoQueue', 'myCeoCount', 'ceoHistory', 'ceoHistoryCount',
            'faPending', 'faPendingCount', 'faProcessed',
            'woQueue', 'woCount',
            'myWoItems', 'myWoCount',
            'woItem', 'nitItem', 'bulkItems', 'bulkNitItems',
            'bulkIdsRaw', 'bulkNitIdsRaw', 'userStaffId'
        ));
    }

    public function houSign(Request $request)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isHOU()) abort(403);

        $ewIds    = array_unique(array_filter(array_map('intval', explode(',', $request->hou_sign_id ?? ''))));
        $action   = $request->hou_action ?? 'approve';
        $remark   = $request->hou_remark ?? '';
        $sigImg   = $request->hou_sig_img ?? '';
        $ewId     = $ewIds[0] ?? 0;

        $ew = EwasteItem::where('id', $ewId)->where('checked_by_user_id', $user->id)->where('hou_status', 'Pending')->first();
        if (!$ew) return back()->with('error', 'Item not found or already processed.');

        if ($action === 'reject') {
            $toRevert = EwasteItem::whereIn('id', $ewIds)->where('checked_by_user_id', $user->id)->where('hou_status', 'Pending')->get();
            foreach ($toRevert as $revertEw) { $this->setAssetStatus($revertEw, 'Active'); }
            EwasteItem::whereIn('id', $ewIds)->where('checked_by_user_id', $user->id)->where('hou_status', 'Pending')
                ->update(['hou_status' => 'Rejected', 'disposal_status' => 'Rejected', 'hou_signed_name' => $user->full_name, 'hou_sig_img' => $sigImg, 'hou_signed_at' => now(), 'hou_remark' => $remark]);
            ActivityLogService::log('HOU_REJECTED', 'ewaste', $ewId, 'HOU rejected write-off: ' . $ew->description);
            if ($ew->created_by) NotificationService::notifyUser($ew->created_by, 'writeoff', 'âŒ Write-Off Rejected by HOU', $user->full_name . ' (HOU) rejected the write-off.', route('it.writeoff.index'));
            return redirect()->route('it.writeoff.index')->with('success', 'Write-off rejected.');
        }

        $gms  = User::where('it_role', 'gm')->orderBy('id')->take(2)->pluck('id');
        $gm1  = $gms[0] ?? null;
        $gm2  = $gms[1] ?? null;
        $curGm = $gm1 ?? $gm2;

        EwasteItem::whereIn('id', $ewIds)->where('checked_by_user_id', $user->id)->where('hou_status', 'Pending')
            ->update(['hou_status' => 'Checked', 'hou_signed_name' => $user->full_name, 'hou_sig_img' => $sigImg, 'hou_signed_at' => now(), 'hou_remark' => $remark, 'gm1_user_id' => $gm1, 'gm2_user_id' => $gm2, 'current_gm_user_id' => $curGm, 'gm_assigned_at' => now(), 'gm_status' => 'Pending']);
        ActivityLogService::log('HOU_CHECKED', 'ewaste', $ewId, 'HOU checked write-off: ' . $ew->description);
        if ($curGm) NotificationService::notifyUser($curGm, 'writeoff', 'âœï¸ Write-Off Awaiting GM Signature', $user->full_name . ' (HOU) has checked the write-off for "' . $ew->description . '" â€” please review and sign.', route('it.writeoff.index'));
        return redirect()->route('it.writeoff.index')->with('success', 'Write-off forwarded to GM.');
    }

    public function gmSign(Request $request)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isGM()) abort(403);

        $ewIds  = array_unique(array_filter(array_map('intval', explode(',', $request->gm_sign_id ?? ''))));
        $ewId   = $ewIds[0] ?? 0;
        $action = $request->gm_action ?? 'approve';
        $remark = $request->gm_remark ?? '';
        $sigImg = $request->gm_sig_img ?? '';

        $ew = EwasteItem::where('id', $ewId)->where('current_gm_user_id', $user->id)->where('gm_status', 'Pending')->first();
        if (!$ew) return back()->with('error', 'Item not found.');

        if ($action === 'reject') {
            $toRevert = EwasteItem::whereIn('id', $ewIds)->where('current_gm_user_id', $user->id)->where('gm_status', 'Pending')->get();
            foreach ($toRevert as $r) { $this->setAssetStatus($r, 'Active'); }
            EwasteItem::whereIn('id', $ewIds)->where('current_gm_user_id', $user->id)->where('gm_status', 'Pending')
                ->update(['gm_status' => 'Rejected', 'disposal_status' => 'Rejected', 'gm_signed_name' => $user->full_name, 'gm_sig_img' => $sigImg, 'gm_signed_at' => now(), 'gm_remark' => $remark]);
            ActivityLogService::log('GM_REJECTED', 'ewaste', $ewId, 'GM rejected write-off: ' . $ew->description);
            if ($ew->created_by) NotificationService::notifyUser($ew->created_by, 'writeoff', 'âŒ Write-Off Rejected by GM', $user->full_name . ' (GM) rejected the write-off.', route('it.writeoff.index'));
            if ($ew->checked_by_user_id) NotificationService::notifyUser($ew->checked_by_user_id, 'writeoff', 'âŒ Write-Off Rejected by GM', $user->full_name . ' (GM) rejected the write-off that you had checked.', route('it.writeoff.index'));
            return redirect()->route('it.writeoff.index')->with('success', count($ewIds) . ' write-off(s) rejected.');
        }

        $ceo = User::where('it_role', 'ceo')->first();
        EwasteItem::whereIn('id', $ewIds)->where('current_gm_user_id', $user->id)->where('gm_status', 'Pending')
            ->update(['gm_status' => 'Checked', 'gm_signed_name' => $user->full_name, 'gm_sig_img' => $sigImg, 'gm_signed_at' => now(), 'gm_remark' => $remark, 'ceo_user_id' => $ceo?->id, 'ceo_status' => 'Pending']);
        ActivityLogService::log('GM_CHECKED', 'ewaste', $ewId, 'GM checked write-off: ' . $ew->description);
        if ($ceo) NotificationService::notifyUser($ceo->id, 'writeoff', 'âœï¸ Write-Off Awaiting Your CEO Approval', $user->full_name . ' (General Manager) signed ' . count($ewIds) . ' write-off item(s) â€” please review and approve or reject.', route('it.writeoff.index'));
        return redirect()->route('it.writeoff.index')->with('success', count($ewIds) . ' write-off(s) forwarded to CEO.');
    }

    public function ceoApprove(Request $request)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isCEO() && !$user->isAdmin()) abort(403);

        $ewIds  = array_unique(array_filter(array_map('intval', explode(',', $request->ceo_sign_id ?? ''))));
        $ewId   = $ewIds[0] ?? 0;
        $action = $request->ceo_action ?? 'approve';
        $remark = $request->ceo_remark ?? '';
        $sigImg = $request->ceo_sig_img ?? '';

        $ew = EwasteItem::findOrFail($ewId);

        if ($action === 'reject') {
            $items = EwasteItem::whereIn('id', $ewIds)->get();
            foreach ($items as $item) {
                $this->setAssetStatus($item, 'Active');
                if ($item->original_inventory_id) {
                    \App\Models\InventoryItem::where('id', $item->original_inventory_id)->update(['location' => '']);
                }
            }
            EwasteItem::whereIn('id', $ewIds)->update(['ceo_status' => 'Rejected', 'disposal_status' => 'Rejected', 'ceo_signed_name' => $user->full_name, 'ceo_sig_img' => $sigImg, 'ceo_signed_at' => now(), 'ceo_remark' => $remark]);
            ActivityLogService::log('CEO_REJECTED', 'ewaste', $ewId, 'CEO rejected write-off.');
            if ($ew->created_by) NotificationService::notifyUser($ew->created_by, 'writeoff', 'âŒ Write-Off Rejected by CEO', $user->full_name . ' (CEO) rejected the write-off.', route('it.writeoff.index'));
            return redirect()->route('it.writeoff.index')->with('success', count($ewIds) . ' write-off(s) rejected.');
        }

        EwasteItem::whereIn('id', $ewIds)->update(['ceo_status' => 'Approved', 'disposal_status' => 'Approved', 'finance_status' => 'Pending', 'ceo_signed_name' => $user->full_name, 'ceo_sig_img' => $sigImg, 'ceo_signed_at' => now(), 'ceo_remark' => $remark]);
        $items = EwasteItem::whereIn('id', $ewIds)->get();
        foreach ($items as $item) { $this->setAssetStatus($item, 'Pending to E-Waste/Disposal'); }
        ActivityLogService::log('CEO_APPROVED', 'ewaste', $ewId, 'CEO approved ' . count($ewIds) . ' write-off item(s).');
        NotificationService::notifyAdmins('writeoff', 'âœ… Write-Off Approved by CEO', 'CEO approved ' . count($ewIds) . ' write-off item(s).', route('it.writeoff.index'));
        return redirect()->route('it.writeoff.index')->with('success', count($ewIds) . ' write-off(s) approved.');
    }

    public function assignToHOU(Request $request)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdmin()) abort(403);

        $ewIds   = array_map('intval', $request->selected_ids ?? []);
        $houId   = (int)$request->hou_user_id;
        $houUser = User::find($houId);
        if (!$houUser || !$houUser->isHOU()) return back()->with('error', 'Invalid HOU selected.');

        foreach ($ewIds as $id) {
            EwasteItem::where('id', $id)->update(['checked_by_user_id' => $houId, 'hou_status' => 'Pending']);
            NotificationService::notifyUser($houId, 'writeoff', 'âœï¸ Write-Off Assigned to You', 'A write-off has been assigned for your review.', route('it.writeoff.index'));
        }
        ActivityLogService::log('ASSIGN_HOU', 'ewaste', 0, 'Assigned ' . count($ewIds) . ' write-off items to HOU: ' . $houUser->full_name);
        return back()->with('success', 'Write-off assigned to HOU.');
    }

    public function rejectWriteoff($id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->canApproveWriteOff()) abort(403);

        $item = EwasteItem::find($id);
        if ($item) {
            $this->setAssetStatus($item, 'Active');
            if ($item->original_inventory_id) {
                \App\Models\InventoryItem::where('id', $item->original_inventory_id)->update(['location' => '']);
            }
            ActivityLogService::log('REJECT_WRITEOFF', 'ewaste', $id, 'Rejected write-off: ' . $item->description);
            $item->delete();
            return redirect()->route('it.writeoff.index')->with('success', 'Write-off rejected. Item restored to IT Assets.');
        }
        return redirect()->route('it.writeoff.index')->with('error', 'Item not found.');
    }

    public function submitWriteoff(Request $request)
    {
        $user   = Auth::guard('it')->user();

        $woName = trim($request->writeoff_name ?? '');
        $woDes  = trim($request->writeoff_designation ?? '');
        $woDate = $request->writeoff_date ?: now()->toDateString();
        $sigImg = $request->writeoff_sig_img ?? '';
        $houId  = (int)($request->hou_user_id ?? 0);

        if (!$houId) {
            return back()->with('error', 'Please select an HOU user.');
        }
        if (!$woName) {
            return back()->with('error', 'Write-off name is required.');
        }

        // Optional proof file upload
        $proofPath = null;
        if ($request->hasFile('writeoff_proof') && $request->file('writeoff_proof')->isValid()) {
            $file      = $request->file('writeoff_proof');
            $filename  = 'proof_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('writeoff_proofs', $filename, 'public');
            $proofPath = 'writeoff_proofs/' . $filename;
        }

        // Resolve item lists
        $itItems  = collect();
        $nitItems = collect();
        $batchId  = null;

        if ($itemId = (int)($request->item_id ?? 0)) {
            $inv = InventoryItem::find($itemId);
            if ($inv) $itItems->push($inv);
        }
        if ($nitId = (int)($request->nit_item_id ?? 0)) {
            $nit = NonItAsset::find($nitId);
            if ($nit) $nitItems->push($nit);
        }
        if ($rawBulk = trim($request->bulk_item_ids ?? '')) {
            $ids = array_unique(array_filter(array_map('intval', explode(',', $rawBulk))));
            if ($ids) {
                $itItems = InventoryItem::whereIn('id', $ids)->get();
                if ($itItems->count() > 1) $batchId = uniqid('BATCH_');
            }
        }
        if ($rawNit = trim($request->bulk_nit_item_ids ?? '')) {
            $ids = array_unique(array_filter(array_map('intval', explode(',', $rawNit))));
            if ($ids) {
                $nitItems = NonItAsset::whereIn('id', $ids)->get();
                if ($nitItems->count() > 1 && !$batchId) $batchId = uniqid('BATCH_');
            }
        }

        if ($itItems->isEmpty() && $nitItems->isEmpty()) {
            return back()->with('error', 'No items selected for write-off.');
        }

        $common = [
            'writeoff_name'        => $woName,
            'writeoff_designation' => $woDes,
            'writeoff_date'        => $woDate,
            'writeoff_sig_img'     => $sigImg,
            'created_by'           => $user->id,
            'date_flagged'         => now()->toDateString(),
            'disposal_status'      => 'Pending',
            'hou_status'           => 'Pending',
            'checked_by_user_id'   => $houId,
            'batch_id'             => $batchId,
            'finance_status'       => null,
        ];

        $created = [];

        foreach ($itItems as $inv) {
            if (EwasteItem::where('original_inventory_id', $inv->id)->where('disposal_status', 'Pending')->exists()) {
                continue;
            }
            $ew = EwasteItem::create(array_merge($common, [
                'asset_number'          => $inv->asset_number,
                'asset_class'           => $inv->asset_class,
                'description'           => $inv->description,
                'serial_number'         => $inv->serial_number,
                'brand'                 => $inv->brand,
                'model'                 => $inv->model,
                'original_inventory_id' => $inv->id,
                'asset_source'          => 'IT',
                'notes'                 => $proofPath,
            ]));
            $created[] = $ew;
            $inv->update(['item_status' => 'Pending for Write-Off']);
        }

        foreach ($nitItems as $nit) {
            $noteParts = [];
            if ($nit->id) $noteParts[] = 'NIT_ID:' . $nit->id;
            if ($proofPath) $noteParts[] = $proofPath;
            $ew = EwasteItem::create(array_merge($common, [
                'asset_number'  => $nit->asset_number,
                'asset_class'   => $nit->asset_class,
                'description'   => $nit->description,
                'serial_number' => $nit->serial_number,
                'asset_source'  => 'NIT',
                'notes'         => implode('|', $noteParts),
            ]));
            $created[] = $ew;
            $nit->update(['item_status' => 'Pending for Write-Off']);
        }

        if (empty($created)) {
            return back()->with('error', 'No items could be submitted (they may already be in the write-off queue).');
        }

        $count = count($created);
        ActivityLogService::log('SUBMIT_WRITEOFF', 'ewaste', $created[0]->id, 'Submitted ' . $count . ' item(s) for write-off.');

        NotificationService::notifyUser($houId, 'writeoff', 'âœï¸ Write-Off Awaiting Your Signature',
            $user->full_name . ' submitted ' . $count . ' item(s) for write-off. Please review and sign.',
            route('it.writeoff.index'));

        return redirect()->route('it.writeoff.index')->with('success', $count . ' item(s) submitted for write-off successfully.');
    }

    public function report(int $id)
    {
        $item  = EwasteItem::with(['creator', 'houUser', 'currentGmUser', 'ceoUser'])->findOrFail($id);
        $items = $item->batch_id
            ? EwasteItem::where('batch_id', $item->batch_id)->get()
            : collect([$item]);
        return view('it.writeoff.report', compact('item', 'items'));
    }

    public function approveWriteoff(int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->canApproveWriteOff()) abort(403);

        $ew = EwasteItem::findOrFail($id);
        $ew->update([
            'ceo_status'      => 'Approved',
            'disposal_status' => 'Approved',
            'finance_status'  => 'Pending',
            'ceo_signed_name' => $user->full_name,
            'ceo_signed_at'   => now(),
        ]);
        $this->setAssetStatus($ew, 'Pending to E-Waste/Disposal');
        if ($ew->original_inventory_id) {
            InventoryItem::where('id', $ew->original_inventory_id)->update(['location' => 'E-Waste']);
        }
        ActivityLogService::log('CEO_APPROVED', 'ewaste', $id, 'Approved write-off: ' . $ew->description);
        if ($ew->created_by) {
            NotificationService::notifyUser($ew->created_by, 'writeoff', 'âœ… Write-Off Approved', $user->full_name . ' approved the write-off.', route('it.writeoff.index'));
        }
        return redirect()->route('it.writeoff.index')->with('success', 'Write-off approved.');
    }

    public function approveAllWriteoffs()
    {
        $user = Auth::guard('it')->user();
        if (!$user->canApproveWriteOff() || $user->isAdmin()) abort(403);

        $items = EwasteItem::where('disposal_status', 'Pending')
            ->where(fn($q) => $q->whereNull('ceo_status')->orWhere('ceo_status', 'Pending'))
            ->where(fn($q) => $q->where('gm_status', 'Checked')
                ->orWhere(fn($q2) => $q2->where('hou_status', 'Checked')->whereNull('gm1_user_id')))
            ->get();

        foreach ($items as $ew) {
            $ew->update(['ceo_status' => 'Approved', 'disposal_status' => 'Approved', 'finance_status' => 'Pending', 'ceo_signed_name' => $user->full_name, 'ceo_signed_at' => now()]);
            $this->setAssetStatus($ew, 'Pending to E-Waste/Disposal');
            if ($ew->original_inventory_id) {
                InventoryItem::where('id', $ew->original_inventory_id)->update(['location' => 'E-Waste']);
            }
            if ($ew->created_by) {
                NotificationService::notifyUser($ew->created_by, 'writeoff', 'âœ… Write-Off Approved', $user->full_name . ' approved the write-off.', route('it.writeoff.index'));
            }
        }
        ActivityLogService::log('CEO_APPROVE_ALL', 'ewaste', 0, 'Approved all ' . $items->count() . ' pending write-offs.');
        return redirect()->route('it.writeoff.index')->with('success', $items->count() . ' write-off(s) approved.');
    }

    private function setAssetStatus(EwasteItem $ew, string $status): void
    {
        if ($ew->asset_source === 'IT' && $ew->original_inventory_id) {
            InventoryItem::where('id', $ew->original_inventory_id)
                ->update(['item_status' => $status]);
        } elseif ($ew->asset_source === 'NIT' && $ew->notes) {
            foreach (explode('|', $ew->notes) as $part) {
                if (str_starts_with($part, 'NIT_ID:')) {
                    NonItAsset::where('id', (int) substr($part, 7))
                        ->update(['item_status' => $status]);
                    break;
                }
            }
        }
    }

    public function routeBatch(Request $request)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isFinanceAdmin()) abort(403);

        $rawIds = trim($request->batch_ids ?? '');
        $route  = $request->route_to ?? '';
        $ewIds  = array_unique(array_filter(array_map('intval', explode(',', $rawIds))));

        if (empty($ewIds) || !in_array($route, ['ewaste', 'disposal'])) {
            return back()->with('error', 'Invalid request.');
        }

        $finStatus = $route === 'ewaste' ? 'EWaste' : 'Disposal';
        $newLoc    = $route === 'ewaste' ? 'E-Waste' : 'Disposal';

        $items     = EwasteItem::whereIn('id', $ewIds)->where('ceo_status', 'Approved')->where('finance_status', 'Pending')->get();
        $firstItem = null;
        foreach ($items as $item) {
            if (!$firstItem) $firstItem = $item;
            $item->update(['finance_status' => $finStatus, 'disposal_status' => 'Approved']);
            if ($item->original_inventory_id) {
                \App\Models\InventoryItem::where('id', $item->original_inventory_id)->update(['location' => $newLoc]);
            }
            ActivityLogService::log('FINANCE_ROUTE', 'ewaste', $item->id, 'Finance routed write-off to ' . $finStatus . ': ' . $item->description);
        }

        if ($firstItem && $firstItem->created_by) {
            $label = count($ewIds) > 1 ? count($ewIds) . ' write-off item(s)' : '"' . $firstItem->description . '"';
            NotificationService::notifyUser(
                $firstItem->created_by, 'writeoff',
                'âœ… Write-Off Routed to ' . ($finStatus === 'EWaste' ? 'E-Waste' : 'Disposal'),
                $user->full_name . ' (Finance) has routed the write-off for ' . $label . ' to ' . ($finStatus === 'EWaste' ? 'E-Waste' : 'Disposal') . '.',
                route('it.writeoff.index')
            );
        }

        return redirect()->route('it.writeoff.index')->with('success', 'Write-off routed to ' . ($finStatus === 'EWaste' ? 'E-Waste' : 'Disposal') . ' successfully.');
    }

    // Dismiss a single batch from "My Write-Off Submissions"
    public function dismissBatch(Request $request)
    {
        $user    = Auth::guard('it')->user();
        $batchId = $request->input('batch_id');
        $itemId  = $request->input('item_id');

        $query = EwasteItem::where('created_by', $user->id);

        if ($batchId) {
            $query->where('batch_id', $batchId);
        } elseif ($itemId) {
            $query->where('id', $itemId);
        } else {
            return back();
        }

        $query->update(['dismissed_at' => now()]);

        return back();
    }

    // Dismiss all submissions from "My Write-Off Submissions"
    public function dismissAll(Request $request)
    {
        $user = Auth::guard('it')->user();

        EwasteItem::where('created_by', $user->id)
            ->whereNull('dismissed_at')
            ->where('created_at', '>=', now()->subMonths(3))
            ->update(['dismissed_at' => now()]);

        return back();
    }
}

