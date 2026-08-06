import re

filepath = "resources/views/it/inventory/index.blade.php"

with open(filepath, "r", encoding="utf-8") as f:
    content = f.read()

# The user wants to replace the edit data fields
new_js = """
function openEditModal(id) {
  var d = _editData[id];
  if (!d) return;
  var isAdminOrFinance = {{ $user->isAdminOrFinance() ? 'true' : 'false' }};

  document.getElementById('itEditFormEl').action = '{{ url("it/inventory") }}/' + id;
  
  // New fields
  document.getElementById('ief_asset_name').value = d.asset_name || '';
  document.getElementById('ief_asset_type').value = d.asset_type || '';
  document.getElementById('ief_model').value = d.model || '';
  document.getElementById('ief_serial_number').value = d.serial_number || '';
  
  document.getElementById('ief_domain').value = d.domain || '';
  document.getElementById('ief_ip_address').value = d.ip_address || '';
  document.getElementById('ief_os_code').value = d.os_code || '';
  document.getElementById('ief_sp').value = d.sp || '';
  document.getElementById('ief_description').value = d.description || '';
  document.getElementById('ief_fqdn').value = d.fqdn || '';
  document.getElementById('ief_mac_address').value = d.mac_address || '';
  document.getElementById('ief_memory_mb').value = d.memory_mb || '';
  document.getElementById('ief_nr_processors').value = d.nr_processors || '';
  document.getElementById('ief_processor').value = d.processor || '';
  document.getElementById('ief_state').value = d.state || '';
  
  // Date fields need to be in YYYY-MM-DD if input is type="date", we might need to parse. Assuming d.purchase_date is string or we can just try assigning. 
  // Wait, if it's 'date' casted, it might come as full ISO string. Let's just assign substring(0,10)
  document.getElementById('ief_purchase_date').value = d.purchase_date ? d.purchase_date.substring(0, 10) : '';
  document.getElementById('ief_warranty_date').value = d.warranty_date ? d.warranty_date.substring(0, 10) : '';
  
  document.getElementById('ief_last_patched').value = d.last_patched || '';
  document.getElementById('ief_last_full_backup').value = d.last_full_backup || '';
  document.getElementById('ief_last_full_image').value = d.last_full_image || '';
  document.getElementById('ief_order_number').value = d.order_number || '';
  document.getElementById('ief_comments').value = d.comments || '';
  document.getElementById('ief_location').value = d.location || '';
  document.getElementById('ief_building').value = d.building || '';
  document.getElementById('ief_department').value = d.department || '';
  document.getElementById('ief_branch_office').value = d.branch_office || '';
  document.getElementById('ief_bar_code').value = d.bar_code || '';
  document.getElementById('ief_manufacturer').value = d.manufacturer || '';
  document.getElementById('ief_contact').value = d.contact || '';
  document.getElementById('ief_scan_server').value = d.scan_server || '';
  document.getElementById('ief_chrome_os_device_id').value = d.chrome_os_device_id || '';
  document.getElementById('ief_system_sku').value = d.system_sku || '';
"""

start_idx = content.find('function openEditModal(id) {')
end_idx = content.find('if (!isAdminOrFinance)', start_idx)

if start_idx != -1 and end_idx != -1:
    content = content[:start_idx] + new_js + "\n  " + content[end_idx:]
    with open(filepath, "w", encoding="utf-8") as f:
        f.write(content)
    print("JS updated successfully")
else:
    print("JS replace failed, idxs:", start_idx, end_idx)
