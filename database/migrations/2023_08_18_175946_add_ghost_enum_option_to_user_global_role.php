<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE users CHANGE COLUMN global_role global_role ENUM('admin', 'user', 'ghost') NOT NULL DEFAULT 'user'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users CHANGE COLUMN global_role global_role ENUM('admin', 'user') NOT NULL DEFAULT 'user'");
    }
};
