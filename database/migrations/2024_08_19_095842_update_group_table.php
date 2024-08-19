<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            // Define the foreign key names to drop
      

            // Drop the 'government_id' foreign key and column if they exist
            if (Schema::hasColumn('groups', 'government_id')) {
                $table->dropForeign(['government_id']); // Drop the foreign key constraint
                $table->dropColumn('government_id'); // Remove the column
            }

            // Add the new 'sector_id' foreign key
            $table->unsignedBigInteger('sector_id')->nullable();
            $table->foreign('sector_id')->references('id')->on('sectors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Implement the down method to revert the changes if necessary
    }

    /**
     * Check if a foreign key exists on the table.
     */
    protected function foreignKeyExists($tableName, $foreignKeyName)
    {
        // Check if the foreign key exists in the database
        return DB::selectOne(
            "SELECT CONSTRAINT_NAME
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
             AND TABLE_NAME = ?
             AND CONSTRAINT_NAME = ?",
            [$tableName, $foreignKeyName]
        ) !== null;
    }

    /**
     * Extract column name from a foreign key constraint name.
     */
    protected function getColumnNameFromForeignKey($foreignKeyName, $tableName)
    {
        // Assuming a naming pattern <table>_<column>_foreign
        $parts = explode('_foreign', $foreignKeyName);
        $part = explode($tableName . '_', $parts[0]);
        return $part[1] ?? null;
    }
};
