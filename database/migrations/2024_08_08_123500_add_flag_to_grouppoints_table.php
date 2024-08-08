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
        Schema::table('group_points', function (Blueprint $table) {
            $table->boolean('flag')->default(0)->after('government_id'); // Add 'flag' column after 'government_id'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_points', function (Blueprint $table) {
            $table->dropColumn('flag');
        });
    }
};