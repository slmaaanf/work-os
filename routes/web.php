<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\DailyPlan;
use App\Models\DailyPlanActivity;

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\RecapController;
use App\Http\Controllers\GoalController;

// ==========================================
// ROUTE HALAMAN UTAMA (TODAY'S PLAN & TIME TRAVEL)
// ==========================================
Route::get('/', function (Request $request) {
    // 1. Ambil user Salma
    $user = User::firstOrCreate(
        ['email' => 'salma@cimory.com'],
        ['name' => 'Salma', 'password' => bcrypt('password')]
    );

    // 2. TANGKAP TANGGAL DARI URL (Mesin Waktu)
    $dateString = $request->query('date', Carbon::today()->timezone('Asia/Jakarta')->toDateString());
    $activeDate = Carbon::parse($dateString)->timezone('Asia/Jakarta');
    
    // Cek apakah tanggal yang dibuka adalah hari ini (untuk memunculkan badge "Time Travel")
    $isToday = $activeDate->isToday();

    // 3. Tarik atau Buat Jurnal Harian (Daily Plan) KHUSUS UNTUK TANGGAL TERSEBUT
    $dailyPlan = DailyPlan::firstOrCreate([
        'user_id' => $user->id,
        'date' => $activeDate->toDateString()
    ]);

    // 4. Tarik semua tugas (Task/Queue) yang berada di tanggal tersebut
    $activities = DailyPlanActivity::with('activity')
        ->where('daily_plan_id', $dailyPlan->id)
        ->get();

    // 5. Kirim semua data ke file HTML today.blade.php
    return view('pages.today', compact('dailyPlan', 'activeDate', 'isToday', 'activities'));
});

// ==========================================
// ROUTE UNTUK ABSENSI & ACTIVITIES
// ==========================================
Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn']);
Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut']);

Route::post('/activities', [ActivityController::class, 'store']);
Route::post('/activities/{id}/complete', [ActivityController::class, 'complete']);
Route::post('/activities/{id}/done-for-today', [ActivityController::class, 'doneForToday']);

// ==========================================
// ROUTE UNTUK RECAP & ANALYTICS
// ==========================================
Route::get('/recap', [RecapController::class, 'index']);

// ==========================================
// ROUTE UNTUK GOALS & HABITS
// ==========================================
Route::get('/goals', [GoalController::class, 'index']);
Route::post('/goals', [GoalController::class, 'storeGoal']); 
Route::post('/habits', [GoalController::class, 'storeHabit']);
Route::post('/habits/{id}/toggle', [GoalController::class, 'toggleHabit']);
Route::delete('/goals/{id}', [GoalController::class, 'destroy']);
Route::post('/milestones/{id}/toggle', [GoalController::class, 'toggleMilestone']);
Route::delete('/activities/{id}', [App\Http\Controllers\ActivityController::class, 'destroy']);