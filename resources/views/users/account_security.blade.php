@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h2>Account Security</h2>
        <p class="page-subtitle">Manage security settings for your account</p>
    </div>
</div>

<div style="max-width:580px;">
    <div class="card">
        <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--border);">
            <strong style="font-size:.95rem;">Microsoft Authenticator (TOTP)</strong>
        </div>
        <div style="padding:1.25rem;">
            <div style="display:flex;align-items:flex-start;gap:1rem;margin-bottom:1.25rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="1.5" style="flex-shrink:0;margin-top:.1rem;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <div>
                    <div style="font-size:.9rem;font-weight:600;margin-bottom:.35rem;">Two-factor authentication for password reset</div>
                    <div style="font-size:.83rem;color:#6b7280;line-height:1.55;">
                        Once set up, you will use the 6-digit code from your Authenticator app instead of an email OTP whenever you use <strong>Forgot Password</strong>.
                        The code refreshes every 30 seconds and works even without internet access on your phone.
                    </div>
                </div>
            </div>

            @if (auth()->user()->hasTotpSetup())
                <div style="display:flex;align-items:center;gap:.75rem;padding:.9rem 1rem;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:.6rem;margin-bottom:1.25rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <div>
                        <div style="font-size:.88rem;font-weight:600;color:#15803d;">Authenticator is active</div>
                        <div style="font-size:.8rem;color:#166534;">Your account is secured. Use your app to reset your password.</div>
                    </div>
                </div>
                <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
                    <a href="{{ route('totp.setup') }}" class="btn btn-outline btn-sm">Reconfigure</a>
                    <form method="POST" action="{{ route('totp.remove') }}"
                          onsubmit="return confirm('Remove Microsoft Authenticator? You will not be able to use forgot password until you set it up again.')">
                        @csrf
                        <button type="submit" class="btn btn-sm" style="background:#fef3c7;color:#92400e;">Remove Authenticator</button>
                    </form>
                </div>
            @else
                <div style="display:flex;align-items:center;gap:.75rem;padding:.9rem 1rem;background:#fefce8;border:1px solid #fde68a;border-radius:.6rem;margin-bottom:1.25rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div>
                        <div style="font-size:.88rem;font-weight:600;color:#92400e;">Not configured</div>
                        <div style="font-size:.8rem;color:#78350f;">You cannot use self-service password reset until this is set up.</div>
                    </div>
                </div>
                <a href="{{ route('totp.setup') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-.15em;margin-right:.4rem;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Set Up Microsoft Authenticator
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
