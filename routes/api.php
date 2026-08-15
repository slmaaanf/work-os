<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

// Jalur untuk frontend berkomunikasi dengan backend
Route::post('/tasks', [TaskController::class, 'store']);
Route::get('/tasks', [TaskController::class, 'index']);
Route::put('/tasks/{id}/done', [TaskController::class, 'markAsDone']);
Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
Route::put('/tasks/{id}', [TaskController::class, 'update']);