<?php

use App\Models\User;
use App\Models\Room;
use App\Models\RoomAuditLog;
use App\Models\RoomRecording;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnterpriseFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->host = User::factory()->create([
            'name' => 'Test Host',
            'email' => 'host@test.com'
        ]);
        
        $this->participant = User::factory()->create([
            'name' => 'Test Participant',
            'email' => 'participant@test.com'
        ]);
        
        // Create test room
        $this->room = Room::create([
            'name' => 'Test Enterprise Room',
            'creator_id' => $this->host->id,
            'visibility' => 'public',
            'recording_enabled' => true
        ]);
        
        // Add users to room
        $this->room->users()->attach($this->host->id, [
            'role_in_room' => 'host',
            'joined_at' => now()
        ]);
        
        $this->room->users()->attach($this->participant->id, [
            'role_in_room' => 'participant',
            'joined_at' => now()
        ]);
    }

    /** @test */
    public function test_room_has_enterprise_features()
    {
        $this->assertFalse($this->room->is_locked);
        $this->assertFalse($this->room->recording_in_progress);
        $this->assertTrue($this->room->recording_enabled);
        $this->assertNull($this->room->password);
    }

    /** @test */
    public function test_host_can_set_room_password()
    {
        $password = 'secret123';
        $this->room->setPassword($password);
        $this->room->save();
        
        $this->assertTrue($this->room->isPasswordProtected());
        $this->assertTrue($this->room->checkPassword($password));
        $this->assertFalse($this->room->checkPassword('wrong'));
    }

    /** @test */
    public function test_host_can_lock_room()
    {
        $this->room->is_locked = true;
        $this->room->save();
        
        $this->assertTrue($this->room->is_locked);
        $this->assertFalse($this->room->canJoin($this->participant));
        $this->assertTrue($this->room->canJoin($this->host)); // Host can always join
    }

    /** @test */
    public function test_host_can_mute_participants()
    {
        $this->actingAs($this->host);
        
        $response = $this->postJson("/api/rooms/{$this->room->uuid}/mute-participant", [
            'user_id' => $this->participant->id
        ]);
        
        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'user_id' => $this->participant->id,
            'is_muted' => true
        ]);
        
        // Check audit log was created
        $this->assertDatabaseHas('room_audit_logs', [
            'room_id' => $this->room->id,
            'action' => RoomAuditLog::ACTION_MUTED,
            'user_id' => $this->host->id
        ]);
    }

    /** @test */
    public function test_participant_cannot_mute_others()
    {
        $this->actingAs($this->participant);
        
        $response = $this->postJson("/api/rooms/{$this->room->uuid}/mute-participant", [
            'user_id' => $this->host->id
        ]);
        
        $response->assertStatus(403);
    }

    /** @test */
    public function test_host_can_remove_participants()
    {
        $this->actingAs($this->host);
        
        $response = $this->postJson("/api/rooms/{$this->room->uuid}/remove-participant", [
            'user_id' => $this->participant->id
        ]);
        
        $response->assertOk();
        
        // Check user was removed from room
        $this->assertFalse($this->room->isParticipant($this->participant));
        
        // Check audit log
        $this->assertDatabaseHas('room_audit_logs', [
            'room_id' => $this->room->id,
            'action' => RoomAuditLog::ACTION_KICKED
        ]);
    }

    /** @test */
    public function test_participant_can_raise_hand()
    {
        $this->actingAs($this->participant);
        
        $response = $this->postJson("/api/rooms/{$this->room->uuid}/toggle-hand");
        
        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'hand_raised' => true
        ]);
        
        // Check database
        $userInRoom = $this->room->users()->where('user_id', $this->participant->id)->first();
        $this->assertTrue($userInRoom->pivot->hand_raised);
    }

    /** @test */
    public function test_recording_functionality()
    {
        $this->actingAs($this->host);
        
        // Start recording
        $response = $this->postJson("/api/rooms/{$this->room->uuid}/start-recording");
        $response->assertOk();
        
        $this->room->refresh();
        $this->assertTrue($this->room->recording_in_progress);
        
        // Check recording record was created
        $this->assertDatabaseHas('room_recordings', [
            'room_id' => $this->room->id,
            'status' => 'recording'
        ]);
        
        // Stop recording
        $response = $this->postJson("/api/rooms/{$this->room->uuid}/stop-recording");
        $response->assertOk();
        
        $this->room->refresh();
        $this->assertFalse($this->room->recording_in_progress);
    }

    /** @test */
    public function test_get_room_participants()
    {
        $this->actingAs($this->host);
        
        $response = $this->getJson("/api/rooms/{$this->room->uuid}/participants");
        
        $response->assertOk();
        $response->assertJsonStructure([
            'participants' => [
                '*' => ['id', 'name', 'role', 'is_muted', 'hand_raised', 'joined_at']
            ],
            'total_count',
            'raised_hands_count',
            'is_locked',
            'has_password',
            'is_recording'
        ]);
        
        $data = $response->json();
        $this->assertEquals(2, $data['total_count']);
        $this->assertFalse($data['is_locked']);
        $this->assertFalse($data['has_password']);
        $this->assertFalse($data['is_recording']);
    }

    /** @test */
    public function test_room_model_methods()
    {
        // Test host detection
        $this->assertTrue($this->room->isHost($this->host));
        $this->assertFalse($this->room->isHost($this->participant));
        
        // Test participant detection
        $this->assertTrue($this->room->isParticipant($this->participant));
        $this->assertTrue($this->room->isParticipant($this->host)); // Host is also a participant
        
        // Test participant count
        $this->assertEquals(2, $this->room->getParticipantCount());
        
        // Test user role
        $this->assertEquals('host', $this->room->getUserRole($this->host));
        $this->assertEquals('participant', $this->room->getUserRole($this->participant));
        
        // Test permissions
        $this->assertTrue($this->room->canUserPerformAction($this->host, 'mute_participant'));
        $this->assertFalse($this->room->canUserPerformAction($this->participant, 'mute_participant'));
        $this->assertTrue($this->room->canUserPerformAction($this->participant, 'raise_hand'));
    }

    /** @test */
    public function test_audit_log_creation()
    {
        RoomAuditLog::log(
            $this->room->id,
            RoomAuditLog::ACTION_JOINED,
            $this->participant->id,
            $this->participant->name
        );
        
        $this->assertDatabaseHas('room_audit_logs', [
            'room_id' => $this->room->id,
            'user_id' => $this->participant->id,
            'action' => RoomAuditLog::ACTION_JOINED,
            'actor_name' => $this->participant->name
        ]);
    }

    /** @test */
    public function test_room_recording_model()
    {
        $recording = RoomRecording::create([
            'room_id' => $this->room->id,
            'recording_id' => 'test_recording_123',
            'status' => 'recording',
            'started_at' => now(),
            'metadata' => ['test' => 'data']
        ]);
        
        $this->assertTrue($recording->isRecording());
        $this->assertFalse($recording->isCompleted());
        $this->assertEquals('0:00', $recording->formatted_duration);
        
        // Complete the recording
        $recording->update([
            'status' => 'completed',
            'completed_at' => now(),
            'duration' => 120, // 2 minutes
            'file_size' => 50000000 // 50MB
        ]);
        
        $this->assertTrue($recording->isCompleted());
        $this->assertEquals('2:00', $recording->formatted_duration);
        $this->assertEquals('47.68 MB', $recording->formatted_file_size);
    }

    /** @test */
    public function test_password_protected_room_access()
    {
        $this->room->setPassword('secret123');
        $this->room->save();
        
        // Without password - should redirect to password page
        $response = $this->get("/room/{$this->room->uuid}");
        $response->assertOk();
        $response->assertViewIs('convo.room-password');
        
        // With wrong password
        $response = $this->post("/room/{$this->room->uuid}/validate-password", [
            'password' => 'wrong'
        ]);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['password']);
        
        // With correct password
        $response = $this->post("/room/{$this->room->uuid}/validate-password", [
            'password' => 'secret123'
        ]);
        $response->assertRedirect("/room/{$this->room->uuid}");
    }
}