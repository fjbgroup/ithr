@php
    $isAdminRoute = request()->routeIs('wt.admin.*');
    $layout = $isAdminRoute ? 'wt.layouts.admin' : 'wt.layouts.user';
    $routePrefix = $routePrefix ?? ($isAdminRoute ? 'wt.admin' : 'wt.user');
    $currentUser = auth('wt')->user();
    $recipientName = strtoupper($accessRequest->full_name ?: ($currentUser->full_name ?: $currentUser->username));
    $handoverByName = strtoupper(optional($accessRequest->handler)->full_name ?: optional($accessRequest->handler)->username ?: 'ICT');
    $accessories = collect(explode(',', (string) $accessRequest->accessories))
        ->map(fn ($item) => trim($item))
        ->filter()
        ->values();
@endphp

@extends($layout)

@section('title', 'Pickup WT')
@section('page_title', 'Pickup WT')

@push('styles')
<style>
    .pickup-shell{display:grid;gap:16px}
    .pickup-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:18px;box-shadow:0 1px 3px rgba(0,0,0,.06)}
    .pickup-title{font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.14em;color:var(--muted);margin:0 0 12px}
    .pickup-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px}
    .pickup-item{border:1px solid var(--border);border-radius:9px;background:var(--body-bg);padding:10px 12px}
    .pickup-label{font-size:8px;font-weight:900;text-transform:uppercase;letter-spacing:.12em;color:var(--muted);margin-bottom:4px}
    .pickup-value{font-size:12px;font-weight:800;color:var(--text);overflow-wrap:anywhere}
    .pickup-accessory-list{display:flex;flex-wrap:wrap;gap:8px}
    .pickup-accessory{display:inline-flex;align-items:center;gap:6px;border:1px solid rgba(22,163,74,.25);background:rgba(22,163,74,.08);color:#15803d;border-radius:999px;padding:7px 10px;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.06em}
    .signature-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
    .signature-box{border:1px solid var(--border);border-radius:12px;overflow:hidden;background:var(--surface)}
    .signature-head{display:flex;align-items:center;justify-content:space-between;gap:10px;border-bottom:1px solid var(--border);background:var(--body-bg);padding:10px 12px}
    .signature-head-title{font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.12em;color:var(--text)}
    .signature-name{border:1px solid var(--border);border-radius:8px;background:var(--surface);color:var(--text);padding:9px 10px;width:100%;font-size:12px;font-weight:800;text-transform:uppercase}
    .signature-source{display:flex;flex-wrap:wrap;gap:8px;padding:10px 12px;border-bottom:1px solid var(--border)}
    .signature-source label{display:inline-flex;align-items:center;gap:6px;font-size:9px;font-weight:900;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)}
    .signature-pad{height:170px;background:#fff;position:relative}
    .signature-pad canvas{display:block;width:100%;height:170px;touch-action:none;background:#fff}
    .signature-preview{display:none;height:170px;align-items:center;justify-content:center;background:#fff;padding:10px}
    .signature-preview img{max-width:100%;max-height:150px;object-fit:contain}
    .signature-actions{display:flex;align-items:center;justify-content:space-between;gap:10px;border-top:1px solid var(--border);background:var(--body-bg);padding:9px 12px}
    .signature-clear{border:1px solid var(--border);border-radius:7px;background:var(--surface);color:var(--text);font-size:9px;font-weight:900;text-transform:uppercase;letter-spacing:.1em;padding:7px 11px}
    .signature-upload{display:none;padding:10px 12px;border-bottom:1px solid var(--border)}
    @media (max-width: 900px){.signature-grid{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="pickup-shell">
    @if($errors->any())
    <div class="alert-danger-custom"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
    @endif

    <div>
        <div style="font-size:16px;font-weight:900;color:var(--text)">Pickup Handover</div>
        <p style="margin-top:4px;font-size:12px;color:var(--muted)">Request #{{ str_pad($accessRequest->id, 5, '0', STR_PAD_LEFT) }} is ready for signed pickup.</p>
    </div>

    <div class="pickup-card">
        <p class="pickup-title">WT To Be Received</p>
        <div class="pickup-grid">
            <div class="pickup-item"><div class="pickup-label">Owner</div><div class="pickup-value">{{ $recipientName }}</div></div>
            <div class="pickup-item"><div class="pickup-label">Department</div><div class="pickup-value">{{ strtoupper($accessRequest->department ?: '-') }}</div></div>
            <div class="pickup-item"><div class="pickup-label">Position</div><div class="pickup-value">{{ strtoupper($accessRequest->position ?: '-') }}</div></div>
            <div class="pickup-item"><div class="pickup-label">Approved By ICT</div><div class="pickup-value">{{ $handoverByName }}</div></div>
        </div>

        <div style="margin-top:14px" class="pickup-grid">
            @forelse($assignedWalkies as $walkie)
            <div class="pickup-item">
                <div class="pickup-label">Walkie Talkie</div>
                <div class="pickup-value">Radio ID: {{ $walkie->radio_id ?: '-' }}</div>
                <div class="pickup-value" style="font-size:11px;color:var(--muted)">Serial: {{ $walkie->serial_number ?: '-' }} | Model: {{ $walkie->model ?: '-' }}</div>
            </div>
            @empty
            <div class="pickup-item"><div class="pickup-value">No assigned WT unit found.</div></div>
            @endforelse
        </div>
    </div>

    <div class="pickup-card">
        <p class="pickup-title">Accessories Prepared By ICT</p>
        @if($accessories->isNotEmpty())
        <div class="pickup-accessory-list">
            @foreach($accessories as $accessory)
            <span class="pickup-accessory"><i class="fa-solid fa-check"></i>{{ $accessory }}</span>
            @endforeach
        </div>
        @else
        <div class="pickup-item"><div class="pickup-value">No accessories listed by ICT.</div></div>
        @endif
    </div>

    <div class="pickup-card policy-note-card">
        <p class="pickup-title">WT Policy</p>
        @include('wt.partials.walkie-policy-content', ['policyContent' => $policyContent])
    </div>

    <form action="{{ route($routePrefix . '.handover.pickup.store', $accessRequest->id) }}" method="POST" enctype="multipart/form-data" class="pickup-card" data-pickup-signature-form>
        @csrf
        <p class="pickup-title">Pickup Signatures</p>
        <div class="signature-grid">
            @include('wt.user.handover.partials.signature-box', [
                'boxId' => 'recipient',
                'title' => 'Owner / Receiver Signature',
                'nameField' => 'pickup_recipient_name',
                'signatureField' => 'pickup_recipient_signature',
                'sourceField' => 'pickup_recipient_signature_source',
                'defaultName' => $recipientName,
                'savedSignatureUrl' => $savedSignatureUrl,
            ])

            @include('wt.user.handover.partials.signature-box', [
                'boxId' => 'handover_by',
                'title' => 'ICT / Handover By Signature',
                'nameField' => 'handover_by_name',
                'signatureField' => 'handover_by_signature',
                'sourceField' => 'handover_by_signature_source',
                'defaultName' => $handoverByName,
                'savedSignatureUrl' => $savedSignatureUrl,
            ])
        </div>

        <label style="display:flex;align-items:flex-start;gap:10px;margin-top:16px;font-size:11px;font-weight:800;color:var(--text)">
            <input type="checkbox" name="policy_acceptance" value="1" required style="margin-top:2px">
            <span>I confirm the WT, listed accessories, and WT policy have been reviewed during pickup.</span>
        </label>

        <div style="display:flex;justify-content:flex-end;margin-top:18px">
            <button type="submit" class="btn-primary-custom" style="background:#16a34a">
                <i class="fa-solid fa-check"></i> Save Pickup Signature
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function resizeCanvas(canvas) {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * ratio;
        canvas.height = rect.height * ratio;
        const ctx = canvas.getContext('2d');
        ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#111827';
    }

    function setupBox(box) {
        const canvas = box.querySelector('canvas');
        const input = box.querySelector('[data-signature-input]');
        const sourceInputs = box.querySelectorAll('[data-signature-source]');
        const clearButton = box.querySelector('[data-signature-clear]');
        const uploadInput = box.querySelector('[data-signature-upload]');
        const uploadPanel = box.querySelector('[data-upload-panel]');
        const preview = box.querySelector('[data-signature-preview]');
        const previewImg = preview ? preview.querySelector('img') : null;
        const savedUrl = box.dataset.savedSignatureUrl || '';
        let drawing = false;
        let hasInk = false;

        resizeCanvas(canvas);

        function selectedSource() {
            const checked = Array.from(sourceInputs).find((item) => item.checked);
            return checked ? checked.value : 'draw';
        }

        function setMode() {
            const mode = selectedSource();
            box.querySelector('[data-signature-pad]').style.display = mode === 'draw' ? 'block' : 'none';
            uploadPanel.style.display = mode === 'upload' ? 'block' : 'none';
            preview.style.display = mode === 'saved' || mode === 'upload' ? 'flex' : 'none';
            if (mode === 'saved') {
                if (previewImg) previewImg.src = savedUrl;
                input.value = '';
            } else if (mode === 'draw') {
                input.value = hasInk ? canvas.toDataURL('image/png') : '';
            }
        }

        function point(event) {
            const rect = canvas.getBoundingClientRect();
            const touch = event.touches && event.touches[0] ? event.touches[0] : event;
            return { x: touch.clientX - rect.left, y: touch.clientY - rect.top };
        }

        function start(event) {
            if (selectedSource() !== 'draw') return;
            event.preventDefault();
            drawing = true;
            const ctx = canvas.getContext('2d');
            const p = point(event);
            ctx.beginPath();
            ctx.moveTo(p.x, p.y);
        }

        function move(event) {
            if (!drawing || selectedSource() !== 'draw') return;
            event.preventDefault();
            const ctx = canvas.getContext('2d');
            const p = point(event);
            ctx.lineTo(p.x, p.y);
            ctx.stroke();
            hasInk = true;
            input.value = canvas.toDataURL('image/png');
        }

        function end() {
            drawing = false;
            if (hasInk && selectedSource() === 'draw') input.value = canvas.toDataURL('image/png');
        }

        canvas.addEventListener('mousedown', start);
        canvas.addEventListener('mousemove', move);
        document.addEventListener('mouseup', end);
        canvas.addEventListener('touchstart', start, { passive: false });
        canvas.addEventListener('touchmove', move, { passive: false });
        canvas.addEventListener('touchend', end);

        clearButton.addEventListener('click', function () {
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasInk = false;
            input.value = '';
            if (uploadInput) uploadInput.value = '';
            if (previewImg) previewImg.removeAttribute('src');
        });

        uploadInput.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (event) {
                input.value = event.target.result;
                if (previewImg) previewImg.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });

        sourceInputs.forEach((source) => source.addEventListener('change', setMode));
        setMode();
    }

    document.querySelectorAll('[data-signature-box]').forEach(setupBox);
});
</script>
@endpush
