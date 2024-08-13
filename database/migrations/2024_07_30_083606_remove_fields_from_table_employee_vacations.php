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
        Schema::table('employee_vacations', function (Blueprint $table) {
            //
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['vacation_type_id']);
            $table->dropForeign(['created_departement']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            $table->foreign('employee_id')->nullable()->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('vacation_type_id')->nullable( )->references('id')->on('vacation_types')->onDelete('restrict')->onUpdate('cascade');
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
        Schema::table('employee_vacations', function (Blueprint $table) {
            //
        });
    }
};
