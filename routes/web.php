<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SessionsController;
use App\Http\Controllers\IdeaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
use App\Models\Idea;
/*
Route::get('/', function (){
    return redirect('/ideas');
});
*/

Route::get('/', function() {
    return 'Welcome to the home page';
});

Route::middleware('auth')->group(function () {
    Route::get('/ideas', [IdeaController::class, 'index'])->middleware('auth');
    Route::get('/ideas/create', [IdeaController::class, 'create']);
    Route::get('/ideas/{idea}', [IdeaController::class, 'show']);
    Route::post('/ideas', [IdeaController::class, 'store']);
    Route::get('/ideas/{idea}/edit', [IdeaController::class, 'edit']);
    Route::patch('/ideas/{idea}', [IdeaController::class, 'update']);
    Route::delete('/ideas/{idea}', [IdeaController::class, 'destroy']);   

    Route::delete('/logout', [SessionsController::class, 'destroy']);
});

    
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create']);
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [SessionsController::class, 'create'])->name('login');
    Route::post('/login', [SessionsController::class, 'store']);
});

/*
Route::get('/admin', function () {
    Gate::authorize('view-admin');

    return 'Private admin only area';
});
*/