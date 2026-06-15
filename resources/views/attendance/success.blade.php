<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance Marked — {{ config('app.name', 'HR System') }}</title>
<script>
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.setAttribute('data-theme', 'dark');
    }
</script>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --bg: #f8fafc; --card: #fff; --border: #e2e8f0;
    --text: #1e293b; --muted: #64748b; --accent: #6366f1; --danger: #ef4444;
    --star: #f59e0b; --star-off: #cbd5e1;
}
[data-theme="dark"] {
    --bg: #0f172a; --card: #1e293b; --border: #334155;
    --text: #f1f5f9; --muted: #94a3b8; --accent: #818cf8; --danger: #f87171;
    --star: #fbbf24; --star-off: #475569;
}
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: var(--bg); color: var(--text);
    min-height: 100vh; display: flex; align-items: center;
    justify-content: center; padding: 1.5rem;
}
.card {
    background: var(--card); border: 1.5px solid var(--border);
    border-radius: 16px; padding: 2rem 1.75rem;
    max-width: 420px; width: 100%; text-align: center;
}
.logo { font-size: .72rem; font-weight: 700; letter-spacing: .08em; color: var(--muted); text-transform: uppercase; margin-bottom: 1.5rem; }
.icon {
    width: 64px; height: 64px; border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.25rem; background: #d1fae5;
}
[data-theme="dark"] .icon { background: #064e3b; }
.title { font-size: 1.2rem; font-weight: 700; margin-bottom: .5rem; }
.message {
    font-size: .9rem; color: var(--muted); line-height: 1.6;
    margin-bottom: 1.75rem;
}
.message strong { color: var(--text); }
.divider { border: none; border-top: 1.5px solid var(--border); margin-bottom: 1.75rem; }
.detail-row {
    display: flex; justify-content: space-between; align-items: center;
    font-size: .82rem; padding: .4rem 0;
}
.detail-label { color: var(--muted); }
.detail-val { font-weight: 600; text-align: right; max-width: 60%; }
.detail-block { margin-bottom: 1.75rem; }
.btn {
    display: block; width: 100%; padding: .85rem;
    border-radius: 10px; border: none;
    background: var(--accent); color: #fff;
    font-size: .95rem; font-weight: 700; cursor: pointer;
    font-family: inherit; text-decoration: none; text-align: center;
    transition: opacity .15s;
}
.btn:active { opacity: .85; }
.btn-ghost {
    display: block; width: 100%; padding: .75rem;
    border-radius: 10px; border: 1.5px solid var(--border);
    background: transparent; color: var(--muted);
    font-size: .9rem; font-weight: 600; cursor: pointer;
    font-family: inherit; text-decoration: none; text-align: center;
    transition: opacity .15s; margin-top: .65rem;
}
/* ── Feedback questionnaire ── */
.fb-head { text-align: left; margin-bottom: 1.25rem; }
.fb-head h2 { font-size: 1.05rem; font-weight: 700; margin-bottom: .25rem; }
.fb-head p { font-size: .82rem; color: var(--muted); line-height: 1.5; }
.q { text-align: left; margin-bottom: 1.25rem; }
.q-label { display: block; font-size: .85rem; font-weight: 600; margin-bottom: .5rem; }
/* star rating: reversed flex so :hover/:checked can style preceding stars */
.stars { display: inline-flex; flex-direction: row-reverse; justify-content: flex-end; gap: .15rem; }
.stars input { position: absolute; opacity: 0; width: 0; height: 0; }
.stars label {
    cursor: pointer; font-size: 1.8rem; line-height: 1; color: var(--star-off);
    transition: color .12s; padding: 0 .05rem;
}
.stars label::before { content: "★"; }
.stars input:checked ~ label,
.stars label:hover,
.stars label:hover ~ label { color: var(--star); }
.choice { display: flex; gap: .6rem; }
.choice label {
    flex: 1; cursor: pointer; text-align: center;
    padding: .65rem; border: 1.5px solid var(--border); border-radius: 9px;
    font-size: .9rem; font-weight: 600; color: var(--muted);
    transition: all .12s;
}
.choice input { position: absolute; opacity: 0; width: 0; height: 0; }
.choice input:checked + label { border-color: var(--accent); color: var(--accent); background: color-mix(in srgb, var(--accent) 8%, transparent); }
textarea {
    width: 100%; padding: .7rem .85rem; min-height: 84px; resize: vertical;
    border: 1.5px solid var(--border); border-radius: 8px;
    background: var(--bg); color: var(--text);
    font-size: .92rem; font-family: inherit; outline: none; transition: border-color .15s;
}
textarea:focus { border-color: var(--accent); }
.err { font-size: .78rem; color: var(--danger); margin-top: .3rem; text-align: left; }
.err-box {
    background: #fee2e2; color: #b91c1c; border-radius: 8px;
    padding: .6rem .8rem; font-size: .82rem; margin-bottom: 1rem; text-align: left;
}
[data-theme="dark"] .err-box { background: #450a0a; color: #fca5a5; }
</style>
</head>
<body>

@php
    $alreadyDone = session('feedback_done') || ($attendance && $attendance->feedback);
    $showForm = $attendance && !$alreadyDone;
@endphp

<div class="card">
    <div class="logo">{{ config('app.name', 'HR System') }}</div>

    <div class="icon">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
    </div>

    <h1 class="title">Attendance Marked!</h1>
    <p class="message">
        Attendance marked successfully for<br>
        <strong>{{ $course->title }}</strong><br>
        as <strong>{{ $staffName }}</strong>.
    </p>

    <hr class="divider">

    <div class="detail-block">
        @if($course->start_date)
        <div class="detail-row">
            <span class="detail-label">Date</span>
            <span class="detail-val">{{ \Carbon\Carbon::parse($course->start_date)->format('d M Y') }}</span>
        </div>
        @endif
        @if($course->venue)
        <div class="detail-row">
            <span class="detail-label">Venue</span>
            <span class="detail-val">{{ $course->venue }}</span>
        </div>
        @endif
        <div class="detail-row">
            <span class="detail-label">Status</span>
            <span class="detail-val" style="color:#059669; font-weight:700;">Completed</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Recorded at</span>
            <span class="detail-val">{{ now()->format('d M Y, H:i') }}</span>
        </div>
    </div>

    @if($alreadyDone)
        {{-- ── Feedback already submitted ── --}}
        <div style="background:#ecfdf5;color:#047857;border-radius:10px;padding:.85rem 1rem;font-size:.85rem;font-weight:600;margin-bottom:1.25rem;">
            🙏 Thank you for your feedback!
        </div>
        <a href="{{ route('training.index') }}" class="btn">View My Training</a>

    @elseif($showForm)
        {{-- ── Feedback questionnaire ── --}}
        <hr class="divider">

        <div class="fb-head">
            <h2>Quick feedback</h2>
            <p>Help us improve future sessions — it only takes a moment.</p>
        </div>

        @if($errors->any())
            <div class="err-box">Please answer all the rating questions before submitting.</div>
        @endif

        <form method="POST" action="{{ route('attendance.feedback.submit', $course->id) }}">
            @csrf
            <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

            @php
                $ratingQs = [
                    'content_rating' => 'Course content & relevance',
                    'trainer_rating' => 'Trainer / facilitator',
                    'venue_rating'   => 'Venue & facilities',
                    'overall_rating' => 'Overall experience',
                ];
            @endphp

            @foreach($ratingQs as $name => $label)
                <div class="q">
                    <span class="q-label">{{ $label }}</span>
                    <div class="stars">
                        @for($v = 5; $v >= 1; $v--)
                            <input type="radio" id="{{ $name }}_{{ $v }}" name="{{ $name }}" value="{{ $v }}"
                                   {{ (string)old($name) === (string)$v ? 'checked' : '' }}>
                            <label for="{{ $name }}_{{ $v }}" title="{{ $v }} star{{ $v > 1 ? 's' : '' }}"></label>
                        @endfor
                    </div>
                    @error($name)<div class="err">{{ $message }}</div>@enderror
                </div>
            @endforeach

            <div class="q">
                <span class="q-label">Would you recommend this training?</span>
                <div class="choice">
                    <input type="radio" id="rec_yes" name="would_recommend" value="1" {{ old('would_recommend') === '1' ? 'checked' : '' }}>
                    <label for="rec_yes">👍 Yes</label>
                    <input type="radio" id="rec_no" name="would_recommend" value="0" {{ old('would_recommend') === '0' ? 'checked' : '' }}>
                    <label for="rec_no">👎 No</label>
                </div>
                @error('would_recommend')<div class="err">{{ $message }}</div>@enderror
            </div>

            <div class="q">
                <span class="q-label">Additional comments <span style="color:var(--muted);font-weight:400;">(optional)</span></span>
                <textarea name="comments" placeholder="What did you like? What could be better?">{{ old('comments') }}</textarea>
                @error('comments')<div class="err">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn">Submit Feedback</button>
            <a href="{{ route('training.index') }}" class="btn-ghost">Skip for now</a>
        </form>

    @else
        {{-- ── No feedback context (e.g. direct visit) ── --}}
        <a href="{{ route('training.index') }}" class="btn">View My Training</a>
    @endif
</div>

</body>
</html>
