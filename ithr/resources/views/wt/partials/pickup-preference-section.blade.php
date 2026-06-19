@php
    $pickupWhen = $request->requested_pickup_at
        ? \Carbon\Carbon::parse($request->requested_pickup_at)->format('d M Y H:i')
        : '-';
    $sectionClass = $prefix . '-section';
    $titleClass = $prefix . '-section-title';
    $gridClass = $prefix === 'damage-status' ? 'damage-status-form-grid' : $prefix . '-grid';
    $fieldClass = $prefix === 'damage-status' ? 'damage-status-detail' : $prefix . '-field';
    $fieldWideClass = $prefix === 'damage-status' ? '' : $prefix . '-field-wide';
    $labelClass = $prefix . '-label';
    $valueClass = $prefix . '-value';
@endphp

<div class="{{ $sectionClass }}">
    <div class="{{ $titleClass }}"><i class="fa-solid fa-handshake"></i> Handover / Pickup</div>
    <div class="{{ $gridClass }}">
        <div class="{{ $fieldClass }}">
            <div class="{{ $labelClass }}">Walkie Talkie For</div>
            <div class="{{ $valueClass }}">{{ strtoupper($request->full_name ?: '-') }}</div>
        </div>
        <div class="{{ $fieldClass }}">
            <div class="{{ $labelClass }}">Preferred Pickup</div>
            <div class="{{ $valueClass }}">{{ $pickupWhen }}</div>
        </div>
        <div class="{{ $fieldClass }} {{ $fieldWideClass }}">
            <div class="{{ $labelClass }}">Pickup Location</div>
            <div class="{{ $valueClass }}">ICT Department, after ICT approval</div>
        </div>
        @if($request->pickup_note)
        <div class="{{ $fieldClass }} {{ $fieldWideClass }}">
            <div class="{{ $labelClass }}">Additional Pickup Info</div>
            <div class="{{ $valueClass }} whitespace-pre-line">{{ $request->pickup_note }}</div>
        </div>
        @endif
    </div>
</div>

