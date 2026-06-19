<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>WT System | Authentication</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        const savedTheme = localStorage.getItem('color-theme');
        const initialTheme = savedTheme === 'dark' || savedTheme === 'light'
            ? savedTheme
            : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.classList.toggle('dark', initialTheme === 'dark');
        document.documentElement.dataset.theme = initialTheme;
        document.documentElement.style.colorScheme = initialTheme;
    </script>
    <style>
    :root {
        --primary: #1e293b;
        --accent: #B38A5A;
        --accent-hover: #8D6742;
        --bg-gradient: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        --card-bg: rgba(255, 255, 255, 0.95);
        --text-main: #1e293b;
        --text-muted: #64748b;
        --input-bg: #ffffff;
        --input-border: #e2e8f0;
    }

    html.dark {
        --primary: #0f172a;
        --accent: #D1AE7B;
        --accent-hover: #B38A5A;
        --bg-gradient: radial-gradient(circle at top left, #1e293b 0%, #0f172a 100%);
        --bg-base: #0f172a;
        --card-bg: rgba(30, 41, 59, 0.8);
        --text-main: #f1f5f9;
        --text-muted: #94a3b8;
        --input-bg: #0f172a;
        --input-border: #334155;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }
    .hidden { display: none !important; }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--bg-base, #eef4fa);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        color: var(--text-main);
        overflow-x: hidden;
        overflow-y: auto;
        position: relative;
        isolation: isolate;
    }

    body::before {
        content: "";
        position: fixed;
        inset: -12%;
        z-index: -1;
        background: var(--bg-gradient);
        filter: blur(100px);
        transform: scale(1.05);
    }

    /* Ambient Background Elements */
    .ambient-1 { position: fixed; top: -10%; left: -5%; width: 40vw; height: 40vw; background: radial-gradient(circle, rgba(179, 138, 90, 0.08) 0%, transparent 70%); z-index: -1; filter: blur(60px); }
    .ambient-2 { position: fixed; bottom: -10%; right: -5%; width: 50vw; height: 50vw; background: radial-gradient(circle, rgba(30, 41, 59, 0.05) 0%, transparent 70%); z-index: -1; filter: blur(80px); }

    .login-container {
        width: 100%;
        max-width: 360px;
        perspective: 1000px;
        animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .glass-card {
        background: var(--card-bg);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 28px;
        padding: 30px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        position: relative;
        overflow: hidden;
    }

    html.dark .glass-card {
        border-color: rgba(255, 255, 255, 0.05);
        box-shadow: 0 25px 80px -12px rgba(0, 0, 0, 0.5);
    }

    .brand-section {
        text-align: center;
        margin-bottom: 24px;
    }

    .logo-box {
        width: 54px;
        height: 54px;
        background: linear-gradient(135deg, var(--accent), var(--accent-hover));
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        box-shadow: 0 10px 20px rgba(179, 138, 90, 0.3);
        color: white;
        font-size: 24px;
        transform: rotate(-3deg);
        transition: transform 0.3s ease;
    }

    .logo-box:hover { transform: rotate(0deg) scale(1.05); }

    /* Theme Toggle */
    .theme-toggle-floating {
        position: fixed;
        top: 24px;
        right: 24px;
        width: 44px;
        height: 44px;
        background: var(--card-bg);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: var(--text-muted);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 100;
    }
    html.dark .theme-toggle-floating { border-color: rgba(255, 255, 255, 0.05); }
    .theme-toggle-floating:hover { transform: translateY(-2px); color: var(--accent); box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15); }
    .theme-toggle-floating:active { transform: translateY(0); }

    h1 {
        font-size: 20px;
        font-weight: 900;
        letter-spacing: -0.02em;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .subtitle {
        font-size: 9px;
        font-weight: 800;
        color: var(--accent);
        text-transform: uppercase;
        letter-spacing: 0.2em;
    }

    .form-group {
        margin-bottom: 16px;
        position: relative;
    }

    label {
        font-size: 10px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 8px;
        display: block;
        padding-left: 4px;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-wrapper i.field-icon {
        position: absolute;
        left: 16px;
        color: var(--text-muted);
        font-size: 14px;
        transition: color 0.3s;
    }

    input {
        width: 100%;
        background: var(--input-bg);
        border: 1.5px solid var(--input-border);
        border-radius: 12px;
        padding: 10px 14px 10px 40px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-main);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        outline: none;
    }

    input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 4px rgba(179, 138, 90, 0.1);
    }

    input:focus + i.field-icon {
        color: var(--accent);
    }

    .toggle-pass {
        position: absolute;
        right: 14px;
        color: var(--text-muted);
        cursor: pointer;
        padding: 4px;
        transition: color 0.3s;
    }

    .toggle-pass:hover { color: var(--accent); }

    .btn-submit {
        width: 100%;
        background: linear-gradient(135deg, var(--accent), var(--accent-hover));
        color: white;
        border: none;
        border-radius: 14px;
        padding: 12px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        cursor: pointer;
        box-shadow: 0 10px 25px rgba(179, 138, 90, 0.25);
        transition: all 0.3s;
        margin-top: 6px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(179, 138, 90, 0.4);
        filter: brightness(1.1);
    }

    .btn-submit:active { transform: translateY(0); }

    .alert {
        font-size: 12px;
        font-weight: 600;
        padding: 14px 18px;
        border-radius: 16px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
    }

    @keyframes shake {
        10%, 90% { transform: translate3d(-1px, 0, 0); }
        20%, 80% { transform: translate3d(2px, 0, 0); }
        30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
        40%, 60% { transform: translate3d(4px, 0, 0); }
    }

    .alert-error { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
    .alert-success { background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2); }

    .footer-links {
        margin-top: 32px;
        text-align: center;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .footer-links a {
        color: var(--accent);
        text-decoration: none;
        transition: color 0.3s;
    }

    .footer-links a:hover { color: var(--accent-hover); }

    /* Modal Styling */
    .modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(12px);
        z-index: 1000; align-items: center; justify-content: center; padding: 20px;
    }
    .modal-overlay.active { display: flex; }

    .modal-box {
        background: var(--card-bg);
        width: 100%; max-width: 400px;
        padding: 32px; border-radius: 28px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
        animation: popUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes popUp { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }

    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .modal-close { cursor: pointer; color: var(--text-muted); font-size: 20px; transition: color 0.3s; }
    .modal-close:hover { color: #ef4444; }

    /* Responsive */
    @media (max-width: 480px) {
        .glass-card { padding: 32px 24px; border-radius: 24px; }
    }

    @media (max-height: 820px) {
        body { padding-top: 28px; padding-bottom: 28px; }
    }
    </style>
</head>
<body>
    <div class="ambient-1"></div>
    <div class="ambient-2"></div>

    {{-- Floating Theme Toggle --}}
    <div class="theme-toggle-floating" id="theme-toggle" title="Toggle Dark/Light Mode">
        <i id="theme-toggle-dark-icon" class="hidden fas fa-moon"></i>
        <i id="theme-toggle-light-icon" class="hidden fas fa-sun text-yellow-500"></i>
    </div>

    <div class="login-container">
        <div class="glass-card">
            <div class="brand-section">
                <div class="logo-box">
                    <i class="fas fa-walkie-talkie"></i>
                </div>
                <h1>WT System</h1>
                <div class="subtitle">FGV Johor Bulkers Sdn Bhd</div>
            </div>

            @if(session('error'))
                <div class="alert alert-error"><i class="fas fa-circle-exclamation"></i> <span>{{ session('error') }}</span></div>
            @endif
            @if(session('success'))
                <div class="alert alert-success"><i class="fas fa-circle-check"></i> <span>{{ session('success') }}</span></div>
            @endif
            @if(isset($errors) && is_object($errors) && $errors->any())
                <div class="alert alert-error"><i class="fas fa-circle-exclamation"></i> <span>{{ $errors->first() }}</span></div>
            @endif

            <form method="POST" action="{{ route('wt.login') }}">
                @csrf
                <div class="form-group">
                    <label>Staff No.</label>
                    <div class="input-wrapper">
                        <i class="fas fa-id-badge field-icon"></i>
                        <input type="text" name="staff_no" placeholder="Enter your Staff No." value="{{ old('staff_no') }}" autocapitalize="off" autocomplete="off" spellcheck="false" required autofocus />
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock field-icon"></i>
                        <input type="password" name="password" id="pass" placeholder="••••••••" required />
                        <i class="fas fa-eye toggle-pass" onclick="togglePassword('pass', this)"></i>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    Sign In <i class="fas fa-arrow-right-to-bracket ml-2"></i>
                </button>
            </form>

            <div class="footer-links">
                <a href="#" onclick="openForgotModal(); return false;">Forgot your password?</a>
                &nbsp;&middot;&nbsp;
                <a href="{{ url('/') }}" style="opacity:.65;">&larr; Back to Portal</a>
            </div>
        </div>
    </div>

    {{-- Reset Password Modal --}}
    <div id="forgotModal" class="modal-overlay" onclick="closeOutside(event,'forgotModal')">
        <div class="modal-box">
            <div class="modal-header">
                <h3 style="font-weight: 800; font-size: 16px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent);">Reset Request</h3>
                <i class="fas fa-times modal-close" onclick="closeForgotModal()"></i>
            </div>
            <form method="POST" action="{{ route('wt.password.reset') }}">
                @csrf
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 20px; line-height: 1.6;">
                    Submit your details. Your request will be forwarded to <strong>ICT</strong> for review. ICT will handle the password reset for your account.
                </p>

                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="requester_name" placeholder="Enter your name" value="{{ old('requester_name') }}" required />
                </div>
                
                <div class="form-group">
                    <label>Staff ID</label>
                    <input type="text" name="staff_id" placeholder="Enter your ID" value="{{ old('staff_id') }}" required />
                </div>
                
                <div class="form-group">
                    <label>Justification</label>
                    <textarea name="justification" placeholder="State the reason for password reset request" required style="width: 100%; min-height: 96px; background: var(--input-bg); border: 1.5px solid var(--input-border); border-radius: 12px; padding: 12px 14px; font-size: 13px; font-weight: 600; color: var(--text-main); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); outline: none; resize: vertical;">{{ old('justification') }}</textarea>
                </div>
                
                <button type="submit" class="btn-submit" style="margin-top: 10px;">Send Request</button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(id, icon) {
            const f = document.getElementById(id);
            if (f.type === "password") { 
                f.type = "text"; 
                icon.classList.replace("fa-eye","fa-eye-slash"); 
            } else { 
                f.type = "password"; 
                icon.classList.replace("fa-eye-slash","fa-eye"); 
            }
        }
        function openForgotModal()  { document.getElementById('forgotModal').classList.add('active'); }
        function closeForgotModal() { document.getElementById('forgotModal').classList.remove('active'); }
        function closeOutside(e, id) { if (e.target === document.getElementById(id)) closeForgotModal(); }
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeForgotModal(); });

        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        function getSystemTheme() {
            const savedTheme = localStorage.getItem('color-theme');
            if (savedTheme === 'dark' || savedTheme === 'light') {
                return savedTheme;
            }

            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        function applySystemTheme(theme) {
            document.documentElement.classList.toggle('dark', theme === 'dark');
            document.documentElement.dataset.theme = theme;
            document.documentElement.style.colorScheme = theme;

            if (themeToggleDarkIcon && themeToggleLightIcon) {
                const isDark = theme === 'dark';
                themeToggleDarkIcon.classList.toggle('hidden', isDark);
                themeToggleLightIcon.classList.toggle('hidden', !isDark);
                themeToggleDarkIcon.style.display = isDark ? 'none' : 'inline-block';
                themeToggleLightIcon.style.display = isDark ? 'inline-block' : 'none';
            }

            if (themeToggleBtn) {
                const nextLabel = theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode';
                themeToggleBtn.setAttribute('aria-label', nextLabel);
                themeToggleBtn.setAttribute('title', nextLabel);
            }
        }

        applySystemTheme(getSystemTheme());

        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', function() {
                const nextTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
                localStorage.setItem('color-theme', nextTheme);
                applySystemTheme(nextTheme);
            });
        }
    </script>
</body>
</html>


