<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDepartmentIdFromInspectorsTable extends Migration
{
    public function up()
    {
        Schema::table('inspectors', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['department_id']); // Adjust 'department_id' to match your column name
            // Drop the department_id column
            $table->dropColumn('department_id');
        });
    }

    public function down()
    {
        Schema::table('inspectors', function (Blueprint $table) {
            // Re-add the department_id column
            $table->unsignedBigInteger('department_id')->nullable();
            // Re-add the foreign key constraint
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }
}
