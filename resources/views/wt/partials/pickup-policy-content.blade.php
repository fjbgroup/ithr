@php
  $policyContent = $policyContent ?? [
    'en' => [
        'The following are the terms and conditions that must be adhered to:-',
        'a. Officers must be responsible to ensure that each walkie-talkie and additional equipment (accessories) provided are used carefully and maintained as well as possible to prevent any damage.',
        'b. If it is found that damage or loss occurs due to :-<br><span style="padding-left:18px; display:inline-block;">&bull; Willful negligence</span><br><span style="padding-left:18px; display:inline-block;">&bull; Misuse of the walkie-talkie</span><br><span style="padding-left:18px; display:inline-block;">&bull; Intentional loss</span><br><span style="padding-left:18px; display:inline-block;">&bull; Intentional damage</span>',
        'c. The staff member concerned will be held responsible to bear the repair and replacement cost of a new walkie-talkie if necessary.',
        'd. However, repair costs for damage caused by "manufacturing defect" and usage exceeding the lifespan will be borne by the company.',
        'Thank you, please be informed.'
    ],
    'bm' => [
        'Berikut adalah syarat-syarat yang perlu dipatuhi:-',
        'a. Petugas perlu bertanggungjawab untuk memastikan setiap walkie-talkie dan kelengkapan tambahan (aksesori) yang dibekalkan digunakan dengan cermat dan dijaga sebaik mungkin bagi mengelakkan berlakunya sebarang kerosakan.',
        'b. Jika didapati berlaku kerosakan atau kehilangan yang disebabkan :-<br><span style="padding-left:18px; display:inline-block;">&bull; Kecuaian yang disengajakan</span><br><span style="padding-left:18px; display:inline-block;">&bull; Penyalahgunaan walkie Talkie</span><br><span style="padding-left:18px; display:inline-block;">&bull; Kehilangan yang disengajakan</span><br><span style="padding-left:18px; display:inline-block;">&bull; Kerosakan yang disengajakan</span>',
        'c. Petugas yang berkenaan akan dipertanggungjawabkan untuk menanggung kos baik pulih dan penggantian walkie-talkie yang baru sekiranya perlu.',
        'd. Bagaimanapun, kos baik pulih terhadap kerosakan yang disebabkan oleh "manufacturing defeat" dan penggunaan yang melebihi jangka hayat akan ditanggung oleh pihak syarikat.',
        'Sekian, harap maklum.'
    ]
  ];
@endphp

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
</div>

<script>
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
