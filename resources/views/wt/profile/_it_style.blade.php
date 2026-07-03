@php
  $user = auth('wt')->user();
  $routePrefix = $routePrefix ?? (request()->routeIs('wt.admin.*') ? 'wt.admin' : 'wt.user');
  $dashboardRoute = request()->routeIs('wt.admin.*') ? route('wt.admin.dashboard') : route('wt.user.dashboard');
  $roleLabel = method_exists($user, 'getWtRoleLabel') ? $user->getWtRoleLabel() : strtoupper(str_replace('_', ' ', $user->wt_role ?? 'user'));
  $avatarInitial = strtoupper(substr($user->full_name ?: $user->username ?: 'U', 0, 1));
  $hasAvatar = $user->avatar && Storage::disk('public')->exists($user->avatar);
  $hasSignature = $user->signature_img && Storage::disk('public')->exists($user->signature_img);
@endphp

<style>
.profile-hero{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:28px 32px;margin-bottom:24px;display:flex;align-items:center;gap:24px}
.profile-hero-avatar{position:relative;flex-shrink:0}
.profile-hero-img{width:88px;height:88px;border-radius:50%;object-fit:cover;border:3px solid var(--accent)}
.profile-hero-initial{width:88px;height:88px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-family:'Inter',sans-serif;font-size:32px;font-weight:800;color:#fff;border:3px solid var(--accent)}
.profile-hero-info{flex:1;min-width:0}
.profile-hero-name{font-size:22px;font-weight:800;color:var(--text);font-family:'Inter',sans-serif;line-height:1}
.profile-hero-meta{display:flex;align-items:center;gap:10px;margin-top:8px;flex-wrap:wrap}
.profile-section-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:20px}
.profile-section-header{padding:16px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:9px}
.profile-section-title{font-size:14px;font-weight:700;color:var(--text)}
.profile-section-body{padding:22px}
.account-row{display:flex;justify-content:space-between;align-items:center;gap:16px;padding:11px 0;border-bottom:1px solid var(--border)}
.account-row:last-child{border-bottom:none}
.account-row-label{font-size:13px;color:var(--muted);font-weight:500}
.account-row-value{font-size:13px;font-weight:600;color:var(--text);text-align:right}
.profile-status-badge{background:rgba(2,132,199,.12);color:var(--accent);border-radius:5px;padding:2px 10px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em}
.profile-action-row{margin-top:22px;display:flex;gap:8px;flex-wrap:wrap}
.profile-upload-row{display:flex;align-items:center;gap:16px;margin-bottom:22px;padding:16px;background:var(--body-bg);border-radius:10px;border:1px solid var(--border)}
.profile-thumb{width:52px;height:52px;border-radius:50%;overflow:hidden;flex-shrink:0;border:2px solid var(--accent);display:flex;align-items:center;justify-content:center;background:var(--accent)}
.profile-upload-label{display:inline-flex;align-items:center;gap:6px;background:var(--accent);color:#fff;border:none;border-radius:8px;padding:7px 14px;font-size:12px;font-weight:700;cursor:pointer}
.profile-signature-preview{margin-bottom:16px;padding:12px;background:var(--body-bg);border:1px solid var(--border);border-radius:10px;text-align:center}
.profile-tab-wrap{display:flex;gap:0;border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:16px;width:fit-content}
.profile-tab-btn{padding:7px 16px;font-size:12px;font-weight:700;border:none;cursor:pointer}
.profile-sig-canvas{border:1px solid var(--border);border-radius:6px;background:#fff;display:block;cursor:crosshair;touch-action:none;max-width:100%}
@media (max-width:768px){
  .profile-hero{align-items:flex-start;flex-direction:column;padding:22px}
  .profile-hero-name{font-size:18px}
  .profile-hero > div:last-child{text-align:left !important}
  .profile-upload-row{align-items:flex-start;flex-direction:column}
}
</style>

<div class="profile-hero">
  <div class="profile-hero-avatar">
    @if($hasAvatar)
    <img src="{{ asset('storage/' . $user->avatar) }}" class="profile-hero-img" id="avatarPreview" alt="Profile photo">
    @else
    <div class="profile-hero-initial" id="avatarPreview">{{ $avatarInitial }}</div>
    @endif
  </div>
  <div class="profile-hero-info">
    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:4px">Your Account</div>
    <div class="profile-hero-name">{{ strtoupper($user->full_name ?: $user->username) }}</div>
    <div class="profile-hero-meta">
      <span class="profile-status-badge">{{ $roleLabel }}</span>
      <span style="font-size:12px;color:var(--muted)"><i class="fas fa-user" style="font-size:11px"></i> {{ $user->username ?: '-' }}</span>
      @if($user->department)
      <span style="font-size:12px;color:var(--muted)"><i class="fas fa-building" style="font-size:11px"></i> {{ $user->department }}</span>
      @endif
      @if($user->email)
      <span style="font-size:12px;color:var(--muted)"><i class="fas fa-envelope" style="font-size:11px"></i> {{ $user->email }}</span>
      @endif
    </div>
  </div>
  <div style="display:flex;flex-direction:column;gap:10px;text-align:right;flex-shrink:0">
    <div>
      <div style="font-size:11px;color:var(--muted);font-weight:500;text-transform:uppercase;letter-spacing:.06em">Member Since</div>
      <div style="font-size:14px;font-weight:700;color:var(--text);margin-top:2px">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</div>
    </div>
    <div>
      <div style="font-size:11px;color:var(--muted);font-weight:500;text-transform:uppercase;letter-spacing:.06em">Last Login</div>
      <div style="font-size:13px;font-weight:600;color:var(--text);margin-top:2px">{{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->format('d M Y, H:i') : 'Never' }}</div>
    </div>
  </div>
</div>

@if(session('success'))
<div class="alert-success-custom mb-4"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-danger-custom mb-4"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif

<div class="row g-4" style="max-width:960px">
  <div class="col-md-7">
    <div class="profile-section-card">
      <div class="profile-section-header">
        <i class="fas fa-user" style="color:var(--accent);font-size:15px"></i>
        <div class="profile-section-title">Profile Information</div>
      </div>
      <div class="profile-section-body">
        <form method="POST" action="{{ route($routePrefix . '.profile.update') }}" enctype="multipart/form-data">
          @csrf
          <div class="profile-upload-row">
            <div id="avatarThumb" class="profile-thumb">
              @if($hasAvatar)
              <img src="{{ asset('storage/' . $user->avatar) }}" style="width:100%;height:100%;object-fit:cover" alt="Profile photo">
              @else
              <span style="font-family:'Inter',sans-serif;font-weight:800;font-size:18px;color:#fff">{{ $avatarInitial }}</span>
              @endif
            </div>
            <div>
              <label for="avatarInput" class="profile-upload-label">
                <i class="fas fa-upload"></i> Upload Photo
              </label>
              <input type="file" name="avatar" id="avatarInput" accept="image/*" style="display:none" onchange="previewAvatar(this)">
              <div style="font-size:11px;color:var(--muted);margin-top:4px">JPG, PNG, WebP - Max 2MB</div>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $user->full_name) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Staff ID <span style="font-size:11px;color:var(--muted);font-weight:400">(cannot change)</span></label>
              <input type="text" class="form-control" value="{{ $user->staff_id ?: $user->username }}" disabled style="opacity:.6;cursor:not-allowed">
            </div>
            <div class="col-md-6">
              <label class="form-label">Username</label>
              <input type="text" class="form-control" value="{{ $user->username }}" disabled style="opacity:.6;cursor:not-allowed">
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone No</label>
              <input type="text" name="phone_no" class="form-control" value="{{ old('phone_no', $user->phone_no) }}" placeholder="e.g. 012-3456789" data-preserve-case="true">
            </div>
            <div class="col-md-6">
              <label class="form-label">Department</label>
              <input type="text" name="department" class="form-control" value="{{ old('department', $user->department) }}" placeholder="e.g. ICT" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Position</label>
              <input type="text" name="position" class="form-control" value="{{ old('position', $user->position) }}" placeholder="e.g. Staff" required>
            </div>
          </div>

          <div class="profile-action-row">
            <button type="submit" class="btn-primary-custom"><i class="fas fa-check"></i> Save Changes</button>
            <a href="{{ $dashboardRoute }}" class="btn-secondary-custom"><i class="fas fa-times"></i> Cancel</a>
          </div>
        </form>
      </div>
    </div>

    <div class="profile-section-card">
      <div class="profile-section-header">
        <i class="fas fa-pen-fancy" style="color:var(--accent);font-size:15px"></i>
        <div class="profile-section-title">My Signature</div>
      </div>
      <div class="profile-section-body">
        @if($hasSignature)
        <div class="profile-signature-preview">
          <div style="font-size:11px;color:var(--muted);margin-bottom:8px;font-weight:600;text-transform:uppercase;letter-spacing:.05em">Current Saved Signature</div>
          <img src="{{ route($routePrefix . '.profile.signature.image') }}?v={{ time() }}" alt="Saved Signature" id="currentSigImg" style="max-height:80px;max-width:100%;border-bottom:1.5px solid #000;display:inline-block">
        </div>
        <form method="POST" action="{{ route($routePrefix . '.profile.signature.clear') }}" style="margin-bottom:16px">
          @csrf
          <button type="submit" class="btn-secondary-custom" style="font-size:12px;padding:6px 14px" onclick="return confirm('Clear your saved signature?')">
            <i class="fas fa-trash"></i> Clear Signature
          </button>
        </form>
        @endif

        <div class="profile-tab-wrap">
          <button type="button" id="sigTabUpload" onclick="switchSigTab('upload')" class="profile-tab-btn" style="background:var(--accent);color:#fff">
            <i class="fas fa-upload"></i> Upload Image
          </button>
          <button type="button" id="sigTabDraw" onclick="switchSigTab('draw')" class="profile-tab-btn" style="background:var(--surface);color:var(--muted)">
            <i class="fas fa-pencil"></i> Draw
          </button>
        </div>

        <div id="sigPanelUpload">
          <form method="POST" action="{{ route($routePrefix . '.profile.signature') }}" enctype="multipart/form-data" onsubmit="return validateSigUpload(event)">
            @csrf
            <div style="margin-bottom:12px">
              <label for="sigFileInput" class="profile-upload-label">
                <i class="fas fa-image"></i> Choose Image
              </label>
              <input type="file" name="signature_file" id="sigFileInput" accept="image/*" style="display:none" onchange="previewSigFile(this)">
              <span id="sigFileName" style="font-size:12px;color:var(--muted);margin-left:8px">No file chosen</span>
            </div>
            <div id="sigFilePreview" style="display:none;margin-bottom:12px;padding:10px;background:var(--body-bg);border:1px solid var(--border);border-radius:8px;text-align:center">
              <img id="sigFilePreviewImg" style="max-height:70px;max-width:100%;border-bottom:1px solid #000" alt="Signature preview">
            </div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:12px">JPG, PNG, WebP - Max 2MB - Use a white-background image for best results</div>
            <button type="submit" class="btn-primary-custom" style="font-size:12px;padding:7px 16px"><i class="fas fa-check"></i> Save Signature</button>
          </form>
        </div>

        <div id="sigPanelDraw" style="display:none">
          <form method="POST" action="{{ route($routePrefix . '.profile.signature') }}" id="sigDrawForm">
            @csrf
            <input type="hidden" name="sig_canvas_data" id="sigCanvasData">
            <div style="margin-bottom:8px;font-size:11px;color:var(--muted)">Draw your signature below, then click Save.</div>
            <canvas id="profileSigCanvas" width="380" height="100" class="profile-sig-canvas"></canvas>
            <div style="margin-top:8px;display:flex;gap:8px">
              <button type="button" onclick="clearProfileSig()" class="btn-secondary-custom" style="font-size:12px;padding:6px 12px"><i class="fas fa-eraser"></i> Clear</button>
              <button type="button" onclick="saveProfileSig()" class="btn-primary-custom" style="font-size:12px;padding:6px 14px"><i class="fas fa-check"></i> Save Signature</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-5">
    <div class="profile-section-card">
      <div class="profile-section-header">
        <i class="fas fa-info-circle" style="color:var(--accent);font-size:15px"></i>
        <div class="profile-section-title">Account Details</div>
      </div>
      <div style="padding:6px 22px">
        <div class="account-row"><span class="account-row-label">Member since</span><span class="account-row-value">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span></div>
        <div class="account-row"><span class="account-row-label">Last login</span><span class="account-row-value">{{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->format('d M Y, H:i') : 'Never' }}</span></div>
        <div class="account-row"><span class="account-row-label">Account status</span><span class="profile-status-badge">Active</span></div>
        <div class="account-row"><span class="account-row-label">Role</span><span class="profile-status-badge">{{ $roleLabel }}</span></div>
        <div class="account-row"><span class="account-row-label">Staff ID</span><span class="account-row-value">{{ $user->staff_id ?: '-' }}</span></div>
        <div class="account-row"><span class="account-row-label">Department</span><span class="account-row-value">{{ $user->department ?: '-' }}</span></div>
        <div class="account-row"><span class="account-row-label">Position</span><span class="account-row-value">{{ $user->position ?: '-' }}</span></div>
        <div class="account-row"><span class="account-row-label">Phone</span><span class="account-row-value">{{ $user->phone_no ?: '-' }}</span></div>
      </div>
    </div>
  </div>
</div>

<script>
function previewAvatar(input) {
  if (!input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    ['avatarPreview','avatarThumb'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.innerHTML = '<img src="'+e.target.result+'" style="width:100%;height:100%;object-fit:cover;border-radius:50%" alt="Profile photo">';
    });
  };
  reader.readAsDataURL(input.files[0]);
}
function switchSigTab(tab) {
  var upload = document.getElementById('sigPanelUpload');
  var draw = document.getElementById('sigPanelDraw');
  var btnUp = document.getElementById('sigTabUpload');
  var btnDr = document.getElementById('sigTabDraw');
  if (tab === 'upload') {
    upload.style.display = '';
    draw.style.display = 'none';
    btnUp.style.background = 'var(--accent)';
    btnUp.style.color = '#fff';
    btnDr.style.background = 'var(--surface)';
    btnDr.style.color = 'var(--muted)';
  } else {
    upload.style.display = 'none';
    draw.style.display = '';
    btnDr.style.background = 'var(--accent)';
    btnDr.style.color = '#fff';
    btnUp.style.background = 'var(--surface)';
    btnUp.style.color = 'var(--muted)';
    initProfileSigCanvas();
  }
}
function previewSigFile(input) {
  if (!input.files[0]) return;
  var nameEl = document.getElementById('sigFileName');
  if (nameEl) nameEl.textContent = input.files[0].name;
  var preview = document.getElementById('sigFilePreview');
  var previewImg = document.getElementById('sigFilePreviewImg');
  var reader = new FileReader();
  reader.onload = function(e) {
    previewImg.src = e.target.result;
    preview.style.display = 'block';
  };
  reader.readAsDataURL(input.files[0]);
}
var _pSigCanvas, _pSigCtx, _pSigDrawing = false, _pSigInited = false;
function initProfileSigCanvas() {
  if (_pSigInited) return;
  _pSigCanvas = document.getElementById('profileSigCanvas');
  _pSigCtx = _pSigCanvas.getContext('2d');
  _pSigCtx.strokeStyle = '#000';
  _pSigCtx.lineWidth = 1.8;
  _pSigCtx.lineCap = 'round';
  _pSigCtx.lineJoin = 'round';
  function getPos(e) {
    var r = _pSigCanvas.getBoundingClientRect();
    var t = e.touches ? e.touches[0] : e;
    return { x: (t.clientX - r.left) * (_pSigCanvas.width / r.width), y: (t.clientY - r.top) * (_pSigCanvas.height / r.height) };
  }
  _pSigCanvas.onmousedown = function(e) { _pSigDrawing = true; var p = getPos(e); _pSigCtx.beginPath(); _pSigCtx.moveTo(p.x, p.y); };
  _pSigCanvas.onmousemove = function(e) { if (!_pSigDrawing) return; var p = getPos(e); _pSigCtx.lineTo(p.x, p.y); _pSigCtx.stroke(); };
  _pSigCanvas.onmouseup = function() { _pSigDrawing = false; };
  _pSigCanvas.onmouseleave = function() { _pSigDrawing = false; };
  _pSigCanvas.ontouchstart = function(e) { e.preventDefault(); _pSigDrawing = true; var p = getPos(e); _pSigCtx.beginPath(); _pSigCtx.moveTo(p.x, p.y); };
  _pSigCanvas.ontouchmove = function(e) { e.preventDefault(); if (!_pSigDrawing) return; var p = getPos(e); _pSigCtx.lineTo(p.x, p.y); _pSigCtx.stroke(); };
  _pSigCanvas.ontouchend = function() { _pSigDrawing = false; };
  _pSigInited = true;
}
function clearProfileSig() {
  if (_pSigCtx) _pSigCtx.clearRect(0, 0, _pSigCanvas.width, _pSigCanvas.height);
}
function validateSigUpload(e) {
  var file = document.getElementById('sigFileInput').files[0];
  if (!file) {
    alert('Please choose an image file first.');
    e.preventDefault();
    return false;
  }
  var allowed = ['image/jpeg','image/png','image/webp'];
  if (!allowed.includes(file.type)) {
    alert('Only JPG, PNG, or WebP images are allowed.');
    e.preventDefault();
    return false;
  }
  if (file.size > 2 * 1024 * 1024) {
    alert('File size must be 2MB or under.');
    e.preventDefault();
    return false;
  }
  return true;
}
function saveProfileSig() {
  if (!_pSigCanvas) {
    alert('Please switch to the Draw tab first.');
    return;
  }
  var d = _pSigCtx.getImageData(0, 0, _pSigCanvas.width, _pSigCanvas.height).data;
  var blank = true;
  for (var i = 3; i < d.length; i += 4) {
    if (d[i] > 0) {
      blank = false;
      break;
    }
  }
  if (blank) {
    alert('Please draw your signature before saving.');
    return;
  }
  document.getElementById('sigCanvasData').value = _pSigCanvas.toDataURL('image/png');
  document.getElementById('sigDrawForm').submit();
}
</script>
