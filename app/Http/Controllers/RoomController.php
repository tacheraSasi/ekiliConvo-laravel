<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    /**
     * Display the lobby page
     */
    public function index()
    {
        return view('convo.lobby');
    }

    /**
     * Display dashboard with user's rooms
     */
    public function dashboard()
    {
        $user = Auth::user();
        $createdRooms = $user->createdRooms()
                            ->where(function($query) {
                                $query->whereNull('expires_at')
                                      ->orWhere('expires_at', '>', now());
                            })
                            ->latest()
                            ->get();
        
        $joinedRooms = $user->joinedRooms()
                           ->where(function($query) {
                               $query->whereNull('expires_at')
                                     ->orWhere('expires_at', '>', now());
                           })
                           ->latest()
                           ->get();

        return view('convo.dashboard', compact('createdRooms', 'joinedRooms'));
    }

    /**
     * Store a newly created room
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'visibility' => 'required|in:public,private',
            'waiting_room_enabled' => 'boolean',
        ]);

        $room = Room::create([
            'name' => $request->name,
            'creator_id' => Auth::id(),
            'visibility' => $request->visibility,
            'waiting_room_enabled' => $request->boolean('waiting_room_enabled'),
            'uuid' => Str::uuid(),
        ]);

        // Add creator as host to the room
        $room->users()->attach(Auth::id(), [
            'role_in_room' => 'host',
            'joined_at' => now(),
            'status' => 'admitted', // Host is always admitted
        ]);

        return redirect()->route('room', $room->uuid);
    }

    /**
     * Display the specified room
     */
    public function show(string $uuid, Request $request)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();
        
        // Check if room is expired
        if ($room->isExpired()) {
            return redirect()->route('home')->with('error', 'This room has expired.');
        }

        // Check if room requires password
        if ($room->isPasswordProtected()) {
            $password = $request->input('password') ?? session('room_password_' . $uuid);
            
            if (!$password || !$room->checkPassword($password)) {
                return view('convo.room-password', [
                    'roomUuid' => $room->uuid,
                    'roomName' => $room->name
                ]);
            }
            
            // Store password in session for this room
            session(['room_password_' . $uuid => $password]);
        }

        // Check if user can join (room lock status)
        if (Auth::check()) {
            $user = Auth::user();
            if (!$room->canJoin($user)) {
                return redirect()->route('home')->with('error', 'This room is locked and you cannot join.');
            }
        } else {
            // For guests, check if room is locked
            if ($room->is_locked) {
                return redirect()->route('home')->with('error', 'This room is locked to new participants.');
            }
        }

        // If user is already in the room but waiting, show waiting room
        if (Auth::check() && $room->isParticipantWaiting(Auth::user())) {
            return view('convo.waiting-room', [
                'room' => $room->name,
                'roomUuid' => $room->uuid,
                'roomModel' => $room,
            ]);
        }
        if (Auth::check() && !$room->isParticipant(Auth::user()) && !$room->isHost(Auth::user())) {
            $status = $room->isWaitingRoomEnabled() ? 'waiting' : 'admitted';
            
            $room->users()->attach(Auth::id(), [
                'role_in_room' => 'participant',
                'joined_at' => now(),
                'status' => $status,
            ]);
            
            // If waiting room is enabled and user is not host, show waiting room
            if ($room->isWaitingRoomEnabled() && $status === 'waiting') {
                return view('convo.waiting-room', [
                    'room' => $room->name,
                    'roomUuid' => $room->uuid,
                    'roomModel' => $room,
                ]);
            }
        }

        return view('convo.room', [
            'room' => $room->name,
            'roomUuid' => $room->uuid,
            'roomModel' => $room,
            'isHost' => Auth::check() ? $room->isHost(Auth::user()) : false
        ]);
    }

    /**
     * Validate room password
     */
    public function validatePassword(Request $request, string $uuid)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        $room = Room::where('uuid', $uuid)->firstOrFail();
        
        if (!$room->isPasswordProtected()) {
            return redirect()->route('room', $uuid);
        }

        if ($room->checkPassword($request->password)) {
            session(['room_password_' . $uuid => $request->password]);
            return redirect()->route('room', $uuid);
        }

        return back()->withErrors(['password' => 'Invalid room password.']);
    }

    /**
     * Join room via invite link (for guests)
     */
    public function join(string $uuid)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();
        
        if ($room->isExpired()) {
            return redirect()->route('home')->with('error', 'This room has expired.');
        }

        if (!Auth::check()) {
            // For guests, redirect to lobby with room UUID
            session(['joining_room' => $uuid]);
            return redirect()->route('home')->with('info', 'Please enter your name to join the room.');
        }

        return redirect()->route('room', $uuid);
    }

    /**
     * Remove the specified room
     */
    public function destroy(string $uuid)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();
        
        // Only creator can delete the room
        if ($room->creator_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only delete rooms you created.');
        }

        $room->delete();
        
        return redirect()->route('dashboard')->with('success', 'Room deleted successfully.');
    }
}
