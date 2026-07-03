<?php
$roleMeta = [
  'admin'         => ['IT Admin',        'bi-shield-fill',       '#0284c7', 'rgba(2,132,199,.12)',   'rgba(2,132,199,.25)'],
  'finance_admin' => ['Finance Admin',   'bi-currency-dollar',   '#0369a1', 'rgba(56,189,248,.12)',  'rgba(56,189,248,.3)'],
  'ceo'           => ['CEO',             'bi-star-fill',         '#b45309', 'rgba(245,158,11,.12)',  'rgba(245,158,11,.3)'],
  'gm'            => ['General Manager', 'bi-briefcase-fill',    '#0d9488', 'rgba(20,184,166,.12)',  'rgba(20,184,166,.25)'],
  'hou'           => ['Head of Unit',    'bi-person-badge-fill', '#7c3aed', 'rgba(124,58,237,.12)',  'rgba(124,58,237,.25)'],
  'user'          => ['Staff',           'bi-person-fill',       '#2563eb', 'rgba(37,99,235,.08)',   'rgba(37,99,235,.2)'],
];
[$label,$icon,$color,$bg,$border] = $roleMeta[$activeRole];
$avatarColor = fn($name) => ['#0284c7','#2563eb','#16a34a','#7c3aed','#0891b2','#dc2626','#d97706'][ord(strtoupper($name[0] ?? 'A')) % 7];
$me = auth()->guard('it')->id();
?>

<div class="mu-table-wrap">
  @forelse($users as $row)
  @php
    $aColor = $avatarColor($row->full_name);
    $isMe   = $row->id == $me;
  @endphp
  <div class="mu-user-row">
    @if($row->avatar)
    <img src="{{ asset('storage/' . $row->avatar) }}" class="mu-avatar">
    @else
    <div class="mu-avatar" style="background:{{ $aColor }}">{{ strtoupper(substr($row->full_name,0,1)) }}</div>
    @endif

    <div class="mu-user-info">
      <div class="mu-user-name">
        {{ $row->full_name }}
        @if($isMe)
        <span style="font-size:9px;background:rgba(2,132,199,.15);color:var(--accent);border-radius:4px;padding:1px 6px;font-weight:700;vertical-align:middle;margin-left:4px">YOU</span>
        @endif
        @if($row->must_change_password)
        <span style="font-size:9px;background:rgba(220,38,38,.1);color:#dc2626;border-radius:4px;padding:1px 6px;font-weight:700;vertical-align:middle;margin-left:4px"><i class="bi bi-key-fill"></i> Temp PW</span>
        @endif
      </div>
      <div class="mu-user-sub">
        <span class="mu-user-meta"><i class="bi bi-person-fill"></i>{{ $row->username }}</span>
        @if($row->email)
        <span class="mu-user-meta"><i class="bi bi-envelope-fill"></i>{{ $row->email }}</span>
        @endif
        @if($row->department)
        <span class="mu-user-meta"><i class="bi bi-person-badge-fill"></i>{{ $row->department }}</span>
        @endif
        <span class="mu-user-meta"><i class="bi bi-clock-history"></i>{{ $row->last_login ? $row->last_login->format('d/m/Y H:i') : 'Never logged in' }}</span>
      </div>
    </div>

    @if($row->is_active)
    <span class="mu-status" style="background:rgba(22,163,74,.1);color:#16a34a">
      <span style="width:6px;height:6px;border-radius:50%;background:#16a34a;display:inline-block"></span> Active
    </span>
    @else
    <span class="mu-status" style="background:rgba(239,68,68,.1);color:#dc2626">
      <span style="width:6px;height:6px;border-radius:50%;background:#dc2626;display:inline-block"></span> Inactive
    </span>
    @endif

    <div class="mu-actions">
      <button type="button" class="mu-btn mu-edit-trigger"
        style="background:rgba(2,132,199,.08);color:var(--accent);border-color:rgba(2,132,199,.2)" title="Edit"
        data-action="{{ route('it.users.update', $row->id) }}"
        data-name="{{ e($row->full_name) }}"
        data-username="{{ e($row->username) }}"
        data-full-name="{{ e($row->full_name) }}"
        data-email="{{ e($row->email ?? '') }}"
        data-role="{{ $row->it_role === 'admin_it' ? 'admin' : $row->it_role }}"
        data-dept="{{ e($row->dept_name ?? '') }}">
        <i class="bi bi-pencil-fill"></i>
      </button>
      @if(!$isMe)
      <form method="POST" action="{{ route('it.users.toggle', $row->id) }}" style="display:contents"
        onsubmit="return confirm('{{ $row->is_active ? 'Deactivate' : 'Activate' }} {{ $row->full_name }}?')">
        @csrf
        <button type="submit" class="mu-btn"
          style="background:{{ $row->is_active ? 'rgba(22,163,74,.08)' : 'rgba(239,68,68,.08)' }};color:{{ $row->is_active ? '#16a34a' : '#dc2626' }};border-color:{{ $row->is_active ? 'rgba(22,163,74,.2)' : 'rgba(239,68,68,.2)' }}"
          title="{{ $row->is_active ? 'Deactivate' : 'Activate' }}">
          <i class="bi bi-toggle-{{ $row->is_active ? 'on' : 'off' }}"></i>
        </button>
      </form>
      @if($row->it_role !== 'admin_it' && $row->it_role !== 'admin')
      <form method="POST" action="{{ route('it.users.reset-password', $row->id) }}" style="display:contents"
        onsubmit="return confirm('Reset password for {{ $row->full_name }} to default?')">
        @csrf
        <button type="submit" class="mu-btn"
          style="background:rgba(245,158,11,.08);color:#d97706;border-color:rgba(245,158,11,.2)" title="Reset Password">
          <i class="bi bi-key-fill"></i>
        </button>
      </form>
      @endif
      @if($row->it_role !== 'user')
      <form method="POST" action="{{ route('it.users.destroy', $row->id) }}" style="display:contents"
        onsubmit="return confirm('Reset {{ $row->full_name }} to the default Staff role?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="mu-btn"
          style="background:rgba(239,68,68,.08);color:#dc2626;border-color:rgba(239,68,68,.2)" title="Reset to Staff role">
          <i class="bi bi-arrow-counterclockwise"></i>
        </button>
      </form>
      @endif
      @endif
    </div>
  </div>
  @empty
  <div class="mu-empty">
    <i class="bi {{ $icon }} mu-empty-icon" style="color:{{ $color }}"></i>
    <div class="mu-empty-title">{{ $search ? 'No results for "'.e($search).'"' : 'No '.$label.' accounts' }}</div>
    <div class="mu-empty-sub">
      @if($search)
      <span style="color:{{ $color }};font-weight:700;cursor:pointer" onclick="document.getElementById('mu-search-input').value='';document.getElementById('mu-search-input').dispatchEvent(new Event('input'))">Clear search</span>
      @else
      <a href="{{ route('it.users.index', ['action'=>'add','default_role'=>$activeRole,'role_tab'=>$activeRole]) }}"
        style="color:{{ $color }};font-weight:700;text-decoration:none">Create the first one</a>
      @endif
    </div>
  </div>
  @endforelse
</div>

@if($users->hasPages())
<div style="padding:16px 20px;background:var(--surface);border:1px solid var(--border);border-radius:14px">
  {{ $users->links() }}
</div>
@endif
