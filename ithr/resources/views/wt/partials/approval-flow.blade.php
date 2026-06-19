@php
    $compact = $compact ?? false;
    $status = (string) ($request->status ?? '');
    $returnStatus = (string) ($request->return_status ?? '');
    $hasHandover = filled($request->handover ?? null);
    $isRejected = $status === 'Rejected';
    $isReturned = $returnStatus === 'Returned' || $status === 'Returned';
    $isReturnFlow = in_array($returnStatus, ['Pending Admin Approval', 'Pending IT Approval', 'Returned'], true);

    $steps = $isReturnFlow
        ? [
            ['key' => 'submitted', 'label' => 'Return Request'],
            ['key' => 'admin', 'label' => 'Executive Review'],
            ['key' => 'it', 'label' => 'ICT Confirm'],
            ['key' => 'complete', 'label' => 'Returned'],
        ]
        : [
            ['key' => 'submitted', 'label' => 'Submitted'],
            ['key' => 'admin', 'label' => 'Executive Review'],
            ['key' => 'it', 'label' => 'ICT Review'],
            ['key' => 'ready', 'label' => 'Ready To Collect'],
            ['key' => 'complete', 'label' => 'Completed'],
        ];

    if ($isRejected) {
        $activeKey = 'rejected';
        $doneKeys = ['submitted'];
    } elseif ($isReturnFlow) {
        $activeKey = match ($returnStatus) {
            'Pending Admin Approval' => 'admin',
            'Pending IT Approval' => 'it',
            'Returned' => 'complete',
            default => 'submitted',
        };
        $doneKeys = match ($activeKey) {
            'admin' => ['submitted'],
            'it' => ['submitted', 'admin'],
            'complete' => ['submitted', 'admin', 'it', 'complete'],
            default => [],
        };
    } else {
        $activeKey = match ($status) {
            'Draft' => 'submitted',
            'Pending Admin Approval' => 'admin',
            'Pending IT Approval' => 'it',
            'Pending Executive Pickup', 'Pending Staff Pickup' => 'ready',
            'Approved' => $hasHandover ? 'complete' : 'ready',
            default => 'submitted',
        };
        $doneKeys = match ($activeKey) {
            'admin' => ['submitted'],
            'it' => ['submitted', 'admin'],
            'ready' => ['submitted', 'admin', 'it'],
            'complete' => ['submitted', 'admin', 'it', 'ready', 'complete'],
            default => [],
        };
    }
@endphp

@once
<style>
    .approval-flow {
        display: grid;
        gap: 8px;
        width: 100%;
        min-width: 0;
    }

    .approval-flow-track {
        display: grid;
        grid-template-columns: repeat(var(--approval-flow-count), minmax(0, 1fr));
        gap: 0;
        align-items: start;
        width: 100%;
        min-width: 0;
    }

    .approval-flow-step {
        position: relative;
        display: grid;
        justify-items: center;
        gap: 6px;
        min-width: 0;
        color: #64748b;
        text-align: center;
    }

    .approval-flow-step::before {
        content: "";
        position: absolute;
        top: 9px;
        left: calc(-50% + 10px);
        width: calc(100% - 20px);
        height: 2px;
        background: #dbe3ee;
        z-index: 0;
    }

    .approval-flow-step:first-child::before {
        display: none;
    }

    .approval-flow-dot {
        position: relative;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 999px;
        border: 2px solid #dbe3ee;
        background: #ffffff;
        color: transparent;
        font-size: 8px;
        line-height: 1;
    }

    .approval-flow-label {
        max-width: 100%;
        overflow: hidden;
        color: inherit;
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .08em;
        line-height: 1.25;
        text-overflow: ellipsis;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .approval-flow-step.is-done,
    .approval-flow-step.is-active {
        color: #0f766e;
    }

    .approval-flow-step.is-done::before,
    .approval-flow-step.is-active::before {
        background: #99f6e4;
    }

    .approval-flow-step.is-done .approval-flow-dot {
        border-color: #14b8a6;
        background: #14b8a6;
        color: #ffffff;
    }

    .approval-flow-step.is-active .approval-flow-dot {
        border-color: #0ea5e9;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(14, 165, 233, .14);
    }

    .approval-flow-step.is-active .approval-flow-dot::after {
        content: "";
        width: 7px;
        height: 7px;
        border-radius: 999px;
        background: #0ea5e9;
    }

    .approval-flow.is-rejected .approval-flow-step.is-active {
        color: #b91c1c;
    }

    .approval-flow.is-rejected .approval-flow-step.is-active .approval-flow-dot {
        border-color: #ef4444;
        background: #fee2e2;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, .12);
    }

    .approval-flow.is-rejected .approval-flow-step.is-active .approval-flow-dot::after {
        background: #ef4444;
    }

    .approval-flow-compact .approval-flow-label {
        font-size: 7px;
        letter-spacing: .06em;
    }

    .approval-flow-compact .approval-flow-dot {
        width: 17px;
        height: 17px;
    }

    .approval-flow-compact .approval-flow-step::before {
        top: 8px;
    }

    .dark .approval-flow-step::before {
        background: #334155;
    }

    .dark .approval-flow-dot {
        border-color: #334155;
        background: #0f172a;
    }

    .dark .approval-flow-label {
        color: inherit;
    }

    .dark .approval-flow-step {
        color: #94a3b8;
    }

    .dark .approval-flow-step.is-done,
    .dark .approval-flow-step.is-active {
        color: #7dd3fc;
    }

    .dark .approval-flow-step.is-done::before,
    .dark .approval-flow-step.is-active::before {
        background: #0e7490;
    }

    .dark .approval-flow-step.is-active .approval-flow-dot {
        background: #0f172a;
    }

    @media (max-width: 520px) {
        .approval-flow-track {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .approval-flow-step {
            grid-template-columns: 20px minmax(0, 1fr);
            justify-items: start;
            text-align: left;
        }

        .approval-flow-step::before {
            top: -8px;
            bottom: auto;
            left: 9px;
            width: 2px;
            height: 10px;
        }

        .approval-flow-label {
            white-space: normal;
        }
    }
</style>
@endonce

<div class="approval-flow {{ $compact ? 'approval-flow-compact' : '' }} {{ $isRejected ? 'is-rejected' : '' }}" style="--approval-flow-count: {{ count($steps) + ($isRejected ? 1 : 0) }};">
    <div class="approval-flow-track">
        @foreach($steps as $step)
            @php
                $stateClass = in_array($step['key'], $doneKeys, true)
                    ? 'is-done'
                    : ($activeKey === $step['key'] ? 'is-active' : '');
            @endphp
            <div class="approval-flow-step {{ $stateClass }}">
                <span class="approval-flow-dot">{{ in_array($step['key'], $doneKeys, true) ? '✓' : '' }}</span>
                <span class="approval-flow-label">{{ $step['label'] }}</span>
            </div>
        @endforeach

        @if($isRejected)
            <div class="approval-flow-step is-active">
                <span class="approval-flow-dot"></span>
                <span class="approval-flow-label">Rejected</span>
            </div>
        @endif
    </div>
</div>

