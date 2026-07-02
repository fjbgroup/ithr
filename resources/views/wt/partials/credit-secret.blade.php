<script>
(function(){
  var targets = document.querySelectorAll('[data-credit-secret]');
  if(!targets.length) return;

  var parts = [
    'WW91IGZvdW5kIHRoZSBjcmVh',
    'dG9yIHwgZGV2ZWxvcGVyClNp',
    'dGkgSGFqYXIgYmludGkgQWJk',
    'IFJhemFrClN0dWRlbnQgSW50',
    'ZXJuIGZyb20gS1YgUGVyZGFn',
    'YW5nYW4sIEpCCnYxLjAuMCAt',
    'IDIwMjY='
  ];

  function creditLines(){
    var fallback = [
      'YOU FOUND THE SECRET',
      'CREATOR | DEVELOPER',
      'SITI HAJAR BINTI ABD RAZAK',
      'STUDENT INTERN FROM KV PERDAGANGAN, JB',
      '13/3/2026 - 31/7/2026<br>V1.0.0',
      'Crafted to empower your workflow, today and onwards.'
    ];

    try {
      var decoded = atob(parts.join('')).split('\n');
      return [
        'YOU FOUND THE SECRET',
        'CREATOR | DEVELOPER',
        (decoded[1] || fallback[2]).toUpperCase(),
        (decoded[2] || fallback[3]).toUpperCase(),
        fallback[4],
        fallback[5]
      ];
    } catch (e) {
      return fallback;
    }
  }

  function showCredit(){
    var existing = document.getElementById('creditSecretOverlay');
    if(existing) existing.remove();

    var lines = creditLines();
    var colors = ['#f0abfc', '#fb7185', '#a78bfa', '#60a5fa', '#67e8f9', '#ffffff'];
    var overlay = document.createElement('div');
    overlay.id = 'creditSecretOverlay';
    overlay.innerHTML =
      '<div class="credit-secret-confetti" aria-hidden="true"></div>' +
      '<div class="credit-secret-card" role="dialog" aria-modal="true" aria-label="Creator credit">' +
        '<button type="button" class="credit-secret-close" aria-label="Close">&times;</button>' +
        '<div class="credit-secret-shine"></div>' +
        '<div class="credit-secret-mark">IT</div>' +
        '<div class="credit-secret-kicker">' + lines[0] + '</div>' +
        '<div class="credit-secret-title">' + lines[1] + '</div>' +
        '<div class="credit-secret-divider"></div>' +
        '<div class="credit-secret-name">' + lines[2] + '</div>' +
        '<div class="credit-secret-copy">' + lines[3] + '</div>' +
        '<div class="credit-secret-version">' + lines[4] + '</div>' +
        '<div class="credit-secret-note">' + lines[5] + '</div>' +
      '</div>';

    document.body.appendChild(overlay);
    var confetti = overlay.querySelector('.credit-secret-confetti');
    if(confetti){
      for(var i = 0; i < 84; i++){
        var piece = document.createElement('span');
        piece.style.left = (Math.random() * 100) + '%';
        piece.style.background = colors[Math.floor(Math.random() * colors.length)];
        piece.style.animationDuration = (2.1 + Math.random() * 1.9) + 's';
        piece.style.animationDelay = (Math.random() * 0.35) + 's';
        piece.style.setProperty('--credit-confetti-x', ((Math.random() * 120) - 60) + 'px');
        piece.style.transform = 'rotate(' + (Math.random() * 180) + 'deg)';
        if(Math.random() > 0.55) piece.style.borderRadius = '999px';
        confetti.appendChild(piece);
      }
    }
    requestAnimationFrame(function(){ overlay.classList.add('show'); });

    function close(){
      overlay.classList.remove('show');
      setTimeout(function(){ overlay.remove(); }, 180);
    }

    overlay.addEventListener('click', function(event){
      if(event.target === overlay) close();
    });
    overlay.querySelector('.credit-secret-close').addEventListener('click', close);
    document.addEventListener('keydown', function esc(event){
      if(event.key !== 'Escape') return;
      document.removeEventListener('keydown', esc);
      close();
    });
  }

  targets.forEach(function(target){
    var timer = null;
    var fired = false;

    function start(){
      fired = false;
      clearTimeout(timer);
      timer = setTimeout(function(){
        fired = true;
        showCredit();
      }, 2400);
    }

    function cancel(event){
      clearTimeout(timer);
      if(fired && event && event.type === 'click'){
        event.preventDefault();
        event.stopPropagation();
      }
    }

    target.addEventListener('pointerdown', start);
    target.addEventListener('pointerup', cancel);
    target.addEventListener('pointerleave', cancel);
    target.addEventListener('pointercancel', cancel);
    target.addEventListener('click', cancel, true);
    target.addEventListener('contextmenu', function(event){ event.preventDefault(); });
  });
})();
</script>
<style>
#creditSecretOverlay {
  position: fixed;
  inset: 0;
  z-index: 100000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background: rgba(2, 6, 23, 0.62);
  backdrop-filter: blur(7px);
  opacity: 0;
  pointer-events: none;
  transition: opacity 180ms ease;
}
#creditSecretOverlay.show {
  opacity: 1;
  pointer-events: auto;
}
.credit-secret-card {
  position: relative;
  z-index: 100002;
  width: min(460px, 100%);
  overflow: hidden;
  border-radius: 24px;
  border: 1px solid rgba(240, 171, 252, 0.42);
  background:
    radial-gradient(circle at 15% 0%, rgba(251, 113, 133, 0.28), transparent 34%),
    radial-gradient(circle at 86% 8%, rgba(96, 165, 250, 0.28), transparent 36%),
    linear-gradient(145deg, #2b124c 0%, #1d4ed8 48%, #be185d 100%);
  color: #f8fafc;
  padding: 34px 30px 30px;
  text-align: center;
  box-shadow: 0 30px 90px rgba(2, 6, 23, 0.55), 0 0 52px rgba(217, 70, 239, 0.26);
  transform: translateY(16px) scale(0.96);
  transition: transform 220ms cubic-bezier(.16,.84,.44,1);
}
#creditSecretOverlay.show .credit-secret-card {
  transform: translateY(0) scale(1);
  animation: creditSecretPop 520ms cubic-bezier(.18,.89,.32,1.28) both;
}
@keyframes creditSecretPop {
  0% { opacity: 0; transform: translateY(26px) scale(0.9); }
  60% { opacity: 1; transform: translateY(-6px) scale(1.035); }
  100% { opacity: 1; transform: translateY(0) scale(1); }
}
.credit-secret-shine {
  position: absolute;
  inset: -80px -100px auto auto;
  width: 220px;
  height: 220px;
  border-radius: 50%;
  background: rgba(240, 171, 252, 0.22);
  filter: blur(10px);
  pointer-events: none;
}
.credit-secret-close {
  position: absolute;
  z-index: 3;
  top: 14px;
  right: 16px;
  border: 0;
  background: transparent;
  color: rgba(248, 250, 252, 0.68);
  font-size: 26px;
  line-height: 1;
  cursor: pointer;
  pointer-events: auto;
}
.credit-secret-close:hover { color: #ffffff; }
.credit-secret-mark {
  position: relative;
  display: inline-flex;
  width: 62px;
  height: 62px;
  align-items: center;
  justify-content: center;
  border-radius: 20px;
  background: linear-gradient(135deg, #f0abfc, #60a5fa 52%, #fb7185);
  color: #ffffff;
  font-weight: 950;
  font-size: 20px;
  box-shadow: 0 18px 36px rgba(217, 70, 239, 0.34);
  animation: creditSecretPulse 1600ms ease-in-out infinite;
}
@keyframes creditSecretPulse {
  0%, 100% { transform: scale(1); box-shadow: 0 18px 36px rgba(217, 70, 239, 0.34); }
  50% { transform: scale(1.06); box-shadow: 0 20px 44px rgba(96, 165, 250, 0.46); }
}
.credit-secret-confetti {
  position: fixed;
  inset: 0;
  overflow: hidden;
  pointer-events: none;
  z-index: 100001;
}
.credit-secret-confetti span {
  position: absolute;
  top: -18px;
  width: 9px;
  height: 14px;
  opacity: 0.95;
  animation-name: creditSecretConfettiDrop;
  animation-timing-function: cubic-bezier(.2,.65,.38,1);
  animation-fill-mode: forwards;
}
@keyframes creditSecretConfettiDrop {
  0% { transform: translate3d(0, -24px, 0) rotate(0deg); opacity: 0; }
  10% { opacity: 1; }
  100% { transform: translate3d(var(--credit-confetti-x, 28px), 108vh, 0) rotate(720deg); opacity: 0; }
}
.credit-secret-kicker {
  margin-top: 22px;
  color: #f0abfc;
  font-size: 12px;
  font-weight: 950;
  letter-spacing: 0.16em;
}
.credit-secret-title {
  margin-top: 8px;
  font-size: 26px;
  font-weight: 950;
  letter-spacing: 0.06em;
}
.credit-secret-divider {
  width: 64px;
  height: 3px;
  margin: 20px auto;
  border-radius: 999px;
  background: linear-gradient(90deg, transparent, #f0abfc, #60a5fa, #fb7185, transparent);
}
.credit-secret-name {
  font-size: 15px;
  font-weight: 900;
  letter-spacing: 0.08em;
}
.credit-secret-copy,
.credit-secret-version {
  margin-top: 9px;
  color: rgba(226, 232, 240, 0.78);
  font-size: 12px;
  font-weight: 800;
  letter-spacing: 0.06em;
}
.credit-secret-note {
  margin: 18px auto 0;
  max-width: none;
  width: 100%;
  color: rgba(255, 255, 255, 0.84);
  font-size: 12px;
  font-weight: 700;
  line-height: 1.25;
  letter-spacing: 0.03em;
  white-space: nowrap;
}
@media (max-width: 520px) {
  #creditSecretOverlay {
    padding: 14px;
    align-items: center;
  }

  .credit-secret-card {
    width: 100%;
    border-radius: 18px;
    padding: 28px 18px 22px;
  }

  .credit-secret-close {
    top: 10px;
    right: 12px;
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .credit-secret-mark {
    width: 54px;
    height: 54px;
    border-radius: 17px;
    font-size: 18px;
  }

  .credit-secret-kicker {
    margin-top: 18px;
    font-size: 10px;
    letter-spacing: 0.12em;
  }

  .credit-secret-title {
    font-size: 20px;
    line-height: 1.15;
  }

  .credit-secret-name {
    font-size: 12px;
    line-height: 1.4;
    overflow-wrap: anywhere;
  }

  .credit-secret-copy,
  .credit-secret-version {
    font-size: 10.5px;
    line-height: 1.45;
  }

  .credit-secret-note {
    max-width: none;
    width: 100%;
    font-size: clamp(7.5px, 2.35vw, 10.5px);
    line-height: 1.25;
    letter-spacing: 0;
    white-space: nowrap;
  }
}
</style>
