import re

filepath = 'resources/views/it/inventory/index.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

def field(name, label, required=False, type='text', id_prefix='', is_textarea=False, datalist=False):
    req_mark = '<span style="color:var(--red)">*</span>' if required else ''
    id_attr = f'id="{id_prefix}{name}"' if id_prefix else ''
    dl_attr = 'list="assetTypeOptions" autocomplete="off"' if datalist else ''
    
    col_span = 'style="grid-column: span 4;"' if is_textarea or name == 'description' else ''
    
    label_html = f'<label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">{label} {req_mark}</label>'
    
    input_style = "width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"
    
    if is_textarea:
        input_html = f'<textarea name="{name}" {id_attr} style="{input_style}"></textarea>'
    else:
        input_html = f'<input type="{type}" name="{name}" {id_attr} {dl_attr} style="{input_style}">'
        
    return f'<div {col_span}>{label_html}{input_html}</div>'

def generate_form(id_prefix=''):
    sections = [
        ('Asset Identity', 'bi-person-badge', [
            ('asset_name', 'Asset Name', True, 'text', False, False),
            ('asset_type', 'Asset Type', True, 'text', False, True),
            ('model', 'Model', True, 'text', False, False),
            ('serial_number', 'Serial Number', True, 'text', False, False),
            ('description', 'Description', True, 'text', False, False),
            ('manufacturer', 'Manufacturer', False, 'text', False, False),
            ('system_sku', 'System SKU', False, 'text', False, False),
            ('bar_code', 'Bar Code', False, 'text', False, False),
            ('domain', 'Domain', False, 'text', False, False),
            ('chrome_os_device_id', 'Chrome OS Device ID', False, 'text', False, False),
            ('fqdn', 'FQDN', False, 'text', False, False)
        ]),
        ('Technical Details', 'bi-pc-display', [
            ('ip_address', 'IP Address', False, 'text', False, False),
            ('mac_address', 'MAC Address', False, 'text', False, False),
            ('os_code', 'OS Code', False, 'text', False, False),
            ('sp', 'SP', False, 'text', False, False),
            ('memory_mb', 'Memory (GB)', False, 'text', False, False),
            ('nr_processors', 'Nr Processors', False, 'text', False, False),
            ('processor', 'Processor', False, 'text', False, False),
            ('scan_server', 'Scan Server', False, 'text', False, False),
            ('last_patched', 'Last Patched', False, 'text', False, False),
            ('last_full_backup', 'Last Full Backup', False, 'text', False, False),
            ('last_full_image', 'Last Full Image', False, 'text', False, False)
        ]),
        ('Financial Details', 'bi-currency-dollar', [
            ('purchase_date', 'Purchase Date', False, 'date', False, False),
            ('order_number', 'Order Number', False, 'text', False, False)
        ]),
        ('Warranty & Lifespan', 'bi-shield-check', [
            ('warranty_date', 'Warranty Until', False, 'date', False, False)
        ]),
        ('Notes / Other', 'bi-journal-text', [
            ('location', 'Location', False, 'text', False, False),
            ('building', 'Building', False, 'text', False, False),
            ('department', 'Department', False, 'text', False, False),
            ('branch_office', 'Branch Office', False, 'text', False, False),
            ('contact', 'Contact', False, 'text', False, False),
            ('comments', 'Comments', False, 'text', True, False)
        ])
    ]
    
    html = '<div class="wizard-container" style="display:flex;flex-direction:column;gap:12px;">\n'
    for i, (title, icon, fields) in enumerate(sections):
        is_first = i == 0
        display_style = "block" if is_first else "none"
        icon_rotation = "transform: rotate(180deg);" if is_first else ""
        bg_color = "rgba(14, 165, 233, 0.05)" if is_first else "#f8fafc"
        border_color = "#0ea5e9" if is_first else "var(--border)"
        
        prefix = id_prefix if id_prefix else 'add_'
        header_id = f"header_{prefix}{i}"
        body_id = f"body_{prefix}{i}"
        icon_id = f"icon_{prefix}{i}"
        
        html += f'''
        <div style="border: 1px solid var(--border); border-radius: 12px; overflow: hidden; background: #fff;">
          <div id="{header_id}" onclick="toggleAccordion('{prefix}', {i}, {len(sections)})" style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:{bg_color};border-bottom: 1px solid {border_color};cursor:pointer;transition:all .2s;">
            <div style="display:flex;align-items:center;gap:10px;">
                <i class="bi {icon}" style="font-size:16px;color:var(--accent)"></i>
                <span style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--navy)">{title}</span>
            </div>
            <i class="bi bi-chevron-down" id="{icon_id}" style="color:var(--muted);transition:all .2s;{icon_rotation}"></i>
          </div>
          <div id="{body_id}" style="display:{display_style};">
            <div style="display:grid;grid-template-columns:repeat(4, 1fr);gap:16px;padding:24px 20px;">
'''
        for name, label, req, typ, is_text, dl in fields:
            html += "              " + field(name, label, req, typ, id_prefix, is_text, dl) + "\\n"
            
        html += '''
            </div>
'''
        if i < len(sections) - 1:
            html += f'''
            <div style="padding:0 20px 20px;text-align:right;">
                <button type="button" class="btn-secondary-custom" onclick="nextAccordion('{prefix}', {i}, {len(sections)})" style="padding:8px 16px;font-size:13px;"><i class="bi bi-arrow-down-circle"></i> Next: {sections[i+1][0]}</button>
            </div>
'''
        html += '''
          </div>
        </div>
'''
    html += '</div>\n'
    return html

add_form_html = generate_form('')
start_add = content.find('<div style="padding:24px 28px">')
if start_add != -1:
    end_add = content.find('{{-- Footer --}}', start_add)
    # the end is the </div> right before {{-- Footer --}}
    end_add_div = content.rfind('</div>', start_add, end_add)
    # Actually wait! The `start_add` is the `<div style="padding:24px 28px">` which is BEFORE the form body!
    # I should replace from start_add + len('<div style="padding:24px 28px">') to end_add_div
    
    content = content[:start_add] + '<div style="padding:24px 28px">\n' + add_form_html + '\n      </div>\n\n      ' + content[end_add:]
    print("Add form updated.")

# Let's add the JS if it doesn't exist
js_snippet = """
function toggleAccordion(prefix, index, total) {
  var body = document.getElementById('body_' + prefix + index);
  var header = document.getElementById('header_' + prefix + index);
  var icon = document.getElementById('icon_' + prefix + index);
  
  if (body.style.display === 'none') {
    body.style.display = 'block';
    icon.style.transform = 'rotate(180deg)';
    header.style.background = 'rgba(14, 165, 233, 0.05)';
    header.style.borderBottom = '1px solid #0ea5e9';
  } else {
    body.style.display = 'none';
    icon.style.transform = 'rotate(0deg)';
    header.style.background = '#f8fafc';
    header.style.borderBottom = '1px solid var(--border)';
  }
}

function nextAccordion(prefix, index, total) {
  // Collapse current
  var currentBody = document.getElementById('body_' + prefix + index);
  var currentHeader = document.getElementById('header_' + prefix + index);
  var currentIcon = document.getElementById('icon_' + prefix + index);
  currentBody.style.display = 'none';
  currentIcon.style.transform = 'rotate(0deg)';
  currentHeader.style.background = '#f8fafc';
  currentHeader.style.borderBottom = '1px solid var(--border)';
  
  // Expand next
  var nextIndex = index + 1;
  if (nextIndex < total) {
    var nextBody = document.getElementById('body_' + prefix + nextIndex);
    var nextHeader = document.getElementById('header_' + prefix + nextIndex);
    var nextIcon = document.getElementById('icon_' + prefix + nextIndex);
    nextBody.style.display = 'block';
    nextIcon.style.transform = 'rotate(180deg)';
    nextHeader.style.background = 'rgba(14, 165, 233, 0.05)';
    nextHeader.style.borderBottom = '1px solid #0ea5e9';
    // Scroll to next section
    nextHeader.scrollIntoView({behavior: 'smooth', block: 'start'});
  }
}
"""

if 'function toggleAccordion(' not in content:
    idx = content.find('</script>')
    if idx != -1:
        content = content[:idx] + js_snippet + '\n' + content[idx:]

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
