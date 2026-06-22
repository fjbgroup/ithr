@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h2>Set Up Microsoft Authenticator</h2>
        <p class="page-subtitle">Secure your password reset with an authenticator app</p>
    </div>
</div>

<div style="max-width:520px;margin:0 auto;">
    <div class="card">
        <div style="padding:1.5rem;">

            @if (session('error'))
                <div style="margin-bottom:1rem;padding:.75rem;background:#fee2e2;color:#b91c1c;border-radius:.5rem;font-size:.875rem;display:flex;align-items:center;gap:.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Step 1: Scan QR --}}
            <div style="margin-bottom:1.5rem;">
                <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.75rem;">
                    <div style="width:24px;height:24px;border-radius:50%;background:#6366f1;color:#fff;font-size:.8rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">1</div>
                    <strong style="font-size:.95rem;">Scan this QR code with Microsoft Authenticator</strong>
                </div>
                <p style="font-size:.84rem;color:#6b7280;margin-bottom:1rem;padding-left:1.9rem;">
                    Open the app, tap <strong>+</strong> → <strong>Other account</strong>, then scan:
                </p>
                <div style="display:flex;justify-content:center;padding:1rem;background:#f9fafb;border:1px solid #e5e7eb;border-radius:.75rem;margin-bottom:1rem;">
                    {!! $qrSvg !!}
                </div>
            </div>

            {{-- Manual entry --}}
            <div style="margin-bottom:1.5rem;padding:.9rem;background:#f0f0ff;border:1px dashed #a5b4fc;border-radius:.75rem;">
                <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.4rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span style="font-size:.8rem;font-weight:600;color:#4f46e5;">Can't scan? Enter this code manually:</span>
                </div>
                <code style="font-size:1.05rem;letter-spacing:.25em;font-weight:700;color:#3730a3;word-break:break-all;">
                    {{ implode(' ', str_split($pendingSecret, 4)) }}
                </code>
            </div>

            {{-- Step 2: Verify --}}
            <div>
                <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.75rem;">
                    <div style="width:24px;height:24px;border-radius:50%;background:#6366f1;color:#fff;font-size:.8rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">2</div>
                    <strong style="font-size:.95rem;">Enter the 6-digit code to confirm</strong>
                </div>
                <p style="font-size:.84rem;color:#6b7280;margin-bottom:1rem;padding-left:1.9rem;">
                    After scanning, the app shows a code that refreshes every 30 seconds. Enter it below.
                </p>

                <form method="POST" action="{{ route('totp.confirm') }}" id="totpForm">
                    @csrf
                    <input type="hidden" name="totp_code" id="totp_hidden" value="">
                    <div style="display:flex;gap:.5rem;justify-content:center;margin-bottom:1rem;">
                        @for ($i = 0; $i < 6; $i++)
                            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                                   class="totp-digit" data-index="{{ $i }}" autocomplete="off"
                                   style="width:44px;height:52px;text-align:center;font-size:1.4rem;font-weight:700;border:2px solid #d1d5db;border-radius:8px;font-family:monospace;padding:0;transition:border-color .2s;">
                        @endfor
                    </div>
                    <button type="submit" class="btn btn-primary btn-full" id="totpBtn" disabled>
                        Verify &amp; Save
                    </button>
                </form>
            </div>

        </div>
    </div>

    <div style="text-align:center;margin-top:1rem;">
        <a href="{{ route('account.security') }}" style="font-size:.85rem;color:#6b7280;text-decoration:none;">← Cancel</a>
    </div>
</div>

<script>
const digits   = document.querySelectorAll('.totp-digit');
const hidden   = document.getElementById('totp_hidden');
const totpBtn  = document.getElementById('totpBtn');

digits.forEach((inp, i) => {
    inp.addEventListener('focus', () => { inp.style.borderColor = '#6366f1'; inp.style.boxShadow = '0 0 0 3px rgba(99,102,241,.15)'; });
    inp.addEventListener('blur',  () => { inp.style.borderColor = inp.value ? '#6366f1' : '#d1d5db'; inp.style.boxShadow = ''; });
    inp.addEventListener('input', e => {
        inp.value = inp.value.replace(/\D/g, '').slice(-1);
        if (inp.value && i < digits.length - 1) digits[i + 1].focus();
        sync();
    });
    inp.addEventListener('keydown', e => {
        if (e.key === 'Backspace' && !inp.value && i > 0) {
            digits[i - 1].focus();
            digits[i - 1].value = '';
            sync();
        }
    });
    inp.addEventListener('paste', e => {
        e.preventDefault();
        const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'');
        [...text.slice(0,6)].forEach((ch, j) => { if (digits[j]) digits[j].value = ch; });
        sync();
        if (digits[Math.min(text.length, 5)]) digits[Math.min(text.length, 5)].focus();
    });
});

function sync() {
    const val = [...digits].map(d => d.value).join('');
    hidden.value = val;
    totpBtn.disabled = val.length < 6;
}

digits[0]?.focus();
</script>
@endsection
