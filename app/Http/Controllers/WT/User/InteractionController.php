<?php

namespace App\Http\Controllers\WT\User;

use App\Http\Controllers\WT\Controller;
use Illuminate\Http\Request;
use App\Models\WT\AccessRequest;
use App\Models\WT\Handover;
use App\Models\WT\User;
use App\Models\WT\MaintenanceRecord;
use App\Models\WT\WalkieTalkie;
use App\Services\SystemNotifier;
use App\Services\TemporaryRequestExpiryService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InteractionController extends Controller
{
    private function routePrefix(Request $request): string
    {
        return $request->routeIs('wt.admin.*') ? 'wt.admin' : 'wt.user';
    }

    private function managerMode(Request $request): string
    {
        if (! $request->routeIs('wt.admin.*')) {
            return 'self';
        }

        return $request->query('mode') === 'staff' ? 'staff' : 'self';
    }

    private function managedUsers()
    {
        $users = User::query()
            ->where('wt_role', 'user')
            ->orderByRaw("CASE WHEN name IS NULL OR name = '' THEN 1 ELSE 0 END")
            ->orderBy('name')
            ->orderBy('staff_no')
            ->get();

        $latestDamageRecords = MaintenanceRecord::query()
            ->whereIn('reporter_name', $users->pluck('full_name')->filter()->map(fn ($value) => strtoupper((string) $value))->unique()->values())
            ->orderByDesc('maintenance_id')
            ->get()
            ->groupBy(fn (MaintenanceRecord $record) => strtoupper((string) $record->reporter_name))
            ->map(fn (Collection $records) => $records->first());

        return $users->map(function (User $user) use ($latestDamageRecords) {
            $lookupKey = strtoupper((string) ($user->full_name ?: $user->username));
            $latestRecord = $latestDamageRecords->get($lookupKey);

            $user->last_ownership_type = strtoupper((string) ($latestRecord->ownership_type ?? ''));
            $user->last_shared_with = strtoupper((string) ($latestRecord->shared_with ?? ''));
            $user->last_sector = strtoupper((string) ($latestRecord->sector ?? ''));
            $user->last_location = strtoupper((string) ($latestRecord->location ?? ''));

            return $user;
        });
    }

    private function findOrCreateManagedUserForDamageReport(array $validated): ?User
    {
        $reporterName = strtoupper(trim((string) ($validated['reporter_name'] ?? '')));
        $department = strtoupper(trim((string) ($validated['department'] ?? auth('wt')->user()->department ?? 'GENERAL')));
        $position = strtoupper(trim((string) ($validated['designation'] ?? 'STAFF')));
        $phoneNo = trim((string) ($validated['phone_no'] ?? ''));
        $targetUserId = (int) ($validated['user_id'] ?? 0);

        if ($targetUserId > 0) {
            $existingById = User::query()
                ->where('id', $targetUserId)
                ->where('wt_role', 'user')
                ->first();

            if ($existingById) {
                $existingById->update([
                    'full_name' => $reporterName !== '' ? $reporterName : ($existingById->full_name ?: $existingById->username),
                    'department' => $department !== '' ? $department : $existingById->department,
                    'position' => $position !== '' ? $position : $existingById->position,
                    'phone_no' => $phoneNo !== '' ? $phoneNo : $existingById->phone_no,
                    'role' => 'user',
                ]);

                return $existingById;
            }
        }

        if ($reporterName !== '') {
            $existingByName = User::query()
                ->where(function ($query) use ($reporterName) {
                    $query->where('name', $reporterName)
                        ->orWhere('staff_no', $reporterName);
                })
                ->when($department !== '', function ($query) use ($department) {
                    $query->where('dept_name', $department);
                })
                ->first();

            if ($existingByName) {
                $existingByName->update([
                    'full_name' => $reporterName,
                    'department' => $department !== '' ? $department : $existingByName->department,
                    'position' => $position !== '' ? $position : $existingByName->position,
                    'phone_no' => $phoneNo !== '' ? $phoneNo : $existingByName->phone_no,
                    'role' => 'user',
                ]);

                return $existingByName;
            }
        }

        $usernameBase = Str::of($reporterName !== '' ? $reporterName : $phoneNo)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '.')
            ->trim('.')
            ->value();

        if ($usernameBase === '') {
            $usernameBase = 'staff.user';
        }

        $username = $usernameBase;
        $suffix = 1;
        while (User::where('staff_no', $username)->exists()) {
            $username = $usernameBase . '.' . $suffix;
            $suffix++;
        }

        $generatedContactId = strtoupper('CONTACT-' . Str::random(8));
        while (User::where('staff_no', $generatedContactId)->exists()) {
            $generatedContactId = strtoupper('CONTACT-' . Str::random(8));
        }

        return User::create([
            'staff_id' => $generatedContactId,
            'username' => strtoupper($username),
            'full_name' => $reporterName !== '' ? $reporterName : strtoupper($username),
            'department' => $department !== '' ? $department : 'GENERAL',
            'position' => $position !== '' ? $position : 'STAFF',
            'phone_no' => $phoneNo !== '' ? $phoneNo : null,
            'password' => Str::random(24),
            'role' => 'user',
            'created_at' => now(),
        ]);
    }

    private function damageRecordsForContext(Request $request, string $mode): Collection
    {
        $isAdminRoute = $request->routeIs('wt.admin.*');
        $authUser = auth('wt')->user();

        if (! $isAdminRoute) {
            return MaintenanceRecord::query()
                ->where(function ($query) use ($authUser) {
                    $query->where('reporter_name', $authUser->username)
                        ->orWhere('reporter_name', $authUser->full_name)
                        ->orWhere('reporter_staff_id', $authUser->staff_id);
                })
                ->orderByDesc('maintenance_id')
                ->get();
        }

        $records = MaintenanceRecord::query()
            ->where('submit_to_admin_id', $authUser->id)
            ->orderByDesc('maintenance_id')
            ->get();

        $isSelfRecord = function (MaintenanceRecord $record) use ($authUser) {
            return $record->reporter_staff_id === $authUser->staff_id
                || $record->reporter_name === $authUser->username
                || $record->reporter_name === $authUser->full_name;
        };

        return $records->filter(function (MaintenanceRecord $record) use ($mode, $isSelfRecord) {
            if ($mode === 'staff') {
                return ! $isSelfRecord($record);
            }

            return $isSelfRecord($record);
        })->values();
    }

    private function damageStatusBuckets(Collection $records): array
    {
        $drafts = $records->filter(fn (MaintenanceRecord $record) => strtoupper((string) $record->status) === 'DRAFT')->values();
        $completed = $records->filter(function (MaintenanceRecord $record) {
            $status = strtoupper((string) $record->status);

            return (bool) $record->done || in_array($status, ['DONE', 'REJECTED', 'REFUSED'], true);
        })->values();
        $pending = $records->reject(function (MaintenanceRecord $record) {
            $status = strtoupper((string) $record->status);

            return $status === 'DRAFT'
                || (bool) $record->done
                || in_array($status, ['DONE', 'REJECTED', 'REFUSED'], true);
        })->values();

        return [
            'pending' => $pending,
            'drafts' => $drafts,
            'completed' => $completed,
        ];
    }

    private function findReportedWalkie(array $validated): ?WalkieTalkie
    {
        $radioId = trim((string) ($validated['radio_id'] ?? ''));
        $serialNumber = trim((string) ($validated['serial_number'] ?? ''));
        $model = trim((string) ($validated['model'] ?? ''));

        if ($radioId === '' && $serialNumber === '' && $model === '') {
            return null;
        }

        $query = WalkieTalkie::query()
            ->where(function ($query) use ($validated) {
                if (! blank($validated['radio_id'] ?? null)) {
                    $query->orWhere('radio_id', trim((string) $validated['radio_id']));
                }

                if (! blank($validated['serial_number'] ?? null)) {
                    $query->orWhere('serial_number', trim((string) $validated['serial_number']));
                }

                if (! blank($validated['model'] ?? null)) {
                    $query->orWhere('model', trim((string) $validated['model']));
                }
            })
            ->limit(2)
            ->get();

        return $query->count() === 1 ? $query->first() : null;
    }

    private function safeUploadName(?string $value, string $fallback): string
    {
        $name = strtoupper(trim((string) $value));
        $name = preg_replace('/[^A-Z0-9_-]+/', '_', $name) ?: $fallback;

        return trim($name, '_') ?: $fallback;
    }

    private function storeDamageUploads(Request $request, string $field, string $folder, string $baseName): array
    {
        if (! $request->hasFile($field)) {
            return [];
        }

        $paths = [];
        $files = $request->file($field);
        $files = is_array($files) ? $files : [$files];

        foreach ($files as $index => $file) {
            $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'file');
            $suffix = count($files) > 1 ? '_' . ($index + 1) : '';
            $paths[] = $file->storeAs($folder, $baseName . $suffix . '.' . $extension, 'public');
        }

        return $paths;
    }

    private function activeReturnRequestQuery()
    {
        return AccessRequest::with(['user', 'walkieTalkie'])
            ->where('status', 'Approved')
            ->where(function ($q) {
                $q->whereNull('return_status')
                    ->orWhereNotIn('return_status', ['Pending Admin Approval', 'Pending IT Approval', 'Returned']);
            })
            ->where(function ($q) {
                $q->whereNotNull('radio_id')
                    ->orWhereNotNull('walkie_inventory_id')
                    ->orWhereNotNull('assigned_radio_ids')
                    ->orWhereNotNull('assigned_walkie_inventory_ids');
            });
    }

    private function applyReturnOwnershipScope($query, Request $request, string $mode)
    {
        $user = auth('wt')->user();
        $isAdminRoute = $request->routeIs('wt.admin.*');

        if ($isAdminRoute && $mode === 'staff') {
            $managedIds = $this->managedUsers()->pluck('user_id')->filter()->values();

            return $query->where(function ($scope) use ($managedIds, $user) {
                $scope->whereIn('user_id', $managedIds)
                    ->orWhere(function ($legacy) use ($user) {
                        $legacy->where('submit_to_admin_id', $user->user_id)
                            ->whereNull('user_id');
                    });
            });
        }

        if ($isAdminRoute) {
            return $query->where(function ($scope) use ($user) {
                $scope->where('user_id', $user->user_id)
                    ->orWhere('submit_to_admin_id', $user->user_id);
            });
        }

        $userNames = collect([$user->full_name, $user->username])
            ->filter()
            ->map(fn ($value) => strtoupper(trim((string) $value)))
            ->unique()
            ->values();

        return $query->where(function ($scope) use ($user, $userNames) {
            $scope->where('user_id', $user->user_id);

            if (! blank($user->staff_id)) {
                $scope->orWhere('staff_id', $user->staff_id);
            }

            if ($userNames->isNotEmpty()) {
                $scope->orWhereIn(\Illuminate\Support\Facades\DB::raw('UPPER(full_name)'), $userNames->all());
            }
        });
    }

    private function returnLookupQuery(Request $request, string $mode)
    {
        $query = $this->activeReturnRequestQuery();

        if ($request->routeIs('wt.admin.*')) {
            return $query;
        }

        return $this->applyReturnOwnershipScope($query, $request, $mode);
    }

    // --- REQUEST ACCESS ---
    public function createRequest()
    {
        $admins = User::where('wt_role', 'admin')->orderBy('staff_no')->get();
        return view('wt.user.requests.create', compact('admins'));
    }

    public function storeRequest(Request $request)
    {
        $validated = $request->validate([
            'submit_to_admin_id' => ['required', 'integer', Rule::exists(User::class, 'id')],
            'requestor_name' => 'required|string|max:255',
            'requestor_staff_id' => 'required|string|max:255',
            'request_date' => 'required|date',
            'requestor_dept' => 'required|array|min:1',
            'requestor_dept.*' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'ownership_type' => 'required|in:unallocated,shared,individual,spare',
            'shared_with' => 'nullable|string|max:255|required_if:ownership_type,shared',
            'bay_from' => 'nullable|string|max:255',
            'sector' => 'nullable|string|max:255',
            'location' => 'required|string|max:255',
            'event_name' => 'required|string|max:255',
            'justification' => 'required|string|max:2000',
            'request_signature' => ['required', 'string', 'regex:/^data:image\/png;base64,/'],
        ]);

        $selectedAdmin = User::where('id', $validated['submit_to_admin_id'])->first();
        if (! $selectedAdmin || $selectedAdmin->wt_role !== 'admin') {
            return back()
                ->withInput()
                ->withErrors(['submit_to_admin_id' => 'Selected approver must be an Executive account.']);
        }

        $createdRequest = AccessRequest::create([
            'user_id' => auth('wt')->id(),
            'request_type' => 'walkie_talkie',
            'full_name' => $validated['requestor_name'],
            'staff_id' => $validated['requestor_staff_id'],
            'request_date' => $validated['request_date'],
            'department' => implode(', ', $validated['requestor_dept']),
            'position' => $validated['position'] ?? null,
            'ownership_type' => $validated['ownership_type'],
            'shared_with' => $validated['ownership_type'] === 'shared' ? ($validated['shared_with'] ?? null) : null,
            'bay_from' => $validated['ownership_type'] === 'shared' ? ($validated['bay_from'] ?? null) : null,
            'sector' => $validated['sector'] ?? null,
            'location' => $validated['location'],
            'event_name' => $validated['event_name'],
            'justifications' => $validated['justification'],
            'request_signature' => $validated['request_signature'],
            'status' => 'Pending Admin Approval',
            'submit_to_admin_id' => $validated['submit_to_admin_id'],
        ]);

        SystemNotifier::notifyUser(
            $selectedAdmin,
            'Permohonan Baru Diterima',
            "Permohonan Walkie Talkie #{$createdRequest->id} menunggu semakan anda.",
            'request_submitted'
        );

        SystemNotifier::notifyUser(
            auth('wt')->user(),
            'Permohonan Berjaya Dihantar',
            "Permohonan Walkie Talkie #{$createdRequest->id} telah dihantar kepada executive untuk semakan.",
            'request_sent'
        );

        $adminName = $selectedAdmin->full_name ?: $selectedAdmin->username;
        $adminPos = $selectedAdmin->position ?: 'Executive';
        $adminDept = $selectedAdmin->department ?: 'General';

        $successMsg = "Permohonan anda telah dihantar kepada {$adminName}, {$adminPos} dari {$adminDept} untuk pengesahan.";

        return redirect()->route('wt.admin.requests.index')->with('success', $successMsg);
    }

    // --- RETURN UNIT ---
    public function createReturn()
    {
        $mode = $this->managerMode(request());
        $isAdminRoute = request()->routeIs('wt.admin.*');

        $activeAssets = $this->applyReturnOwnershipScope(
            $this->activeReturnRequestQuery(),
            request(),
            $mode
        )
            ->orderByDesc('request_date')
            ->orderByDesc('id')
            ->get();
        $returnPeople = $this->returnPeopleOptions($activeAssets);

        return view('wt.user.returns.create', compact('activeAssets', 'mode', 'returnPeople'));
    }

    public function searchReturn(Request $request)
    {
        $validated = $request->validate([
            'q' => 'required|string|max:120',
        ]);

        $mode = $this->managerMode($request);
        $search = strtoupper(trim($validated['q']));

        $assets = $this->returnLookupQuery($request, $mode)
            ->where(function ($query) use ($search) {
                $like = '%' . $search . '%';

                $query->whereRaw("UPPER(COALESCE(full_name, '')) LIKE ?", [$like])
                    ->orWhereRaw("UPPER(COALESCE(staff_id, '')) LIKE ?", [$like])
                    ->orWhereRaw("UPPER(COALESCE(department, '')) LIKE ?", [$like])
                    ->orWhereRaw("UPPER(COALESCE(position, '')) LIKE ?", [$like])
                    ->orWhereRaw("UPPER(COALESCE(radio_id, '')) LIKE ?", [$like])
                    ->orWhereRaw("UPPER(COALESCE(CAST(assigned_radio_ids AS CHAR), '')) LIKE ?", [$like])
                    ->orWhereRaw("UPPER(COALESCE(assigned_serial_number, '')) LIKE ?", [$like])
                    ->orWhereRaw("UPPER(COALESCE(CAST(assigned_serial_numbers AS CHAR), '')) LIKE ?", [$like])
                    ->orWhereHas('walkieTalkie', function ($walkie) use ($like) {
                        $walkie->whereRaw("UPPER(COALESCE(radio_id, '')) LIKE ?", [$like])
                            ->orWhereRaw("UPPER(COALESCE(serial_number, '')) LIKE ?", [$like])
                            ->orWhereRaw("UPPER(COALESCE(model, '')) LIKE ?", [$like]);
                    });
            })
            ->orderByDesc('request_date')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $results = $assets
            ->flatMap(function (AccessRequest $asset) use ($search) {
                $walkieIds = collect($asset->assigned_walkie_inventory_ids ?? [])->filter()->values();
                if ($walkieIds->isEmpty() && $asset->walkie_inventory_id) {
                    $walkieIds = collect([$asset->walkie_inventory_id]);
                }

                $radioIds = collect($asset->assigned_radio_ids ?? [])->filter()->values();
                if ($radioIds->isEmpty() && $asset->radio_id) {
                    $radioIds = collect(explode(',', (string) $asset->radio_id))
                        ->map(fn ($id) => trim($id))
                        ->filter()
                        ->values();
                }

                $serials = collect($asset->assigned_serial_numbers ?? [])->filter()->values();
                if ($serials->isEmpty() && $asset->assigned_serial_number) {
                    $serials = collect(explode(',', (string) $asset->assigned_serial_number))
                        ->map(fn ($serial) => trim($serial))
                        ->filter()
                        ->values();
                }

                if ($radioIds->isEmpty() && $asset->walkieTalkie) {
                    $radioIds = collect([$asset->walkieTalkie->radio_id])->filter()->values();
                }

                if ($serials->isEmpty() && $asset->walkieTalkie) {
                    $serials = collect([$asset->walkieTalkie->serial_number])->filter()->values();
                }

                $unitCount = max($radioIds->count(), $walkieIds->count(), 1);

                return collect(range(0, $unitCount - 1))->map(function ($index) use ($asset, $walkieIds, $radioIds, $serials, $search) {
                    $radioId = (string) ($radioIds->get($index) ?: $asset->radio_id ?: optional($asset->walkieTalkie)->radio_id ?: '');
                    $serialNumber = (string) ($serials->get($index) ?: $asset->assigned_serial_number ?: optional($asset->walkieTalkie)->serial_number ?: '');
                    $haystack = strtoupper(implode(' ', [
                        $asset->id,
                        $asset->full_name,
                        $asset->staff_id,
                        $asset->department,
                        $asset->position,
                        $radioId,
                        $serialNumber,
                        optional($asset->walkieTalkie)->model,
                    ]));

                    if (! str_contains($haystack, $search)) {
                        return null;
                    }

                    return [
                        'id' => $asset->id,
                        'walkie_inventory_id' => $walkieIds->get($index) ?: $asset->walkie_inventory_id,
                        'radio_id' => $radioId,
                        'serial_number' => $serialNumber,
                        'full_name' => strtoupper((string) ($asset->full_name ?: optional($asset->user)->username ?: '-')),
                        'staff_id' => strtoupper((string) ($asset->staff_id ?: optional($asset->user)->staff_id ?: '-')),
                        'department' => strtoupper((string) ($asset->department ?: optional($asset->user)->department ?: '-')),
                        'position' => strtoupper((string) ($asset->position ?: optional($asset->user)->position ?: '-')),
                        'request_date' => $asset->request_date ? \Carbon\Carbon::parse($asset->request_date)->format('d M Y') : '-',
                        'label' => trim('REQ #' . str_pad($asset->id, 5, '0', STR_PAD_LEFT) . ' / ' . ($radioId ?: 'NO RADIO ID') . ' / ' . ($asset->full_name ?: optional($asset->user)->username)),
                    ];
                })->filter();
            })
            ->take(12)
            ->values();

        return response()->json([
            'results' => $results,
        ]);
    }

    private function returnPeopleOptions(Collection $activeAssets): Collection
    {
        $userOptions = User::query()
            ->orderByRaw("CASE WHEN name IS NULL OR name = '' THEN 1 ELSE 0 END")
            ->orderBy('name')
            ->orderBy('staff_no')
            ->get()
            ->map(fn (User $user) => [
                'name' => strtoupper((string) ($user->full_name ?: $user->username)),
                'department' => strtoupper((string) ($user->department ?: '')),
                'phone_no' => (string) ($user->phone_no ?: ''),
            ]);

        $ownershipOptions = $activeAssets
            ->flatMap(fn (AccessRequest $asset) => collect($asset->pic_details ?? []))
            ->filter(fn ($pic) => is_array($pic) && trim((string) ($pic['name'] ?? '')) !== '')
            ->map(fn (array $pic) => [
                'name' => strtoupper((string) ($pic['name'] ?? '')),
                'department' => strtoupper((string) ($pic['department'] ?? '')),
                'phone_no' => (string) ($pic['phone_no'] ?? ''),
            ]);

        return $userOptions
            ->concat($ownershipOptions)
            ->filter(fn ($person) => $person['name'] !== '')
            ->unique(fn ($person) => $person['name'] . '|' . $person['department'] . '|' . $person['phone_no'])
            ->values();
    }

    public function storeReturn(Request $request)
    {
        $request->validate([
            'access_request_id' => 'required',
            'return_date' => 'required|date',
            'selected_walkie_inventory_id' => 'nullable|integer',
            'selected_radio_id' => 'nullable|string|max:120',
            'selected_serial_number' => 'nullable|string|max:120',
            'return_person' => 'required|string|max:255',
            'return_department' => 'required|string|max:255',
            'return_phone_no' => 'required|string|max:50',
            'return_signature' => ['required', 'string', 'regex:/^data:image\/png;base64,/'],
        ]);

        $isAdminRoute = $request->routeIs('wt.admin.*');
        $mode = $this->managerMode($request);

        $access = $this->returnLookupQuery($request, $mode)
            ->where('id', $request->access_request_id)
            ->firstOrFail();

        $access = $this->singleUnitReturnRequest($access, $request);

        $access->update([
            'return_status' => $isAdminRoute ? 'Pending IT Approval' : 'Pending Admin Approval',
            'submit_to_admin_id' => $isAdminRoute ? auth('wt')->id() : $access->submit_to_admin_id,
            'return_date' => $request->return_date,
            'return_person' => trim((string) $request->return_person),
            'return_department' => trim((string) $request->return_department),
            'return_phone_no' => trim((string) $request->return_phone_no),
            'return_signature' => $request->return_signature,
        ]);

        $senderUser = auth('wt')->user();
        if ($isAdminRoute) {
            $itUsers = User::where('wt_role', 'admin_it')->get();
            SystemNotifier::notifyUsers(
                $itUsers,
                'Permintaan Return Baru',
                "Permintaan return untuk Request #{$access->id} menunggu semakan ICT.",
                'request_submitted'
            );
            SystemNotifier::notifyUser(
                $senderUser,
                'Permintaan Return Dihantar',
                "Permintaan return untuk Request #{$access->id} berjaya dihantar.",
                'request_sent'
            );

            if ((int) $access->user_id !== (int) $senderUser->id) {
                SystemNotifier::notifyUser(
                    (int) $access->user_id,
                    'Permintaan Return Dibuat Untuk Anda',
                    "Executive telah menghantar permintaan return untuk Request #{$access->id} bagi pihak anda.",
                    'request_sent'
                );
            }
        } else {
            SystemNotifier::notifyUser(
                (int) $access->submit_to_admin_id,
                'Permintaan Return Baru',
                "Pengguna telah menghantar permintaan return untuk Request #{$access->id}.",
                'request_submitted'
            );
            SystemNotifier::notifyUser(
                $senderUser,
                'Permintaan Return Dihantar',
                "Permintaan return untuk Request #{$access->id} berjaya dihantar kepada executive.",
                'request_sent'
            );
        }

        $successMessage = $isAdminRoute
            ? 'Return request submitted successfully and sent to ICT.'
            : 'Return request submitted. Please hand over the unit to the Executive.';

        if ($isAdminRoute) {
            return redirect()->route('wt.admin.all.status')->with('success', $successMessage);
        }

        return redirect()->route('wt.user.requests.status', ['status' => 'history'])->with('success', $successMessage);
    }

    private function singleUnitReturnRequest(AccessRequest $access, Request $request): AccessRequest
    {
        $selectedWalkieId = (int) $request->input('selected_walkie_inventory_id', 0);
        $selectedRadioId = trim((string) $request->input('selected_radio_id', ''));
        $selectedSerialNumber = trim((string) $request->input('selected_serial_number', ''));

        if ($selectedWalkieId <= 0 && $selectedRadioId === '') {
            return $access;
        }

        $assignedWalkieIds = collect($access->assigned_walkie_inventory_ids ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        if ($assignedWalkieIds->isEmpty() && $access->walkie_inventory_id) {
            $assignedWalkieIds = collect([(int) $access->walkie_inventory_id]);
        }

        $assignedRadioIds = collect($access->assigned_radio_ids ?? [])
            ->map(fn ($id) => trim((string) $id))
            ->filter()
            ->values();

        if ($assignedRadioIds->isEmpty() && $access->radio_id) {
            $assignedRadioIds = collect(explode(',', (string) $access->radio_id))
                ->map(fn ($id) => trim($id))
                ->filter()
                ->values();
        }

        $assignedSerials = collect($access->assigned_serial_numbers ?? [])
            ->map(fn ($serial) => trim((string) $serial))
            ->values();

        if ($assignedSerials->isEmpty() && $access->assigned_serial_number) {
            $assignedSerials = collect(explode(',', (string) $access->assigned_serial_number))
                ->map(fn ($serial) => trim($serial))
                ->values();
        }

        $unitCount = max($assignedWalkieIds->count(), $assignedRadioIds->count(), 1);
        if ($unitCount <= 1) {
            return $access;
        }

        $selectedIndex = null;
        if ($selectedWalkieId > 0) {
            $selectedIndex = $assignedWalkieIds->search($selectedWalkieId);
        }

        if ($selectedIndex === false || $selectedIndex === null) {
            $selectedIndex = $assignedRadioIds
                ->map(fn ($id) => strtoupper($id))
                ->search(strtoupper($selectedRadioId));
        }

        if ($selectedIndex === false || $selectedIndex === null) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'access_request_id' => 'Selected walkie talkie unit is not part of this active request.',
            ]);
        }

        return \DB::transaction(function () use (
            $access,
            $assignedWalkieIds,
            $assignedRadioIds,
            $assignedSerials,
            $selectedIndex,
            $selectedWalkieId,
            $selectedRadioId,
            $selectedSerialNumber
        ) {
            $selectedWalkieId = $selectedWalkieId > 0 ? $selectedWalkieId : (int) $assignedWalkieIds->get($selectedIndex, 0);
            $selectedRadioId = $selectedRadioId !== '' ? $selectedRadioId : (string) $assignedRadioIds->get($selectedIndex, $access->radio_id);
            $selectedSerialNumber = $selectedSerialNumber !== '' ? $selectedSerialNumber : (string) $assignedSerials->get($selectedIndex, '');
            $picDetails = collect($access->pic_details ?? [])->values();
            $selectedPicDetails = $picDetails->has($selectedIndex) ? [$picDetails->get($selectedIndex)] : ($access->pic_details ?? null);

            $returnAccess = $access->replicate();
            $returnAccess->walkie_inventory_id = $selectedWalkieId ?: null;
            $returnAccess->assigned_walkie_inventory_ids = $selectedWalkieId ? [$selectedWalkieId] : [];
            $returnAccess->radio_id = $selectedRadioId;
            $returnAccess->assigned_radio_ids = [$selectedRadioId];
            $returnAccess->assigned_serial_number = $selectedSerialNumber;
            $returnAccess->assigned_serial_numbers = $selectedSerialNumber !== '' ? [$selectedSerialNumber] : [];
            $returnAccess->quantity = 1;
            $returnAccess->pic_details = $selectedPicDetails;
            $returnAccess->return_status = null;
            $returnAccess->return_date = null;
            $returnAccess->handled_by = null;
            $returnAccess->created_at = now();
            $returnAccess->save();

            $remainingWalkieIds = $assignedWalkieIds
                ->reject(fn ($value, $index) => (int) $index === (int) $selectedIndex)
                ->values();
            $remainingRadioIds = $assignedRadioIds
                ->reject(fn ($value, $index) => (int) $index === (int) $selectedIndex)
                ->values();
            $remainingSerials = $assignedSerials
                ->reject(fn ($value, $index) => (int) $index === (int) $selectedIndex)
                ->values();
            $remainingPicDetails = $picDetails
                ->reject(fn ($value, $index) => (int) $index === (int) $selectedIndex)
                ->values();

            $access->assigned_walkie_inventory_ids = $remainingWalkieIds->all();
            $access->assigned_radio_ids = $remainingRadioIds->all();
            $access->assigned_serial_numbers = $remainingSerials->all();
            $access->pic_details = $remainingPicDetails->isNotEmpty() ? $remainingPicDetails->all() : $access->pic_details;
            $access->walkie_inventory_id = $remainingWalkieIds->first();
            $access->radio_id = $remainingRadioIds->implode(', ');
            $access->assigned_serial_number = $remainingSerials->implode(', ');
            $access->quantity = max($remainingRadioIds->count(), $remainingWalkieIds->count(), 1);
            $access->save();

            return $returnAccess;
        });
    }

    // --- REPORT DAMAGE ---
    public function createDamage(Request $request)
    {
        $mode = $request->routeIs('wt.admin.*') ? 'staff' : $this->managerMode($request);

        return redirect()->route($this->routePrefix($request) . '.damages.form', $request->routeIs('wt.admin.*') ? ['mode' => $mode] : []);
    }

    public function createDamageDashboard(Request $request)
    {
        $mode = $request->routeIs('wt.admin.*') ? 'staff' : $this->managerMode($request);
        $records = $this->damageRecordsForContext($request, $mode);
        $buckets = $this->damageStatusBuckets($records);
        $recentDamageRequests = $buckets['pending']
            ->take(5)
            ->values();
        $recentCompletedDamageRequests = $buckets['completed']
            ->take(5)
            ->values();

        $summary = [
            'new_request' => 1,
            'pending' => $buckets['pending']->count(),
            'drafts' => $buckets['drafts']->count(),
            'completed' => $buckets['completed']->count(),
        ];

        return view('wt.user.damages.index', compact('mode', 'summary', 'recentDamageRequests', 'recentCompletedDamageRequests'));
    }

    public function createDamageForm(Request $request)
    {
        $mode = $request->routeIs('wt.admin.*') ? 'staff' : $this->managerMode($request);
        $isAdminRoute = $request->routeIs('wt.admin.*');
        $admins = User::where('wt_role', 'admin')
            ->orderBy('staff_no')
            ->get();

        $managedUsers = $isAdminRoute ? $this->managedUsers() : collect();
        $currentUser = auth('wt')->user();
        $responsibilityKeys = collect([
            $currentUser->full_name,
            $currentUser->username,
            $currentUser->staff_id,
            $currentUser->department,
        ])
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->unique()
            ->values();
        $responsibleWalkies = $responsibilityKeys->isEmpty()
            ? collect()
            : WalkieTalkie::query()
                ->where(function ($query) use ($responsibilityKeys) {
                    foreach ($responsibilityKeys as $key) {
                        $query->orWhere('ownership', $key)
                            ->orWhere('shared_with', $key);
                    }
                })
                ->orderBy('radio_id')
                ->get(['walkie_id', 'radio_id', 'model', 'serial_number', 'status', 'ownership_type', 'shared_with', 'ownership', 'department']);
        $activeAssignedRequests = AccessRequest::query()
            ->where('status', 'Approved')
            ->where(function ($query) use ($currentUser) {
                $query->where('user_id', $currentUser->id)
                    ->orWhere('submit_to_admin_id', $currentUser->id)
                    ->orWhere('full_name', $currentUser->full_name)
                    ->orWhere('full_name', $currentUser->username);
            })
            ->where(function ($query) {
                $query->whereNull('return_status')
                    ->orWhereNotIn('return_status', ['Pending Admin Approval', 'Pending IT Approval', 'Returned']);
            })
            ->get();
        $activeAssignedWalkieIds = $activeAssignedRequests
            ->flatMap(function (AccessRequest $accessRequest) {
                $assignedIds = collect($accessRequest->assigned_walkie_inventory_ids ?? [])
                    ->map(fn ($id) => (int) $id)
                    ->filter();

                if ($assignedIds->isEmpty() && $accessRequest->walkie_inventory_id) {
                    $assignedIds->push((int) $accessRequest->walkie_inventory_id);
                }

                return $assignedIds;
            })
            ->unique()
            ->values();

        if ($activeAssignedWalkieIds->isNotEmpty()) {
            $assignedWalkies = WalkieTalkie::query()
                ->whereIn('walkie_id', $activeAssignedWalkieIds->all())
                ->orderBy('radio_id')
                ->get(['walkie_id', 'radio_id', 'model', 'serial_number', 'status', 'ownership_type', 'shared_with', 'ownership', 'department']);

            $responsibleWalkies = $responsibleWalkies
                ->merge($assignedWalkies)
                ->unique('walkie_id')
                ->sortBy('radio_id')
                ->values();
        }

        $responsibleWalkieAssignments = $responsibleWalkies
            ->mapWithKeys(function (WalkieTalkie $walkie) use ($activeAssignedRequests) {
                $matchedRequest = $activeAssignedRequests->first(function (AccessRequest $accessRequest) use ($walkie) {
                    $assignedIds = collect($accessRequest->assigned_walkie_inventory_ids ?? [])
                        ->map(fn ($id) => (int) $id)
                        ->filter();

                    if ($assignedIds->isEmpty() && $accessRequest->walkie_inventory_id) {
                        $assignedIds->push((int) $accessRequest->walkie_inventory_id);
                    }

                    $assignedRadioIds = collect($accessRequest->assigned_radio_ids ?? [])
                        ->map(fn ($id) => mb_strtoupper(trim((string) $id)))
                        ->filter();

                    return $assignedIds->contains((int) $walkie->walkie_id)
                        || $assignedRadioIds->contains(mb_strtoupper((string) $walkie->radio_id))
                        || (int) $accessRequest->walkie_inventory_id === (int) $walkie->walkie_id
                        || mb_strtoupper((string) $accessRequest->radio_id) === mb_strtoupper((string) $walkie->radio_id);
                });

                return [
                    $walkie->walkie_id => [
                        'reporter_name' => mb_strtoupper(trim((string) ($matchedRequest?->full_name ?: $walkie->ownership))),
                        'department' => mb_strtoupper(trim((string) ($matchedRequest?->department ?: $walkie->department))),
                        'ownership_type' => mb_strtoupper(trim((string) ($matchedRequest?->ownership_type ?: $walkie->ownership_type))),
                        'shared_with' => mb_strtoupper(trim((string) ($matchedRequest?->shared_with ?: $walkie->shared_with))),
                        'sector' => mb_strtoupper(trim((string) ($matchedRequest?->sector ?: ''))),
                        'bay_from' => preg_replace('/^BAY\s+/i', '', mb_strtoupper(trim((string) ($matchedRequest?->bay_from ?: '')))) ?: '',
                        'location' => mb_strtoupper(trim((string) ($matchedRequest?->location ?: ''))),
                    ],
                ];
            });
        $prefillDamage = [];

        if ($request->filled('walkie_id')) {
            $prefillWalkieId = (int) $request->query('walkie_id');
            $prefillWalkie = $responsibleWalkies->firstWhere('walkie_id', $prefillWalkieId);
            $prefillRequest = null;

            if (! $prefillWalkie) {
                $prefillRequest = $activeAssignedRequests
                    ->first(function (AccessRequest $accessRequest) use ($prefillWalkieId) {
                        $assignedIds = collect($accessRequest->assigned_walkie_inventory_ids ?? [])
                            ->map(fn ($id) => (int) $id)
                            ->filter();

                        if ($assignedIds->isEmpty() && $accessRequest->walkie_inventory_id) {
                            $assignedIds->push((int) $accessRequest->walkie_inventory_id);
                        }

                        return $assignedIds->contains($prefillWalkieId);
                    });

                if ($prefillRequest) {
                    $prefillWalkie = WalkieTalkie::query()
                        ->where('walkie_id', $prefillWalkieId)
                        ->first(['walkie_id', 'radio_id', 'model', 'serial_number', 'status', 'ownership_type', 'shared_with', 'ownership', 'department']);
                }
            }

            if ($prefillWalkie) {
                $prefillDamage = [
                    'reporter_name' => mb_strtoupper(trim((string) ($request->query('owner') ?: $prefillWalkie->ownership ?: $prefillRequest?->full_name))),
                    'department' => mb_strtoupper(trim((string) ($request->query('department') ?: $prefillWalkie->department ?: $prefillRequest?->department))),
                    'ownership_type' => mb_strtoupper(trim((string) ($request->query('ownership_type') ?: $prefillWalkie->ownership_type ?: $prefillRequest?->ownership_type))),
                    'shared_with' => mb_strtoupper(trim((string) ($request->query('shared_with') ?: $prefillWalkie->shared_with ?: $prefillRequest?->shared_with))),
                    'sector' => mb_strtoupper(trim((string) ($request->query('sector') ?: $prefillRequest?->sector))),
                    'bay_from' => preg_replace('/^BAY\s+/i', '', mb_strtoupper(trim((string) ($request->query('bay_from') ?: $prefillRequest?->bay_from)))) ?: '',
                    'location' => mb_strtoupper(trim((string) ($request->query('location') ?: $prefillRequest?->location))),
                    'model' => mb_strtoupper(trim((string) $prefillWalkie->model)),
                    'radio_id' => mb_strtoupper(trim((string) $prefillWalkie->radio_id)),
                    'serial_number' => mb_strtoupper(trim((string) $prefillWalkie->serial_number)),
                ];
            }
        }
        $draftRecord = null;
        $submittedRecord = null;

        if ($request->filled('draft')) {
            $draftId = (int) $request->query('draft');
            $draftRecord = $this->damageRecordsForContext($request, $mode)
                ->first(function (MaintenanceRecord $record) use ($draftId, $isAdminRoute) {
                    if ((int) $record->maintenance_id !== $draftId || strtoupper((string) $record->status) !== 'DRAFT') {
                        return false;
                    }

                    if (! $isAdminRoute && $record->request_source === 'manager_on_behalf_draft') {
                        return false;
                    }

                    return true;
                });
        }

        if (session()->has('recent_damage_id')) {
            $recentDamageId = (int) session('recent_damage_id');
            $submittedRecord = $this->damageRecordsForContext($request, $mode)
                ->first(fn (MaintenanceRecord $record) => (int) $record->maintenance_id === $recentDamageId);
        }

        return view('wt.user.damages.create', compact('admins', 'managedUsers', 'currentUser', 'responsibleWalkies', 'responsibleWalkieAssignments', 'mode', 'draftRecord', 'submittedRecord', 'prefillDamage'));
    }

    public function damageStatusPage(Request $request, string $bucket)
    {
        $mode = $request->routeIs('wt.admin.*') ? 'staff' : $this->managerMode($request);
        $records = $this->damageRecordsForContext($request, $mode);
        $buckets = $this->damageStatusBuckets($records);

        abort_unless(array_key_exists($bucket, $buckets), 404);

        $titles = [
            'pending' => 'Pending Faulty Reports',
            'drafts' => 'Draft Faulty Reports',
            'completed' => 'Completed Faulty Reports',
        ];

        $descriptions = [
            'pending' => 'Track reports that are still waiting for review or repair action.',
            'drafts' => 'Continue any saved faulty reports before submitting them.',
            'completed' => 'Review reports that have already been completed by the workflow.',
        ];

        $summary = [
            'pending' => $buckets['pending']->count(),
            'drafts' => $buckets['drafts']->count(),
            'completed' => $buckets['completed']->count(),
        ];

        $pageTitle = $titles[$bucket];
        $pageDescription = $descriptions[$bucket];
        $records = $buckets[$bucket];

        return view('wt.user.damages.status', compact('mode', 'bucket', 'records', 'summary', 'pageTitle', 'pageDescription'));
    }

    public function showDamageRecord(Request $request, int $damage)
    {
        $mode = $request->routeIs('wt.admin.*') ? 'staff' : $this->managerMode($request);
        $record = $this->damageRecordsForContext($request, $mode)
            ->first(fn (MaintenanceRecord $item) => (int) $item->maintenance_id === $damage);

        abort_unless($record, 404);

        return view('wt.user.damages.show', [
            'mode' => $mode,
            'record' => $record,
        ]);
    }

    public function requestTemporarySpare(Request $request, int $damage)
    {
        $mode = $request->routeIs('wt.admin.*') ? 'staff' : $this->managerMode($request);
        $record = $this->damageRecordsForContext($request, $mode)
            ->first(fn (MaintenanceRecord $item) => (int) $item->maintenance_id === $damage);

        abort_unless($record, 404);

        $validated = $request->validate([
            'need_temporary_spare' => 'required|in:0,1',
            'temporary_spare_request_note' => 'nullable|string|max:1000',
        ]);

        $requested = $validated['need_temporary_spare'] === '1';
        $note = trim((string) ($validated['temporary_spare_request_note'] ?? ''));

        $record->update([
            'temporary_spare_requested' => $requested,
            'temporary_spare_request_note' => $requested
                ? ($note !== '' ? $note : 'Temporary walkie requested while original unit is being handled.')
                : ($note !== '' ? $note : null),
        ]);

        SystemNotifier::notifyUser(
            auth('wt')->user(),
            'Temporary Walkie Response Recorded',
            $requested
                ? "Your request for a temporary walkie on faulty report #{$record->maintenance_id} has been recorded."
                : "You selected no temporary walkie for faulty report #{$record->maintenance_id}.",
            'received'
        );

        if ($requested) {
            $itUsers = User::where('wt_role', 'admin_it')->get();
            SystemNotifier::notifyUsers(
                $itUsers,
                'Temporary Walkie Requested',
                "Faulty report #{$record->maintenance_id} needs a temporary walkie while the original unit is handled.",
                'request_submitted'
            );
        }

        return redirect()
            ->route($this->routePrefix($request) . '.damages.form', $request->routeIs('wt.admin.*') ? ['mode' => $mode] : [])
            ->with('success', $requested
                ? 'Temporary walkie request recorded. ICT will review spare availability.'
                : 'Temporary walkie response recorded.'
            )
            ->with('recent_damage_id', $record->maintenance_id);
    }

    public function storeDamage(Request $request)
    {
        $isAdminRoute = $request->routeIs('wt.admin.*');
        $mode = $isAdminRoute ? 'staff' : $this->managerMode($request);
        $isDraft = false;

        $rules = [
            'model' => 'nullable|string|max:255',
            'radio_id' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'ownership_type' => 'nullable|string|in:SHARED,INDIVIDUAL',
            'shared_with' => 'nullable|string|max:255|required_if:ownership_type,SHARED',
            'sector' => 'nullable|string|max:255',
            'bay_from' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'problem_possible' => $isDraft ? 'nullable|array' : 'required|array|min:1',
            'problem_possible.*' => 'nullable|string|max:255',
            'other_problem' => 'nullable|string|max:1000',
            'remarks' => 'nullable|string|max:2000',
            'request_replacement' => 'nullable|in:0,1',
            'replacement_note' => 'nullable|string|max:1000',
            'need_temporary_spare' => ($isDraft ? 'nullable' : 'required') . '|in:0,1',
            'temporary_spare_request_note' => 'nullable|string|max:1000',
            'phone_no' => ($isDraft ? 'nullable' : 'required') . '|string|max:50',
            'handover_person' => ($isDraft ? 'nullable' : 'required') . '|string|max:255',
            'handover_at' => ($isDraft ? 'nullable' : 'required') . '|date',
            'pickup_person' => ($isDraft ? 'nullable' : 'required') . '|string|max:255',
            'pickup_at' => 'nullable|date',
            'draft_id' => 'nullable|integer|exists:maintenance_records,maintenance_id',
            'device_reference_image' => 'nullable|array|max:3',
            'device_reference_image.*' => 'file|mimes:jpg,jpeg,png,webp|max:10240',
            'damage_evidence' => 'nullable|array|max:3',
            'damage_evidence.*' => 'file|mimes:jpg,jpeg,png,webp,mp4,mov,avi|max:20480',
            'submit_action' => 'nullable|in:submit',
        ];

        if ($isAdminRoute) {
            if ($mode === 'staff') {
                $rules['user_id'] = ['nullable', 'integer', Rule::exists(User::class, 'id')];
                $rules['reporter_name'] = 'required|string|max:255';
                $rules['department'] = ($isDraft ? 'nullable' : 'required') . '|string|max:255';
                $rules['designation'] = 'nullable|string|max:255';
                $rules['quantity'] = ($isDraft ? 'nullable' : 'required') . '|integer|min:1|max:999';
                $rules['recipient_details'] = 'nullable|array';
                $rules['recipient_details.*.user_id'] = ['nullable', 'integer', Rule::exists(User::class, 'id')];
                $rules['recipient_details.*.reporter_name'] = ($isDraft ? 'nullable' : 'required') . '|string|max:255';
                $rules['recipient_details.*.phone_no'] = ($isDraft ? 'nullable' : 'required') . '|string|max:50';
                $rules['recipient_details.*.department'] = ($isDraft ? 'nullable' : 'required') . '|string|max:255';
                $rules['recipient_details.*.ownership_type'] = ($isDraft ? 'nullable' : 'required') . '|string|in:SHARED,INDIVIDUAL';
                $rules['recipient_details.*.shared_with'] = 'nullable|string|max:255';
                $rules['recipient_details.*.sector'] = ($isDraft ? 'nullable' : 'required') . '|string|max:255';
                $rules['recipient_details.*.bay_from'] = 'nullable|string|max:255';
                $rules['recipient_details.*.location'] = ($isDraft ? 'nullable' : 'required') . '|string|max:255';
                $rules['device_details'] = 'nullable|array';
                $rules['device_details.*.model'] = 'nullable|string|max:255';
                $rules['device_details.*.radio_id'] = 'nullable|string|max:100';
                $rules['device_details.*.serial_number'] = 'nullable|string|max:100';
            }
        } else {
            $rules['submit_to_admin_id'] = [($isDraft ? 'nullable' : 'required'), 'integer', Rule::exists(User::class, 'id')];
            $rules['reporter_name'] = ($isDraft ? 'nullable' : 'required') . '|string|max:255';
            $rules['staff_id'] = ($isDraft ? 'nullable' : 'required') . '|string|max:100';
            $rules['designation'] = ($isDraft ? 'nullable' : 'required') . '|string|max:255';
            $rules['department'] = ($isDraft ? 'nullable' : 'required') . '|string|max:255';
        }

        $validated = $request->validate($rules);
        $damageQuantity = ($isAdminRoute && $mode === 'staff' && ! $isDraft)
            ? max(1, min(999, (int) ($validated['quantity'] ?? 1)))
            : 1;

        if ($isAdminRoute && $mode === 'staff' && ! $isDraft) {
            $submittedRecipientRows = collect($validated['recipient_details'] ?? []);
            if ($submittedRecipientRows->count() < $damageQuantity - 1) {
                return back()
                    ->withInput()
                    ->withErrors(['recipient_details' => 'Please complete recipient information based on the selected quantity.']);
            }

            $missingSharedWith = $submittedRecipientRows
                ->take(max(0, $damageQuantity - 1))
                ->contains(fn (array $recipient) => strtoupper((string) ($recipient['ownership_type'] ?? '')) === 'SHARED' && blank($recipient['shared_with'] ?? null));

            if ($missingSharedWith) {
                return back()
                    ->withInput()
                    ->withErrors(['recipient_details' => 'Please fill in Shared With for every shared recipient.']);
            }

            $submittedDeviceRows = collect($validated['device_details'] ?? []);
            $missingDeviceDetails = $submittedDeviceRows->count() < $damageQuantity
                || $submittedDeviceRows
                    ->take($damageQuantity)
                    ->contains(fn (array $device) => blank($device['model'] ?? null) && blank($device['radio_id'] ?? null) && blank($device['serial_number'] ?? null));

            if ($missingDeviceDetails) {
                return back()
                    ->withInput()
                    ->withErrors(['device_details' => 'Please fill in at least one device detail for every faulty report quantity.']);
            }
        }

        if (! $isDraft
            && blank($validated['model'] ?? null)
            && blank($validated['radio_id'] ?? null)
            && blank($validated['serial_number'] ?? null)
            && collect($validated['device_details'] ?? [])->every(fn ($device) => blank($device['model'] ?? null) && blank($device['radio_id'] ?? null) && blank($device['serial_number'] ?? null))
            && ! $request->hasFile('device_reference_image')
            && ! $request->hasFile('damage_evidence')
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'device_details' => 'Please fill in at least one device detail or upload a device photo/video for ICT to identify manually.',
                ]);
        }

        if (! $isAdminRoute && ! empty($validated['submit_to_admin_id'])) {
            $selectedAdmin = User::where('id', $validated['submit_to_admin_id'])->first();
            if (! $selectedAdmin || $selectedAdmin->wt_role !== 'admin') {
                return back()
                    ->withInput()
                    ->withErrors(['submit_to_admin_id' => 'Selected approver must be an Executive account.']);
            }
        }

        $recipientRows = collect();

        if ($isAdminRoute) {
            $targetUser = auth('wt')->user();
            if ($mode === 'staff') {
                $targetUser = $this->findOrCreateManagedUserForDamageReport($validated);
                if (! $targetUser) {
                    return back()->withInput()->withErrors(['reporter_name' => 'Please enter a valid recipient name.']);
                }

                $recipientRows->push([
                    'user' => $targetUser,
                    'validated' => $validated,
                ]);

                collect($validated['recipient_details'] ?? [])
                    ->take(max(0, $damageQuantity - 1))
                    ->each(function (array $recipient) use (&$recipientRows, $validated) {
                        $recipientValidated = array_merge($validated, [
                            'user_id' => $recipient['user_id'] ?? null,
                            'reporter_name' => $recipient['reporter_name'] ?? '',
                            'phone_no' => $recipient['phone_no'] ?? '',
                            'department' => $recipient['department'] ?? '',
                            'ownership_type' => $recipient['ownership_type'] ?? '',
                            'shared_with' => $recipient['shared_with'] ?? null,
                            'sector' => $recipient['sector'] ?? '',
                            'bay_from' => $recipient['bay_from'] ?? '',
                            'location' => $recipient['location'] ?? '',
                        ]);

                        $recipientUser = $this->findOrCreateManagedUserForDamageReport($recipientValidated);

                        if ($recipientUser) {
                            $recipientRows->push([
                                'user' => $recipientUser,
                                'validated' => $recipientValidated,
                            ]);
                        }
                    });
            }

            $reporterName = $targetUser->full_name ?: $targetUser->username;
            $reporterExecutiveId = null;
            $designation = $validated['designation'] ?? ($targetUser->position ?: 'STAFF');
            $department = $targetUser->department ?: (auth('wt')->user()->department ?: 'GENERAL');
            $submitToAdminId = auth('wt')->id();
            $status = $isDraft ? 'Draft' : 'PENDING ADMIN IT';
        } else {
            $reporterName = auth('wt')->user()->username;
            $reporterExecutiveId = $validated['staff_id'] ?? auth('wt')->user()->staff_id ?? '-';
            $designation = $validated['designation'] ?? auth('wt')->user()->position ?? 'EXECUTIVE';
            $department = $validated['department'] ?? auth('wt')->user()->department ?? 'GENERAL';
            $submitToAdminId = $validated['submit_to_admin_id'] ?? null;
            $status = $isDraft ? 'Draft' : 'WAITING FOR ADMIN';
        }

        $problemList = $validated['problem_possible'] ?? [];
        if (!empty($validated['other_problem'])) {
            $problemList[] = 'OTHER: ' . $validated['other_problem'];
        }

        $replacementRequested = ($validated['request_replacement'] ?? '0') === '1';
        $replacementNote = trim((string) ($validated['replacement_note'] ?? ''));
        $temporarySpareRequested = ($validated['need_temporary_spare'] ?? '0') === '1';
        $temporarySpareNote = trim((string) ($validated['temporary_spare_request_note'] ?? ''));
        $remarks = trim((string) ($validated['remarks'] ?? ''));

        if ($replacementRequested) {
            $replacementLine = 'REPLACEMENT REQUESTED';
            if ($replacementNote !== '') {
                $replacementLine .= ': ' . strtoupper($replacementNote);
            }
            $remarks = $remarks !== '' ? $remarks . ' | ' . $replacementLine : $replacementLine;
        }

        $uploadFolder = 'damage-evidence/' . now()->format('Ymd_His') . '_' . Str::upper(Str::random(6));
        $firstDeviceDetail = collect($validated['device_details'] ?? [])->first() ?: [];
        $deviceImageName = $this->safeUploadName(($validated['radio_id'] ?? null) ?: ($firstDeviceDetail['radio_id'] ?? null), 'RADIO_ID');
        $evidencePaths = array_merge(
            $this->storeDamageUploads($request, 'device_reference_image', $uploadFolder, $deviceImageName),
            $this->storeDamageUploads($request, 'damage_evidence', $uploadFolder, 'EVIDENCE')
        );

        $draftRecord = null;
        if (! empty($validated['draft_id'])) {
            $draftRecord = $this->damageRecordsForContext($request, $mode)
                ->first(fn (MaintenanceRecord $record) => (int) $record->maintenance_id === (int) $validated['draft_id'] && strtoupper((string) $record->status) === 'DRAFT');
        }

        $requestSource = ($isAdminRoute && $mode === 'staff' && $isDraft)
            ? 'manager_on_behalf_draft'
            : 'user';
        $reportedWalkie = $this->findReportedWalkie(array_merge($validated, $firstDeviceDetail));

        $damagePayload = [
            'walkie_id' => $reportedWalkie?->walkie_id,
            'radio_id' => ($validated['radio_id'] ?? null) ?: ($firstDeviceDetail['radio_id'] ?? null),
            'serial_number' => ($validated['serial_number'] ?? null) ?: ($firstDeviceDetail['serial_number'] ?? null),
            'model' => ($validated['model'] ?? null) ?: ($firstDeviceDetail['model'] ?? null),
            'department_name' => $department,
            'received_date' => $draftRecord?->received_date ?? now()->toDateString(),
            'repair_date' => null,
            'done' => false,
            'finish_date' => null,
            'issue_description' => ! empty($problemList) ? implode(', ', $problemList) : null,
            'issue' => ! empty($problemList) ? implode(', ', $problemList) : null,
            'remarks' => $remarks !== '' ? $remarks : null,
            'temporary_spare_requested' => $temporarySpareRequested,
            'temporary_spare_request_note' => $temporarySpareRequested
                ? ($temporarySpareNote !== '' ? $temporarySpareNote : 'Temporary spare requested while original unit is being repaired.')
                : ($temporarySpareNote !== '' ? $temporarySpareNote : null),
            'maintenance_date' => $draftRecord?->maintenance_date ?? now()->toDateString(),
            'status' => $status,
            'request_source' => $requestSource,
            'submit_to_admin_id' => $submitToAdminId,
            'reporter_name' => $reporterName,
            'reporter_staff_id' => $reporterExecutiveId,
            'designation' => $designation,
            'phone_no' => $validated['phone_no'] ?? null,
            'handover_person' => $validated['handover_person'] ?? null,
            'handover_at' => ! empty($validated['handover_at']) ? \Carbon\Carbon::parse($validated['handover_at'])->toDateTimeString() : null,
            'pickup_person' => $validated['pickup_person'] ?? null,
            'pickup_at' => ! empty($validated['pickup_at']) ? \Carbon\Carbon::parse($validated['pickup_at'])->toDateTimeString() : null,
            'ownership_type' => $validated['ownership_type'] ?? null,
            'shared_with' => $validated['shared_with'] ?? null,
            'sector' => $validated['sector'] ?? null,
            'location' => $validated['location'] ?? null,
            'problem_possible' => ! empty($problemList) ? implode(', ', $problemList) : null,
            'evidence_paths' => $evidencePaths ?: ($draftRecord->evidence_paths ?? null),
        ];

        $createdDamageRecords = collect();

        if ($draftRecord) {
            $draftRecord->update($damagePayload);
            $damageRecord = $draftRecord;
            $createdDamageRecords->push($damageRecord);
        } else {
            for ($quantityIndex = 1; $quantityIndex <= $damageQuantity; $quantityIndex++) {
                $payload = $damagePayload;
                $recipientRow = $recipientRows->get($quantityIndex - 1);

                if ($recipientRow) {
                    $recipientUser = $recipientRow['user'];
                    $recipientValidated = $recipientRow['validated'];

                    $payload['reporter_name'] = $recipientUser->full_name ?: $recipientUser->username;
                    $payload['designation'] = $recipientValidated['designation'] ?? ($recipientUser->position ?: 'STAFF');
                    $payload['phone_no'] = $recipientValidated['phone_no'] ?? null;
                    $payload['department_name'] = $recipientValidated['department'] ?? ($recipientUser->department ?: 'GENERAL');
                    $payload['ownership_type'] = $recipientValidated['ownership_type'] ?? null;
                    $payload['shared_with'] = $recipientValidated['shared_with'] ?? null;
                    $payload['sector'] = $recipientValidated['sector'] ?? null;
                    $payload['location'] = $recipientValidated['location'] ?? null;
                }

                $deviceDetail = $validated['device_details'][$quantityIndex - 1] ?? [];
                if (! empty($deviceDetail)) {
                    $payload['model'] = $deviceDetail['model'] ?? null;
                    $payload['radio_id'] = $deviceDetail['radio_id'] ?? null;
                    $payload['serial_number'] = $deviceDetail['serial_number'] ?? null;
                    $rowReportedWalkie = $this->findReportedWalkie(array_merge($validated, $deviceDetail));
                    $payload['walkie_id'] = $rowReportedWalkie?->walkie_id;
                }

                $recipientValidatedForBay = $recipientRow['validated'] ?? [];
                $bayFrom = trim((string) (($recipientValidatedForBay['bay_from'] ?? null) ?: ($validated['bay_from'] ?? '')));
                if ($bayFrom !== '') {
                    $payload['remarks'] = trim((string) ($payload['remarks'] ?? '')) !== ''
                        ? $payload['remarks'] . " | BAY: {$bayFrom}"
                        : "BAY: {$bayFrom}";
                }

                if ($damageQuantity > 1) {
                    $payload['remarks'] = trim((string) ($payload['remarks'] ?? '')) !== ''
                        ? $payload['remarks'] . " | BATCH ITEM {$quantityIndex}/{$damageQuantity}"
                        : "BATCH ITEM {$quantityIndex}/{$damageQuantity}";
                }

                $createdDamageRecords->push(MaintenanceRecord::create($payload));
            }

            $damageRecord = $createdDamageRecords->first();
        }

        if (! $isDraft && $reportedWalkie) {
            $reportedWalkie->update([
                'status' => 'FAULTY',
                'remark' => trim((string) $reportedWalkie->remark) !== ''
                    ? $reportedWalkie->remark . ' | Faulty report #' . $damageRecord->maintenance_id . ' submitted'
                    : 'Faulty report #' . $damageRecord->maintenance_id . ' submitted',
            ]);
        }

        \App\Models\UserActivityLog::create([
            'user_id' => auth('wt')->id(),
            'username' => auth('wt')->user()->username,
            'event_type' => 'maintenance',
            'event_action' => $isDraft ? 'Damage Report Draft Saved' : 'Damage Report Submitted',
            'event_details' => ($isDraft
                ? "Saved draft damage report #{$damageRecord->maintenance_id}"
                : ($damageQuantity > 1
                    ? "Submitted {$damageQuantity} damage reports starting from #{$damageRecord->maintenance_id} for Radio " . ($validated['radio_id'] ?? $validated['serial_number'] ?? 'N/A')
                    : "Submitted damage report #{$damageRecord->maintenance_id} for Radio " . ($validated['radio_id'] ?? $validated['serial_number'] ?? 'N/A')))
                . ($replacementRequested ? ' with replacement request.' : '.')
                . ($temporarySpareRequested ? ' Temporary spare requested.' : ' No temporary spare requested.'),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        $senderUser = auth('wt')->user();
        if ($isDraft) {
            SystemNotifier::notifyUser(
                $senderUser,
                'Faulty Report Draft Saved',
                "Your faulty report draft #{$damageRecord->maintenance_id} has been saved.",
                'info'
            );

            return redirect()
                ->route($this->routePrefix($request) . '.damages.status', ['bucket' => 'drafts', 'mode' => $mode])
                ->with('success', 'Faulty report saved as draft.');
        }

        if ($isAdminRoute) {
            $itUsers = User::where('wt_role', 'admin_it')->get();
            SystemNotifier::notifyUsers(
                $itUsers,
                'Laporan Kerosakan Baru',
                $damageQuantity > 1
                    ? "{$damageQuantity} laporan kerosakan baru bermula #{$damageRecord->maintenance_id} menunggu semakan ICT."
                    : "Laporan kerosakan #{$damageRecord->maintenance_id} menunggu semakan ICT.",
                'request_submitted'
            );
            SystemNotifier::notifyUser(
                $senderUser,
                'Damage Report Submitted',
                $damageQuantity > 1
                    ? "{$damageQuantity} damage reports have been submitted. Please hand over the faulty walkie talkies to ICT Department Sejurumus for inspection. Pickup can be done at ICT Department Sejurumus after ICT approval."
                    : "Damage report #{$damageRecord->maintenance_id} has been submitted. Please hand over the faulty walkie talkie to ICT Department Sejurumus for inspection. Pickup can be done at ICT Department Sejurumus after ICT approval." . ($temporarySpareRequested ? ' Your temporary spare request has been recorded and ICT will review stock availability.' : ' You selected no temporary spare.'),
                'request_sent'
            );

        } else {
            SystemNotifier::notifyUser(
                (int) $submitToAdminId,
                'Laporan Kerosakan Baru',
                "Pengguna telah menghantar laporan kerosakan #{$damageRecord->maintenance_id} untuk semakan anda. Handover WT rosak dan pickup selepas approval akan dibuat di ICT Department Sejurumus.",
                'request_submitted'
            );
            SystemNotifier::notifyUser(
                $senderUser,
                'Damage Report Submitted',
                "Damage report #{$damageRecord->maintenance_id} has been submitted. Please hand over the faulty walkie talkie to ICT Department Sejurumus for inspection. Pickup can be done at ICT Department Sejurumus after ICT approval." . ($temporarySpareRequested ? ' Your temporary spare request has been recorded and ICT will review stock availability.' : ' You selected no temporary spare.'),
                'request_sent'
            );
        }

        $successMessage = $damageQuantity > 1
            ? "{$damageQuantity} damage reports submitted successfully. Please send the walkie talkies to ICT Department Sejurumus for inspection. Pickup can be done there after ICT approval."
            : 'Damage report submitted successfully. Please hand over the walkie talkie to ICT Department Sejurumus for inspection. Pickup can be done there after ICT approval.';
        $successMessage .= $temporarySpareRequested
            ? ' Temporary spare request recorded; ICT will decide based on stock availability.'
            : ' No temporary spare requested.';

        return redirect()
            ->route($this->routePrefix($request) . '.damages.form', $isAdminRoute ? ['mode' => $mode] : [])
            ->with('success', $successMessage)
            ->with('recent_damage_id', $damageRecord->maintenance_id);
    }



    public function requestStatus()
    {
        TemporaryRequestExpiryService::syncExpired();

        $historyRetentionYears = $this->returnHistoryRetentionYears();
        $returnHistoryCutoff = now()->subYears($historyRetentionYears)->startOfDay();

        $accessRequests = AccessRequest::query()
            ->where('user_id', auth('wt')->id())
            ->where(function ($query) {
                $query->where('status', '!=', 'Draft')
                    ->orWhere(function ($draftQuery) {
                        $draftQuery->where('status', 'Draft')
                            ->whereNotNull('submit_to_admin_id')
                            ->where('submit_to_admin_id', '!=', auth('wt')->id());
                    });
            })
            ->where(function ($query) {
                $query->whereNull('request_type')
                    ->orWhereIn('request_type', ['walkie_talkie', 'temporary_walkie_talkie']);
            })
            ->where(function ($query) use ($returnHistoryCutoff) {
                $query->whereNull('return_status')
                    ->orWhere('return_status', '!=', 'Returned')
                    ->orWhere(function ($returnedQuery) use ($returnHistoryCutoff) {
                        $returnedQuery->where('return_status', 'Returned')
                            ->where(function ($dateQuery) use ($returnHistoryCutoff) {
                                $dateQuery->where('return_date', '>=', $returnHistoryCutoff->toDateString())
                                    ->orWhere(function ($missingReturnDateQuery) use ($returnHistoryCutoff) {
                                        $missingReturnDateQuery->whereNull('return_date')
                                            ->where('created_at', '>=', $returnHistoryCutoff);
                                    });
                            });
                    });
            })
            ->orderByDesc('request_date')
            ->orderByDesc('id')
            ->get();

        $repairRecords = MaintenanceRecord::query()
            ->where(function ($query) {
                $user = auth('wt')->user();
                $query->where('reporter_name', $user->username)
                    ->orWhere('reporter_name', $user->full_name)
                    ->orWhere('reporter_staff_id', $user->staff_id);
            })
            ->orderByDesc('finish_date')
            ->orderByDesc('received_date')
            ->orderByDesc('maintenance_id')
            ->get();

        $requestStatuses = $accessRequests->map(function (AccessRequest $request) use ($historyRetentionYears) {
            $hasPendingReturn = in_array($request->return_status, ['Pending Admin Approval', 'Pending IT Approval'], true);
            $isReturned = $request->return_status === 'Returned';
            $effectiveStatus = $isReturned ? 'Returned' : ($hasPendingReturn ? $request->return_status : $request->status);

            return (object) [
                'source_type' => 'request',
                'id' => $request->id,
                'status' => $effectiveStatus,
                'return_status' => $request->return_status,
                'return_date' => $request->return_date,
                'status_group' => match ($effectiveStatus) {
                    'Draft' => 'draft',
                    'Pending Admin Approval', 'Pending IT Approval' => 'processing',
                    'Pending Executive Pickup' => 'ready',
                    'Approved' => 'approved',
                    'Returned' => 'history',
                    'Rejected' => 'rejected',
                    default => 'processing',
                },
                'title' => $request->event_name ?: 'Walkie Talkie Request',
                'request_date' => $request->request_date,
                'end_date' => $request->end_date,
                'department' => $request->department,
                'position' => $request->position,
                'radio_id' => $request->radio_id,
                'assigned_serial_number' => $request->assigned_serial_number,
                'accessories' => $request->accessories,
                'is_temporary' => $request->request_type === 'temporary_walkie_talkie',
                'quantity' => $request->quantity,
                'duration_days' => $request->duration_days,
                'note' => $isReturned
                    ? "Returned unit history. Stored up to {$historyRetentionYears} years."
                    : ($hasPendingReturn
                    ? 'Return request is waiting for approval and ICT confirmation.'
                    : ($request->status === 'Draft'
                        ? 'Draft only. Waiting for executive to submit to ICT.'
                        : null)),
                'approval_remark' => $request->approval_remark,
                'sort_date' => $request->request_date ?: optional($request->created_at)->format('Y-m-d'),
            ];
        });

        $repairStatuses = $repairRecords->map(function (MaintenanceRecord $record) {
            $recordStatus = strtoupper((string) $record->status);
            $isRejected = in_array($recordStatus, ['REJECTED', 'REFUSED'], true);
            $isReadyToCollect = ! $isRejected && ((bool) $record->done || in_array($recordStatus, ['DONE', 'READY TO COLLECT'], true));

            return (object) [
                'source_type' => 'repair',
                'id' => $record->maintenance_id,
                'status' => $record->status,
                'status_group' => $isRejected ? 'rejected' : ($isReadyToCollect ? 'ready' : 'processing'),
                'title' => 'Faulty Walkie Talkie Report',
                'request_date' => $record->received_date ?: $record->maintenance_date,
                'end_date' => null,
                'department' => $record->department_name,
                'position' => $record->designation,
                'radio_id' => $record->radio_id ?: $record->serial_number,
                'is_temporary' => false,
                'quantity' => null,
                'duration_days' => null,
                'note' => $isRejected
                    ? ($record->remarks ?: 'Damage report was rejected by ICT.')
                    : ($isReadyToCollect
                        ? ($recordStatus === 'READY TO COLLECT' ? 'Ready to collect from ICT.' : 'Already fixed and ready to collect.')
                        : 'Repair request is still being processed.'),
                'sort_date' => $record->finish_date ?: $record->received_date ?: $record->maintenance_date,
            ];
        });

        $requestStatuses = $requestStatuses
            ->concat($repairStatuses)
            ->sortByDesc(function ($item) {
                return sprintf('%s-%010d', $item->sort_date ?: '0000-00-00', $item->id);
            })
            ->values();

        $activeStatusFilter = request()->query('status');
        $statusSummary = [
            'draft' => $requestStatuses->filter(fn ($request) => $request->status_group === 'draft')->count(),
            'processing' => $requestStatuses->filter(fn ($request) => $request->status_group === 'processing')->count(),
            'ready' => $requestStatuses->filter(fn ($request) => $request->status_group === 'ready')->count(),
            'approved' => $requestStatuses->filter(fn ($request) => $request->status_group === 'approved')->count(),
            'history' => $requestStatuses->filter(fn ($request) => $request->status_group === 'history')->count(),
            'rejected' => $requestStatuses->filter(fn ($request) => $request->status_group === 'rejected')->count(),
        ];

        if (array_key_exists($activeStatusFilter, $statusSummary)) {
            $requestStatuses = $requestStatuses
                ->filter(fn ($request) => $request->status_group === $activeStatusFilter)
                ->values();
        } else {
            $activeStatusFilter = null;
        }

        return view('wt.user.request_status', compact('requestStatuses', 'statusSummary', 'historyRetentionYears', 'activeStatusFilter'));
    }

    private function returnHistoryRetentionYears(): int
    {
        $years = (int) env('WT_RETURN_HISTORY_YEARS', 5);

        return max(1, min(5, $years));
    }
}
