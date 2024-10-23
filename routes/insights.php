<?php 
use App\Http\Controllers\InsightController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use Illuminate\Support\Facades\Route;

#Giving access to even a guest

Route::get('/insights/{insight}', [InsightController::class, 'show'])->name('insights.show');

Route::middleware('auth')->group(function () {
    // Route::resource('insights', InsightController::class);


    Route::get('/insights', [InsightController::class, 'index'])->name('insights.index');

    // Route::get('/insights/create', [InsightController::class, 'create'])->name('insights.create'); doesnt work for some reason
    Route::get('/write', [InsightController::class, 'create'])->name('insights.create');
    
    Route::post('/insights', [InsightController::class, 'store'])->name('insights.store');
    Route::get('/insights/{insight}/edit', [InsightController::class, 'edit'])->name('insights.edit');
    Route::put('/insights/{insight}', [InsightController::class, 'update'])->name('insights.update');
    Route::delete('/insights/{insight}', [InsightController::class, 'destroy'])->name('insights.destroy');

    Route::post('insights/{insight}/like', [LikeController::class, 'store'])->name('insights.like');
    Route::post('insights/{insight}/comments', [CommentController::class, 'store'])->name('comments.store');
});


