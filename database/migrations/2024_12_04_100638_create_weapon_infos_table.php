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
        Schema::create('weapon_infos', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->default(0);
            $table->integer('weapon_num')->nullable()->default(0);
            $table->integer('ammunition_num')->nullable()->default(0);
            $table->foreignId('content_id')->constrained('point_contents');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weapons_infos');
    }
};
