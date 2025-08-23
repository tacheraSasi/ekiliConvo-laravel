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
        Schema::create('room_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('note'); // 'note', 'agenda', 'task', 'summary'
            $table->string('title')->nullable();
            $table->text('content');
            $table->json('metadata')->nullable(); // Additional data like task status, priority, etc.
            $table->boolean('is_shared')->default(true); // Whether visible to all participants
            $table->boolean('is_pinned')->default(false); // Whether pinned in the room
            $table->timestamp('due_date')->nullable(); // For tasks
            $table->timestamps();
            
            $table->index(['room_id', 'type']);
            $table->index(['room_id', 'is_shared']);
            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_notes');
    }
};
