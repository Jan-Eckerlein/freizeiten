<?php

use App\Http\Services\MigrationEnumService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected MigrationEnumService $migrationEnumService;

    public function __construct() {
        $this->migrationEnumService = new MigrationEnumService();
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->migrationEnumService->updateEnum('users', 'global_role', ['user', 'admin', 'ghost']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->migrationEnumService->updateEnum('users', 'global_role', ['user', 'admin'], ['ghost' => 'user']);
    }
};
