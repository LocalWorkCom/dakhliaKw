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
        Schema::table('absence_employees', function (Blueprint $table) {
            $table->foreignId('type_employee')->nullable()->references('id')->on('violation_type')->onDelete('restrict')->onUpdate('cascade');
            $table->string('civil_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absence_employees', function (Blueprint $table) {
            //
        });
    }
};
