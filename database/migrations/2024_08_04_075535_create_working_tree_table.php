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
        Schema::create('working_trees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('working_days_num');
            $table->integer('holiday_days_num');
            $table->unsignedBigInteger('created_by');

            // Ensure that the created_by and created_department columns are defined as unsignedBigInteger
            $table->foreign('created_by')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_trees');
    }
};
