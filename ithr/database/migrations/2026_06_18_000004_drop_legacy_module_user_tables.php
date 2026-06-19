<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

// it_users and wt_users are superseded by the unified users table.
// All three module models (App\Models\User, IT\User, WT\User) now read from users.
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('it_users');
        Schema::dropIfExists('wt_users');
    }

    public function down(): void
    {
        // Re-create stubs so migrate:rollback does not error.
        // Real data lives in the users table — these are no longer populated.
        Schema::create('it_users', function ($table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('full_name');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('wt_users', function ($table) {
            $table->increments('user_id');
            $table->string('username')->unique();
            $table->string('password');
            $table->rememberToken();
        });
    }
};
