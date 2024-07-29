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
            // Drop existing foreign key constraints
            $table->dropForeign(['manger']);
            $table->dropForeign(['manger_assistance']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            
            // Add new foreign key constraints without cascading on delete
            $table->foreign('manger')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('manger_assistance')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
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
