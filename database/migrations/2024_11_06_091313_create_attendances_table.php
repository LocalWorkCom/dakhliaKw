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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->date('date');

            $table->foreignId('mission_id')->constrained('inspector_mission');

            $table->foreignId('instant_id')->constrained('instantmissions');

            $table->integer('total');

            $table->foreignId('inspector_id')->constrained('inspectors');

            $table->integer('parent')->nullable();

            $table->integer('flag')->default(1)->comment('0->notactive , 1->active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
