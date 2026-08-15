<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Fungsi Menyimpan Tugas ke Antrean
    public function store(Request $request)
    {
        $status = $request->is_done ? 'done' : 'todo';

        $task = Task::create([
            'user_id' => 1,
            'title' => $request->title,
            'task_type' => $request->task_type ?? 'Feature',
            
            // Simpan Data Project & Milestone
            'project' => $request->project,
            'milestone' => $request->milestone,
            
            'task_date' => explode('T', $request->deadline)[0],
            'mode' => $request->mode ?? 'work',
            'status' => $status,
            'effort_score' => $request->duration ?? 0,
            'actual_time' => $request->is_done ? (($request->duration ?? 0) * 60) : 0,
            
            'stakeholder' => $request->stakeholder,
            'environment' => $request->environment,
            'daily_win' => $request->daily_win,
            'oops_moment' => $request->oops_moment,
            'lesson_learned' => $request->lesson_learned,

            'severity' => $request->severity,
            'bug_environment' => $request->bug_environment,
            'steps_to_reproduce' => $request->steps_to_reproduce,
            'expected_result' => $request->expected_result,
            'actual_result' => $request->actual_result,
            'root_cause' => $request->root_cause,
            'solution' => $request->solution,
        ]);

        return response()->json(['message' => 'Task tersimpan!', 'task' => $task]);
    }

    // Pastikan fungsi ini ada untuk menerima laporan Work Log
    public function markAsDone(Request $request, $id)
    {
        $task = Task::find($id);
        
        if ($task) {
            $task->status = 'done';
            
            // Simpan array Work Log dan Total Waktu Aktual (dalam menit)
            if ($request->has('work_logs')) {
                $task->work_logs = $request->work_logs;
            }
            if ($request->has('actual_time')) {
                $task->actual_time = $request->actual_time;
            }
            
            $task->save();
            return response()->json(['message' => 'Laporan Work Log tersimpan!']);
        }

        return response()->json(['message' => 'Tugas tidak ditemukan'], 404);
    }

    // Fungsi Mengedit Tugas
    public function update(Request $request, $id)
    {
        $task = Task::find($id);
        
        if ($task) {
            // Update nama tugas atau durasi aktual jika ada perubahan
            $task->title = $request->title ?? $task->title;
            $task->effort_score = $request->effort_score ?? $task->effort_score;
            $task->save();
            
            return response()->json(['message' => 'Tugas berhasil diperbarui!']);
        }

        return response()->json(['message' => 'Tugas tidak ditemukan'], 404);
    }
    // Fungsi Menghapus Tugas
    public function destroy($id)
    {
        $task = Task::find($id);
        
        if ($task) {
            $task->delete();
            return response()->json(['message' => 'Tugas berhasil dihapus dari MySQL!']);
        }

        return response()->json(['message' => 'Tugas tidak ditemukan'], 404);
    }

    // Fungsi Mengambil Daftar Tugas Berdasarkan Mode
    public function index(Request $request)
    {
        // Tangkap permintaan mode dari frontend (default: work)
        $mode = $request->query('mode', 'work');
        
        // Ambil tugas dari MySQL sesuai mode, urutkan dari yang terbaru
        $tasks = Task::where('mode', $mode)->orderBy('created_at', 'desc')->get();
        
        return response()->json($tasks);
    }
}