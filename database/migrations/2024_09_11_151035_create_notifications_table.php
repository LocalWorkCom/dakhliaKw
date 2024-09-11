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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->text('message'); // Column for the notification message
            $table->boolean('status')->default(0)->comment('0 for unread , 1 for readed'); // Column for the notification message

            $table->foreignId('user_id')->constrained('users'); // Foreign key for user_id
            $table->foreignId('mission_id')->constrained('inspector_mission'); // Foreign key for mission_id
            $table->timestamps(); // Created at and updated at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            Schema::dropIfExists('notifications');
        });
    }
};
