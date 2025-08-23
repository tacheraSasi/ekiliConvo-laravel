<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomRecording;
use App\Models\RoomAuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class RecordingController extends Controller
{
    /**
     * Start recording a room session
     */
    public function startRecording(string $roomUuid): JsonResponse
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $currentUser = Auth::user();

        if (!$room->isHost($currentUser)) {
            return response()->json(['error' => 'Only hosts can start recordings'], 403);
        }

        if ($room->isRecording()) {
            return response()->json(['error' => 'Recording is already in progress'], 400);
        }

        if (!$room->recording_enabled) {
            return response()->json(['error' => 'Recording is not enabled for this room'], 400);
        }

        try {
            // In a real implementation, you would integrate with Agora Cloud Recording API
            // For now, we'll create a recording record with a mock implementation
            $recordingId = 'rec_' . uniqid();
            
            $recording = RoomRecording::create([
                'room_id' => $room->id,
                'recording_id' => $recordingId,
                'status' => 'recording',
                'started_at' => now(),
                'metadata' => [
                    'room_name' => $room->name,
                    'host_name' => $currentUser->name,
                    'participants_count' => $room->getParticipantCount()
                ]
            ]);

            $room->recording_in_progress = true;
            $room->save();

            // Log the action
            RoomAuditLog::log(
                $room->id,
                RoomAuditLog::ACTION_RECORDING_STARTED,
                $currentUser->id,
                $currentUser->name
            );

            return response()->json([
                'success' => true,
                'recording_id' => $recordingId,
                'message' => 'Recording started successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to start recording: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Stop recording a room session
     */
    public function stopRecording(string $roomUuid): JsonResponse
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $currentUser = Auth::user();

        if (!$room->isHost($currentUser)) {
            return response()->json(['error' => 'Only hosts can stop recordings'], 403);
        }

        $recording = $room->recordings()->where('status', 'recording')->latest()->first();
        
        if (!$recording) {
            return response()->json(['error' => 'No active recording found'], 404);
        }

        try {
            // In a real implementation, you would call Agora Cloud Recording API to stop
            $recording->update([
                'status' => 'completed',
                'completed_at' => now(),
                'duration' => now()->diffInSeconds($recording->started_at),
                'file_path' => 'recordings/' . $recording->recording_id . '.mp4',
                'file_url' => url('recordings/' . $recording->recording_id),
                'file_size' => rand(50000000, 200000000) // Mock file size
            ]);

            $room->recording_in_progress = false;
            $room->save();

            // Log the action
            RoomAuditLog::log(
                $room->id,
                RoomAuditLog::ACTION_RECORDING_STOPPED,
                $currentUser->id,
                $currentUser->name
            );

            return response()->json([
                'success' => true,
                'recording' => [
                    'id' => $recording->id,
                    'recording_id' => $recording->recording_id,
                    'duration' => $recording->formatted_duration,
                    'file_size' => $recording->formatted_file_size,
                    'file_url' => $recording->file_url
                ],
                'message' => 'Recording stopped successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to stop recording: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recordings for a room
     */
    public function getRoomRecordings(string $roomUuid): JsonResponse
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $currentUser = Auth::user();

        if (!$room->isHost($currentUser) && !$room->isParticipant($currentUser)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $recordings = $room->recordings()
            ->orderBy('started_at', 'desc')
            ->get()
            ->map(function ($recording) {
                return [
                    'id' => $recording->id,
                    'recording_id' => $recording->recording_id,
                    'status' => $recording->status,
                    'started_at' => $recording->started_at->format('Y-m-d H:i:s'),
                    'completed_at' => $recording->completed_at?->format('Y-m-d H:i:s'),
                    'duration' => $recording->formatted_duration,
                    'file_size' => $recording->formatted_file_size,
                    'file_url' => $recording->file_url,
                    'can_download' => $recording->isCompleted()
                ];
            });

        return response()->json([
            'recordings' => $recordings,
            'total_count' => $recordings->count()
        ]);
    }

    /**
     * Play/stream a recording
     */
    public function playRecording(int $recordingId): JsonResponse
    {
        $recording = RoomRecording::findOrFail($recordingId);
        $currentUser = Auth::user();

        // Check if user has access to this recording
        $room = $recording->room;
        if (!$room->isHost($currentUser) && !$room->isParticipant($currentUser)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        if (!$recording->isCompleted()) {
            return response()->json(['error' => 'Recording is not yet completed'], 400);
        }

        return response()->json([
            'recording' => [
                'id' => $recording->id,
                'recording_id' => $recording->recording_id,
                'room_name' => $room->name,
                'duration' => $recording->formatted_duration,
                'file_size' => $recording->formatted_file_size,
                'file_url' => $recording->file_url,
                'started_at' => $recording->started_at->format('Y-m-d H:i:s'),
                'metadata' => $recording->metadata
            ]
        ]);
    }

    /**
     * Delete a recording
     */
    public function deleteRecording(int $recordingId): JsonResponse
    {
        $recording = RoomRecording::findOrFail($recordingId);
        $currentUser = Auth::user();

        // Only room host can delete recordings
        if (!$recording->room->isHost($currentUser)) {
            return response()->json(['error' => 'Only hosts can delete recordings'], 403);
        }

        try {
            // In a real implementation, you would also delete the file from storage
            if ($recording->file_path && Storage::exists($recording->file_path)) {
                Storage::delete($recording->file_path);
            }

            $recording->delete();

            return response()->json([
                'success' => true,
                'message' => 'Recording deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete recording: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle recording feature for a room
     */
    public function toggleRecordingEnabled(string $roomUuid): JsonResponse
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $currentUser = Auth::user();

        if (!$room->isHost($currentUser)) {
            return response()->json(['error' => 'Only hosts can toggle recording settings'], 403);
        }

        $room->recording_enabled = !$room->recording_enabled;
        $room->save();

        return response()->json([
            'success' => true,
            'recording_enabled' => $room->recording_enabled,
            'message' => $room->recording_enabled ? 
                'Recording enabled for this room' : 
                'Recording disabled for this room'
        ]);
    }
}