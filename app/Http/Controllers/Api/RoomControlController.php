<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomAuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RoomControlController extends Controller
{
    /**
     * Mute a participant (host only)
     */
    public function muteParticipant(Request $request, string $roomUuid): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id'
        ]);

        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $currentUser = Auth::user();

        if (!$room->isHost($currentUser)) {
            return response()->json(['error' => 'Only hosts can mute participants'], 403);
        }

        $targetUserId = $request->user_id;
        $targetUser = $room->users()->where('user_id', $targetUserId)->first();

        if (!$targetUser) {
            return response()->json(['error' => 'User not found in room'], 404);
        }

        // Update mute status
        $room->users()->updateExistingPivot($targetUserId, ['is_muted' => true]);

        // Log the action
        RoomAuditLog::log(
            $room->id,
            RoomAuditLog::ACTION_MUTED,
            $currentUser->id,
            $currentUser->name,
            $targetUser->name
        );

        return response()->json([
            'success' => true,
            'message' => 'Participant muted successfully',
            'user_id' => $targetUserId,
            'is_muted' => true
        ]);
    }

    /**
     * Unmute a participant (host only)
     */
    public function unmuteParticipant(Request $request, string $roomUuid): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id'
        ]);

        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $currentUser = Auth::user();

        if (!$room->isHost($currentUser)) {
            return response()->json(['error' => 'Only hosts can unmute participants'], 403);
        }

        $targetUserId = $request->user_id;
        $targetUser = $room->users()->where('user_id', $targetUserId)->first();

        if (!$targetUser) {
            return response()->json(['error' => 'User not found in room'], 404);
        }

        // Update mute status
        $room->users()->updateExistingPivot($targetUserId, ['is_muted' => false]);

        // Log the action
        RoomAuditLog::log(
            $room->id,
            RoomAuditLog::ACTION_UNMUTED,
            $currentUser->id,
            $currentUser->name,
            $targetUser->name
        );

        return response()->json([
            'success' => true,
            'message' => 'Participant unmuted successfully',
            'user_id' => $targetUserId,
            'is_muted' => false
        ]);
    }

    /**
     * Remove a participant from the room (host only)
     */
    public function removeParticipant(Request $request, string $roomUuid): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id'
        ]);

        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $currentUser = Auth::user();

        if (!$room->isHost($currentUser)) {
            return response()->json(['error' => 'Only hosts can remove participants'], 403);
        }

        $targetUserId = $request->user_id;
        $targetUser = $room->users()->where('user_id', $targetUserId)->first();

        if (!$targetUser) {
            return response()->json(['error' => 'User not found in room'], 404);
        }

        if ($targetUser->pivot->role_in_room === 'host') {
            return response()->json(['error' => 'Cannot remove the host'], 400);
        }

        // Remove user from room
        $room->users()->detach($targetUserId);

        // Log the action
        RoomAuditLog::log(
            $room->id,
            RoomAuditLog::ACTION_KICKED,
            $currentUser->id,
            $currentUser->name,
            $targetUser->name
        );

        return response()->json([
            'success' => true,
            'message' => 'Participant removed successfully',
            'user_id' => $targetUserId
        ]);
    }

    /**
     * Lock/unlock the room (host only)
     */
    public function toggleRoomLock(string $roomUuid): JsonResponse
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $currentUser = Auth::user();

        if (!$room->isHost($currentUser)) {
            return response()->json(['error' => 'Only hosts can lock/unlock rooms'], 403);
        }

        $room->is_locked = !$room->is_locked;
        $room->save();

        // Log the action
        $action = $room->is_locked ? RoomAuditLog::ACTION_ROOM_LOCKED : RoomAuditLog::ACTION_ROOM_UNLOCKED;
        RoomAuditLog::log(
            $room->id,
            $action,
            $currentUser->id,
            $currentUser->name
        );

        return response()->json([
            'success' => true,
            'is_locked' => $room->is_locked,
            'message' => $room->is_locked ? 'Room locked successfully' : 'Room unlocked successfully'
        ]);
    }

    /**
     * Set room password (host only)
     */
    public function setRoomPassword(Request $request, string $roomUuid): JsonResponse
    {
        $request->validate([
            'password' => 'required|string|min:4|max:50'
        ]);

        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $currentUser = Auth::user();

        if (!$room->isHost($currentUser)) {
            return response()->json(['error' => 'Only hosts can set room passwords'], 403);
        }

        $room->setPassword($request->password);
        $room->save();

        return response()->json([
            'success' => true,
            'message' => 'Room password set successfully'
        ]);
    }

    /**
     * Remove room password (host only)
     */
    public function removeRoomPassword(string $roomUuid): JsonResponse
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $currentUser = Auth::user();

        if (!$room->isHost($currentUser)) {
            return response()->json(['error' => 'Only hosts can remove room passwords'], 403);
        }

        $room->password = null;
        $room->save();

        return response()->json([
            'success' => true,
            'message' => 'Room password removed successfully'
        ]);
    }

    /**
     * Raise/lower hand
     */
    public function toggleRaiseHand(string $roomUuid): JsonResponse
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $currentUser = Auth::user();

        $userInRoom = $room->users()->where('user_id', $currentUser->id)->first();
        if (!$userInRoom) {
            return response()->json(['error' => 'User not found in room'], 404);
        }

        $newHandRaisedStatus = !$userInRoom->pivot->hand_raised;
        $room->users()->updateExistingPivot($currentUser->id, ['hand_raised' => $newHandRaisedStatus]);

        // Log the action
        $action = $newHandRaisedStatus ? RoomAuditLog::ACTION_HAND_RAISED : RoomAuditLog::ACTION_HAND_LOWERED;
        RoomAuditLog::log(
            $room->id,
            $action,
            $currentUser->id,
            $currentUser->name
        );

        return response()->json([
            'success' => true,
            'hand_raised' => $newHandRaisedStatus,
            'message' => $newHandRaisedStatus ? 'Hand raised' : 'Hand lowered'
        ]);
    }

    /**
     * Get room participants with their status
     */
    public function getRoomParticipants(string $roomUuid): JsonResponse
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $currentUser = Auth::user();

        if (!$room->isParticipant($currentUser) && !$room->isHost($currentUser)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $participants = $room->users()->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->pivot->role_in_room,
                'is_muted' => $user->pivot->is_muted,
                'hand_raised' => $user->pivot->hand_raised,
                'joined_at' => $user->pivot->joined_at
            ];
        });

        return response()->json([
            'participants' => $participants,
            'total_count' => $participants->count(),
            'raised_hands_count' => $participants->where('hand_raised', true)->count(),
            'is_locked' => $room->is_locked,
            'has_password' => $room->isPasswordProtected(),
            'is_recording' => $room->isRecording()
        ]);
    }
}