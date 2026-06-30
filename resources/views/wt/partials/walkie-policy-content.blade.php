<div class="walkie-policy-language" style="font-size:12px;line-height:1.7;color:var(--text)">
  <div style="display:inline-flex;align-items:center;gap:8px;margin:0 0 16px;padding:4px;border:1px solid var(--border);border-radius:10px;background:var(--soft-surface)">
    <button type="button" class="walkie-policy-lang-btn is-active" data-policy-lang="en" style="min-height:30px;border:0;border-radius:7px;background:var(--navy);color:#fff;padding:0 13px;font-size:11px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;cursor:pointer">English</button>
    <button type="button" class="walkie-policy-lang-btn" data-policy-lang="bm" style="min-height:30px;border:0;border-radius:7px;background:transparent;color:var(--muted);padding:0 13px;font-size:11px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;cursor:pointer">Bahasa Malaysia</button>
  </div>

  <section class="walkie-policy-panel" data-policy-panel="en">
    <h3 style="margin:0 0 10px;font-size:12px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;color:var(--text)">English</h3>
    <p style="margin:0 0 14px;font-weight:700">
      This policy is established to ensure that walkie talkies at FGV Johor Bulkers Sdn Bhd are used in an orderly, controlled manner and for official purposes only.
    </p>

    <ul style="display:grid;gap:10px;margin:0;padding-left:18px">
      <li>Walkie talkies are company property and must be used for official purposes only.</li>
      <li>All requests must be submitted through the assigned supervisor using the provided system.</li>
      <li>Users are responsible for keeping the walkie talkie in good, clean, and safe condition.</li>
      <li>The equipment must not be loaned to another party without supervisor approval.</li>
      <li>Any loss, damage, or issue must be reported immediately through the provided system.</li>
      <li>The company only covers improvement or repair costs caused by normal wear and tear or manufacturing defects.</li>
      <li>If damage or loss is caused by negligence, misuse, or failure to follow usage instructions, the user is fully responsible for the repair or replacement cost.</li>
    </ul>
  </section>

  <section class="walkie-policy-panel" data-policy-panel="bm" style="display:none">
    <h3 style="margin:0 0 10px;font-size:12px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;color:var(--text)">Bahasa Malaysia</h3>
    <p style="margin:0 0 14px;font-weight:700">
      Polisi ini diwujudkan untuk memastikan penggunaan walkie talkie di FGV Johor Bulkers Sdn Bhd adalah teratur, terkawal dan digunakan bagi tujuan rasmi sahaja.
    </p>

    <ul style="display:grid;gap:10px;margin:0;padding-left:18px">
      <li>Walkie talkie adalah hak milik syarikat dan hanya untuk kegunaan rasmi sahaja.</li>
      <li>Semua permohonan hendaklah dibuat melalui penyelia masing-masing menggunakan sistem yang disediakan.</li>
      <li>Pengguna bertanggungjawab memastikan walkie talkie sentiasa berada dalam keadaan baik, bersih dan selamat.</li>
      <li>Peralatan tidak boleh dipinjamkan kepada pihak lain tanpa kelulusan penyelia.</li>
      <li>Sebarang kehilangan, kerosakan atau masalah hendaklah dilaporkan segera melalui sistem yang disediakan.</li>
      <li>Syarikat hanya menanggung kos penambahbaikan bagi kerosakan akibat penggunaan biasa (wear and tear) atau kecacatan pembuatan.</li>
      <li>Sekiranya kerosakan atau kehilangan berpunca daripada kecuaian, penyalahgunaan atau kegagalan mematuhi arahan penggunaan, pengguna bertanggungjawab menanggung sepenuhnya kos pembaikan atau penggantian peralatan.</li>
    </ul>
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
