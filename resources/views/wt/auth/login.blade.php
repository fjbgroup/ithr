<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
<title>WT System | Sign In</title>
@include('partials.favicons')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --brand: #ee1c25;
            --brand-dark: #9f1118;
            --brand-deep: #6f0d14;
            --bg: #eef3f7;
            --text: #121a2d;
            --muted: #6c7a91;
            --line: #dce4ef;
            --field: #f8fafc;
            --white: #ffffff;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
        }

        .login-shell {
            width: 100%;
            max-width: 940px;
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            background: var(--white);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 30px 70px -20px rgba(8,20,40,.55), 0 2px 8px rgba(8,20,40,.2);
        }

        .brand-panel {
            position: relative;
            padding: 3rem 2.75rem;
            background:
                radial-gradient(circle at 82% 20%, rgba(255, 255, 255, .12), transparent 34%),
                linear-gradient(135deg, #ee1c25 0%, #c91821 58%, #8f1118 100%);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand-logo {
            width: 56px;
            height: 56px;
            object-fit: contain;
            margin-bottom: 32px;
        }

        .brand-title {
            max-width: 390px;
            margin: 0;
            font-size: 1.9rem;
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: 0;
        }

        .brand-copy {
            max-width: 430px;
            margin: 12px 0 0;
            color: rgba(255,255,255,.70);
            font-size: 13px;
            line-height: 1.6;
            font-weight: 600;
        }

        .feature-list {
            display: grid;
            gap: 14px;
            margin-top: 44px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
            color: rgba(255,255,255,.82);
            font-size: 13px;
            font-weight: 700;
        }

        .feature-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,.13);
            color: #ffffff;
            flex: 0 0 auto;
        }

        .form-panel {
            padding: 3rem 2.75rem;
            display: flex;
            align-items: center;
        }

        .form-card {
            width: 100%;
            max-width: none;
            margin: 0 auto;
        }

        .form-title {
            margin: 0;
            font-size: 26px;
            line-height: 1.15;
            font-weight: 900;
            letter-spacing: 0;
            color: #101827;
        }

        .form-subtitle {
            margin: 4px 0 28px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 500;
        }

        .alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
        }

        .alert-error {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #9f1239;
        }

        .alert-success {
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            margin: 0 0 9px;
            color: #404a60;
            font-size: 13px;
            font-weight: 800;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrap .field-icon {
            position: absolute;
            left: 16px;
            color: #94a3b8;
            font-size: 16px;
            pointer-events: none;
        }

        .login-input {
            width: 100%;
            min-height: 46px;
            border: 1.5px solid var(--line);
            border-radius: 11px;
            background: var(--field);
            color: var(--text);
            padding: 0 46px 0 42px;
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            outline: none;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .login-input::placeholder { color: #94a3b8; font-weight: 600; }

        .login-input:focus {
            background: #ffffff;
            border-color: var(--brand);
            box-shadow: 0 0 0 4px rgba(238, 28, 37, .13);
        }

        .toggle-pass {
            position: absolute;
            right: 14px;
            width: 30px;
            height: 30px;
            border: 0;
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 17px;
        }

        .toggle-pass:hover { color: var(--brand); background: #fff1f2; }

        .forgot-row {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
        }

        .forgot-link {
            color: var(--brand-dark);
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
        }

        .forgot-link:hover { color: var(--brand); }

        .btn-submit {
            width: 100%;
            min-height: 48px;
            margin-top: 4px;
            border: 0;
            border-radius: 10px;
            background: var(--brand);
            color: #ffffff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 900;
            letter-spacing: .01em;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            box-shadow: 0 13px 24px rgba(238, 28, 37, .32);
            transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            filter: brightness(.97);
            box-shadow: 0 16px 28px rgba(238, 28, 37, .28);
        }

        .login-links {
            margin-top: 24px;
            text-align: center;
        }

        .back-link {
            color: #7b8aa2;
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
        }

        .back-link:hover { color: var(--brand); }

        .switch-title {
            margin-top: 20px;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .system-switch {
            margin-top: 10px;
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            color: #94a3b8;
            font-size: 14px;
            font-weight: 800;
        }

        .system-switch a,
        .system-switch span {
            color: #6b7890;
            text-decoration: none;
        }

        .system-switch a:hover { color: var(--brand); }
        .system-switch .active { color: var(--brand-dark); }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 50;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(15, 23, 42, .48);
            backdrop-filter: blur(10px);
        }

        .modal-overlay.active { display: flex; }

        .modal-box {
            width: min(430px, 100%);
            border-radius: 18px;
            background: #ffffff;
            border: 1px solid var(--line);
            box-shadow: 0 30px 70px rgba(15, 23, 42, .28);
            padding: 28px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }

        .modal-header h3 {
            margin: 0;
            color: #101827;
            font-size: 18px;
            font-weight: 900;
        }

        .modal-close {
            width: 34px;
            height: 34px;
            border: 1px solid var(--line);
            border-radius: 9px;
            background: #ffffff;
            color: #64748b;
            cursor: pointer;
        }

        .modal-close:hover { color: var(--brand); border-color: #fecdd3; }

        .modal-desc {
            margin: 0 0 18px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
            font-weight: 600;
        }

        .modal-field { margin-bottom: 14px; }
        .modal-field label {
            display: block;
            margin-bottom: 7px;
            color: #404a60;
            font-size: 12px;
            font-weight: 900;
        }
        .modal-field input,
        .modal-field textarea {
            width: 100%;
            border: 1.5px solid var(--line);
            border-radius: 10px;
            background: var(--field);
            padding: 12px;
            font: 600 14px 'Inter', sans-serif;
            color: var(--text);
            outline: none;
        }
        .modal-field textarea { min-height: 96px; resize: vertical; }
        .modal-field input:focus,
        .modal-field textarea:focus {
            border-color: var(--brand);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(238, 28, 37, .13);
        }
        .modal-btn {
            width: 100%;
            min-height: 46px;
            border: 0;
            border-radius: 10px;
            background: var(--brand);
            color: #ffffff;
            font: 900 14px 'Inter', sans-serif;
            cursor: pointer;
        }

        @media (max-width: 880px) {
            body { padding: 18px; align-items: flex-start; }
            .login-shell { grid-template-columns: 1fr; min-height: 0; }
            .brand-panel { padding: 34px 28px; }
            .brand-logo { margin-bottom: 24px; }
            .brand-title { font-size: 28px; }
            .feature-list { margin-top: 32px; }
            .form-panel { padding: 36px 28px; }
        }

        @media (max-width: 520px) {
            body { padding: 0; background: #ffffff; }
            .login-shell { border-radius: 0; box-shadow: none; }
            .brand-panel { padding: 28px 22px; }
            .brand-logo { width: 54px; height: 54px; }
            .brand-title { font-size: 25px; }
            .brand-copy { font-size: 14px; }
            .feature-item { font-size: 13px; }
            .form-panel { padding: 32px 22px; }
        }
    </style>
</head>
<body>
    <main class="login-shell">
        <section class="brand-panel" aria-label="WT System introduction">
            <div>
                <img class="brand-logo" src="{{ asset('assets/images/fjb-logo.png') }}" alt="FJB">
                <h1 class="brand-title">Welcome to the Walkie Talkie Management System</h1>
                <p class="brand-copy">Manage walkie talkie assets, requests and handovers from one secure workspace.</p>
            </div>

            <div class="feature-list">
                <div class="feature-item">
                    <span class="feature-icon"><i class="bi bi-broadcast-pin"></i></span>
                    <span>Walkie Talkie inventory tracking</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon"><i class="bi bi-clipboard-check"></i></span>
                    <span>Request management</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon"><i class="bi bi-arrow-left-right"></i></span>
                    <span>Request, return and faulty reporting</span>
                </div>
            </div>
        </section>

        <section class="form-panel">
            <div class="form-card">
                <h2 class="form-title">Sign In</h2>
                <p class="form-subtitle">Enter your credentials to access your account.</p>

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
                        <label for="staff_no">Staff ID</label>
                        <div class="input-wrap">
                            <i class="bi bi-person field-icon"></i>
                            <input id="staff_no" class="login-input" type="text" name="staff_no" placeholder="e.g. 0000001" value="{{ old('staff_no') }}" autocapitalize="off" autocomplete="username" spellcheck="false" required autofocus data-preserve-case="true">
                        </div>
                    </div>

                    <div class="field">
                        <label for="pass">Password</label>
                        <div class="input-wrap">
                            <i class="bi bi-lock field-icon"></i>
                            <input id="pass" class="login-input" type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
                            <button type="button" class="toggle-pass" onclick="togglePassword('pass', this)" title="Show/hide password" aria-label="Show or hide password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="forgot-row">
                            <a href="#" class="forgot-link" onclick="openForgotModal(); return false;">Forgot password?</a>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <span>Sign In</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                <div class="login-links">
                    <a class="back-link" href="{{ url('/') }}">&larr; Back to Portal</a>

                    <div class="switch-title">Switch System</div>
                    <div class="system-switch">
                        <a href="{{ url('/login') }}">HR System</a>
                        <span>&middot;</span>
                        <span class="active">WT System</span>
                        <span>&middot;</span>
                        <a href="{{ url('/it/login') }}">IT System</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- Access Denied Modal --}}
    <div id="accessDeniedModal" class="modal-overlay" onclick="closeOutside(event,'accessDeniedModal')">
        <div class="modal-box" style="text-align:center;">
            <div style="margin-bottom:18px;">
                <span style="display:inline-flex;align-items:center;justify-content:center;width:56px;height:56px;border-radius:50%;background:#fff1f2;margin-bottom:12px;">
                    <i class="bi bi-shield-x" style="font-size:26px;color:#e11d48;"></i>
                </span>
                <h3 style="margin:0;font-size:18px;font-weight:900;color:#101827;">Access Denied</h3>
            </div>
            <p style="margin:0 0 22px;color:#64748b;font-size:14px;font-weight:600;line-height:1.6;">
                You don't have access to this system.<br>Please contact ICT to request access.
            </p>
            <div style="display:flex;gap:10px;justify-content:center;">
                <a href="{{ url('/') }}" class="modal-btn" style="display:inline-flex;align-items:center;justify-content:center;gap:8px;text-decoration:none;border-radius:10px;padding:0 20px;min-height:44px;background:#0f223b;color:#fff;font:700 14px 'Inter',sans-serif;">
                    <i class="bi bi-arrow-left"></i> Back to Portal
                </a>
                <button type="button" class="modal-btn" onclick="document.getElementById('accessDeniedModal').classList.remove('active')" style="background:#f1f5f9;color:#475569;">
                    Dismiss
                </button>
            </div>
        </div>
    </div>

    <div id="forgotModal" class="modal-overlay" onclick="closeOutside(event,'forgotModal')">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Reset Request</h3>
                <button type="button" class="modal-close" onclick="closeForgotModal()" aria-label="Close reset password modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('wt.password.reset') }}">
                @csrf
                <p class="modal-desc">
                    Submit your details. Your request will be forwarded to <strong>ICT</strong> for review.
                </p>
                <div class="modal-field">
                    <label for="requester_name">Name</label>
                    <input id="requester_name" type="text" name="requester_name" placeholder="Enter your name" value="{{ old('requester_name') }}" required>
                </div>
                <div class="modal-field">
                    <label for="staff_id">Staff ID</label>
                    <input id="staff_id" type="text" name="staff_id" placeholder="Enter your Staff ID" value="{{ old('staff_id') }}" required>
                </div>
                <div class="modal-field">
                    <label for="justification">Justification</label>
                    <textarea id="justification" name="justification" placeholder="State the reason for password reset request" required>{{ old('justification') }}</textarea>
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

        @if(session('wt_access_denied'))
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('accessDeniedModal').classList.add('active');
        });
        @endif

        function openForgotModal() {
            document.getElementById('forgotModal').classList.add('active');
        }

        function closeForgotModal() {
            document.getElementById('forgotModal').classList.remove('active');
        }

        function closeOutside(e, id) {
            if (e.target === document.getElementById(id)) {
                document.getElementById(id).classList.remove('active');
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeForgotModal();
        });
    </script>
</body>
</html>
