<?php

use App\Http\Controllers\Api\RoomControlController;
use App\Http\Controllers\Api\RecordingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Room control API routes (require authentication)
Route::middleware(['auth'])->prefix('rooms/{roomUuid}')->group(function () {
    // Room control endpoints
    Route::post('/mute-participant', [RoomControlController::class, 'muteParticipant']);
    Route::post('/unmute-participant', [RoomControlController::class, 'unmuteParticipant']);
    Route::post('/remove-participant', [RoomControlController::class, 'removeParticipant']);
    Route::post('/toggle-lock', [RoomControlController::class, 'toggleRoomLock']);
    Route::post('/set-password', [RoomControlController::class, 'setRoomPassword']);
    Route::delete('/password', [RoomControlController::class, 'removeRoomPassword']);
    Route::post('/toggle-hand', [RoomControlController::class, 'toggleRaiseHand']);
    Route::get('/participants', [RoomControlController::class, 'getRoomParticipants']);
    
    // Waiting room endpoints
    Route::post('/toggle-waiting-room', [RoomControlController::class, 'toggleWaitingRoom']);
    Route::get('/waiting-participants', [RoomControlController::class, 'getWaitingParticipants']);
    Route::post('/admit-participant', [RoomControlController::class, 'admitParticipant']);
    Route::post('/reject-participant', [RoomControlController::class, 'rejectParticipant']);
    Route::get('/admission-status', [RoomControlController::class, 'checkAdmissionStatus']);
    
    // Recording endpoints
    Route::post('/start-recording', [RecordingController::class, 'startRecording']);
    Route::post('/stop-recording', [RecordingController::class, 'stopRecording']);
    Route::get('/recordings', [RecordingController::class, 'getRoomRecordings']);
    Route::post('/toggle-recording-enabled', [RecordingController::class, 'toggleRecordingEnabled']);
});

// Recording playback and management
Route::middleware(['auth'])->group(function () {
    Route::get('/recordings/{recordingId}/play', [RecordingController::class, 'playRecording']);
    Route::delete('/recordings/{recordingId}', [RecordingController::class, 'deleteRecording']);
});