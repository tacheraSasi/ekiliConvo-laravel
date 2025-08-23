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
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('password')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->boolean('recording_enabled')->default(false);
            $table->boolean('recording_in_progress')->default(false);
            $table->json('settings')->nullable(); // For future extensibility
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn([
                'password', 
                'is_locked', 
                'recording_enabled', 
                'recording_in_progress',
                'settings'
            ]);
        });
    }
};