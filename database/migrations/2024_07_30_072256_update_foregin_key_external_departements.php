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
        Schema::table('external_departements', function (Blueprint $table) {
            //
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->foreign('created_by')->nullable()->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');

            $table->foreign('updated_by')->nullable()->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('external_departements', function (Blueprint $table) {
            //
        });
    }
};