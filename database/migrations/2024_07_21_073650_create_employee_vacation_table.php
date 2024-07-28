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
        Schema::create('employee_vacations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('vacation_type_id')->nullable( )->references('id')->on('vacation_types')->onDelete('cascade');
            $table->date('date_from');
            $table->date('date_to');
            $table->boolean('active')->default(1);
            $table->foreignId('created_departement')->nullable()->references('id')->on('departements')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable( )->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_vacations');
    }
};
