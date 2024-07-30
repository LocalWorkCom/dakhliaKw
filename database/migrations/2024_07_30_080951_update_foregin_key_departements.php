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
        Schema::table('departements', function (Blueprint $table) {
            //
            $table->dropForeign(['parent_id']);
            
            // Add new foreign key constraints without cascading on delete
            $table->foreign('parent_id')->references('id')->on('departements')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departements', function (Blueprint $table) {
            //
        });
    }
};
