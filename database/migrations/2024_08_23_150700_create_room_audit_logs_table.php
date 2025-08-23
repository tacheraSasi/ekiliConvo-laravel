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
        Schema::create('room_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // joined, left, muted, unmuted, kicked, recording_started, etc.
            $table->string('actor_name')->nullable(); // Name of the user who performed the action
            $table->string('target_name')->nullable(); // Name of the target user (for kicks, mutes, etc.)
            $table->json('details')->nullable(); // Additional action details
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_audit_logs');
    }
};