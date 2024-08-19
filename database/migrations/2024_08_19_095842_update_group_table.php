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
        //
        Schema::table('groups', function (Blueprint $table) {
            // $foreignKeys = [
            //     'groups_government_id_foreign',

            // ];


            // foreach ($foreignKeys as $foreignKey) {
            //     if ($this->foreignKeyExists('groups', $foreignKey)) {
            //         $table->dropForeign([$this->getColumnNameFromForeignKey($foreignKey, 'groups')]);
            //     }
            // }
            // $table->dropForeign(['government_id']); // Drop the foreign key constraint
            $table->dropColumn('government_id'); // Remove the column
              $table->unsignedBigInteger('sector_id');
            $table->foreign('sector_id')->references('id')->on('sectors');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
    protected function foreignKeyExists($tableName, $foreignKeyName)
    {
        // For MySQL
        //echo $foreignKeyName;
        return DB::selectOne(
            "SELECT CONSTRAINT_NAME
                 FROM information_schema.TABLE_CONSTRAINTS
                 WHERE TABLE_SCHEMA = DATABASE()
                 AND TABLE_NAME = ?
                 AND CONSTRAINT_NAME = ?",
            [$tableName, $foreignKeyName]
        ) !== null;






        return false; // Default false if unsupported DB
    }

    /**
     * Extract column name from a foreign key constraint name.
     *
     * @param  string  $foreignKeyName
     * @return string
     */
    protected function getColumnNameFromForeignKey($foreignKeyName, $tableName)
    {
        // Assuming a naming pattern <table>_<column>_foreign
        $parts = explode('_foreign', $foreignKeyName);
        $part = explode($tableName . '_', $parts[0]); //explode('_', $parts);
        // print_r($parts); 
        //echo $parts[count($parts) - 3];
        // print_r($part);
        //  return $parts[count($parts) - 3]; // Extracts the column name
        return $part[1];
    }
};
