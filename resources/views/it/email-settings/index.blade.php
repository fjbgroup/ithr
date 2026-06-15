@extends('it.layouts.app')

@section('title', 'Email Settings')
@section('page_title', 'Email Settings')

@section('content')

<style>
.es-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 12px rgba(0,0,0,.05);margin-bottom:20px}
.es-head{padding:16px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:12px}
.es-head-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0}
.es-label{display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#64748b;margin-bottom:6px}
.es-input{width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:13.5px;color:#1e293b;background:#f8fafc;outline:none;transition:border-color .18s,box-shadow .18s,background .18s;box-sizing:border-box}
.es-input:focus{border-color:#0284c7;background:#fff;box-shadow:0 0 0 3px rgba(2,132,199,.1)}
.es-input::placeholder{color:#94a3b8}
</style>


<!-- HEADER -->
<div style="margin-bottom:24px;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
  <div>
    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:#64748b;margin-bottom:5px">Admin › <span style="color:#0284c7">Email Settings</span></div>
    <h4 style="font-family:'DM Sans',sans-serif;font-weight:800;font-size:22px;color:#1e293b;margin:0">Email Notifications</h4>
    <p style="font-size:13px;color:#64748b;margin:4px 0 0">Configure outgoing email so admins get notified when users submit requests.</p>
  </div>
  <div style="display:inline-flex;align-items:center;gap:6px;border-radius:20px;padding:6px 14px;font-size:12px;font-weight:700;{{ $configured ? 'background:#dcfce7;color:#16a34a;border:1px solid #bbf7d0' : 'background:#fff7ed;color:#c2410c;border:1px solid #fed7aa' }}">
    <i class="bi bi-{{ $configured ? 'check-circle-fill' : 'exclamation-triangle-fill' }}"></i>
    {{ $configured ? 'SMTP Configured' : 'Not Configured' }}
  </div>
</div>

<div class="row g-4">
<div class="col-lg-8">

  <!-- SMTP SETTINGS -->
  <div class="es-card">
    <div class="es-head">
      <div class="es-head-icon" style="background:#e0f2fe;color:#0284c7"><i class="bi bi-envelope-fill"></i></div>
      <div>
        <div style="font-size:14px;font-weight:700;color:#1e293b">SMTP Configuration</div>
        <div style="font-size:12px;color:#64748b;margin-top:1px">Outgoing mail server settings</div>
      </div>
    </div>
    <form method="POST" action="{{ route('it.email-settings.update') }}" style="padding:22px">
      @csrf
      <div class="row g-3">

        <!-- Provider quick-select -->
        <div class="col-12">
          <label class="es-label">Quick Select Provider</label>
          <div style="display:flex;gap:8px;flex-wrap:wrap">
            @foreach([['Gmail','smtp.gmail.com','587','tls'],['Outlook','smtp-mail.outlook.com','587','tls'],['Yahoo','smtp.mail.yahoo.com','587','tls']] as [$pname,$phost,$pport,$penc])
            <button type="button" onclick="setProvider('{{ $phost }}','{{ $pport }}','{{ $penc }}')"
              style="padding:7px 16px;border:1.5px solid #e2e8f0;border-radius:8px;background:#f8fafc;font-family:'DM Sans',sans-serif;font-size:12px;font-weight:600;color:#475569;cursor:pointer;transition:all .15s{{ ($settings['smtp_host'] ?? '') === $phost ? ';border-color:#0284c7;background:#f0f9ff;color:#0284c7' : '' }}"
              onmouseover="this.style.borderColor='#0284c7';this.style.color='#0284c7'"
              onmouseout="this.style.borderColor='{{ ($settings['smtp_host'] ?? '') === $phost ? '#0284c7' : '#e2e8f0' }}';this.style.color='{{ ($settings['smtp_host'] ?? '') === $phost ? '#0284c7' : '#475569' }}'">
              {{ $pname }}
            </button>
            @endforeach
          </div>
        </div>

        <div class="col-8">
          <label class="es-label">SMTP Host <span style="color:#ef4444">*</span></label>
          <input type="text" name="smtp_host" id="f_host" class="es-input" placeholder="smtp.gmail.com" value="{{ $settings['smtp_host'] ?? '' }}">
          <div style="font-size:11px;color:#94a3b8;margin-top:5px">Gmail: smtp.gmail.com &nbsp;·&nbsp; Outlook: smtp-mail.outlook.com</div>
        </div>
        <div class="col-4">
          <label class="es-label">Port</label>
          <input type="text" name="smtp_port" id="f_port" class="es-input" value="{{ $settings['smtp_port'] ?? '587' }}">
        </div>

        <div class="col-5">
          <label class="es-label">Encryption</label>
          <select name="smtp_encryption" id="f_enc" class="es-input" style="cursor:pointer">
            <option value="tls"  {{ ($settings['smtp_encryption'] ?? 'tls') === 'tls'  ? 'selected' : '' }}>STARTTLS (Recommended)</option>
            <option value="ssl"  {{ ($settings['smtp_encryption'] ?? '') === 'ssl'  ? 'selected' : '' }}>SSL / TLS</option>
            <option value="none" {{ ($settings['smtp_encryption'] ?? '') === 'none' ? 'selected' : '' }}>None</option>
          </select>
        </div>

        <div class="col-7">
          <label class="es-label">SMTP Username / Email <span style="color:#ef4444">*</span></label>
          <input type="text" name="smtp_user" id="f_user" class="es-input" placeholder="your@gmail.com" value="{{ $settings['smtp_user'] ?? '' }}" autocomplete="off">
        </div>

        <div class="col-12">
          <label class="es-label" style="display:flex;align-items:center;gap:7px">
            App Password <span style="color:#ef4444">*</span>
            <button type="button" id="appPwBtn" onclick="toggleAppPwPopup(event)"
              style="width:18px;height:18px;border-radius:50%;background:#e0f2fe;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;font-size:11px;color:#0284c7;transition:background .15s;flex-shrink:0;padding:0"
              onmouseover="this.style.background='#bae6fd'" onmouseout="this.style.background='#e0f2fe'"
              title="How to get an App Password">
              <i class="bi bi-question-lg" style="font-size:10px;font-weight:900"></i>
            </button>
          </label>
          <div style="position:relative">
            <input type="password" name="smtp_pass" id="f_pass" class="es-input" style="padding-right:44px"
              placeholder="{{ !empty($settings['smtp_pass']) ? '•••••••••••••••• (saved — leave blank to keep)' : 'Paste your App Password here' }}"
              autocomplete="new-password">
            <button type="button" onclick="togglePass()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;font-size:16px"><i class="bi bi-eye-slash" id="passEye"></i></button>
          </div>
          @if(!empty($settings['smtp_pass']))
          <div style="font-size:11px;color:#16a34a;margin-top:5px"><i class="bi bi-check-circle-fill"></i> Password saved. Leave blank to keep existing.</div>
          @endif
        </div>

        <div class="col-6">
          <label class="es-label">From Name</label>
          <input type="text" name="smtp_from_name" class="es-input" placeholder="FJB IT Inventory" value="{{ $settings['smtp_from_name'] ?? 'FJB IT Inventory' }}">
        </div>
        <div class="col-6">
          <label class="es-label">From Email <span style="font-weight:400;text-transform:none;letter-spacing:0">(optional)</span></label>
          <input type="email" name="smtp_from" class="es-input" placeholder="Same as username above" value="{{ $settings['smtp_from'] ?? '' }}">
        </div>

        <div class="col-12" style="margin-top:4px">
          <button type="submit"
            style="display:inline-flex;align-items:center;gap:8px;background:#142b47;color:#fff;border:none;border-radius:9px;padding:11px 26px;font-size:13.5px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;transition:background .15s"
            onmouseover="this.style.background='#254a78'" onmouseout="this.style.background='#142b47'">
            <i class="bi bi-floppy2-fill"></i> Save Settings
          </button>
        </div>

      </div>
    </form>
  </div>

  <!-- TEST EMAIL -->
  <div class="es-card">
    <div class="es-head">
      <div class="es-head-icon" style="background:#dcfce7;color:#16a34a"><i class="bi bi-send-check-fill"></i></div>
      <div>
        <div style="font-size:14px;font-weight:700;color:#1e293b">Send a Test Email</div>
        <div style="font-size:12px;color:#64748b;margin-top:1px">Verify your settings are working before relying on them</div>
      </div>
    </div>
    <form method="POST" action="{{ route('it.email-settings.test') }}" style="padding:18px 22px">
      @csrf
      <div class="row g-3 align-items-end">
        <div class="col">
          <label class="es-label">Send test to</label>
          <input type="email" name="test_to" class="es-input" placeholder="admin@example.com" value="{{ $settings['smtp_user'] ?? '' }}">
        </div>
        <div class="col-auto">
          <button type="submit" name="test_email" value="1" {{ !$configured ? 'disabled' : '' }}
            style="display:inline-flex;align-items:center;gap:7px;background:{{ $configured ? '#16a34a' : '#94a3b8' }};color:#fff;border:none;border-radius:9px;padding:10px 20px;font-size:13px;font-weight:700;cursor:{{ $configured ? 'pointer' : 'not-allowed' }};font-family:'DM Sans',sans-serif">
            <i class="bi bi-send-fill"></i> Send Test
          </button>
        </div>
      </div>
      @if(!$configured)
      <div style="margin-top:10px;font-size:12px;color:#94a3b8"><i class="bi bi-arrow-up"></i> Save your settings above first.</div>
      @endif
    </form>
  </div>

</div><!-- /col-lg-8 -->

<!-- RIGHT COLUMN -->
<div class="col-lg-4" style="display:flex;flex-direction:column;gap:20px">

  <!-- Who gets notified -->
  <div class="es-card">
    <div class="es-head">
      <div class="es-head-icon" style="background:#e0f2fe;color:#0284c7"><i class="bi bi-people-fill"></i></div>
      <div><div style="font-size:14px;font-weight:700;color:#1e293b">Who Gets Notified</div><div style="font-size:12px;color:#64748b;margin-top:1px">Admins with an email on their profile</div></div>
    </div>
    <div style="padding:12px 20px 16px">
      @forelse($admins as $a)
        @php $hasEmail = !empty($a->email); @endphp
        <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f8fafc">
          <div style="width:30px;height:30px;border-radius:50%;background:{{ $a->role === 'admin' ? '#dbeafe' : '#ede9fe' }};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:{{ $a->role === 'admin' ? '#2563eb' : '#7c3aed' }};flex-shrink:0">{{ strtoupper(substr($a->full_name, 0, 1)) }}</div>
          <div style="flex:1;min-width:0">
            <div style="font-size:12px;font-weight:600;color:#1e293b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $a->full_name }}</div>
            <div style="font-size:11px;color:{{ $hasEmail ? '#64748b' : '#ef4444' }};overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $hasEmail ? $a->email : '⚠ No email on profile' }}</div>
          </div>
          <i class="bi bi-{{ $hasEmail ? 'envelope-check-fill' : 'envelope-x-fill' }}" style="color:{{ $hasEmail ? '#16a34a' : '#ef4444' }};font-size:14px;flex-shrink:0"></i>
        </div>
      @empty
        <div style="padding:20px;text-align:center;color:#94a3b8;font-size:13px">No admin accounts found.</div>
      @endforelse
      <div style="margin-top:12px;font-size:11px;color:#94a3b8;line-height:1.6;display:flex;gap:6px">
        <i class="bi bi-info-circle" style="color:#0284c7;flex-shrink:0;margin-top:1px"></i>
        Admins marked ❌ only get bell notifications. Add their email in Manage Users.
      </div>
    </div>
  </div>

  <!-- Triggers -->
  <div class="es-card">
    <div class="es-head">
      <div class="es-head-icon" style="background:#fef9c3;color:#ca8a04"><i class="bi bi-bell-fill"></i></div>
      <div><div style="font-size:14px;font-weight:700;color:#1e293b">When Emails Are Sent</div></div>
    </div>
    <div style="padding:10px 20px 16px">
      @foreach([['#2563eb','bi-box-seam-fill','New Add Asset Request'],['#d97706','bi-pen-fill','New Write-Off Request'],['#dc2626','bi-trash3-fill','New Delete Request'],['#16a34a','bi-recycle','E-Waste Pending Approval']] as [$tc,$ti,$tt])
      <div style="display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid #f8fafc">
        <div style="width:28px;height:28px;border-radius:7px;background:{{ $tc }}18;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0"><i class="bi {{ $ti }}" style="color:{{ $tc }}"></i></div>
        <div style="font-size:12px;font-weight:600;color:#334155">{{ $tt }}</div>
      </div>
      @endforeach
    </div>
  </div>

</div>
</div><!-- /row -->

<!-- APP PASSWORD POPUP MODAL -->
<div id="appPwModal" style="display:none;position:fixed;inset:0;z-index:9000;align-items:center;justify-content:center;background:rgba(15,23,42,.45);backdrop-filter:blur(3px);padding:1rem">
  <div style="background:#fff;border-radius:16px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.2);overflow:hidden;animation:apwIn .2s ease">

    <!-- Header -->
    <div style="background:#142b47;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;gap:12px">
      <div style="display:flex;align-items:center;gap:12px">
        <div style="width:36px;height:36px;border-radius:9px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">🔑</div>
        <div>
          <div style="color:#fff;font-size:14px;font-weight:700">How to Get an App Password</div>
          <div style="color:rgba(255,255,255,.55);font-size:11px;margin-top:2px">Required for Gmail &amp; Outlook</div>
        </div>
      </div>
      <button onclick="closeAppPwPopup()" style="background:rgba(255,255,255,.12);border:none;border-radius:7px;color:rgba(255,255,255,.8);width:30px;height:30px;cursor:pointer;font-size:17px;display:flex;align-items:center;justify-content:center">&times;</button>
    </div>

    <!-- Tab switcher -->
    <div style="display:flex;background:#f8fafc;border-bottom:1px solid #e2e8f0">
      <button id="apwTabGmail" onclick="apwTab('gmail')"
        style="flex:1;padding:11px;border:none;background:transparent;font-family:'DM Sans',sans-serif;font-size:12px;font-weight:700;cursor:pointer;color:#0284c7;border-bottom:2px solid #0284c7;transition:all .15s">
        📧 Gmail
      </button>
      <button id="apwTabOutlook" onclick="apwTab('outlook')"
        style="flex:1;padding:11px;border:none;background:transparent;font-family:'DM Sans',sans-serif;font-size:12px;font-weight:700;cursor:pointer;color:#94a3b8;border-bottom:2px solid transparent;transition:all .15s">
        📨 Outlook
      </button>
    </div>

    <!-- Gmail Steps -->
    <div id="apwGmail" style="padding:20px 22px">
      @foreach([
        ['#142b47','Go to Google Account Security','Open <a href="https://myaccount.google.com/security" target="_blank" style="color:#0284c7;font-weight:700">myaccount.google.com/security</a> in your browser.'],
        ['#2563eb','Turn on 2-Step Verification','Under "How you sign in to Google", enable <strong>2-Step Verification</strong> if it\'s not already on.'],
        ['#7c3aed','Open App Passwords','Go to <a href="https://myaccount.google.com/apppasswords" target="_blank" style="color:#0284c7;font-weight:700">myaccount.google.com/apppasswords</a>.'],
        ['#16a34a','Generate a password','Type a name like <strong>"FJB Inventory"</strong> and click <strong>Create</strong>.'],
        ['#d97706','Copy &amp; paste it here','A 16-character code will appear. Copy it and paste it into the <strong>App Password</strong> field.'],
      ] as $idx => [$gc,$gt,$gd])
      <div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px solid #f8fafc">
        <div style="width:26px;height:26px;border-radius:50%;background:{{ $gc }};display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff;flex-shrink:0;margin-top:2px">{{ $idx + 1 }}</div>
        <div>
          <div style="font-size:13px;font-weight:700;color:#1e293b;margin-bottom:3px">{!! $gt !!}</div>
          <div style="font-size:12px;color:#64748b;line-height:1.6">{!! $gd !!}</div>
        </div>
      </div>
      @endforeach
      <div style="margin-top:14px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:10px 13px;font-size:11px;color:#0369a1;display:flex;gap:7px">
        <i class="bi bi-lightbulb-fill" style="flex-shrink:0;margin-top:1px"></i>
        <span>Your regular Gmail password won't work here — Google blocks it for security. The App Password is the only way.</span>
      </div>
    </div>

    <!-- Outlook Steps -->
    <div id="apwOutlook" style="padding:20px 22px;display:none">
      @foreach([
        ['#142b47','Go to Microsoft Account Security','Open <a href="https://account.microsoft.com/security" target="_blank" style="color:#0284c7;font-weight:700">account.microsoft.com/security</a>.'],
        ['#2563eb','Enable Two-step verification','Find <strong>Two-step verification</strong> and turn it on.'],
        ['#7c3aed','Find App Passwords','Under Advanced security, click <strong>App passwords</strong>.'],
        ['#16a34a','Create a new app password','Give it a name like <strong>"FJB Inventory"</strong> and click <strong>Create</strong>.'],
        ['#d97706','Copy &amp; paste it here','Copy the password shown and paste it into the <strong>App Password</strong> field.'],
      ] as $idx => [$oc,$ot,$od])
      <div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px solid #f8fafc">
        <div style="width:26px;height:26px;border-radius:50%;background:{{ $oc }};display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff;flex-shrink:0;margin-top:2px">{{ $idx + 1 }}</div>
        <div>
          <div style="font-size:13px;font-weight:700;color:#1e293b;margin-bottom:3px">{!! $ot !!}</div>
          <div style="font-size:12px;color:#64748b;line-height:1.6">{!! $od !!}</div>
        </div>
      </div>
      @endforeach
      <div style="margin-top:14px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:10px 13px;font-size:11px;color:#0369a1;display:flex;gap:7px">
        <i class="bi bi-lightbulb-fill" style="flex-shrink:0;margin-top:1px"></i>
        <span>Microsoft also requires two-step verification before App Passwords become available.</span>
      </div>
    </div>

    <!-- Footer -->
    <div style="padding:14px 22px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end">
      <button onclick="closeAppPwPopup()"
        style="display:inline-flex;align-items:center;gap:7px;background:#142b47;color:#fff;border:none;border-radius:8px;padding:9px 22px;font-size:13px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif">
        Got it <i class="bi bi-check-lg"></i>
      </button>
    </div>
  </div>
</div>

<style>
@keyframes apwIn { from{opacity:0;transform:scale(.95) translateY(-8px)} to{opacity:1;transform:scale(1) translateY(0)} }
</style>

<script>
function setProvider(host, port, enc) {
  document.getElementById('f_host').value = host;
  document.getElementById('f_port').value = port;
  document.getElementById('f_enc').value  = enc;
}
function togglePass() {
  var inp = document.getElementById('f_pass');
  var ico = document.getElementById('passEye');
  if (inp.type === 'password') { inp.type='text'; ico.className='bi bi-eye'; }
  else { inp.type='password'; ico.className='bi bi-eye-slash'; }
}
function toggleAppPwPopup(e) {
  e.stopPropagation();
  var m = document.getElementById('appPwModal');
  m.style.display = m.style.display === 'flex' ? 'none' : 'flex';
}
function closeAppPwPopup() {
  document.getElementById('appPwModal').style.display = 'none';
}
function apwTab(tab) {
  var isGmail = tab === 'gmail';
  document.getElementById('apwGmail').style.display    = isGmail ? 'block' : 'none';
  document.getElementById('apwOutlook').style.display  = isGmail ? 'none'  : 'block';
  document.getElementById('apwTabGmail').style.color         = isGmail ? '#0284c7'  : '#94a3b8';
  document.getElementById('apwTabGmail').style.borderBottom  = isGmail ? '2px solid #0284c7' : '2px solid transparent';
  document.getElementById('apwTabOutlook').style.color        = isGmail ? '#94a3b8' : '#0284c7';
  document.getElementById('apwTabOutlook').style.borderBottom = isGmail ? '2px solid transparent' : '2px solid #0284c7';
}
document.addEventListener('click', function(e) {
  var modal = document.getElementById('appPwModal');
  if (modal && modal.style.display === 'flex' && e.target === modal) closeAppPwPopup();
});
</script>

@endsection

