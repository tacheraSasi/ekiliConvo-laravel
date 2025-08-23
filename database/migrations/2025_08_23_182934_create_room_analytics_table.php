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
        Schema::create('room_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->date('date'); // Date for this analytics record
            $table->integer('total_participants')->default(0);
            $table->integer('max_concurrent_participants')->default(0);
            $table->integer('total_duration_minutes')->default(0); // Total meeting time for this date
            $table->integer('total_sessions')->default(1); // Number of meeting sessions
            $table->json('participant_data')->nullable(); // Detailed participant info
            $table->json('quality_metrics')->nullable(); // Connection quality data
            $table->json('activity_summary')->nullable(); // Activity breakdown (join/leave times, etc.)
            $table->timestamps();
            
            // Ensure one record per room per date
            $table->unique(['room_id', 'date']);
            $table->index(['date', 'room_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_analytics');
    }
};
