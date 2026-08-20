<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

public function index(Request $request)
{
    // 1. Tangkap tanggal dari URL, jika kosong gunakan hari ini
    $dateString = $request->query('date', Carbon::today()->timezone('Asia/Jakarta')->toDateString());
    $activeDate = Carbon::parse($dateString)->timezone('Asia/Jakarta');
    $isToday = $activeDate->isToday();

    $user = auth()->user(); // Sesuaikan dengan auth kamu

    // 2. Tarik Daily Plan berdasarkan $activeDate (bukan lagi today())
    $dailyPlan = \App\Models\DailyPlan::with('activities.activity')->firstOrCreate([
        'user_id' => $user->id,
        'date' => $activeDate->toDateString()
    ]);

    // ... (sisa kodemu untuk me-return view, pastikan mengirim $activeDate dan $isToday)
    return view('pages.today', compact('dailyPlan', 'activeDate', 'isToday' /*, variabel lain... */));
}