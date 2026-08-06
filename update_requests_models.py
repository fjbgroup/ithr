import re

models = ["app/Models/IT/AddAssetRequest.php", "app/Models/IT/EditAssetRequest.php"]

new_fillables = """
        'domain', 'ip_address', 'os_code', 'sp', 'asset_type', 'asset_name',
        'fqdn', 'mac_address', 'memory_mb', 'nr_processors', 'processor',
        'state', 'last_patched', 'last_full_backup', 'last_full_image',
        'order_number', 'comments', 'building', 'department', 'branch_office',
        'bar_code', 'manufacturer', 'contact', 'scan_server', 'chrome_os_device_id',
        'system_sku', 'purchase_date', 'warranty_date',
"""

for filepath in models:
    with open(filepath, "r", encoding="utf-8") as f:
        content = f.read()
    
    start_idx = content.find('protected $fillable = [')
    end_idx = content.find('];', start_idx)
    
    if start_idx != -1 and end_idx != -1:
        # Extract the current fillable part, insert new fields before closing bracket
        fillable_str = content[start_idx:end_idx]
        if 'asset_name' not in fillable_str:
            new_fillable_str = fillable_str.rstrip() + "\n" + new_fillables + "    "
            content = content[:start_idx] + new_fillable_str + content[end_idx:]
            with open(filepath, "w", encoding="utf-8") as f:
                f.write(content)
            print(f"Updated {filepath}")
        else:
            print(f"Already updated {filepath}")
    else:
        print(f"Fillable not found in {filepath}")
