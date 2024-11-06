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
        Schema::create('attendance_employees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attendance_id')->constrained('attendances');

            $table->foreignId('grade_id')->constrained('grades');

            $table->foreignId('type_id')->constrained('violation_type');

            $table->string( 'name');

            $table->foreignId('force_id')->constrained('force_names');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_employees');
    }
};
