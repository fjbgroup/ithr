@php
  $policyContent = $policyContent ?? [
    'en' => [
      'This policy is established to ensure that walkie talkies at FGV Johor Bulkers Sdn Bhd are used in an orderly, controlled manner and for official purposes only.',
      'Walkie talkies are company property and must be used for official purposes only.',
      'All requests must be submitted through the assigned supervisor using the provided system.',
      'Users are responsible for keeping the walkie talkie in good, clean, and safe condition.',
      'The equipment must not be loaned to another party without supervisor approval.',
      'Any loss, damage, or issue must be reported immediately through the provided system.',
      'The company only covers improvement or repair costs caused by normal wear and tear or manufacturing defects.',
      'If damage or loss is caused by negligence, misuse, or failure to follow usage instructions, the user is fully responsible for the repair or replacement cost.',
    ],
    'bm' => [
      'Polisi ini diwujudkan untuk memastikan penggunaan walkie talkie di FGV Johor Bulkers Sdn Bhd adalah teratur, terkawal dan digunakan bagi tujuan rasmi sahaja.',
      'Walkie talkie adalah hak milik syarikat dan hanya untuk kegunaan rasmi sahaja.',
      'Semua permohonan hendaklah dibuat melalui penyelia masing-masing menggunakan sistem yang disediakan.',
      'Pengguna bertanggungjawab memastikan walkie talkie sentiasa berada dalam keadaan baik, bersih dan selamat.',
      'Peralatan tidak boleh dipinjamkan kepada pihak lain tanpa kelulusan penyelia.',
      'Sebarang kehilangan, kerosakan atau masalah hendaklah dilaporkan segera melalui sistem yang disediakan.',
      'Syarikat hanya menanggung kos penambahbaikan bagi kerosakan akibat penggunaan biasa (wear and tear) atau kecacatan pembuatan.',
      'Sekiranya kerosakan atau kehilangan berpunca daripada kecuaian, penyalahgunaan atau kegagalan mematuhi arahan penggunaan, pengguna bertanggungjawab menanggung sepenuhnya kos pembaikan atau penggantian peralatan.',
    ],
  ];
  $canEditPolicy = request()->routeIs('wt.admin.policies') && auth('wt')->user()?->wt_role === 'admin_it';
@endphp

<style>
  .walkie-policy-edit-grid {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 22px !important;
    width: 100% !important;
  }

  .walkie-policy-edit-field,
  .walkie-policy-edit-textarea {
    width: 100% !important;
    max-width: none !important;
    min-width: 0 !important;
  }

  .walkie-policy-edit-textarea {
    min-height: 220px !important;
    box-sizing: border-box !important;
    display: block !important;
    resize: vertical !important;
    text-transform: none !important;
  }

  @media (max-width: 900px) {
    .walkie-policy-edit-grid {
      grid-template-columns: 1fr !important;
    }
  }
</style>

<div class="walkie-policy-language" style="font-size:12px;line-height:1.7;color:var(--text)">
  <div style="display:inline-flex;align-items:center;gap:8px;margin:0 0 16px;padding:4px;border:1px solid var(--border);border-radius:10px;background:var(--soft-surface)">
    <button type="button" class="walkie-policy-lang-btn is-active" data-policy-lang="en" style="min-height:30px;border:0;border-radius:7px;background:var(--navy);color:#fff;padding:0 13px;font-size:11px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;cursor:pointer">English</button>
    <button type="button" class="walkie-policy-lang-btn" data-policy-lang="bm" style="min-height:30px;border:0;border-radius:7px;background:transparent;color:var(--muted);padding:0 13px;font-size:11px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;cursor:pointer">Bahasa Malaysia</button>
  </div>

  <section class="walkie-policy-panel" data-policy-panel="en">
    <h3 style="margin:0 0 10px;font-size:12px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;color:var(--text)">English</h3>
    <p style="margin:0 0 14px;font-weight:700">
      {!! $policyContent['en'][0] ?? '' !!}
    </p>

    <div style="display:grid;gap:10px;margin:0">
      @foreach(array_slice($policyContent['en'] ?? [], 1) as $policyLine)
      <div style="padding-left: 4px">{!! $policyLine !!}</div>
      @endforeach
    </div>
  </section>

  <section class="walkie-policy-panel" data-policy-panel="bm" style="display:none">
    <h3 style="margin:0 0 10px;font-size:12px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;color:var(--text)">Bahasa Malaysia</h3>
    <p style="margin:0 0 14px;font-weight:700">
      {!! $policyContent['bm'][0] ?? '' !!}
    </p>

    <div style="display:grid;gap:10px;margin:0">
      @foreach(array_slice($policyContent['bm'] ?? [], 1) as $policyLine)
      <div style="padding-left: 4px">{!! $policyLine !!}</div>
      @endforeach
    </div>
  </section>

  @if($canEditPolicy)
  <form method="POST" action="{{ route('wt.admin.policies.update') }}" class="walkie-policy-edit-form" style="display:none;width:100%;margin-top:16px;border-top:1px solid var(--border);padding-top:16px">
    @csrf
    <div class="walkie-policy-edit-grid">
      <label class="walkie-policy-edit-field" style="display:grid;gap:8px;color:var(--text);font-size:11px;font-weight:900;letter-spacing:.08em;text-transform:uppercase">
        English Policy
        <textarea name="policy_en" rows="8" class="walkie-policy-edit-textarea" style="width:100% !important;max-width:none !important;min-width:100% !important;border:1px solid var(--border);border-radius:10px;background:var(--surface);color:var(--text);padding:12px;font-size:12px;line-height:1.5;text-transform:none !important">{{ implode("\n", $policyContent['en'] ?? []) }}</textarea>
      </label>
      <label class="walkie-policy-edit-field" style="display:grid;gap:8px;color:var(--text);font-size:11px;font-weight:900;letter-spacing:.08em;text-transform:uppercase">
        Bahasa Malaysia Policy
        <textarea name="policy_bm" rows="8" class="walkie-policy-edit-textarea" style="width:100% !important;max-width:none !important;min-width:100% !important;border:1px solid var(--border);border-radius:10px;background:var(--surface);color:var(--text);padding:12px;font-size:12px;line-height:1.5;text-transform:none !important">{{ implode("\n", $policyContent['bm'] ?? []) }}</textarea>
      </label>
    </div>
    <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:12px">
      <button type="button" class="walkie-policy-cancel-btn" onclick="closeWalkiePolicyEdit(this)" style="min-height:34px;border:1px solid var(--border);border-radius:8px;background:var(--surface);color:var(--muted);padding:0 14px;font-size:11px;font-weight:900;text-transform:uppercase">Cancel</button>
      <button type="submit" style="min-height:34px;border:0;border-radius:8px;background:var(--navy);color:#fff;padding:0 14px;font-size:11px;font-weight:900;text-transform:uppercase">Save Policy</button>
    </div>
  </form>
  @endif
</div>

<script>
  window.toggleWalkiePolicyEdit = function (trigger) {
    var policyCard = trigger.closest('.policy-note-card');
    if (!policyCard) return;

    var editForm = policyCard.querySelector('.walkie-policy-edit-form');
    if (!editForm) return;

    var isEditing = editForm.style.display !== 'none';
    editForm.style.display = isEditing ? 'none' : 'block';
    trigger.innerHTML = isEditing
      ? '<i class="fa-solid fa-pen-to-square"></i> Edit'
      : '<i class="fa-solid fa-xmark"></i> Close';
  };

  window.closeWalkiePolicyEdit = function (trigger) {
    var policyCard = trigger.closest('.policy-note-card');
    if (!policyCard) return;

    var editForm = policyCard.querySelector('.walkie-policy-edit-form');
    var editTrigger = policyCard.querySelector('[data-policy-edit-trigger]');

    if (editForm) editForm.style.display = 'none';
    if (editTrigger) editTrigger.innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Edit';
  };

  document.querySelectorAll('.walkie-policy-language').forEach(function (policyBlock) {
    var buttons = policyBlock.querySelectorAll('.walkie-policy-lang-btn');
    var panels = policyBlock.querySelectorAll('.walkie-policy-panel');

    buttons.forEach(function (button) {
      button.addEventListener('click', function () {
        var selectedLang = button.getAttribute('data-policy-lang');

        buttons.forEach(function (item) {
          var isActive = item === button;
          item.classList.toggle('is-active', isActive);
          item.style.background = isActive ? 'var(--navy)' : 'transparent';
          item.style.color = isActive ? '#fff' : 'var(--muted)';
        });

        panels.forEach(function (panel) {
          panel.style.display = panel.getAttribute('data-policy-panel') === selectedLang ? 'block' : 'none';
        });
      });
    });

  });
</script>
