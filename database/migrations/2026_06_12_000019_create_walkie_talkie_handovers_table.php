<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('walkie_talkie_handovers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('access_request_id')->nullable()->index();
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->string('radio_id')->nullable();
            $table->unsignedInteger('walkie_talkie_id')->nullable();
            $table->string('staff_name')->nullable();
            $table->string('shared_with')->nullable();
            $table->string('staff_no')->nullable();
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('walkie_talkie_handovers');
    }
};
