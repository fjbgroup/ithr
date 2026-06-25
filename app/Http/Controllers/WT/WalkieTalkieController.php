<?php

namespace App\Http\Controllers\WT;

use Illuminate\Http\Request;
use App\Models\WT\AccessRequest;
use App\Models\WT\Handover;
use App\Models\WT\WalkieTalkie;
use App\Models\WT\MasterData;
use App\Models\WT\MaintenanceRecord;
use App\Models\WT\UserActivityLog;
use App\Models\Staff;
use App\Services\TemporaryRequestExpiryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\WalkieTalkieImport;

class WalkieTalkieController extends Controller
{
    private const ALLOWED_STATUSES = [
        'CALIBRATING',
        'UNKNOWN',
        'IN USE',
        'UNDER REPAIR',
        'REPAIRING',
        'UNUSED',
        'FAULTY',
        'B.E.R',
        'READY TO COLLECT',
        'ALREADY FIXED',
        'TEMPORARY',
        'CHANGE ID',
        'LOST',
    ];

    private const ALLOWED_MODELS = [
        'R7',
        'P8200',
        'P8268',
        'P8600I',
        'P8660I',
        'P8260',
    ];

    private const ALLOWED_OWNERSHIP_TYPES = [
        'INDIVIDUAL',
        'SHARED',
        'SPARE',
        'UNALLOCATED',
    ];

    private function normalizeValue(?string $value): string
    {
        return strtoupper(trim((string) $value));
    }

    /**
     * Option lists for the inventory-tool views (special use, unused,
     * duplicated ID) so their add/edit modals share the same master-data
     * driven lists as the main inventory form instead of hardcoding them.
     */
    private function inventoryToolOptions(): array
    {
        return [
            'walkieModels' => $this->mergeMasterData('model', WalkieTalkie::query()->pluck('model')),
            'statusOptions' => collect(self::ALLOWED_STATUSES),
            'ownershipTypeOptions' => $this->mergeMasterData(
                'ownership_type',
                WalkieTalkie::query()->pluck('ownership_type')
                    ->merge(WalkieTalkie::query()->pluck('ownership_type_to_be'))
            ),
            'locationOptions' => $this->mergeMasterData('location', WalkieTalkie::query()->pluck('location')->merge(collect(['T4', 'T5', 'GT', 'LBSB']))),
        ];
    }

    /**
     * Build a dropdown option list from the WT master data for a category,
     * merged with any values already present on existing walkie records so
     * historical free-text entries never disappear from the form.
     */
    private function mergeMasterData(string $category, $existingValues)
    {
        return MasterData::valuesFor($category)
            ->merge($existingValues)
            ->map(fn ($value) => $this->normalizeValue($value))
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }

    /**
     * Ownership of a walkie must reference an existing staff record, so the
     * ownership-name dropdowns are sourced from the staff table rather than
     * free-text. Names are returned as stored on the staff record.
     */
    private function staffOwnershipOptions()
    {
        return Staff::query()
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->orderBy('name')
            ->pluck('name')
            ->unique()
            ->values();
    }

    private function normalizeInventoryStatus(?string $value): string
    {
        $status = $this->normalizeValue($value);

        return $status === '' ? 'UNKNOWN' : $status;
    }

    private function actionRemark(Request $request): string
    {
        return trim((string) $request->input('action_remark', ''));
    }

    private function withActionRemark(string $details, Request $request): string
    {
        $remark = $this->actionRemark($request);

        return $remark === '' ? $details : $details . ' Remark: ' . $remark;
    }

    private function getFormOptions(): array
    {
        $walkies = WalkieTalkie::orderByDesc('created_at')
            ->orderByDesc('walkie_id')
            ->get();

        return [
            'walkies' => $walkies,
            'walkieRadioIds' => $walkies->pluck('radio_id')->filter()->unique()->sort()->values(),
            'walkieSerials' => $walkies->pluck('serial_number')->filter()->unique()->sort()->values(),
            'walkieModels' => $this->mergeMasterData('model', $walkies->pluck('model')),
            'walkieOwnerships' => $walkies->pluck('ownership')->filter()->unique()->sort()->values(),
            'staffOwnerships' => $this->staffOwnershipOptions(),
            'walkieDepartments' => $this->mergeMasterData('department', $walkies->pluck('department')),
            'walkieLocations' => $this->mergeMasterData('location', $walkies->pluck('location')->merge(collect(['T4', 'T5', 'GT', 'LBSB']))),
            'walkiePositions' => $this->mergeMasterData('position', $walkies->pluck('position')),
            'walkieTemporaryIds' => $walkies->pluck('temporary_radio_id')->filter()->unique()->sort()->values(),
            'walkieTrackingRefs' => $walkies->pluck('tracking_ref')->filter()->unique()->sort()->values(),
            'statusOptions' => collect(self::ALLOWED_STATUSES),
            'ownershipTypeOptions' => $this->mergeMasterData(
                'ownership_type',
                collect(self::ALLOWED_OWNERSHIP_TYPES)
                    ->merge($walkies->pluck('ownership_type'))
                    ->merge($walkies->pluck('ownership_type_to_be'))
            ),
            'yesNoOptions' => collect([
                ['value' => '0', 'label' => 'NO'],
                ['value' => '1', 'label' => 'YES'],
            ]),
        ];
    }

    private function timelineDate(?string $value): ?array
    {
        if (blank($value)) {
            return null;
        }

        try {
            $date = \Carbon\Carbon::parse($value);
        } catch (\Throwable $exception) {
            return null;
        }

        return [
            'sort' => $date->timestamp,
            'date' => $date->format('d M Y'),
            'time' => $date->format('h:i A'),
        ];
    }

    private function timelineEvent(?string $dateValue, string $title, string $detail, string $type = 'info'): ?array
    {
        $date = $this->timelineDate($dateValue);

        if (! $date) {
            return null;
        }

        return [
            'sort' => $date['sort'],
            'date' => $date['date'],
            'time' => $date['time'],
            'title' => $title,
            'detail' => $detail,
            'type' => $type,
        ];
    }

    private function buildWalkieTimelineData($walkies): array
    {
        $walkieIds = $walkies->pluck('walkie_id')->filter()->values();
        $radioIds = $walkies->pluck('radio_id')->filter()->values();
        $serialNumbers = $walkies->pluck('serial_number')->filter()->values();

        $maintenanceRecords = MaintenanceRecord::query()
            ->whereIn('walkie_id', $walkieIds)
            ->orWhereIn('radio_id', $radioIds)
            ->orWhereIn('serial_number', $serialNumbers)
            ->orderBy('maintenance_id')
            ->get();

        $handovers = Handover::query()
            ->whereIn('radio_id', $radioIds)
            ->orWhereIn('walkie_talkie_id', $walkieIds->map(fn ($id) => (string) $id))
            ->orWhereIn('walkie_talkie_id', $radioIds)
            ->orderBy('issued_at')
            ->get();

        $activityLogs = UserActivityLog::query()
            ->where(function ($query) {
                $query->where('event_details', 'like', '%Walkie Talkie%')
                    ->orWhere('event_details', 'like', '%Unit ID:%')
                    ->orWhere('event_details', 'like', '%unit%');
            })
            ->orderByDesc('created_at')
            ->limit(600)
            ->get();

        return $walkies->mapWithKeys(function (WalkieTalkie $walkie) use ($maintenanceRecords, $handovers, $activityLogs) {
            $events = collect();

            $registered = $this->timelineEvent(
                $walkie->created_at,
                'Unit registered',
                'Added to inventory as ' . ($walkie->status ?: 'UNKNOWN') . '.',
                'created'
            );
            if ($registered) {
                $events->push($registered);
            }

            $matchedHandovers = $handovers->filter(function (Handover $handover) use ($walkie) {
                return (string) $handover->radio_id === (string) $walkie->radio_id
                    || (string) $handover->walkie_talkie_id === (string) $walkie->walkie_id
                    || (string) $handover->walkie_talkie_id === (string) $walkie->radio_id;
            });

            foreach ($matchedHandovers as $handover) {
                $issued = $this->timelineEvent(
                    $handover->issued_at,
                    'Issued to user',
                    trim(($handover->staff_name ?: $walkie->ownership ?: 'User') . (($handover->department ?: $walkie->department) ? ' - ' . ($handover->department ?: $walkie->department) : '')),
                    'handover'
                );
                if ($issued) {
                    $events->push($issued);
                }

                $returned = $this->timelineEvent(
                    $handover->returned_at,
                    'Returned',
                    'Unit returned from ' . ($handover->staff_name ?: $walkie->ownership ?: 'user') . '.',
                    'return'
                );
                if ($returned) {
                    $events->push($returned);
                }
            }

            $matchedMaintenance = $maintenanceRecords->filter(function (MaintenanceRecord $record) use ($walkie) {
                return (int) $record->walkie_id === (int) $walkie->walkie_id
                    || (string) $record->radio_id === (string) $walkie->radio_id
                    || (string) $record->serial_number === (string) $walkie->serial_number;
            });

            foreach ($matchedMaintenance as $record) {
                $received = $this->timelineEvent(
                    $record->received_date ?: $record->maintenance_date,
                    'Maintenance reported',
                    $record->issue_description ?: $record->issue ?: $record->remarks ?: 'Issue record created.',
                    'maintenance'
                );
                if ($received) {
                    $events->push($received);
                }

                $repair = $this->timelineEvent(
                    $record->repair_date,
                    'Repair started',
                    'Status: ' . ($record->status ?: 'UNDER REPAIR') . '.',
                    'repair'
                );
                if ($repair) {
                    $events->push($repair);
                }

                $spareAssigned = $this->timelineEvent(
                    $record->temporary_spare_assigned_at,
                    'Temporary spare assigned',
                    'Spare unit issued while original unit is under maintenance.',
                    'handover'
                );
                if ($spareAssigned) {
                    $events->push($spareAssigned);
                }

                $spareReturned = $this->timelineEvent(
                    $record->temporary_spare_returned_at,
                    'Temporary spare returned',
                    'Temporary spare was returned.',
                    'return'
                );
                if ($spareReturned) {
                    $events->push($spareReturned);
                }

                $finished = $this->timelineEvent(
                    $record->finish_date,
                    ($record->status === 'READY TO COLLECT') ? 'Ready to collect' : 'Repair updated',
                    'Latest maintenance status: ' . ($record->status ?: 'DONE') . '.',
                    'complete'
                );
                if ($finished) {
                    $events->push($finished);
                }
            }

            $matchedLogs = $activityLogs->filter(function (UserActivityLog $log) use ($walkie) {
                $details = strtoupper((string) $log->event_details);

                return ($walkie->radio_id && str_contains($details, strtoupper((string) $walkie->radio_id)))
                    || ($walkie->serial_number && str_contains($details, strtoupper((string) $walkie->serial_number)));
            })->take(5);

            foreach ($matchedLogs as $log) {
                $activity = $this->timelineEvent(
                    $log->created_at,
                    'System activity',
                    trim(($log->event_details ?: 'Activity recorded.') . ($log->username ? ' By ' . $log->username . '.' : '')),
                    'activity'
                );
                if ($activity) {
                    $events->push($activity);
                }
            }

            $events = $events
                ->sortBy('sort')
                ->values()
                ->map(fn ($event) => collect($event)->except('sort')->all())
                ->all();

            return [
                (string) $walkie->walkie_id => [
                    'summary' => [
                        'radio_id' => $walkie->radio_id ?: '-',
                        'serial_number' => $walkie->serial_number ?: '-',
                        'model' => $walkie->model ?: '-',
                        'status' => $walkie->status ?: 'UNKNOWN',
                        'ownership' => $walkie->ownership ?: '-',
                        'department' => $walkie->department ?: '-',
                        'location' => $walkie->location ?: '-',
                    ],
                    'events' => $events,
                ],
            ];
        })->all();
    }

    private function walkieJsonPayload(WalkieTalkie $walkie): array
    {
        return [
            'walkie_id' => $walkie->walkie_id,
            'radio_id' => $walkie->radio_id,
            'serial_number' => $walkie->serial_number,
            'model' => $walkie->model,
            'status' => $walkie->status,
            'location' => $walkie->location,
            'label' => 'SERIAL: ' . $walkie->serial_number . ' | RADIO ID: ' . $walkie->radio_id . ' | MODEL: ' . $walkie->model,
        ];
    }

    private function unusedInventoryPayload(WalkieTalkie $walkie, ?string $remark = null): array
    {
        $isSpare = strtoupper((string) $walkie->ownership_type) === 'SPARE' || (bool) $walkie->is_special_use;

        return [
            'status' => 'UNUSED',
            'ownership_type' => $isSpare ? 'SPARE' : 'UNALLOCATED',
            'shared_with' => null,
            'ownership' => '',
            'position' => '',
            'department' => '',
            'location' => '',
            'temporary_radio_id' => '',
            'remark' => $remark ?? '',
            'tracking_ref' => '',
            'need_to_change_id' => false,
            'id_change_done' => false,
            'ownership_type_to_be' => null,
            'is_special_use' => $isSpare ? (bool) $walkie->is_special_use : false,
            'special_use_returned' => false,
        ];
    }

    private function walkiePairError(Request $request, array $errors)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Radio ID or Serial Number already used.',
                'errors' => $errors,
            ], 422);
        }

        return back()->withInput()->withErrors($errors);
    }

    private function hasActiveDamageRecord(WalkieTalkie $walkie): bool
    {
        return $walkie->maintenanceRecords()
            ->whereIn('status', [
                'WAITING FOR ADMIN',
                'PENDING ADMIN IT',
                'FAULTY',
                'UNDER REPAIR',
                'REPAIRING',
                'B.E.R',
                'READY TO COLLECT',
                'ALREADY FIXED',
            ])
            ->exists();
    }

    public function index(Request $request)
    {
        $data = $this->getFormOptions();
        $data['walkieTimelines'] = $this->buildWalkieTimelineData($data['walkies']);
        $data['walkieActions'] = $data['walkies']
            ->mapWithKeys(fn ($walkie) => [
                (string) $walkie->walkie_id => [
                    'walkie_id' => $walkie->walkie_id,
                    'edit_url' => route('wt.admin.walkies.edit', ['walkie' => $walkie->walkie_id, 'source' => 'index']),
                    'delete_url' => route('wt.admin.walkies.forceDelete', $walkie->walkie_id),
                    'handover_url' => strtoupper((string) $walkie->status) === 'IN USE'
                        ? route('wt.admin.walkies.update.status', $walkie->walkie_id)
                        : null,
                    'can_manage' => true,
                ],
            ])
            ->all();

        return view('wt.admin.walkie_talkies.index', $data);
    }

    public function timeline(WalkieTalkie $walkie)
    {
        $timeline = $this->buildWalkieTimelineData(collect([$walkie]))[(string) $walkie->walkie_id] ?? [
            'summary' => [],
            'events' => [],
        ];

        $timeline['summary'] = array_merge([
            'walkie_id' => $walkie->walkie_id,
            'radio_id' => $walkie->radio_id ?: '-',
            'serial_number' => $walkie->serial_number ?: '-',
            'model' => $walkie->model ?: '-',
            'status' => $walkie->status ?: 'UNKNOWN',
            'ownership_type' => $walkie->ownership_type ?: '-',
            'ownership' => $walkie->ownership ?: '-',
            'shared_with' => $walkie->shared_with ?: '-',
            'position' => $walkie->position ?: '-',
            'department' => $walkie->department ?: '-',
            'received_date' => $walkie->received_date ?: '-',
            'repair_date' => $walkie->repair_date ?: '-',
            'temporary_radio_id' => $walkie->temporary_radio_id ?: '-',
            'tracking_ref' => $walkie->tracking_ref ?: '-',
            'remark' => $walkie->remark ?: '-',
            'need_to_change_id' => $walkie->need_to_change_id ?: '-',
            'id_change_done' => (int) ($walkie->id_change_done ?? 0) === 1 ? 'YES' : 'NO',
            'ownership_type_to_be' => $walkie->ownership_type_to_be ?: '-',
            'is_special_use' => (int) ($walkie->is_special_use ?? 0) === 1 ? 'YES' : 'NO',
            'special_use_returned' => (int) ($walkie->special_use_returned ?? 0) === 1 ? 'YES' : 'NO',
        ], $timeline['summary'] ?? []);

        return response()->json($timeline);
    }

    public function timelineFromMaintenance(MaintenanceRecord $maintenance)
    {
        $walkie = null;

        if ($maintenance->walkie_id) {
            $walkie = WalkieTalkie::find($maintenance->walkie_id);
        }

        if (! $walkie) {
            $walkie = WalkieTalkie::query()
                ->where(function ($query) use ($maintenance) {
                    if ($maintenance->radio_id) {
                        $query->orWhere('radio_id', $maintenance->radio_id);
                    }
                    if ($maintenance->serial_number) {
                        $query->orWhere('serial_number', $maintenance->serial_number);
                    }
                })
                ->first();
        }

        if ($walkie) {
            return $this->timeline($walkie);
        }

        return response()->json([
            'summary' => [
                'walkie_id' => '-',
                'radio_id' => $maintenance->radio_id ?: '-',
                'serial_number' => $maintenance->serial_number ?: '-',
                'model' => $maintenance->model ?: '-',
                'status' => $maintenance->status ?: 'UNKNOWN',
                'ownership_type' => $maintenance->ownership_type ?: '-',
                'ownership' => $maintenance->current_ownership ?: '-',
                'shared_with' => $maintenance->shared_with ?: '-',
                'position' => $maintenance->designation ?: '-',
                'department' => $maintenance->department_name ?: '-',
                'received_date' => $maintenance->received_date ?: '-',
                'repair_date' => $maintenance->repair_date ?: '-',
                'temporary_radio_id' => '-',
                'tracking_ref' => '-',
                'remark' => $maintenance->remarks ?: '-',
                'need_to_change_id' => '-',
                'id_change_done' => '-',
                'ownership_type_to_be' => '-',
                'is_special_use' => '-',
                'special_use_returned' => '-',
            ],
            'events' => collect([
                $this->timelineEvent(
                    $maintenance->received_date ?: $maintenance->maintenance_date,
                    'Maintenance reported',
                    $maintenance->issue_description ?: $maintenance->issue ?: $maintenance->remarks ?: 'Issue record created.',
                    'maintenance'
                ),
                $this->timelineEvent(
                    $maintenance->repair_date,
                    'Repair started',
                    'Status: ' . ($maintenance->status ?: 'UNDER REPAIR') . '.',
                    'repair'
                ),
                $this->timelineEvent(
                    $maintenance->finish_date,
                    'Repair updated',
                    'Latest maintenance status: ' . ($maintenance->status ?: 'DONE') . '.',
                    'complete'
                ),
            ])->filter()->sortBy('sort')->values()->map(fn ($event) => collect($event)->except('sort')->all())->all(),
        ]);
    }

    public function unused()
    {
        $records = WalkieTalkie::where('status', 'UNUSED')
            ->orderByDesc('created_at')
            ->orderByDesc('walkie_id')
            ->get();

        $columns = [
            ['key' => 'radio_id', 'label' => 'radio_id'],
            ['key' => 'serial_number', 'label' => 'serial_no'],
            ['key' => 'model', 'label' => 'model'],
            ['key' => 'status', 'label' => 'status'],
            ['key' => 'ownership_type', 'label' => 'current_ownership_type'],
            ['key' => 'shared_with', 'label' => 'shared_with'],
            ['key' => 'ownership', 'label' => 'current_ownership'],
            ['key' => 'department', 'label' => 'department'],
            ['key' => 'location', 'label' => 'location'],
            ['key' => 'position', 'label' => 'position'],
            ['key' => 'temporary_radio_id', 'label' => 'temporary_swapped_wt_radio_id'],
            ['key' => 'tracking_ref', 'label' => 'tracking_ref'],
            ['key' => 'remark', 'label' => 'remarks'],
            ['key' => 'need_to_change_id', 'label' => 'need_to_change_into'],
            ['key' => 'id_change_done', 'label' => 'done'],
            ['key' => 'ownership_type_to_be', 'label' => 'ownership_type_to_be'],
            ['key' => 'is_special_use', 'label' => 'is_special_use'],
            ['key' => 'special_use_returned', 'label' => 'returned'],
        ];

        return view('wt.admin.walkie_talkies.unused', array_merge(compact('records', 'columns'), $this->inventoryToolOptions()));
    }

    public function create()
    {
        return view('wt.admin.walkie_talkies.create', array_merge($this->getFormOptions(), [
            'pageTitle' => 'Add Walkie Talkie',
            'pageSubtitle' => 'Fill in all required fields to register a new unit.',
            'formTitle' => 'New Unit Registration',
            'formSubtitle' => 'Complete the form below and save when ready.',
            'backRoute' => route('wt.admin.walkies.index'),
            'submitLabel' => 'Save Unit',
            'returnRouteName' => 'wt.admin.walkies.index',
            'defaults' => [],
            'hiddenFields' => [],
            'formAction' => route('wt.admin.walkies.store'),
            'formMethod' => 'POST',
        ]));
    }

    public function createUnassigned()
    {
        return view('wt.admin.walkie_talkies.create', array_merge($this->getFormOptions(), [
            'pageTitle' => 'Add WT Without User',
            'pageSubtitle' => 'Register a walkie talkie as unused stock without assigning it to any user.',
            'formTitle' => 'Inventory Stock Registration',
            'formSubtitle' => 'Only unit details are needed. Status will be UNUSED and ownership will be UNALLOCATED.',
            'backRoute' => route('wt.admin.walkies.index'),
            'submitLabel' => 'Save Stock Unit',
            'returnRouteName' => 'wt.admin.walkies.index',
            'defaults' => [
                'status' => 'UNUSED',
                'ownership_type' => 'UNALLOCATED',
            ],
            'hiddenFields' => [
                'status' => 'UNUSED',
                'ownership_type' => 'UNALLOCATED',
            ],
            'inventoryOnly' => true,
            'formAction' => route('wt.admin.walkies.store'),
            'formMethod' => 'POST',
        ]));
    }

    public function createSpecialUse()
    {
        return view('wt.admin.walkie_talkies.create', array_merge($this->getFormOptions(), [
            'pageTitle' => 'Add Special Use Unit',
            'pageSubtitle' => 'Create a full page special use record without using a popup.',
            'formTitle' => 'Special Use Registration',
            'formSubtitle' => 'Register a walkie talkie that is marked for special use.',
            'backRoute' => route('wt.admin.walkies.specialUse'),
            'submitLabel' => 'Add Special Use Unit',
            'returnRouteName' => 'wt.admin.walkies.specialUse',
            'defaults' => [
                'status' => 'UNUSED',
                'ownership_type' => 'SPARE',
                'is_special_use' => '1',
                'special_use_returned' => '0',
            ],
            'hiddenFields' => [
                'is_special_use' => '1',
                'special_use_returned' => '0',
            ],
            'formAction' => route('wt.admin.walkies.store'),
            'formMethod' => 'POST',
        ]));
    }

    public function createDuplicate()
    {
        return view('wt.admin.walkie_talkies.create', array_merge($this->getFormOptions(), [
            'pageTitle' => 'Add Duplicated ID Record',
            'pageSubtitle' => 'Create a duplicate ID record in a full page form.',
            'formTitle' => 'Duplicated ID Registration',
            'formSubtitle' => 'Register a unit that needs an ID change or overlap tracking.',
            'backRoute' => route('wt.admin.walkies.duplicateIds'),
            'submitLabel' => 'Save Duplicate Record',
            'returnRouteName' => 'wt.admin.walkies.duplicateIds',
            'defaults' => [
                'need_to_change_id' => '1',
                'id_change_done' => '0',
                'status' => 'CHANGE ID',
            ],
            'hiddenFields' => [],
            'formAction' => route('wt.admin.walkies.store'),
            'formMethod' => 'POST',
        ]));
    }

    public function edit(Request $request, WalkieTalkie $walkie)
    {
        $source = $request->query('source', 'index');
        $routeMap = [
            'index' => 'wt.admin.walkies.index',
            'unused' => 'wt.admin.walkies.unused',
            'special_use' => 'wt.admin.walkies.specialUse',
            'duplicate' => 'wt.admin.walkies.duplicateIds',
        ];

        $returnRouteName = $routeMap[$source] ?? 'wt.admin.walkies.index';

        return view('wt.admin.walkie_talkies.create', array_merge($this->getFormOptions(), [
            'pageTitle' => 'Edit Walkie Talkie',
            'pageSubtitle' => 'Update the selected unit in a full page form.',
            'formTitle' => 'Update Unit Details',
            'formSubtitle' => 'Review the current unit information and save your changes.',
            'backRoute' => route($returnRouteName),
            'submitLabel' => 'Save Changes',
            'returnRouteName' => $returnRouteName,
            'defaults' => [
                'radio_id' => $walkie->radio_id,
                'serial_number' => $walkie->serial_number,
                'status' => $walkie->status,
                'ownership_type' => $walkie->ownership_type,
                'shared_with' => $walkie->shared_with,
                'model' => $walkie->model,
                'ownership' => $walkie->ownership,
                'position' => $walkie->position,
                'department' => $walkie->department,
                'location' => $walkie->location,
                'temporary_radio_id' => $walkie->temporary_radio_id,
                'tracking_ref' => $walkie->tracking_ref,
                'remark' => $walkie->remark,
                'need_to_change_id' => $walkie->need_to_change_id ? '1' : '0',
                'id_change_done' => $walkie->id_change_done ? '1' : '0',
                'ownership_type_to_be' => $walkie->ownership_type_to_be,
                'is_special_use' => $walkie->is_special_use ? '1' : '0',
                'special_use_returned' => $walkie->special_use_returned ? '1' : '0',
            ],
            'hiddenFields' => [],
            'formAction' => route('wt.admin.walkies.updateMeta', $walkie),
            'formMethod' => 'POST',
        ]));
    }

public function repairFaulty()
    {
        $records = MaintenanceRecord::with('walkieTalkie')
            ->whereIn('status', ['WAITING FOR ADMIN', 'PENDING ADMIN IT', 'FAULTY', 'UNDER REPAIR', 'REPAIRING', 'B.E.R', 'READY TO COLLECT', 'ALREADY FIXED'])
            ->orderByDesc('received_date')
            ->orderByDesc('repair_date')
            ->orderByDesc('finish_date')
            ->orderByDesc('maintenance_date')
            ->orderByDesc('maintenance_id')
            ->get();

        return view('wt.admin.repair_faulty', compact('records'));
    }

    public function duplicateIds()
    {
        $duplicateRadioIds = WalkieTalkie::select('radio_id')
            ->whereNotNull('radio_id')
            ->where('radio_id', '!=', '')
            ->groupBy('radio_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('radio_id');

        $records = WalkieTalkie::whereIn('radio_id', $duplicateRadioIds)
            ->orWhere('need_to_change_id', true)
            ->orderByDesc('created_at')
            ->orderByDesc('walkie_id')
            ->get();

        return view('wt.admin.duplicated_id', array_merge(compact('records'), $this->inventoryToolOptions()));
    }

    public function specialUse()
    {
        // Exclusively show only records manually marked as Special Use (e.g. from Special Use Excel tab)
        $records = WalkieTalkie::where('is_special_use', true)
            ->orderByDesc('created_at')
            ->orderByDesc('walkie_id')
            ->get();

        return view('wt.admin.special_use', array_merge(compact('records'), $this->inventoryToolOptions()));
    }

    public function myInventory()
    {
        TemporaryRequestExpiryService::syncExpired();
        TemporaryRequestExpiryService::syncReturnedAssignments();

        $user = Auth::guard('wt')->user();
        $viewMode = request()->query('view') === 'history' ? 'history' : 'inventory';
        $historyRetentionYears = max(1, min(5, (int) env('WT_RETURN_HISTORY_YEARS', 5)));
        $historyCutoff = now()->subYears($historyRetentionYears)->startOfDay();

        if ($user->wt_role === 'admin') {
            $records = collect();
            $historyRequests = collect();

            return view('wt.admin.walkie_talkies.my_inventory', compact('records', 'historyRequests', 'viewMode', 'historyRetentionYears'));
        }

        $candidateRequests = AccessRequest::with('user')
            ->where('status', 'Approved')
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->user_id)
                    ->orWhere('submit_to_admin_id', $user->user_id)
                    ->orWhere('full_name', $user->full_name)
                    ->orWhere('full_name', $user->username);
            })
            ->where(function ($query) {
                $query->whereNull('return_status')
                    ->orWhereNotIn('return_status', ['Pending Admin Approval', 'Pending IT Approval', 'Returned']);
            })
            ->orderByDesc('id')
            ->get();

        $activeAssignedIds = $candidateRequests
            ->flatMap(function (AccessRequest $request) {
                $assignedIds = collect($request->assigned_walkie_inventory_ids ?? []);

                if ($assignedIds->isEmpty() && $request->walkie_inventory_id) {
                    $assignedIds->push($request->walkie_inventory_id);
                }

                return $assignedIds;
            })
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $records = WalkieTalkie::where('status', 'IN USE')
            ->where(function ($query) use ($user, $activeAssignedIds) {
                $query->where('ownership', $user->full_name)
                    ->orWhere('ownership', $user->username);

                if ($activeAssignedIds->isNotEmpty()) {
                    $query->orWhereIn('walkie_id', $activeAssignedIds->all());
                }
            })
            ->orderBy('radio_id')
            ->get();

        $records->each(function (WalkieTalkie $record) use ($candidateRequests) {
            $matchedRequest = $candidateRequests->first(function (AccessRequest $request) use ($record) {
                $assignedIds = collect($request->assigned_walkie_inventory_ids ?? [])
                    ->map(fn ($id) => (int) $id)
                    ->filter()
                    ->values();
                $assignedRadioIds = collect($request->assigned_radio_ids ?? [])
                    ->map(fn ($id) => strtoupper(trim((string) $id)))
                    ->filter()
                    ->values();

                return $assignedIds->contains((int) $record->walkie_id)
                    || $assignedRadioIds->contains(strtoupper((string) $record->radio_id))
                    || (int) $request->walkie_inventory_id === (int) $record->walkie_id
                    || strtoupper((string) $request->radio_id) === strtoupper((string) $record->radio_id);
            });

            $record->active_request = $matchedRequest;
        });
        $records = $records->filter(fn (WalkieTalkie $record) => $record->active_request)->values();

        $historyRequests = AccessRequest::with(['user', 'submitToAdmin', 'handler'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->user_id)
                    ->orWhere('submit_to_admin_id', $user->user_id)
                    ->orWhere('full_name', $user->full_name)
                    ->orWhere('full_name', $user->username);
            })
            ->where('return_status', 'Returned')
            ->where(function ($query) {
                $query->whereNull('request_type')
                    ->orWhereIn('request_type', ['walkie_talkie', 'temporary_walkie_talkie']);
            })
            ->where(function ($query) use ($historyCutoff) {
                $query->where('return_date', '>=', $historyCutoff->toDateString())
                    ->orWhereNull('return_date')
                    ->orWhere(function ($missingReturnDateQuery) use ($historyCutoff) {
                        $missingReturnDateQuery->whereNull('return_date')
                            ->where('created_at', '>=', $historyCutoff);
                    });
            })
            ->orderByDesc('return_date')
            ->orderByDesc('end_date')
            ->orderByDesc('id')
            ->get();

        return view('wt.admin.walkie_talkies.my_inventory', compact('records', 'historyRequests', 'viewMode', 'historyRetentionYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'radio_id' => 'required|string|max:50',
            'status' => 'nullable|string|in:' . implode(',', self::ALLOWED_STATUSES),
            'serial_number' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'ownership_type' => 'required|string|in:' . implode(',', self::ALLOWED_OWNERSHIP_TYPES),
            'shared_with' => 'nullable|string|max:255|required_if:ownership_type,SHARED',
            'ownership' => ['nullable', 'string', 'max:255', Rule::exists('staff', 'name')],
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:50',
            'has_temporary_radio' => 'nullable|boolean',
            'temporary_radio_id' => 'nullable|string|max:100',
            'remark' => 'nullable|string|max:2000',
            'tracking_ref' => 'nullable|string|max:120',
            'need_to_change_id' => 'nullable|boolean',
            'id_change_done' => 'nullable|boolean',
            'ownership_type_to_be' => 'nullable|string|max:50',
            'is_special_use' => 'nullable|boolean',
            'special_use_returned' => 'nullable|boolean',
        ], [
            'ownership.exists' => 'Ownership name must match an existing staff record.',
        ]);

        $radioId = $this->normalizeValue($validated['radio_id']);
        $serialNumber = trim((string) $validated['serial_number']);
        $existingByRadio = WalkieTalkie::where('radio_id', $radioId)->first();
        $existingBySerial = WalkieTalkie::where('serial_number', $serialNumber)->first();

        if ($existingByRadio || $existingBySerial) {
            $errors = [];

            if ($existingByRadio) {
                $errors['radio_id'] = "Radio ID {$radioId} already used. Duplicate Radio ID is not allowed.";
            }

            if ($existingBySerial) {
                $errors['serial_number'] = "Serial number {$serialNumber} already used. Duplicate serial number is not allowed.";
            }

            return $this->walkiePairError($request, $errors);
        }

        $walkie = WalkieTalkie::create([
            'radio_id' => $radioId,
            'model' => $this->normalizeValue($validated['model']),
            'serial_number' => $serialNumber,
            'status' => $this->normalizeInventoryStatus($validated['status'] ?? null),
            'ownership_type' => $this->normalizeValue($validated['ownership_type']),
            'shared_with' => strtoupper((string) ($validated['ownership_type'] ?? '')) === 'SHARED'
                ? ($validated['shared_with'] ?? null)
                : null,
            'ownership' => $validated['ownership'] ?? '',
            'position' => $validated['position'] ?? '',
            'department' => $validated['department'] ?? '',
            'location' => $this->normalizeValue($validated['location'] ?? ''),
            'temporary_radio_id' => $validated['temporary_radio_id'] ?? '',
            'remark' => $validated['remark'] ?? '',
            'tracking_ref' => $validated['tracking_ref'] ?? '',
            'need_to_change_id' => (bool) ($validated['need_to_change_id'] ?? false),
            'id_change_done' => (bool) ($validated['id_change_done'] ?? false),
            'ownership_type_to_be' => filled($validated['ownership_type_to_be'] ?? null)
                ? $this->normalizeValue($validated['ownership_type_to_be'])
                : null,
            'is_special_use' => (bool) ($validated['is_special_use'] ?? false),
            'special_use_returned' => (bool) ($validated['special_use_returned'] ?? false),
        ]);

        if (in_array($walkie->status, ['REPAIRING', 'FAULTY', 'B.E.R'], true)) {
            MaintenanceRecord::create([
                'walkie_id' => $walkie->walkie_id,
                'radio_id' => $walkie->radio_id,
                'serial_number' => $walkie->serial_number,
                'model' => $walkie->model,
                'current_ownership' => $walkie->ownership,
                'department_name' => $walkie->department,
                'location' => $walkie->location,
                'received_date' => now()->toDateString(),
                'repair_date' => now()->toDateString(),
                'issue_description' => $walkie->remark ?: 'NO ISSUE SPECIFIED',
                'issue' => $walkie->remark ?: 'NO ISSUE SPECIFIED',
                'remarks' => $walkie->remark ?: null,
                'maintenance_date' => now()->toDateString(),
                'status' => $walkie->status
            ]);
        }

        // Log Activity
        UserActivityLog::create([
            'user_id' => Auth::guard('wt')->id(),
            'username' => Auth::guard('wt')->user()->username,
            'event_type' => 'action',
            'event_action' => 'insert',
            'event_details' => 'Added Walkie Talkie (ID: ' . $walkie->radio_id . ')',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unit ' . $walkie->radio_id . ' successfully registered!',
                'walkie' => $this->walkieJsonPayload($walkie),
            ], 201);
        }

        $returnRoute = $request->input('return_route');
        $allowedReturnRoutes = [
            'wt.admin.walkies.index',
            'wt.admin.walkies.unused',
            'wt.admin.walkies.specialUse',
            'wt.admin.walkies.duplicateIds',
        ];

        if (in_array($returnRoute, $allowedReturnRoutes, true)) {
            return redirect()->route($returnRoute)->with('success', 'Unit ' . $walkie->radio_id . ' successfully registered!');
        }

        return redirect()->route('wt.admin.walkies.index')->with('success', 'Unit ' . $walkie->radio_id . ' successfully registered!');
    }

    public function updateMeta(Request $request, WalkieTalkie $walkie)
    {
        $validated = $request->validate([
            'radio_id' => 'required|string|max:50',
            'status' => 'nullable|string|in:' . implode(',', self::ALLOWED_STATUSES),
            'serial_number' => 'required|string|max:100|unique:walkie_talkies,serial_number,' . $walkie->walkie_id . ',walkie_id',
            'model' => 'required|string|max:100',
            'ownership_type' => 'required|string|in:' . implode(',', self::ALLOWED_OWNERSHIP_TYPES),
            'shared_with' => 'nullable|string|max:255|required_if:ownership_type,SHARED',
            'ownership' => ['nullable', 'string', 'max:255', Rule::exists('staff', 'name')],
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:50',
            'has_temporary_radio' => 'nullable|boolean',
            'temporary_radio_id' => 'nullable|string|max:100',
            'remark' => 'nullable|string|max:2000',
            'tracking_ref' => 'nullable|string|max:120',
            'need_to_change_id' => 'nullable|boolean',
            'id_change_done' => 'nullable|boolean',
            'ownership_type_to_be' => 'nullable|string|max:50',
            'is_special_use' => 'nullable|boolean',
            'special_use_returned' => 'nullable|boolean',
        ], [
            'ownership.exists' => 'Ownership name must match an existing staff record.',
        ]);

        $walkie->update([
            'radio_id' => $this->normalizeValue($validated['radio_id']),
            'status' => $this->normalizeInventoryStatus($validated['status'] ?? null),
            'serial_number' => $validated['serial_number'],
            'model' => $this->normalizeValue($validated['model']),
            'ownership_type' => $this->normalizeValue($validated['ownership_type']),
            'shared_with' => strtoupper((string) ($validated['ownership_type'] ?? '')) === 'SHARED'
                ? ($validated['shared_with'] ?? null)
                : null,
            'ownership' => $validated['ownership'] ?? '',
            'position' => $validated['position'] ?? '',
            'department' => $validated['department'] ?? '',
            'location' => $this->normalizeValue($validated['location'] ?? ''),
            'temporary_radio_id' => $validated['temporary_radio_id'] ?? '',
            'remark' => $validated['remark'] ?? '',
            'tracking_ref' => $validated['tracking_ref'] ?? '',
            'need_to_change_id' => (bool) ($validated['need_to_change_id'] ?? false),
            'id_change_done' => (bool) ($validated['id_change_done'] ?? false),
            'ownership_type_to_be' => filled($validated['ownership_type_to_be'] ?? null)
                ? $this->normalizeValue($validated['ownership_type_to_be'])
                : null,
            'is_special_use' => (bool) ($validated['is_special_use'] ?? false),
            'special_use_returned' => (bool) ($validated['special_use_returned'] ?? false),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::guard('wt')->id(),
            'username' => Auth::guard('wt')->user()->username,
            'event_type' => 'action',
            'event_action' => 'update',
            'event_details' => 'Updated Walkie Talkie (ID: ' . $walkie->radio_id . ')',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        $returnRoute = $request->input('return_route');
        $allowedReturnRoutes = [
            'wt.admin.walkies.index',
            'wt.admin.walkies.unused',
            'wt.admin.walkies.specialUse',
            'wt.admin.walkies.duplicateIds',
        ];

        if (in_array($returnRoute, $allowedReturnRoutes, true)) {
            return redirect()->route($returnRoute)->with('success', 'Unit ' . $walkie->radio_id . ' updated successfully.');
        }

        return back()->with('success', 'Unit ' . $walkie->radio_id . ' updated successfully.');
    }

    public function updateReturned(Request $request, WalkieTalkie $walkie)
    {
        $validated = $request->validate([
            'special_use_returned' => 'required|boolean',
        ]);

        $walkie->update([
            'special_use_returned' => (bool) $validated['special_use_returned'],
        ]);

        UserActivityLog::create([
            'user_id' => Auth::guard('wt')->id(),
            'username' => Auth::guard('wt')->user()->username,
            'event_type' => 'action',
            'event_action' => 'update_special_return',
            'event_details' => $this->withActionRemark('Toggled special use return status for Unit ID: ' . $walkie->radio_id . ' to ' . ($walkie->special_use_returned ? 'Returned' : 'In Use'), $request),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return back()->with('success', 'Special use return status updated for Unit ' . $walkie->radio_id);
    }

    public function updateChangeDone(Request $request, WalkieTalkie $walkie)
    {
        $walkie->update(['id_change_done' => true]);

        UserActivityLog::create([
            'user_id'      => Auth::guard('wt')->id(),
            'username'     => Auth::guard('wt')->user()->username,
            'event_type'   => 'action',
            'event_action' => 'mark_change_done',
            'event_details'=> $this->withActionRemark('Marked ID change DONE for Unit ID: ' . $walkie->radio_id, $request),
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent()
        ]);

        return back()->with('success', 'Unit ' . $walkie->radio_id . ' marked as change DONE.');
    }

    public function updateStatus(Request $request, WalkieTalkie $walkie)
    {
        $validated = $request->validate([
            'id_change_done' => 'nullable|boolean',
            'need_to_change_id' => 'nullable|boolean',
            'status' => 'nullable|string|in:' . implode(',', self::ALLOWED_STATUSES),
        ]);

        $walkie->update($validated);

        if (isset($validated['status']) && in_array($validated['status'], ['ALREADY FIXED', 'READY TO COLLECT'], true)) {
            $maintenanceRecord = MaintenanceRecord::query()
                ->where(function ($query) use ($walkie) {
                    $query->where('walkie_id', $walkie->walkie_id);

                    if ($walkie->radio_id) {
                        $query->orWhere('radio_id', $walkie->radio_id);
                    }

                    if ($walkie->serial_number) {
                        $query->orWhere('serial_number', $walkie->serial_number);
                    }
                })
                ->whereNotIn('status', ['DONE', 'REJECTED', 'REFUSED'])
                ->latest('maintenance_id')
                ->first();

            $maintenanceRecord?->update([
                    'status' => $validated['status'],
                    'finish_date' => $validated['status'] === 'READY TO COLLECT' ? now()->toDateString() : null,
                    'done' => false,
                    'handled_by' => Auth::guard('wt')->id(),
                ]);
        }

        UserActivityLog::create([
            'user_id' => Auth::guard('wt')->id(),
            'username' => Auth::guard('wt')->user()->username,
            'event_type' => 'action',
            'event_action' => 'update_status',
            'event_details' => $this->withActionRemark('Updated status/ID change info for Unit ID: ' . $walkie->radio_id, $request),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return back()->with('success', 'Unit ' . $walkie->radio_id . ' status updated.');
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'integer|exists:walkie_talkies,walkie_id',
            'bulk_action' => 'required|string|in:set_status,set_unused',
            'bulk_status' => 'required_if:bulk_action,set_status|nullable|string|in:' . implode(',', self::ALLOWED_STATUSES),
            'bulk_remark' => 'nullable|string|max:1000',
        ]);

        $selectedIds = collect($validated['selected_ids'])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $walkies = WalkieTalkie::whereIn('walkie_id', $selectedIds)->get();

        if ($walkies->isEmpty()) {
            return back()->with('error', 'No units selected for bulk action.');
        }

        $remark = trim((string) ($validated['bulk_remark'] ?? ''));
        $radioIds = $walkies->pluck('radio_id')->filter()->implode(', ');

        if ($validated['bulk_action'] === 'set_unused') {
            $walkies->each(function (WalkieTalkie $walkie) use ($remark) {
                $walkie->update($this->unusedInventoryPayload($walkie, $remark));
            });

            $actionLabel = 'set unused';
            $successMessage = $walkies->count() . ' unit(s) set to unused.';
        } else {
            $status = $this->normalizeValue($validated['bulk_status'] ?? '');
            $walkies->each(function (WalkieTalkie $walkie) use ($status, $remark) {
                $payload = ['status' => $status];

                if ($remark !== '') {
                    $payload['remark'] = $remark;
                }

                $walkie->update($payload);
            });

            $actionLabel = 'set status to ' . $status;
            $successMessage = $walkies->count() . ' unit(s) updated to ' . $status . '.';
        }

        UserActivityLog::create([
            'user_id' => Auth::guard('wt')->id(),
            'username' => Auth::guard('wt')->user()->username,
            'event_type' => 'walkie_talkie',
            'event_action' => 'bulk_action',
            'event_details' => 'Bulk action: ' . $actionLabel . ' for units: ' . ($radioIds ?: $selectedIds->implode(', ')) . ($remark !== '' ? '. Remark: ' . $remark : ''),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', $successMessage);
    }

    public function destroy(WalkieTalkie $walkie)
    {
        $radioId = $walkie->radio_id;
        
        try {
            // Keep the unit record for future reuse instead of permanently deleting it.
            $walkie->update($this->unusedInventoryPayload($walkie, $this->actionRemark(request())));

            UserActivityLog::create([
                'user_id' => Auth::guard('wt')->id(),
                'username' => Auth::guard('wt')->user()->username,
                'event_type' => 'walkie_talkie',
                'event_action' => 'reset',
                'event_details' => $this->withActionRemark('Reset Walkie Talkie (ID: ' . $radioId . ') to unused', request()),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return back()->with('success', 'Unit ' . $radioId . ' reset to unused. The record was kept in inventory.');
        } catch (QueryException $e) {
            return back()->with('error', 'Unable to reset unit ' . $radioId . ' right now.');
        }
    }

    public function forceDelete(WalkieTalkie $walkie)
    {
        $radioId = $walkie->radio_id;

        try {
            $walkie->delete();

            UserActivityLog::create([
                'user_id' => Auth::guard('wt')->id(),
                'username' => Auth::guard('wt')->user()->username,
                'event_type' => 'walkie_talkie',
                'event_action' => 'delete',
                'event_details' => $this->withActionRemark('Deleted Walkie Talkie (ID: ' . $radioId . ')', request()),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return back()->with('success', 'Unit ' . $radioId . ' deleted successfully.');
        } catch (QueryException $e) {
            return back()->with('error', 'Unable to delete unit ' . $radioId . ' because it is linked to other records.');
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new WalkieTalkieImport, $request->file('file'));

            UserActivityLog::create([
                'user_id' => Auth::guard('wt')->id(),
                'username' => Auth::guard('wt')->user()->username,
                'event_type' => 'action',
                'event_action' => 'import',
                'event_details' => 'Bulk imported walkie talkies from Excel/CSV',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return back()->with('success', 'All units imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error during import: ' . $e->getMessage());
        }
    }
}
