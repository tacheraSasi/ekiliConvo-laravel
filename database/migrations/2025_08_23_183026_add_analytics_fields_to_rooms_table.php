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
            // Analytics and customization fields
            $table->string('category')->nullable()->after('settings'); // Room category for organization
            $table->text('description')->nullable()->after('name'); // Room description
            $table->json('tags')->nullable()->after('category'); // Quick reference to tag names
            $table->integer('total_sessions')->default(0)->after('recording_in_progress'); // Total number of sessions
            $table->integer('total_participants_all_time')->default(0); // Unique participants count
            $table->timestamp('last_activity_at')->nullable(); // Last time room was used
            $table->json('quality_settings')->nullable(); // Quality monitoring preferences
            $table->json('notification_settings')->nullable(); // Notification preferences for this room
            $table->string('theme')->default('default')->after('settings'); // Room theme/branding
            $table->text('custom_background')->nullable(); // Custom background URL or settings
        });
        
        Schema::table('users', function (Blueprint $table) {
            // User preferences and customization
            $table->string('avatar_url')->nullable()->after('email_verified_at');
            $table->text('bio')->nullable()->after('avatar_url');
            $table->string('preferred_language', 5)->default('en')->after('bio');
            $table->json('notification_preferences')->nullable()->after('preferred_language');
            $table->json('accessibility_settings')->nullable(); // Screen reader, contrast, etc.
            $table->string('timezone')->nullable()->after('accessibility_settings');
            $table->boolean('is_online')->default(false); // Presence indicator
            $table->timestamp('last_seen_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn([
                'category', 'description', 'tags', 'total_sessions', 
                'total_participants_all_time', 'last_activity_at', 
                'quality_settings', 'notification_settings', 'theme', 'custom_background'
            ]);
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar_url', 'bio', 'preferred_language', 'notification_preferences',
                'accessibility_settings', 'timezone', 'is_online', 'last_seen_at'
            ]);
        });
    }
};
