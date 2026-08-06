import glob

migration_files = glob.glob("database/migrations/*add_new_fields_to_asset_requests.php")
if migration_files:
    migration_file = migration_files[0]
    
    with open(migration_file, "r") as f:
        content = f.read()

    new_fields_add = """
            $table->string('domain')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('os_code')->nullable();
            $table->string('sp')->nullable();
            $table->string('asset_type')->nullable();
            $table->string('asset_name')->nullable();
            $table->string('fqdn')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('memory_mb')->nullable();
            $table->string('nr_processors')->nullable();
            $table->string('processor')->nullable();
            $table->string('state')->nullable();
            $table->string('last_patched')->nullable();
            $table->string('last_full_backup')->nullable();
            $table->string('last_full_image')->nullable();
            $table->string('order_number')->nullable();
            $table->text('comments')->nullable();
            $table->string('building')->nullable();
            $table->string('department')->nullable();
            $table->string('branch_office')->nullable();
            $table->string('bar_code')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('contact')->nullable();
            $table->string('scan_server')->nullable();
            $table->string('chrome_os_device_id')->nullable();
            $table->string('system_sku')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_date')->nullable();
"""

    new_fields_edit = """
            $table->string('domain')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('os_code')->nullable();
            $table->string('sp')->nullable();
            $table->string('asset_name')->nullable();
            $table->string('fqdn')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('memory_mb')->nullable();
            $table->string('nr_processors')->nullable();
            $table->string('processor')->nullable();
            $table->string('state')->nullable();
            $table->string('last_patched')->nullable();
            $table->string('last_full_backup')->nullable();
            $table->string('last_full_image')->nullable();
            $table->string('order_number')->nullable();
            $table->text('comments')->nullable();
            $table->string('building')->nullable();
            $table->string('department')->nullable();
            $table->string('branch_office')->nullable();
            $table->string('bar_code')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('contact')->nullable();
            $table->string('scan_server')->nullable();
            $table->string('chrome_os_device_id')->nullable();
            $table->string('system_sku')->nullable();
"""

    down_fields_add = """
            $table->dropColumn([
                'domain', 'ip_address', 'os_code', 'sp', 'asset_type', 'asset_name',
                'fqdn', 'mac_address', 'memory_mb', 'nr_processors', 'processor',
                'state', 'last_patched', 'last_full_backup', 'last_full_image',
                'order_number', 'comments', 'building', 'department', 'branch_office',
                'bar_code', 'manufacturer', 'contact', 'scan_server', 'chrome_os_device_id',
                'system_sku', 'purchase_date', 'warranty_date'
            ]);
"""

    down_fields_edit = """
            $table->dropColumn([
                'domain', 'ip_address', 'os_code', 'sp', 'asset_name',
                'fqdn', 'mac_address', 'memory_mb', 'nr_processors', 'processor',
                'state', 'last_patched', 'last_full_backup', 'last_full_image',
                'order_number', 'comments', 'building', 'department', 'branch_office',
                'bar_code', 'manufacturer', 'contact', 'scan_server', 'chrome_os_device_id',
                'system_sku'
            ]);
"""

    up_method = f"""    public function up(): void
    {{
        Schema::table('add_asset_requests', function (Blueprint $table) {{
{new_fields_add}
        }});
        Schema::table('edit_asset_requests', function (Blueprint $table) {{
{new_fields_edit}
        }});
    }}
"""
    
    down_method = f"""    public function down(): void
    {{
        Schema::table('add_asset_requests', function (Blueprint $table) {{
{down_fields_add}
        }});
        Schema::table('edit_asset_requests', function (Blueprint $table) {{
{down_fields_edit}
        }});
    }}
"""

    start_up = content.find('public function up()')
    start_down = content.find('public function down()')
    
    if start_up != -1 and start_down != -1:
        end_up = content.find('}', content.find('}', start_up) + 1) + 1
        end_down = content.find('}', content.find('}', start_down) + 1) + 1
        
        content = content[:start_up] + up_method + "\n" + down_method + content[end_down:]
        
        with open(migration_file, "w") as f:
            f.write(content)
        print("Migration modified")
