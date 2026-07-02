// assets/js/app.js

// ===================== MODAL SYSTEM =====================
function openModal(id) {
    document.querySelectorAll('.modal.active').forEach(m => m.classList.remove('active'));
    const modal = document.getElementById(id);
    const overlay = document.getElementById('modalOverlay');
    if (modal)   { modal.classList.add('active'); }
    if (overlay) { overlay.classList.add('active'); }

    // Dispatch event for any page-specific handlers
    document.dispatchEvent(new CustomEvent('openModal', { detail: id }));
}

function closeModal() {
    document.querySelectorAll('.modal.active').forEach(m => m.classList.remove('active'));
    const overlay = document.getElementById('modalOverlay');
    if (overlay) overlay.classList.remove('active');
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});

// ===================== IMPORT COLUMN MAPPING =====================
function _importRenderMapping(containerId, fields, headers) {
    const skipOpt = '<option value="">— Skip —</option>';
    const rows = fields.map(f => {
        const firstMatch = headers.find(h => h.key === f.key || (f.aliases && f.aliases.includes(h.key)));
        const opts = (f.required ? '' : skipOpt) +
            headers.map(h => `<option value="${h.key}"${h === firstMatch ? ' selected' : ''}>${h.raw}</option>`).join('');
        const req = f.required ? '<span style="color:#ef4444;"> *</span>' : '';
        return `<tr style="border-bottom:1px solid var(--border);">
            <td style="padding:.35rem .6rem;font-size:.82rem;white-space:nowrap;">${f.label}${req}</td>
            <td style="padding:.35rem .6rem;">
                <select name="mapping[${f.key}]" style="font-size:.82rem;padding:.3rem .5rem;border:1px solid var(--border);border-radius:6px;width:100%;">
                    ${opts}
                </select>
            </td>
        </tr>`;
    }).join('');
    document.getElementById(containerId).innerHTML =
        `<p style="font-size:.8rem;color:#64748b;margin-bottom:.6rem;">Map your file's columns to the fields below. <span style="color:#ef4444;">*</span> = required.</p>
        <div style="border:1px solid var(--border);border-radius:8px;max-height:280px;overflow-y:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead style="position:sticky;top:0;background:#f8fafc;z-index:1;">
                    <tr>
                        <th style="padding:.4rem .6rem;font-size:.75rem;color:var(--muted);text-align:left;border-bottom:1px solid var(--border);">Field</th>
                        <th style="padding:.4rem .6rem;font-size:.75rem;color:var(--muted);text-align:left;border-bottom:1px solid var(--border);">Your Column</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
        </div>`;
}

function _importRenderSheetSelector(containerId, sheets, redetectFn) {
    const el = document.getElementById(containerId);
    if (!el) return;
    if (!sheets || sheets.length <= 1) { el.style.display = 'none'; return; }
    el.style.display = '';
    const selId = containerId + 'Select';
    const hiddenId = containerId.replace('SheetSelector', 'SheetIndexInput');
    el.innerHTML = `<div style="display:flex;align-items:center;gap:.5rem;background:#f0f9ff;border:1px solid #bae6fd;border-radius:6px;padding:.5rem .75rem;margin-bottom:.75rem;flex-wrap:wrap;">
        <label style="font-size:.82rem;color:#0369a1;white-space:nowrap;font-weight:500;">Sheet:</label>
        <select id="${selId}" style="font-size:.82rem;padding:.25rem .5rem;border:1px solid #bae6fd;border-radius:5px;flex:1;min-width:120px;"
            onchange="const h=document.getElementById('${hiddenId}');if(h)h.value=this.value;">
            ${sheets.map((s, i) => `<option value="${i}">${s}</option>`).join('')}
        </select>
        <button type="button" class="btn btn-outline btn-sm" onclick="${redetectFn}()">Re-detect Columns</button>
    </div>`;
}

// ===================== SIDEBAR TOGGLE (mobile) =====================
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar) sidebar.classList.toggle('open');
    if (overlay) overlay.classList.toggle('active');
}

// ===================== TOAST AUTO-DISMISS =====================
document.addEventListener('DOMContentLoaded', function() {
    const toast = document.getElementById('mainToast');
    if (toast) {
        setTimeout(() => {
            const container = document.getElementById('toastContainer');
            if (container) {
                container.style.opacity = '0';
                container.style.transition = 'opacity .4s';
                setTimeout(() => container.remove(), 400);
            }
        }, 4000);
    }
});

// ===================== TABLE SEARCH (client-side) =====================
function tableSearch(inputId, tableId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#' + tableId + ' tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}

// ===================== FORM VALIDATION =====================
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        let valid = true;
        this.querySelectorAll('[required]').forEach(field => {
            if (!field.value.trim()) {
                field.style.borderColor = '#dc2626';
                valid = false;
            } else {
                field.style.borderColor = '';
            }
        });
        if (!valid) {
            e.preventDefault();
            // Show first invalid field
            const first = this.querySelector('[required]:not([value])');
            if (first) first.focus();
        }
    });
});


// ── Nav group dropdown ──────────────────────────────────────────────────────
function toggleNavGroup(groupId) {
    const group    = document.getElementById(groupId);
    const toggle   = group.querySelector('.nav-group-toggle');
    const children = group.querySelector('.nav-group-children');
    const isOpen   = toggle.classList.contains('open');

    // Close all other nav groups to prevent overlapping popups
    document.querySelectorAll('.nav-group').forEach(function(g) {
        if (g.id !== groupId) {
            const t = g.querySelector('.nav-group-toggle');
            const c = g.querySelector('.nav-group-children');
            if (t) t.classList.remove('open');
            if (c) c.classList.remove('open');
        }
    });

    toggle.classList.toggle('open', !isOpen);
    children.classList.toggle('open', !isOpen);
}
