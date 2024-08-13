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
        Schema::table('vacation_types', function (Blueprint $table) {
            //
            $foreignKeys = [
                'vacation_types_created_departement_foreign',
                'vacation_types_created_by_foreign',
                'vacation_types_updated_by_foreign'
            ];


            foreach ($foreignKeys as $foreignKey) {
                if ($this->foreignKeyExists('vacation_types', $foreignKey)) {
                    $table->dropForeign([$this->getColumnNameFromForeignKey($foreignKey,'vacation_types')]);
                }
            }
       /*     $table->dropForeign(['created_departement']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);*/
            $table->foreign('created_departement')->nullable()->references('id')->on('departements')->onDelete('restrict')->onUpdate('cascade');

            $table->foreign('created_by')->nullable()->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('updated_by')->nullable( )->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vacation_types', function (Blueprint $table) {
            //
        });
    }
    
     /**
     * Check if a foreign key exists on a table.
     *
     * @param  string  $tableName
     * @param  string  $foreignKeyName
     * @return bool
     */
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
    protected function getColumnNameFromForeignKey($foreignKeyName,$tableName)
    {
        // Assuming a naming pattern <table>_<column>_foreign
        $parts = explode('_foreign', $foreignKeyName);
        $part = explode($tableName.'_',$parts[0]);//explode('_', $parts);
       // print_r($parts); 
        //echo $parts[count($parts) - 3];
       // print_r($part);
      //  return $parts[count($parts) - 3]; // Extracts the column name
      return $part[1];
    }
};
