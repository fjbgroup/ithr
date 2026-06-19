<?php

namespace App\Http\Controllers\WT;

use Illuminate\Http\Request;
use App\Models\WT\WalkieTalkie;
use App\Models\WT\MaintenanceRecord;
use App\Models\WT\UserActivityLog;
use App\Models\WT\User;
use App\Services\SystemNotifier;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    private function actionRemark(Request $request): string
    {
        return trim((string) $request->input('action_remark', ''));
    }

    private function withActionRemark(string $details, Request $request): string
    {
        $remark = $this->actionRemark($request);

        return $remark === '' ? $details : $details . ' Remark: ' . $remark;
    }

    private function isSettled(MaintenanceRecord $record): bool
    {
        return (bool) $record->done || strtoupper((string) $record->status) === 'DONE';
    }

    private function reporterFor(MaintenanceRecord $record): ?User
    {
        if (! $record->reporter_staff_id && ! $record->reporter_name) {
            return null;
        }

        return User::query()
            ->when($record->reporter_staff_id, function ($query, $staffId) {
                $query->orWhere('staff_no', $staffId);
            })
            ->when($record->reporter_name, function ($query, $name) {
                $query->orWhere('name', $name)
                    ->orWhere('staff_no', $name);
            })
            ->first();
    }

    private function notifySettledIfNeeded(MaintenanceRecord $record, bool $wasSettled): void
    {
        $record->refresh();
        if ($wasSettled || ! $this->isSettled($record)) {
            return;
        }

        $reporter = $this->reporterFor($record);
        if (! $reporter) {
            return;
        }

        SystemNotifier::notifyUser(
            $reporter,
            'Damage Report Settled',
            "Your damage report #{$record->maintenance_id} has been repaired or checked by ICT. Please collect your walkie talkie from ICT Department.",
            'approved'
        );
    }

    private function releaseTemporarySpare(MaintenanceRecord $maintenance): void
    {
        if (! $maintenance->temporary_spare_walkie_id) {
            return;
        }

        WalkieTalkie::where('walkie_id', $maintenance->temporary_spare_walkie_id)->update([
            'status' => 'UNUSED',
            'ownership_type' => 'SPARE',
            'ownership' => '',
            'position' => '',
            'department' => '',
            'remark' => 'Temporary spare/new returned from faulty report #' . $maintenance->maintenance_id,
        ]);
    }

    private function restoreOriginalWalkie(MaintenanceRecord $maintenance): void
    {
        $query = WalkieTalkie::query();

        if ($maintenance->walkie_id) {
            $query->where('walkie_id', $maintenance->walkie_id);
        } elseif ($maintenance->radio_id) {
            $query->where('radio_id', $maintenance->radio_id);
        } elseif ($maintenance->serial_number) {
            $query->where('serial_number', $maintenance->serial_number);
        } else {
            return;
        }

        $query->update([
            'status' => 'IN USE',
            'ownership_type' => $maintenance->ownership_type ?: 'INDIVIDUAL',
            'ownership' => $maintenance->current_ownership ?: $maintenance->reporter_name ?: '',
            'position' => $maintenance->designation ?: '',
            'department' => $maintenance->department_name ?: '',
            'remark' => 'Original unit returned after faulty report #' . $maintenance->maintenance_id,
        ]);
    }

    public function index()
    {
        $records = MaintenanceRecord::with('walkieTalkie')
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhereRaw("UPPER(status) <> 'DRAFT'");
            })
            ->orderByDesc('received_date')
            ->orderByDesc('repair_date')
            ->orderByDesc('finish_date')
            ->orderByDesc('maintenance_date')
            ->orderByDesc('maintenance_id')
            ->get();
        $walkies = WalkieTalkie::orderBy('serial_number')->get();

        return view('wt.admin.maintenance.index', compact('records', 'walkies'));
    }

    public function create()
    {
        $walkies = WalkieTalkie::orderBy('serial_number')->get();

        return view('wt.admin.maintenance.create', compact('walkies'));
    }

    public function edit(MaintenanceRecord $maintenance)
    {
        $walkies = WalkieTalkie::orderBy('serial_number')->get();

        return view('wt.admin.maintenance.create', [
            'walkies' => $walkies,
            'editRecord' => $maintenance,
        ]);
    }

    public function faultyReports()
    {
        $records = MaintenanceRecord::query()
            ->with('temporarySpareWalkie')
            ->where('request_source', 'user')
            ->whereNotIn('status', ['Draft'])
            ->orderByDesc('received_date')
            ->orderByDesc('repair_date')
            ->orderByDesc('finish_date')
            ->orderByDesc('maintenance_date')
            ->orderByDesc('maintenance_id')
            ->get();

        $availableSpareWalkies = WalkieTalkie::query()
            ->where(function ($query) {
                $query->whereRaw("UPPER(COALESCE(ownership_type, '')) = 'SPARE'")
                    ->orWhereRaw("UPPER(COALESCE(ownership_type, '')) = 'UNALLOCATED'")
                    ->orWhereRaw("UPPER(COALESCE(ownership_type, '')) = 'NEW'")
                    ->orWhereRaw("UPPER(COALESCE(status, '')) = 'SPARE'");
            })
            ->whereRaw("UPPER(COALESCE(status, '')) IN ('UNUSED', 'SPARE')")
            ->orderBy('radio_id')
            ->get(['walkie_id', 'radio_id', 'model', 'serial_number', 'status', 'ownership_type']);

        $summary = [
            'total' => $records->count(),
            'pending' => $records->whereIn('status', ['WAITING FOR ADMIN', 'PENDING ADMIN IT'])->count(),
            'active' => $records->whereIn('status', ['UNDER REPAIR', 'FAULTY', 'B.E.R', 'READY TO COLLECT'])->count(),
            'done' => $records->filter(fn (MaintenanceRecord $record) => $this->isSettled($record))->count(),
        ];

        return view('wt.admin.faulty_reports.index', compact('records', 'summary', 'availableSpareWalkies'));
    }

    public function updateFaultyReport(Request $request, MaintenanceRecord $maintenance)
    {
        abort_unless($maintenance->request_source === 'user', 404);

        $validated = $request->validate([
            'status' => 'required|string|in:PENDING ADMIN IT,UNDER REPAIR,FAULTY,B.E.R,READY TO COLLECT,ALREADY FIXED,DONE',
            'repair_date' => 'nullable|date',
            'finish_date' => 'nullable|date',
            'issue' => 'nullable|string|max:2000',
            'remarks' => 'nullable|string|max:2000',
            'temporary_spare_walkie_id' => 'nullable|integer|exists:walkie_talkies,walkie_id',
        ]);

        $isReadyToCollect = $validated['status'] === 'READY TO COLLECT';
        $isDone = $validated['status'] === 'DONE';
        $previousSpareId = $maintenance->temporary_spare_walkie_id;
        $selectedSpareId = $validated['temporary_spare_walkie_id'] ?? null;

        if ($selectedSpareId && (int) $selectedSpareId !== (int) $previousSpareId) {
            $spareWalkie = WalkieTalkie::query()
                ->where('walkie_id', $selectedSpareId)
                ->where(function ($query) {
                    $query->whereRaw("UPPER(COALESCE(ownership_type, '')) = 'SPARE'")
                        ->orWhereRaw("UPPER(COALESCE(status, '')) = 'SPARE'");
                })
                ->whereRaw("UPPER(COALESCE(status, '')) IN ('UNUSED', 'SPARE')")
                ->first();

            if (! $spareWalkie) {
                return back()
                    ->withInput()
                    ->withErrors(['temporary_spare_walkie_id' => 'Selected spare walkie talkie is no longer available.']);
            }
        }

        if ($previousSpareId && ((int) $previousSpareId !== (int) $selectedSpareId || $isDone)) {
            $this->releaseTemporarySpare($maintenance);
        }

        if ($selectedSpareId && ! $isDone) {
            WalkieTalkie::where('walkie_id', $selectedSpareId)->update([
                'status' => 'IN USE',
                'ownership_type' => 'SPARE',
                'ownership' => $maintenance->reporter_name ?: $maintenance->current_ownership ?: '',
                'position' => $maintenance->designation ?: '',
                'department' => $maintenance->department_name ?: '',
                'remark' => 'Temporary spare for faulty report #' . $maintenance->maintenance_id,
            ]);
        }

        $maintenance->update([
            'status' => $validated['status'],
            'temporary_spare_walkie_id' => $isDone ? null : $selectedSpareId,
            'temporary_spare_assigned_at' => $selectedSpareId && ! $isDone
                ? ($maintenance->temporary_spare_assigned_at ?: now()->toDateString())
                : null,
            'temporary_spare_returned_at' => $isDone && $previousSpareId
                ? now()->toDateString()
                : ($selectedSpareId ? null : $maintenance->temporary_spare_returned_at),
            'original_returned_at' => $isDone ? ($maintenance->original_returned_at ?: now()->toDateString()) : null,
            'original_returned_by' => $isDone ? Auth::guard('wt')->id() : null,
            'repair_date' => $validated['repair_date'] ?? null,
            'finish_date' => ($isDone || $isReadyToCollect) ? ($validated['finish_date'] ?? now()->toDateString()) : ($validated['finish_date'] ?? null),
            'done' => $isDone,
            'issue' => $validated['issue'] ?? $maintenance->issue,
            'issue_description' => $validated['issue'] ?? $maintenance->issue_description,
            'remarks' => $validated['remarks'] ?? null,
            'handled_by' => Auth::guard('wt')->id(),
        ]);

        if ($isDone) {
            $this->restoreOriginalWalkie($maintenance->fresh());
        } else {
            if ($maintenance->walkie_id) {
                WalkieTalkie::where('walkie_id', $maintenance->walkie_id)->update([
                    'status' => $validated['status'],
                ]);
            }
        }

        UserActivityLog::create([
            'user_id'       => Auth::guard('wt')->id(),
            'username'      => Auth::guard('wt')->user()->username,
            'event_type'    => 'maintenance',
            'event_action'  => 'ict_faulty_update',
            'event_details' => 'Updated user faulty report ID: ' . $maintenance->maintenance_id . ' to ' . $validated['status'],
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
            'created_at'    => now(),
        ]);

        $reporter = $this->reporterFor($maintenance->fresh());
        if ($reporter) {
            $pickupLine = $maintenance->pickup_at
                ? ' Planned pickup: ' . \Carbon\Carbon::parse($maintenance->pickup_at)->format('d M Y, h:i A') . ' by ' . ($maintenance->pickup_person ?: $maintenance->reporter_name ?: 'reporter') . '.'
                : '';
            $message = match ($validated['status']) {
                'READY TO COLLECT' => "Your damage report #{$maintenance->maintenance_id} has been repaired or checked by ICT and is ready to collect at ICT Department Sejurumus after ICT approval.{$pickupLine}",
                'ALREADY FIXED' => "ICT has marked your damage report #{$maintenance->maintenance_id} as already fixed. ICT will update you when it is ready to collect.",
                default => "ICT updated your damage report #{$maintenance->maintenance_id}. Current status: {$validated['status']}.",
            };
            if ($selectedSpareId && ! $isDone) {
                $message .= ' A temporary spare walkie talkie has been assigned while your unit is being repaired.';
            }
            if (! empty($validated['remarks'])) {
                $message .= ' ICT note: ' . $validated['remarks'];
            }

            SystemNotifier::notifyUser(
                $reporter,
                'Faulty Report Updated',
                $message,
                ($isDone || $isReadyToCollect) ? 'approved' : 'received'
            );
        }

        return redirect()->route('wt.admin.faultyReports.index')->with('success', 'Faulty report updated and user has been notified.');
    }

    public function receiveFaultyWalkie(MaintenanceRecord $maintenance)
    {
        abort_unless($maintenance->request_source === 'user', 404);

        $status = strtoupper((string) $maintenance->status);
        abort_if((bool) $maintenance->done || in_array($status, ['DONE', 'READY TO COLLECT', 'REJECTED'], true), 422);

        $maintenance->update([
            'status' => $status === 'PENDING ADMIN IT' ? 'UNDER REPAIR' : ($maintenance->status ?: 'UNDER REPAIR'),
            'ict_received_at' => now()->toDateString(),
            'ict_received_by' => Auth::guard('wt')->id(),
            'repair_date' => $maintenance->repair_date ?: now()->toDateString(),
            'handled_by' => Auth::guard('wt')->id(),
        ]);

        if ($maintenance->walkie_id) {
            WalkieTalkie::where('walkie_id', $maintenance->walkie_id)->update([
                'status' => 'UNDER REPAIR',
            ]);
        }

        UserActivityLog::create([
            'user_id'       => Auth::guard('wt')->id(),
            'username'      => Auth::guard('wt')->user()->username,
            'event_type'    => 'maintenance',
            'event_action'  => 'ict_received_faulty_walkie',
            'event_details' => 'ICT received walkie talkie for faulty report ID: ' . $maintenance->maintenance_id,
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
            'created_at'    => now(),
        ]);

        $reporter = $this->reporterFor($maintenance->fresh());
        if ($reporter) {
            SystemNotifier::notifyUser(
                $reporter,
                'Walkie Talkie Received By ICT',
                "ICT has received your walkie talkie for damage report #{$maintenance->maintenance_id}. Repair/checking is now in progress. Pickup can be done at ICT Department Sejurumus after ICT approval.",
                'received'
            );
        }

        return redirect()->route('wt.admin.faultyReports.index')->with('success', 'Walkie talkie marked as received by ICT.');
    }

    public function returnOriginalWalkie(MaintenanceRecord $maintenance)
    {
        abort_unless($maintenance->request_source === 'user', 404);

        $this->releaseTemporarySpare($maintenance);

        $maintenance->update([
            'status' => 'DONE',
            'done' => true,
            'finish_date' => $maintenance->finish_date ?: now()->toDateString(),
            'temporary_spare_returned_at' => $maintenance->temporary_spare_walkie_id ? now()->toDateString() : $maintenance->temporary_spare_returned_at,
            'temporary_spare_walkie_id' => null,
            'original_returned_at' => now()->toDateString(),
            'original_returned_by' => Auth::guard('wt')->id(),
            'handled_by' => Auth::guard('wt')->id(),
        ]);

        $this->restoreOriginalWalkie($maintenance->fresh());

        UserActivityLog::create([
            'user_id'       => Auth::guard('wt')->id(),
            'username'      => Auth::guard('wt')->user()->username,
            'event_type'    => 'maintenance',
            'event_action'  => 'ict_returned_original_walkie',
            'event_details' => 'ICT returned original walkie and closed faulty report ID: ' . $maintenance->maintenance_id,
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
            'created_at'    => now(),
        ]);

        $reporter = $this->reporterFor($maintenance->fresh());
        if ($reporter) {
            SystemNotifier::notifyUser(
                $reporter,
                'Original Walkie Talkie Returned',
                "Your faulty report #{$maintenance->maintenance_id} is complete. Any spare/new unit has been returned and your original walkie talkie has been handed back.",
                'approved'
            );
        }

        return redirect()->route('wt.admin.faultyReports.index')->with('success', 'Spare/new unit returned and original walkie talkie handed back.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'walkie_id' => 'required|integer|exists:walkie_talkies,walkie_id',
            'received_date' => 'required|date',
            'repair_date' => 'nullable|date',
            'done' => 'nullable|boolean',
            'finish_date' => 'nullable|date',
            'issue' => 'required|string|max:2000',
            'remarks' => 'nullable|string|max:2000',
            'status' => 'required|string|in:UNDER REPAIR,FAULTY,B.E.R,READY TO COLLECT,ALREADY FIXED,DONE',
        ]);

        $walkie = WalkieTalkie::findOrFail($validated['walkie_id']);

        MaintenanceRecord::create([
            'walkie_id' => $walkie->walkie_id,
            'radio_id' => $walkie->radio_id,
            'serial_number' => $walkie->serial_number,
            'model' => $walkie->model,
            'current_ownership' => $walkie->ownership,
            'department_name' => $walkie->department,
            'received_date' => $validated['received_date'],
            'repair_date' => $validated['repair_date'] ?? null,
            'done' => (bool) ($validated['done'] ?? false),
            'finish_date' => in_array($validated['status'], ['READY TO COLLECT', 'ALREADY FIXED', 'DONE'], true)
                ? ($validated['finish_date'] ?? now()->toDateString())
                : ($validated['finish_date'] ?? null),
            'issue_description' => $validated['issue'],
            'issue' => $validated['issue'],
            'remarks' => $validated['remarks'] ?? null,
            'maintenance_date' => $validated['repair_date'] ?? $validated['received_date'],
            'status' => $validated['status'],
            'request_source' => 'admin_it',
        ]);

        $walkie->update(['status' => $validated['status']]);

        // Log Activity
        UserActivityLog::create([
            'user_id'       => Auth::guard('wt')->id(),
            'username'      => Auth::guard('wt')->user()->username,
            'event_type'    => 'maintenance',
            'event_action'  => 'insert',
            'event_details' => 'Submitted repair form for Walkie Talkie serial ' . $walkie->serial_number,
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
        ]);

        return redirect()->route('wt.admin.maintenance.index')->with('success', 'Repair record for serial ' . $walkie->serial_number . ' has been submitted.');
    }

    public function update(Request $request, MaintenanceRecord $maintenance)
    {
        $wasSettled = $this->isSettled($maintenance);

        $validated = $request->validate([
            'received_date' => 'required|date',
            'repair_date' => 'nullable|date',
            'done' => 'nullable|boolean',
            'finish_date' => 'nullable|date',
            'issue' => 'required|string|max:2000',
            'remarks' => 'nullable|string|max:2000',
            'status' => 'required|string|in:UNDER REPAIR,FAULTY,B.E.R,READY TO COLLECT,ALREADY FIXED,DONE',
        ]);

        if ($validated['status'] === 'READY TO COLLECT') {
            $validated['finish_date'] = $validated['finish_date'] ?? now()->toDateString();
        }

        $maintenance->update($validated);

        if ($maintenance->walkie_id) {
            WalkieTalkie::where('walkie_id', $maintenance->walkie_id)->update(['status' => $validated['status']]);
        }

        UserActivityLog::create([
            'user_id'       => Auth::guard('wt')->id(),
            'username'      => Auth::guard('wt')->user()->username,
            'event_type'    => 'maintenance',
            'event_action'  => 'update',
            'event_details' => 'Updated repair record ID: ' . $maintenance->maintenance_id,
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
        ]);

        $this->notifySettledIfNeeded($maintenance, $wasSettled);

        $returnRoute = $request->input('return_route');
        if ($returnRoute === 'admin.maintenance.index') {
            return redirect()->route($returnRoute)->with('success', 'Maintenance record updated successfully.');
        }

        return back()->with('success', 'Maintenance record updated successfully.');
    }

    public function updateStatus(Request $request, MaintenanceRecord $maintenance)
    {
        $wasSettled = $this->isSettled($maintenance);

        if ($request->has('new_status')) {
            $validated = $request->validate([
                'new_status' => 'required|string|in:UNDER REPAIR,REPAIRING,FAULTY,B.E.R,READY TO COLLECT,ALREADY FIXED,DONE',
            ]);
            $newStatus = $validated['new_status'];

            $maintenance->update([
                'status' => $newStatus,
                'finish_date' => $newStatus === 'READY TO COLLECT' ? now()->toDateString() : $maintenance->finish_date,
                'done' => false,
                'handled_by' => Auth::guard('wt')->id(),
            ]);
            
            if ($maintenance->walkie_id) {
                WalkieTalkie::where('walkie_id', $maintenance->walkie_id)->update(['status' => $newStatus]);
            }

            UserActivityLog::create([
                'user_id'       => Auth::guard('wt')->id(),
                'username'      => Auth::guard('wt')->user()->username,
                'event_type'    => 'maintenance',
                'event_action'  => 'update_status',
                'event_details' => $this->withActionRemark('Updated maintenance record ID: ' . $maintenance->maintenance_id . ' to ' . $newStatus . '.', $request),
                'ip_address'    => request()->ip(),
                'user_agent'    => request()->userAgent(),
            ]);

            if ($newStatus === 'READY TO COLLECT' && ($reporter = $this->reporterFor($maintenance->fresh()))) {
                SystemNotifier::notifyUser(
                    $reporter,
                    'Walkie Talkie Ready To Collect',
                    "Your damage report #{$maintenance->maintenance_id} has been repaired or checked by ICT and is ready to collect at ICT Department Sejurumus.",
                    'approved'
                );
            }

            $this->notifySettledIfNeeded($maintenance, $wasSettled);

            return back()->with('success', 'Maintenance status updated to ' . $newStatus . '.');
        }

        $maintenance->update([
            'done' => true,
            'finish_date' => now()->toDateString(),
            'status' => 'DONE'
        ]);

        if ($maintenance->walkie_id) {
            WalkieTalkie::where('walkie_id', $maintenance->walkie_id)->update(['status' => 'UNUSED']);
        }

        UserActivityLog::create([
            'user_id'       => Auth::guard('wt')->id(),
            'username'      => Auth::guard('wt')->user()->username,
            'event_type'    => 'maintenance',
            'event_action'  => 'update_status',
            'event_details' => $this->withActionRemark('Marked maintenance record ID: ' . $maintenance->maintenance_id . ' as DONE.', $request),
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
        ]);

        $this->notifySettledIfNeeded($maintenance, $wasSettled);

        return back()->with('success', 'Maintenance record marked as DONE.');
    }

    public function destroy(MaintenanceRecord $maintenance)
    {
        $recordLabel = $maintenance->radio_id ?: $maintenance->serial_number ?: $maintenance->maintenance_id;
        $maintenance->delete();

        UserActivityLog::create([
            'user_id'       => Auth::guard('wt')->id(),
            'username'      => Auth::guard('wt')->user()->username,
            'event_type'    => 'maintenance',
            'event_action'  => 'delete',
            'event_details' => 'Deleted maintenance record ID: ' . $recordLabel,
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
        ]);

        return back()->with('success', 'Maintenance record deleted successfully.');
    }
}


