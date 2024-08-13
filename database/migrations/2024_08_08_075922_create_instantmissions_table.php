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
        Schema::create('instantmissions', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->text('location')->nullable();
            $table->text('attachment')->nullable();
            $table->foreignId('group_id')->nullable()->references('id')->on('groups')->onDelete('restrict');
            $table->foreignId('group_team_id')->nullable()->references('id')->on('group_teams')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instantmissions');
    }
};
