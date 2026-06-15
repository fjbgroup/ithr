@php
    $prefix = $prefix ?? 'request-form';
    $sectionClass = "{$prefix}-section";
    $titleClass = "{$prefix}-section-title";
    $gridClass = $prefix === 'damage-status' ? 'damage-status-form-grid' : "{$prefix}-grid";
    $fieldClass = $prefix === 'damage-status' ? 'damage-status-detail' : "{$prefix}-field";
    $wideFieldClass = $prefix === 'damage-status' ? $fieldClass : "{$fieldClass} {$fieldClass}-wide";
    $labelClass = "{$prefix}-label";
    $valueClass = "{$prefix}-value";
    $ictUpdateStatus = match ($request->status) {
        'Draft' => 'DRAFT ONLY',
        'Pending Admin Approval' => 'PENDING EXECUTIVE REVIEW',
        'Pending IT Approval' => 'PENDING ICT REVIEW',
        'Approved' => 'ICT APPROVED / READY TO COLLECT',
        'Rejected' => 'REJECTED BY ICT',
        default => strtoupper((string) ($request->status ?: 'UNKNOWN')),
    };
    $ictHandler = strtoupper((string) (($request->handler->full_name ?? null) ?: ($request->handler->username ?? '-')));
@endphp

<div class="{{ $sectionClass }}">
    <div class="{{ $titleClass }}"><i class="fa-solid fa-screwdriver-wrench"></i> ICT Update</div>
    <div class="{{ $gridClass }}">
        <div class="{{ $fieldClass }}">
            <div class="{{ $labelClass }}">ICT Status</div>
            <div class="{{ $valueClass }}">{{ $ictUpdateStatus }}</div>
        </div>
        <div class="{{ $fieldClass }}">
            <div class="{{ $labelClass }}">Handled By ICT</div>
            <div class="{{ $valueClass }}">{{ $ictHandler }}</div>
        </div>
        <div class="{{ $fieldClass }}">
            <div class="{{ $labelClass }}">Assigned Radio ID</div>
            <div class="{{ $valueClass }}">{{ strtoupper($request->radio_id ?: '-') }}</div>
        </div>
        <div class="{{ $fieldClass }}">
            <div class="{{ $labelClass }}">Assigned Serial</div>
            <div class="{{ $valueClass }}">{{ strtoupper($request->assigned_serial_number ?: '-') }}</div>
        </div>
        <div class="{{ $wideFieldClass }}">
            <div class="{{ $labelClass }}">ICT Remark / Update</div>
            <div class="{{ $valueClass }} whitespace-pre-line">{{ $request->approval_remark ?: 'No ICT update yet.' }}</div>
        </div>
    </div>
</div>

