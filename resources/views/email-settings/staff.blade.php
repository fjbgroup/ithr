@extends('layouts.app')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

@section('title', 'Email Notifications')
@section('page_title', 'Email Notifications')

@section('content')

<style>
.es-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 12px rgba(0,0,0,.05);margin-bottom:20px}
.es-head{padding:16px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:12px}
.es-head-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0}
.es-notify-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;padding:16px 20px 4px}
.es-notify-tile{border:1px solid #e2e8f0;background:#f8fafc;border-radius:12px;padding:14px;min-height:136px;display:flex;flex-direction:column;gap:10px}
.es-notify-icon{width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0}
.es-notify-title{font-size:13px;font-weight:800;color:#1e293b;line-height:1.25}
.es-notify-copy{font-size:12px;color:#64748b;line-height:1.55;margin:0}
.es-notify-chips{display:flex;gap:6px;flex-wrap:wrap;margin-top:auto}
.es-notify-chip{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;border-radius:999px;padding:4px 8px;border:1px solid}
.es-note{margin:10px 20px 16px;padding:11px 13px;border:1px solid #bae6fd;background:#f0f9ff;border-radius:10px;font-size:12px;color:#0369a1;display:flex;gap:8px;line-height:1.55}
html.dark .es-card{background:var(--surface)!important;border-color:var(--border)!important;box-shadow:0 1px 3px rgba(0,0,0,.24),0 8px 22px rgba(0,0,0,.18)!important}
html.dark .es-head{background:var(--surface)!important;border-bottom-color:var(--border)!important}
html.dark .es-card [style*="color:#1e293b"],
html.dark .es-card [style*="color:#334155"]{color:var(--text)!important}
html.dark .es-card [style*="color:#64748b"],
html.dark .es-card [style*="color:#94a3b8"]{color:#cbd5e1!important}
html.dark .es-card [style*="border-bottom:1px solid #f8fafc"]{border-bottom-color:var(--border)!important}
html.dark .es-notify-tile{background:#111827;border-color:#334155}
html.dark .es-notify-title{color:var(--text)}
html.dark .es-notify-copy{color:#cbd5e1}
html.dark .es-note{background:rgba(14,165,233,.12);border-color:rgba(56,189,248,.28);color:#bae6fd}
@media (max-width: 991.98px){.es-notify-grid{grid-template-columns:1fr}}
</style>

<!-- HEADER -->
<div style="margin-bottom:24px">
  <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:5px">Settings â€º <span style="color:#0284c7">Email Notifications</span></div>
  <h4 style="font-family:'Inter',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0">My Email Notifications</h4>
  <p style="font-size:13px;color:var(--muted);margin:4px 0 0">You'll receive email updates when your requests are approved or rejected.</p>
</div>

<div class="row g-4">
<div class="col-lg-6">

  <!-- Email address status -->
  <div class="es-card">
    <div class="es-head">
      <div class="es-head-icon" style="background:#e0f2fe;color:#0284c7"><i class="bi bi-person-fill"></i></div>
      <div>
        <div style="font-size:14px;font-weight:700;color:#1e293b">Your Notification Email</div>
        <div style="font-size:12px;color:#64748b;margin-top:1px">The address emails will be sent to</div>
      </div>
    </div>
    <div style="padding:20px 22px">
      @if(!empty($authUser->email))
        <div style="display:flex;align-items:center;gap:12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px 16px">
          <div style="width:38px;height:38px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="bi bi-envelope-check-fill" style="color:#16a34a;font-size:17px"></i>
          </div>
          <div>
            <div style="font-size:13px;font-weight:700;color:#15803d">Email configured</div>
            <div style="font-size:12px;color:#166534;margin-top:2px">{{ $authUser->email }}</div>
          </div>
        </div>
        @if(!$configured)
        <div style="margin-top:12px;display:flex;gap:8px;background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;padding:10px 13px;font-size:12px;color:#c2410c">
          <i class="bi bi-exclamation-triangle-fill" style="flex-shrink:0;margin-top:1px"></i>
          <span>The system mail server is not yet configured by the admin. Emails will not be sent until it is set up.</span>
        </div>
        @endif
      @else
        <div style="display:flex;align-items:center;gap:12px;background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:14px 16px">
          <div style="width:38px;height:38px;border-radius:50%;background:#ffedd5;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="bi bi-envelope-x-fill" style="color:#d97706;font-size:17px"></i>
          </div>
          <div>
            <div style="font-size:13px;font-weight:700;color:#c2410c">No email on your profile</div>
            <div style="font-size:12px;color:#92400e;margin-top:2px">You won't receive email notifications until you add one.</div>
          </div>
        </div>
        <div style="margin-top:14px">
          <a href="{{ route('it.profile') }}"
            style="display:inline-flex;align-items:center;gap:8px;background:#142b47;color:#fff;border-radius:9px;padding:10px 20px;font-size:13px;font-weight:700;text-decoration:none">
            <i class="bi bi-person-fill"></i> Go to My Profile
          </a>
          <div style="margin-top:8px;font-size:11px;color:#94a3b8">Add your email address in your profile to start receiving notifications.</div>
        </div>
      @endif
    </div>
  </div>

  <!-- SMTP status -->
  <div class="es-card">
    <div class="es-head">
      <div class="es-head-icon" style="{{ $configured ? 'background:#dcfce7;color:#16a34a' : 'background:#fff7ed;color:#d97706' }}"><i class="bi bi-{{ $configured ? 'check-circle-fill' : 'exclamation-triangle-fill' }}"></i></div>
      <div>
        <div style="font-size:14px;font-weight:700;color:#1e293b">System Mail Status</div>
        <div style="font-size:12px;color:#64748b;margin-top:1px">Whether the mail server is ready to send</div>
      </div>
    </div>
    <div style="padding:16px 22px">
      @if($configured)
        <div style="font-size:13px;color:#15803d;font-weight:600"><i class="bi bi-check-circle-fill"></i> Mail server is configured and ready.</div>
        <div style="font-size:12px;color:#64748b;margin-top:6px">Emails will be sent automatically when your requests are decided.</div>
      @else
        <div style="font-size:13px;color:#c2410c;font-weight:600"><i class="bi bi-exclamation-triangle-fill"></i> Mail server is not configured.</div>
        <div style="font-size:12px;color:#64748b;margin-top:6px">Your IT admin needs to set up the outgoing mail server. You'll still receive in-app bell notifications.</div>
      @endif
    </div>
  </div>

</div>
<div class="col-lg-6">

  <!-- What you'll receive -->
  <div class="es-card">
    <div class="es-head">
      <div class="es-head-icon" style="background:#fef9c3;color:#ca8a04"><i class="bi bi-bell-fill"></i></div>
      <div><div style="font-size:14px;font-weight:700;color:#1e293b">What You'll Be Emailed About</div></div>
    </div>
    <div class="es-notify-grid">
      <div class="es-notify-tile">
        <div class="es-notify-icon" style="background:rgba(2,132,199,.12);color:#0284c7"><i class="bi bi-box-seam-fill"></i></div>
        <div class="es-notify-title">Asset Requests</div>
        <p class="es-notify-copy">Updates for add, edit, delete, and e-waste requests after review.</p>
        <div class="es-notify-chips">
          <span class="es-notify-chip" style="background:rgba(22,163,74,.1);color:#16a34a;border-color:rgba(22,163,74,.25)">Approved</span>
          <span class="es-notify-chip" style="background:rgba(239,68,68,.1);color:#ef4444;border-color:rgba(239,68,68,.25)">Rejected</span>
        </div>
      </div>
      <div class="es-notify-tile">
        <div class="es-notify-icon" style="background:rgba(217,119,6,.12);color:#d97706"><i class="bi bi-pen-fill"></i></div>
        <div class="es-notify-title">Write-Off Flow</div>
        <p class="es-notify-copy">Final write-off decisions from HOU, GM, or CEO approval stages.</p>
        <div class="es-notify-chips">
          <span class="es-notify-chip" style="background:rgba(22,163,74,.1);color:#16a34a;border-color:rgba(22,163,74,.25)">CEO Approved</span>
          <span class="es-notify-chip" style="background:rgba(239,68,68,.1);color:#ef4444;border-color:rgba(239,68,68,.25)">Returned</span>
        </div>
      </div>
      <div class="es-notify-tile">
        <div class="es-notify-icon" style="background:rgba(124,58,237,.12);color:#7c3aed"><i class="bi bi-clipboard-check-fill"></i></div>
        <div class="es-notify-title">IT Request Forms</div>
        <p class="es-notify-copy">Status changes for submitted IT request forms after validation.</p>
        <div class="es-notify-chips">
          <span class="es-notify-chip" style="background:rgba(22,163,74,.10);color:#16a34a;border-color:rgba(22,163,74,.25)">Completed</span>
          <span class="es-notify-chip" style="background:rgba(239,68,68,.10);color:#ef4444;border-color:rgba(239,68,68,.25)">Needs Action</span>
        </div>
      </div>
    </div>
    <div class="es-note">
      <i class="bi bi-info-circle-fill" style="flex-shrink:0;margin-top:1px"></i>
      Email is sent alongside the in-app bell notification when your profile has an email address.
    </div>
  </div>

</div>
</div>

@endsection
