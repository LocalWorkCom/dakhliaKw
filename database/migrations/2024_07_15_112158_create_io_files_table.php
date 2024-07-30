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
        Schema::create('io_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iotelegram_id')->nullable()->references('id')->on('iotelegrams')->onDelete('cascade');
            $table->string('real_name');
            $table->string('file_name');
            $table->boolean('active')->default(1);

            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable( )->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('io_files');
    }
};
