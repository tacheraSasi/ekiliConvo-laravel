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
        Schema::table('room_users', function (Blueprint $table) {
            $table->boolean('is_muted')->default(false);
            $table->boolean('hand_raised')->default(false);
            $table->json('permissions')->nullable(); // For role-based permissions
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_users', function (Blueprint $table) {
            $table->dropColumn(['is_muted', 'hand_raised', 'permissions']);
        });
    }
};