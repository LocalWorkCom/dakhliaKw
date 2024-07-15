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
        Schema::create('outgoings', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->integer('file_num')->nullable();
            $table->string('name')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('departement_id')->unsigned()->references('id')->on('departements')->onDelete('cascade');
            $table->foreignId('user_id')->unsigned()->nullable()->references('id')->on('departements')->onDelete('cascade');
            $table->foreignId('created_by')->unsigned()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('updated_by')->unsigned()->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outgpings');
    }
};
