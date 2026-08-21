<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Milestone;
use App\Models\Habit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index()
    {
        $user = User::firstOrCreate(
            ['email' => 'salma@cimory.com'],
            ['name' => 'Salma', 'password' => bcrypt('password')]
        );

        // Tarik Goals & Milestones
        $goals = Goal::with('milestones')->where('user_id', $user->id)->get();

        // Tarik Habits dari Database
        $habitsCollection = Habit::where('user_id', $user->id)->get();

        // Siapkan rentang 7 hari ke belakang (Senin - Minggu minggu ini)
        $startOfWeek = Carbon::now()->startOfWeek();
        $days = [];
        $dateKeys = [];

        for ($i = 0; $i < 7; $i++) {
            $dayDate = $startOfWeek->copy()->addDays($i);
            $days[] = [
                'name' => $dayDate->format('D'), // Mon, Tue, Wed, dll
                'date' => $dayDate->format('Y-m-d') // 2026-08-17
            ];
            $dateKeys[] = $dayDate->format('Y-m-d');
        }

        // Format data habit untuk dicocokkan dengan tabel UI
        $habits = $habitsCollection->map(function($habit) use ($dateKeys) {
            $logs = $habit->logs ?? [];
            $history = [];
            $consecutiveStreak = 0;
            $isStreakActive = true;

            // Hitung centang untuk 7 hari & hitung streak
            foreach ($dateKeys as $date) {
                $checked = $logs[$date] ?? false;
                $history[] = $checked;

                if ($checked && $isStreakActive) {
                    $consecutiveStreak++;
                } elseif (!$checked && Carbon::parse($date)->isPast()) {
                    // Jika terlewat di hari lalu, putus streak sementara
                    // (Bisa disederhanakan sesuai kebutuhan)
                }
            }

            return [
                'id' => $habit->id,
                'title' => $habit->title,
                'category' => $habit->category,
                'streak' => max($consecutiveStreak, count(array_filter($history))), 
                'history' => $history,
                'dates' => $dateKeys
            ];
        });

        return view('pages.goals', compact('goals', 'habits', 'days'));
    }

    // Simpan Goal Baru
    public function storeGoal(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255', 'color' => 'required|string']);
        $user = User::where('email', 'salma@cimory.com')->first();

        $goal = Goal::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'color' => $request->color,
        ]);

        if ($request->filled('milestones')) {
            foreach ($request->milestones as $ms_title) {
                if (!empty($ms_title)) {
                    Milestone::create(['goal_id' => $goal->id, 'title' => $ms_title]);
                }
            }
        }
        return response()->json(['success' => true]);
    }

    // Simpan Habit Baru (Bisa dari input Personal Task / Add Habit)
    public function storeHabit(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255']);
        $user = User::where('email', 'salma@cimory.com')->first();

        Habit::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'category' => $request->category ?? 'Personal',
            'logs' => []
        ]);

        return response()->json(['success' => true]);
    }

    // Fungsi Centang/Uncentang Kotak Habit (Toggle)
    public function toggleHabit(Request $request, $id)
    {
        $habit = Habit::findOrFail($id);
        $date = $request->date; // Format Y-m-d

        $logs = $habit->logs ?? [];
        // Balikkan nilai centangnya (kalau true jadi false, kalau false jadi true)
        $logs[$date] = !($logs[$date] ?? false);

        $habit->logs = $logs;
        $habit->save();

        return response()->json(['success' => true, 'logs' => $logs]);
    }
    public function destroy($id)
{
    // Mencari goal berdasarkan ID lalu menghapusnya
    $goal = \App\Models\Goal::findOrFail($id);
    $goal->delete();

    return response()->json(['message' => 'Goal berhasil dihapus!']);
}
public function toggleMilestone($id)
{
    // Cari milestone yang diklik
    $milestone = \App\Models\Milestone::findOrFail($id);
    
    // Ubah statusnya (jika false jadi true, jika true jadi false)
    $milestone->is_completed = !$milestone->is_completed;
    $milestone->save();

    // Hitung ulang persentase progress dari Goal induknya
    $goal = $milestone->goal;
    $total = $goal->milestones()->count();
    $completed = $goal->milestones()->where('is_completed', true)->count();
    
    // Update persentase (Progress = Total selesai / Total semua * 100)
    $goal->progress = $total > 0 ? round(($completed / $total) * 100) : 0;
    $goal->save();

    return response()->json(['message' => 'Progress updated!', 'progress' => $goal->progress]);
}
}