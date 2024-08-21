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
        Schema::table('absence_employees', function (Blueprint $table) {
            $table->unsignedBigInteger('grade')->change();

            $table->foreign('grade')->nullable()->references('id')->on('grades');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absence_employees', function (Blueprint $table) {
            $table->dropForeign(['grade']);
        });
    }
};
