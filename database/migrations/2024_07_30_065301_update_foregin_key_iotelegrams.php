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
        Schema::table('iotelegrams', function (Blueprint $table) {
            //
            $foreignKeys = [
                'iotelegrams_from_departement_foreign',
                'iotelegrams_created_by_foreign',
                'iotelegrams_updated_by_foreign',
                'iotelegrams_representive_id_foreign',
                'iotelegrams_recieved_by_foreign',
                'iotelegrams_created_departement_foreign',
            ];


            foreach ($foreignKeys as $foreignKey) {
                if ($this->foreignKeyExists('iotelegrams', $foreignKey)) {
                    $table->dropForeign([$this->getColumnNameFromForeignKey($foreignKey,'iotelegrams')]);
                }
            }




           /*  $table->dropForeign(['from_departement']);
            $table->dropForeign(['representive_id']);
            $table->dropForeign(['recieved_by']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['created_departement']); */



           
            
            // Add new foreign key constraints without cascading on delete
            $table->foreign('from_departement')->nullable()->references('id')->on('departements')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('representive_id')->nullable()->references('id')->on('postmans')->onDelete('restrict')->onUpdate('cascade');

            $table->foreign('recieved_by')->nullable()->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');

            $table->foreign('created_by')->nullable()->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');

            $table->foreign('updated_by')->nullable()->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('created_departement')->nullable()->references('id')->on('departements')->onDelete('restrict')->onUpdate('cascade');


          

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iotelegrams', function (Blueprint $table) {
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
          //  echo $foreignKeyName;
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
