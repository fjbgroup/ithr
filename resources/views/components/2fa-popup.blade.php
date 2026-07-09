@if(session('prompt_2fa_setup') && Auth::check() && empty(Auth::user()->totp_secret))
<!-- 2FA Setup Prompt Modal -->
<div id="modal-2fa-prompt" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(15,23,42,0.75);z-index:99999;display:flex;align-items:center;justify-content:center;opacity:1;transition:opacity 0.3s;backdrop-filter:blur(4px);">
    <div style="background:var(--surface,#fff);border-radius:12px;width:100%;max-width:420px;padding:2.5rem 2rem;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);position:relative;transform:translateY(0);transition:transform 0.3s;">
        <div style="display:flex;justify-content:center;margin-bottom:1.5rem;">
            <div style="width:72px;height:72px;border-radius:50%;background:#e0e7ff;display:flex;align-items:center;justify-content:center;color:#4f46e5;">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
            </div>
        </div>
        <h3 style="margin:0 0 .75rem 0;font-size:1.35rem;font-weight:800;color:var(--text,#0f172a);text-align:center;font-family:'Inter',sans-serif;line-height:1.3;">Protect Your Account</h3>
        <p style="margin:0 0 1.75rem 0;font-size:.95rem;color:var(--muted,#64748b);text-align:center;line-height:1.6;">
            Enhance your account security by setting up Microsoft Authenticator. It only takes a minute and provides an extra layer of protection.
        </p>
        <div style="display:flex;flex-direction:column;gap:.75rem;">
            <a href="{{ route('user.setup-2fa') }}" style="display:inline-flex;align-items:center;justify-content:center;background:#4f46e5;color:#fff;font-weight:600;font-size:.95rem;padding:.85rem 1.5rem;border-radius:8px;text-decoration:none;transition:background 0.2s;box-shadow:0 4px 6px -1px rgba(79, 70, 229, 0.2);">
                Activate now
            </a>
            <button onclick="dismiss2faPrompt()" style="display:inline-flex;align-items:center;justify-content:center;background:transparent;border:1px solid var(--border,#cbd5e1);color:var(--text,#475569);font-weight:600;font-size:.95rem;padding:.85rem 1.5rem;border-radius:8px;cursor:pointer;transition:all 0.2s;">
                Do it later
            </button>
        </div>
    </div>
</div>

<script>
    // Clear the session flag immediately so the popup only shows once per login session
    @php
        session()->forget('prompt_2fa_setup');
    @endphp

    function dismiss2faPrompt() {
        var modal = document.getElementById('modal-2fa-prompt');
        if (modal) {
            modal.style.opacity = '0';
            modal.children[0].style.transform = 'translateY(20px)';
            setTimeout(function() { modal.remove(); }, 300);
        }
    }
</script>
@endif
