<?php

namespace App\Http\Controllers;

use App\Models\DailyPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TodayController extends Controller
{
    public function index()
    {
        // 1. Otomatis buat user jika database baru saja di-reset (Anti 404)
        $user = User::firstOrCreate(
            ['email' => 'salma@cimory.com'],
            ['name' => 'Salma', 'password' => bcrypt('password')]
        );

        $date = Carbon::today()->toDateString();

        // 2. Cari atau buat Daily Plan hari ini
        $dailyPlan = DailyPlan::with('activities.activity')->firstOrCreate(
            ['user_id' => $user->id, 'date' => $date]
        );

        $activities = $dailyPlan->activities;

        return view('pages.today', compact('dailyPlan', 'activities'));
    }
}