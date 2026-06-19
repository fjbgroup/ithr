<?php

namespace App\Services;

use App\Models\WT\AccessRequest;
use App\Models\WT\WalkieTalkie;

class TemporaryRequestExpiryService
{
    /**
     * Mark approved temporary requests whose end_date has passed as Expired
     * and free up the assigned walkie talkies.
     */
    public static function syncExpired(): void
    {
        $expired = AccessRequest::where('request_type', 'temporary_walkie_talkie')
            ->where('status', 'Approved')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<', now()->toDateString())
            ->get();

        foreach ($expired as $request) {
            $request->status = 'Expired';
            $request->save();

            static::freeWalkies($request);
        }
    }

    /**
     * Ensure walkie talkies are freed for requests already marked as Returned
     * but whose inventory records still show them as assigned.
     */
    public static function syncReturnedAssignments(): void
    {
        $returned = AccessRequest::where('return_status', 'Returned')
            ->where('status', 'Approved')
            ->get();

        foreach ($returned as $request) {
            static::freeWalkies($request);
        }
    }

    private static function freeWalkies(AccessRequest $request): void
    {
        $ids = collect($request->assigned_walkie_inventory_ids ?? [])
            ->filter()
            ->map(fn ($id) => (int) $id);

        if ($ids->isEmpty() && $request->walkie_inventory_id) {
            $ids = collect([(int) $request->walkie_inventory_id]);
        }

        if ($ids->isEmpty()) {
            return;
        }

        WalkieTalkie::whereIn('walkie_id', $ids)->get()->each(function (WalkieTalkie $walkie) {
            $isSpare = in_array(strtoupper((string) $walkie->ownership_type), ['SPARE'], true)
                || strtoupper((string) $walkie->status) === 'SPARE'
                || (bool) $walkie->is_special_use;

            $walkie->update([
                'status'               => 'UNUSED',
                'ownership_type'       => $isSpare ? 'SPARE' : 'UNALLOCATED',
                'shared_with'          => null,
                'ownership'            => '',
                'position'             => '',
                'department'           => '',
                'temporary_radio_id'   => '',
                'remark'               => '',
                'tracking_ref'         => '',
                'need_to_change_id'    => false,
                'id_change_done'       => false,
                'ownership_type_to_be' => null,
                'is_special_use'       => $isSpare ? (bool) $walkie->is_special_use : false,
                'special_use_returned' => false,
            ]);
        });
    }
}
