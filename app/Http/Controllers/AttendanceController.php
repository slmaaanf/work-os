<?php

namespace App\Http\Controllers;

use App\Models\DailyPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function clockIn(Request $request)
{
    $request->validate(['time' => 'required']);
    
    $user = auth()->user(); // Sesuaikan jika menggunakan user auth
    $date = Carbon::today()->format('Y-m-d');
    
    // Gabungkan tanggal hari ini dengan waktu inputan user
    $time = Carbon::createFromFormat('H:i', $request->time);
    $dateTime = Carbon::today()->setTime($time->hour, $time->minute);

    $plan = DailyPlan::updateOrCreate(
        ['user_id' => $user->id, 'date' => $date],
        ['clock_in_at' => $dateTime]
    );

    return response()->json(['success' => true, 'clock_in_at' => $dateTime->format('H:i')]);
}

public function clockOut(Request $request)
{
    $request->validate(['time' => 'required']);
    
    $user = auth()->user();
    $date = Carbon::today()->format('Y-m-d');

    $time = Carbon::createFromFormat('H:i', $request->time);
    $dateTime = Carbon::today()->setTime($time->hour, $time->minute);

    $plan = DailyPlan::where('user_id', $user->id)->where('date', $date)->first();
    
    if ($plan) {
        $plan->update(['clock_out_at' => $dateTime]);
        return response()->json(['success' => true, 'clock_out_at' => $dateTime->format('H:i')]);
    }

    return response()->json(['success' => false], 404);

    }
}