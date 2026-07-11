<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SessionsController;
use App\Http\Controllers\IdeaController;
use App\Http\Controllers\StepController;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/ideas');
});

Route::middleware('auth')->group(function () {
    Route::get('/ideas', [IdeaController::class, 'index'])->name('idea.index');
    Route::get('/ideas/create', [IdeaController::class, 'create'])->name('idea.create');
    Route::get('/ideas/{idea}', [IdeaController::class, 'show'])->name('idea.show');
    Route::post('/ideas', [IdeaController::class, 'store'])->name('idea.store');
    Route::get('/ideas/{idea}/edit', [IdeaController::class, 'edit'])->name('idea.edit');
    Route::patch('/ideas/{idea}', [IdeaController::class, 'update'])->name('idea.update');
    Route::delete('/ideas/{idea}', [IdeaController::class, 'destroy'])->name('idea.destroy');

    Route::delete('/logout', [SessionsController::class, 'destroy']);
});

Route::patch('/steps/{step}', [StepController::class, 'update'])
    ->name('step.update')->middleware('auth');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create']);
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [SessionsController::class, 'create'])->name('login')->middleware('guest');
    Route::post('/login', [SessionsController::class, 'store']);
});

/*
Route::get('/admin', function () {
    Gate::authorize('view-admin');

    return 'Private admin only area';
});
*/
