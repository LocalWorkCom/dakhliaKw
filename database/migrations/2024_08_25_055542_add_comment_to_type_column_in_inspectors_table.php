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
        Schema::table('inspectors', function (Blueprint $table) {
        $table->string('type')->comment('Buildings => مفتش مبانى  , internbilding =>مفتش متدرب مبانى   , internslok=> مفتس متدرب سلوك انضباطى   ,slok=>  مفتش سلوك ')->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspectors', function (Blueprint $table) {
            $table->string('type')->comment(null)->change(); // Remove the comment
        });
    }
};
