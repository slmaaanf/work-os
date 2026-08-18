<?php

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\DailyPlanActivityController;
use App\Http\Controllers\Api\FocusSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    
    // Activities
    Route::post('/activities', [ActivityController::class, 'store']);
    
    // Daily Plan Activities (Occurrences)
    Route::post('/daily-plans/{date}/activities', [DailyPlanActivityController::class, 'store']);
    Route::post('/daily-plan-activities/{dailyPlanActivity}/carry-over', [DailyPlanActivityController::class, 'carryOver']);
    
    // Focus Sessions 
    Route::get('/focus-sessions/active', [FocusSessionController::class, 'active']); // The Hydration Route
    Route::post('/daily-plan-activities/{dailyPlanActivity}/start', [FocusSessionController::class, 'start']);
    
    Route::post('/focus-sessions/{focusSession}/pause', [FocusSessionController::class, 'pause']);
    Route::post('/focus-sessions/{focusSession}/resume', [FocusSessionController::class, 'resume']);
    Route::post('/focus-sessions/{focusSession}/finish', [FocusSessionController::class, 'finish']);
    Route::post('/focus-sessions/{focusSession}/abandon', [FocusSessionController::class, 'abandon']);
    
});