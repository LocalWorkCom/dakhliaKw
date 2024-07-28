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
        Schema::table('iotelegrams', function (Blueprint $table) {
            //
            $table->string('outgoing_num');
            $table->string('outgoing_date');
            $table->string('iotelegram_num');
            $table->integer('files_num');
            $table->string('user_id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iotelegrams', function (Blueprint $table) {
            //
        });
    }
};
