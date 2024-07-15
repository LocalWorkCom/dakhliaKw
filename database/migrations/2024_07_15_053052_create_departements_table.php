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
        Schema::create('departements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('manger_id')->unsigned()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('assistance_id')->unsigned()->references('id')->on('users')->onDelete('cascade');
            $table->boolean('active')->nullable()->default(1);

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
        Schema::dropIfExists('departements');
    }
};
