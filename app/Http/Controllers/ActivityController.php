<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\DailyPlan;
use App\Models\DailyPlanActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'planned_mins' => 'nullable|integer',
            'project_id' => 'nullable|integer',
            'goal_id' => 'nullable|integer',
            'milestone_id' => 'nullable|integer',
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'target_date' => 'nullable|date' // TAMBAHAN: Menerima tanggal dari frontend
        ]);

        $user = \App\Models\User::firstOrCreate(
            ['email' => 'salma@cimory.com'],
            ['name' => 'Salma', 'password' => bcrypt('password')]
        );

        // Tentukan tanggal target (dari request, atau hari ini jika kosong)
        $targetDateStr = $request->target_date ?? \Carbon\Carbon::today()->timezone('Asia/Jakarta')->toDateString();
        $targetDateObj = \Carbon\Carbon::parse($targetDateStr)->timezone('Asia/Jakarta');

        $activity = \App\Models\Activity::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'category' => $request->category,
            'project_id' => $request->project_id,
            'goal_id' => $request->goal_id,
            'milestone_id' => $request->milestone_id,
        ]);

        // Gunakan $targetDateStr, bukan Carbon::today()
        $dailyPlan = \App\Models\DailyPlan::firstOrCreate([
            'user_id' => $user->id,
            'date' => $targetDateStr
        ]);

        // Simpan Jam Absen dengan menyatukannya pada $targetDateObj (Tanggal Mundur)
        if ($request->has('clock_in')) {
            $dailyPlan->clock_in_at = $request->clock_in ? \Carbon\Carbon::createFromFormat('H:i', $request->clock_in, 'Asia/Jakarta')->setDateFrom($targetDateObj) : null;
        }
        if ($request->has('clock_out')) {
            $dailyPlan->clock_out_at = $request->clock_out ? \Carbon\Carbon::createFromFormat('H:i', $request->clock_out, 'Asia/Jakarta')->setDateFrom($targetDateObj) : null;
        }
        $dailyPlan->save();

        \App\Models\DailyPlanActivity::create([
            'daily_plan_id' => $dailyPlan->id,
            'activity_id' => $activity->id,
            'planned_mins' => $request->planned_mins ?? 0,
            'status' => 'planned' 
        ]);

        return response()->json(['success' => true]);
    }

    // Fungsi untuk Mark as Completed (Menyelesaikan tugas selamanya)
    public function complete(Request $request, $id)
    {
        $dpa = DailyPlanActivity::findOrFail($id);
        
        $dpa->update([
            'status' => 'completed',
            'actual_mins' => $request->actual_mins ?? 0,
            'achievements' => $request->achievements,
            'blockers' => $request->blockers,
        ]);

        return response()->json(['success' => true]);
    }

    // Fungsi untuk Done for Today (Menyimpan durasi tapi tugas TETAP ADA di Queue)
    public function doneForToday(Request $request, $id)
    {
        $dpa = DailyPlanActivity::findOrFail($id);
        
        // Status dibiarkan / di-set 'planned' agar tetap muncul di Queue for Today
        $dpa->update([
            'status' => 'planned',
            'actual_mins' => $request->actual_mins ?? 0,
        ]);

        return response()->json(['success' => true]);
    }
    public function destroy($id)
{
    // Cari data aktivitas berdasarkan ID.
    // Ganti 'DailyPlanActivity' dengan model yang kamu gunakan untuk tabel ini, 
    // misalnya 'Activity' atau 'Task' sesuai struktur database-mu.
    $activity = \App\Models\DailyPlanActivity::find($id); 
    
    if($activity) {
        $activity->delete();
    }

    return response()->json(['message' => 'Task berhasil dihapus dari riwayat!']);
}
}