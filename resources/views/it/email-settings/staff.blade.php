@extends('it.layouts.app')

@section('title', 'Email Notifications')
@section('page_title', 'Email Notifications')

@section('content')

<style>
.es-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 12px rgba(0,0,0,.05);margin-bottom:20px}
.es-head{padding:16px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:12px}
.es-head-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0}
</style>

<!-- HEADER -->
<div style="margin-bottom:24px">
  <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:#64748b;margin-bottom:5px">Settings â€º <span style="color:#0284c7">Email Notifications</span></div>
  <h4 style="font-family:'Inter',sans-serif;font-weight:800;font-size:22px;color:#1e293b;margin:0">My Email Notifications</h4>
  <p style="font-size:13px;color:#64748b;margin:4px 0 0">You'll receive email updates when your requests are approved or rejected.</p>
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
    <div style="padding:10px 20px 16px">
      @foreach([
        ['#16a34a','bi-check-circle-fill','Add Asset Request â€” Approved'],
        ['#ef4444','bi-x-circle-fill',    'Add Asset Request â€” Rejected'],
        ['#16a34a','bi-check-circle-fill','Edit Asset Request â€” Approved'],
        ['#ef4444','bi-x-circle-fill',    'Edit Asset Request â€” Rejected'],
        ['#16a34a','bi-check-circle-fill','Delete Asset Request â€” Approved'],
        ['#ef4444','bi-x-circle-fill',    'Delete Asset Request â€” Rejected'],
        ['#16a34a','bi-check-circle-fill','E-Waste Request â€” Approved'],
        ['#ef4444','bi-x-circle-fill',    'E-Waste Request â€” Rejected'],
        ['#16a34a','bi-pen-fill',         'Write-Off â€” Approved by CEO'],
        ['#ef4444','bi-pen-fill',         'Write-Off â€” Rejected (HOU / GM / CEO)'],
        ['#16a34a','bi-clipboard-check-fill','IT Request Form â€” Approved'],
        ['#ef4444','bi-clipboard-x-fill', 'IT Request Form â€” Rejected'],
      ] as [$tc,$ti,$tt])
      <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f8fafc">
        <div style="width:26px;height:26px;border-radius:7px;background:{{ $tc }}18;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0">
          <i class="bi {{ $ti }}" style="color:{{ $tc }}"></i>
        </div>
        <div style="font-size:12px;font-weight:500;color:#334155">{{ $tt }}</div>
      </div>
      @endforeach
      <div style="margin-top:12px;font-size:11px;color:#94a3b8;display:flex;gap:6px;line-height:1.6">
        <i class="bi bi-info-circle" style="color:#0284c7;flex-shrink:0;margin-top:1px"></i>
        You always get in-app bell notifications. Emails are sent in addition, only when your profile has an email address.
      </div>
    </div>
  </div>

</div>
</div>

@endsection
