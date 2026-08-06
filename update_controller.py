import re

filepath = "app/Http/Controllers/IT/InventoryController.php"

with open(filepath, "r", encoding="utf-8") as f:
    content = f.read()

new_validation = """
        $data = $request->validate([
            'asset_name'       => 'required|string|max:255',
            'asset_type'       => 'required|string|max:100',
            'model'            => 'required|string|max:100',
            'serial_number'    => 'required|string|max:100',
            'domain'           => 'nullable|string|max:100',
            'ip_address'       => 'nullable|string|max:100',
            'os_code'          => 'nullable|string|max:100',
            'sp'               => 'nullable|string|max:100',
            'description'      => 'nullable|string|max:255',
            'fqdn'             => 'nullable|string|max:255',
            'mac_address'      => 'nullable|string|max:100',
            'memory_mb'        => 'nullable|string|max:100',
            'nr_processors'    => 'nullable|string|max:100',
            'processor'        => 'nullable|string|max:100',
            'state'            => 'nullable|string|max:100',
            'purchase_date'    => 'nullable|date',
            'warranty_date'    => 'nullable|date',
            'last_patched'     => 'nullable|string|max:100',
            'last_full_backup' => 'nullable|string|max:100',
            'last_full_image'  => 'nullable|string|max:100',
            'order_number'     => 'nullable|string|max:100',
            'comments'         => 'nullable|string',
            'location'         => 'nullable|string|max:100',
            'building'         => 'nullable|string|max:100',
            'department'       => 'nullable|string|max:100',
            'branch_office'    => 'nullable|string|max:100',
            'bar_code'         => 'nullable|string|max:100',
            'manufacturer'     => 'nullable|string|max:100',
            'contact'          => 'nullable|string|max:100',
            'scan_server'      => 'nullable|string|max:100',
            'chrome_os_device_id' => 'nullable|string|max:255',
            'system_sku'       => 'nullable|string|max:100',
        ]);
"""

# Replace store method validation
start_idx1 = content.find('$data = $request->validate([', content.find('public function store'))
end_idx1 = content.find(']);', start_idx1) + 3

if start_idx1 != -1:
    content = content[:start_idx1] + new_validation.strip() + content[end_idx1:]

# Replace update method validation
start_idx2 = content.find('$data = $request->validate([', content.find('public function update'))
end_idx2 = content.find(']);', start_idx2) + 3

if start_idx2 != -1:
    content = content[:start_idx2] + new_validation.strip() + content[end_idx2:]

with open(filepath, "w", encoding="utf-8") as f:
    f.write(content)
print("Controller updated successfully")
