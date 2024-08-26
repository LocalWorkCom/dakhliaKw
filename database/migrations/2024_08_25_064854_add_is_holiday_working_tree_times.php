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
        Schema::table('working_tree_times', function (Blueprint $table) {


            $foreignKeys = [
                'working_time_id_foreign'
            ];


            foreach ($foreignKeys as $foreignKey) {
                if ($this->foreignKeyExists('working_tree_times', $foreignKey)) {
                    $table->dropForeign([$this->getColumnNameFromForeignKey($foreignKey,'working_tree_times')]);
                }
            }
            // Drop the foreign key constraint
          //  $table->dropForeign(['working_time_id']);
              
            // Modify the column
            $table->unsignedBigInteger('working_time_id')->nullable()->change();
            
            // Recreate the foreign key constraint
            $table->foreign('working_time_id')->references('id')->on('working_times');
        });
        
        // Add 'is_holiday' column if it doesn't exist
        if (!Schema::hasColumn('working_tree_times', 'is_holiday')) {
            Schema::table('working_tree_times', function (Blueprint $table) {
                $table->boolean('is_holiday')->default(0);
            });
        }
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    
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
