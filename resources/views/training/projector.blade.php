<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Projector — {{ $course->title }}</title>
<link rel="shortcut icon" href="{{ asset('assets/images/footer.jpg') }}?v=it-team-20260623" type="image/jpeg">
<link rel="apple-touch-icon" href="{{ asset('assets/images/footer.jpg') }}?v=it-team-20260623">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: #0f172a;
    color: #f1f5f9;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2rem;
    padding: 2rem;
}
.course-title {
    font-size: 2rem;
    font-weight: 700;
    text-align: center;
    color: #f1f5f9;
    letter-spacing: -.01em;
    max-width: 800px;
}
.course-meta {
    font-size: 1.1rem;
    color: #94a3b8;
    text-align: center;
}
#qr-container {
    background: #fff;
    border-radius: 20px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 60px rgba(99,102,241,.35);
}
#qr-container svg { display: block; }
.timer-row {
    display: flex;
    align-items: center;
    gap: .75rem;
    font-size: 1rem;
    color: #94a3b8;
}
.timer-badge {
    background: #1e293b;
    border: 1.5px solid #334155;
    border-radius: 999px;
    padding: .35rem 1.1rem;
    font-size: 1.4rem;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
    color: #818cf8;
    min-width: 3.5rem;
    text-align: center;
}
.status-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #22c55e;
    animation: pulse 2s ease-in-out infinite;
}
@keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:.4; } }
.instruction {
    font-size: 1.25rem;
    color: #cbd5e1;
    text-align: center;
}
</style>
</head>
<body>

<div class="course-title">{{ $course->title }}</div>
<div class="course-meta">
    {{ $course->code }}
    @if($course->start_date) 
        &nbsp;·&nbsp; 
        @if($course->end_date && $course->end_date !== $course->start_date)
            {{ \Carbon\Carbon::parse($course->start_date)->format('d M') }} – {{ \Carbon\Carbon::parse($course->end_date)->format('d M Y') }}
        @else
            {{ \Carbon\Carbon::parse($course->start_date)->format('d M Y') }}
        @endif
    @endif
    @if($course->venue) &nbsp;·&nbsp; {{ $course->venue }} @endif
</div>

<div id="qr-container">
    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(400)->generate($url) !!}
</div>

<div class="timer-row">
    <div class="status-dot"></div>
    <span>Refreshes in</span>
    <div class="timer-badge" id="countdown">30</div>
    <span>seconds</span>
</div>

<div class="instruction">Scan the QR code with your phone to mark attendance</div>

<script>
(function () {
    var refreshUrl = "{{ route('training.refresh-token', $course->id) }}";
    var countdown  = 30;
    var timerEl    = document.getElementById('countdown');

    var tickInterval = setInterval(function () {
        countdown--;
        if (timerEl) timerEl.textContent = countdown;
    }, 1000);

    setInterval(function () {
        fetch(refreshUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                document.getElementById('qr-container').innerHTML = data.svg;
                countdown = 30;
                if (timerEl) timerEl.textContent = countdown;
            })
            .catch(function () {});
    }, 30000);
}());
</script>

</body>
</html>
