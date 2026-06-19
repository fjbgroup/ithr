<?php

namespace App\Http\Controllers\WT\User;

use App\Http\Controllers\WT\Controller;
use App\Models\WT\AccessRequest;
use App\Models\WT\Handover;
use App\Models\WT\UserActivityLog;
use App\Models\WT\WalkieTalkie;
use App\Models\WT\User;
use App\Services\SystemNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HandoverController extends Controller
{
    private function normalizeOwnershipType(?string $value): string
    {
        $normalized = strtolower(trim((string) $value));
        return match ($normalized) {
            'individual' => 'individual',
            'share', 'shared', 'shapper' => 'shared',
            'spare' => 'spare',
            'unallocated' => 'unallocated',
            default => 'individual',
        };
    }

    private function isSpareWalkie(WalkieTalkie $walkie): bool
    {
        return strtoupper((string) $walkie->ownership_type) === 'SPARE'
            || strtoupper((string) $walkie->status) === 'SPARE'
            || (bool) $walkie->is_special_use;
    }

    public function index()
    {
        $isIctView = request()->routeIs('admin.*')
            && auth('wt')->user()->role === 'admin_it'
            && session('view_mode', auth('wt')->user()->role) === 'admin_it';

        if (request()->routeIs('admin.*') && ! $isIctView && auth('wt')->user()->role === 'admin_it') {
            return redirect()
                ->route('wt.admin.requests.index')
                ->with('error', 'ICT direct handover is only available in ICT mode.');
        }

        if ($isIctView) {
            $directHandoverScope = request()->query('scope') === 'on_behalf' ? 'on_behalf' : 'self';
            $approvedRequest = null;
            $approvedRequestWalkies = collect();

            if (request()->filled('approved_request_id')) {
                $approvedRequest = AccessRequest::with(['user', 'handover'])
                    ->where('id', request()->query('approved_request_id'))
                    ->where('status', 'Approved')
                    ->firstOrFail();

                if ($approvedRequest->handover) {
                    return redirect()
                        ->route('wt.admin.all.status')
                        ->with('error', 'Handover for this approved request has already been submitted.');
                }

                $assignedWalkieIds = collect($approvedRequest->assigned_walkie_inventory_ids ?? [])
                    ->map(fn ($walkieId) => (int) $walkieId)
                    ->filter()
                    ->unique()
                    ->values();

                if ($assignedWalkieIds->isEmpty() && $approvedRequest->walkie_inventory_id) {
                    $assignedWalkieIds = collect([(int) $approvedRequest->walkie_inventory_id]);
                }

                $approvedRequestWalkies = WalkieTalkie::query()
                    ->whereIn('walkie_id', $assignedWalkieIds)
                    ->orderBy('radio_id')
                    ->get(['walkie_id', 'radio_id', 'model', 'serial_number', 'status']);

                $directHandoverScope = 'on_behalf';
            }

            $users = User::query()
                ->whereIn('role', $directHandoverScope === 'on_behalf' ? ['admin'] : ['admin', 'admin_it'])
                ->when($directHandoverScope === 'self', function ($query) {
                    $query->where('id', auth('wt')->id());
                })
                ->orderBy('name')
                ->orderBy('staff_no')
                ->get();

            $availableRadios = WalkieTalkie::query()
                ->where('status', 'UNUSED')
                ->orderBy('radio_id')
                ->get(['walkie_id', 'radio_id', 'model', 'serial_number', 'status']);

            $recentHandovers = Handover::query()
                ->with('walkieTalkie')
                ->when($directHandoverScope === 'self', function ($query) {
                    $query->where('user_id', auth('wt')->id());
                })
                ->when($directHandoverScope === 'on_behalf', function ($query) {
                    $query->where('user_id', '!=', auth('wt')->id());
                })
                ->orderByDesc('issued_at')
                ->orderByDesc('id')
                ->take(15)
                ->get();

            return view('wt.admin.handover.ict', compact('users', 'availableRadios', 'recentHandovers', 'directHandoverScope', 'approvedRequest', 'approvedRequestWalkies'));
        }

        $pendingHandoverRequests = AccessRequest::where('user_id', auth('wt')->id())
            ->where('status', 'Approved')
            ->orderByDesc('id')
            ->get();

        $handedOverRequestIds = Handover::where('user_id', auth('wt')->id())
            ->whereNotNull('access_request_id')
            ->pluck('access_request_id')
            ->all();

        $pendingHandovers = $pendingHandoverRequests->whereNotIn('id', $handedOverRequestIds);

        $myHandovers = Handover::where('user_id', auth('wt')->id())
            ->orderByDesc('issued_at')
            ->get();

        $statusRequests = AccessRequest::where('user_id', auth('wt')->id())
            ->where(function ($query) {
                $query->whereNull('request_type')
                    ->orWhereIn('request_type', ['walkie_talkie', 'temporary_walkie_talkie']);
            })
            ->whereIn('status', ['Pending Admin Approval', 'Pending IT Approval', 'Approved', 'Rejected'])
            ->orderByDesc('id')
            ->get();

        return view('wt.user.handover.index', compact('pendingHandovers', 'myHandovers', 'statusRequests'));
    }

    public function store(Request $request)
    {
        $isIctView = $request->routeIs('admin.*')
            && auth('wt')->user()->role === 'admin_it'
            && session('view_mode', auth('wt')->user()->role) === 'admin_it';

        if ($request->routeIs('admin.*') && ! $isIctView && $request->input('handover_mode') === 'ict_direct') {
            return redirect()
                ->route('wt.admin.requests.index')
                ->with('error', 'Executives do not perform handovers. ICT will notify the individual after approval.');
        }

        if ($isIctView && $request->input('handover_mode') === 'ict_direct') {
            return $this->storeIctDirectHandover($request);
        }

        $validated = $request->validate([
            'access_request_id' => 'required|integer',
            'pickup_response' => 'required|in:yes,not_yet,representative',
            'representative_name' => 'nullable|required_if:pickup_response,representative|string|max:255',
            'pickup_collection_note' => 'nullable|required_if:pickup_response,not_yet|string|max:255',
        ]);

        $accessRequest = AccessRequest::where('id', $validated['access_request_id'])
            ->where('status', 'Approved')
            ->when($request->routeIs('admin.*'), function ($query) {
                $query->where(function ($inner) {
                    $inner->where('user_id', auth('wt')->id())
                        ->orWhere('submit_to_admin_id', auth('wt')->id());
                });
            }, function ($query) {
                $query->where('user_id', auth('wt')->id());
            })
            ->firstOrFail();

        if (Handover::where('access_request_id', $accessRequest->id)->exists()) {
            return redirect()->route($request->routeIs('admin.*') ? 'admin.all.status' : 'login')
                ->with('error', 'Handover for this approved request has already been submitted.');
        }

        if ($validated['pickup_response'] === 'not_yet') {
            $collectionNote = trim((string) ($validated['pickup_collection_note'] ?? ''));

            DB::transaction(function () use ($accessRequest, $collectionNote) {
                UserActivityLog::create([
                    'user_id' => auth('wt')->id(),
                    'username' => auth('wt')->user()->username,
                    'event_type' => 'action',
                    'event_action' => 'Pickup Not Yet',
                    'event_details' => "Marked pickup as not yet for Request #{$accessRequest->id}. Expected collection: {$collectionNote}",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_at' => now(),
                ]);

                $existingRemark = trim((string) $accessRequest->approval_remark);
                $pickupRemark = "Pickup not yet. Expected collection: {$collectionNote}.";
                $accessRequest->update([
                    'approval_remark' => trim($existingRemark . ($existingRemark !== '' ? "\n" : '') . $pickupRemark),
                ]);

                SystemNotifier::notifyUser(
                    auth('wt')->user(),
                    'Pickup Response Submitted',
                    "You selected Not Yet for request #{$accessRequest->id}. Expected collection: {$collectionNote}.",
                    'received'
                );

                if ($accessRequest->submit_to_admin_id && (int) $accessRequest->submit_to_admin_id !== (int) auth('wt')->id()) {
                    SystemNotifier::notifyUser(
                        (int) $accessRequest->submit_to_admin_id,
                        'Recipient Pickup Pending',
                        "The recipient selected Not Yet for request #{$accessRequest->id}. Expected collection: {$collectionNote}.",
                        'received'
                    );
                }

                $itUsers = User::where('role', 'admin_it')->get();
                SystemNotifier::notifyUsers(
                    $itUsers,
                    'Recipient Pickup Pending',
                    "The recipient selected Not Yet for request #{$accessRequest->id}. Expected collection: {$collectionNote}.",
                    'received'
                );
            });

            return redirect()
                ->route($request->routeIs('admin.*') ? 'admin.all.status' : 'login')
                ->with('success', 'Pickup marked as not yet.');
        }

        $representativeName = trim((string) ($validated['representative_name'] ?? ''));
        $pickupStaffName = $validated['pickup_response'] === 'representative'
            ? $representativeName
            : $accessRequest->full_name;
        $pickupNotes = $validated['pickup_response'] === 'representative'
            ? "Representative {$representativeName} will pick up at ICT Department for {$accessRequest->full_name}."
            : 'Recipient confirmed pickup at ICT Department.';

        DB::transaction(function () use ($validated, $accessRequest, $pickupStaffName, $pickupNotes, $representativeName) {
            Handover::create([
                'access_request_id' => $accessRequest->id,
                'user_id' => auth('wt')->id(),
                'radio_id' => $accessRequest->radio_id,
                'walkie_talkie_id' => (string) $accessRequest->radio_id,
                'staff_name' => $pickupStaffName,
                'staff_no' => $accessRequest->staff_id ?? '',
                'position' => $accessRequest->position,
                'department' => $accessRequest->department,
                'notes' => $pickupNotes,
                'issued_at' => now()->toDateString(),
            ]);

            $accessRequest->update([
                'status' => 'Approved',
            ]);

            UserActivityLog::create([
                'user_id' => auth('wt')->id(),
                'username' => auth('wt')->user()->username,
                'event_type' => 'action',
                'event_action' => 'Submit',
                'event_details' => $validated['pickup_response'] === 'representative'
                    ? "Assigned representative {$representativeName} to pick up Request #{$accessRequest->id}"
                    : "Confirmed pickup for Request #{$accessRequest->id}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            $itUsers = User::where('role', 'admin_it')->get();
            SystemNotifier::notifyUsers(
                $itUsers,
                'Recipient Pickup Confirmed',
                $validated['pickup_response'] === 'representative'
                    ? "Representative {$representativeName} will pick up Request #{$accessRequest->id}."
                    : "The recipient confirmed pickup for Request #{$accessRequest->id}.",
                'received'
            );

            SystemNotifier::notifyUser(
                auth('wt')->user(),
                'Pickup Confirmed',
                $validated['pickup_response'] === 'representative'
                    ? "Your representative {$representativeName} has been recorded for Request #{$accessRequest->id}."
                    : "Your pickup confirmation for Request #{$accessRequest->id} has been recorded.",
                'received'
            );

            if ($accessRequest->submit_to_admin_id && (int) $accessRequest->submit_to_admin_id !== (int) auth('wt')->id()) {
                SystemNotifier::notifyUser(
                    (int) $accessRequest->submit_to_admin_id,
                    'Recipient Pickup Confirmed',
                    $validated['pickup_response'] === 'representative'
                        ? "Representative {$representativeName} will pick up Request #{$accessRequest->id}."
                        : "The recipient confirmed pickup for Request #{$accessRequest->id}.",
                    'received'
                );
            }
        });

        $message = $validated['pickup_response'] === 'representative'
            ? 'Representative pickup submitted successfully.'
            : 'Pickup confirmation submitted successfully.';

        return redirect()->route($request->routeIs('admin.*') ? 'admin.all.status' : 'login')->with('success', $message);
    }

    private function storeIctDirectHandover(Request $request)
    {
        $validated = $request->validate([
            'direct_handover_scope' => 'nullable|in:self,on_behalf',
            'access_request_id' => 'nullable|integer|exists:access_requests,id',
            'user_id' => ['nullable', 'required_without:access_request_id', 'integer', Rule::exists(User::class, 'id')],
            'walkie_inventory_id' => 'nullable|required_without:access_request_id|integer|exists:walkie_talkies,walkie_id',
            'staff_name' => 'required|string|max:255',
            'staff_no' => 'nullable|string|max:255',
            'ownership_type' => 'required|string|in:INDIVIDUAL,SHARED,SPARE',
            'shared_with' => 'nullable|string|max:255|required_if:ownership_type,SHARED',
            'position' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'issued_at' => 'required|date',
            'notes' => 'nullable|string|max:2000',
        ]);

        $directHandoverScope = $validated['direct_handover_scope'] ?? 'self';
        $accessRequest = null;

        if (! empty($validated['access_request_id'])) {
            $accessRequest = AccessRequest::query()
                ->where('id', $validated['access_request_id'])
                ->where('status', 'Approved')
                ->firstOrFail();

            if (Handover::where('access_request_id', $accessRequest->id)->exists()) {
                return back()
                    ->withInput()
                    ->withErrors(['access_request_id' => 'Handover for this approved request has already been submitted.']);
            }

            $validated['user_id'] = $accessRequest->user_id ?: auth('wt')->id();
            $directHandoverScope = 'on_behalf';
        } elseif ($directHandoverScope === 'self') {
            $validated['user_id'] = auth('wt')->id();
        }

        $targetUser = User::query()
            ->when(! $accessRequest, fn ($query) => $query->whereIn('role', ['admin', 'admin_it']))
            ->where('id', $validated['user_id'])
            ->firstOrFail();

        $walkies = collect();
        if ($accessRequest) {
            $assignedWalkieIds = collect($accessRequest->assigned_walkie_inventory_ids ?? [])
                ->map(fn ($walkieId) => (int) $walkieId)
                ->filter()
                ->unique()
                ->values();

            if ($assignedWalkieIds->isEmpty() && $accessRequest->walkie_inventory_id) {
                $assignedWalkieIds = collect([(int) $accessRequest->walkie_inventory_id]);
            }

            $walkies = WalkieTalkie::query()
                ->whereIn('walkie_id', $assignedWalkieIds)
                ->get();
        } else {
            $walkies = WalkieTalkie::query()
                ->where('walkie_id', $validated['walkie_inventory_id'])
                ->where('status', 'UNUSED')
                ->get();
        }

        if ($walkies->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['walkie_inventory_id' => 'Please select an available walkie talkie that is still marked UNUSED.']);
        }

        DB::transaction(function () use ($validated, $targetUser, $walkies, $accessRequest) {
            $radioIds = $walkies->pluck('radio_id')->filter()->values();
            $serialNumbers = $walkies->pluck('serial_number')->filter()->values();

            Handover::create([
                'access_request_id' => $accessRequest?->id,
                'user_id' => $targetUser->id,
                'radio_id' => $radioIds->implode(', '),
                'walkie_talkie_id' => $radioIds->implode(', '),
                'staff_name' => $validated['staff_name'],
                'shared_with' => $validated['ownership_type'] === 'SHARED' ? ($validated['shared_with'] ?? null) : null,
                'staff_no' => $validated['staff_no'] ?? '',
                'position' => $validated['position'],
                'department' => $validated['department'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'issued_at' => $validated['issued_at'],
            ]);

            $walkies->each(function (WalkieTalkie $walkie) use ($validated, $accessRequest) {
                $ownershipType = $this->isSpareWalkie($walkie)
                    ? 'SPARE'
                    : $validated['ownership_type'];

                $walkie->update([
                    'status' => 'IN USE',
                    'ownership_type' => $ownershipType,
                    'shared_with' => $ownershipType === 'SHARED' ? ($validated['shared_with'] ?? null) : null,
                    'ownership' => $validated['staff_name'],
                    'position' => $validated['position'],
                    'department' => $validated['department'] ?? '',
                    'remark' => $validated['notes'] ?? ($accessRequest ? "ICT handover for request #{$accessRequest->id}." : 'ICT direct handover to individual.'),
                ]);
            });

            UserActivityLog::create([
                'user_id' => auth('wt')->id(),
                'username' => auth('wt')->user()->username,
                'event_type' => 'action',
                'event_action' => 'ICT Direct Handover',
                'event_details' => "ICT handed over Radio {$radioIds->implode(', ')} / Serial {$serialNumbers->implode(', ')} to {$validated['staff_name']}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            SystemNotifier::notifyUser(
                $targetUser,
                'Walkie Talkie Handover',
                "ICT telah menyerahkan walkie talkie {$radioIds->implode(', ')} kepada anda.",
                'approved'
            );
        });

        return redirect()
            ->route('wt.admin.all.status')
            ->with('success', 'Walkie talkie handover recorded and the unit is now marked as IN USE.');
    }
}


