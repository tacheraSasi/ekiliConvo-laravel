<?php

// use App\Http\Controllers\InsightController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
// use App\Models\Insight;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    if(Auth::check()){
       return redirect(route("home"));
    }
    // $insights = Insight::with('category', 'user', 'likes', 'comments')->latest()->paginate(6);
    return view('welcome');
});

//ui testing route 
Route::get("/ui",function (){
    return view("ui");
});

Route::get('/dashboard', function () {
    return redirect(route("home"));
})->middleware(['auth', 'verified'])->name('dashboard');


// Route::get('/home', [InsightController::class,"index"])->middleware(['auth', 'verified'])->name('home');

// Route::get("/write", function(){
//     return ["route"=>"home","write"=> "write posts"];
// })->name("write");

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get("/lobby", [RoomController::class,"index"])->name("home");
    Route::post("/lobby", [RoomController::class,"create"])->name("create-room-post");

    Route::get("/room/{name}", function($name){
        $room = $name;
        return view('convo.room',["room"=>$room]);
    })->name('room');
});

// require __DIR__.'/insights.php';
require __DIR__.'/auth.php';
