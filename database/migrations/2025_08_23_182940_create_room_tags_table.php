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
        Schema::create('room_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('color', 7)->default('#6B7280'); // Hex color code
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('name');
        });
        
        // Create pivot table for room-tag relationships
        Schema::create('room_tag_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_tag_id')->constrained()->onDelete('cascade');
            $table->timestamp('assigned_at')->useCurrent();
            
            $table->unique(['room_id', 'room_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_tag_assignments');
        Schema::dropIfExists('room_tags');
    }
};
