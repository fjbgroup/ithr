<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
<title>WT System</title>
@include('partials.favicons')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        const savedTheme = localStorage.getItem('color-theme');
        const initialTheme = savedTheme === 'dark' || savedTheme === 'light'
            ? savedTheme
            : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.classList.toggle('dark', initialTheme === 'dark');
        document.documentElement.dataset.theme = initialTheme;
    </script>
    <style>
    

    :root {
        --card: #FFFFFF;
        --input-bg: #F8FAFC;
        --input-border: #D7DEE7;
        --text-dark: #1F2937;
        --text-muted: #64748B;
        --primary: #243041;
        --primary-hover: #1B2432;
        --gold: #64748B;
        --navy: #1F2937;
    }

    html.dark {
        --card: rgba(30, 41, 59, 0.86);
        --input-bg: #0f172a;
        --input-border: #334155;
        --text-dark: #f1f5f9;
        --text-muted: #94a3b8;
        --primary: #334155;
        --primary-hover: #475569;
        --gold: #94a3b8;
        --navy: #f1f5f9;
    }

    * { box-sizing: border-box; }

    body {
        min-height: 100vh; margin: 0; padding: 20px 0;
        background: #eef4fa;
        font-family: 'DM Sans', sans-serif;
        display: flex; justify-content: center; align-items: center;
        transition: background-color 0.3s;
        overflow-x: hidden;
        position: relative;
        isolation: isolate;
    }

    body::before {
        content: "";
        position: fixed;
        inset: -12%;
        z-index: -1;
        background:
            radial-gradient(circle at top right, rgba(59,130,246,0.10), transparent 24%),
            radial-gradient(circle at bottom left, rgba(148,163,184,0.18), transparent 28%),
            linear-gradient(135deg, #EEF4FA 0%, #E6EDF5 100%);
        filter: blur(100px);
        transform: scale(1.05);
    }
    
    html.dark body {
        background: #0f172a;
    }

    html.dark body::before {
        background:
            radial-gradient(circle at top right, rgba(59,130,246,0.16), transparent 24%),
            radial-gradient(circle at bottom left, rgba(148,163,184,0.12), transparent 28%),
            linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    }

    /* ===== Card - Clean & No Scroll ===== */
    .card {
        background: var(--card);
        border-radius: 28px;
        width: 100%; max-width: 430px;
        padding: 34px 28px 40px;
        border: 1px solid rgba(215, 222, 231, 0.9);
        box-shadow: 0 28px 60px rgba(15,23,42,0.12);
        position: relative;
        transition: all 0.3s;
        backdrop-filter: blur(18px);
    }
    
    html.dark .card {
        border-color: #334155;
        box-shadow: 0 28px 60px rgba(0, 0, 0, 0.3);
    }

    .card-header { text-align: center; margin-bottom: 25px; }
    .icon-header {
        background: linear-gradient(135deg, rgba(31,41,55,0.08), rgba(100,116,139,0.14));
        width: 58px; height: 58px; border-radius: 18px;
        display: flex; justify-content: center; align-items: center;
        margin: 0 auto 14px; color: var(--navy); font-size: 23px;
        border: 1px solid rgba(100,116,139,0.20);
    }
    h1 { color: var(--text-dark); font-weight: 800; margin: 0; font-size: 21px; text-transform: uppercase; letter-spacing: 0.02em; }
    .subtitle { color: var(--text-muted); font-size: 10px; margin-top: 8px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.22em; }

    /* ===== Form Styles ===== */
    .form-label { font-weight: 700; font-size: 10px; color: var(--text-muted); text-transform: uppercase; display: block; margin: 0 0 6px 3px; letter-spacing: 0.12em; }
    .form-input { 
        background: var(--input-bg); border: 1.5px solid var(--input-border);
        padding: 12px 15px; border-radius: 14px; width: 100%;
        font-size: 14px; margin-bottom: 15px; transition: 0.2s;
        font-family: 'DM Sans', sans-serif;
        color: var(--text-dark);
    }
    .form-input:focus { outline: none; border-color: #94A3B8; background: #fff; box-shadow: 0 0 0 4px rgba(148,163,184,0.12); }
    html.dark .form-input:focus { background: #0f172a; border-color: #475569; }

    /* ===== Role Selection - Fixed Widths ===== */
    .role-toggle { 
        display: grid; 
        grid-template-columns: 1fr 1fr 1.2fr; /* ICT is slightly wider */
        gap: 8px; 
        margin-bottom: 18px; 
    }
    .role-btn {
        padding: 12px 5px; border-radius: 12px; border: 1.5px solid var(--input-border);
        background: var(--input-bg); color: var(--text-muted); font-size: 11px;
        font-weight: 700; cursor: pointer; text-align: center; transition: all 0.2s;
        display: flex; align-items: center; justify-content: center; gap: 4px;
    }
    .role-btn.active { border-color: #94A3B8; background: rgba(51,65,85,0.07); color: var(--navy); box-shadow: 0 10px 22px rgba(15,23,42,0.08); }
    html.dark .role-btn.active { background: rgba(255, 255, 255, 0.05); border-color: #475569; }

    /* ===== Verification Box & Info ===== */
    #verifyBox { display: none; margin-bottom: 15px; }
    .admin-info {
        display: none; align-items: center; gap: 6px; margin-top: -8px; margin-bottom: 15px;
        color: var(--gold); cursor: help;
    }

    .input-group { position: relative; }
    .toggle-pass { position: absolute; right: 15px; top: 14px; cursor: pointer; color: var(--text-muted); }
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear { display: none; }

    /* ===== Button & Footer ===== */
    .btn-submit {
        border-radius: 14px; border: none; background: linear-gradient(135deg, var(--navy), var(--primary)); color: #fff;
        font-size: 13px; font-weight: 700; padding: 14px; cursor: pointer; 
        width: 100%; margin-top: 10px; text-transform: uppercase; letter-spacing: 0.16em;
        box-shadow: 0 16px 24px rgba(31,41,55,0.18);
    }
    .btn-submit:hover { background: linear-gradient(135deg, #18212E, var(--primary-hover)); transform: translateY(-1px); }

    .divider { height: 1px; background: #E2E8F0; margin: 25px 0; }
    .back-link { text-align: center; font-size: 12px; color: var(--text-muted); }
    .back-link a { color: var(--navy); font-weight: 700; text-decoration: none; text-transform: uppercase; letter-spacing: 0.08em; }
    
    .field-error { font-size: 11px; color: #dc2626; margin: -10px 0 10px 3px; font-weight: 600; }
    .alert {
        font-size: 12px;
        font-weight: 700;
        padding: 12px 14px;
        border-radius: 14px;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .alert-error {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
    .alert-success {
        background: rgba(34, 197, 94, 0.1);
        color: #16a34a;
        border: 1px solid rgba(34, 197, 94, 0.2);
    }
    </style>
</head>
<body>

<div class="card">
    <div class="card-header">
        <div class="icon-header"><i class="fas fa-user-plus"></i></div>
        <h1>Create Account</h1>
        <p class="subtitle">Corporate User Registration</p>
    </div>

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-circle-exclamation"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}" id="registerForm">
        @csrf
        <input type="hidden" name="role" id="roleInput" value="{{ old('role', 'user') }}">

        <label class="form-label">Account Type</label>
        <div class="role-toggle">
            <div class="role-btn {{ old('role','user') === 'user' ? 'active' : '' }}" id="btnUser" onclick="setRole('user')">
                <i class="fas fa-user"></i> User
            </div>
            <div class="role-btn {{ old('role') === 'admin' ? 'active' : '' }}" id="btnAdmin" onclick="setRole('admin')">
                <i class="fas fa-shield-halved"></i> Executive
            </div>
            <div class="role-btn {{ old('role') === 'admin_it' ? 'active' : '' }}" id="btnAdminIT" onclick="setRole('admin_it')">
                <i class="fas fa-laptop-code"></i> ICT
            </div>
        </div>

        <div id="verifyBox">
            <label class="form-label">Verification Code</label>
            <div id="adminInfo" class="admin-info" title="Use the correct code for the selected account type">
            <span id="verificationHint" style="font-size: 11px; font-weight: 600; padding-top: 20px;">Use the correct verification code</span>
        </div>
            <input type="text" name="verification_code" id="verificationCodeInput" class="form-input" placeholder="Enter code..." value="{{ old('verification_code') }}">
        </div>

        <label class="form-label">Staff ID</label>
        <input type="text" name="staff_id" class="form-input" placeholder="e.g. 123456" value="{{ old('staff_id') }}" required>

        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-input" placeholder="Enter your username" value="{{ old('username') }}" autocapitalize="off" autocomplete="username" spellcheck="false" data-preserve-case="true" required>
        
        <label class="form-label">Password</label>
        <div class="input-group">
            <input type="password" name="password" id="pass1" class="form-input" placeholder="Min 6 Characters" required>
            <i class="fas fa-eye toggle-pass" onclick="togglePass('pass1', this)"></i>
        </div>

        <label class="form-label">Confirm Password</label>
        <div class="input-group">
            <input type="password" name="password_confirmation" id="pass2" class="form-input" placeholder="Repeat Password" required>
            <i class="fas fa-eye toggle-pass" onclick="togglePass('pass2', this)"></i>
        </div>

        <button type="submit" class="btn-submit">Register Account</button>
    </form>

    <div class="divider"></div>
    
    <div class="back-link">
        Already have an account? <a href="{{ route('wt.login') }}">Login here →</a>
    </div>
</div>

<script>
    function setRole(role) {
        document.getElementById('roleInput').value = role;
        document.getElementById('btnUser').classList.toggle('active', role === 'user');
        document.getElementById('btnAdmin').classList.toggle('active', role === 'admin');
        document.getElementById('btnAdminIT').classList.toggle('active', role === 'admin_it');
        
        const box = document.getElementById('verifyBox');
        const info = document.getElementById('adminInfo');
        const hint = document.getElementById('verificationHint');
        const codeInput = document.getElementById('verificationCodeInput');
        const needsCode = (role === 'admin' || role === 'admin_it');
        
        box.style.display = needsCode ? 'block' : 'none';
        info.style.display = needsCode ? 'flex' : 'none';

        if (role === 'admin') {
            hint.textContent = 'Executive code: Fgvjb@123';
            codeInput.placeholder = 'Enter executive verification code';
        } else if (role === 'admin_it') {
            hint.textContent = 'ICT code: Ict@FJB';
            codeInput.placeholder = 'Enter ICT verification code';
        } else {
            hint.textContent = 'No verification code required';
            codeInput.placeholder = 'Enter code...';
        }
        
        if (codeInput) codeInput.required = needsCode;
    }

    function togglePass(id, icon) {
        const f = document.getElementById(id);
        if (f.type === 'password') { 
            f.type = 'text'; 
            icon.classList.replace('fa-eye','fa-eye-slash'); 
        } else { 
            f.type = 'password'; 
            icon.classList.replace('fa-eye-slash','fa-eye'); 
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        setRole(document.getElementById('roleInput').value);
    });
</script>

</body>
</html>


