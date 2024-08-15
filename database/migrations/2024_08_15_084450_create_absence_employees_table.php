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
        Schema::create('absence_employees', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('grade')->nullable();
            $table->string('military_number')->nullable();
            $table->foreignId('absence_types_id')->nullable()->references('id')->on('absence_types')->onDelete('restrict');
            $table->foreignId('absences_id')->nullable()->references('id')->on('absences')->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absence_employees');
    }
};
