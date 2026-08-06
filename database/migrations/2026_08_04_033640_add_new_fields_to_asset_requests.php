<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
            public function up(): void
    {
        Schema::table('add_asset_requests', function (Blueprint $table) {

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

        });
        Schema::table('edit_asset_requests', function (Blueprint $table) {

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

        });
    }

    public function down(): void
    {
        Schema::table('add_asset_requests', function (Blueprint $table) {

            $table->dropColumn([
                'domain', 'ip_address', 'os_code', 'sp', 'asset_type', 'asset_name',
                'fqdn', 'mac_address', 'memory_mb', 'nr_processors', 'processor',
                'state', 'last_patched', 'last_full_backup', 'last_full_image',
                'order_number', 'comments', 'building', 'department', 'branch_office',
                'bar_code', 'manufacturer', 'contact', 'scan_server', 'chrome_os_device_id',
                'system_sku', 'purchase_date', 'warranty_date'
            ]);

        });
        Schema::table('edit_asset_requests', function (Blueprint $table) {

            $table->dropColumn([
                'domain', 'ip_address', 'os_code', 'sp', 'asset_name',
                'fqdn', 'mac_address', 'memory_mb', 'nr_processors', 'processor',
                'state', 'last_patched', 'last_full_backup', 'last_full_image',
                'order_number', 'comments', 'building', 'department', 'branch_office',
                'bar_code', 'manufacturer', 'contact', 'scan_server', 'chrome_os_device_id',
                'system_sku'
            ]);

        });
    }
};
