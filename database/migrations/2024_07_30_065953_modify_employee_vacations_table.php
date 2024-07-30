<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyEmployeeVacationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_vacations', function (Blueprint $table) {
            // Drop columns
            $table->dropColumn(['name', 'date_from', 'date_to']);
            // Add new columns
            $table->integer('country_id')->nullable();
            $table->integer('days_number')->default(0);
            $table->date('start_date')->nullable();
            $table->enum('status', ['Approved', 'Rejected', 'Pending'])->default('Pending');
            $table->boolean('is_cut')->default(false);
            $table->boolean('is_exceeded')->default(false);
            $table->date('end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_vacations', function (Blueprint $table) {
            // Add the dropped columns back
            $table->string('name');
            $table->date('date_from');
            $table->date('date_to');
            // Drop the new columns
            $table->dropColumn(['country_id', 'days_number', 'start_date', 'status', 'is_cut', 'is_exceeded', 'end_date']);
        });
    }
}
