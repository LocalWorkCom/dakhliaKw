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
        Schema::create('outgoing_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outgoing_id')->unsigned()->references('id')->on('iotelegrams')->onDelete('cascade');
            $table->string('real_name');
            $table->string('file_name');

            $table->foreignId('created_by')->unsigned()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('updated_by')->unsigned()->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outgoing_files');
    }
};
