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
        Schema::table('users', function (Blueprint $table) {


            $foreignKeys = [
                'users_rule_id_foreign',
              
                'users_grade_id_foreign',
                'users_department_id_foreign',
                'users_job_id_foreign',
            ];


            foreach ($foreignKeys as $foreignKey) {
                if ($this->foreignKeyExists('users', $foreignKey)) {
                    $table->dropForeign([$this->getColumnNameFromForeignKey($foreignKey,'users')]);
                }
            }

            //
          /*   $table->dropForeign(['rule_id']);
            $table->dropForeign(['created_by']);  
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['grade_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['job_id']); */

            
            
            // Add new foreign key constraints without cascading on delete
            $table->foreign('rule_id')->nullable()->references('id')->on('rules')->onDelete('restrict')->onUpdate('cascade');
          //  $table->foreign('created_by')->nullable()->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
          //  $table->foreign('updated_by')->nullable( )->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('grade_id')->nullable()->references('id')->on('grades')->onDelete('restrict')->onUpdate('cascade');  //الرتبه
            $table->foreign('department_id')->nullable()->references('id')->on('departements')->onDelete('restrict')->onUpdate('cascade');  // القسم
            $table->foreign('job_id')->nullable()->references('id')->on('jobs')->onDelete('restrict')->onUpdate('cascade');  // 

         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
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
