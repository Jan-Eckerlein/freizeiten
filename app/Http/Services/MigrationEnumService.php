<?php
namespace App\Http\Services;

use Illuminate\Support\Facades\DB;

class MigrationEnumService {

    /**
     * @param string $table
     * @param string $column
     * @param array $options
     * @param array|null $updateRemovedOptions
     * @param mixed|null $defaultOption - if null, will default to first option in array
     */
    public function updateEnum(string $table, string $column, array $options, array $updateRemovedOptions = null, $defaultOption = null): void
    {
        if ($updateRemovedOptions) {
            foreach ($updateRemovedOptions as $removedOption => $replaceWith) {
                DB::statement("UPDATE $table SET $column = '$replaceWith' WHERE $column = '$removedOption'");
            }
        }

        $enum = "enum('" . implode("','", $options) . "')";
        $defaultOption = $defaultOption ?? $options[0];
        DB::statement("ALTER TABLE $table CHANGE COLUMN $column $column $enum NOT NULL DEFAULT '$defaultOption'");
    }
}
