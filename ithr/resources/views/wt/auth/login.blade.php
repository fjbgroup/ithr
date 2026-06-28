<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>WT System | Sign In</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
    :root {
        --bg: #ffffff;
        --surface: #fff;
        --surface2: #f7f8fa;
        --border: #e2e5ea;
        --text: #1a1f2e;
        --muted: #6b7280;
        --accent: #38bdf8;
        --accent-h: #0284c7;
        --navy: #142b47;
        --red: #dc2626;
        --green: #16a34a;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    .hidden { display: none !important; }
    body {
        min-height: 100vh;
        background: linear-gradient(135deg, #ffffff 0%, #dcfce7 50%, #bbf7d0 100%);
        font-family: 'Inter', sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        position: relative;
        overflow-x: hidden;
    }
    body::before {
        content: '';
        position: fixed;
        inset: 0;
        background-image: radial-gradient(circle, rgba(34,197,94,0.10) 1px, transparent 1px);
        background-size: 28px 28px;
        z-index: 0;
        opacity: .6;
    }
    body::after {
        content: '';
        position: fixed;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(34,197,94,.18) 0%, transparent 65%);
        top: -200px;
        right: -200px;
        z-index: 0;
        animation: orb 10s ease-in-out infinite alternate;
    }
    @keyframes orb { to { transform: translate(-60px, 100px); } }
    .wrap { position: relative; z-index: 1; width: 100%; max-width: 480px; animation: slideUp .6s cubic-bezier(.16,1,.3,1); }
    @keyframes slideUp { from { opacity: 0; transform: translateY(32px); } to { opacity: 1; transform: translateY(0); } }
    .brand { display: flex; align-items: center; gap: 14px; justify-content: center; margin-bottom: 28px; }
    .brand-logo-box {
        width: 52px; height: 52px;
        background: linear-gradient(135deg, #142b47 0%, #254a78 100%);
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 10px 24px rgba(20,43,71,.28);
        color: white; font-size: 22px;
        transform: rotate(-3deg);
        transition: transform .3s ease;
        overflow: hidden;
    }
    .brand-logo-box:hover { transform: rotate(0deg) scale(1.05); }
    .brand-logo-box img { width: 32px; height: 32px; object-fit: contain; }
    .brand-text { font-family: 'Inter', sans-serif; font-size: 20px; font-weight: 800; color: #000; text-transform: uppercase; letter-spacing: .06em; line-height: 1.3; }
    .brand-text span { color: #000; display: block; font-size: 15px; letter-spacing: .05em; font-weight: 800; }
    .card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,.04), 0 20px 40px rgba(0,0,0,.07);
    }
    .form-body { padding: 32px 36px; }
    .form-title { font-family: 'Inter', sans-serif; font-size: 24px; font-weight: 800; color: var(--text); margin-bottom: 4px; }
    .form-sub { color: var(--muted); font-size: 13px; margin-bottom: 26px; }
    .field { margin-bottom: 18px; }
    label { display: block; font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .09em; margin-bottom: 7px; }
    .input-wrap { position: relative; display: flex; align-items: center; }
    .input-wrap i.field-icon { position: absolute; left: 14px; color: #9ca3af; font-size: 15px; pointer-events: none; z-index: 1; }
    input[type="text"], input[type="password"] {
        width: 100%;
        background: var(--surface2);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        color: var(--text);
        padding: 13px 14px 13px 42px;
        font-size: 14px;
        font-family: 'Inter', sans-serif;
        transition: border-color .2s, box-shadow .2s;
        outline: none;
    }
    input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(56,189,248,.15); background: #fff; }
    input::placeholder { color: #bfc5cc; }
    .toggle-pass {
        position: absolute;
        right: 14px;
        color: var(--muted);
        cursor: pointer;
        padding: 4px;
        transition: color .3s;
        background: none;
        border: none;
        font-size: 16px;
    }
    .toggle-pass:hover { color: var(--accent-h); }
    .forgot-link { display: block; text-align: right; margin-top: 8px; font-size: 12px; color: var(--accent); text-decoration: none; font-weight: 600; letter-spacing: .01em; }
    .forgot-link:hover { color: var(--accent-h); }
    .btn-submit {
        width: 100%;
        margin-top: 6px;
        background: var(--navy);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 14px;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: .04em;
        cursor: pointer;
        transition: background .2s, transform .1s, box-shadow .2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(20,43,71,.25);
    }
    .btn-submit:hover { background: var(--accent-h); box-shadow: 0 6px 20px rgba(20,43,71,.3); transform: translateY(-1px); }
    .alert { border-radius: 10px; padding: 12px 16px; font-size: 13px; display: flex; align-items: center; gap: 9px; margin-bottom: 18px; font-weight: 500; }
    .alert-error { background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; }
    .alert-success { background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; }
    .divider { text-align: center; padding: 16px 36px; border-top: 1px solid var(--border); font-size: 11px; color: #94a3b8; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; }
    .footer-links { text-align: center; margin-top: 20px; font-size: 12px; font-weight: 600; color: var(--muted); }
    .footer-links a { color: var(--accent); text-decoration: none; transition: color .2s; }
    .footer-links a:hover { color: var(--accent-h); }

    /* Theme toggle */
    .theme-toggle-floating {
        position: fixed; top: 24px; right: 24px;
        width: 44px; height: 44px;
        background: rgba(255,255,255,.9);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(226,232,240,.8);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        color: var(--muted);
        box-shadow: 0 4px 14px rgba(0,0,0,.08);
        transition: all .3s;
        z-index: 100;
        font-size: 18px;
    }
    .theme-toggle-floating:hover { transform: translateY(-2px); color: var(--accent-h); box-shadow: 0 8px 24px rgba(0,0,0,.12); }

    /* Modal */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.4); backdrop-filter: blur(12px); z-index: 1000; align-items: center; justify-content: center; padding: 20px; }
    .modal-overlay.active { display: flex; }
    .modal-box { background: var(--surface); width: 100%; max-width: 420px; padding: 32px; border-radius: 20px; border: 1px solid var(--border); box-shadow: 0 30px 60px rgba(0,0,0,.18); animation: popUp .35s cubic-bezier(.16,1,.3,1); }
    @keyframes popUp { from { opacity: 0; transform: scale(.92) translateY(16px); } to { opacity: 1; transform: scale(1) translateY(0); } }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-header h3 { font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: .1em; color: var(--navy); }
    .modal-close { cursor: pointer; color: var(--muted); font-size: 20px; background: none; border: none; transition: color .2s; }
    .modal-close:hover { color: #ef4444; }
    .modal-desc { font-size: 12px; color: var(--muted); margin-bottom: 20px; line-height: 1.6; }
    .modal-field { margin-bottom: 16px; }
    .modal-field label { display: block; font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .09em; margin-bottom: 6px; }
    .modal-field input, .modal-field textarea {
        width: 100%; background: var(--surface2); border: 1.5px solid var(--border); border-radius: 8px;
        padding: 10px 12px; font-size: 13px; font-family: 'Inter', sans-serif; color: var(--text); outline: none; transition: all .2s;
    }
    .modal-field textarea { min-height: 90px; resize: vertical; }
    .modal-field input:focus, .modal-field textarea:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(56,189,248,.15); }
    .modal-btn { width: 100%; background: var(--navy); color: #fff; border: none; border-radius: 8px; padding: 12px; font-family: 'Inter', sans-serif; font-size: 13px; font-weight: 700; cursor: pointer; transition: background .2s; margin-top: 8px; }
    .modal-btn:hover { background: var(--accent-h); }

    @media (max-width: 480px) {
        .form-body { padding: 24px 20px; }
        .divider { padding: 12px 20px; }
    }
    </style>
</head>
<body>
    {{-- Floating Theme Toggle --}}
    <div class="theme-toggle-floating" id="theme-toggle" title="Toggle Dark/Light Mode">
        <i id="theme-toggle-dark-icon" class="hidden bi bi-moon-fill"></i>
        <i id="theme-toggle-light-icon" class="hidden bi bi-sun-fill"></i>
    </div>

    <div class="wrap">
        <div class="brand">
            <div class="brand-logo-box">
                <img src="{{ asset('assets/images/fjb-logo.svg') }}" alt="FJB" onerror="this.style.display='none';this.parentNode.innerHTML='<i class=\'fas fa-walkie-talkie\' style=\'font-size:22px;color:#fff\'></i>'">
            </div>
            <div class="brand-text">WT System<span>Walkie Talkie Management</span></div>
        </div>

        <div class="card">
            <div class="form-body">
                <div class="form-title">Welcome Back</div>
                <div class="form-sub">Sign in to access the WT System portal</div>

                @if(session('error'))
                    <div class="alert alert-error"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
                @endif
                @if(isset($errors) && is_object($errors) && $errors->any())
                    <div class="alert alert-error"><i class="bi bi-exclamation-triangle-fill"></i> {{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('wt.login') }}">
                    @csrf
                    <div class="field">
                        <label>Staff No.</label>
                        <div class="input-wrap">
                            <i class="bi bi-person field-icon"></i>
                            <input type="text" name="staff_no" placeholder="Enter your Staff No." value="{{ old('staff_no') }}" autocapitalize="off" autocomplete="off" spellcheck="false" required autofocus data-preserve-case="true">
                        </div>
                    </div>

                    <div class="field">
                        <label>Password</label>
                        <div class="input-wrap">
                            <i class="bi bi-lock field-icon"></i>
                            <input type="password" name="password" id="pass" placeholder="Enter your password" required>
                            <button type="button" class="toggle-pass" onclick="togglePassword('pass', this)" title="Show/hide password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <a href="#" class="forgot-link" onclick="openForgotModal(); return false;">
                            <i class="bi bi-key" style="font-size:11px"></i> Forgot Password?
                        </a>
                    </div>

                    <button type="submit" class="btn-submit">
                        Sign In <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </div>
            <div class="divider">FGV Johor Bulkers Sdn Bhd</div>
        </div>

        <div class="footer-links" style="margin-top:20px">
            <a href="{{ url('/') }}" style="color:#94a3b8" onmouseover="this.style.color='#0284c7'" onmouseout="this.style.color='#94a3b8'">
                &larr; Back to Portal
            </a>
        </div>
    </div>

    {{-- Reset Password Modal --}}
    <div id="forgotModal" class="modal-overlay" onclick="closeOutside(event,'forgotModal')">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Reset Request</h3>
                <button type="button" class="modal-close" onclick="closeForgotModal()"><i class="bi bi-x-lg"></i></button>
            </div>
            <form method="POST" action="{{ route('wt.password.reset') }}">
                @csrf
                <p class="modal-desc">
                    Submit your details. Your request will be forwarded to <strong>ICT</strong> for review. ICT will handle the password reset for your account.
                </p>
                <div class="modal-field">
                    <label>Name</label>
                    <input type="text" name="requester_name" placeholder="Enter your name" value="{{ old('requester_name') }}" required>
                </div>
                <div class="modal-field">
                    <label>Staff ID</label>
                    <input type="text" name="staff_id" placeholder="Enter your Staff ID" value="{{ old('staff_id') }}" required>
                </div>
                <div class="modal-field">
                    <label>Justification</label>
                    <textarea name="justification" placeholder="State the reason for password reset request" required>{{ old('justification') }}</textarea>
                </div>
                <button type="submit" class="modal-btn">Send Request</button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(id, btn) {
            const f = document.getElementById(id);
            const icon = btn.querySelector('i');
            if (f.type === 'password') {
                f.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                f.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
        function openForgotModal()  { document.getElementById('forgotModal').classList.add('active'); }
        function closeForgotModal() { document.getElementById('forgotModal').classList.remove('active'); }
        function closeOutside(e, id) { if (e.target === document.getElementById(id)) closeForgotModal(); }
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeForgotModal(); });

        // Theme toggle (syncs with fjb-theme key used by main layouts)
        const themeToggleBtn = document.getElementById('theme-toggle');
        const darkIcon = document.getElementById('theme-toggle-dark-icon');
        const lightIcon = document.getElementById('theme-toggle-light-icon');

        function applyLoginTheme(dark) {
            document.documentElement.classList.toggle('dark', dark);
            document.documentElement.dataset.theme = dark ? 'dark' : 'light';
            if (darkIcon && lightIcon) {
                darkIcon.style.display = dark ? 'none' : 'inline-block';
                lightIcon.style.display = dark ? 'inline-block' : 'none';
            }
        }

        (function(){
            const t = localStorage.getItem('fjb-theme') || localStorage.getItem('color-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            applyLoginTheme(t === 'dark');
        })();

        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', function() {
                const isDark = document.documentElement.classList.contains('dark');
                const next = isDark ? 'light' : 'dark';
                localStorage.setItem('fjb-theme', next);
                localStorage.setItem('color-theme', next);
                applyLoginTheme(next === 'dark');
            });
        }
    </script>
</body>
</html>
