<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('point_contents', function (Blueprint $table) {
            $table->id();


            $table->foreignId('mission_id')->constrained('inspector_mission');

            $table->foreignId('point_id')->constrained('points');

            $table->foreignId('inspector_id')->constrained('inspectors');

            $table->integer('parent')->nullable();

            $table->integer('flag')->default(1)->comment('0->notactive , 1->active');
            $table->integer('mechanisms_num')->nullable()->default(0);
            $table->integer('cams_num')->nullable()->default(0);
            $table->integer('computers_num')->nullable()->default(0);
            $table->integer('cars_num')->nullable()->default(0);
            $table->integer('faxes_num')->nullable()->default(0);
            $table->integer('wires_num')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_contents');
    }
};
