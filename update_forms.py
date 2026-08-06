import re

filepath = "resources/views/it/inventory/index.blade.php"

with open(filepath, "r", encoding="utf-8") as f:
    content = f.read()

# the fields requested: Domain, IPAddress, OScode, SP, Description, Assettype, AssetName, FQDN, Mac, Memory (MB), NrProcessors, Processor, State, PurchaseDate, Warrantydate, LastPatched, LastFullbackup, LastFullimage, OrderNumber, Comments, Location, Building, Department, Branchoffice, BarCode, Manufacturer, Contact, Model, Serialnumber, Scanserver, Chrome OS Device ID, System SKU
# only assetname, assettype, model, serial_number are mandatory.

new_edit_fields = """
        <div class="nit-section-label"><i class="bi bi-tag-fill"></i> Asset Details</div>
        <div class="row g-3 mb-4">
          <div class="col-md-3 nit-field"><label>Asset Name <span class="req">*</span></label><input type="text" name="asset_name" id="ief_asset_name" required></div>
          <div class="col-md-3 nit-field"><label>Asset Type <span class="req">*</span></label><input type="text" name="asset_type" id="ief_asset_type" required></div>
          <div class="col-md-3 nit-field"><label>Model <span class="req">*</span></label><input type="text" name="model" id="ief_model" required></div>
          <div class="col-md-3 nit-field"><label>Serial Number <span class="req">*</span></label><input type="text" name="serial_number" id="ief_serial_number" required></div>
          
          <div class="col-md-3 nit-field"><label>Domain</label><input type="text" name="domain" id="ief_domain"></div>
          <div class="col-md-3 nit-field"><label>IP Address</label><input type="text" name="ip_address" id="ief_ip_address"></div>
          <div class="col-md-3 nit-field"><label>OS Code</label><input type="text" name="os_code" id="ief_os_code"></div>
          <div class="col-md-3 nit-field"><label>SP</label><input type="text" name="sp" id="ief_sp"></div>
          <div class="col-12 nit-field"><label>Description</label><input type="text" name="description" id="ief_description"></div>
          <div class="col-md-3 nit-field"><label>FQDN</label><input type="text" name="fqdn" id="ief_fqdn"></div>
          <div class="col-md-3 nit-field"><label>MAC Address</label><input type="text" name="mac_address" id="ief_mac_address"></div>
          <div class="col-md-3 nit-field"><label>Memory (MB)</label><input type="text" name="memory_mb" id="ief_memory_mb"></div>
          <div class="col-md-3 nit-field"><label>Nr Processors</label><input type="text" name="nr_processors" id="ief_nr_processors"></div>
          <div class="col-md-3 nit-field"><label>Processor</label><input type="text" name="processor" id="ief_processor"></div>
          <div class="col-md-3 nit-field"><label>State</label><input type="text" name="state" id="ief_state"></div>
          <div class="col-md-3 nit-field"><label>Purchase Date</label><input type="date" name="purchase_date" id="ief_purchase_date"></div>
          <div class="col-md-3 nit-field"><label>Warranty Date</label><input type="date" name="warranty_date" id="ief_warranty_date"></div>
          <div class="col-md-3 nit-field"><label>Last Patched</label><input type="text" name="last_patched" id="ief_last_patched"></div>
          <div class="col-md-3 nit-field"><label>Last Full Backup</label><input type="text" name="last_full_backup" id="ief_last_full_backup"></div>
          <div class="col-md-3 nit-field"><label>Last Full Image</label><input type="text" name="last_full_image" id="ief_last_full_image"></div>
          <div class="col-md-3 nit-field"><label>Order Number</label><input type="text" name="order_number" id="ief_order_number"></div>
          <div class="col-12 nit-field"><label>Comments</label><textarea name="comments" id="ief_comments"></textarea></div>
          <div class="col-md-3 nit-field"><label>Location</label><input type="text" name="location" id="ief_location"></div>
          <div class="col-md-3 nit-field"><label>Building</label><input type="text" name="building" id="ief_building"></div>
          <div class="col-md-3 nit-field"><label>Department</label><input type="text" name="department" id="ief_department"></div>
          <div class="col-md-3 nit-field"><label>Branch Office</label><input type="text" name="branch_office" id="ief_branch_office"></div>
          <div class="col-md-3 nit-field"><label>Bar Code</label><input type="text" name="bar_code" id="ief_bar_code"></div>
          <div class="col-md-3 nit-field"><label>Manufacturer</label><input type="text" name="manufacturer" id="ief_manufacturer"></div>
          <div class="col-md-3 nit-field"><label>Contact</label><input type="text" name="contact" id="ief_contact"></div>
          <div class="col-md-3 nit-field"><label>Scan Server</label><input type="text" name="scan_server" id="ief_scan_server"></div>
          <div class="col-md-3 nit-field"><label>Chrome OS Device ID</label><input type="text" name="chrome_os_device_id" id="ief_chrome_os_device_id"></div>
          <div class="col-md-3 nit-field"><label>System SKU</label><input type="text" name="system_sku" id="ief_system_sku"></div>
        </div>
"""

new_add_fields = """
      <div style="padding:24px 28px">
        <div style="display:flex;align-items:center;gap:7px;margin-bottom:18px">
          <i class="bi bi-tag" style="font-size:13px;color:var(--muted)"></i>
          <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Asset Details</span>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:16px;margin-bottom:16px">
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Asset Name *</label><input type="text" name="asset_name" required style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Asset Type *</label><input type="text" name="asset_type" required style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Model *</label><input type="text" name="model" required style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Serial Number *</label><input type="text" name="serial_number" required style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Domain</label><input type="text" name="domain" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">IP Address</label><input type="text" name="ip_address" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">OS Code</label><input type="text" name="os_code" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">SP</label><input type="text" name="sp" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div style="grid-column: span 4;"><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Description</label><input type="text" name="description" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">FQDN</label><input type="text" name="fqdn" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">MAC Address</label><input type="text" name="mac_address" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Memory (MB)</label><input type="text" name="memory_mb" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Nr Processors</label><input type="text" name="nr_processors" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Processor</label><input type="text" name="processor" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">State</label><input type="text" name="state" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Purchase Date</label><input type="date" name="purchase_date" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Warranty Date</label><input type="date" name="warranty_date" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Last Patched</label><input type="text" name="last_patched" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Last Full Backup</label><input type="text" name="last_full_backup" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Last Full Image</label><input type="text" name="last_full_image" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Order Number</label><input type="text" name="order_number" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div style="grid-column: span 4;"><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Comments</label><textarea name="comments" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></textarea></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Location</label><input type="text" name="location" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Building</label><input type="text" name="building" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Department</label><input type="text" name="department" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Branch Office</label><input type="text" name="branch_office" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Bar Code</label><input type="text" name="bar_code" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Manufacturer</label><input type="text" name="manufacturer" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Contact</label><input type="text" name="contact" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Scan Server</label><input type="text" name="scan_server" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Chrome OS Device ID</label><input type="text" name="chrome_os_device_id" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
          <div><label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">System SKU</label><input type="text" name="system_sku" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"></div>
        </div>
      </div>
"""

# Replace the edit form inside <div class="nit-form-body"> ... </div> for edit form
# Find the start and end of edit form body
start_idx = content.find('<div class="nit-form-body">')
end_idx = content.find('<div class="nit-form-footer"', start_idx)

if start_idx != -1 and end_idx != -1:
    content = content[:start_idx + len('<div class="nit-form-body">')] + new_edit_fields + content[end_idx:]
else:
    print("Edit form body not found")

# Replace the add form inside <form method="POST" action="{{ route('it.inventory.store') }}"> ... <div style="padding:16px 28px;border-top:1px solid var(--border);display:flex;align-items:center;gap:10px">
start_idx2 = content.find('<form method="POST" action="{{ route(\'it.inventory.store\') }}">')
if start_idx2 != -1:
    start_inner = content.find('{{-- Section: Asset Identity --}}', start_idx2)
    end_inner = content.find('{{-- Footer --}}', start_inner)
    
    if start_inner != -1 and end_inner != -1:
        content = content[:start_inner] + new_add_fields + "\n      " + content[end_inner:]
    else:
        print("Add form sections not found")
else:
    print("Add form not found")

with open(filepath, "w", encoding="utf-8") as f:
    f.write(content)
print("Forms updated successfully")
