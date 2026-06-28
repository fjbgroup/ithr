<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Scan QR â€” {{ config('app.name', 'HR System') }}</title>
<script>
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.setAttribute('data-theme', 'dark');
    }
</script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --bg: #f8fafc; --card: #fff; --border: #e2e8f0;
    --text: #1e293b; --muted: #64748b; --accent: #6366f1;
}
[data-theme="dark"] {
    --bg: #0f172a; --card: #1e293b; --border: #334155;
    --text: #f1f5f9; --muted: #94a3b8; --accent: #818cf8;
}
body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.25rem; }
.scan-card { background: var(--card); border: 1.5px solid var(--border); border-radius: 16px; padding: 1.75rem 1.5rem; max-width: 420px; width: 100%; text-align: center; }
.scan-logo { font-size: .75rem; font-weight: 700; letter-spacing: .08em; color: var(--muted); text-transform: uppercase; margin-bottom: 1.25rem; }
.scan-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; background: #ede9fe; }
[data-theme="dark"] .scan-icon { background: #312e81; }
.scan-title { font-size: 1.15rem; font-weight: 700; margin-bottom: .4rem; }
.scan-sub { font-size: .85rem; color: var(--muted); margin-bottom: 1.25rem; line-height: 1.5; }
#reader { width: 100%; border-radius: 12px; overflow: hidden; border: 1.5px solid var(--border); background: #000; min-height: 240px; }
#reader video { display: block; width: 100% !important; }
.scan-status { font-size: .82rem; color: var(--muted); margin-top: 1rem; min-height: 1.2em; }
.scan-status.err { color: #ef4444; font-weight: 600; }
.scan-btn { width: 100%; padding: .85rem; border-radius: 10px; border: none; font-size: .95rem; font-weight: 700; cursor: pointer; font-family: inherit; transition: opacity .15s; margin-top: 1rem; }
.scan-btn:active { opacity: .85; }
.scan-btn-primary { background: var(--accent); color: #fff; }
.scan-btn-ghost { background: transparent; border: 1.5px solid var(--border); color: var(--muted); }
.scan-hint { font-size: .75rem; color: var(--muted); margin-top: .9rem; line-height: 1.5; }
/* Tidy up html5-qrcode's injected UI */
#reader__dashboard_section_csr button { font-family: inherit !important; }
#reader__scan_region img { display: none; }
</style>
</head>
<body>

<div class="scan-card">
    <div class="scan-logo">{{ config('app.name', 'HR System') }}</div>

    <div class="scan-icon">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><path d="M14 14h.01M14 18h.01M18 14h.01M18 18h.01"/></svg>
    </div>
    <h1 class="scan-title">Scan Attendance QR</h1>
    <p class="scan-sub">Point your camera at the QR code shown on the training screen.</p>

    <div id="reader"></div>
    <div class="scan-status" id="status">Starting cameraâ€¦</div>

    <button class="scan-btn scan-btn-primary" id="startBtn" style="display:none;">Allow Camera</button>
    <a href="{{ url('/') }}"><button class="scan-btn scan-btn-ghost">Cancel</button></a>

    <p class="scan-hint">Tip: most phones can also scan the code directly from the built-in camera app.</p>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function () {
    var statusEl = document.getElementById('status');
    var startBtn = document.getElementById('startBtn');
    var qr = new Html5Qrcode('reader');
    var handled = false;
    var origin = window.location.origin;

    function setStatus(msg, isErr) {
        statusEl.textContent = msg;
        statusEl.className = 'scan-status' + (isErr ? ' err' : '');
    }

    // Only follow QR payloads that point at this app's attendance verify URL.
    function resolveTarget(text) {
        try {
            var u = new URL(text, origin);
            if (u.origin === origin && /\/attendance\/verify\//.test(u.pathname)) {
                return u.href;
            }
        } catch (e) { /* not a URL */ }
        return null;
    }

    function onScan(decodedText) {
        if (handled) return;
        var target = resolveTarget(decodedText);
        if (!target) {
            setStatus('That QR code is not a valid attendance code.', true);
            return;
        }
        handled = true;
        setStatus('Code recognised â€” openingâ€¦');
        qr.stop().catch(function () {}).finally(function () {
            window.location.href = target;
        });
    }

    function start() {
        startBtn.style.display = 'none';
        setStatus('Starting cameraâ€¦');
        qr.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 230, height: 230 } },
            onScan,
            function () { /* per-frame decode failures are normal; ignore */ }
        ).then(function () {
            setStatus('Searching for a QR codeâ€¦');
        }).catch(function (err) {
            console.error(err);
            setStatus('Could not access the camera. Please grant camera permission and try again.', true);
            startBtn.textContent = 'Try Again';
            startBtn.style.display = 'block';
        });
    }

    startBtn.addEventListener('click', start);
    start();
})();
</script>

</body>
</html>
