@php
    $dayBookings = $allRangeBookings->filter(fn($b) => $b->booking_date === $viewDate);
    $bookingsByRoom = $dayBookings->groupBy('room_id');
    $bookingsByRoomDate = $allRangeBookings->groupBy(['room_id', 'booking_date']);
    $bookingsByDate = $allRangeBookings->groupBy('booking_date');
    $freeCount = $rooms->filter(fn($r) => !isset($bookingsByRoom[$r->id]))->count();

    $rangeDays = [];
    $d = new \DateTime($rangeStart);
    $dEnd = new \DateTime($rangeEnd);
    $dEnd->modify('+1 day');
    while ($d < $dEnd) {
        $rangeDays[] = $d->format('Y-m-d');
        $d->modify('+1 day');
    }

    $calWeeks = [];
    if ($viewMode === 'month') {
        $firstDay = new \DateTime($rangeStart);
        $lastDay = new \DateTime($rangeEnd);
        $startDow = (int)$firstDay->format('N') - 1;
        $calStart = clone $firstDay;
        if ($startDow > 0) $calStart->modify('-' . $startDow . ' days');
        $endDow = (int)$lastDay->format('N');
        $calEnd = clone $lastDay;
        if ($endDow < 7) $calEnd->modify('+' . (7 - $endDow) . ' days');
        $d = clone $calStart;
        $wk = [];
        while ($d <= $calEnd) {
            $wk[] = $d->format('Y-m-d');
            if (count($wk) === 7) {
                $calWeeks[] = $wk;
                $wk = [];
            }
            $d->modify('+1 day');
        }
    }

    $GRID_START = 7 * 60;
    $GRID_END = 20 * 60;
    $GRID_SPAN = $GRID_END - $GRID_START;

    if (!function_exists('toMinutes')) {
        function toMinutes($t) {
            $parts = explode(':', $t);
            return (int)$parts[0] * 60 + (int)$parts[1];
        }
    }
    if (!function_exists('timelineLeft')) {
        function timelineLeft($t, $GRID_START, $GRID_SPAN) {
            return max(0, min(100, (toMinutes($t) - $GRID_START) / $GRID_SPAN * 100));
        }
    }
    if (!function_exists('timelineWidth')) {
        function timelineWidth($s, $e, $GRID_SPAN) {
            return max(1, min(100, (toMinutes($e) - toMinutes($s)) / $GRID_SPAN * 100));
        }
    }

    $nowMin = (int)date('H') * 60 + (int)date('i');
    $isToday = ($viewDate === date('Y-m-d'));
    $isPastDay = ($viewDate < date('Y-m-d'));
    $nowPct = ($isToday && $nowMin >= $GRID_START && $nowMin <= $GRID_END) ? (($nowMin - $GRID_START) / $GRID_SPAN * 100) : -1;

    $colorMap = [
        'room-blue' => ['dot' => '#185FA5', 'light' => '#E6F1FB'],
        'room-green' => ['dot' => '#1D9E75', 'light' => '#E1F5EE'],
        'room-amber' => ['dot' => '#BA7517', 'light' => '#FAEEDA'],
        'room-teal' => ['dot' => '#1D9E75', 'light' => '#E1F5EE'],
        'room-red' => ['dot' => '#A32D2D', 'light' => '#FCEBEB'],
        'room-orange' => ['dot' => '#BA7517', 'light' => '#FFF3E0'],
        'room-purple' => ['dot' => '#534AB7', 'light' => '#EEEDFE'],
        'room-yellow' => ['dot' => '#BA7517', 'light' => '#FFFDE7'],
    ];
    $roomEmojis = ['🛋️', '🔐', '🎓', '🌊', '⭐', '🏢', '💡', '📋'];

    $ts_prev = Carbon\Carbon::parse($viewDate);
    $ts_next = Carbon\Carbon::parse($viewDate);
    if ($viewMode === 'week') {
        $prevNavDate = $ts_prev->subWeek()->startOfWeek()->format('Y-m-d');
        $nextNavDate = $ts_next->addWeek()->startOfWeek()->format('Y-m-d');
    } elseif ($viewMode === 'month') {
        $prevNavDate = $ts_prev->subMonth()->startOfMonth()->format('Y-m-d');
        $nextNavDate = $ts_next->addMonth()->startOfMonth()->format('Y-m-d');
    } else {
        $prevNavDate = $ts_prev->subDay()->format('Y-m-d');
        $nextNavDate = $ts_next->addDay()->format('Y-m-d');
    }
    $prevNav = route('hr.home', ['date' => $prevNavDate, 'view' => $viewMode]);
    $nextNav = route('hr.home', ['date' => $nextNavDate, 'view' => $viewMode]);

    $navLabel = '';
    $ts = strtotime($viewDate);
    if ($viewMode === 'week') {
        $navLabel = date('d M', strtotime($rangeStart)) . ' – ' . date('d M Y', strtotime($rangeEnd));
    } elseif ($viewMode === 'month') {
        $navLabel = date('F Y', $ts);
    } else {
        $navLabel = date('l, d M Y', $ts);
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Meeting Room Availability — HR Admin System</title>
@include('partials.favicons')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<script>!function(){var t=localStorage.getItem('fjb-theme')||localStorage.getItem('color-theme')||localStorage.getItem('theme');if(t==='dark')document.documentElement.classList.add('dark');}();</script>
<style>
  body { background: var(--bg); font-family: 'DM Sans', sans-serif; margin: 0; color: var(--text); }
  .pub-topbar { background: var(--surface); border-bottom: 1px solid var(--border); padding: .75rem 2rem; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100; box-shadow: 0 1px 2px rgba(0,0,0,.03); }
  .pub-brand  { display: flex; align-items: center; gap: .75rem; font-weight: 800; font-size: 1.1rem; color: #0f172a; letter-spacing: -0.01em; }
  .pub-login-btn { background: #0f172a; color: #fff; border: none; padding: .6rem 1.25rem; border-radius: 10px; font-size: .875rem; font-weight: 700; cursor: pointer; text-decoration: none; transition: all .2s; display: flex; align-items: center; gap: .5rem; }
  .pub-login-btn:hover { background: #1e293b; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(15,23,42,0.15); }
  
  .pub-notice { background: #eff6ff; color: #1e40af; padding: .75rem 1.5rem; display: flex; align-items: center; justify-content: center; gap: .75rem; font-size: .875rem; border-bottom: 1px solid #dbeafe; font-weight: 500; }
  .pub-notice strong { font-weight: 700; color: #1d4ed8; }
  .pub-notice a { color: #2563eb; font-weight: 700; text-decoration: underline; cursor: pointer; }

  .pub-container { max-width: 1200px; margin: 0 auto; padding: 2.5rem 1.5rem; }
  
  .pub-hero { text-align: center; margin-bottom: 3rem; max-width: 700px; margin-left: auto; margin-right: auto; }
  .pub-hero h1 { font-size: 2.25rem; font-weight: 800; color: #0f172a; margin: 0 0 .75rem; letter-spacing: -0.02em; }
  .pub-hero p  { color: #64748b; font-size: 1.05rem; margin: 0; line-height: 1.6; }

  /* Controls */
  .pub-controls { display: flex; align-items: center; justify-content: center; gap: 1.5rem; margin-bottom: 2.5rem; flex-wrap: wrap; }
  .pub-view-tabs { display: flex; background: #e2e8f0; padding: .3rem; border-radius: 12px; gap: .2rem; }
  .pvt-btn { padding: .5rem 1.5rem; border-radius: 9px; font-size: .875rem; font-weight: 700; color: #64748b; text-decoration: none; transition: all .2s; }
  .pvt-active { background: var(--surface); color: var(--text); box-shadow: 0 2px 8px rgba(0,0,0,.08); }

  .pub-date-nav { display: flex; align-items: center; gap: .5rem; background: var(--surface); padding: .3rem; border-radius: 12px; border: 1px solid var(--border); }
  .pub-nav-btn { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 9px; color: #64748b; transition: all .2s; border: none; background: transparent; cursor: pointer; }
  .pub-nav-btn:hover { background: #f1f5f9; color: #0f172a; }
  
  .pub-datepicker-lbl { display: flex; align-items: center; gap: .5rem; padding: 0 .75rem; cursor: pointer; font-weight: 700; font-size: .95rem; color: #0f172a; }
  .pub-datepicker-lbl input { border: none; font-family: inherit; font-size: inherit; font-weight: inherit; color: inherit; padding: 0; cursor: pointer; outline: none; background:transparent; width: 130px; }

  /* Stats Bar */
  .pub-day-stats { display: flex; gap: 1.25rem; margin-bottom: 2.5rem; justify-content: center; flex-wrap: wrap; }
  .pub-dstat { background: var(--surface); border: 1px solid var(--border); padding: 1.25rem 2rem; border-radius: 16px; display: flex; flex-direction: column; align-items: center; min-width: 140px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
  .pub-dstat-num { font-size: 1.75rem; font-weight: 800; color: #0f172a; line-height: 1; }
  .pub-dstat-lbl { font-size: .7rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .06em; margin-top: .4rem; }
  .pub-dstat-green .pub-dstat-num { color: #10b981; }
  .pub-dstat-blue .pub-dstat-num  { color: #3b82f6; }
  .pub-dstat-red .pub-dstat-num   { color: #ef4444; }

  /* Room Grid */
  .rb-rooms-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }
  .rb-room-card { background: var(--surface); border-radius: 20px; border: 1px solid var(--border); overflow: hidden; transition: all .3s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column; }
  .rb-room-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.06); border-color: #cbd5e1; }
  
  .rb-card-header { padding: 1.5rem; display: flex; gap: 1rem; align-items: flex-start; }
  .rb-room-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
  .rb-room-info { flex: 1; }
  .rb-room-name { font-size: 1.15rem; font-weight: 800; color: #0f172a; letter-spacing: -0.01em; }
  .rb-room-meta { font-size: .82rem; color: #64748b; margin-top: .3rem; display: flex; gap: .75rem; align-items: center; }
  
  .rb-avail-badge { padding: .3rem .75rem; border-radius: 99px; font-size: .75rem; font-weight: 700; }
  .rb-badge-free { background: #ecfdf5; color: #059669; }
  .rb-badge-busy { background: #fef2f2; color: #dc2626; }

  .rb-bookings-list { padding: 0 1.5rem 1.25rem; flex: 1; }
  .rb-booking-record { padding: .75rem; background: var(--bg); border-radius: 12px; margin-bottom: .5rem; display: flex; align-items: center; gap: .75rem; border: 1px solid var(--border); }
  .rb-record-time { font-size: .8rem; font-weight: 800; color: var(--text); background: var(--surface); padding: .4rem .6rem; border-radius: 8px; border: 1px solid var(--border); min-width: 50px; text-align: center; }
  .rb-record-info { flex: 1; min-width: 0; }
  .rb-record-purp { font-size: .82rem; font-weight: 600; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .rb-record-by   { font-size: .7rem; color: #64748b; margin-top: .1rem; }

  .rb-card-footer { padding: 1.25rem 1.5rem 1.5rem; border-top: 1px solid var(--border); }
  .rb-book-btn { width: 100%; background: #0f172a; color: #fff; border: none; padding: .75rem; border-radius: 12px; font-weight: 700; font-size: .9rem; cursor: pointer; transition: all .2s; display: flex; align-items: center; justify-content: center; gap: .5rem; }
  .rb-book-btn:hover { background: #1e293b; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }

  /* Schedule List */
  .pub-schedule-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; margin-bottom: 2.5rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
  .pub-sch-hd { padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: .75rem; cursor: pointer; user-select: none; }
  .pub-sch-hd span:first-child { font-weight: 800; color: #0f172a; font-size: 1.05rem; }
  .pub-sch-badge { background: #f1f5f9; color: #475569; padding: .25rem .75rem; border-radius: 99px; font-size: .75rem; font-weight: 700; }
  .pub-sch-caret { margin-left: auto; color: #94a3b8; transition: transform .2s; font-size: 1.2rem; }
  .pub-sch-caret.collapsed { transform: rotate(-90deg); }
  .pub-sch-body { border-top: 1px solid #f1f5f9; padding: .5rem 0; }
  .pub-sch-row { display: flex; align-items: center; padding: .75rem 1.5rem; gap: 1.25rem; }
  .pub-sch-time { min-width: 65px; font-size: .9rem; font-weight: 800; color: #0f172a; display: flex; flex-direction: column; }
  .pub-sch-end { font-size: .75rem; font-weight: 500; color: #94a3b8; margin-top: -2px; }
  .pub-sch-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; position: relative; }
  .pub-sch-dot::after { content: ''; position: absolute; inset: -4px; border-radius: 50%; background: inherit; opacity: .15; }
  .pub-sch-info { flex: 1; }
  .pub-sch-room { font-size: .9rem; font-weight: 700; color: #0f172a; }
  .pub-sch-purp { font-size: .82rem; color: #64748b; margin-top: .15rem; }
  .pub-sch-stat { font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; }

  /* Modal Enhancements */
  .modal-box { border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid rgba(255,255,255,0.1); }
  .modal-header { padding: 1.75rem 2rem 1.25rem; border: none; }
  .modal-header h3 { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em; }
  .modal-footer { padding: 1.5rem 2rem 2rem; border: none; background: transparent; }
  .rb-step-badge { width: 28px; height: 28px; background: #0f172a; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: .85rem; font-weight: 800; }
  .rb-step-title { font-size: 1.05rem; font-weight: 800; color: #0f172a; letter-spacing: -0.01em; }
  .rb-modal-section { padding: 0 2rem 1.5rem; }
  .rb-modal-step-header { display: flex; align-items: center; gap: .75rem; margin-bottom: 1.25rem; }
  
  .form-group label { font-size: .75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .05em; margin-bottom: .5rem; }
  .form-group input, .form-group select { border-radius: 12px; border: 1.5px solid #e2e8f0; padding: .75rem 1rem; font-weight: 500; }
  .form-group input:focus { border-color: #0f172a; box-shadow: 0 0 0 4px rgba(15,23,42,0.05); }

  /* ── Staff Login modal: matched login styling ── */
  #loginConfirmModal .form-group label { text-transform: none; letter-spacing: 0; font-size: .8rem; font-weight: 600; color: #1e293b; }
  #loginConfirmModal .lcm-input-wrap { position: relative; }
  #loginConfirmModal .lcm-input-wrap > svg.lead {
    position: absolute; left: .9rem; top: 50%; transform: translateY(-50%);
    color: #64748b; pointer-events: none;
  }
  #loginConfirmModal .lcm-input-wrap input {
    width: 100%; padding: .8rem 1rem .8rem 2.6rem;
    background: var(--form-input-bg); border: 1.5px solid var(--border); border-radius: 11px;
    font-size: .95rem; font-weight: 500;
    transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
  }
  #loginConfirmModal .lcm-input-wrap input::placeholder { color: #94a3b8; }
  #loginConfirmModal .lcm-input-wrap input:focus {
    outline: none; background: var(--surface); border-color: #38bdf8;
    box-shadow: 0 0 0 4px rgba(56,189,248,.16);
  }
  #loginConfirmModal .lcm-input-wrap input.has-toggle { padding-right: 2.9rem; }
  #loginConfirmModal .lcm-toggle {
    position: absolute; right: .35rem; top: 50%; transform: translateY(-50%);
    background: none; border: none; padding: .5rem; cursor: pointer;
    color: #64748b; display: flex; align-items: center; border-radius: 8px;
  }
  #loginConfirmModal .lcm-toggle:hover { color: #1e293b; }
  #loginConfirmModal .btn-primary {
    background: linear-gradient(135deg, #1a4b8c, #0284c7); border: none; color: #fff;
    display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
    box-shadow: 0 10px 22px -8px rgba(2,132,199,.6);
    transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
  }
  #loginConfirmModal .btn-primary:hover { filter: brightness(1.06); box-shadow: 0 14px 26px -8px rgba(2,132,199,.7); }
  #loginConfirmModal .btn-primary:active { transform: translateY(1px); }

  .rb-ro-check { border-radius: 50%; border: 2.5px solid #e2e8f0; }
  .rb-room-option.active .rb-ro-check { border-color: #0f172a; background: #0f172a; box-shadow: inset 0 0 0 3px #fff; }
  .rb-room-option { border-radius: 14px; border: 1.5px solid var(--border); padding: .85rem 1rem; transition: all .2s; }
  .rb-room-option:hover { border-color: #cbd5e1; background: var(--bg); }
  .rb-room-option.active { border-color: #0f172a; background: var(--bg); }

  .rb-dur-pill { border-radius: 10px; border: 1.5px solid var(--border); padding: .4rem 1rem; font-weight: 700; color: var(--muted); background: var(--surface); }
  .rb-dur-pill:hover { border-color: #cbd5e1; background: var(--bg); }
  .rb-dur-pill.active { border-color: #0f172a; background: #0f172a; color: #fff; }

  .rb-room-options-list { display: flex; flex-direction: column; gap: .5rem; max-height: 240px; overflow-y: auto; padding-right: .5rem; }
  .rb-room-selection { display: flex; align-items: center; gap: 1rem; padding: .85rem 1.25rem; border: 1.5px solid var(--border); border-radius: 16px; cursor: pointer; transition: all .2s ease; background: var(--surface); position: relative; }
  .rb-room-selection:hover { border-color: #cbd5e1; background: var(--bg); transform: translateX(4px); }
  .rb-room-selection.active { border-color: #0f172a; background: var(--bg); box-shadow: 0 4px 12px rgba(15,23,42,0.05); }
  
  .rb-rs-radio { width: 20px; height: 20px; border: 2px solid #cbd5e1; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all .2s; }
  .rb-room-selection.active .rb-rs-radio { border-color: #0f172a; }
  .rb-rs-inner { width: 10px; height: 10px; background: #0f172a; border-radius: 50%; transform: scale(0); transition: transform .2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
  .rb-room-selection.active .rb-rs-inner { transform: scale(1); }

  .rb-rs-info { flex: 1; }
  .rb-rs-name { font-weight: 800; color: #0f172a; font-size: .95rem; }
  .rb-rs-meta { font-size: .78rem; color: #64748b; font-weight: 500; }
  .rb-rs-emoji { font-size: 1.25rem; opacity: .8; }

  .rb-modal-summary { background: var(--bg); border: 1.5px solid var(--border); border-radius: 16px; padding: 1.25rem; color: var(--text); line-height: 1.5; font-weight: 500; }
  .rb-cart-item { border-radius: 14px; padding: 1rem; border: 1.5px solid var(--border); background: var(--surface); }
  .rb-mocc-pill { background: var(--surface); border: 1.5px solid #ffedd5; color: #9a3412; padding: .3rem .6rem; border-radius: 8px; font-size: .75rem; font-weight: 700; }

  .clock-display { background:var(--surface); border:1.5px solid var(--border); border-radius:12px; padding:.65rem .9rem; cursor:pointer; display:flex; align-items:center; justify-content:space-between; gap:.5rem; transition:all .2s; }
  .clock-display:hover { border-color:#3b82f6; background:var(--bg); }
  .clock-display.open  { border-color:#0f172a; box-shadow:0 0 0 4px rgba(15,23,42,0.06); }
  .clock-lbl  { font-size:.6rem; font-weight:800; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:.1rem; }
  .clock-val  { font-size:1.15rem; font-weight:800; color:var(--text); font-variant-numeric:tabular-nums; line-height:1; }
  .clock-val.unset { color:var(--border); }
  .clock-dropdown    { display:none; position:fixed; width:230px; background:var(--surface); border:1px solid var(--border); border-radius:14px; box-shadow:0 12px 30px rgba(0,0,0,.12); z-index:400; padding:.75rem; }
  #clockBackdrop     { display:none; position:fixed; inset:0; background:rgba(15,23,42,.5); z-index:9998; }
  .clock-section-lbl { font-size:.6rem; font-weight:800; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:.4rem; }
  .clock-divider     { height:1px; background:var(--border); margin:.55rem -.75rem .55rem; }
  .clock-hour-grid   { display:grid; grid-template-columns:repeat(4,1fr); gap:.3rem; }
  .clock-min-row     { display:grid; grid-template-columns:repeat(4,1fr); gap:.3rem; }
  .clock-btn         { padding:.45rem .2rem; text-align:center; border-radius:8px; cursor:pointer; font-weight:700; color:#475569; font-size:.82rem; border:1.5px solid transparent; transition:all .12s; }
  .clock-btn:hover:not(.disabled) { background:#eff6ff; border-color:#bfdbfe; color:#1e40af; }
  .clock-btn.h-selected { background:#e0f2fe; color:#0369a1; border-color:#bae6fd; }
  .clock-btn.selected   { background:#0f172a; color:#fff; border-color:#0f172a; }
  .clock-btn.disabled   { opacity:.28; cursor:not-allowed; pointer-events:none; }

  /* Animations */
  @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
  .rb-room-card { animation: fadeInUp .4s ease backwards; }
  @for ($i = 1; $i <= 20; $i++)
    .rb-room-card:nth-child({{ $i }}) { animation-delay: {{ $i * 0.05 }}s; }
  @endfor

  /* Day Legend */
  .pub-day-legend { display: flex; align-items: center; gap: 1.25rem; margin-bottom: .75rem; padding: 0 .25rem; font-size: .75rem; font-weight: 600; color: #64748b; }
  .pub-leg-dot { width: 10px; height: 10px; border-radius: 2px; display: inline-block; vertical-align: middle; margin-right: .4rem; }

  /* View Legends (Week/Month) */
  .pub-view-legend { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap; }
  .pvl-item { display: flex; align-items: center; gap: .4rem; font-size: .75rem; font-weight: 600; color: #64748b; }
  .pvl-item::before { content: ''; width: 12px; height: 12px; border-radius: 3px; }
  .pvl-free::before  { background: var(--surface); border: 1px solid var(--border); }
  .pvl-light::before { background: #f0fdf4; border: 1px solid #bbf7d0; }
  .pvl-mod::before   { background: #dcfce7; border: 1px solid #86efac; }
  .pvl-busy::before  { background: #22c55e; }

  /* Timeline Labels */
  .rb-timeline-labels { display: flex; justify-content: space-between; padding: .4rem .25rem 0; font-size: .68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; }

  /* Week Table */
  .pwt-wrap { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; overflow-x: auto; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
  .pwt-head { display: flex; border-bottom: 2px solid var(--border); min-width: 800px; }
  .pwt-room-col { width: 180px; flex-shrink: 0; padding: 1rem; display: flex; align-items: center; gap: .6rem; border-right: 1px solid var(--border); }
  .pwt-day-hd { flex: 1; padding: .75rem; text-align: center; display: flex; flex-direction: column; gap: .1rem; border-right: 1px solid var(--border); }
  .pwt-day-name { font-size: .7rem; font-weight: 800; text-transform: uppercase; color: var(--muted); letter-spacing: .05em; }
  .pwt-day-num  { font-size: .9rem; font-weight: 700; color: var(--text); }
  .pwt-today { background: #eff6ff; }
  .pwt-today .pwt-day-num { color: #2563eb; }
  .pwt-row { display: flex; border-bottom: 1px solid var(--border); min-width: 800px; transition: background .15s; }
  .pwt-row:hover { background: var(--bg); }
  .pwt-row:last-child { border-bottom: none; }
  .pwt-cell { flex: 1; border-right: 1px solid var(--border); position: relative; display: flex; align-items: center; justify-content: center; text-decoration: none; min-height: 52px; }
  .pwt-cnt { width: 22px; height: 22px; border-radius: 50%; background: rgba(255,255,255,.9); display: flex; align-items: center; justify-content: center; font-size: .72rem; font-weight: 800; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
  .pwt-free  { background: var(--surface); }
  .pwt-light { background: #f0fdf4; color: #166534; }
  .pwt-mod   { background: #dcfce7; color: #15803d; }
  .pwt-busy  { background: #22c55e; color: #fff; }
  .pwt-busy .pwt-cnt { color: #15803d; }
  .pwt-today-cell { background-color: rgba(37, 99, 235, 0.03); }
  .pwt-room-ico { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: .9rem; }
  .pwt-room-nm  { font-size: .85rem; font-weight: 700; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

  /* Month Grid */
  .pmc-wrap { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
  .pmc-head-row { display: grid; grid-template-columns: repeat(7, 1fr); background: var(--table-head-bg); border-bottom: 1px solid var(--border); }
  .pmc-day-name { padding: .75rem; text-align: center; font-size: .72rem; font-weight: 800; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; }
  .pmc-week-row { display: grid; grid-template-columns: repeat(7, 1fr); border-bottom: 1px solid var(--border); }
  .pmc-week-row:last-child { border-bottom: none; }
  .pmc-day { min-height: 100px; padding: .5rem; border-right: 1px solid var(--border); text-decoration: none; transition: background .15s; position: relative; }
  .pmc-day:last-child { border-right: none; }
  .pmc-day:hover { background: var(--bg); }
  .pmc-day-num { font-size: .9rem; font-weight: 700; color: var(--muted); margin-bottom: .25rem; }
  .pmc-today .pmc-day-num { color: #2563eb; background: #eff6ff; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
  .pmc-other { opacity: 0.4; background: var(--bg); }
  .pmc-bkg-cnt { font-size: .7rem; font-weight: 800; color: var(--text); background: var(--border); padding: .15rem .4rem; border-radius: 4px; display: inline-block; margin-bottom: .15rem; }
  .pmc-free-cnt { font-size: .65rem; color: #94a3b8; font-weight: 600; }
  .pmc-busy  { background: #f0fdf4; }
  .pmc-mod   { background: #f0fdf4; }
  .pmc-light { background: #f0fdf4; }
  .pmc-today { background: #f0f9ff; }

  /* Scroll hints */
  .pub-scroll-hint { display: none; font-size: .7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin: 0 0 .5rem; text-align: center; }
  @media (max-width: 800px) { .pub-scroll-hint { display: block; } }

  .pub-sys-label { display: inline; }
  @media (max-width: 480px) { .pub-sys-label { display: none; } }

  /* Mobile Adjustments */
  @media (max-width: 640px) {
    .pub-topbar { padding: .75rem 1rem; }
    .pub-brand { font-size: .9rem; }
    .pub-brand img { height: 28px !important; width: 28px !important; }
    .pub-login-btn { padding: .4rem .85rem; font-size: .8rem; }
    .pub-container { padding: 1rem .75rem; }
    .pub-hero h1 { font-size: 1.2rem; }
    .pub-hero p { font-size: .82rem; }
    .pub-controls { flex-direction: column; align-items: stretch; gap: .75rem; }
    .pub-view-tabs { order: 2; }
    .pub-date-nav { order: 1; justify-content: space-between; }
    .rb-today-btn { order: 3; width: 100%; justify-content: center; }
    .pub-day-stats { grid-template-columns: 1fr 1fr; }
    .pub-day-legend { flex-wrap: wrap; gap: .75rem; }
  }

  /* Cart Styles */
  .rb-cart-item { display:flex; align-items:center; gap:.75rem; padding:.6rem .85rem; background:var(--surface); border:1.5px solid var(--border); border-radius:10px; margin-bottom:.4rem; }
  .rb-cart-info { flex:1; font-size:.82rem; line-height:1.3; }
  .rb-cart-rm { background:none; border:none; color:#dc2626; cursor:pointer; font-size:1.1rem; padding:0 .2rem; }

  /* Booking summary */
  .rb-modal-summary { padding: .75rem 1rem; background: var(--bg); border-radius: 10px; border: 1px solid var(--border); font-size: .82rem; color: var(--text); line-height: 1.4; }

  @media (max-width:640px) {
    .rb-room-options { grid-template-columns: 1fr; }

    /* ── Guest booking modal: make it fit the phone, no sideways scroll ── */
    #guestBookModal .modal-box,
    #loginConfirmModal .modal-box { border-radius: 18px; overflow-x: hidden; }

    /* Tighter header / section / footer padding (was 2rem sides) */
    #guestBookModal .modal-header,
    #loginConfirmModal .modal-header { padding: 1.1rem 1.1rem .9rem !important; }
    #guestBookModal .modal-header h3,
    #loginConfirmModal .modal-header h3 { font-size: 1.15rem; }
    .rb-modal-section { padding: 0 1.1rem 1.1rem !important; }
    .modal-footer { padding: 1rem 1.1rem 1.25rem !important; }
    .guest-pad { padding-left: 1.1rem !important; padding-right: 1.1rem !important; }
    .lcm-pad { margin-left: 1.1rem !important; margin-right: 1.1rem !important; }
    .lcm-form-pad { padding-left: 1.1rem !important; padding-right: 1.1rem !important; }

    /* Booking Date, Start Time, End Time each on their own full-width row */
    .guest-time-grid { grid-template-columns: 1fr !important; gap: .9rem !important; }
    .guest-det-grid { grid-template-columns: 1fr 1fr !important; gap: .6rem !important; }
    /* Roomier Start/End clock boxes to match the logged-in modal */
    #guestBookModal .clock-display { padding: .8rem 1rem; }

    /* Time picker pops up centered in the middle of the screen on mobile */
    .clock-dropdown { width: min(320px, 90vw) !important; z-index: 9999 !important; box-shadow: 0 16px 48px rgba(0,0,0,.22); }

    /* Let grid/flex children shrink instead of forcing overflow */
    .guest-time-grid > *,
    .guest-det-grid > *,
    .rb-rs-info { min-width: 0 !important; }
    #guestBookModal input,
    #loginConfirmModal input { width: 100%; min-width: 0 !important; }

    /* Room rows: tighter, drop the hover slide that can poke past the edge */
    .rb-room-selection { padding: .7rem .85rem !important; gap: .7rem !important; }
    .rb-room-selection:hover { transform: none !important; }
    .rb-rs-name { font-size: .88rem; }

    .rb-dur-pill { padding: .4rem .8rem; }
  }
</style>
</head>
<body>

@if(session('success'))
<div style="background:#dcfce7;border-bottom:1px solid #bbf7d0;padding:.65rem 1.5rem;font-size:.875rem;color:#166534;display:flex;align-items:center;gap:.5rem;">
  ✓ {{ session('success') }}
</div>
@endif

@if(session('error'))
<div style="background:#fee2e2;border-bottom:1px solid #fecaca;padding:.65rem 1.5rem;font-size:.875rem;color:#991b1b;display:flex;align-items:center;gap:.5rem;">
  ✕ {{ session('error') }}
</div>
@endif

<div class="pub-topbar">
  <div class="pub-brand">
    <div style="background:var(--surface);padding:5px;border-radius:10px;display:flex;border:1px solid var(--border);">
      <img src="{{ asset('assets/images/logo.png') }}" alt="FJB" style="height:28px;width:28px;object-fit:contain;">
    </div>
    <span>HR and Administration Management Information System</span>
  </div>
  <div style="display:flex;align-items:center;gap:.75rem;">
    <div style="display:flex;align-items:center;gap:5px;">
      <span class="pub-sys-label" style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-right:2px;">System</span>
      <span style="display:inline-flex;align-items:center;padding:4px 11px;border-radius:20px;border:1.5px solid #0f172a;font-size:11px;font-weight:700;color:#0f172a;background:#f1f5f9;cursor:default;">HR</span>
      <a href="{{ url('/wt') }}" style="display:inline-flex;align-items:center;padding:4px 11px;border-radius:20px;border:1.5px solid var(--border);font-size:11px;font-weight:600;color:var(--muted);text-decoration:none;background:var(--surface);transition:all .2s;">WT</a>
      <a href="{{ url('/it/login') }}" style="display:inline-flex;align-items:center;padding:4px 11px;border-radius:20px;border:1.5px solid var(--border);font-size:11px;font-weight:600;color:var(--muted);text-decoration:none;background:var(--surface);transition:all .2s;">IT</a>
    </div>
    <a class="pub-login-btn" href="{{ route('login') }}">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
      Staff Login
    </a>
  </div>
</div>

<div class="pub-notice">
  <span>✨</span>
  <span>You're viewing as a <strong>guest</strong>. Browse availability freely — <a href="{{ route('login') }}">login</a> to book a room.</span>
</div>

<div class="pub-container">
  <div class="pub-hero">
    <h1>Reserve your space</h1>
    <p>Select a room below to see real-time availability and submit your booking request instantly.</p>
  </div>

  <!-- Controls: view toggle + date nav -->
  <div class="pub-controls">
    <div class="pub-view-tabs">
      <a href="{{ route('hr.home', ['date' => $viewDate, 'view' => 'day']) }}"   class="pvt-btn{{ $viewMode==='day'  ?' pvt-active':'' }}">Day</a>
      <a href="{{ route('hr.home', ['date' => $viewDate, 'view' => 'week']) }}"  class="pvt-btn{{ $viewMode==='week' ?' pvt-active':'' }}">Week</a>
      <a href="{{ route('hr.home', ['date' => $viewDate, 'view' => 'month']) }}" class="pvt-btn{{ $viewMode==='month'?' pvt-active':'' }}">Month</a>
    </div>
    
    <div class="pub-date-nav">
      <a href="{{ $prevNav }}" class="pub-nav-btn" title="Previous">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
      </a>
      <label class="pub-datepicker-lbl">
        <input type="date" id="rbViewDatePicker" value="{{ $viewDate }}"
               onchange="window.location.href='{{ route('hr.home') }}?date='+this.value+'&view={{ $viewMode }}'">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:#64748b;margin-left:auto;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      </label>
      <a href="{{ $nextNav }}" class="pub-nav-btn" title="Next">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
      </a>
    </div>
    
    <a href="{{ route('hr.home', ['date' => date('Y-m-d'), 'view' => $viewMode]) }}"
       class="btn btn-outline" style="border-radius:12px;padding:.5rem 1.25rem;">Today</a>

    <button id="pub-theme-toggle" onclick="pubToggleTheme()" title="Toggle dark / light mode"
            style="width:40px;height:40px;border-radius:12px;border:1px solid var(--border);background:var(--surface);cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .15s,border-color .15s;">
        <svg id="pub-icon-moon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
        <svg id="pub-icon-sun" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" style="display:none;"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
    </button>
  </div>


@if ($viewMode === 'day')
  <!-- Day stats bar -->
  @php
    $totalBooked  = $dayBookings->filter(fn($b) => $b->status !== 'Rejected')->count();
    $inUseNow     = $isToday ? $dayBookings->filter(fn($b) =>
        $b->status === 'Approved' &&
        toMinutes($b->start_time) <= $nowMin && toMinutes($b->end_time) > $nowMin)->count() : 0;
  @endphp
  <div class="pub-day-stats">
    <div class="pub-dstat">
      <span class="pub-dstat-num">{{ $rooms->count() }}</span>
      <span class="pub-dstat-lbl">Total Rooms</span>
    </div>
    <div class="pub-dstat pub-dstat-green">
      <span class="pub-dstat-num">{{ $freeCount }}</span>
      <span class="pub-dstat-lbl">Available Now</span>
    </div>
    <div class="pub-dstat pub-dstat-blue">
      <span class="pub-dstat-num">{{ $totalBooked }}</span>
      <span class="pub-dstat-lbl">Bookings Today</span>
    </div>
    @if ($isToday)
    <div class="pub-dstat {{ $inUseNow > 0 ? 'pub-dstat-red' : '' }}">
      <span class="pub-dstat-num">{{ $inUseNow }}</span>
      <span class="pub-dstat-lbl">Currently in Use</span>
    </div>
    @endif
  </div>

  <!-- Schedule list -->
  @php
    $sortedBkgs = $dayBookings->filter(fn($b) => $b->status !== 'Rejected')->sortBy('start_time');
    $scheduledCount = $sortedBkgs->count();
  @endphp
  <div class="pub-schedule-card" id="pubScheduleCard">
    <div class="pub-sch-hd" onclick="pubToggleSchedule()">
      <div style="background:#f1f5f9;width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;">📋</div>
      <span>{{ $isToday ? "Today's Schedule" : date('d M Y', strtotime($viewDate)).' Schedule' }}</span>
      <span class="pub-sch-badge">{{ $scheduledCount }} event{{ $scheduledCount!==1?'s':'' }}</span>
      <span class="pub-sch-caret" id="pubSchCaret">▾</span>
    </div>
    <div class="pub-sch-body" id="pubSchBody">
      @if ($sortedBkgs->isEmpty())
      <div style="padding:3rem 1.5rem;text-align:center;">
        <div style="font-size:2rem;margin-bottom:1rem;">☕</div>
        <div style="font-weight:800;font-size:1.1rem;color:#0f172a;margin-bottom:.5rem;">Quiet day ahead</div>
        <div style="color:#64748b;font-size:.9rem;">No meetings scheduled for this date. All rooms are ready to book!</div>
      </div>
      @else
      @foreach ($sortedBkgs as $b)
        @php
          $isPending   = in_array($b->status,['Pending','CancelRequested']);
          $statusLabel = match($b->status) { 'Approved'=>'Confirmed','CancelRequested'=>'Pending Cancel',default=>'Pending Review' };
          $sColor      = $isPending ? '#eab308' : '#10b981';
        @endphp
      <div class="pub-sch-row">
        <div class="pub-sch-time">
          {{ date('H:i', strtotime($b->start_time)) }}
          <span class="pub-sch-end">{{ date('H:i', strtotime($b->end_time)) }}</span>
        </div>
        <div class="pub-sch-dot" style="background:{{ $sColor }}"></div>
        <div class="pub-sch-info">
          <div class="pub-sch-room">{{ $b->room->name }}</div>
          <div class="pub-sch-purp">{{ $b->purpose }}</div>
        </div>
        <span class="pub-sch-stat" style="color:{{ $sColor }};">{{ $statusLabel }}</span>
      </div>
      @endforeach
      @endif
    </div>
  </div>

  <!-- Legend -->
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;padding:0 .5rem;">
    <div class="pub-day-legend">
      <span><span class="pub-leg-dot" style="background:#10b981;"></span>Confirmed</span>
      <span><span class="pub-leg-dot" style="background:#eab308;"></span>Pending</span>
    </div>
    <div style="font-size:.85rem;font-weight:700;color:#0f172a;">
      <span style="color:#10b981;">{{ $freeCount }}</span> rooms available
    </div>
  </div>
  <div class="rb-rooms-grid">
  @foreach ($rooms as $i => $rm)
    @php
      $rid    = $rm->id;
      $rmBkgs = $bookingsByRoom[$rid] ?? collect();
      $clr    = $colorMap[$rm->color_class] ?? $colorMap['room-blue'];
      $emoji  = $roomEmojis[$i % count($roomEmojis)];
      $isBusy = $isToday && $rmBkgs->filter(fn($b) =>
          in_array($b->status,['Approved','Pending']) &&
          toMinutes($b->start_time) <= $nowMin && toMinutes($b->end_time) > $nowMin)->isNotEmpty();
      $activeCount = $rmBkgs->filter(fn($b) => in_array($b->status,['Approved','Pending','CancelRequested']))->count();
      $picNames = $rm->pics->pluck('name')->join(', ');
    @endphp
  <div class="rb-room-card" id="roomCard_{{ $rm->id }}">
    <div class="rb-card-header">
      <div class="rb-room-icon" style="background:{{ $clr['light'] }}">{{ $emoji }}</div>
      <div class="rb-room-info">
        <div class="rb-room-name">{{ $rm->name }}</div>
        <div class="rb-room-meta">
          <span>👥 Up to {{ (int)$rm->capacity }}</span>
          @if ($picNames)
          <span title="PIC">🔑 {{ $picNames }}</span>
          @endif
        </div>
      </div>
      <span class="rb-avail-badge {{ $isBusy?'rb-badge-busy':'rb-badge-free' }}">{{ $isBusy?'In Use':'Available' }}</span>
    </div>

    <div class="rb-bookings-list">
      @php $visible = $rmBkgs->filter(fn($b) => $b->status !== 'Rejected')->sortBy('start_time'); @endphp
      @if ($visible->isEmpty())
      <div class="rb-empty-day">
        <div style="font-size:1.5rem;margin-bottom:.5rem;">🗓️</div>
        <div style="font-weight:700;color:#0f172a;margin-bottom:.2rem;">No meetings yet</div>
        <div style="color:#64748b;font-size:.78rem;">This room is fully available.</div>
      </div>
      @else
      <div style="font-size:.75rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.75rem;padding:0 .25rem;">Schedule</div>
      @foreach ($visible as $b)
        @php
          $isPending = in_array($b->status,['Pending','CancelRequested']);
          $sColor    = $isPending ? '#854d0e' : '#10b981';
        @endphp
      <div class="rb-booking-record">
        <div class="rb-record-time">
          {{ date('H:i', strtotime($b->start_time)) }}
        </div>
        <div class="rb-record-info">
          <div class="rb-record-purp">{{ $b->purpose }}</div>
          <div class="rb-record-by" style="color:{{ $sColor }};font-weight:700;">{{ $isPending ? 'Pending Review' : 'Confirmed' }}</div>
        </div>
      </div>
      @endforeach
      @endif
    </div>

    @if ($isPastDay)
    <div class="rb-card-footer" style="background:var(--bg);text-align:center;padding:.75rem;">
      <span style="font-size:.8rem;color:#94a3b8;font-weight:600;">📅 Past date — view only</span>
    </div>
    @else
    <div class="rb-card-footer">
      <button class="rb-book-btn" onclick="openGuestBookModal({{ $rm->id }}, {{ json_encode($rm->name) }})">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/></svg>
        Quick Book
      </button>
    </div>
    @endif
  </div>
  @endforeach
  </div>

@elseif ($viewMode === 'week')
  <!-- WEEK VIEW -->
  <div class="pub-view-legend">
    <span class="pvl-item pvl-free">Free</span>
    <span class="pvl-item pvl-light">1–2 bookings</span>
    <span class="pvl-item pvl-mod">3–4 bookings</span>
    <span class="pvl-item pvl-busy">5+ bookings</span>
  </div>
  <p class="pub-scroll-hint">Swipe left/right to see all days →</p>
  <div class="pwt-wrap">
    <div class="pwt-head">
      <div class="pwt-room-col">Room</div>
      @foreach ($rangeDays as $wd)
        @php $wdTs = strtotime($wd); @endphp
      <div class="pwt-day-hd{{ $wd===date('Y-m-d')?' pwt-today':'' }}">
        <div class="pwt-day-name">{{ date('D', $wdTs) }}</div>
        <div class="pwt-day-num">{{ date('j M', $wdTs) }}</div>
      </div>
      @endforeach
    </div>
    @foreach ($rooms as $i => $rm)
      @php
        $clr   = $colorMap[$rm->color_class] ?? $colorMap['room-blue'];
        $emoji = $roomEmojis[$i % count($roomEmojis)];
      @endphp
    <div class="pwt-row">
      <div class="pwt-room-col">
        <span class="pwt-room-ico" style="background:{{ $clr['light'] }}">{{ $emoji }}</span>
        <span class="pwt-room-nm">{{ $rm->name }}</span>
      </div>
      @foreach ($rangeDays as $wd)
        @php
          $bkgs = $bookingsByRoomDate->get($rm->id)?->get($wd) ?? collect();
          $cnt  = $bkgs->count();
          if ($cnt === 0)    $cc = 'pwt-free';
          elseif ($cnt <= 2) $cc = 'pwt-light';
          elseif ($cnt <= 4) $cc = 'pwt-mod';
          else               $cc = 'pwt-busy';
        @endphp
      <a href="{{ route('hr.home', ['date' => $wd, 'view' => 'day']) }}" class="pwt-cell {{ $cc }}{{ $wd===date('Y-m-d')?' pwt-today-cell':'' }}" title="{{ $cnt }} booking{{ $cnt!==1?'s':'' }}">
        @if ($cnt > 0)<span class="pwt-cnt">{{ $cnt }}</span>@endif
      </a>
      @endforeach
    </div>
    @endforeach
  </div>

@elseif ($viewMode === 'month')
  <!-- MONTH VIEW -->
  <div class="pub-view-legend">
    <span class="pvl-item pvl-free">No bookings</span>
    <span class="pvl-item pvl-light">1–3 bookings</span>
    <span class="pvl-item pvl-mod">4–6 bookings</span>
    <span class="pvl-item pvl-busy">7+ bookings</span>
  </div>
  <div class="pmc-wrap">
    <div class="pmc-head-row">
      @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $dn)
      <div class="pmc-day-name">{{ $dn }}</div>
      @endforeach
    </div>
    @foreach ($calWeeks as $week)
    <div class="pmc-week-row">
      @foreach ($week as $day)
        @php
          $inMonth  = (substr($day,0,7) === substr($rangeStart,0,7));
          $isDay    = ($day === date('Y-m-d'));
          $bkgs     = $bookingsByDate->get($day) ?? collect();
          $bkgCnt   = $bkgs->count();
          $freeRms  = $rooms->count() - $bkgs->pluck('room_id')->unique()->count();
          if (!$inMonth)       $dc = 'pmc-other';
          elseif ($bkgCnt===0) $dc = 'pmc-free';
          elseif ($bkgCnt<=3)  $dc = 'pmc-light';
          elseif ($bkgCnt<=6)  $dc = 'pmc-mod';
          else                 $dc = 'pmc-busy';
        @endphp
      <a href="{{ route('hr.home', ['date' => $day, 'view' => 'day']) }}" class="pmc-day {{ $dc }}{{ $isDay?' pmc-today':'' }}">
        <div class="pmc-day-num">{{ (int)date('j', strtotime($day)) }}</div>
        @if ($inMonth && $bkgCnt > 0)
        <div class="pmc-bkg-cnt">{{ $bkgCnt }} booking{{ $bkgCnt>1?'s':'' }}</div>
        <div class="pmc-free-cnt">{{ $freeRms }} room{{ $freeRms!==1?'s':'' }} free</div>
        @endif
      </a>
      @endforeach
    </div>
    @endforeach
  </div>
@endif

</div><!-- .pub-container -->

<!-- Dimmed backdrop behind the centered mobile time picker (tap to close via document handler) -->
<div id="clockBackdrop"></div>

<!-- GUEST BOOKING MODAL -->
<div class="modal" id="guestBookModal">
  <div class="modal-box" style="max-width:560px;width:100%">
    <div class="modal-header" style="background:var(--table-head-bg);border-bottom:1px solid var(--border);">
      <div>
        <h3 style="margin:0;">Book <span id="guestModalRoomName" style="color:#0f172a;"></span></h3>
        <div id="guestModalRoomMeta" style="font-size:.85rem;color:#64748b;margin-top:.25rem;font-weight:500;"></div>
      </div>
      <button class="modal-close" onclick="closeModal()">×</button>
    </div>

    <div class="guest-pad" style="padding:1.5rem 2rem 0;">
      <div id="guestPastWarn" class="alert alert-warning" style="display:none;margin-bottom:1rem;border-radius:12px;">
        ⚠️ Past day cannot book
      </div>
      <div id="guestConflictWarn" class="alert alert-danger" style="display:none;margin-bottom:1rem;border-radius:12px;">
        ⚠️ This slot is already taken.
      </div>
      <div id="guestOccSlots" class="rb-modal-occ" style="display:none;margin-bottom:1.25rem;padding:1rem;background:#fff7ed;border:1px solid #ffedd5;border-radius:12px;">
        <div style="font-size:.75rem;font-weight:800;color:#9a3412;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem;">Occupied Times</div>
        <div id="guestOccList" style="display:flex;flex-wrap:wrap;gap:.4rem;"></div>
      </div>
    </div>

    <!-- STEP 1: TIME -->
    <div class="rb-modal-section">
      <div class="rb-modal-step-header">
        <span class="rb-step-badge">1</span>
        <span class="rb-step-title">Choose Date &amp; Time</span>
      </div>
      <div class="guest-time-grid" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1.25rem;">
        <div class="form-group guest-date-col">
          <label>Booking Date</label>
          <input type="date" id="guestDate" value="{{ $viewDate }}" min="{{ date('Y-m-d') }}" onchange="buildTimePicker('start'); buildTimePicker('end');guestCheckConflict();guestUpdateSummary();guestRefreshOccupied();guestCheckPastDate()">
        </div>
        <div class="form-group" style="position:relative;">
          <label>Start Time</label>
          <div class="clock-display" id="startDisplay" onclick="toggleClock('start')">
            <div>
              <div class="clock-lbl">From</div>
              <span class="clock-val unset" id="startVal">--:--</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <div class="clock-dropdown" id="startDrop">
            <div class="clock-section-lbl">Hour</div>
            <div class="clock-hour-grid" id="startHourGrid"></div>
            <div class="clock-divider"></div>
            <div class="clock-section-lbl">Minute</div>
            <div class="clock-min-row" id="startMinRow"></div>
          </div>
          <input type="hidden" id="guestStart">
        </div>
        <div class="form-group" style="position:relative;">
          <label>End Time</label>
          <div class="clock-display" id="endDisplay" onclick="toggleClock('end')">
            <div>
              <div class="clock-lbl">To</div>
              <span class="clock-val unset" id="endVal">--:--</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <div class="clock-dropdown" id="endDrop">
            <div class="clock-section-lbl">Hour</div>
            <div class="clock-hour-grid" id="endHourGrid"></div>
            <div class="clock-divider"></div>
            <div class="clock-section-lbl">Minute</div>
            <div class="clock-min-row" id="endMinRow"></div>
          </div>
          <input type="hidden" id="guestEnd">
        </div>
      </div>
      <div style="display:flex;justify-content:space-between;align-items:center;">
        <div class="rb-dur-pills">
          <span class="rb-dur-pill" onclick="guestSetDuration(30,this)">30m</span>
          <span class="rb-dur-pill" onclick="guestSetDuration(60,this)">1h</span>
          <span class="rb-dur-pill" onclick="guestSetDuration(120,this)">2h</span>
        </div>
        <button type="button" id="guestFullDay" class="btn btn-ghost btn-sm" onclick="guestSetFullDay()" style="display:none;color:#0f172a;font-weight:700;">☀️ Full Day</button>       
      </div>
    </div>

    <!-- STEP 2: DETAILS -->
    <div class="rb-modal-section">
      <div class="rb-modal-step-header">
        <span class="rb-step-badge">2</span>
        <span class="rb-step-title">Meeting Details</span>
      </div>

      <div class="form-group" style="margin-bottom:1.25rem;">
        <label>Select Room</label>
        <div class="rb-room-options-list">
          @foreach ($rooms as $rm)
            @php $clr = $colorMap[$rm->color_class] ?? $colorMap['room-blue']; @endphp
            <label class="rb-room-selection" id="guestOptLabel_{{ $rm->id }}">
              <input type="radio" name="guest_room_radio" value="{{ $rm->id }}" id="guestRadio_{{ $rm->id }}" onchange="guestOnRoomChange(this.value)" style="display:none;">
              <div class="rb-rs-radio">
                <div class="rb-rs-inner"></div>
              </div>
              <div class="rb-rs-info">
                <div class="rb-rs-name">{{ $rm->name }}</div>
                <div class="rb-rs-meta">👥 {{ $rm->capacity }} seats</div>
              </div>
              <div class="rb-rs-emoji">{{ $roomEmojis[$loop->index % count($roomEmojis)] }}</div>
            </label>
          @endforeach
        </div>
      </div>
      
      <div class="guest-det-grid" style="display:grid;grid-template-columns:120px 1fr;gap:1rem;margin-bottom:1.25rem;">
        <div class="form-group">
          <label>Attendees</label>
          <input type="number" id="guestAttendees" min="1" max="500" placeholder="5" oninput="guestCheckCapacity()">
        </div>
        <div class="form-group">
          <label>Purpose</label>
          <input type="text" id="guestPurpose" placeholder="e.g. Weekly Sync" oninput="guestUpdateSummary()">
        </div>
      </div>
      
      <div id="guestCapacityHint" style="font-size:.78rem;margin-top:-.75rem;margin-bottom:1.25rem;font-weight:600;"></div>
      
      <div class="rb-modal-summary" id="guestSummary" style="display:none;margin-bottom:1.25rem;border-left:4px solid #0f172a;"></div>
      
      <button type="button" class="btn btn-outline" onclick="guestAddToCart()" style="width:100%;justify-content:center;height:48px;border-radius:14px;border-width:2px;font-size:.95rem;font-weight:800;border-color:#0f172a;color:#0f172a;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Add to Booking List
      </button>
    </div>

    <!-- CART / BOOKING LIST -->
    <div class="rb-modal-section" id="guestCartWrap" style="display:none;background:var(--bg);padding-top:1.5rem;padding-bottom:1.5rem;border-top:1px solid var(--border);border-bottom:1px solid var(--border);">
      <div style="font-size:.8rem;font-weight:800;color:#0f172a;text-transform:uppercase;letter-spacing:.05em;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;">
        <div style="background:#0f172a;color:#fff;width:24px;height:24px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.8rem;">✓</div>
        Your Booking List
      </div>
      <div id="guestCartList" style="display:flex;flex-direction:column;gap:.75rem;"></div>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-ghost" onclick="closeModal()" style="font-weight:700;">Cancel</button>
      <button type="button" id="guestSubmitBtn" class="btn btn-primary" onclick="guestProceedToLogin()" style="min-width:200px;display:none;height:48px;border-radius:14px;font-size:.95rem;box-shadow:0 10px 15px -3px rgba(15,23,42,0.2);">
        Sign In to Confirm
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:8px;"><polyline points="9 18 15 12 9 6"></polyline></svg>
      </button>
    </div>
  </div>
</div>

<!-- LOGIN / CONFIRM MODAL -->
<div class="modal" id="loginConfirmModal">
  <div class="modal-box" style="max-width:480px;width:100%">
    <div class="modal-header" style="background:var(--table-head-bg);border-bottom:1px solid var(--border);">
      <div>
        <h3 id="lcmTitle" style="margin:0;">Verify Identity</h3>
        <div id="lcmSubtitle" style="font-size:.85rem;color:#64748b;margin-top:.25rem;font-weight:500;">Please sign in to confirm your booking.</div>
      </div>
      <button class="modal-close" onclick="closeModal()">×</button>
    </div>

    <!-- Booking summary card -->
    <div id="lcmSummary" class="lcm-pad" style="margin:1.5rem 2rem 0;padding:1.25rem;background:#f0f9ff;border-radius:16px;border:1.5px solid #e0f2fe;">
      <div style="font-size:.75rem;font-weight:800;color:#0369a1;text-transform:uppercase;letter-spacing:.06em;margin-bottom:1rem;display:flex;align-items:center;gap:.4rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Booking Details
      </div>
      <div style="display:flex;flex-direction:column;gap:.6rem;">
        <div class="lcm-row" style="display:flex;justify-content:space-between;font-size:.875rem;"><span style="color:#64748b;">Room</span><span id="lcmRoom" style="font-weight:700;color:#0f172a;"></span></div>
        <div class="lcm-row" style="display:flex;justify-content:space-between;font-size:.875rem;"><span style="color:#64748b;">Date</span><span id="lcmDate" style="font-weight:700;color:#0f172a;"></span></div>
        <div class="lcm-row" style="display:flex;justify-content:space-between;font-size:.875rem;"><span style="color:#64748b;">Time</span><span id="lcmTime" style="font-weight:700;color:#0f172a;"></span></div>
        <div class="lcm-row" style="display:flex;justify-content:space-between;font-size:.875rem;"><span style="color:#64748b;">Purpose</span><span id="lcmPurpose" style="font-weight:700;color:#0f172a;max-width:200px;text-align:right;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span></div>
      </div>
    </div>

    <!-- Login form -->
    <form method="POST" action="{{ route('rooms.bookings.store') }}" class="lcm-form-pad" style="padding:1.5rem 2rem 0;">
      @csrf
      <input type="hidden" name="action"            value="add">
      <input type="hidden" name="room_id"           id="lcmPendingRoom">
      <input type="hidden" name="booking_date"      id="lcmPendingDate">
      <input type="hidden" name="start_time"        id="lcmPendingStart">
      <input type="hidden" name="end_time"          id="lcmPendingEnd">
      <input type="hidden" name="purpose"           id="lcmPendingPurpose">
      <input type="hidden" name="attendees"         id="lcmPendingAttendees">
      <input type="hidden" name="redirect_date"     id="lcmRedirectDate">
      <input type="hidden" name="pending_room"      id="lcmLegacyRoom">
      <input type="hidden" name="pending_date"      id="lcmLegacyDate">
      <input type="hidden" name="pending_start"     id="lcmLegacyStart">
      <input type="hidden" name="pending_end"       id="lcmLegacyEnd">
      <input type="hidden" name="pending_purpose"   id="lcmLegacyPurpose">
      <input type="hidden" name="pending_attendees" id="lcmLegacyAttendees">
      <input type="hidden" name="pending_rooms"     id="lcmPendingRooms">
      <input type="hidden" name="pending_dates"     id="lcmPendingDates">
      <input type="hidden" name="slots"             id="lcmPendingSlots">

      @if ($errors->any())
      <div class="alert alert-danger" style="margin-bottom:.75rem;">
          @foreach ($errors->all() as $error)
              <div>{{ $error }}</div>
          @endforeach
      </div>
      @endif

      <div class="form-group" style="margin-bottom:.75rem;">
        <label>Staff ID</label>
        <div class="lcm-input-wrap">
          <svg class="lead" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <input type="text" name="staff_no" placeholder="e.g. 0000001" required autocomplete="username"
                 autocapitalize="none" autocorrect="off" spellcheck="false" value="{{ old('staff_no') }}">
        </div>
      </div>
      <div class="form-group" style="margin-bottom:.75rem;">
        <label>Password</label>
        <div class="lcm-input-wrap">
          <svg class="lead" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" name="password" class="has-toggle" placeholder="Enter your password" required autocomplete="current-password">
          <button type="button" class="lcm-toggle" aria-label="Show password" onclick="lcmTogglePassword(this)">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>
      <div style="font-size:.78rem;color:#64748b;margin-bottom:.5rem;text-align:right;">
        <a href="{{ route('password.request') }}" style="color:#0284c7;font-weight:600;">Forgot password?</a>
      </div>

      <div class="modal-footer" style="padding:0;margin-top:.25rem;border:none;">
        <button type="button" class="btn btn-ghost" id="lcmBackBtn" onclick="lcmBack()">← Back</button>
        <button type="submit" class="btn btn-primary">
          Sign In
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="modalOverlay" onclick="closeModal()"></div>

<script src="{{ asset('assets/js/app.js') }}"></script>
<script>
// Schedule toggle
function pubToggleSchedule() {
    const body  = document.getElementById('pubSchBody');
    const caret = document.getElementById('pubSchCaret');
    if (!body) return;
    const open = body.style.display !== 'none';
    body.style.display = open ? 'none' : '';
    caret.classList.toggle('collapsed', open);
}

const guestBookings = @json($allRangeBookings);
const guestRooms    = @json($rooms);
const guestViewDate = '{{ $viewDate }}';
const isPastDay     = {{ $isPastDay ? 'true' : 'false' }};
const guestColorMap = @json($colorMap);
const guestEmojis   = @json($roomEmojis);
let guestRoomId = null;
let clockState  = { start:{h:null,m:null}, end:{h:null,m:null} };
let guestCart   = [];

// Time helpers
function gToMin(t) { if(!t) return 0; const [h,m]=t.substring(0,5).split(':').map(Number); return h*60+m; }
function gGetNowMin() { const n=new Date(); return n.getHours()*60+n.getMinutes(); }
function gIsToday(date) { return date===new Date().toISOString().slice(0,10); }
function gIsPastDate(date) { return date < new Date().toISOString().slice(0,10); }
function dateFmt(d) { return new Date(d+'T00:00:00').toLocaleDateString('en-GB', {day:'numeric',month:'short'}); }

function gIsStartDisabled(h, m) {
    const tMin = h*60+m;
    const date = document.getElementById('guestDate').value;
    if (gIsToday(date) && tMin <= gGetNowMin()) return true;
    if (!guestRoomId || !date) return false;
    const t = String(h).padStart(2,'0')+':'+String(m).padStart(2,'0');
    const dbConflict = guestBookings.some(b =>
        b.room_id == guestRoomId && b.booking_date === date &&
        !['Rejected'].includes(b.status) &&
        (b.start_time||'').substring(0,5) <= t && t < (b.end_time||'').substring(0,5)
    );
    if (dbConflict) return true;
    return guestCart.some(s =>
        s.room_id == guestRoomId && s.booking_date === date &&
        s.start_time <= t && t < s.end_time
    );
}

function gIsEndDisabled(h, m) {
    const tMin = h*60+m;
    const date = document.getElementById('guestDate').value;
    const startH = clockState.start.h, startM = clockState.start.m;
    const startMin = (startH!==null&&startM!==null) ? startH*60+startM : null;
    if (startMin !== null && tMin <= startMin) return true;
    if (gIsToday(date) && tMin <= gGetNowMin()) return true;
    if (!guestRoomId || !date) return false;
    if (startMin !== null) {
        const startT = String(startH).padStart(2,'0')+':'+String(startM).padStart(2,'0');
        const t = String(h).padStart(2,'0')+':'+String(m).padStart(2,'0');
        const dbConflict = guestBookings.some(b =>
            b.room_id == guestRoomId && b.booking_date === date &&
            !['Rejected'].includes(b.status) &&
            startT < (b.end_time||'').substring(0,5) && t > (b.start_time||'').substring(0,5)
        );
        if (dbConflict) return true;
        const cartConflict = guestCart.some(s =>
            s.room_id == guestRoomId && s.booking_date === date &&
            startMin < gToMin(s.end_time) && tMin > gToMin(s.start_time)
        );
        if (cartConflict) return true;
    }
    return false;
}

// Clock picker
function buildTimePicker(which) {
    const hGrid = document.getElementById(which + 'HourGrid');
    if (!hGrid) return;
    hGrid.innerHTML = '';
    for (let h = 7; h <= 20; h++) {
        const allOff = [0,15,30,45].every(m => which==='start' ? gIsStartDisabled(h,m) : gIsEndDisabled(h,m));
        const isSel = clockState[which].h === h;
        const d = document.createElement('div');
        d.className = 'clock-btn'
            + (allOff ? ' disabled' : '')
            + (isSel && clockState[which].m === null ? ' h-selected' : '')
            + (isSel && clockState[which].m !== null ? ' selected' : '');
        d.textContent = String(h).padStart(2,'0');
        if (!allOff) d.onclick = () => gPickHour(which, h);
        hGrid.appendChild(d);
    }
    const mRow = document.getElementById(which + 'MinRow');
    mRow.innerHTML = '';
    [0, 15, 30, 45].forEach(m => {
        const h = clockState[which].h;
        const isOff = h !== null ? (which==='start' ? gIsStartDisabled(h,m) : gIsEndDisabled(h,m)) : false;
        const isSel = clockState[which].m === m;
        const d = document.createElement('div');
        d.className = 'clock-btn' + (isOff ? ' disabled' : '') + (isSel ? ' selected' : '');
        d.textContent = String(m).padStart(2,'0');
        if (!isOff) d.onclick = () => gPickMinute(which, m);
        mRow.appendChild(d);
    });
}

function gPickHour(which, h) {
    clockState[which].h = h;
    if (clockState[which].m !== null) {
        const off = which==='start' ? gIsStartDisabled(h, clockState[which].m) : gIsEndDisabled(h, clockState[which].m);
        if (off) clockState[which].m = null;
    }
    buildTimePicker(which);
    if (clockState[which].m === null) {
        const firstM = [0,15,30,45].find(m => !(which==='start' ? gIsStartDisabled(h,m) : gIsEndDisabled(h,m)));
        if (firstM !== undefined) gPickMinute(which, firstM);
    }
}

function gPickMinute(which, m) {
    if (clockState[which].h === null) return;
    clockState[which].m = m;
    const t = String(clockState[which].h).padStart(2,'0') + ':' + String(m).padStart(2,'0');
    document.getElementById('guest' + (which==='start' ? 'Start' : 'End')).value = t;
    const valEl = document.getElementById(which + 'Val');
    valEl.textContent = t;
    valEl.classList.remove('unset');
    document.getElementById(which + 'Drop').style.display = 'none';
    document.getElementById(which + 'Display').classList.remove('open');
    document.getElementById('clockBackdrop').style.display = 'none';
    if (which === 'start' && clockState.end.h === null) {
        const eh = Math.min(clockState.start.h + 1, 20);
        clockState.end.h = eh;
        buildTimePicker('end');
        const targetM = !gIsEndDisabled(eh, m) ? m : [0,15,30,45].find(mm => !gIsEndDisabled(eh,mm));
        if (targetM !== undefined) { gPickMinute('end', targetM); return; }
    }
    buildTimePicker(which);
    guestCheckConflict(); guestUpdateSummary();
}

function gSetClockValue(which, h, m) {
    clockState[which] = {h, m};
    buildTimePicker(which);
    gPickMinute(which, m);
}

function toggleClock(which) {
    const drop = document.getElementById(which + 'Drop');
    const disp = document.getElementById(which + 'Display');
    const isOpen = drop.style.display === 'block';
    document.querySelectorAll('.clock-dropdown').forEach(d => d.style.display = 'none');
    document.querySelectorAll('.clock-display').forEach(d => d.classList.remove('open'));
    document.getElementById('clockBackdrop').style.display = 'none';
    if (!isOpen) {
        buildTimePicker(which);
        if (window.innerWidth <= 768) {
            // Mobile: center the picker in the middle of the screen.
            // Reparent to <body> so it escapes the modal's stacking context
            // and renders above the dimmed backdrop.
            document.body.appendChild(drop);
            drop.style.top = '50%';
            drop.style.left = '50%';
            drop.style.transform = 'translate(-50%, -50%)';
            document.getElementById('clockBackdrop').style.display = 'block';
        } else {
            const rect = disp.getBoundingClientRect();
            drop.style.top  = (rect.bottom + 6) + 'px';
            drop.style.left = rect.left + 'px';
            drop.style.transform = '';
        }
        drop.style.display = 'block';
        disp.classList.add('open');
    }
}

document.addEventListener('click', e => {
    if (!e.target.closest('.clock-display') && !e.target.closest('.clock-dropdown')) {
        document.querySelectorAll('.clock-dropdown').forEach(d => d.style.display='none');
        document.querySelectorAll('.clock-display').forEach(d => d.classList.remove('open'));
        document.getElementById('clockBackdrop').style.display = 'none';
    }
});

// Past date check
function guestCheckPastDate() {
    const date = document.getElementById('guestDate').value;
    const warn = document.getElementById('guestPastWarn');
    if (warn) warn.style.display = gIsPastDate(date) ? 'block' : 'none';
}

// Conflict check
function guestCheckConflict() {
    const date  = document.getElementById('guestDate').value;
    const start = document.getElementById('guestStart').value;
    const end   = document.getElementById('guestEnd').value;
    if (!guestRoomId||!date||!start||!end) return false;
    const dbConflict = guestBookings.some(b =>
        b.room_id==guestRoomId && b.booking_date===date &&
        !['Rejected'].includes(b.status) &&
        start < (b.end_time||'').substring(0,5) && end > (b.start_time||'').substring(0,5)
    );
    const cartConflict = guestCart.some(s =>
        s.room_id==guestRoomId && s.booking_date===date &&
        start < s.end_time && end > s.start_time
    );
    const conflict = dbConflict || cartConflict;
    document.getElementById('guestConflictWarn').style.display = conflict ? 'block' : 'none';
    return conflict;
}

// Occupied slots
function guestRefreshOccupied() {
    const date = document.getElementById('guestDate').value;
    const list = document.getElementById('guestOccList');
    const wrap = document.getElementById('guestOccSlots');
    const fullDayBtn = document.getElementById('guestFullDay');
    if (!guestRoomId || !date) return;

    const dbOcc   = guestBookings.filter(b => b.room_id == guestRoomId && b.booking_date === date && b.status !== 'Rejected');
    const cartOcc = guestCart.filter(s => s.room_id == guestRoomId && s.booking_date === date);
    const all     = [...dbOcc, ...cartOcc].sort((a,b) => gToMin(a.start_time) - gToMin(b.start_time));

    let hasAnyFree = false;
    for (let h = 7; h <= 19; h++) {
        for (const m of [0,15,30,45]) {
            if (!gIsStartDisabled(h, m)) { hasAnyFree = true; break; }
        }
        if (hasAnyFree) break;
    }

    if (!hasAnyFree) {
        wrap.style.display = 'block'; wrap.style.borderColor = '#dc2626'; wrap.style.background = '#fef2f2';
        fullDayBtn.style.display = 'none';
        list.innerHTML = '<div style="color:#dc2626;font-weight:700;width:100%;text-align:center;padding:.5rem;">🚫 Fully booked, please choose another option.</div>';
        return;
    }
    if (all.length === 0) {
        wrap.style.display = 'none';
        fullDayBtn.style.display = gIsToday(date) ? 'none' : 'inline-flex';
    } else {
        wrap.style.display = 'block'; wrap.style.borderColor = '#e2e8f0'; wrap.style.background = '#fff7ed';
        fullDayBtn.style.display = 'none';
        list.innerHTML = '';
        all.forEach(b => {
            const pill = document.createElement('span');
            pill.className = 'rb-mocc-pill';
            pill.textContent = b.start_time.substring(0,5) + ' – ' + b.end_time.substring(0,5);
            list.appendChild(pill);
        });
    }
}

// Full day
function guestSetFullDay() {
    gSetClockValue('start', 7, 0);
    gSetClockValue('end', 20, 0);
}

// Capacity & summary
function guestCheckCapacity() {
    const attendees = parseInt(document.getElementById('guestAttendees').value)||0;
    const hint = document.getElementById('guestCapacityHint');
    if (!guestRoomId || !attendees) { hint.textContent=''; return true; }
    const rm = guestRooms.find(r => r.id==guestRoomId);
    if (!rm) { hint.textContent=''; return true; }
    const cap = parseInt(rm.capacity);
    if (attendees > cap) {
        hint.innerHTML = `<span style="color:#dc2626;font-weight:600;">⚠️ Exceeds limit — max ${cap} seats</span>`;
        return false;
    }
    hint.innerHTML = `<span style="color:#16a34a;">${cap-attendees} of ${cap} seat(s) remaining</span>`;
    return true;
}

function guestUpdateSummary() {
    const date  = document.getElementById('guestDate').value;
    const start = document.getElementById('guestStart').value;
    const end   = document.getElementById('guestEnd').value;
    const purp  = document.getElementById('guestPurpose').value.trim();
    const el    = document.getElementById('guestSummary');
    if (!guestRoomId||!date||!start||!end) { el.style.display='none'; return; }
    const rm = guestRooms.find(r => r.id==guestRoomId);
    if (!rm) { el.style.display='none'; return; }
    const [sh,sm] = start.split(':').map(Number);
    const [eh,em] = end.split(':').map(Number);
    const d = (eh*60+em)-(sh*60+sm);
    const dur = d>0 ? (Math.floor(d/60)?(Math.floor(d/60)+'h '):'')+(d%60?(d%60+'m'):'') : '';
    el.innerHTML = '<strong>'+rm.name+'</strong> · '+date+' · '+start+'–'+end+(dur?' ('+dur.trim()+')':'')+(purp?'<br><span style="color:#94a3b8">"'+purp.substring(0,70)+(purp.length>70?'…':'')+'"</span>':'');
    el.style.display='block';
}

// Room change (inside modal)
function guestOnRoomChange(val) {
    guestRoomId = parseInt(val);
    const rm = guestRooms.find(r => r.id == guestRoomId);
    document.getElementById('guestModalRoomName').textContent = rm ? rm.name : '';
    
    let picNames = '';
    if (rm && rm.pics) {
        picNames = rm.pics.map(p => p.name).join(', ');
    }
    
    document.getElementById('guestModalRoomMeta').textContent = rm ? '👥 Up to ' + rm.capacity + ' seats' + (picNames ? ' · PIC: ' + picNames : '') : '';
    
    // Toggle active class on selection cards
    document.querySelectorAll('.rb-room-selection').forEach(el => el.classList.remove('active'));
    const lbl = document.getElementById('guestOptLabel_' + val);
    if (lbl) lbl.classList.add('active');
    
    guestCheckCapacity();
    guestCheckConflict();
    guestRefreshOccupied();
    buildTimePicker('start'); buildTimePicker('end');
    guestUpdateSummary();
}

// Open booking modal
function openGuestBookModal(roomId, roomName) {
    if (isPastDay) return;
    guestRenderCart();

    // Automatically select the room
    guestRoomId = parseInt(roomId);
    const radio = document.getElementById('guestRadio_' + roomId);
    if (radio) { radio.checked = true; }
    
    // Initialize room details (capacity, name, PIC, conflicts, etc.)
    guestOnRoomChange(roomId);

    document.getElementById('guestConflictWarn').style.display='none';
    document.getElementById('guestPastWarn').style.display='none';
    document.getElementById('guestSummary').style.display='none';
    document.getElementById('guestDate').value = guestViewDate;
    document.getElementById('guestStart').value = '';
    document.getElementById('guestEnd').value = '';
    document.getElementById('guestPurpose').value = '';
    document.getElementById('guestAttendees').value = '';
    document.getElementById('guestCapacityHint').textContent = '';
    const svEl = document.getElementById('startVal'); svEl.textContent = '--:--'; svEl.classList.add('unset');
    const evEl = document.getElementById('endVal');   evEl.textContent = '--:--'; evEl.classList.add('unset');
    clockState = {start:{h:null,m:null}, end:{h:null,m:null}};
    document.querySelectorAll('.rb-dur-pill').forEach(p => p.classList.remove('active'));
    buildTimePicker('start'); buildTimePicker('end');
    guestRefreshOccupied();

    if (gIsToday(guestViewDate)) {
        const nowMin  = gGetNowMin();
        const nextMin = Math.ceil((nowMin+1)/15)*15;
        const sh = Math.floor(nextMin/60), sm = nextMin%60;
        if (sh < 20) {
            gSetClockValue('start', sh, sm);
            const endMin = nextMin+60;
            if (endMin <= 20*60) gSetClockValue('end', Math.floor(endMin/60), endMin%60);
        }
    }
    guestCheckPastDate();
    openModal('guestBookModal');
}

// Duration pills
function guestSetDuration(mins, el) {
    document.querySelectorAll('.rb-dur-pill').forEach(p => p.classList.remove('active'));
    el.classList.add('active');
    const st = document.getElementById('guestStart').value;
    if (!st) return;
    const [h,m] = st.split(':').map(Number);
    const endMin = h*60+m+mins;
    if (endMin <= 20*60) gSetClockValue('end', Math.floor(endMin/60), endMin%60);
}

// Cart management
function guestAddToCart() {
    const date  = document.getElementById('guestDate').value;
    const start = document.getElementById('guestStart').value;
    const end   = document.getElementById('guestEnd').value;
    const purp  = document.getElementById('guestPurpose').value.trim();
    const att   = parseInt(document.getElementById('guestAttendees').value)||0;

    if (!guestRoomId)            { alert('Please select a meeting room.'); return; }
    if (!date || !start || !end) { alert('Select date and times.'); return; }
    if (gIsPastDate(date))       { alert('Past day cannot book.'); return; }
    if (!purp)                   { alert('Enter meeting purpose.'); return; }
    if (!att)                    { alert('Enter attendees.'); return; }
    if (guestCheckConflict())    { alert('Time slot conflict. Choose a different time.'); return; }
    if (!guestCheckCapacity())   { alert('Exceeds room capacity. Please reduce attendees or choose a larger room.'); return; }

    const rm = guestRooms.find(r => r.id == guestRoomId);
    guestCart.push({ room_id: guestRoomId, room_name: rm ? rm.name : 'Room #'+guestRoomId, booking_date: date, start_time: start, end_time: end, purpose: purp, attendees: att });
    guestRenderCart();
    guestRefreshOccupied();
    buildTimePicker('start'); buildTimePicker('end');
    document.querySelectorAll('.rb-dur-pill').forEach(p => p.classList.remove('active'));
    document.getElementById('guestStart').value = '';
    document.getElementById('guestEnd').value = '';
    const _sv = document.getElementById('startVal'); _sv.textContent = '--:--'; _sv.classList.add('unset');
    const _ev = document.getElementById('endVal');   _ev.textContent = '--:--'; _ev.classList.add('unset');
    clockState = {start:{h:null,m:null}, end:{h:null,m:null}};
    document.getElementById('guestSummary').style.display='none';
}

function guestRenderCart() {
    const wrap   = document.getElementById('guestCartWrap');
    const list   = document.getElementById('guestCartList');
    const subBtn = document.getElementById('guestSubmitBtn');
    if (guestCart.length === 0) { wrap.style.display='none'; subBtn.style.display='none'; return; }
    wrap.style.display = 'block'; subBtn.style.display = 'flex'; list.innerHTML = '';
    guestCart.forEach((s, idx) => {
        const item = document.createElement('div');
        item.className = 'rb-cart-item';
        item.innerHTML = `<div class="rb-cart-info"><strong>${s.room_name}</strong> · ${dateFmt(s.booking_date)}<br><span style="color:#64748b">${s.start_time}–${s.end_time} · ${s.purpose.substring(0,30)}${s.purpose.length>30?'…':''}</span></div><button type="button" class="rb-cart-rm" onclick="guestRemoveFromCart(${idx})">✕</button>`;
        list.appendChild(item);
    });
}

function guestRemoveFromCart(idx) {
    guestCart.splice(idx, 1);
    guestRenderCart();
    guestRefreshOccupied();
    buildTimePicker('start'); buildTimePicker('end');
}

// Login modal (direct, no booking)
function openLoginModal() {
    document.getElementById('lcmTitle').textContent    = 'Staff Login';
    document.getElementById('lcmSubtitle').textContent = 'Sign in to access the HR system';
    document.getElementById('lcmSummary').style.display = 'none';
    document.getElementById('lcmBackBtn').style.display = 'none';
    ['lcmPendingRoom','lcmPendingDate','lcmPendingStart','lcmPendingEnd','lcmPendingPurpose','lcmPendingAttendees',
     'lcmLegacyRoom','lcmLegacyDate','lcmLegacyStart','lcmLegacyEnd','lcmLegacyPurpose','lcmLegacyAttendees',
     'lcmPendingRooms','lcmPendingDates','lcmPendingSlots'].forEach(id => {
        const el = document.getElementById(id); if (el) el.value = '';
    });
    openModal('loginConfirmModal');
}

function lcmTogglePassword(btn) {
    const input = btn.parentElement.querySelector('input');
    const showing = input.type === 'text';
    input.type = showing ? 'password' : 'text';
    btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
    btn.innerHTML = showing
      ? '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>'
      : '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
}

let lcmSourceModal = 'guestBookModal';
function lcmBack() { closeModal(); openModal(lcmSourceModal); }

// Proceed to login (from booking cart)
function guestProceedToLogin() {
    if (guestCart.length === 0) {
        const date  = document.getElementById('guestDate').value;
        const start = document.getElementById('guestStart').value;
        if (date && start && guestRoomId) guestAddToCart();
    }
    if (guestCart.length === 0) {
        alert('Your booking list is empty. Add at least one slot before signing in.');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("rooms.bookings.hold") }}';

    const csrf = document.createElement('input');
    csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);

    const slots = document.createElement('input');
    slots.type = 'hidden'; slots.name = 'slots'; slots.value = JSON.stringify(guestCart);
    form.appendChild(slots);

    document.body.appendChild(form);
    form.submit();
}

function pubToggleTheme() {
    var isDark = document.documentElement.classList.contains('dark');
    var next = isDark ? 'light' : 'dark';
    ['fjb-theme','color-theme','theme'].forEach(function(k){ localStorage.setItem(k, next); });
    document.documentElement.classList.toggle('dark', !isDark);
    document.documentElement.setAttribute('data-theme', next);
    document.documentElement.style.colorScheme = next;
    var moon = document.getElementById('pub-icon-moon');
    var sun  = document.getElementById('pub-icon-sun');
    if (moon) moon.style.display = !isDark ? 'none' : '';
    if (sun)  sun.style.display  = !isDark ? '' : 'none';
}

(function(){
    var dark = document.documentElement.classList.contains('dark');
    document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
    document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
    var moon = document.getElementById('pub-icon-moon');
    var sun  = document.getElementById('pub-icon-sun');
    if (moon) moon.style.display = dark ? 'none' : '';
    if (sun)  sun.style.display  = dark ? '' : 'none';
})();

</script>
</body>
</html>
