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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
        $isIctView = request()->routeIs('wt.admin.*')
            && auth('wt')->user()->wt_role === 'admin_it'
            && session('view_mode', auth('wt')->user()->wt_role) === 'admin_it';

        if (request()->routeIs('wt.admin.*') && ! $isIctView && auth('wt')->user()->wt_role === 'admin_it') {
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
                ->whereIn('wt_role', $directHandoverScope === 'on_behalf' ? ['admin'] : ['admin', 'admin_it'])
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
            ->whereIn('status', ['Pending Executive Pickup', 'Approved'])
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
            ->whereIn('status', ['Pending Admin Approval', 'Pending IT Approval', 'Pending Executive Pickup', 'Approved', 'Rejected'])
            ->orderByDesc('id')
            ->get();

        return view('wt.user.handover.index', compact('pendingHandovers', 'myHandovers', 'statusRequests'));
    }

    public function store(Request $request)
    {
        $isIctView = $request->routeIs('wt.admin.*')
            && auth('wt')->user()->wt_role === 'admin_it'
            && session('view_mode', auth('wt')->user()->wt_role) === 'admin_it';

        if ($request->routeIs('wt.admin.*') && ! $isIctView && $request->input('handover_mode') === 'ict_direct') {
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
            ->whereIn('status', ['Pending Executive Pickup', 'Approved'])
            ->when($request->routeIs('wt.admin.*'), function ($query) {
                $query->where(function ($inner) {
                    $inner->where('user_id', auth('wt')->id())
                        ->orWhere('submit_to_admin_id', auth('wt')->id());
                });
            }, function ($query) {
                $query->where('user_id', auth('wt')->id());
            })
            ->firstOrFail();

        if (Handover::where('access_request_id', $accessRequest->id)->exists()) {
            return redirect()->route($request->routeIs('wt.admin.*') ? 'wt.admin.all.status' : 'wt.login')
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

                $itUsers = User::where('wt_role', 'admin_it')->get();
                SystemNotifier::notifyUsers(
                    $itUsers,
                    'Recipient Pickup Pending',
                    "The recipient selected Not Yet for request #{$accessRequest->id}. Expected collection: {$collectionNote}.",
                    'received'
                );
            });

            return redirect()
                ->route($request->routeIs('wt.admin.*') ? 'wt.admin.all.status' : 'wt.user.handover.index')
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

            $itUsers = User::where('wt_role', 'admin_it')->get();
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

        return redirect()->route($request->routeIs('wt.admin.*') ? 'wt.admin.all.status' : 'wt.user.handover.index')->with('success', $message);
    }

    public function showPickup(AccessRequest $accessRequest)
    {
        $accessRequest = $this->authorizedPickupRequest($accessRequest)
            ->load(['handover', 'handler', 'submitToAdmin']);

        if ($accessRequest->handover) {
            return redirect()
                ->route(request()->routeIs('wt.admin.*') ? 'wt.admin.all.status' : 'wt.user.handover.index')
                ->with('error', 'Pickup for this request has already been signed.');
        }

        $assignedWalkies = $this->assignedWalkiesFor($accessRequest);
        $currentUser = auth('wt')->user();
        $savedSignatureUrl = $currentUser->signature_img && Storage::disk('public')->exists($currentUser->signature_img)
            ? route(request()->routeIs('wt.admin.*') ? 'wt.admin.profile.signature.image' : 'wt.user.profile.signature.image')
            : null;

        return view('wt.user.handover.pickup', [
            'accessRequest' => $accessRequest,
            'assignedWalkies' => $assignedWalkies,
            'savedSignatureUrl' => $savedSignatureUrl,
            'policyContent' => $this->loadPolicyContent(),
            'routePrefix' => request()->routeIs('wt.admin.*') ? 'wt.admin' : 'wt.user',
        ]);
    }

    public function storePickup(Request $request, AccessRequest $accessRequest)
    {
        $accessRequest = $this->authorizedPickupRequest($accessRequest)
            ->load(['handover', 'handler', 'submitToAdmin']);

        if ($accessRequest->handover) {
            return redirect()
                ->route($request->routeIs('wt.admin.*') ? 'wt.admin.all.status' : 'wt.user.handover.index')
                ->with('error', 'Pickup for this request has already been signed.');
        }

        $validated = $request->validate([
            'pickup_recipient_name' => 'required|string|max:255',
            'pickup_recipient_signature_source' => 'required|in:draw,saved,upload',
            'pickup_recipient_signature' => 'required_unless:pickup_recipient_signature_source,saved|nullable|string',
            'handover_by_name' => 'required|string|max:255',
            'handover_by_signature_source' => 'required|in:draw,saved,upload',
            'handover_by_signature' => 'required_unless:handover_by_signature_source,saved|nullable|string',
            'checked_by_name' => 'required|string|max:255',
            'checked_by_signature_source' => 'required|in:draw,saved,upload',
            'checked_by_signature' => 'required_unless:checked_by_signature_source,saved|nullable|string',
            'policy_acceptance' => 'accepted',
        ]);

        $recipientSignature = $this->resolveSubmittedSignature(
            $validated['pickup_recipient_signature_source'],
            $validated['pickup_recipient_signature'] ?? null
        );
        $handoverSignature = $this->resolveSubmittedSignature(
            $validated['handover_by_signature_source'],
            $validated['handover_by_signature'] ?? null
        );
        $checkedBySignature = $this->resolveSubmittedSignature(
            $validated['checked_by_signature_source'],
            $validated['checked_by_signature'] ?? null
        );

        $assignedWalkies = $this->assignedWalkiesFor($accessRequest);
        if ($assignedWalkies->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['access_request_id' => 'No assigned WT unit was found for this approved request.']);
        }

        DB::transaction(function () use ($accessRequest, $assignedWalkies, $validated, $recipientSignature, $handoverSignature, $checkedBySignature) {
            $now = now();
            $radioIds = $assignedWalkies->pluck('radio_id')->filter()->values();
            $serialNumbers = $assignedWalkies->pluck('serial_number')->filter()->values();

            Handover::create([
                'access_request_id' => $accessRequest->id,
                'user_id' => auth('wt')->id(),
                'radio_id' => $radioIds->implode(', '),
                'walkie_talkie_id' => $assignedWalkies->first()?->walkie_id,
                'staff_name' => $accessRequest->full_name,
                'shared_with' => $accessRequest->shared_with,
                'staff_no' => $accessRequest->staff_id ?? '',
                'position' => $accessRequest->position,
                'department' => $accessRequest->department,
                'notes' => 'Pickup signed by recipient. Accessories: ' . ($accessRequest->accessories ?: 'None listed'),
                'pickup_recipient_name' => $validated['pickup_recipient_name'],
                'pickup_recipient_signature' => $recipientSignature,
                'pickup_recipient_signed_at' => $now,
                'handover_by_name' => $validated['handover_by_name'],
                'handover_by_signature' => $handoverSignature,
                'handover_by_signed_at' => $now,
                'checked_by_name' => $validated['checked_by_name'],
                'checked_by_signature' => $checkedBySignature,
                'checked_by_signed_at' => $now,
                'accessories_snapshot' => $accessRequest->accessories,
                'policy_accepted_at' => $now,
                'pickup_completed_at' => $now,
                'issued_at' => $now,
            ]);

            $accessRequest->update([
                'status' => 'Approved',
            ]);

            $assignedWalkies->each(function (WalkieTalkie $walkie) use ($accessRequest) {
                $walkie->update([
                    'status' => 'IN USE',
                    'ownership_type' => $accessRequest->ownership_type ?: 'INDIVIDUAL',
                    'shared_with' => $accessRequest->shared_with,
                    'ownership' => $accessRequest->full_name,
                    'position' => $accessRequest->position,
                    'department' => $accessRequest->department,
                    'location' => $accessRequest->location,
                    'executive' => $accessRequest->submitToAdmin ? $accessRequest->submitToAdmin->full_name : null,
                    'remark' => trim((string) $walkie->remark) !== ''
                        ? $walkie->remark . ' | Pickup signed for request #' . $accessRequest->id
                        : 'Pickup signed for request #' . $accessRequest->id,
                ]);
            });

            UserActivityLog::create([
                'user_id' => auth('wt')->id(),
                'username' => auth('wt')->user()->username,
                'event_type' => 'action',
                'event_action' => 'Pickup Signature',
                'event_details' => "Pickup signed for Request #{$accessRequest->id}. Radio {$radioIds->implode(', ')} / Serial {$serialNumbers->implode(', ')}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => $now,
            ]);

            $itUsers = User::where('wt_role', 'admin_it')->get();
            SystemNotifier::notifyUsers(
                $itUsers,
                'Pickup Signed',
                "Pickup for Request #{$accessRequest->id} has been signed by {$validated['pickup_recipient_name']}.",
                'approved'
            );

            if ($accessRequest->submitToAdmin && (int) $accessRequest->submit_to_admin_id !== (int) auth('wt')->id()) {
                SystemNotifier::notifyUser(
                    $accessRequest->submitToAdmin,
                    'Pickup Signed',
                    "Pickup for Request #{$accessRequest->id} has been signed by {$validated['pickup_recipient_name']}.",
                    'approved'
                );
            }
        });

        return redirect()
            ->route($request->routeIs('wt.admin.*') ? 'wt.admin.all.status' : 'wt.user.handover.index')
            ->with('success', 'Pickup signature saved. WT handover has been completed.');
    }

    private function authorizedPickupRequest(AccessRequest $accessRequest): AccessRequest
    {
        $query = AccessRequest::query()
            ->whereKey($accessRequest->id)
            ->whereIn('status', ['Pending Executive Pickup', 'Approved']);

        if (request()->routeIs('wt.admin.*')) {
            $query->where(function ($inner) {
                $inner->where('user_id', auth('wt')->id())
                    ->orWhere('submit_to_admin_id', auth('wt')->id())
                    ->orWhere(function ($adminIt) {
                        $adminIt->whereNotNull('handled_by')
                            ->where('handled_by', auth('wt')->id());
                    });
            });
        } else {
            $query->where('user_id', auth('wt')->id());
        }

        return $query->firstOrFail();
    }

    private function assignedWalkiesFor(AccessRequest $accessRequest)
    {
        $assignedWalkieIds = collect($accessRequest->assigned_walkie_inventory_ids ?? [])
            ->map(fn ($walkieId) => (int) $walkieId)
            ->filter()
            ->unique()
            ->values();

        if ($assignedWalkieIds->isEmpty() && $accessRequest->walkie_inventory_id) {
            $assignedWalkieIds = collect([(int) $accessRequest->walkie_inventory_id]);
        }

        if ($assignedWalkieIds->isEmpty()) {
            return collect();
        }

        return WalkieTalkie::query()
            ->whereIn('walkie_id', $assignedWalkieIds)
            ->orderBy('radio_id')
            ->get();
    }

    private function resolveSubmittedSignature(string $source, ?string $submittedSignature): string
    {
        if ($source === 'saved') {
            $user = auth('wt')->user();
            if (! $user->signature_img || ! Storage::disk('public')->exists($user->signature_img)) {
                throw ValidationException::withMessages([
                    'pickup_recipient_signature' => 'No saved profile signature was found. Please draw or upload a signature.',
                ]);
            }

            $path = $user->signature_img;
            $mime = Storage::disk('public')->mimeType($path) ?: 'image/png';
            $data = base64_encode(Storage::disk('public')->get($path));

            return "data:{$mime};base64,{$data}";
        }

        $signature = trim((string) $submittedSignature);
        if (! preg_match('/^data:image\/(png|jpeg|jpg|gif|webp);base64,/', $signature)) {
            throw ValidationException::withMessages([
                'pickup_recipient_signature' => 'Invalid signature image. Please draw or upload a valid signature.',
            ]);
        }

        $imageData = base64_decode(substr($signature, strpos($signature, ',') + 1), true);
        if ($imageData === false || strlen($imageData) > 2 * 1024 * 1024) {
            throw ValidationException::withMessages([
                'pickup_recipient_signature' => 'Signature image is invalid or larger than 2MB.',
            ]);
        }

        return $signature;
    }
    private function loadPolicyContent(): array
    {
        $default = [
            'en' => [
                'The following are the terms and conditions that must be adhered to:-',
                'a. Officers must be responsible to ensure that each walkie-talkie and additional equipment (accessories) provided are used carefully and maintained as well as possible to prevent any damage.',
                'b. If it is found that damage or loss occurs due to :-<br><span style="padding-left:18px; display:inline-block;">&bull; Willful negligence</span><br><span style="padding-left:18px; display:inline-block;">&bull; Misuse of the walkie-talkie</span><br><span style="padding-left:18px; display:inline-block;">&bull; Intentional loss</span><br><span style="padding-left:18px; display:inline-block;">&bull; Intentional damage</span>',
                'c. The staff member concerned will be held responsible to bear the repair and replacement cost of a new walkie-talkie if necessary.',
                'd. However, repair costs for damage caused by "manufacturing defect" and usage exceeding the lifespan will be borne by the company.',
                'Thank you, please be informed.'
            ],
            'bm' => [
                'Berikut adalah syarat-syarat yang perlu dipatuhi:-',
                'a. Petugas perlu bertanggungjawab untuk memastikan setiap walkie-talkie dan kelengkapan tambahan (aksesori) yang dibekalkan digunakan dengan cermat dan dijaga sebaik mungkin bagi mengelakkan berlakunya sebarang kerosakan.',
                'b. Jika didapati berlaku kerosakan atau kehilangan yang disebabkan :-<br><span style="padding-left:18px; display:inline-block;">&bull; Kecuaian yang disengajakan</span><br><span style="padding-left:18px; display:inline-block;">&bull; Penyalahgunaan walkie Talkie</span><br><span style="padding-left:18px; display:inline-block;">&bull; Kehilangan yang disengajakan</span><br><span style="padding-left:18px; display:inline-block;">&bull; Kerosakan yang disengajakan</span>',
                'c. Petugas yang berkenaan akan dipertanggungjawabkan untuk menanggung kos baik pulih dan penggantian walkie-talkie yang baru sekiranya perlu.',
                'd. Bagaimanapun, kos baik pulih terhadap kerosakan yang disebabkan oleh "manufacturing defeat" dan penggunaan yang melebihi jangka hayat akan ditanggung oleh pihak syarikat.',
                'Sekian, harap maklum.'
            ]
        ];

        $path = storage_path('app/wt/pickup_policies.json');
        if (! is_file($path)) {
            return $default;
        }

        $stored = json_decode((string) file_get_contents($path), true);

        return [
            'en' => is_array($stored['en'] ?? null) ? array_values(array_filter($stored['en'])) : $default['en'],
            'bm' => is_array($stored['bm'] ?? null) ? array_values(array_filter($stored['bm'])) : $default['bm'],
        ];
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
                ->whereIn('status', ['Pending Executive Pickup', 'Approved'])
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
            ->when(! $accessRequest, fn ($query) => $query->whereIn('wt_role', ['admin', 'admin_it']))
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
