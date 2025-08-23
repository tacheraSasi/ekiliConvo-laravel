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
        Schema::create('room_recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('recording_id')->unique(); // Agora recording ID
            $table->string('resource_id')->nullable(); // Agora resource ID
            $table->string('sid')->nullable(); // Agora SID
            $table->string('file_path')->nullable(); // Storage path
            $table->string('file_url')->nullable(); // Public URL
            $table->bigInteger('file_size')->nullable(); // File size in bytes
            $table->integer('duration')->nullable(); // Duration in seconds
            $table->enum('status', ['recording', 'completed', 'failed'])->default('recording');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->json('metadata')->nullable(); // Additional recording data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_recordings');
    }
};