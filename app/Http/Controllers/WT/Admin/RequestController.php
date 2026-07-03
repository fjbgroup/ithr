<?php

namespace App\Http\Controllers\WT\Admin;

use App\Http\Controllers\WT\Controller;
use Illuminate\Http\Request;
use App\Models\WT\AccessRequest;
use App\Models\WT\MasterData;
use App\Models\WT\MaintenanceRecord;
use App\Models\WT\User;
use App\Models\WT\UserActivityLog;
use App\Models\WT\WalkieTalkie;
use App\Models\Staff;
use App\Services\SystemNotifier;
use App\Services\TemporaryRequestExpiryService;
use Illuminate\Support\Str;

class RequestController extends Controller
{
    private function effectiveWtRole(): ?string
    {
        $actualRole = auth('wt')->user()?->wt_role;

        if ($actualRole === 'admin_it') {
            return session('view_mode', $actualRole);
        }

        return $actualRole;
    }

    private function activeExecutiveAccount(): ?User
    {
        $user = auth('wt')->user();

        if (! $user) {
            return null;
        }

        if ($user->wt_role === 'admin_it' && session('view_mode') === 'admin') {
            $selectedExecutiveUserId = session('selected_executive_user_id');

            return $selectedExecutiveUserId
                ? User::where('wt_role', 'admin')->find($selectedExecutiveUserId)
                : null;
        }

        return $user->wt_role === 'admin' ? $user : null;
    }

    private function redirectIfNotExecutiveRequestView()
    {
        if ($this->effectiveWtRole() !== 'admin') {
            return redirect()->route('wt.admin.dashboard');
        }

        if (auth('wt')->user()?->wt_role === 'admin_it' && ! $this->activeExecutiveAccount()) {
            return redirect()
                ->route('wt.admin.dashboard')
                ->with('error', 'Please select an Executive account first.');
        }

        return null;
    }

    /**
     * HR staff lookup for executives registering ownership on behalf of their
     * team. Returns active staff records so the owner can be tied to a real HR
     * staff record instead of being free-typed.
     */
    public function staffSearch(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = Staff::with('department')
            ->where('is_active', 1)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('staff_no', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(15)
            ->get()
            ->map(fn ($s) => [
                'staff_no'  => $s->staff_no,
                'name'      => Str::upper($s->name),
                'dept_name' => Str::upper($s->department?->name ?? ''),
                'position'  => Str::upper($s->position ?? ''),
                'phone'     => $s->phone_number ?? '',
            ]);

        return response()->json($results);
    }

    private function isSpareWalkie(WalkieTalkie $walkie): bool
    {
        return in_array(strtoupper((string) $walkie->ownership_type), ['SPARE'], true)
            || strtoupper((string) $walkie->status) === 'SPARE'
            || (bool) $walkie->is_special_use;
    }

    private function returnedWalkiePayload(WalkieTalkie $walkie): array
    {
        $isSpare = $this->isSpareWalkie($walkie);

        return [
            'status' => 'UNUSED',
            'ownership_type' => $isSpare ? 'SPARE' : 'UNALLOCATED',
            'shared_with' => null,
            'ownership' => '',
            'position' => '',
            'department' => '',
            'temporary_radio_id' => '',
            'remark' => '',
            'tracking_ref' => '',
            'need_to_change_id' => false,
            'id_change_done' => false,
            'ownership_type_to_be' => null,
            'is_special_use' => $isSpare ? (bool) $walkie->is_special_use : false,
            'special_use_returned' => false,
        ];
    }

    protected function walkieRequestTypes(): array
    {
        return ['walkie_talkie', 'temporary_walkie_talkie'];
    }

    protected function applyWalkieRequestTypeFilter($query)
    {
        return $query->where(function ($requestTypeQuery) {
            $requestTypeQuery->whereNull('request_type')
                ->orWhereIn('request_type', $this->walkieRequestTypes());
        });
    }

    protected function applyExecutiveRequestFilter($query)
    {
        return $query->where(function ($executiveQuery) {
            $executiveQuery
                ->whereHas('submitToAdmin', fn ($userQuery) => $userQuery->where('wt_role', 'admin'))
                ->orWhereHas('user', fn ($userQuery) => $userQuery->where('wt_role', 'admin'));
        });
    }

    protected function applyExecutiveSubmittedFilter($query)
    {
        return $query->whereHas('submitToAdmin', fn ($userQuery) => $userQuery->where('wt_role', 'admin'));
    }

    protected function requestVariantViewData(string $requestVariant): array
    {
        return [
            'requestVariant' => $requestVariant,
            'isTemporaryRequest' => $requestVariant === 'temporary',
        ];
    }


    public function index()
    {
        $actualRole = auth('wt')->user()->wt_role;
        $userRole = $actualRole === 'admin_it'
            ? session('view_mode', $actualRole)
            : $actualRole;

        if ($userRole === 'admin') {
            return redirect()->route('wt.admin.requests.create.shared');
        }

        $pendingRequests = AccessRequest::with(['handover', 'user', 'submitToAdmin', 'handler'])
            ->where('status', $userRole === 'admin_it' ? 'Pending IT Approval' : 'Pending Admin Approval')
            ->where(function ($query) {
                $this->applyExecutiveRequestFilter($query);
            })
            ->when($userRole === 'admin', function ($q) {
                $q->where(function ($qq) {
                    $qq->whereNull('submit_to_admin_id')
                        ->orWhere('submit_to_admin_id', auth('wt')->id());
                });
            })
            ->where(function ($query) {
                $this->applyWalkieRequestTypeFilter($query);
            })
            ->orderBy('full_name', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $pendingReturns = AccessRequest::with(['user', 'submitToAdmin', 'handler'])
            ->where('return_status', $userRole === 'admin_it' ? 'Pending IT Approval' : 'Pending Admin Approval')
            ->where(function ($query) {
                $this->applyExecutiveRequestFilter($query);
            })
            ->when($userRole === 'admin', function ($q) {
                $q->where(function ($qq) {
                    $qq->whereNull('submit_to_admin_id')
                        ->orWhere('submit_to_admin_id', auth('wt')->id());
                });
            })
            ->orderBy('id', 'asc')
            ->get();

        $pendingDamageReports = MaintenanceRecord::with(['handler', 'submitToAdmin'])
            ->where('status', $userRole === 'admin_it' ? 'PENDING ADMIN IT' : 'WAITING FOR ADMIN')
            ->where(function ($query) {
                $this->applyExecutiveSubmittedFilter($query);
            })
            ->when($userRole === 'admin', function ($q) {
                $q->where('submit_to_admin_id', auth('wt')->id());
            })
            ->orderByDesc('maintenance_id')
            ->get();

        $activeDamageStatuses = [
            'WAITING FOR ADMIN',
            'PENDING ADMIN IT',
            'FAULTY',
            'UNDER REPAIR',
            'REPAIRING',
            'B.E.R',
            'READY TO COLLECT',
            'ALREADY FIXED',
        ];

        $walkies = WalkieTalkie::orderBy('walkie_id', 'desc')->get();
        $availableRadios = WalkieTalkie::query()
            ->where('status', 'UNUSED')
            ->whereDoesntHave('maintenanceRecords', function ($query) use ($activeDamageStatuses) {
                $query->whereIn('status', $activeDamageStatuses);
            })
            ->orderBy('walkie_id', 'desc')
            ->get();
        $walkieRadioIds = $walkies->pluck('radio_id')->filter()->unique()->sort()->values();
        $walkieSerials = $walkies->pluck('serial_number')->filter()->unique()->sort()->values();
        $walkieModels = collect(['R7', 'P8200', 'P8268', 'P8600I', 'P8660I', 'P8260'])
            ->merge($walkies->pluck('model')->filter())
            ->unique()
            ->sort()
            ->values();
        $walkieOwnerships = $walkies->pluck('ownership')->filter()->unique()->sort()->values();
        $walkieDepartments = $walkies->pluck('department')->filter()->unique()->sort()->values();
        $walkiePositions = $walkies->pluck('position')
            ->filter(fn ($value) => ! MasterData::isBlockedValue('position', $value))
            ->unique()
            ->sort()
            ->values();
        $ownershipTypeOptions = collect(['INDIVIDUAL', 'SHARED', 'SPARE'])
            ->merge(
                $walkies->pluck('ownership_type')
                    ->merge($walkies->pluck('ownership_type_to_be'))
                    ->filter()
                    ->map(fn ($value) => strtoupper(trim((string) $value)))
            )
            ->filter(fn ($value) => in_array(strtoupper(trim((string) $value)), ['INDIVIDUAL', 'SHARED', 'SPARE'], true))
            ->unique()
            ->sort()
            ->values();
        
        return view('wt.admin.requests.index', compact(
            'pendingRequests',
            'pendingReturns',
            'pendingDamageReports',
            'availableRadios',
            'userRole',
            'walkieRadioIds',
            'walkieSerials',
            'walkieModels',
            'walkieOwnerships',
            'walkieDepartments',
            'walkiePositions',
            'ownershipTypeOptions'
        ));
    }

    public function history()
    {
        abort_unless(auth('wt')->user()->wt_role === 'admin_it', 403);

        TemporaryRequestExpiryService::syncExpired();

        $returnHistoryCutoff = $this->returnHistoryCutoff();

        $historyRequests = collect();

        $historyDamageReports = MaintenanceRecord::with(['handler', 'submitToAdmin'])
            ->whereIn('status', ['UNDER REPAIR', 'READY TO COLLECT', 'ALREADY FIXED', 'DONE', 'REJECTED'])
            ->where(function ($query) {
                $this->applyExecutiveSubmittedFilter($query);
            })
            ->orderByDesc('maintenance_id')
            ->get();

        return view('wt.admin.requests.history', compact('historyRequests', 'historyDamageReports'));
    }

    public function createShared()
    {
        if ($redirect = $this->redirectIfNotExecutiveRequestView()) {
            return $redirect;
        }

        $currentUser = $this->activeExecutiveAccount() ?: auth('wt')->user();

        return view('wt.admin.requests.create_shared', array_merge(
            compact('currentUser'),
            $this->requestVariantViewData('standard')
        ));
    }

    public function createMenu()
    {
        return $this->createShared();
    }

    public function createIndividual()
    {
        return redirect()->route('wt.admin.requests.create.shared');
    }

    public function createTemporaryMenu()
    {
        return view('wt.admin.requests.create_menu', $this->requestVariantViewData('temporary'));
    }

    public function createTemporaryShared()
    {
        if ($redirect = $this->redirectIfNotExecutiveRequestView()) {
            return $redirect;
        }

        $currentUser = $this->activeExecutiveAccount() ?: auth('wt')->user();

        return view('wt.admin.requests.create_shared', array_merge(
            compact('currentUser'),
            $this->requestVariantViewData('temporary')
        ));
    }

    public function createTemporaryIndividual()
    {
        return redirect()->route('wt.admin.requests.create.temporary');
    }

    public function store(Request $request)
    {
        return $this->storeManagerRequest($request, 'walkie_talkie');
    }

    public function storeTemporary(Request $request)
    {
        return $this->storeManagerRequest($request, 'temporary_walkie_talkie');
    }

    protected function storeManagerRequest(Request $request, string $requestType)
    {
        if ($redirect = $this->redirectIfNotExecutiveRequestView()) {
            return $redirect;
        }

        $submitAction = $request->input('submit_action') === 'draft' ? 'draft' : 'submit';
        $isDraft = $submitAction === 'draft';
        $isTemporaryRequest = $requestType === 'temporary_walkie_talkie';
        $requestLabel = $isTemporaryRequest ? 'temporary walkie talkie request' : 'walkie talkie request';
        $requestScope = $request->input('request_scope') === 'on_behalf' ? 'on_behalf' : 'self';
        $isSelfRequest = $requestScope === 'self';
        $activeExecutive = $this->activeExecutiveAccount();
        $submitterUser = $activeExecutive ?: auth('wt')->user();
        $submitterUserId = $submitterUser?->id ?? auth('wt')->id();

        $validated = $request->validate([
            'request_scope' => 'nullable|in:self,on_behalf',
            'ownership_type' => $isDraft ? 'nullable|in:Individual,Shared' : 'required|in:Individual,Shared',
            'shared_with' => $isDraft
                ? 'nullable|string|max:255'
                : 'nullable|required_if:ownership_type,Shared|string|max:255',
            'full_name' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'staff_id' => $isSelfRequest && ! $isDraft ? 'required|string|max:255' : 'nullable|string|max:255',
            'department' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'request_date' => $isTemporaryRequest
                ? ($isDraft ? 'nullable|date' : 'required|date')
                : ($isDraft ? 'nullable|date' : 'required|date'),
            'end_date' => $isTemporaryRequest
                ? ($isDraft ? 'nullable|date|after_or_equal:request_date' : 'required|date|after_or_equal:request_date')
                : 'nullable|date',
            'duration_days' => $isTemporaryRequest
                ? 'nullable|integer|min:1|max:365'
                : 'nullable|integer|min:1|max:365',
            'location' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'event_name' => $isTemporaryRequest
                ? ($isDraft ? 'nullable|string|max:255' : 'required|string|max:255')
                : 'nullable|string|max:255',
            'quantity' => $isTemporaryRequest
                ? ($isDraft ? 'nullable|integer|min:1|max:999' : 'required|integer|min:1|max:999')
                : 'nullable|integer|min:1|max:999',
            'pic_details' => 'nullable|array',
            'pic_details.*.staff_no' => 'nullable|string|max:255',
            'pic_details.*.name' => ! $isSelfRequest && ! $isDraft ? 'required|string|max:255' : 'nullable|string|max:255',
            'pic_details.*.phone_no' => 'nullable|string|max:255',
            'pic_details.*.department' => ! $isSelfRequest && ! $isDraft ? 'required|string|max:255' : 'nullable|string|max:255',
            'pic_details.*.ownership_type' => ! $isSelfRequest && ! $isDraft ? 'required|in:Individual,Shared' : 'nullable|in:Individual,Shared',
            'pic_details.*.bay_from' => 'nullable|string|max:255',
            'pic_details.*.location' => ! $isSelfRequest && ! $isDraft ? 'required|string|max:255' : 'nullable|string|max:255',
            'pic_details.*.pickup_person' => ! $isSelfRequest && ! $isDraft ? 'required|string|max:255' : 'nullable|string|max:255',
            'pic_details.*.pickup_phone_no' => ! $isSelfRequest && ! $isDraft ? 'required|string|max:255' : 'nullable|string|max:255',
            'pickup_method' => 'nullable|in:self,representative',
            'pickup_representative_name' => 'nullable|string|max:255',
            'requested_pickup_at' => $isSelfRequest && ! $isDraft ? 'required|date' : 'nullable|date',
            'pickup_note' => 'nullable|string|max:1000',
            'justifications' => $isDraft ? 'nullable|string|max:2000' : 'required|string|max:2000',
            'bay_from' => 'nullable|string|max:255',
            'request_signature' => $isDraft
                ? ['nullable', 'string', 'regex:/^data:image\/png;base64,/']
                : ['required', 'string', 'regex:/^data:image\/png;base64,/'],
        ]);

        $quantity = max(1, (int) ($validated['quantity'] ?? 1));
        $picDetails = collect($validated['pic_details'] ?? [])
            ->map(function ($pic) {
                return [
                    'staff_no' => trim((string) ($pic['staff_no'] ?? '')),
                    'name' => trim((string) ($pic['name'] ?? '')),
                    'phone_no' => trim((string) ($pic['phone_no'] ?? '')),
                    'department' => trim((string) ($pic['department'] ?? '')),
                    'ownership_type' => trim((string) ($pic['ownership_type'] ?? '')),
                    'bay_from' => trim((string) ($pic['bay_from'] ?? '')),
                    'location' => trim((string) ($pic['location'] ?? '')),
                    'pickup_person' => trim((string) ($pic['pickup_person'] ?? '')),
                    'pickup_phone_no' => trim((string) ($pic['pickup_phone_no'] ?? '')),
                ];
            })
            ->filter(fn ($pic) => $pic['name'] !== '' || $pic['phone_no'] !== '' || $pic['department'] !== '' || $pic['ownership_type'] !== '' || $pic['location'] !== '' || $pic['pickup_person'] !== '' || $pic['pickup_phone_no'] !== '')
            ->values();

        if (! $isSelfRequest && ! $isDraft) {
            if ($picDetails->count() !== $quantity) {
                return back()
                    ->withErrors(['pic_details' => "Please enter ownership details for exactly {$quantity} unit(s)."])
                    ->withInput();
            }

            $missingPic = $picDetails->first(fn ($pic) => $pic['name'] === '' || $pic['department'] === '' || $pic['ownership_type'] === '' || $pic['location'] === '' || $pic['pickup_person'] === '' || $pic['pickup_phone_no'] === '');
            if ($missingPic) {
                return back()
                    ->withErrors(['pic_details' => 'Each unit must have one ownership name, department, ownership type, location, pickup person, and pickup phone no. Ownership phone no is optional.'])
                    ->withInput();
            }

            $firstPic = $picDetails->first();
            $validated['full_name'] = $firstPic['name'];
            $validated['department'] = $firstPic['department'];
        }

        $computedDurationDays = null;
        if ($isTemporaryRequest && ! empty($validated['request_date']) && ! empty($validated['end_date'])) {
            $startDate = \Carbon\Carbon::parse($validated['request_date'])->startOfDay();
            $endDate = \Carbon\Carbon::parse($validated['end_date'])->startOfDay();
            $computedDurationDays = $startDate->diffInDays($endDate) + 1;
        }

        $accessRequest = AccessRequest::create([
            'user_id' => $isSelfRequest ? $submitterUserId : null,
            'request_type' => $requestType,
            'full_name' => $validated['full_name'] ?? null,
            'staff_id' => $validated['staff_id'] ?? null,
            'request_date' => $validated['request_date'] ?? null,
            'end_date' => $isTemporaryRequest ? ($validated['end_date'] ?? null) : null,
            'department' => $validated['department'] ?? null,
            'position' => $validated['position'] ?? null,
            'ownership_type' => $isTemporaryRequest ? ($validated['ownership_type'] ?? 'Individual') : ($validated['ownership_type'] ?? null),
            'shared_with' => strcasecmp((string) ($validated['ownership_type'] ?? ''), 'Shared') === 0
                ? ($validated['shared_with'] ?? null)
                : null,
            'bay_from' => $validated['bay_from'] ?? null,
            'location' => $validated['location'] ?? null,
            'event_name' => $isTemporaryRequest
                ? ($validated['event_name'] ?? null)
                : ($validated['event_name'] ?? 'General Request'),
            'quantity' => $quantity,
            'duration_days' => $isTemporaryRequest ? $computedDurationDays : null,
            'pic_details' => $picDetails->isNotEmpty() ? $picDetails->all() : null,
            'pickup_method' => $validated['pickup_method'] ?? 'self',
            'pickup_representative_name' => ($validated['pickup_method'] ?? null) === 'representative'
                ? ($validated['pickup_representative_name'] ?? null)
                : null,
            'requested_pickup_at' => ! empty($validated['requested_pickup_at'])
                ? \Carbon\Carbon::parse($validated['requested_pickup_at'])
                : null,
            'pickup_note' => $validated['pickup_note'] ?? null,
            'justifications' => $validated['justifications'] ?? null,
            'request_signature' => $validated['request_signature'] ?? null,
            'status' => $isDraft ? 'Draft' : 'Pending IT Approval',
            'submit_to_admin_id' => $submitterUserId,
        ]);

        if (! $isDraft) {
            $itUsers = User::where('wt_role', 'admin_it')->get();
            SystemNotifier::notifyUsers(
                $itUsers,
                'Permohonan Baru Diterima',
                "Permohonan #{$accessRequest->id} menunggu semakan ICT.",
                'request_submitted'
            );

            SystemNotifier::notifyUser(
                $submitterUser,
                'Permohonan Berjaya Dihantar',
                "Permohonan #{$accessRequest->id} telah dihantar kepada ICT untuk semakan.",
                'request_sent'
            );
        }

        UserActivityLog::create([
            'user_id' => auth('wt')->id(),
            'username' => auth('wt')->user()->username,
            'event_type' => 'action',
            'event_action' => $isDraft
                ? ($isTemporaryRequest ? 'Admin Save Draft Temporary Request' : 'Admin Save Draft Request')
                : ($isTemporaryRequest ? 'Admin Create Temporary Request' : 'Admin Create Request'),
            'event_details' => $isDraft
                ? "Executive saved draft {$requestScope} {$requestLabel} (#{$accessRequest->id})"
                : ($isSelfRequest
                    ? "Executive created a self {$validated['ownership_type']} {$requestLabel} (#{$accessRequest->id})"
                    : "Executive created an on behalf {$validated['ownership_type']} {$requestLabel} (#{$accessRequest->id}) for {$validated['full_name']}"),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        if ($isDraft) {
            return redirect()->route('wt.admin.requests.status')->with('success', $isTemporaryRequest
                ? 'Temporary request draft saved successfully. The selected recipient can view it in request status.'
                : 'Draft saved successfully. The selected recipient can view it in request status.'
            );
        }

        $successMessage = $isTemporaryRequest
                ? 'Temporary request submitted successfully. ICT has been notified.'
                : 'Request submitted successfully. ICT has been notified.';

        return redirect()
            ->route('wt.admin.requests.create.shared')
            ->with('popup_success', $successMessage)
            ->with('popup_redirect', route('wt.admin.all.status'));
    }

    public function forwardToIT($id)
    {
        $req = AccessRequest::findOrFail($id);
        $req->update([
            'status' => 'Pending IT Approval',
            'handled_by' => auth('wt')->id(),
        ]);

        $itUsers = User::where('wt_role', 'admin_it')->get();
        SystemNotifier::notifyUsers(
            $itUsers,
            'Permohonan Diteruskan Ke ICT',
            "Permohonan #{$req->id} telah disahkan executive dan menunggu tindakan ICT.",
            'received'
        );
        SystemNotifier::notifyUser(
            $req->user_id ? (int) $req->user_id : null,
            'Permohonan Anda Diterima Executive',
            "Permohonan #{$req->id} telah diterima executive dan diteruskan ke ICT.",
            'received'
        );

        UserActivityLog::create([
            'user_id' => auth('wt')->id(),
            'username' => auth('wt')->user()->username,
            'event_type' => 'action',
            'event_action' => 'Forward To IT',
            'event_details' => "Forwarded request #{$req->id} to ICT approval",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('wt.admin.requests.index')->with('success', 'Request approved by Admin and forwarded to ICT.');
    }

    public function approve(Request $request, $id)
    {
        $validated = $request->validate([
            'walkie_inventory_ids' => 'required|array|min:1',
            'walkie_inventory_ids.*' => 'integer|distinct|exists:walkie_talkies,walkie_id',
            'assignment_details' => 'nullable|array',
            'assignment_details.*.ownership_type' => 'nullable|string|in:INDIVIDUAL,SHARED,SPARE',
            'assignment_details.*.ownership' => 'nullable|string|max:255',
            'assignment_details.*.shared_with' => 'nullable|string|max:255',
            'assignment_details.*.department' => 'required|string|max:255',
            'assignment_details.*.position' => 'nullable|string|max:255',
            'accessories' => 'nullable|array',
            'accessories.*' => 'string|max:255',
            'approval_remark' => 'nullable|string|max:2000',
        ]);
        $req = AccessRequest::with('handover')->findOrFail($id);
        $requiredQuantity = max(1, (int) ($req->quantity ?: 1));
        $selectedIds = collect($validated['walkie_inventory_ids'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($selectedIds->count() !== $requiredQuantity) {
            $unitLabel = \Illuminate\Support\Str::plural('unit', $requiredQuantity);

            return back()
                ->withErrors(['walkie_inventory_ids' => "Please select exactly {$requiredQuantity} {$unitLabel} for this request."])
                ->withInput();
        }

        $walkies = WalkieTalkie::whereIn('walkie_id', $selectedIds)->get();

        if ($walkies->count() !== $requiredQuantity) {
            return back()
                ->withErrors(['walkie_inventory_ids' => 'One or more selected units could not be found.'])
                ->withInput();
        }

        $unavailableWalkies = $walkies->filter(fn (WalkieTalkie $walkie) => strtoupper((string) $walkie->status) !== 'UNUSED');
        if ($unavailableWalkies->isNotEmpty()) {
            $unavailableLabels = $unavailableWalkies
                ->map(fn (WalkieTalkie $walkie) => $walkie->radio_id ?: $walkie->serial_number ?: $walkie->walkie_id)
                ->implode(', ');

            return back()
                ->withErrors(['walkie_inventory_ids' => "Selected unit(s) are no longer available: {$unavailableLabels}. Please choose unused units only."])
                ->withInput();
        }

        $selectedWalkies = $selectedIds
            ->map(fn ($walkieId) => $walkies->firstWhere('walkie_id', $walkieId))
            ->filter()
            ->values();
        $primaryWalkie = $selectedWalkies->first();
        $radioIds = $selectedWalkies->pluck('radio_id')->filter()->values();
        $serialNumbers = $selectedWalkies->pluck('serial_number')->filter()->values();
        $approvalRemark = trim((string) ($validated['approval_remark'] ?? ''));
        $assignmentDetails = collect($validated['assignment_details'] ?? []);
        $approvedAccessories = collect($validated['accessories'] ?? [])
            ->map(fn ($accessory) => trim((string) $accessory))
            ->filter()
            ->unique()
            ->values();

        $scheduleSummary = '';
        if ($req->request_type === 'temporary_walkie_talkie') {
            $startDate = $req->request_date ? \Carbon\Carbon::parse($req->request_date)->format('d M Y') : '-';
            $endDate = $req->end_date ? \Carbon\Carbon::parse($req->end_date)->format('d M Y') : '-';
            $days = max(1, (int) ($req->duration_days ?: 1));
            $dayLabel = \Illuminate\Support\Str::plural('day', $days);
            $scheduleSummary = " Temporary period: {$startDate} - {$endDate}, {$days} {$dayLabel}.";
        }

        $req->update([
            'status' => 'Pending Executive Pickup',
            'radio_id' => $radioIds->implode(', '),
            'walkie_inventory_id' => $primaryWalkie->walkie_id,
            'assigned_walkie_inventory_ids' => $selectedIds->all(),
            'assigned_radio_ids' => $radioIds->all(),
            'assigned_serial_number' => $serialNumbers->implode(', '),
            'assigned_serial_numbers' => $serialNumbers->all(),
            'accessories' => $approvedAccessories->isNotEmpty() ? $approvedAccessories->implode(', ') : null,
            'approval_remark' => $approvalRemark !== '' ? $approvalRemark : null,
            'handled_by' => auth('wt')->id(),
        ]);

        $picDetails = collect($req->pic_details ?? []);

        $selectedWalkies->each(function (WalkieTalkie $walkie, int $index) use ($assignmentDetails, $picDetails, $req) {
            $submittedDetails = collect($assignmentDetails->get((string) $walkie->walkie_id, $assignmentDetails->get($walkie->walkie_id, [])));
            $pic = collect($picDetails->get($index, []));
            $details = collect([
                'ownership_type' => $submittedDetails->get('ownership_type') ?: $pic->get('ownership_type') ?: $req->ownership_type,
                'ownership' => $submittedDetails->get('ownership') ?: $pic->get('name') ?: $req->full_name,
                'shared_with' => $submittedDetails->get('shared_with') ?: $pic->get('shared_with') ?: $req->shared_with,
                'department' => $submittedDetails->get('department') ?: $pic->get('department') ?: $req->department,
                'position' => $submittedDetails->get('position') ?: $req->position,
                'remark' => $submittedDetails->get('remark'),
            ]);
            $isSpareUnit = $this->isSpareWalkie($walkie);
            $ownershipType = $isSpareUnit
                ? 'SPARE'
                : strtoupper(trim((string) ($details->get('ownership_type') ?: $req->ownership_type ?: 'INDIVIDUAL')));
            if (! in_array($ownershipType, ['INDIVIDUAL', 'SHARED', 'SPARE'], true)) {
                $ownershipType = 'INDIVIDUAL';
            }

            $walkie->update([
                'status' => 'IN USE',
                'ownership_type' => $ownershipType,
                'shared_with' => $ownershipType === 'SHARED' ? (trim((string) $details->get('shared_with')) ?: null) : null,
                'ownership' => trim((string) $details->get('ownership')) ?: null,
                'department' => trim((string) $details->get('department')),
                'position' => trim((string) $details->get('position')) ?: null,
                'remark' => trim((string) $details->get('remark'))
                    ?: ($isSpareUnit ? 'Temporary spare assignment for request #' . $req->id : null),
            ]);
        });

        $unitLabel = \Illuminate\Support\Str::plural('unit', $requiredQuantity);
        $preferredPickup = $req->requested_pickup_at
            ? ' Preferred pickup: ' . \Carbon\Carbon::parse($req->requested_pickup_at)->format('d M Y H:i') . '.'
            : '';
        $pickupLink = $req->user_id ? route('wt.user.handover.pickup', $req->id) : null;
        $pickupMessage = "Request #{$req->id} has been approved for {$requiredQuantity} {$unitLabel} for {$req->full_name}. Your walkie talkie is ready to collect at ICT Department. Please open Pickup and sign the handover form." . $preferredPickup . $scheduleSummary;

        if ($approvalRemark !== '') {
            $pickupMessage .= " ICT remark: {$approvalRemark}";
        }
        if ($approvedAccessories->isNotEmpty()) {
            $pickupMessage .= " Accessories: {$approvedAccessories->implode(', ')}.";
        }
        if ($pickupLink) {
            $pickupMessage .= " Pickup link: {$pickupLink}";
        }

        SystemNotifier::notifyUser(
            $req->user_id ? (int) $req->user_id : null,
            'Walkie Talkie Ready To Collect',
            $pickupMessage,
            'approved'
        );

        
        // Log Activity
        UserActivityLog::create([
            'user_id' => auth('wt')->id(),
            'username' => auth('wt')->user()->username,
            'event_type' => 'action',
            'event_action' => 'Approve and Handover',
            'event_details' => "ICT approved request #{$req->id}, prepared handover, and assigned {$requiredQuantity} {$unitLabel}: Radio {$radioIds->implode(', ')} / Serial {$serialNumbers->implode(', ')}" . ($approvalRemark !== '' ? " with remark: {$approvalRemark}" : ''),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
        
        return redirect()
            ->route('wt.admin.requests.index')
            ->with('success', 'Request approved and prepared for pickup handover.');
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'disapproval_remark' => 'required|string|max:2000',
        ]);

        $disapprovalRemark = trim((string) ($validated['disapproval_remark'] ?? ''));

        $req = AccessRequest::findOrFail($id);
        $req->update([
            'status' => 'Rejected',
            'approval_remark' => $disapprovalRemark !== '' ? $disapprovalRemark : $req->approval_remark,
            'handled_by' => auth('wt')->id(),
        ]);

        $userMessage = "Permohonan #{$req->id} telah disapproved.";
        if ($disapprovalRemark !== '') {
            $userMessage .= " Remark ICT: {$disapprovalRemark}";
        }

        SystemNotifier::notifyUser(
            $req->user_id ? (int) $req->user_id : null,
            'Permohonan Disapproved',
            $userMessage,
            'rejected'
        );


        UserActivityLog::create([
            'user_id' => auth('wt')->id(),
            'username' => auth('wt')->user()->username,
            'event_type' => 'action',
            'event_action' => 'Disapprove',
            'event_details' => "Disapproved request #{$req->id} for {$req->full_name}" . ($disapprovalRemark !== '' ? " with remark: {$disapprovalRemark}" : ''),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('wt.admin.requests.index')->with('success', 'Request disapproved.');
    }

    public function confirmReturn($id)
    {
        $req = AccessRequest::findOrFail($id);

        if (auth('wt')->user()->wt_role === 'admin') {
            $req->update([
                'return_status' => 'Pending IT Approval',
                'handled_by' => auth('wt')->id(),
            ]);

            $itUsers = User::where('wt_role', 'admin_it')->get();
            SystemNotifier::notifyUsers(
                $itUsers,
                'Return Diteruskan Ke ICT',
                "Return untuk Request #{$req->id} telah disahkan executive dan menunggu ICT.",
                'received'
            );
            SystemNotifier::notifyUser(
                $req->user_id ? (int) $req->user_id : null,
                'Return Anda Diterima Executive',
                "Return untuk Request #{$req->id} telah diterima executive dan diteruskan ke ICT.",
                'received'
            );

            UserActivityLog::create([
                'user_id' => auth('wt')->id(),
                'username' => auth('wt')->user()->username,
                'event_type' => 'action',
                'event_action' => 'Forward Return To IT',
                'event_details' => "Forwarded return for Radio {$req->radio_id} from Request #{$req->id} to ICT",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            return redirect()->route('wt.admin.requests.index')->with('success', 'Return reviewed by Admin and forwarded to ICT.');
        }

        $req->update([
            'return_status' => 'Returned',
            'return_date' => $req->return_date ?: now()->toDateString(),
            'handled_by' => auth('wt')->id(),
        ]);

        SystemNotifier::notifyUser(
            $req->user_id ? (int) $req->user_id : null,
            'Return Unit Diterima',
            "Return untuk Request #{$req->id} telah diterima dan disahkan.",
            'approved'
        );

        
        $assignedWalkieIds = collect($req->assigned_walkie_inventory_ids ?? [])
            ->map(fn ($walkieId) => (int) $walkieId)
            ->filter()
            ->unique()
            ->values();

        if ($assignedWalkieIds->isEmpty() && $req->walkie_inventory_id) {
            $assignedWalkieIds = collect([(int) $req->walkie_inventory_id]);
        }

        $walkieQuery = WalkieTalkie::query();
        if ($assignedWalkieIds->isNotEmpty()) {
            $walkieQuery->whereIn('walkie_id', $assignedWalkieIds);
        } else {
            $walkieQuery->where('radio_id', $req->radio_id);
        }

        $walkieQuery->get()->each(function (WalkieTalkie $walkie) {
            $walkie->update($this->returnedWalkiePayload($walkie));
        });
        
        // Log Activity
        UserActivityLog::create([
            'user_id' => auth('wt')->id(),
            'username' => auth('wt')->user()->username,
            'event_type' => 'action',
            'event_action' => 'Confirm Return',
            'event_details' => "Confirmed return of Radio {$req->radio_id} from Request #{$req->id}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
        
        return redirect()->route('wt.admin.requests.index')->with('success', 'Return confirmed. Unit is now marked as unused and kept in the inventory.');
    }

    public function forwardDamageToIT($id)
    {
        $record = MaintenanceRecord::findOrFail($id);
        $record->update([
            'status' => 'PENDING ADMIN IT',
            'handled_by' => auth('wt')->id(),
        ]);

        $itUsers = User::where('wt_role', 'admin_it')->get();
        SystemNotifier::notifyUsers(
            $itUsers,
            'Laporan Kerosakan Diteruskan',
            "Laporan kerosakan #{$record->maintenance_id} telah diteruskan ke ICT.",
            'received'
        );

        if ($record->reporter_staff_id) {
            $reporter = User::where('staff_no', $record->reporter_staff_id)->first();
            if ($reporter) {
                SystemNotifier::notifyUser(
                    $reporter,
                    'Laporan Kerosakan Diterima Executive',
                    "Laporan kerosakan #{$record->maintenance_id} anda telah diterima executive dan dihantar ke ICT.",
                    'received'
                );
            }
        }

        UserActivityLog::create([
            'user_id' => auth('wt')->id(),
            'username' => auth('wt')->user()->username,
            'event_type' => 'maintenance',
            'event_action' => 'Forward To IT',
            'event_details' => "Forwarded damage report #{$record->maintenance_id} to ICT",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('wt.admin.requests.index')->with('success', 'Damage report forwarded to ICT.');
    }

    public function approveDamage(Request $request, $id)
    {
        $validated = $request->validate([
            'approval_remark' => 'nullable|string|max:2000',
        ]);
        
        $record = MaintenanceRecord::findOrFail($id);
        $approvalRemark = trim((string) ($validated['approval_remark'] ?? ''));
        $statusNote = $approvalRemark !== '' ? $approvalRemark : $record->remarks;

        $record->update([
            'status' => 'UNDER REPAIR',
            'repair_date' => $record->repair_date ?: now()->toDateString(),
            'remarks' => $statusNote,
            'handled_by' => auth('wt')->id(),
        ]);

        $reportedWalkie = null;
        if ($record->walkie_id) {
            $reportedWalkie = WalkieTalkie::where('walkie_id', $record->walkie_id)->first();
        }
        if (! $reportedWalkie && $record->radio_id) {
            $reportedWalkie = WalkieTalkie::where('radio_id', $record->radio_id)->first();
        }
        if (! $reportedWalkie && $record->serial_number) {
            $reportedWalkie = WalkieTalkie::where('serial_number', $record->serial_number)->first();
        }

        if ($reportedWalkie) {
            $record->update(['walkie_id' => $reportedWalkie->walkie_id]);
            $reportedWalkie->update([
                'status' => 'UNDER REPAIR',
                'remark' => trim((string) $reportedWalkie->remark) !== ''
                    ? $reportedWalkie->remark . ' | Approved faulty report #' . $record->maintenance_id . ' for repair'
                    : 'Approved faulty report #' . $record->maintenance_id . ' for repair',
            ]);
        }

        if ($record->reporter_staff_id) {
            $reporter = User::where('staff_no', $record->reporter_staff_id)->first();
            if ($reporter) {
                $pickupLine = $record->pickup_at
                    ? ' Pickup: ' . \Carbon\Carbon::parse($record->pickup_at)->format('d M Y, h:i A') . ' by ' . ($record->pickup_person ?: $record->reporter_name ?: 'reporter') . '.'
                    : '';
                $userMessage = "Permohonan kerosakan #{$record->maintenance_id} anda telah diluluskan dan direkod dalam simpanan repair ICT. Sila hantar WT rosak ke ICT Department Sejurumus untuk proses pembaikian.{$pickupLine}";
                if ($record->temporary_spare_requested) {
                    $userMessage .= ' Permintaan spare sementara anda telah direkod; ICT akan tentukan berdasarkan stok yang tersedia.';
                } else {
                    $userMessage .= ' Tiada spare sementara diminta dalam laporan ini.';
                }
                if ($approvalRemark !== '') {
                    $userMessage .= " Remark ICT: {$approvalRemark}";
                }
                SystemNotifier::notifyUser($reporter, 'Walkie Talkie Ready To Collect', $userMessage, 'approved');
            }
        }

        UserActivityLog::create([
            'user_id' => auth('wt')->id(),
            'username' => auth('wt')->user()->username,
            'event_type' => 'maintenance',
            'event_action' => 'Approve Damage Report',
            'event_details' => "Approved damage report #{$record->maintenance_id} for repair"
                . ($reportedWalkie ? " and updated inventory unit {$reportedWalkie->radio_id}" : '')
                . ($record->temporary_spare_requested ? '. Temporary spare requested.' : '. No temporary spare requested.'),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('wt.admin.requests.index')->with('success', 'Damage report approved for repair.');
    }

    public function rejectDamage(Request $request, $id)
    {
        $validated = $request->validate([
            'disapproval_remark' => 'required|string|max:2000',
        ]);
        
        $record = MaintenanceRecord::findOrFail($id);
        $disapprovalRemark = trim((string) ($validated['disapproval_remark'] ?? ''));

        $record->update([
            'status' => 'REJECTED',
            'done' => false,
            'finish_date' => now()->toDateString(),
            'remarks' => $disapprovalRemark !== '' ? $disapprovalRemark : $record->remarks,
            'handled_by' => auth('wt')->id(),
        ]);

        if ($record->walkie_id) {
            WalkieTalkie::where('walkie_id', $record->walkie_id)->update(['status' => 'UNUSED']);
        }

        $reporter = null;
        if ($record->reporter_staff_id) {
            $reporter = User::where('staff_no', $record->reporter_staff_id)->first();
        }

        if (! $reporter && $record->reporter_name) {
            $reporter = User::where('staff_no', $record->reporter_name)
                ->orWhere('name', $record->reporter_name)
                ->first();
        }

        if ($reporter) {
            $reporter->notifications()
                ->whereNull('read_at')
                ->where('data', 'like', '%Damage report #' . $record->maintenance_id . ' has been submitted%')
                ->update(['read_at' => now()]);

            $userMessage = "Damage report #{$record->maintenance_id} has been disapproved by ICT.";
            if ($disapprovalRemark !== '') {
                $userMessage .= " Reason: {$disapprovalRemark}";
            }
            SystemNotifier::notifyUser($reporter, 'Damage Report Disapproved', $userMessage, 'rejected');
        }

        UserActivityLog::create([
            'user_id' => auth('wt')->id(),
            'username' => auth('wt')->user()->username,
            'event_type' => 'maintenance',
            'event_action' => 'Disapprove Damage Report',
            'event_details' => "Disapproved damage report #{$record->maintenance_id}. Remark: {$disapprovalRemark}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('wt.admin.requests.index')->with('success', 'Damage report disapproved and returned to user.');
    }



    public function allStatus(Request $request)
    {
        $viewMode = $request->query('view', 'requests'); // requests, handovers, damages
        TemporaryRequestExpiryService::syncExpired();

        $returnHistoryCutoff = $this->returnHistoryCutoff();
        
        $requestStatuses = AccessRequest::query()
            ->with(['user', 'submitToAdmin', 'handler', 'handover'])
            ->where('submit_to_admin_id', auth('wt')->id())
            ->where(function ($query) {
                $this->applyWalkieRequestTypeFilter($query);
            })
            ->where(function ($query) use ($returnHistoryCutoff) {
                $this->applyReturnHistoryRetention($query, $returnHistoryCutoff);
            })
            ->orderByDesc('request_date')
            ->orderByDesc('id')
            ->get();

        $handoverRequests = AccessRequest::with(['handover', 'user'])
            ->where('submit_to_admin_id', auth('wt')->id())
            ->where(function ($query) {
                $this->applyWalkieRequestTypeFilter($query);
            })
            ->where(function ($query) use ($returnHistoryCutoff) {
                $this->applyReturnHistoryRetention($query, $returnHistoryCutoff);
            })
            ->orderByDesc('request_date')
            ->orderByDesc('id')
            ->get();

        $damageRecords = MaintenanceRecord::where('submit_to_admin_id', auth('wt')->id())
            ->orderByDesc('maintenance_id')
            ->get();

        $requestSummaryBucket = function (AccessRequest $request): string {
            if (($request->return_status ?? null) === 'Returned') {
                return 'history';
            }

            if (in_array($request->return_status, ['Pending Admin Approval', 'Pending IT Approval'], true)) {
                return 'processing';
            }

            if ($request->status === 'Rejected') {
                return 'rejected';
            }

            if ($request->status === 'Draft') {
                return 'draft';
            }

            if (in_array($request->status, ['Pending Admin Approval', 'Pending IT Approval'], true)) {
                return 'processing';
            }

            if ($request->status === 'Pending Executive Pickup' || ($request->status === 'Approved' && ! $request->handover)) {
                return 'ready';
            }

            if ($request->status === 'Approved') {
                return 'approved';
            }

            return 'processing';
        };

        $damageSummaryBucket = function (MaintenanceRecord $record): string {
            $status = strtoupper((string) $record->status);

            if (in_array($status, ['REJECTED', 'REFUSED'], true)) {
                return 'rejected';
            }

            if ((bool) $record->done || $status === 'DONE') {
                return 'history';
            }

            if ($status === 'DRAFT') {
                return 'draft';
            }

            if (in_array($status, ['READY TO COLLECT', 'ALREADY FIXED'], true)) {
                return 'ready';
            }

            if (in_array($status, ['UNDER REPAIR', 'REPAIRING', 'FAULTY', 'B.E.R'], true)) {
                return 'approved';
            }

            return 'processing';
        };

        $summaryKeys = ['draft', 'processing', 'ready', 'approved', 'history', 'rejected'];
        $statusSummary = array_fill_keys($summaryKeys, 0);

        $requestStatuses->each(function (AccessRequest $request) use (&$statusSummary, $requestSummaryBucket) {
            $statusSummary[$requestSummaryBucket($request)]++;
        });

        $damageRecords->each(function (MaintenanceRecord $record) use (&$statusSummary, $damageSummaryBucket) {
            $statusSummary[$damageSummaryBucket($record)]++;
        });

        return view('wt.admin.all_status', compact('requestStatuses', 'handoverRequests', 'damageRecords', 'viewMode', 'statusSummary'));
    }

    public function handoverStatus()
    {
        $handoverRequests = AccessRequest::with(['handover', 'user'])
            ->where('submit_to_admin_id', auth('wt')->id())
            ->where(function ($query) {
                $this->applyWalkieRequestTypeFilter($query);
            })
            ->orderByDesc('request_date')
            ->orderByDesc('id')
            ->get();

        $statusSummary = [
            'processing' => $handoverRequests->filter(function ($request) {
                return in_array($request->status, ['Pending Admin Approval', 'Pending IT Approval'], true);
            })->count(),
            'ready_to_collect' => $handoverRequests->filter(function ($request) {
                return $request->status === 'Approved'
                    && ! $request->handover;
            })->count(),
            'completed' => $handoverRequests->filter(function ($request) {
                return (bool) $request->handover;
            })->count(),
        ];

        return view('wt.admin.handover_status', compact('handoverRequests', 'statusSummary'));
    }

    public function requestStatus()
    {
        return redirect()
            ->route('wt.admin.all.status')
            ->with('info', 'Request Status has been moved to All Status.');
    }

    private function returnHistoryRetentionYears(): int
    {
        $years = (int) env('WT_RETURN_HISTORY_YEARS', 5);

        return max(1, min(5, $years));
    }

    private function returnHistoryCutoff(): \Carbon\Carbon
    {
        return now()->subYears($this->returnHistoryRetentionYears())->startOfDay();
    }

    private function applyReturnHistoryRetention($query, \Carbon\Carbon $cutoff): void
    {
        $query->whereNull('return_status')
            ->orWhere('return_status', '!=', 'Returned')
            ->orWhere(function ($returnedQuery) use ($cutoff) {
                $returnedQuery->where('return_status', 'Returned')
                    ->where(function ($dateQuery) use ($cutoff) {
                        $dateQuery->where('return_date', '>=', $cutoff->toDateString())
                            ->orWhere(function ($missingReturnDateQuery) use ($cutoff) {
                                $missingReturnDateQuery->whereNull('return_date')
                                    ->where('created_at', '>=', $cutoff);
                            });
                    });
            });
    }
}
