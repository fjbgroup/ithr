<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('walkie_talkies', function (Blueprint $table) {
            $table->increments('walkie_id');
            $table->string('radio_id')->nullable()->index();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('status')->nullable()->index();
            $table->string('ownership_type')->nullable();
            $table->string('shared_with')->nullable();
            $table->string('ownership')->nullable();
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->string('temporary_radio_id')->nullable();
            $table->text('remark')->nullable();
            $table->string('tracking_ref')->nullable();
            $table->boolean('need_to_change_id')->default(false);
            $table->boolean('id_change_done')->default(false);
            $table->string('ownership_type_to_be')->nullable();
            $table->boolean('is_special_use')->default(false);
            $table->boolean('special_use_returned')->default(false);
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('walkie_talkies');
    }
};
