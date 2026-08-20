<?php

namespace App\Http\Controllers;

use App\Models\DailyPlan;
use App\Models\DailyPlanActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecapController extends Controller
{
    public function index(Request $request)
    {
        $user = User::firstOrCreate(
            ['email' => 'salma@cimory.com'],
            ['name' => 'Salma', 'password' => bcrypt('password')]
        );

        // 1. DATA UNTUK MINGGU INI (GRAFIK ATAS)
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $completedTasks = DailyPlanActivity::whereHas('dailyPlan', function($q) use ($user, $startOfWeek, $endOfWeek) {
            $q->where('user_id', $user->id)
              ->whereBetween('date', [$startOfWeek, $endOfWeek]);
        })->where('status', 'completed')->with('activity')->get();

        $totalTasks = $completedTasks->count();
        $totalMins = $completedTasks->sum('actual_mins');
        $totalHours = round($totalMins / 60, 1);

        $chartData = ['cimory' => 0, 'work' => 0, 'personal' => 0];
        foreach($completedTasks as $task) {
            // Ambil string category dengan aman
            $cat = is_object($task->activity->category) ? $task->activity->category->value : $task->activity->category; 
            if(isset($chartData[$cat])) { $chartData[$cat] += $task->actual_mins; }
        }

        // 2. DATA UNTUK KALENDER BULANAN 
        $month = $request->query('month', Carbon::now()->month);
        $year = $request->query('year', Carbon::now()->year);

        try {
            $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        } catch (\Exception $e) {
            $startOfMonth = Carbon::now()->startOfMonth();
        }
        
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $prevMonth = $startOfMonth->copy()->subMonth();
        $nextMonth = $startOfMonth->copy()->addMonth();

        $monthlyPlans = DailyPlan::with(['activities.activity'])
            ->where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->get()
            // PERUBAHAN PENTING DI SINI: Kita paksa format kuncinya menjadi Y-m-d murni!
            ->keyBy(function ($plan) {
                return Carbon::parse($plan->date)->format('Y-m-d');
            }); 

        $calendarDays = [];
        $currentDate = $startOfMonth->copy();
        $startDayOfWeek = $currentDate->dayOfWeekIso; 
        
        for ($i = 1; $i < $startDayOfWeek; $i++) {
            $calendarDays[] = null; 
        }

        while ($currentDate <= $endOfMonth) {
            $dateString = $currentDate->format('Y-m-d');
            $plan = $monthlyPlans->get($dateString);
            
            $calendarDays[] = [
                'date' => $currentDate->copy(),
                'date_string' => $dateString,
                'day' => $currentDate->day,
                'has_data' => $plan && $plan->activities->count() > 0,
                'plan' => $plan
            ];
            $currentDate->addDay();
        }

        return view('pages.recap', compact(
            'totalTasks', 'totalHours', 'chartData', 'completedTasks', 
            'calendarDays', 'startOfMonth', 'prevMonth', 'nextMonth'
        ));
    }
}