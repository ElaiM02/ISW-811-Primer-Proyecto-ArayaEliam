<?php

use App\Http\Controllers\IdeaController;
use Illuminate\Support\Facades\Route;
use App\Models\Idea;

Route::get('/ideas', [IdeaController::class, 'index']);
Route::get('/ideas/{id}', [IdeaController::class, 'show']);
Route::get('/ideas/create', [IdeaController::class, 'create']);
Route::post('/ideas', [IdeaController::class, 'store'])
Route::get('/ideas/{id}/edit', [IdeaController::class, 'edit']);
Route::put('/ideas/{id}', [IdeaController::class, 'update']);
Route::delete('/ideas/{id}', [IdeaController::class, 'destroy']);
