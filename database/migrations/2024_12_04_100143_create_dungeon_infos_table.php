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
        Schema::create('dungeon_infos', function (Blueprint $table) {
            $table->id();
            $table->integer('men_num')->nullable()->default(0);
            $table->integer('women_num')->nullable()->default(0);
            $table->integer('overtake')->nullable()->default(0);
            $table->integer('duration')->nullable()->default(0);
            $table->text('note')->nullable();
            $table->foreignId('content_id')->constrained('point_contents');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dungeon_infos');
    }
};
